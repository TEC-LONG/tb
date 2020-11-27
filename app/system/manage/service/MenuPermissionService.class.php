<?php

namespace system\manage\service;
use \model\MenuPermissionModel;
use \Err;
use \Fun;

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

    /**
     * 新增菜单
     */
    public function createMenu($request){

        /// 初始化参数
        $menu_permission_model = new MenuPermissionModel;

        /// 组装数据
        $data = [
            'display_name'  => $request['name'],
            'parent_id'     => $request['pid'],
            'post_date'     => time(),
            'level'         => $request['plevel']+1
        ];

        /// 执行新增
        if( !$re = $menu_permission_model->insert($data)->exec() ){
        
            Err::throw('添加失败!');
        }
    }

    /**
     * 修改菜单
     */
    public function editMenu($request){

        /// 初始化参数
        $data                   = [];
        $menu_permission_id     = $request['id'];
        $menu_permission_model  = new MenuPermissionModel;

        /// 组装数据
        if( $request['name']!==$request['ori_name'] ){

            $data['display_name'] = $request['name'];
        }

        if(empty($data)) Err::throw('数据没有变化，请先修改数据！');

        /// 执行修改
        if( !$re = $menu_permission_model->update($data)->where(['id', $menu_permission_id])->exec() ){
            
            Err::throw('修改操作失败！');
        }
    }

    
}