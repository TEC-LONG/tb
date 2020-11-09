<?php

function L($route, $url=''){
    
    if( empty($url) ){
        
        $url = Config::C('URL');
    }

    return $url.$route;
}