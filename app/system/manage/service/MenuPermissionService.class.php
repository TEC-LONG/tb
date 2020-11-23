<?php

namespace system\manage\service;
use \model\MenuPermissionModel;
use \Fun;
use \TB;

class MenuPermissionService {
    
    /**
     * 获取一级菜单
     */
    public function getLv1Menu(){

        /// 初始化参数
        $menu_permission_model = new MenuPermissionModel;

        /// 查询数据
        $rows = $menu_permission_model->select('id, display_name, level')->where([
            ['parent_id', 0],
            ['level', 1]
        ])->get();

        /// 组装数据
        $one = [];
        foreach( $rows as $k=>$row ){
            $one[$k]['id']     = $row['id'];
            $one[$k]['name']   = $row['display_name'];
            $one[$k]['level']  = $row['level'];
        }

        return [
            'one' => $one
        ];
    }

    /**
     * 获取子菜单
     */
    public function getMenuChild($request){

        /// 初始化参数
        $menu_permission_model = new MenuPermissionModel;
        
        /// 查询数据
        $rows = $menu_permission_model->select('id, display_name, level')->where(['parent_id', $request['p_id']])->get();

        if( empty($rows) ) return [];
    
        $child = [];
        foreach( $rows as $k=>$row ){
            $child[$k]['id']     = $row['id'];
            $child[$k]['name']   = $row['display_name'];
            $child[$k]['level']  = $row['level'];
        }

        return $child;
    }

    
}