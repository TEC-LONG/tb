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

    /**
     * 获取菜单权限列表
     */
    public function getMenuPermissionList($request){

        /// 避开点分页时携带无效的搜索参数
        if(isset($request['s_request'])&&$request['s_request']=='') unset($request['s_request']);
        if(isset($request['s_display_name'])&&$request['s_display_name']=='') unset($request['s_display_name']);

        /// 初始化参数
        $menu_permission_model = new MenuPermissionModel('mp');

        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [
            [['s_route', 'mp.route'], 'like'],
            [['s_request', 'mp.request'], '='],
            [['s_display_name', 'mp.display_name'], 'like']
        ]);

        /// 构建查询对象
        $menu_permission_model->where($_condi)
        ->leftjoin('menu_permission as mp1', 'mp.parent_id=mp1.id')
        ->leftjoin('permission as p', 'mp.permission__id=p.id');

        # 分页参数
        $nowPage    = isset($request['pageNum']) ? intval($request['pageNum']) : 1;
        $pagination = $menu_permission_model->pagination($nowPage)->pagination;

        $pagination['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];

        # 查询数据  'u.*, ug.name as gname'
        $rows = $menu_permission_model->select('mp.*, p.name, p.flag, mp1.display_name as parent_name')->limit($pagination['limitM'], $pagination['numPerPage'])->get();

        return [
            'rows'  => $rows,
            'page'  => $pagination
        ];
    }

    /**
     * 新增/编辑 权限菜单功能
     */
    public function menuPermissionPost($request){

        /// 初始化参数
        $now    = time();
        $flag   = ['PLAT'=>1, 'M-LV2'=>2, 'M-LV3'=>3];

        /// 模型对象
        $menu_permission_model = new MenuPermissionModel;

        if( isset($request['id']) ){/// 编辑
            # 查询已有数据
            $row = $menu_permission_model->where(['id', $request['id']])->find();

            # 新老数据对比，取得需要编辑的数据
            $request['level'] = isset($flag[$request['permission_flag']]) ? $flag[$request['permission_flag']] : 4;

            $_upd = Fun::data__need_update($request, $row, [
                'display_name',
                'permission__id',
                'route',
                'request',
                'parent_id',
                'level3_type',
                'level3_href',
                'level',
                'navtab',
                'sort'
            ]);
            if( empty($_upd) ) Err::throw('您还没有修改任何数据！请先修改数据。');

            $_upd['update_time'] = $now;

            # 执行更新
            $re = $menu_permission_model->update($_upd)->where(['id', $request['id']])->exec();
            if( !$re ) Err::throw('编辑操作失败！');

        }else{/// 新增

            # 数据是否重复，重复了没必要新增
            $duplicate = $menu_permission_model->select('id')->where([
                ['display_name', $request['display_name']],
                ['permission__id', $request['permission__id']]
            ])->find();
            if( !empty($duplicate) ) Err::throw('权限菜单已经存在！无需重复添加。');

            # 组装数据
            $insert = [
                'permission__id'    => $request['permission__id'],
                'route'             => $request['route'],
                'display_name'      => $request['display_name'],
                'parent_id'         => $request['parent_id'],
                'request'           => $request['request'],
                'navtab'            => $request['navtab'],
                'level3_type'       => $request['level3_type'],
                'level3_href'       => $request['level3_href'],
                'level'             => isset($flag[$request['permission_flag']]) ? $flag[$request['permission_flag']] : 4,
                'navtab'            => $request['navtab'],
                'sort'              => $request['sort']==='' ? 0 : $request['sort'],
                'post_date'         => $now
            ];

            # 执行新增
            $re = $menu_permission_model->insert($insert)->exec();
            if( !$re ) Err::throw('新增操作失败！');
        }
    }
}