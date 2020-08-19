<?php

class App{

    private static $_objs=array();

    public static function single($className, $params, $type='single'){//$type='single'表示走单例；$type='no_single'表示不走单例

        if( empty(self::$_objs[$className]) ){
            if( empty($params) ):
                self::$_objs[$className] = new $className;
            else:
                if( $className==='\Upload\File' ){
                    self::$_objs[$className] = new $className($params[0], $params[1]);
                }else{
                    self::$_objs[$className] = new $className($params);
                }
            endif;
        }

        if( $type=='no_single' ){
        
            $tmp_obj = self::$_objs[$className];
            unset(self::$_objs[$className]);
            return $tmp_obj;

        }elseif ( $type=='single' ) {
            
            return self::$_objs[$className];
        }
    }

    public static function autoload($className){ 

        //$className = basename($className);//得到了除去命名空间的纯类名，Linux下不认“\”做目录分隔符，basename无效
        $class_name_explode = explode('\\', $className);
        $single_class_name  = $class_name_explode[count($class_name_explode)-1];

        if( in_array($single_class_name, ['Model', 'NiceModel', 'Json']) ){
            
            $file = CORE_FRAME . '/' . $single_class_name . '.class.php';
        }elseif( substr($single_class_name, -10)==='Controller' ){

            $file = APP . '/' . Route::$plat . '/' . Route::$way . '/controller/' . $single_class_name . '.class.php';
        }elseif ( substr($single_class_name, -5)==='Model' ) {
            
            $file = APP_MODEL . '/' . $single_class_name . '.class.php';
        }elseif( substr($className, -7)==='Service' ){
        
            // $file = APP . '/' . Route::$plat . '/' . Route::$way . '/service/' . $single_class_name . '.class.php';
        }elseif( substr($className, -4)=='Plug' ){
        
            $file = PLUGINS . '/' . $single_class_name . '.class.php';
        }

        if( file_exists($file) ){
            
            include $file;
        }else{

            Log::msg('文件不存在：'.$file);
            exit;
        }
    }

    public static function run(){ 

        $plat   = Route::$plat;
        $way    = Route::$way;
        $contr  = Route::$controller;
        $method = Route::$method;

        $class_name = '\\'.$plat.'\\'.$way.'\\controller\\'.$contr;
        $obj        = new $class_name;

        // $obj->$method();

        Err::try(function (){

            $v1 = 100;
            if( $v1>0 ){
                Err::throw('出现错误');
            }

        }, 'exit');
    }
}

