<?php

class Err{

    private static $Err=null;

    public static function __callStatic($name, $arguments){
        
        if( empty(self::$Err) ){
            self::$Err = new self;
        }
        
        return self::magicCommon($name, $arguments);
    }

    public function __call($name, $arguments){
    
        if( empty(self::$Err) ){
            self::$Err = new self;
        }

        return self::magicCommon($name, $arguments);
    }

    /**
     * __callStatic与__call 调取的公共方法
     */
    private static function magicCommon($name, $arguments){
    
        if( $name=='throw' ){
        
            return self::$Err->gothrowErr($arguments[0]);
        }elseif( $name=='try' ){
            
            if( isset($arguments[1]) ){
                return self::$Err->gotry($arguments[0], $arguments[1]);
            }
            return self::$Err->gotry($arguments[0]);
        }

        $err = new \Exception('非法的操作: Err::'.$name);
        self::$Err->handle($err);
        exit;
    }

    /**
     * 监听且捕获异常操作
     */
    private function gotry($callback, $ifexit=null){
    
        try{

            $callback();

        }catch(\Exception $err){

            $this->handle($err);

            if( $ifexit==='exit' ){
                exit;
            }

            return false;
        }

        return true;
    }

    /**
     * 异常处理
     */
    private function handle($err){
        
        $msg = '';
        $msg .= '错误码：' . $err->getCode() . PHP_EOL;
        $msg .= '目标文件：' . $err->getFile() . PHP_EOL;
        $msg .= '错误信息：' . $err->getMessage() . PHP_EOL;
        $msg .= '追踪信息：' . PHP_EOL . $err->getTraceAsString() . PHP_EOL;

        if( Config::C('DEBUG')==1 ){
        
            echo str_replace(PHP_EOL, '<br/>', $msg);
        }

        Log::msg($msg);
    }

    /**
     * 抛出异常操作
     */
    private function gothrowErr($data){

        if( is_array($data) ){
            $data = json_encode($data);
        }
        throw new \Exception($data);
    }
}