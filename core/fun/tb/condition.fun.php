<?php


if( !function_exists('condition') ){

    /**
     * 根据form表单搜索栏构建查询条件
     */
    function condition($request, $search_form_elems, $default_condition=[]){

        $_condition = [];
        /// 构建默认条件
        if( !empty($default_condition) ){
        
            $_condition = array_merge($_condition, $default_condition);
    }

        /// 构建搜索条件
        if( !empty($search_form_elems) ){
        
            $_c_search  = mkc_c_search($request, $search_form_elems);
            $_condition = array_merge($_condition, $_c_search);
        }

        return $_condition;
    }

    /**
     * @param   $search_form_elems  array
                    [
                        [['表单元素名',                 '数据表字段名'], '匹配规则'],
                        [[['表单元素名1', '表单元素名2'], '数据表字段名'], '匹配规则'],
                        ['表单元素名(数据表字段名与表单元素名一致)',        '匹配规则'],
                        [['acc', 'account'],                          'like'],
                        ['id',                                        '='],
                        [[['b_time', 'e_time'], 'create_time'],       'between']
                        [[['b_time', 'e_time'], 'create_time'],       '>|<=']
                    ]
    */
    function mkc_c_search($request, $search_form_elems){

        $_condi     = [];
        $_condi_k   = 0;
        foreach( $search_form_elems as $elem){

            $_c     = 0;
            $rule   = $elem[1];

            if( is_array($elem[0]) ){# ['表单元素名', '数据表字段名'] 或  [['表单元素名1', '表单元素名2'], '数据表字段名']
            
                $form_elem  = $elem[0][0];
                $field_name = $elem[0][1];

                if( is_array($form_elem) ){# ['表单元素名1', '表单元素名2']
                
                    $form_elem1         = $form_elem[0];
                    $form_elem2         = $form_elem[1];

                    if( !isset($request[$form_elem1]) || !isset($request[$form_elem2]) ) continue;

                    if( in_array($rule, ['between']) ){
                    
                        $_condi_son_val     = $request[$form_elem1] . ' and ' . $request[$form_elem2];
                        $_condi[$_condi_k]  = [$field_name, $rule, $_condi_son_val];
                    }else{## '>|<='

                        $_rules = explode('|', $rule);
                        if( empty($_rules) ) continue;

                        foreach( $_rules as $_r_k=>$_r){

                            $_form_elem             = $form_elem[$_r_k];
                            $_condi_son_val         = $request[$_form_elem];
                            $_condi_k_son           = $_condi_k + $_c;
                            $_condi[$_condi_k_son]  = [$field_name, $_r, $_condi_son_val];

                            $_c++;
                        }
                    }

                }elseif( is_string($form_elem) ){# '表单元素名'

                    if( !isset($request[$form_elem]) ) continue;

                    $_condi_son_val     = $request[$form_elem];
                    $_condi[$_condi_k]  = [$field_name, $rule, $_condi_son_val];
                }else{
                    continue;
                }

            }elseif( is_string($elem[0]) ){# ['表单元素名(数据表字段名与表单元素名一致)', '匹配规则']

                if( strpos($elem[0], '.') ){
                
                    $_str       = explode('.', $elem[0]);## u.nickname
                    $form_elem  = $_str[1];
                    $field_name = $elem[0];
                }else{
                    $form_elem = $field_name = $elem[0];
                }
                
                if( !isset($request[$form_elem]) ) continue;

                $_condi_son_val             = $request[$form_elem];
                $_condi[$_condi_k]          = [$field_name, $rule, $_condi_son_val];
            }else{
                continue;
            }

            if( $_c===0 ){
            
                $_condi_k++;
            }else{
                $_condi_k+=$_c;
            }

            /* if( $elem[1]==='mul' ){//数组
                    
                $str_arr = [];
                //        [1, 3, 4]
                foreach( $request[$elem[0]] as $val){

                    $str_arr[] = $val;
                }
                //                             1|3|4
                $con[$elem[0]] = ' REGEXP "' . implode('|', $str_arr) . '"';
            } */
        }

        return $_condi;
    }
}


