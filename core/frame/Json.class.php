<?php

class Json{
    private $_re;

    public function __construct(){
        
        $this->obj()->stat()->msg();

        return $this;
    }

    private static $Json=null;

    public static function __callStatic($name, $arguments){
        
        if( empty(self::$Json) ){
            self::$Json = new self;
        }
        
        return self::magicCommon($name, $arguments);
    }

    public function __call($name, $arguments){
    
        if( empty(self::$Json) ){
            self::$Json = new self;
        }

        return self::magicCommon($name, $arguments);
    }

    /**
     * __callStatic与__call 调取的公共方法
     */
    private static function magicCommon($name, $arguments){
    
        Err::try()

        if( $name=='throwErr' ){
        
            return self::$Json->throwErr($arguments[0]);
        }

        $err = new \Exception('非法的操作: Err::'.$name);
        self::$Err->handle($err);
        exit;
    }

    /**
     * @method  obj
     * 方法作用：根据字符串生成json对象，默认生成空的json对象
     * 
     * @param    $str    string    json字符串
     * 
     * @return    object
     */
    public function obj($str='{}'){
        
        $this->_re = json_decode($str);

        return $this;
    }

    /**
     * @method  arr
     * 方法作用：调用此方法，可以最终根据指定的数组转换为json字符串
     * 
     * @param    $arr    array    数组
     * 
     * @return    object
     */
    public function arr($arr=[]){

        $this->_re = $arr;

        return $this;
    }

    /**
     * @method  stat
     * 方法作用：设置返回的状态码，这个方法专门针对jui后台框架，如果不是为了支持jui框架，则请根据实际需要来调用
     * 
     * @param    $stat    int    状态码，200成功；300失败
     * 
     * @return    object
     */
    public function stat($stat=200){
    
        if( is_object($this->_re) ){
            $this->_re->statusCode = $stat;
        }elseif( is_array($this->_re) ){
            $this->_re['statusCode'] = $stat;
        }

        return $this;
    }

    /**
     * @method  msg
     * 方法作用：设置返回的提示信息，这个方法专门针对jui后台框架，如果不是为了支持jui框架，则请根据实际需要来调用
     * 
     * @param    $msg    string    提示信息
     * 
     * @return    object
     */
    public function msg($msg='操作成功'){
    
        if( is_object($this->_re) ){
            $this->_re->message = $msg;
        }elseif( is_array($this->_re) ){
            $this->_re['message'] = $msg;
        }

        return $this;
    }

    /**
     * @method  navtab
     * 方法作用：设置返回的navtabId，这个方法专门针对jui后台框架刷新页面使用，如果不是为了支持jui框架，则请根据实际需要来调用
     * 
     * @param    $navtab    string    jui框架页面的navtabId
     * 
     * @return    object
     */
    public function navtab($navtab){
    
        if( is_object($this->_re) ){
            $this->_re->navTabId = $navtab;
        }elseif( is_array($this->_re) ){
            $this->_re['navTabId'] = $navtab;
        }

        return $this;
    }

    /**
     * @method  vars
     * 方法作用：设置json额外内容，这个方法是用于拓展json返回的内容，请根据实际需要来调用
     * 
     * @param    $key_val_arr    array    值对信息，如：$key_val_arr=['msg', 'xxxxx'] 或 $key_val_arr=[['stat', 300], ['msg', 'xxx']]
     * 
     * @return    object
     */
    public function vars($key_val_arr){
    
        if( is_array($key_val_arr[0]) ){//二维数组，多个值对
        
            foreach( $key_val_arr as $v){
                if( is_object($this->_re) ){
                    $this->_re->$v[0] = $v[1];
                }else{
                    $this->_re[$v[0]] = $v[1];
                }
            }
        }else{//一位数组，单个值对
            
            if( is_object($this->_re) ){
                $this->_re->$key_val_arr[0] = $key_val_arr[1];
            }else{
                $this->_re[$key_val_arr[0]] = $key_val_arr[1];
            }
        }
        return $this;
    }

    /**
     * @method  exec
     * 方法作用：将构建好的Json对象以指定方式返回
     * 
     * @param    $type    string    返回类型
                        $type='echo'表示以echo方式输出json字符串
                        $type='return'表示以return方式返回json对象
     * @return    mixed
     */
    public function exec($type='echo'){
    
        if( $type==='echo' ){
            header('Content-Type:application/json');
            echo json_encode($this->_re);
            exit;
        }elseif ($type==='return') {
            return json_encode($this->_re);
        }
    }

    /**
     * @method  decode
     * 方法作用：将构建好的Json对象以指定方式返回
     * 
     * @param    $str       string    json字符串
     * @param    $prefix    string    解析后的属性前缀
     * 
     * @return    mixed
     */
    public function decode($str, $prefix='j_'){
    
        $data = json_decode($str, true);
        // if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
        if ( is_array($data) && !empty($data) ) {

            foreach( $data as $k=>$v){
                $key = $prefix . $k;
                $this->$key = $v;
            }
            return $this;
        }
        return $str;/// 非标准json格式字符串，则原样将该字符串返回
    }
}

