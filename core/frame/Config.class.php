<?php

class Config{

    public static $data=[];

    /**
     * 引入路径常量配置文件
     */
    public static function builtConstants($plat, $way){
    
        /// 初始化参数
        # 当前平台下的公共配置文件路径
        $plat_env_config_dir_path = CONFIG . '/' . $plat . '/share';
        # 当前线路下的配置文件路径
        $way_env_config_dir_path = CONFIG . '/' . $plat . '/' . $way;

        /// 获取各级配置
        # 当前平台
        $plat_env_config_dir_files  = self::getSonFiles($plat_env_config_dir_path, -9, '.path.php');
        
        self::includes($plat_env_config_dir_files);

        # 当前线路
        $way_env_config_dir_files   = self::getSonFiles($way_env_config_dir_path, -9, '.path.php');
        self::includes($way_env_config_dir_files);
    }

    /**
     * 引入文件
     */
    protected static function includes($dir_files){
    
        if( empty($dir_files) ) return false;

        foreach( $dir_files as $file_path){
        
            include($file_path);
        }

        return true;
    }

    /**
     * 引入全局配置文件
     */
    public static function builtGlobalConfigs(){
    
        /// 初始化参数
        # 全局环境配置文件路径
        $global_share_env_config_dir_path = CONFIG_SHARE;

        # 全局
        $global_share_env_config_dir_files  = self::getSonFiles($global_share_env_config_dir_path, -11, '.config.php');
        $global_share_env_config            = self::rebuiltConfigs($global_share_env_config_dir_files);

        self::$data = array_merge(self::$data, $global_share_env_config);
    }

    /**
     * 引入数据配置文件
     */
    public static function builtConfigs($plat, $way){

        /// 初始化参数
        # 当前平台下的公共配置文件路径
        $plat_env_config_dir_path = CONFIG . '/' . $plat . '/share';
        # 当前线路下的配置文件路径
        $way_env_config_dir_path = CONFIG . '/' . $plat . '/' . $way;

        /// 获取 平台 和 路线 级别配置
        # 当前平台
        $plat_env_config_dir_files  = self::getSonFiles($plat_env_config_dir_path, -11, '.config.php');
        $plat_env_config            = self::rebuiltConfigs($plat_env_config_dir_files);

        # 当前线路
        $way_env_config_dir_files   = self::getSonFiles($way_env_config_dir_path, -11, '.config.php');
        $way_env_config             = self::rebuiltConfigs($way_env_config_dir_files);

        /// 合并各级配置
        self::$data = array_merge(self::$data, $plat_env_config, $way_env_config);
    }

    /**
     * 组装目录内配置文件的配置数据
     */
    protected static function rebuiltConfigs($dir_files){
    
        $configs = [];
        if( empty($dir_files) ) return $configs;

        foreach( $dir_files as $file_path){
        
            $this_config    = include($file_path);
            $configs        = array_merge($configs, $this_config);
        }
        
        return $configs;
    }

    /**
     * 取得指定目录下所有的配置文件全路径名
     */
    protected static function getSonFiles($dir_path, $str_num, $target){

        if( !is_dir($dir_path) ) return [];
        
        $son_files  = [];
        $plat_od    = opendir($dir_path);
        while ( ($file=readdir($plat_od))!==false ) {
            
            if( $file=='.'||$file=='..' ) continue;

            if( substr($file, $str_num)==$target ){
                
                $this_file_path = $dir_path . '/' . $file;
                if( file_exists($this_file_path) ){
                    $son_files[] = $this_file_path;
                }
            }
        }

        return $son_files;
    }

    /**
     * 获取 或 设置配置参数
     */
    public static function C($key, $val=null){

        /// 判定当前执行何种操作
        if( is_array($key)||$val!==null||$val=='ORIGINAL' ){# 设置环境配置操作
        
            $type = 'set';
        }else{# 获取环境配置操作
            $type = 'get';
        }
    
        /// 执行操作
        switch($type){
            case 'set':
    
                if( $val=='ORIGINAL' ){

                    $configs = &self::$data;
                    if (empty($configs) ) $configs=[];
                    self::configRecursiveSet($configs, $key);

                }elseif( is_array($key) ){
                
                    self::configSetArray($key);
    
                }elseif( is_string($key) ){
    
                    self::configSetString($key, $val);
                }
    
            break;
            case 'get':
                
                $arr = explode('.', $key);
    
                $configs = self::$data;
                foreach( $arr as $config_name ){ 
                    $configs = $configs[$config_name];
                }
    
            return $configs;
        }
    }

    protected static function configRecursiveSet(&$ori, $configs){
    
        foreach( $configs as $k=>$v){

            if( is_array($v) ){

                if( !isset($ori[$k]) ){
                    $ori[$k] = [];
                }

                self::configRecursiveSet($ori[$k], $v);
            }else{

                $ori[$k] = $v;
            }
        }
    }
    
    protected static function configSetArray($key){
    
        foreach( $key as $config_names=>$config_val){
                        
            $arr        = explode('.', $config_names);# order.goods.price
            $arr_length = count($arr);
            $configs    = &self::$data;
            if (empty($configs) ) $configs=[];
            
            foreach( $arr as $arr_key=>$name){
    
                if( ($arr_length-1)==$arr_key ){
                    
                    $configs[$name] = $config_val;
                }else{
    
                    if( !isset($configs[$name]) ){
                
                        $configs[$name] = [];
                    }
                    $configs = &$configs[$name];
                }
            }
        }
    }
    
    protected static function configSetString($key, $val){
        
        $arr        = explode('.', $key);
        $arr_length = count($arr);
        $configs    = &self::$data;
        if( empty($configs) ) $configs=[];
    
        foreach( $arr as $arr_key=>$name){
    
            if( ($arr_length-1)==$arr_key ){
                
                $configs[$name] = $val;
            }else{
    
                if( !isset($configs[$name]) ){
            
                    $configs[$name] = [];
                }
                $configs = &$configs[$name];
            }
        }
    }
}

