<?php

/**
 * 页面重定向
 * @param   $route
 * @param   $type   1)$type=='echo'为输出并根据指定时间跳转型 2)$type=='page'为中间页面提示后跳转型
 */
function jump($route, $type='echo', $msg='操作成功！', $time=2){
    
    if( $type=='echo' ){
    
        echo $msg; 
    
        preg_match('/^http.*/', $route, $matches);
        if( isset($matches[0]) ){
        
            $url = $route;
        }else{
            $url = Config::C('URL') . $route;
        }
    
        header("Refresh:{$time}; url={$url}");
        exit;
    }
}