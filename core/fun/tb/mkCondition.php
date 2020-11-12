<?php


function mkCondition($request, $search_form_elems, $default_condition=[]){

    $_condition = [];

    /// 构建默认条件
    if( !empty($default_condition) ){
    
         $_c_default = [];

    }

    /// 构建搜索条件
    if( !empty($search_form_elems) ){
    
        $_c_search = [];

    }

    return $_condition;
}

function (){

}




/**
 * @method  _condition_string
 * 方法作用: 将符合要求的指定字段，处理为字符串类型的where条件
 * 
 * @param    $request       array    [表单传值的集合]
 * @param    $form_elems    array    [指定的条件字段及其规则，如：]
            $form_elems = [
                ['acc',         'like'],
                ['nickname',    'like']
            ];
    * @param    $con_arr       array    [默认的条件字段，如：$con_arr=['is_del', 0];]
    * 
    * @return    string    [字符串类型的条件语句]
    */
function _condition_string($request, $form_elems, $con_arr){

    $con_search = $this->_condition($request, $form_elems);
    $con_default = $this->_condition($con_arr, [], 2);
    $con_arr = array_merge($con_default, $con_search);//将非查询的数据与查询的数据进行合并，形成完整的条件数组数据
    
    $con = [];
    /*
    $con_arr = [
        'name' => '="zhangsan"',
        'post_date' => [
            ['>=1234567'],
            ['<=7654321']
        ]
    ]
    */
    foreach( $con_arr as $field=>$val){
    
        if( is_array($val) ){
            $con[] = $field . $val[0];
            $con[] = $field . $val[1];
        }else{
            $con[] = $field . $val;
        }
    }

    $con = implode(' and ', $con);

    return $con;
}
/**
 * 方法名:_condition
 * 方法作用:处理条件初稿，得到可使用的条件数组集合
 * 参数：
 * $request
 * $form_elems
 * $type    处理方式，1=处理带限制规则的条件，当$type为1时，只需要传递第一个参数；2=处理不带限制规则的条件
 * return: array
 */
function _condition($request, $form_elems=[], $type=1){

    $con = [];
    if( $type==1 ){

        foreach( $form_elems as $elem){

            if($elem[1]==='time-in'){
                $has_begin = isset($request['b_'.$elem[0]])&&$request['b_'.$elem[0]]!=='';
                $has_end = isset($request['e_'.$elem[0]])&&$request['e_'.$elem[0]]!=='';
                if(!$has_begin&&!$has_end) continue;
            }else{
                if(!isset($request[$elem[0]])||$request[$elem[0]]==='') continue;
            }
            
            if( isset($elem[1]) ){//y有特殊处理标记

                if( $elem[1]==='mul' ){//数组
                    
                    $str_arr = [];
                    //        [1, 3, 4]
                    foreach( $request[$elem[0]] as $val){

                        $str_arr[] = $val;
                    }
                    //                             1|3|4
                    $con[$elem[0]] = ' REGEXP "' . implode('|', $str_arr) . '"';
                }elseif( $elem[1]==='like' ){//模糊匹配

                    $con[$elem[0]] = ' like "%' . $request[$elem[0]] . '%"';
                }elseif ( $elem[1]==='equal' ) {
                    
                    $con[$elem[0]] = '="' . $request[$elem[0]] . '"';
                }elseif ( $elem[1]==='time-in' ) {
                    
                    $con[$elem[0]][0] = '>=' . strtotime($request['b_'.$elem[0]]);
                    $con[$elem[0]][1] = '<=' . strtotime($request['e_'.$elem[0]]);
                }
            
            }else{//普通

                //     'acc'                     'acc'
                $con[$elem[0]] = '="' . $request[$elem[0]] . '"';
            }
        }
    }elseif ($type==2) {
        
        if( is_array($request[0]) ){
                
            foreach( $request as $k=>$v){

                if( count($v)==3 ){
                    $con[$v[0]] = $v[1] . '"' . $v[2] . '"';
                }elseif( strpos($v[1], '=')!==false ){

                    // $con[$k][$v[0]] = $v[1];
                    $con[$v[0]] = $v[1];
                }else{
                    // $con[$k][$v[0]] = '="' . $v[1] . '"';
                    $con[$v[0]] = '="' . $v[1] . '"';
                }
            }
        }else{
            
            if( count($request)==3 ){

                $con[$request[0]] = $request[1] . '"' . $request[2] . '"';
            }elseif( strpos($request[1], '=')!==false ){

                $con[$request[0]] = $request[1];
            }else{
                $con[$request[0]] = '="' . $request[1] . '"';
            }
        }
    }
    
    return $con;
}