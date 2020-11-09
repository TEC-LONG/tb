<?php

class Request{

    private $_request;

    public function __construct(){
        
        $this->_request = $_REQUEST;
    }

    /**
     * @method    all
     * 方法作用：根据指定的模式，过滤接收的数据，并返回过滤后的数据
     * 
     * @param    $mod    string    模式标识
                    $mod='s' 表示strict严格模式，该模式将使用 "trim,htmlspecialchars,单引转换实体" 来过滤接收的数据
                    $mod='n' 表示normal普通模式，该模式将使用 "trim,htmlspecialchars" 来过滤接收的数据
                    $mod='l' 表示light轻量模式，该模式将使用 "trim" 来过滤接收的数据
     *
     * @return    array
     */
    public function all($mod='s'){
        
        /// 公共处理
        $this->_request = $this->str_trim($this->_request);# trim处理
    
        if( $mod==='s' ){//strict 模式

            $this->_request = $this->str_htmlspecialchars($this->_request);
            $this->_request = $this->str_danyin($this->_request);
            // $this->_request = $this->str_delete_tags($this->_request);
            
        }elseif ($mod=='n') {//normal 模式

            $this->_request = $this->str_htmlspecialchars($this->_request);

        }elseif ($mod=='l') {//light 轻量级模式
            
        }

        return $this->_request;
    }

    /**
     * @method  vars
     * 方法作用：该方法专门用于开放单独的过滤方式
     * 
     * @param    $request   string|array    需要过滤的字符串或数组数据
     * @param    $type      string          过滤方式
                    $type='trim'                    表示使用 "trim" 过滤
                    $type='htmlspecialchars'        表示使用 "htmlspecialchars" 过滤
                    $type='htmlspecialchars_decode' 表示使用 "htmlspecialchars_decode" 逆处理htmlspecialchars过滤后的数据
                    $type='danyin'                  表示使用 "单引转换实体" 将目标字符串中的单引号进行实体转换过滤
                    $type='danyin_decode'           表示使用 "逆向单引转换实体" 逆处理danyin过滤后的字符串
     * @return    string
     */
    public function vars($request, $type='trim'){
    
        if( $type==='trim' ){
            return $this->str_trim($request);
        }elseif ($type==='htmlspecialchars') {
            return $this->str_htmlspecialchars($request);
        }elseif ($type==='htmlspecialchars_decode') {
            return $this->str_htmlspecialchars_decode($request);
        }elseif ($type==='danyin') {
            return $this->str_danyin($request);
        }elseif ($type==='danyin_decode') {
            return $this->str_danyin_decode($request);
        }

    }

    protected function str_delete_tags(){//去除标签(php,html,js...)
    
    }

    protected function str_trim($request){//trim处理，最多只能处理到二维
    
        if( is_array($request) ):
            foreach( $request as $k=>$v){
        
                if( is_string($v) ){
                    
                    $request[$k] = trim($v);
                }elseif( is_array($v) ){
    
                    foreach( $v as $k1=>$v1){
                        
                        if( is_string($v1) ){
                            $request[$k][$k1] = trim($v1);
                        }else{//非文件类表单数据，在$request中最多只能有二维
                            //根据是否为调试模式 抛出错误/记录日志
                        }
                    }
                }else {
                    //根据是否为调试模式 抛出错误/记录日志
                }
            }
        else:
            $request = trim($request);
        endif;

        return $request;
        
    }

    protected function str_htmlspecialchars($request){//htmlspecialchars处理，最多只能处理到二维
    
        if( is_array($request) ):
            foreach( $request as $k=>$v){
        
                if( is_string($v) ){
                    $request[$k] = htmlspecialchars($v);
                }elseif( is_array($v) ){
    
                    foreach( $v as $k1=>$v1){
                        
                        if( is_string($v1) ){
                            $request[$k][$k1] = htmlspecialchars($v1);
                        }else{//非文件类表单数据，在$request中最多只能有二维
                            //根据是否为调试模式 抛出错误/记录日志
                        }
                        
                    }
                }else {
                    //根据是否为调试模式 抛出错误/记录日志
                }
            }
        else:
            $request = htmlspecialchars($request);
        endif;

        return $request;
    }

    protected function str_danyin($request){//对单引号的处理
    
        if( is_array($request) ):
            foreach( $request as $k=>$v){
        
                if( is_string($v) ){
                    
                    $request[$k] = str_replace('\'', '--danyin;', $v);
                }elseif( is_array($v) ){
    
                    foreach( $v as $k1=>$v1){
                        
                        if( is_string($v1) ){
                            $request[$k][$k1] = str_replace('\'', '--danyin;', $v1);
                        }else{//非文件类表单数据，在$request中最多只能有二维
                            //根据是否为调试模式 抛出错误/记录日志
                        }
                    }
                }else {
                    //根据是否为调试模式 抛出错误/记录日志
                }
            }
        else:
            $request = str_replace('\'', '--danyin;', $request);
        endif;

        return $request;
    }

    protected function str_htmlspecialchars_decode($request){//htmlspecialchars_decode处理，最多只能处理到二维
    
        if( is_array($request) ):
            foreach( $request as $k=>$v){
        
                if( is_string($v) ){
                    
                    $request[$k] = htmlspecialchars_decode($v);
                }elseif( is_array($v) ){
    
                    foreach( $v as $k1=>$v1){
                        
                        if( is_string($v1) ){
                            $request[$k][$k1] = htmlspecialchars_decode($v1);
                        }else{//非文件类表单数据，在$request中最多只能有二维
                            //根据是否为调试模式 抛出错误/记录日志
                        }
                        
                    }
                }else {
                    //根据是否为调试模式 抛出错误/记录日志
                }
            }
        else:
            $request = htmlspecialchars_decode($request);
        endif;

        return $request;
    }

    protected function str_danyin_decode($request){//对自定义单引号实体字符的反处理，与str_danyin中的引号处理是互为反向的
    
        if( is_array($request) ):
            foreach( $request as $k=>$v){
        
                if( is_string($v) ){
                    
                    $request[$k] = str_replace('--danyin;', '\'', $v);
                }elseif( is_array($v) ){
    
                    foreach( $v as $k1=>$v1){
                        
                        if( is_string($v1) ){
                            $request[$k][$k1] = str_replace('--danyin;', '\'', $v1);
                        }else{//非文件类表单数据，在$request中最多只能有二维
                            //根据是否为调试模式 抛出错误/记录日志
                        }
                        
                    }
                }else {
                    //根据是否为调试模式 抛出错误/记录日志
                }
            }
        else:
            $request = str_replace('--danyin;', '\'', $request);
        endif;

        return $request;
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