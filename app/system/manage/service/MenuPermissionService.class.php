<?php

namespace system\manage\service;
use \model\MenuPermissionModel;
use \Fun;
use \TB;

class MenuPermissionService {
    
    /**
     * 获取用户列表数据
     */
    public function getLeftMenu(){

        /// 初始化参数
        $menu_permission_model = new MenuPermissionModel;

        /// 查询数据
        $rows = $menu_permission_model->where([
            ['parent_id', 0],
            ['level', 1]
        ])->get();

        /// 组装数据
        $first = [];
        foreach( $rows as $k=>$row ){
            $first['id'][$k]     = $row['id'];
            $first['name'][$k]   = $row['display_name'];
            $first['level'][$k]  = $row['level'];
        }

        return [
            'one' => $first
        ];
    }

    
}