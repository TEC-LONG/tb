<?php
namespace model;
use \BaseModel;

class IntervalsModel extends BaseModel{

    protected $table = 'tl_intervals';

    /**
     * 偏移率转区间条件
     */
    public function pianyilv2IntervalsId($ma_plv, $statistics_rules__id){

        /// 初始化参数
        $_condi = [
            ['statistics_rules__id', $statistics_rules__id]
        ];

        # 数据表的值是比值小数乘以10000倍后的值，故乘以100则为百分数
        $_100bei_plv        = $ma_plv/100;### 3432/100=34.32
        $_compare_target    = (int)number_format($_100bei_plv*100);#### 34.32*100  ==》 3432

        # 去除小数
        $_100bei_plv_del_point  = (int)number_format($_100bei_plv);### 34.32 ==》34

        /// 确定偏移率区间
        if( $_compare_target>(70*100) ){
        
            $_condi[] = ['b_interval', 70];
            $_condi[] = ['is_equal_to_b_interval', 0];
            $_condi[] = ['e_interval', 111];
            $_condi[] = ['is_equal_to_e_interval', 0];

        }elseif( $_compare_target<=(-70*100) ){
            
            $_condi[] = ['b_interval', -111];
            $_condi[] = ['is_equal_to_b_interval', 0];
            $_condi[] = ['e_interval', -70];
            $_condi[] = ['is_equal_to_e_interval', 1];
        }else{

            $_is_oushu = $_100bei_plv_del_point%2==0;

            if( $_is_oushu ){# 偶数
            
                $_100bei_plv_del_point_10bei = $_100bei_plv_del_point*100;## 34*100 ==》 3400

                if( $_compare_target>$_100bei_plv_del_point_10bei ){## 3432>3400
                    ##                    34                             36
                    // $_info_key = '>'.$_100bei_plv_del_point.'_<='.($_100bei_plv_del_point+2);
                    $_condi[] = ['b_interval', $_100bei_plv_del_point];
                    $_condi[] = ['is_equal_to_b_interval', 0];
                    $_condi[] = ['e_interval', ($_100bei_plv_del_point+2)];
                    $_condi[] = ['is_equal_to_e_interval', 1];
                }else{## 3400<=3400
                    ##                     34-2                            34
                    // $_info_key = '>'.($_100bei_plv_del_point-2).'_<='.$_100bei_plv_del_point;
                    $_condi[] = ['b_interval', ($_100bei_plv_del_point-2)];
                    $_condi[] = ['is_equal_to_b_interval', 0];
                    $_condi[] = ['e_interval', $_100bei_plv_del_point];
                    $_condi[] = ['is_equal_to_e_interval', 1];
                }

            }else{# 奇数 如33   则$_info_key='>'.(33-1).'_<='.(33+1)

                // $_info_key = '>'.($_100bei_plv_del_point-1).'_<='.($_100bei_plv_del_point+1);
                $_condi[] = ['b_interval', ($_100bei_plv_del_point-1)];
                $_condi[] = ['is_equal_to_b_interval', 0];
                $_condi[] = ['e_interval', ($_100bei_plv_del_point+1)];
                $_condi[] = ['is_equal_to_e_interval', 1];
            }
        }

        /// 获取数据，返回id
        $_arr = $this->select('id')->where($_condi)->find();

        if( !empty($_arr) ){
            return $_arr['id'];
        }else{
            return false;
        }
    }
}