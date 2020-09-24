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
    public static $midwares;

    private static $prefixes=[];
    private static $count=[];

    private static $_get=[];
    private static $_post=[];
    private static $_request=[];

    private static $selfObj=NULL;
    private static $teamCount=[];

    public static function get($route, $map){

        return self::routeAd($route, $map, 'get');
    }

    public static function post($route, $map){
    
        return self::routeAd($route, $map, 'post');
    }

    public static function request($route, $map){
    
        return self::routeAd($route, $map, 'request');
    }

    public static function team($info, $callback){
    
        if( empty(self::$teamCount) ){
            $key            = 0;
            self::$prefixes = [];
        }else {
            $key = count(self::$teamCount);
        }
        self::$teamCount[$key] = 'using';

        self::$prefixes[$key] = $info['prefix'];
        $callback();

        unset(self::$teamCount[$key]);
        unset(self::$prefixes[$key]);
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
        if( $key===false ) return false;

        $gain .= 's'; # navtabs
        #                 _get      navtabs  
        if( !isset(self::$$var_name[$gain][$key])||empty(self::$$var_name[$gain][$key]) ) return '';

        return self::$$var_name[$gain][$key];
    }

    /**
     * 中间件
     * $midware='auth:check'
     */
    public function midware($midware){

        if( empty($midware) ) return self::$selfObj;
    
        $key    = self::$count[0];
        $name   = '_'.self::$count[1];# _get/_post/_request

        if( !isset(self::$$name['midwares']) ) self::$$name['midwares']=[];
        if( !isset(self::$$name['midwares'][$key]) ) self::$$name['midwares'][$key]=[];

        self::$$name['midwares'][$key][] = $midware;
        
        return self::$selfObj;
    }

    /**
     * 页面标识
     * $navtab如果为空，且name值不为空，则，navtab取name的值
     */
    public function navtab($navtab=''){
        
        ///                get               2
        // self::$_navtab[self::$count[1]][self::$count[0]] = $navtab;

        $key    = self::$count[0];
        $name   = '_'.self::$count[1];# _get/_post/_request

        if( !isset(self::$$name['navtabs']) ) self::$$name['navtabs']=[];
        self::$$name['navtabs'][$key] = empty($navtab) ? (isset(self::$$name['names'][$key])?self::$$name['names'][$key]:'') : $navtab;
        
        return self::$selfObj;
    }

    /**
     * 页面名称
     * $name如果为空，且navtab值不为空，则，name取navtab的值
     */
    public function name($name=''){
    
        $key    = self::$count[0];
        $type_name   = '_'.self::$count[1];# _get、_post、_request

        if( !isset(self::$$type_name['names']) ) self::$$name['names']=[];
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

        $prefix = '';
        if( !empty(self::$prefixes) ){
            $prefix = '/' . implode('/', self::$prefixes);
        }
        
        if( $type=='get' ){#add get

            $k = isset(self::$_get['maps']) ? count(self::$_get['maps']) : 0;
            self::$_get['maps'][$k]     = $map;
            self::$_get['routes'][$k]   = $prefix . $route;
            self::$_get['navtabs'][$k]  = '';
            self::$_get['names'][$k]     = '';

        }elseif( $type=='post' ) {#add post

            $k = isset(self::$_post['maps']) ? count(self::$_post['maps']) : 0;
            self::$_post['maps'][$k]    = $map;
            self::$_post['routes'][$k]  = $prefix . $route;
            self::$_post['navtabs'][$k] = '';
            self::$_post['names'][$k]    = '';

        }else{#add request

            $k = isset(self::$_request['maps']) ? count(self::$_request['maps']) : 0;
            self::$_request['maps'][$k]     = $map;
            self::$_request['routes'][$k]   = $prefix . $route;
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
        if(empty($URI)||$URI==='/') self::$uri=$URI=Config::C('WEB');

        /// 拆分
        if(strpos($URI, '?')){#如果带参数，则取"?"前的部分

            preg_match('/^(.*)\?/', $URI, $preg_arr);
            $URI = $preg_arr[1];
        }

        $URI_arr = explode('/', substr($URI, 1));
        self::$plat = isset($URI_arr[0]) ? strtolower($URI_arr[0]) : '';#路由的第一位为 平台 参数
        self::$way  = isset($URI_arr[1]) ? strtolower($URI_arr[1]) : '';#路由的第二位为 线路 参数
    }

    public static function prepare(){

        ///处理URI
        $URI        = self::$uri;
        $web_404    = Config::C('WEB404');

        /// 平台 限定
        $limit_plat = Config::C('LIMIT_PLAT');

        if( !in_array(self::$plat, $limit_plat) ){

            Log::msg('指定了非法的平台: '.self::$plat);
            header('Location:'.$web_404);
            exit;
        }

        ///确定routes文件
        $routes_path    = APP . '/' . self::$plat . '/routes.php';
        $has_routes     = file_exists($routes_path);

        if(!$has_routes){
            Log::msg('没有routes文件: '.$routes_path);
            header('Location:'.$web_404);
            exit;
        }
        include $routes_path;

        ///当前请求的方式
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if(!in_array($request_method, ['get', 'post'])){
            Log::msg('请求方式非法: '.$request_method);
            header('Location:'.$web_404);
            exit;
        }
        
        ///匹配routes规则
        $var_name       = '_' . $request_method;# _get  或  _post
        $routes_gather  = self::$$var_name['routes'];

        if(!in_array($URI, $routes_gather)){

            $request_method = 'request';
            $var_name       = '_' . $request_method;# _request
            $routes_gather  = isset(self::$$var_name['routes']) ? self::$$var_name['routes'] : [];
            
            if(!in_array($URI, $routes_gather)){
                Log::msg('匹配不到routes对应的规则: '.$URI);
                header('Location:'.$web_404);
                exit;
            }
        }

        $routes_key = array_search($URI, $routes_gather);#routes的key与map的key一致

        $map            = self::$$var_name['maps'][$routes_key];
        self::$name     = !isset(self::$$var_name['names'][$routes_key])    ? '' : self::$$var_name['names'][$routes_key];
        self::$navtab   = !isset(self::$$var_name['navtabs'][$routes_key])  ? '' : self::$$var_name['navtabs'][$routes_key];

        # 得到控制器名和方法
        $map_str            = explode('@', $map);
        self::$controller   = ucfirst($map_str[0]);
        self::$method       = $map_str[1];

        # 获取当前页面需要加载的所有中间件
        self::$midwares = $this_midwares = !isset(self::$$var_name['midwares'][$routes_key])  ? [] : self::$$var_name['midwares'][$routes_key];
        if( !empty($this_midwares) ){
            
            self::$midwares     = [];
            $midwares_mapping   = Config::C('MIDWARE');
            
            foreach( $this_midwares as $k=>$mid){
            
                if( isset($midwares_mapping[$mid]) ){
                
                    $this_midware_mapping   = $midwares_mapping[$mid];
                    $tmp                    = explode(':', $this_midware_mapping);

                    if( count($tmp)==2 ){
                    
                        self::$midwares[$k] = $tmp;
                    }else{
                        Log::msg('中间件参数数据格式有误：'.$mid);
                        header('Location:'.$web_404);
                        exit;
                    }
                    
                }else{
                    Log::msg('配置文件中缺少中间件映射：'.$mid);
                    header('Location:'.$web_404);
                    exit;
                }
            }
        }
    }
}

