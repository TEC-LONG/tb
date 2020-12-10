<?php

/**
 * 方法名：file_arrange
 * 方法作用：将同名多文件的$_FILES元素的格式整理成单文件的格式，如：
     将 $_FILES=[
         'headimg' => [
             'name' => ['aa.jpg', 'bb.jpg'],
            'type' => ['image/jpeg', 'image/jpeg'],
            'tmp_name' => ['xx/xx/xxx/xx.tmp', 'xx/xx/xxx/xxxx.tmp],
            'error' => [0, 0],
            'size' => [12345, 34234]
        ]
    ]
    整理成 $_FILES=[
        'headimg_0' => [
            'name' => 'aa.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => 'xx/xx/xxx/xx.tmp',
            'error' => 0,
            'size' => 12345
        ],
        'headimg_1' => [
            'name' => 'bb.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => 'xx/xx/xxx/xxxx.tmp,
            'error' => 0,
            'size' => 34234
        ]
    ]
    但是最终返回数组，如：['headimg_0', 'headimg_1']
* 参数：
* @param    string    $input_name    某文件$_FILES的下标名（对应的是表单<input type="file" name="xxx" />的name值
* @return    array    整理之后的虚拟表单file名
*/
function arrange($input_name){

    $tmp_arr = $_FILES[$input_name];
    unset($_FILES[$input_name]);
    
    $new_input_name_arr = [];
    foreach ($tmp_arr['name'] as $key => $name) {
        
        $tmp_new_key = $input_name . '_' . $key;
        $new_input_name_arr[$key] = $tmp_new_key;
        $_FILES[$tmp_new_key] = [
            'name' => $name,
            'type' => $tmp_arr['type'][$key],
            'tmp_name' => $tmp_arr['tmp_name'][$key],
            'size' => $tmp_arr['size'][$key],
            'error' => $tmp_arr['error'][$key]
        ];
    }

    return $new_input_name_arr;
}