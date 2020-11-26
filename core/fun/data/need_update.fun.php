<?php

/**
 * 过滤得到需要更新的数据
 * @param   array   $request
 * @param   array   $row
 * @param   array   $fields
 */
function need_update($request, $row, $fields){
    
    $update_data = [];
    foreach( $row as $k=>$v){
    
        if( in_array($k, $fields) ){
            
            if( $request[$k]!=$v ){
                $update_data[$k] = $request[$k];
            }
        }
    }
    return $update_data;
}