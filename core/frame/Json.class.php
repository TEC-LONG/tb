<?php

class Json{
    private $_re=null;
    private static $Json=null;

    public function __construct(){
        
        $this->goobj()->gostat()->gomsg();

        return $this;
    }

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
    
        Err::try(function () use ($name, $arguments){
        
            if( in_array($name, ['obj', 'stat', 'msg', 'navtab', 'vars', 'exec', 'decode']) ){
            
                $method = 'go' . $name;
                return self::commonUse($method, $arguments);
            }

            Err::throw('非法的操作: '.__CLASS__.'::'.$name);

        }, 'exit');

        return self::$Json;
    }

    private static function commonUse($method, $arguments=null){
    
        if( is_array($arguments)&&isset($arguments[0]) ){

            if( isset($arguments[1]) ){

                return self::$Json->$method($arguments[0], $arguments[1]);
            }
            
            return self::$Json->$method($arguments[0]);
            
        }
        return self::$Json->$method();
    }

    /**
     * @method  obj
     * 方法作用：根据字符串生成json对象，默认生成空的json对象
     * 
     * @param    $str    string    json字符串
     * 
     * @return    object
     */
    private function goobj($str='{}'){
        
        $this->_re = json_decode($str);

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
    private function gostat($stat=200){
    
        if( is_object($this->_re) ){
            $this->_re->code = $stat;
        }elseif( is_array($this->_re) ){
            $this->_re['code'] = $stat;
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
    private function gomsg($msg='操作成功'){
    
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
    private function gonavtab($navtab){
    
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
     * @param    $key_val_arr    array    值对信息，如：$key_val_arr=['msg'=>'xxxxx', 'stat'=>300]
     * 
     * @return    object
     */
    private function govars($key_val_arr){

        if( empty($this->_re) ){

            $this->_re = $key_val_arr;
        }else{

            foreach( $key_val_arr as $k=>$v){
            
                if( is_object($this->_re) ){
                    $this->_re->$k = $v;
                }else{
                    $this->_re[$k] = $v;
                }
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
    private function goexec($type='echo'){
    
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
    private function godecode($str, $prefix='j_'){
    
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

