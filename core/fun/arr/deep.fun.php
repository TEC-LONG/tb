<?php

/**
 * 返回给定数据的维数，最多返回到2维度
 */
function deep($arr){

    if(!is_array($arr)) return 0;//不是数组

    if (count($arr)==count($arr, 1)) {
        return 1;//一维数组
    } else {
        return 2;//二维数组
    }
}