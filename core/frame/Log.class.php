<?php

class Log{

    private static $Log;

    private $_type;
    private $_limit_type=['mixed', 'upload', 'download', 'database', 'tbEdit'];

    private $_msg=[];
    private $_save_path;

    public function __construct(){

        $this->_type        = 'mixed';
        $this->_save_path   = STORAGE_LOG;

        // if( !is_dir($this->_save_path) ) $this->_save_path = STORAGE_LOG;
    }

    public static function __callStatic($name, $arguments){
        
        if( empty(self::$Log) ){
            self::$Log = new static;
        }
        
        return self::magicCommon($name, $arguments);
    }

    public function __call($name, $arguments){
    
        if( empty(self::$Log) ){
            self::$Log = $this;
        }

        return self::magicCommon($name, $arguments);
    }

    /**
     * __callStatic与__call 调取的公共方法
     */
    private static function magicCommon($name, $arguments){

        if( $name=='set' ){
        
            return self::$Log->goset($arguments[0], $arguments[1]);
        }elseif( $name=='msg' ){
            
            return self::$Log->gomsg($arguments[0]);
        }elseif( in_array($name, ['clear']) ){
        
            return self::$Log->$name();
        }

        self::$Log->gomsg('非法的操作: Log::'.$name);
        if( Config::C('DEBUG')===1 ){
            echo '非法的操作: Log::'.$name;
        }
        exit;
    }

    /**
     * 方法名：set
     * 方法作用：设置日志类型
     * @param    string    $name    类型名称，目前仅支持'type'一个值
     * @param    string    $val    类型名称，只有private $_limit_type内的元素之才有效
     * @return    object
     */
    private function goset($name, $val){
    
        if( $name=='type' ){
            $this->_type = $val;
        }
        return $this;
    }

    /**
     * 方法名：msg
     * 方法作用：设置日志记录的内容
     * @param    string|array    $msg    日志内容
     * @return    object
     */
    private function gomsg($msg){

        if( is_array($msg) ){
        
            foreach( $msg as $v){
            
                $this->_msg[] = $v;
            }
        }else{
            
            $this->_msg[] = $msg;
        }
    
        // return $this;
        return $this->go();
    }

    /**
     * 方法名：clear
     * 方法作用：清空已有的缓存日志内容
     * @return    object
     */
    private function clear(){

        $this->_msg = [];
        return $this;
    }

    /**
     * 方法名：go
     * 方法作用：执行记录操作
     */
    private function go(){

        $time = date('Y-m-d H:i:s');
    
        if( $this->_type=='tbEdit' ){
        
            $templ = "/*{$time}*/%s{EOL}{EOL}";
        }else{

            $templ = "[{$this->_type}@{$time}] %s{EOL}{EOL}";
        }

        $templ = str_replace('{EOL}', PHP_EOL, $templ);
        $tmp_msg = implode(PHP_EOL, $this->_msg);

        $templ = sprintf($templ, $tmp_msg);

        //                               all_20200102
        $this->_file_name = $this->_type . '_' . date('Ymd') . '.log';
        $re = file_put_contents($this->_save_path.'/'.$this->_file_name, $templ, FILE_APPEND);
        $this->clear();

        return $re;
    }
}