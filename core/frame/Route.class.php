<?php

///命名空间为全局
class Route{

    public static $uri;
    public static $plat;
    public static $way;
    public static $controller;
    public static $method;
    public static $name;
    public static $navtab;

    private static $count=[];

    private static $_get=[];
    private static $_post=[];
    private static $_request=[];

    private static $selfObj=NULL;

    public static function get($route, $map){
    
        return self::routeAd($route, $map, 'get');
    }

    public static function post($route, $map){
    
        return self::routeAd($route, $map, 'post');
    }

    public static function request($route, $map){
    
        return self::routeAd($route, $map, 'request');
    }

    /**
     * @param   $type   int         [路由类型，取值范围："get"、"post"、"request"]
     * @param   $route  string      [路由全名，如："/admin/article"]
     * @param   $gain   string      [获取的目标，取值范围为："navtab"、"name"]
     *                              get   /admin/..   navtab
     */
    public static function getElem($type, $route, $gain){
    
        $var_name = '_' . $type;
        if( !isset(self::$$var_name) ) return false;

        $key = array_search($route, self::$$var_name['routes']);
        if( !$key ) return false;

        $gain .= 's'; # navtabs
        #                 _get      navtabs  
        if( !isset(self::$$var_name[$gain][$key])||empty(self::$$var_name[$gain][$key]) ) return '';

        return self::$$var_name[$gain][$key];
    }

    public function navtab($navtab=''){
        
        ///                get               2
        // self::$_navtab[self::$count[1]][self::$count[0]] = $navtab;

        $key    = self::$count[0];
        $name   = '_'.self::$count[1];# _get/_post/_request

        self::$$name['navtabs'][$key] = $navtab;
        self::$$name['navtabs'][$key] = empty($navtab) ? (isset(self::$$name['names'][$key])?self::$$name['names'][$key]:'') : $navtab;
        
        return self::$selfObj;
    }

    /**
     * $name为空则 name取navtab的值，若navtab也为空，则为空
     */
    public function name($name=''){
    
        $key    = self::$count[0];
        $type_name   = '_'.self::$count[1];# _get、_post、_request

        self::$$type_name['names'][$key] = empty($name) ? (isset(self::$$type_name['navtabs'][$key])?self::$$type_name['navtabs'][$key]:'') : $name;
        
        return self::$selfObj;
    }

    private static function getObj(){
        
        if(self::$selfObj===NULL) self::$selfObj=new self;
        return self::$selfObj;
    }

    private static function setCount($key, $request){

        ///              2       'get'
        self::$count = [$key, $request];
    }

    private static function routeAd($route, $map, $type){
        
        if( $type=='get' ){#add get

            $k = isset(self::$_get['maps']) ? count(self::$_get['maps']) : 0;
            self::$_get['maps'][$k]     = $map;
            self::$_get['routes'][$k]   = '/' . self::$plat . '/' . $route;
            self::$_get['navtabs'][$k]  = '';
            self::$_get['names'][$k]     = '';

        }elseif( $type=='post' ) {#add post

            $k = isset(self::$_post['maps']) ? count(self::$_post['maps']) : 0;
            self::$_post['maps'][$k]    = $map;
            self::$_post['routes'][$k]  = '/' . self::$plat . '/' . $route;
            self::$_post['navtabs'][$k] = '';
            self::$_post['names'][$k]    = '';

        }else{#add request

            $k = isset(self::$_request['maps']) ? count(self::$_request['maps']) : 0;
            self::$_request['maps'][$k]     = $map;
            self::$_request['routes'][$k]   = '/' . self::$plat . '/' . $route;
            self::$_request['navtabs'][$k]  = '';
            self::$_request['names'][$k]     = '';
        }

        self::setCount($k, $type);
        return self::getObj();
    }

    /**
     * 根据当前请求拆分出平台和线路参数
     */
    public static function getPlatAndWay(){
        
        /// 当前URI
        self::$uri = $URI = $_SERVER['REQUEST_URI'];
        if(empty($URI)||$URI==='/') $URI=Config::C('web');

        /// 拆分
        if(strpos($URI, '?')){#如果带参数，则取"?"前的部分

            preg_match('/^(.*)\?/', $URI, $preg_arr);
            $URI = $preg_arr[1];
        }

        $URI_arr = explode('/', substr($URI, 1));
        self::$plat = isset($URI_arr[0]) ? strtolower($URI_arr[0]) : '';#路由的第一位为 平台 参数
        self::$way  = isset($URI_arr[1]) ? strtolower($URI_arr[1]) : '';#路由的第二位为 线路 参数
    }
/*
    public static function prepare(){

        ///处理URI
        $URI = $_SERVER['REQUEST_URI'];
        if(empty($URI)||$URI==='/') $URI='/'.C('dweb.p').'/'.C('dweb.m').'/'.C('dweb.a');

        if(strpos($URI, '?')){#如果带参数，则取"?"前的部分

            preg_match('/^(.*)\?/', $URI, $preg_arr);
            $URI = $preg_arr[1];
        }
        $URI_arr = explode('/', substr($URI, 1));
        self::$plat = $URI_arr[0];#路由的第一位为 平台 参数

        if( !in_array(self::$plat, ['tools', 'admin', 'blog', 'home', 'store']) ){
            return false;
        }

        ///确定routes文件
        $routes_path = APP_PATH . strtolower($URI_arr[0]) . DIRECTORY_SEPARATOR . 'routes.php';
        $has_routes = file_exists($routes_path);

        if(!$has_routes) exit('跳转404，记录日志！没有routes文件');
        include $routes_path;

        ///当前请求的方式
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if(!in_array($request_method, ['get', 'post'])) exit('跳转404，记录日志！请求方式非法');
        
        ///匹配routes规则，确定指向哪个控制器下的哪个方法
        $var_name = '_' . $request_method;# _get  或  _post
        $routes_gather = self::$$var_name['routes'];
        
        if(!in_array($URI, $routes_gather)){
            $request_method = 'request';
            $var_name = '_' . $request_method;# _request
            $routes_gather = self::$$var_name['routes'];
            if(!in_array($URI, $routes_gather)) exit('跳转404，记录日志！匹配不到routes对应的规则');
        }

        $routes_key = array_search($URI, $routes_gather);#routes的key与map的key一致

        $map            = self::$$var_name['maps'][$routes_key];
        self::$name     = !isset(self::$$var_name['names'][$routes_key])    ? '' : self::$$var_name['names'][$routes_key];
        self::$navtab   = !isset(self::$$var_name['navtabs'][$routes_key])  ? '' : self::$$var_name['navtabs'][$routes_key];

        #得到控制器名和方法
        $map_str = explode('@', $map);
        self::$controller = ucfirst($map_str[0]);
        self::$method = $map_str[1];
    }*/
}

