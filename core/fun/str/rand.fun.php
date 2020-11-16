<?php

/**
 * 根据指定字符个数，生成随机字符
 */
function rand($num=6){ 
        
    $str = '';
    for($i=0; $i<$num; $i++ ):
        
        $seed = mt_rand(1, 10);
        if( $seed<4 ){
            $str .= chr(mt_rand(48, 57));
        }elseif( $seed<7 ){
            $str .= chr(mt_rand(65, 90));
        }else{
            $str .= chr(mt_rand(97, 122));
        }
    endfor;

    return str_shuffle($str);
}