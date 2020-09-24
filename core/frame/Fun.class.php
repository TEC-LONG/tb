<?php

class Fun{

    private static $Fun=null;

    public static function __callStatic($name, $arguments){
        
        if( empty(self::$Fun) ){
            self::$Fun = new self;
        }
        
        return self::magicCommon($name, $arguments);
    }

    public function __call($name, $arguments){
    
        if( empty(self::$Fun) ){
            self::$Fun = new self;
        }

        return self::magicCommon($name, $arguments);
    }

    /**
     * __callStatic与__call 调取的公共方法
     */
    private static function magicCommon($name, $arguments){

        /// 名称拆分
        $name_arr = explode('__', $name);

        if( count($name_arr)==1 ){# 在 fun/ 目录下

            $fun_name   = strtolower($name_arr[0]);
            $file_path  = CORE_FUN . '/' . $fun_name . '.fun.php';

        }elseif( count($name_arr)==2 ){# 在 fun/主题/ 目录下
            
            $name_arr[0]    = strtolower($name_arr[0]);
            $fun_name       = strtolower($name_arr[1]);
            $file_path      = CORE_FUN . '/' . $name_arr[0] . '/' . $fun_name . '.fun.php';
        }

        /// 校验函数是否存在
        if( !function_exists($fun_name) ){
            
            if( file_exists($file_path) ){
                
                include $file_path;
            }else{
                return false;
            }
        }

        /// 校验函数的参数个数，不能超过6个参数
        if( 
            !empty($arguments) &&
            count($arguments)>6
        ){
            return false;
        }

        /// 调用函数
        return self::$Fun->functionRun($fun_name, $arguments);
    }

    /**
     * 执行指定函数
     */
    private function functionRun($fun_name, $arguments){
    
        if( empty($arguments) ){
        
            return $fun_name();
        }elseif( count($arguments)==1 ){
        
            return $fun_name($arguments[0]);
        }elseif( count($arguments)==2 ){
        
            return $fun_name($arguments[0], $arguments[1]);
        }elseif( count($arguments)==3 ){
        
            return $fun_name($arguments[0], $arguments[1], $arguments[2]);
        }elseif( count($arguments)==4 ){
        
            return $fun_name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
        }elseif( count($arguments)==5 ){
        
            return $fun_name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
        }elseif( count($arguments)==6 ){
        
            return $fun_name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
        }else{
            return false;
        }
    }
}