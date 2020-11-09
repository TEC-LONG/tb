<?php

class TB{

    protected static $TB;

    protected $table;
    protected $select;
    protected $orderby;
    protected $groupby;
    protected $limit;
    protected $where=[];
    protected $left_join=[];

    protected $e;
    protected $sql;
    protected $pdostatement;

    protected $fields;
    protected $insert=[];
    protected $flag;//操作标识  'insert':新增操作  'update':更新操作    'delete':删除操作

    protected $update=[];
    protected $update_fields=[];

    public $pagination=[];

    public function __construct($host='',$port='',$char='',$db='',$user='',$pwd=''){

        $this->_host    = empty($host)  ? Config::C('PDO.MYSQL.host')       : $host;
		$this->_port    = empty($port)  ? Config::C('PDO.MYSQL.port')       : $port;
		$this->_char    = empty($char)  ? Config::C('PDO.MYSQL.charset')    : $char;
		$this->_db      = empty($db)    ? Config::C('PDO.MYSQL.dbname')     : $db;
		$this->_user    = empty($user)  ? Config::C('PDO.MYSQL.user')       : $user;
        $this->_pwd     = empty($pwd)   ? Config::C('PDO.MYSQL.pwd')        : $pwd;
        
		$this->_dsn = "mysql:host={$this->_host};port={$this->_port};charset={$this->_char};dbname={$this->_db}";
        $this->_pdo = new \PDO($this->_dsn,$this->_user,$this->_pwd);

        if( is_object($this->_pdo) ){
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        }else{

            if( Config::C('DEBUG')===1 ){
            
                echo '连接数据库失败！'; 
            }
            Log::msg('连接数据库失败！');
            exit;
        }
        // if( isset($this->table) ) $this->gotable($this->table);
    }

    public static function __callStatic($name, $arguments){
        
        if( empty(self::$TB) ){
            self::$TB = new static;# 此处不应该用self,而应该使用静态延时绑定的static,以支持子类的继承
        }
        
        return self::magicCommon($name, $arguments);
    }

    public function __call($name, $arguments){
    
        if( empty(self::$TB) ){
            self::$TB = new static;
        }

        return self::magicCommon($name, $arguments);
    }

    /**
     * __callStatic与__call 调取的公共方法
     */
    private static function magicCommon($name, $arguments){

        return Err::try(function () use ($name, $arguments){

            $allow_method = [
                'leftjoin',
                'pagination',
                'dbug',
                'table',
                'select',
                'where',
                'orderby',
                'groupby',
                'limit',
                'fields',
                'update',
                'insert',
                'get',
                'exec',
                'delete',
                'find',
                'last_insert_id'
            ];
        
            if( in_array($name, $allow_method) ){
            
                $method = 'go' . $name;
                return self::commonUse($method, $arguments);
            }

            Err::throw('非法的操作: '.__CLASS__.'::'.$name);

        }, 'exit');
    }

    private static function commonUse($method, $arguments=null){
    
        if( is_array($arguments)&&isset($arguments[0]) ){

            if( isset($arguments[1]) ){

                return self::$TB->$method($arguments[0], $arguments[1]);
            }
            
            return self::$TB->$method($arguments[0]);
            
        }
        return self::$TB->$method();
    }

    protected function gotable($table){
        $this->table = $table;
        return $this;
    }

    protected function init(){
    
        $this->e = '';
        $this->limit = '';
        $this->select = '';
        $this->orderby = '';
        $this->fields = '';

        $this->where = [];
        $this->insert = [];
        $this->update = [];
        $this->left_join = [];
        $this->update_fields = [];
    }

    /**
     * method:指定查询字段列表
     * @param $select string 字段列表，仅支持字符串类型
                如：$select='name, age, user.id'
     * @return object
     */
    protected function goselect($select){
        $this->select = $select;
        return $this;
    }

    /**
     * method:指定条件
     * @param $where string|array 条件
                可能的情况有：
                (1) $where=['id', '>', 10] 或 $where=['id', 10] ==》等同于 $where=['id', '=', 10]
                (2) $where="name='zhangsan' and id in (1, 2, 3)"
                (3) $where=[['age',12],['height', '<=', '2.0'], ['title', 'like', 'aa']]
                (4) $where='user.id=1'
                (5) $where=['id', 'in', '(1, "2", 3)']
                (6) $where=1;
     * @return object
     */
    protected function gowhere($where){

        $tmp_no_need_quote = ['in', 'not in', 'between'];//不需要在值两侧包裹引号的
        $tmp_need_space = ['in', 'not in', 'like', 'between'];//需要在0,1,2元素之间加上空格的
        if( $this->is2arr($where)==1 ){//一维数组  $where=['name', '=', 'xxx']

            $where[0] = $this->explodePointField($where[0]);# `xx`.`xx`

            // $where[0] = '`' . $where[0] . '`';//字段两侧加反引号
            if( count($where)==3 ){//三个元素  $where=['name', '=', 'xxx']
                
                if( $where[1]=='like' ){
                    $where[2] = '%' . $where[2] . '%';
                }

                if( !in_array($where[1], $tmp_no_need_quote) ){
                    $where[1] = ' ' . $where[1] . ' ';
                    $where[2] = '"' . str_replace('"', '\'', $where[2]) . '"';//数据两侧加双引号
                }

                if( in_array($where[1], $tmp_need_space) ){
                    $where = implode(' ', $where);
                }else{
                    $where = implode('', $where);
                }

            }else{//两个元素  $where=['name', 'xxx']
                
                $where[1] = '"' . str_replace('"', '\'', $where[1]) . '"';
                $where = $where[0] . '=' . $where[1];
            }
            
        }elseif( $this->is2arr($where)==2 ){//二维数组    $where=[['name', '=', 'xxx'], ['age', '>=', 10]]

            $tmp = [];
            foreach( $where as $one){
                
                $son[0] = $this->explodePointField($where[0]);# `xx`.`xx`

                if( count($one)==3 ){//三个元素  $one=['name', '=', 'xxx']

                    if( $one[1]=='like' ){
                        $one[2] = '%' . $one[2] . '%';
                    }

                    if( !in_array($one[1], $tmp_no_need_quote) ){
                        $one[2] = '"' . str_replace('"', '\'', $one[2]) . '"';//  "xxx"//数据两侧加双引号
                    }

                    if( in_array($one[1], $tmp_need_space) ){

                        $tmp1 = implode(' ', $one);// name in (1, 2, 3)
                    }else{
                        $tmp1 = implode('', $one);// name="xxx"
                    }
                }else{//两个元素  $one=['name', 'xxx']

                    $one[1] = '"' . str_replace('"', '\'', $one[1]) . '"';
                    $tmp1 = $one[0] . '=' . $one[1];
                }
                
                $tmp[] = $tmp1;
            }

            $where = implode(' and ', $tmp);//    name="xxx" and age="10"
        }

        // $this->where[] = str_replace('\'', '"', $where);//统一数据包裹符号为双引号（这么做是不对的，没有考虑数据内的引号问题）
        $this->where[] = $where;//统一数据包裹符号为双引号
        return $this;
    }

    /**
     * method:指定order by条件
     * @param $orderby string order by条件，仅支持字符串类型
                如：$orderby='post_date' 或 $orderby='post_date desc'
     * @return object
     */
    protected function goorderby($orderby){
    
        $this->orderby = ' order by ' . $orderby;
        return $this;
    }

    /**
     * method:指定group by条件
     * @param $groupby string group by条件，仅支持字符串类型
                如：$groupby='user__id'
     * @return object
     */
    protected function gogroupby($groupby){
    
        $this->groupby = ' group by ' . $groupby;
        return $this;
    }

    /**
     * method:指定limit条件
     * @param $limit string limit条件，仅支持字符串类型
                如：$limit=1 或 $limit='0, 20'
     * @return object
     */
    protected function golimit($limit){
    
        $this->limit = ' limit ' . $limit;
        return $this;
    }

    /**
     * method:连表操作--左连接
     * @param $right_tb string 需要连的表其表名，如：$right_tb='user_info'
     * @param $con string 连表指定的on条件，如：$on='user.id=user_info.user__id'
     * @return object
     */
    protected function goleftjoin($right_tb, $on){

        $this->left_join[] = ' left join ' . $right_tb . ' on ' . $on;
        return $this;
    }

    protected function query($type=1, $sql=''){

        if( $type==1 )://执行查询
            
            if(empty($sql)) $sql = $this->get_sql();
            try{
                $this->pdostatement=$this->_pdo->query($sql);
                // $this->init();//执行完SQL语句则初始化一次
            }catch(\PDOException $e){//捕获异常,并且进行捕获异常后的处理.
    
                $this->e = $e;//记录错误对象
                if( Config::C('DEBUG')===1 ){
                    $this->godbug('err.echo');//如果为调试模式，则直接输出
                }else{
                    $this->godbug('err.log');//如果是非调试模式，则记录日志
                }
                return false;
            }
            return true;
        elseif ($type==2)://执行增，删，改

            if(empty($sql)) $sql = $this->get_sql(2);
            if( is_array($sql) ){//本条件只对批量更新有效，若为批量更新，则使用事务
            
                try{
                    $this->_pdo->beginTransaction();
                    foreach( $sql as $one_sql){
                    
                        $this->_pdo->exec($one_sql);
                    }
                    $re = $this->_pdo->commit();//全部执行成功则提交事务
                    // $this->init();//执行完SQL语句则初始化一次

                }catch(\PDOException $e){

                    // $this->init();//出现错误也视为执行完，执行完SQL语句则初始化一次
                    $this->e = $e;//记录错误对象
                    if( Config::C('DEBUG') ){
                        $this->godbug('err.echo', $one_sql);
                    }else{
                        $this->godbug('err.log', $one_sql);
                    }
                    $re = $this->_pdo->rollBack();//有问题则立即回滚
                    return false;
                }

            }else{

                try{
                    $this->_pdo->exec($sql);
                    // $this->init();//执行完SQL语句则初始化一次
                }catch(\PDOException $e){
        
                    // $this->init();//出现错误也视为执行完，执行完SQL语句则初始化一次
                    $this->e = $e;//记录错误对象
                    if( Config::C('DEBUG') ){
                        $this->godbug('err.echo');
                    }else{
                        $this->godbug('err.log');
                    }
                    return false;
                }
            }

            return true;
        endif;
    }

    protected function gopagination($nowPage=1, $numPerPage=32){

        ///得到当前的查询语句
        $sql = $this->get_sql();

        ///将select与from之间的内容替换成$this->pagination_select
        $pattern = '/^select.*from/';
        preg_match($pattern, $sql, $matches);

        if( isset($matches[0]) ){
            
            $this->sql = $sql = str_replace($matches[0], 'select count(*) as num from', $sql);
            
            $re = $this->query(1, $sql);
            $this->pagination['pageNum'] = $nowPage;
            $this->pagination['numPerPage'] = $numPerPage;

            if( $re ){
                $row = $this->pdostatement->fetch(\PDO::FETCH_ASSOC);
                
                $this->pagination['totalNum'] = $row['num'];
                $this->pagination['totalPageNum'] = intval(ceil(($this->pagination['totalNum']/$this->pagination['numPerPage'])));
                $this->pagination['limitM'] = ($this->pagination['pageNum']-1)*$this->pagination['numPerPage'];
            }

            return $this;
        }
        // $page = [];
        // $page['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];
        // $page['pageNum'] = $pageNum = isset($request['pageNum']) ? intval($request['pageNum']) : (isset($_COOKIE['pageNum']) ? intval($_COOKIE['pageNum']) : 1);
        // setcookie('pageNum', $pageNum);
        // $page['numPerPage'] = $numPerPage = isset($request['numPerPage']) ? intval($request['numPerPage']) : $num_per_page;
        // $tmp_arr_totalNum = M()->table($tb)->select('count(*) as num')->where($condition)->find();
        // $page['totalNum'] = $totalNum = $tmp_arr_totalNum['num'];
        // $page['totalPageNum'] = intval(ceil(($totalNum/$numPerPage)));
        // $page['limitM'] = ($pageNum-1)*$numPerPage;

        // return $page;

        

    }

    protected function get_sql($type=1){
        $sql = '';
        if( $type==1 ){//返回查询sql语句

            $sql = 'SELECT %s FROM %s%s WHERE %s';
            if( empty($this->select) ) $this->select='*';
            if( empty($this->where) ) $this->where='1';
            $sql = sprintf($sql, $this->select, $this->table, implode(' ', $this->left_join), implode(' and ', $this->where));

            if(!empty($this->groupby)) $sql .= ' ' . $this->groupby;
            if(!empty($this->orderby)) $sql .= ' ' . $this->orderby;
            if(!empty($this->limit)) $sql .= ' ' . $this->limit;

            // $sql = 'select ' . $this->select . ' from ' . $this->table . implode(' ', $this->left_join) . ' where ' . implode(' and ', $this->where);
            // if(!empty($this->limit)) $sql .= ' ' . $this->limit;
        }elseif ($type==2){ //返回 增/删/改 SQL语句

            if( $this->flag==='insert' )://新增
                //      insert into xx (x, x, x) values (x, x, x)
                $sql = 'INSERT INTO %s %s VALUES %s';
                
                $sql = sprintf($sql, $this->table, $this->fields, implode(',', $this->insert));
            elseif ($this->flag==='update')://更新
    
                $count_fields = count($this->update_fields);
                $count_update = count($this->update);
                $count_where = count($this->where);

                $is_accordance = ($count_fields===$count_update) && ($count_fields===$count_where);

                // if(!$is_accordance) echo '字段、数据和条件配对不一致！';

                if( $count_fields===1 ){//单条更新
                
                    $count_fields_son = count($this->update_fields[0]);
                    $count_update_son = count($this->count_update[0]);
                    
                    // if($count_fields_son!==$count_update_son) echo '字段个数与数据个数不匹配';

                    $sql = 'UPDATE %s SET %s WHERE %s';

                    $tmp_arr_target = [];
                    foreach( $this->update_fields[0] as $k=>$field){
                        $tmp_arr_target[] = isset($this->update[0][$k]) ? $field.'='.$this->update[0][$k] : $field.'='.$this->update[0][$field];
                    }
                    $target = implode(',', $tmp_arr_target);

                    $sql = sprintf($sql, $this->table, $target, $this->where[0]);

                }else{//批量更新
                    
                    $sql = [];
                    foreach( $this->update_fields as $k=>$fields_row){
                    
                        $count_fields_son = count($this->fields_row);
                        $count_update_son = count($this->count_update[$k]);

                        // if($count_fields_son!==$count_update_son) echo '字段个数与数据个数不匹配';

                        $tmp_sql = 'UPDATE %s SET %s WHERE %s';

                        $tmp_arr_target = [];
                        foreach( $fields_row as $k1=>$field){
                            $tmp_arr_target[] = isset($this->update[$k][$k1]) ? $field.'='.$this->update[$k][$k1] : $field.'='.$this->update[$k][$field];
                        }
                        $target = implode(',', $tmp_arr_target);

                        $sql[] = sprintf($tmp_sql, $this->table, $target, $this->where[$k]);
                    }
                }
                
            elseif ($this->flag==='delete')://删除
                
                $sql = 'DELETE FROM %s WHERE %s';
                $sql = sprintf($sql, $this->table, implode(' and ', $this->where));
                
            endif;
        }

        return $this->sql=$sql;
    }

    /**
     * method:为新增指定添加数据的字段；或为修改指定修改数据的字段
     * @param $fields string|array
                可能的情况有：
                (1) $fields='name, parent_id, post_date'   添加和修改均可使用这种方式指定字段 
                (2) $fields=['name', 'parent_id', 'post_date']   添加和修改均可使用这种方式指定字段 
                (3) $fields=[
                        ['name', 'parent_id', 'post_date'],
                        ['name', 'parent_id'],
                        ['parent_id', 'post_date']
                    ]    仅用于批量更新时指定字段
     * @return object
     */
    protected function gofields($fields){

        $that = $this;
    
        if( is_array($fields) ){//传进来的是数组  $fields=['name', 'age',...]

            if( $this->is2arr($fields)==1 ){

                $this->update_fields[]  = $fields;//这个操作只针对搜集更新字段有效
                
                $fields = array_map(function ($elem) use($that){
                    return $that->explodePointField($elem);
                }, $fields);

                $this->fields = '(' . implode(',', $fields) . ')';

            }elseif ( $this->is2arr($fields)==2 ) {//这个操作只针对搜集更新字段有效
                
                foreach( $fields as $row){

                    $this->update_fields[] = array_map(function ($elem) use($that){
                        return $that->explodePointField(trim($elem));
                    }, $row);
                }
            }
            
        }else {//传进来的是字符串  $fields='name, age,...'

            $tmp = explode(',', $fields);
            $tmp = $this->update_fields[] = array_map(function ($val) use($that){
                return $that->explodePointField(trim($val));
            }, $tmp);//这个操作只针对搜集更新字段有效

            $this->fields = '(' . implode(',', $tmp) . ')';//针对新增
        }
        return $this;
    }

    /**
     * 拆分 xx.xx字段结构
     */
    protected function explodePointField($field){
    
        if( !strpos($field, '.') ) return $field;

        $arr = explode('.', $field);
        return '`'.$arr[0].'`.`'.$arr[1].'`';
    }

    protected function godelete(){
        
        $this->flag = 'delete';//操作标识， delete代表在get_sql方法中返回删除数据的SQL语句来执行
        
        if( $this->query(2) )
            return true;
        else
            return false;
    }

    /**
     * method:指定更新的数据
     * @param $update array 通过fields方法指定字段对应的更新数据
                可能的情况有：
                (1) $update=['aaab', 18, time()]  这种方式用于更新一条数据
                (2) $update=[
                        ['a', 18, time()],
                        ['b', 18],
                        ['c', time()]
                    ]   这种方式用于批量更新数据
     * @return object
     */
    protected function goupdate($update){

        if( $this->is2arr($update)==1 ){//一维数组  $insert=['zhangsan', 12, '@age+1', '@concat(child_ids, ',10')']

            $tmp_keys = array_keys($update);
            if(!is_numeric($tmp_keys[0])){//键为字符串类型，则表示传进来的数组下标代表字段名，值为数据值
                $this->gofields($tmp_keys);
            }

            $this->update[] = array_map(function ($val){
                if( substr($val, 0, 1)=='@' ){#不加引号
                    return str_replace('"', '\'', str_replace('@', '', $val));
                }else{#加引号
                    return '"' . str_replace('"', '\'', $val) . '"';
                }
            }, $update);
        
        }elseif ($this->is2arr($update)==2) {//二维数组  $insert=[['zhangsan', 12],['lisi', 13]]
            
            foreach( $update as $row){
            
                $this->update[] = array_map(function ($val){
                    if( substr($val, 0, 1)=='@' ){#不加引号
                        return str_replace('"', '\'', str_replace('@', '', $val));
                    }else{#加引号
                        return '"' . str_replace('"', '\'', $val) . '"';
                    }
                }, $row);
            }
        }
        $this->flag = 'update';//操作标识， update代表接下来如果调用exec方法则将执行update操作
        return $this;
    }

    /**
     * method:指定新增的数据
     * @param $insert string|array 通过fields方法指定字段对应的新增数据
                可能的情况有：
                (1) $insert='"bb", 12, '.time()     用于新增一条数据
                (2) $insert="'bb', 12, ".time()     用于新增一条数据
                (3) $insert='bb, 12, '.time()       用于新增一条数据
                (4) $insert=['aa', 12, time()]      用于新增一条数据
                (5) $insert=['name'=>'ee', 'parent_id'=>17, 'post_date'=>time()]  用于新增一条数据，这种方式同时指定了数据对应的字段，所以可以不用额外使用fields方法指定字段
                (6) $insert=[
                        ['ff', 18, time()],
                        ['gg', 14, time()],
                        ['hh', 19, time()],
                        ['ii', 21, time()]
                    ]   用于一次新增多条数据
     * @return object
     */
    protected function goinsert($insert){
        
        if( $this->is2arr($insert)==1 ){//一维数组  $insert=['zhangsan', 12]或$insert=['name'=>'zhangsan', 'age'=>12]

            $tmp_keys = array_keys($insert);
            if(!is_numeric($tmp_keys[0])){//键为字符串类型，则表示传进来的数组下标代表字段名，值为数据值
                $this->fields = '(' . implode(',', $tmp_keys) . ')';
            }

            $tmp = array_map(function ($val){
                return '"' . str_replace('"', '\'', $val) . '"';//为数据两边加上双引号包裹
            }, $insert);
            $this->insert[] = '(' . implode(',', $tmp) . ')';
        
        }elseif ($this->is2arr($insert)==2) {//二维数组  $insert=[['zhangsan', 12],['lisi', 13]]
            
            foreach( $insert as $insert_val){
            
                $tmp = array_map(function ($val){
                    return '"' . str_replace('"', '\'', $val) . '"';//为数据两边加上双引号包裹
                }, $insert_val);

                $this->insert[] = '(' . implode(',', $tmp) . ')';
            }
        }else {//字符串    $insert = '"zhangsan", 12'

            $insert_fields_val_arr = explode(',', $insert);
            $insert_fields_val_arr = array_map(function ($elem){

                $elem = trim($elem);

                # 左侧的 单/双 引号去掉
                if( 
                    substr($elem, 0, 1)=='\'' ||
                    substr($elem, 0, 1)=='"'
                ){
                    $elem = substr_replace($elem, '', 0, 1);
                }

                # 右侧的 单/双 引号去掉
                if( 
                    substr($elem, -1)=='\'' ||
                    substr($elem, -1)=='"'
                 ){
                    $elem = substr_replace($elem, '', -1);
                }

                return $elem;
            }, $insert_fields_val_arr);

            $insert = '"' . implode('","', $insert_fields_val_arr) . '"';

            $this->insert[] = '(' . $insert . ')';
        }

        $this->flag = 'insert';//操作标识， insert代表接下来如果调用exec方法则将执行insert操作
        return $this;
    }

    /**
     * method:执行新增或修改操作
     * @return bool 执行成功:true; 执行失败:false
     */
    protected function goexec(){

        if( $this->query(2) ){

            $this->init();
            return true;
        }else{

            $this->init();
            return false;
        }
    }

    /**
     * method:获取多条数据
     * @param $type string 指定返回的数据数组类型，$type='relate'为关联数组；$type='index'为索引数组；默认为'relate'
     * @return 二维数组
     */
    protected function goget($type='relate'){
        
        $re = $this->query();
        
        $this->init();

        if( $re ){
            if( $type==='index' ){
                return $this->pdostatement->fetchAll(\PDO::FETCH_NUM);
            }
            return $this->pdostatement->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return [];
        }
    }

    /**
     * method:获取一条数据
     * @return 一维数组
     */
    protected function gofind(){
    
        $this->golimit('1');
        $re = $this->query();

        $this->init();

        if( $re )
            return $this->pdostatement->fetch(\PDO::FETCH_ASSOC);
        else
            return [];
    }

    protected function golast_insert_id(){
        
        return $this->_pdo->lastInsertId();
    }

    /**
     * method:调试
     * @param $flag string 调试标识
     * @param $e object 当调试错误时才需要传入的捕获的错误对象
     * @return 根据不同的$flag返回不同的结果
        $flag         结果
        'sql'         返回当前SQL语句
        'err.echo'    输出错误
        'err.log'     记录错误到日志文件
     */
    protected function godbug($flag='sql', $sql=''){

        $sql = !empty($sql) ? $sql : $this->sql;
    
        if( $flag==='sql' )://调试返回当前SQL语句

            return $sql;
        elseif ($flag==='err.echo')://输出错误
            echo '时间：' . date('Y-m-d H:i:s');echo '<br/>';
            echo '错误消息内容：'.$this->e->getMessage();echo '<br/>';
            echo '错误代码：'.$this->e->getCode();echo '<br/>';
            echo '错误程序文件名称：'.$this->e->getFile();echo '<br/>';
            echo '错误代码在文件中的行号：'.$this->e->getLine();echo '<br/>';
            echo 'SQL语句：' . $sql;echo '<br/>';
            // exit;
        elseif ($flag==='err.log')://记录错误到日志文件

            $log = '';
            $log .= '时间：' . date('Y-m-d H:i:s') . PHP_EOL;
            $log .= '错误消息内容：'.$this->e->getMessage() . PHP_EOL;
            $log .= '错误代码：'.$this->e->getCode() . PHP_EOL;
            $log .= '错误程序文件名称：'.$this->e->getFile() . PHP_EOL;
            $log .= '错误代码在文件中的行号：'.$this->e->getLine() . PHP_EOL;
            $log .= 'SQL语句：'.$sql;
            Log::set('type', 'database')->msg($log);
        endif;
    }

    /**
     * method:判断传入的数组是一维数组还是二维数组
     * @param $arr array 需要判断的数组
     * @return 0:不是数组；1:一维数组；2:二维数组
     */
    protected function is2arr($arr){

        if(!is_array($arr)) return 0;//不是数组

        if (count($arr)==count($arr, 1)) {
            return 1;//一维数组
        } else {
            return 2;//二维数组
        }
    }
}