<?php

/**
 * 过滤得到需要更新的数据
 * @param   array   $request
 * @param   array   $row
 * @param   array   $fields
                $fields = [
                    'form_elem' => ‘field'
                ]
 */
function need_update($request, $row, $fields){
    
    $update_data = [];
    foreach( $row as $k=>$v){
    
        if( in_array($k, $fields) ){

            $form_elem = array_search($k, $fields);
            if( is_numeric($form_elem) ){
                $form_elem = $k;   
            }
            
            if( $request[$form_elem]!=$v ){
                $update_data[$k] = $request[$form_elem];
            }
        }
    }
    return $update_data;
}