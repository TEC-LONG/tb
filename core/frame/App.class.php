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

        $file = self::autoloadCommon($single_class_name);

        if( empty($file) ){

            if( substr($single_class_name, -10)==='Controller' ){

                $file = APP . '/' . Route::$plat . '/' . Route::$way . '/controller/' . $single_class_name . '.class.php';
            }elseif( substr($className, -7)==='Service' ){
            
                // $file = APP . '/' . Route::$plat . '/' . Route::$way . '/service/' . $single_class_name . '.class.php';
            }elseif( substr($className, -6)==='Middle' ){

                $file = CORE_MIDWARE . '/' . $single_class_name . '.class.php';
            }
        }

        if( file_exists($file) ){
            
            include $file;
        }else{

            // 如果调试模式，则输出错误信息

            Log::msg('文件不存在：'.$file.'; className: '.$className);
            exit;
        }
    }

    private static function autoloadCommon($single_class_name){

        $file = '';
    
        if( in_array($single_class_name, ['TB', 'Json', 'Fun', 'baseCmd']) ){
            
            $file = CORE_FRAME . '/' . $single_class_name . '.class.php';
        }elseif ( substr($single_class_name, -5)==='Model' ) {
            
            $file = APP_MODEL . '/' . $single_class_name . '.class.php';
        }elseif( substr($single_class_name, -4)=='Plug' ){
        
            $file = PLUGINS . '/' . $single_class_name . '.class.php';
        }

        return $file;
    }

    public static function cmdAutoload($className){ 

        //$className = basename($className);//得到了除去命名空间的纯类名，Linux下不认“\”做目录分隔符，basename无效
        $class_name_explode = explode('\\', $className);
        $single_class_name  = $class_name_explode[count($class_name_explode)-1];

        $file = self::autoloadCommon($single_class_name);

        if( empty($file) ){

            if( substr($single_class_name, -7)==='Service' ){
        
                $file = APP_CMD . '/service/' . $single_class_name . '.class.php';
            }elseif( substr($single_class_name, -3)=='Cmd' ){
        
                $file = APP_CMD . '/' . $single_class_name . '.class.php';
            }
        }

        if( file_exists($file) ){
            
            include $file;
        }else{

            // 如果调试模式，则输出错误信息

            Log::msg('文件不存在：'.$file.'; className: '.$className);
            exit;
        }
    }

    public static function run(){ 

        $plat       = Route::$plat;
        $way        = Route::$way;
        $contr      = Route::$controller;
        $method     = Route::$method;
        $midwares   = Route::$midwares;

        /// 中间件
        $obj_arr = [];
        if( !empty($midwares) ){
        
            foreach( $midwares as $this_midware){
            
                $midware_class_name = '\\midware\\' . $this_midware[0] . 'Middle';
                if( !isset($obj_arr[$midware_class_name]) ){
                
                    $obj_arr[$midware_class_name] = new $midware_class_name;
                }

                $mid_obj = $obj_arr[$midware_class_name];
                $mid_act = $this_midware[1];

                $mid_obj->$mid_act();
            }
        }

        /// 控制器
        $controller_class_name  = '\\'.$plat.'\\'.$way.'\\controller\\'.$contr;
        $obj                    = new $controller_class_name;

        $obj->$method();
    }

    /**
     * 引入文件
     */
    public static function includes($file_flag){
    
        /// 引入文件
        foreach( $file_flag as $v){
        
            if( $v=='Config' ){

                include CORE_FRAME . '/Config.class.php';
            
            }elseif( $v=='Log' ){

                include CORE_FRAME . '/Log.class.php';
            
            }elseif( $v=='Err' ){

                include CORE_FRAME . '/Err.class.php';
            
            }elseif( $v=='Route' ){

                include CORE_FRAME . '/Route.class.php';
            
            }elseif( $v=='Smarty' ){

                include CORE_FRAME_SMARTY . '/Smarty.class.php';
            
            }elseif( $v=='Controller' ){

                # 父类控制器
                include CORE_FRAME . '/Controller.class.php';
            }
        }
    }

    /**
     * debug模式设置
     */
    public static function debug(){

        if ( Config::C('DEBUG')===1 ) {
    
            ini_set('error_reporting', E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
            ini_set('display_errors', 1);
        }
    }

    /**
     * 定时任务
     */
    public static function runCmd(){
    
        /// 初始化参数
        global $argv;
        global $argc;

        /// 参数分解
        $cmd_name = isset($argv[1]) ? $argv[1] : '';
        if( empty($cmd_name) ) echo '未指定cmd执行文件！' . PHP_EOL;

        /// 启动对应的cmd功能
        # 文件名   \cmd\OrderCmd
        $cmd_name = '\\cmd\\'.$cmd_name . 'Cmd';

        /// 参数组装
        $params = $argv;
        array_shift($params);
        array_shift($params);
        $params = array_values($params);

        # 启动
        $cmd_obj = new $cmd_name($params);
        $cmd_obj->go();
    }

}

