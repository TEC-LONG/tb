<?php

class Validator{

    protected static $_M;

    protected $rrange;//rule range
    protected $dmsg;//default message
    protected $denySingle;//不可作为独立规则的主规则
    protected $codeMsg;
    protected $codeLevel;


    protected $field;//当前字段
    protected $fieldKey;//当前字段对应的key
    protected $data;//原始字段对应的数据数组
    protected $ruleFlag;//当前检测的规则标识，"single"表示当前规则为独立规则或主规则，无副规则；"multi"表示当前规则为主副结构规则
    protected $nowRuleStr;

    protected $fields=[];//需要检查的字段集合
    protected $rule=[];//用户指定的规则集合
    protected $msg=[];//用户指定的违规消息提示集合
    protected $excludes=[];//用户指定的字段对应的排除检查值

    public $err=[];
    // protected $sysErr=[];
    public $sysErr=[];

    public function __construct(){
    
        ///规则范围 （增加1个规则，就需要增加这个规则对应的处理方法
        $this->rrange = [
            #独立规则
            'along' => ['required', 'email', 'cell', 'phone', 'ch-utf8', 'ip'],
            #主规则
            'main'  => ['int', 'float', 'regex'],
            #副规则
            'vice'  => [
                'int'   => ['>', '>=', '<', '<=', '=', 'min', 'max'],
                'float' => ['>', '>=', '<', '<=', '=', 'min', 'max'],
                'regex' => ['@']
            ]
        ];

        ///不可作为独立规则的主规则
        $this->denySingle = ['regex'];

        ///错误码对应的提示信息
        $this->codeMsg = [
            '1001'      => '规则：{rule} 不可独立使用，请为其指定相应的副规则',
            '1002'      => '无效的规则：{rule}',
            '1003'      => '规则：{rule} 是一个“独立型”规则，不可“主副”联合使用',
            '1004'      => '设定为“主副型”规则的规则：{rule} 没有指定副规则',
            '1005'      => '找不到规则：{rule} 的处理方式，如需处理该规则，请先拓展该规则的处理方式',
            '2001'      => '{field} 为必填参数',
            '2002'      => '{field} 必须为整数',
            '2003.1'    => '{field} 值需要大于 {val}',
            '2003.2'    => '{field} 值需要大于等于 {val}',
            '2003.3'    => '{field} 值需要小于 {val}',
            '2003.4'    => '{field} 值需要小于等于 {val}',
            '2003.5'    => '{field} 值需要等于 {val}',
            '2004'      => '{field} 邮箱格式不正确',
            '2005'      => '{field} 手机号格式不正确',
            '2006'      => '{field} 座机号格式不正确',# 座机号规则暂未构建，故当前暂未支持座机号检测！！
            '2007'      => '',# 号码未分配，预定为float
            '2008'      => '{field} 必须为IP格式',
            '2009'      => '{field} 必须为UTF-8编码的中文',
            '2010'      => '{field} 数据不符合当前自定义正则规则',
        ];

        ///错误码对应的级别（必须）
        $this->codeLevel = [
            #规则参数错误，针对不可作为独立规则的主规则，如只传递了主规则regex,但是却没有必须的副规则
            'param' => ['1001', '1002', '1003', '1004', '1005'],
            #数据错误，数据不满足规则
            'data' => ['2001', '2002', '2003.*', '2004', '2005', '2006', '2007', '2008', '2009']
        ];
    }

    /**
     * @method  make
     * 方法作用：校验数据入口方法
     * 
     * @param    $data              array    被校验的数据
     * @param    $fields_rule_arr   array    需要校验的字段与字段对应的校验规则
     * @param    $msg               array    "字段.规则" 对应的违规提示信息
     * @param    $excludes          array    字段对应的排除检查值
                    $excludes = [
                        'members'   => ['zhangsan', 'lisi'],    # <=== members字段排除检查多个值
                        'age'       => 12                       # <=== age字段排除检查单个值
                    ]
     * @return    Validator object
     */
    public static function make($data, $fields_rule_arr, $msg=[], $excludes=[]){

        self::$_M = new self;

        ///记录并生成用户指定的规则
        self::$_M->mkrule($fields_rule_arr);
        
        ///记录并生成用户指定的字段提示信息
        if( !empty($msg) ) self::$_M->mkmsg($msg);

        ///记录并生成用户指定的排除字段值
        if( !empty($excludes) ) self::$_M->mkexclude($excludes);

        ///根据规则检查字段数据
        self::$_M->ckEveryFields($data);

        ///处理system级err
        self::$_M->sysErrHandler();

        return self::$_M;
    }

    /**
     * 记录并生成用户指定的排除字段值
     */
    protected function mkexclude($excludes){
    
        foreach( $excludes as $field=>$exclude){
        
            $this->excludes[$field] = $exclude;
        }
    }

    /**
     * 处理system err
     */
    protected function sysErrHandler(){
    
        $this->Hexception(function($obj){

            if( !empty($obj->sysErr) ){
                $e = new Exception('检测规则错误，请先指定符合规范的规则');
                throw($e);
            }
        });
    }

    /**
     * 异常处理 Exception Handler
     */
    protected function Hexception($fn){
    
        if(!is_callable($fn)) return false;

        try{
            $fn($this);
        }catch(Exception $e){
            $html = $this->createHtml($e);
            echo $html;
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * 创建system err报错的html报文
     */
    protected function createHtml($e){
    
        $head_ch_name   = ['field'=>'字段', 'code'=>'错误码', 'rule'=>'完整规则', 'codemsg'=>'错误信息', 'level'=>'错误级别'];
        $html           = '<table border="1" cellspacing="0"><thead>{thead1}<tr>{thead2}<tr></thead><tbody>{tbody}<tbody></table>';

        $thead2     = '';
        $tbody      = '';
        $counter    = 0;
        $col_num    = 0;
        foreach( $this->sysErr as $field=>$sysErr){
            
            if( $counter==0 ){
                $thead2.='<td>'.$head_ch_name['field'].'</td>';
                $col_num+=1;
            }

            $tbody .= '<tr>';
            $tbody .= '<td>'.$field.'</td>';

            foreach( $sysErr as $key=>$val){
            
                if( $counter==0 ){
                    $thead2.='<td>'.$head_ch_name[$key].'</td>';
                    $col_num+=1;
                }
                $tbody .= '<td>'.$val.'</td>';
            }
            $tbody .= '</tr>';
            $counter++;
        }

        $html = str_replace('{thead2}', $thead2, $html);
        $html = str_replace('{tbody}', $tbody, $html);

        $thead1 = '<tr><td colspan="'.$col_num.'">时间：'.date('Y-m-d H:i:s').'</td></tr>';
        // $thead1 .= '<tr><td colspan="'.$col_num.'">错误：'.$e->getMessage().'</td></tr>';

        $html = str_replace('{thead1}', $thead1, $html);

        return $html;
    }

    /**
     * 记录并生成用户指定的规则
     */
    protected function mkrule($fields_rule_arr){

        foreach( $fields_rule_arr as $field=>$rule){
        
            $this->fields[] = $field;
            $this->rule[] = $rule;
        }

        return $this;
    }

    /**
     * 记录并生成用户指定的字段提示信息
     */
    protected function mkmsg($msg){

        foreach( $msg as $field_rule=>$this_msg){
            
            ///根据分隔符"."炸开字段与规则
            $tmp = explode('.', $field_rule);#如：cell.int
            $this_field = array_shift($tmp);

            $count = count($tmp);#当前字段的规则层数，如"int.min"是2

            switch($count){
                case 1:#仅有主规则或为独立规则
                    $this->msg[$this_field][$tmp[0]]['default'] = $this_msg;
                break;
                case 2:#主副规则组合
                    $this->msg[$this_field][$tmp[0]][$tmp[1]] = $this_msg;
                break;
            }
        }
    }
    
    /**
     * code to code message
     * $code
     * $search   被替换的目标
     * $replace   替换的值
     */
    protected function code2cmsg($code, $search='', $replace=''){
        
        if( isset($this->codeMsg[$code]) ){

            $this_codeMsg = $this->codeMsg[$code];
            if($search!=='') $this_codeMsg = str_replace($search, $replace, $this->codeMsg[$code]);
            return $this_codeMsg;
        }
        return '';
    }

    /**
     * code to level
     * 根据code返回其对应的level
     */
    protected function code2level($code){
    
        foreach( $this->codeLevel as $level=>$arr){
        
            if( in_array($code, $arr) ){
                return $level;
            }elseif( strpos($code, '.') ){
                
                $tmp_arr = explode('.', $code);
                $this_code = $tmp_arr[0] . '.*';
                if( in_array($this_code, $arr) ){
                    return $level;
                }
            }
        }

        return 'undefined';
    }

    /**
     * 检测规则是否存在，比如检测 int 这个规则是否存在
     */
    protected function hasExists($rule){
    
        ///规则是否存在
        $range = array_merge($this->rrange['along'], $this->rrange['main']);
        if(!in_array($rule, $range)) return false;
        return true;
    }

    /**
     * 根据规则检查字段数据
     */
    protected function ckEveryFields($data){
        ///没有字段
        if( empty($this->fields) ) return false;

        ///外层初始化数据
        $this->data = $data;//包含所有字段数据的数组

        ///遍历每个字段
        foreach( $this->fields as $key=>$field){

            #初始化数据
            $this->field = $field;//当前正在检查的字段
            $this->fieldKey = $key;//当前正在检查的字段对应的下标
            
            $this_field_rule = $this->rule[$key];//当前字段对应的校验规则组，如：required$||int$|min&:10$|max&:20

            ///检查是否有规则分隔符
            $tmp_flag_pos = strpos($this_field_rule, '$||');
            if( $tmp_flag_pos ){#该字段指定了多个规则检查，如：required$||int$|min&:10$|max&:20
            
                $this->multiRule($this_field_rule);

            }else{#该字段只指定了1个规则，如：int$|min&:10$|max&:20

                $this->singleRule($this_field_rule);
            }
        }
        return $this;
    }

    /**
     * 多个主/独立规则，如：required$||int$|min&:10$|max&:20
     */
    protected function multiRule($rule_str){
    
        $rules_arr = explode('$||', $rule_str);
        foreach( $rules_arr as $k=>$rule){
        
            $this->singleRule($rule);
        }
    }

    /**
     * 单个主/独立规则，如：int$|min&:10$|max&:20
     */
    protected function singleRule($rule_str){
        
        #记录当前的规则字符串，用于保存到err信息
        $this->nowRuleStr = $rule_str;

        #检查规则中是否有"副规则"分隔符
        $tmp_flag_pos = strpos($rule_str, '$|');
        if( $tmp_flag_pos ){##有副规则，如：int$|min&:10$|max&:20

            ##数据不存在，且规则又不是required时，则没必要继续检查（仅required规则负责检查存在与空字符串的问题）
            $tmp = explode('$|', $rule_str);
            // if( (!isset($this->data[$this->field]))&&($tmp[0]!='required') ) return true;
            if( $this->can_pass($tmp[0]) ) return true;

            $this->hasSecRule($rule_str);
        
        }else{##没有副规则，如：int

            ##数据不存在，且规则又不是required时，则没必要继续检查（仅required规则负责检查存在与空字符串的问题）
            // if( (!isset($this->data[$this->field]))&&($rule_str!='required') ) return true;
            if( $this->can_pass($rule_str) ) return true;

            $this->noSecRule($rule_str);
        }
    }

    /**
     * 是否跳过检查
     */
    protected function can_pass($rule){

        ///值为指定的排除值，则不检查
        if( isset($this->excludes[$this->field]) ){# 当前被检查的字段存在排除值时

            # 支持给定的排除值为数组和单值类型
            if( is_array($this->excludes[$this->field]) ){
            
                $can_pass = in_array($this->data[$this->field], $this->excludes[$this->field]);
            }else{
                $can_pass = $this->data[$this->field]==$this->excludes[$this->field];
            }
        }else{# 当前被检查的字段不存在排除值
            ## 数据不存在，且规则又不是required时，则没必要继续检查（仅required规则负责检查存在与空字符串的问题）
            $can_pass = ($this->data[$this->field]=='')&&($rule!='required');
        }

        return $can_pass;
    }

    /**
     * 针对 有副规则 的规则字段进行数据检查
     */
    protected function hasSecRule($rule_str){
        
        ///根据"$|"切割数据，如：int$|min:10$|max:20 切割后为 Array ( [0] => int [1] => min&:10 [2] => max&:20 )
        $vice = explode('$|', $rule_str);
        
        #初始化参数
        $this_rule = array_shift($vice);//第一个元素即为rule

        ///能进本方法，说明有副规则，则规则一定不会为"required"(只有当required时，数据不存在才需要检查，其他规则，数据如果不存在，也不需要检查)
        if( !isset($this->data[$this->field]) ) return true;

        ///为独立型规则
        if(in_array($this_rule, $this->rrange['along'])) return $this->mkSysErr('1003', $this_rule);

        ///规则不存在
        if(!$this->hasExists($this_rule)) return $this->mkSysErr('1002', $this_rule);

        ///规则数据检测
        $this->double($this_rule, $vice);
    }

    /**
     * 针对 独立型 或 可单独使用的主规则 的规则字段进行数据检查
     */
    protected function noSecRule($rule_str){//如：int

        ///该规则为 不可独立 使用的规则
        if( in_array($rule_str, $this->denySingle) ) return $this->mkSysErr('1001', $rule_str);

        ///规则不存在
        if(!$this->hasExists($rule_str)) return $this->mkSysErr('1002', $rule_str);

        ///规则数据检测
        return $this->single($rule_str);
    }

    /**
     * 设置返回err信息
     * $code
     * $rule  string 如：int
     * $vice  array  如：['min', 10]
     */
    protected function mkErr($code, $rule, $vice=[]){
        
        $this->err[$this->field]['code']    = $code;
        $this->err[$this->field]['name']    = $this->field;
        $this->err[$this->field]['value']   = $this->data[$this->field];
        $this->err[$this->field]['rule']    = $this->nowRuleStr;
        $this->err[$this->field]['level']   = $this->code2level($code);
        $this->err[$this->field]['msg']     = $this->setMsg($rule, $vice, $code);

        return $this;
    }

    /**
     * @method  getErrMsg
     * 方法作用：将所有的非sys错误信息连接成一句返回的错误信息
     * 
     * @param   $fieldOrIndex  string|int    指定的字段或索引
     * 
     * @return  string
     */
    public function getErrMsg($fieldOrIndex=''){

        ///先判断，后遍历效率更高
        if( $fieldOrIndex==='' ){///没有指定读取的字段
        
            $all_err = [];
            foreach( $this->err as $tmp_field=>$err){
            
                $all_err[] = $err['msg'];
            }
            return implode(';', $all_err);
            
        }else{///指定了读取的字段或第几个err

            if( is_numeric($fieldOrIndex) ){#指定返回第几个错误
            
                foreach( $this->err as $tmp_field=>$err){
                    return $err['msg'];
                }
            }else{#指定具体的字段
                foreach( $this->err as $tmp_field=>$err){
            
                    if( $tmp_field==$fieldOrIndex ){
                        return $err['msg'];
                    }
                }
            }
        }
    }

    /**
     * 设置返回system err信息
     * $code
     * $rule
     */
    protected function mkSysErr($code, $rule){
    
        // $thisKey = count($this->sysErr[$this->field]);
        $this->sysErr[$this->field]['code'] = $code;
        $this->sysErr[$this->field]['rule'] = $this->nowRuleStr;
        $this->sysErr[$this->field]['codemsg'] = $this->code2cmsg($code, '{rule}', $rule);
        $this->sysErr[$this->field]['level'] = $this->code2level($code);

        return $this;
    }

    /**
     * 设置返回信息
     * $rule  string 如：int
     * $vice  array  如：['min',10]
     */
    protected function setMsg($rule, $vice, $code){

        $this_rule = empty($vice) ? $rule : $rule.'.'.$vice[0];

        ///有传则用传的
        if( isset($this->msg[$this->field]) ){
            
            $tmp_msg = $this->msg[$this->field];
            #下标为default表示 独立型规则 或 可以单独使用的主规则 的对应用户指定信息；否则则为 主副型 用户指定信息
            $this_msg = isset($tmp_msg[$rule]['default']) ? $tmp_msg[$rule]['default'] : (isset($tmp_msg[$rule][$vice[0]])?$tmp_msg[$rule][$vice[0]]:'');
        }
        
        ///没传则用默认的
        if( empty($this_msg) ){

            $this_msg = $this->code2cmsg($code, '{field}', $this->field);
        }

        ///返回msg
        return $this->replace([
            '{val}' => $vice[1],
            '{field}' => $this->field,
        ], $this_msg);
    }

    /**
     * 批量替换字符
     */
    protected function replace($replaceArr, $subject){
    
        foreach( $replaceArr as $search=>$replace){
        
            $subject = str_replace($search, $replace, $subject);
        }
        return $subject;
    }

    /**
     * 对 主副结合型规则 进行对应数据检测
     */
    protected function double($rule, $viceArr=[]){

        ///设定当前规则类型标识，double表示规则为 主副结合型规则
        $this->ruleFlag = 'double';

        ///根据规则进行检测
        if( $rule=='int' ):

            $this->ckInt($rule, $viceArr);
            
        elseif( $rule=='float' ):

            // $this->ckFloat($rule, $viceArr);

        elseif( $rule=='regex' ):
            
            $this->ckRegex($rule, $viceArr);
        endif;
    }

    /**
     * 对 独立规则 或 可单独使用的主规则 进行对应数据检测
     */
    protected function single($rule){

        ///设定当前规则类型标识，single表示规则为 独立规则 或 可单独使用的主规则
        $this->ruleFlag = 'single';
    
        ///根据规则进行检测
        if( $rule=='required' ):

            if( !$this->ckRequired() ): $this->mkErr('2001', $rule); endif;

        elseif( $rule=='email' ):

            if( !$this->ckRegexGo('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/') ): $this->mkErr('2004', $rule); endif;

        elseif( $rule=='ip' ):

            if( !$this->ckRegexGo('/^(?:(?:25[0-9]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.){3}(?:25[0-9]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])$/') ): $this->mkErr('2008', $rule); endif;

        elseif( $rule=='cell' ):

            // if( !$this->ckRegexGo('/^[1]([3-9])[0-9]{9}$/') ): $this->mkErr('2005', $rule); endif;
            if( !$this->ckRegexGo('/^1(3[0-9]|4[57]|5[0-9]|6[0-9]|7[0135678]|8[0-9]|9[0-9])\\d{8}$/') ): $this->mkErr('2005', $rule); endif;

        elseif( $rule=='phone' ):

            if( !$this->ckRegexGo('') ): $this->mkErr('2006', $rule); endif;

        elseif( $rule=='ch-utf8' ):

            if( !$this->ckRegexGo('/^[\x{4e00}-\x{9fa5}]+$/u') ): $this->mkErr('2009', $rule); endif;

        elseif( $rule=='int' ):

            $this->ckInt($rule);

        elseif( $rule=='float' ):
        
        endif;

        return $this;
    }

    /**
     * $viceArr 如：array(1) { [0]=> string(23) "@&:/^[1]([3-9])[0-9]{9}$/" }
     */
    protected function ckRegex($rule, $viceArr){

        $vice_arr = explode('&:', $viceArr[0]);
        ///需要副规则值，却没给副规则值
        if( $vice_arr[0]===''||(isset($vice_arr[1])&&$vice_arr[1]==='') ) return $this->mkSysErr('1004', $rule);

        $vice_name = $vice_arr[0];//副规则名
        $vice_val = $vice_arr[1];//副规则值
        $this_rule = $rule.'.'.$vice_name;

        ///副规则名不在限定范围内
        if(!in_array($vice_name, $this->rrange['vice'][$rule])) return $this->mkSysErr('1005', $this_rule);

        ///用规则检测数据
        if( !$this->ckRegexGo($vice_val) ) return $this->mkErr('2010', $rule);
    }

    /**
     * 根据正则规则对数据进行匹配
     * $vice  string  正则规则，如：/^[1]([3-9])[0-9]{9}$/
     */
    protected function ckRegexGo($vice){
    
        ///需要检查的数据
        $subject = $this->data[$this->field];

        ///正则匹配
        $re = preg_match($vice, $subject, $matches);

        if( empty($matches) ) return false;
        return true;
    }

    /**
     * 数据匹配required规则
     */
    protected function ckRequired(){
        
        $is_err = (!isset($this->data[$this->field]) || $this->data[$this->field]=='') ? 1 : 0;

        if( $is_err ) return false;
        return true;
    }

    /**
     * $viceArr 如：Array ( [0] => int [1] => min&:10 [2] => max&:20 )
     */
    protected function ckInt($rule, $viceArr=[]){

        if( empty($viceArr) ){///无副规则
        
            if(!is_int($this->data[$this->field])) return $this->mkErr('2002', $rule);

        }else{///有副规则

            foreach( $viceArr as $k=>$vice){
            
                $vice_arr = explode('&:', $vice);
                #需要副规则值，却没给副规则值
                if( $vice_arr[0]==='' ) return $this->mkSysErr('1004', $rule);
                $vice_name = $vice_arr[0];//副规则名
                $vice_val = $vice_arr[1];//副规则值
                $this_rule = $rule.'.'.$vice_name;

                #副规则名不在限定范围内
                if(!in_array($vice_name, $this->rrange['vice'][$rule])) return $this->mkSysErr('1005', $this_rule);

                #检查数据值
                if( $code=$this->ckIntGo($vice_name, $vice_val) ) return $this->mkErr($code, $rule, $vice_arr);
            }
        }
    }

    protected function ckIntGo($vice_name, $vice_val){

        $code       = 0;
        ///检查数据
        switch($vice_name){
            case '>':
                ($this->data[$this->field] > $vice_val) || ($code = '2003.1');
            break;
            case '>=':
            case 'min':
                ($this->data[$this->field] >= $vice_val) || ($code = '2003.2');
            break;
            case '<':
                ($this->data[$this->field] < $vice_val) || ($code = '2003.3');
            break;
            case '<=':
            case 'max':
                ($this->data[$this->field] <= $vice_val) || ($code = '2003.4');
            break;
            case '=':
                ($this->data[$this->field] == $vice_val) || ($code = '2003.5');
            break;
        }

        return $code;
    }
}