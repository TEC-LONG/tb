<?php
namespace model;
use \BaseModel;

class MenuPermissionModel extends BaseModel{

    protected $table = 'menu_permission_copy1';

    const C_REQUEST = ['无', 'GET', 'POST', 'REQUEST'];
    const C_LEVEL3_TYPE = ['内部跳转链接', '外部跳转', '无'];

    public function getAllLevelMenu(){
    
        ///查询一级、二级、三级菜单及其相关的权限菜单
        $lv1 = $this->select('*')->where(['level', 1])->get();

        $lv2 = $this->select('*')->where(['level', 2])->get();

        $lv3 = $this->select('*')->where(['level', 3])->get();

        $lv4 = $this->select('*')->where(['level', 'in', '(3, 4)'])->get();

        #组装数据
        $all = [];
        $count2 = 0;
        $count3 = 0;
        foreach( $lv1 as $k1=>$v1){
        
            $all[$k1]['lv1'] = $v1;
            foreach( $lv2 as $k2=>$v2){
                
                if( $v2['parent_id']==$v1['id'] ){

                    $all[$k1]['lv2'][$count2]['menu'] = $v2;
                    unset($lv2[$k2]);
                    foreach( $lv3 as $k3=>$v3){
                
                        if( $v3['parent_id']==$v2['id'] ){
                        
                            // $all[$k1]['lv3'][$count3]['menu'] = $v3;
                            $all[$k1]['lv2'][$count2]['son'][$count3] = $v3;
                            unset($lv3[$k3]);
                            foreach( $lv4 as $k4=>$v4){
                            
                                if( $v4['parent_id']==$v3['id'] ){
                                    // $all[$k1]['lv3'][$count3]['son'][] = $v4;
                                    $all[$k1]['lv2'][$count2]['son'][$count3]['son'][] = $v4;
                                    unset($lv4[$k4]);
                                }
                            }
                            $count3++;
                        }
                    }
                    $count2++;
                }
            }
        }
        return $all;
    }
}