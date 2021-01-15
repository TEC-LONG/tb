<?php


if( !function_exists('kv2condition') ){

    /**
     * 将key=>value形式的数组，转换为condition条件
     * 如：['name'=>'zhangsan', 'age'=>12]    转换为  [['name', 'zhangsan'], ['age', 12]]
     */
    function kv2condition($kv_arr){

        $condition = [];
        foreach( $kv_arr as $field=>$value){
        
            $condition[] = [$field, $value];
        }

        return $condition;
    }
}


