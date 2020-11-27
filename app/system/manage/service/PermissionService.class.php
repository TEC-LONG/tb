<?php

namespace system\manage\service;
use \model\PermissionModel;
use model\MenuPermissionModel;
use \Fun;
use \TB;
use \Err;

class PermissionService {
    
    /**
     * 获取权限列表
     */
    public function getPermissionList($request){

        /// 初始化参数
        $permission_model = new PermissionModel;

        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [
            ['name', 'like'],
            ['flag', 'like']
        ]);

        /// 构建查询对象
        $permission_model = $permission_model->where($_condi);

        # 分页参数
        $nowPage    = isset($request['pageNum']) ? intval($request['pageNum']) : 1;
        $pagination = $permission_model->pagination($nowPage)->pagination;

        $pagination['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];

        # 查询数据  'u.*, ug.name as gname'
        $rows = $permission_model->select('*')->limit($pagination['limitM'], $pagination['numPerPage'])->get();
        
        return [
            'rows'  => $rows,
            'page'  => $pagination
        ];
    }

    /**
     * 新增/编辑 权限处理
     */
    public function permissionPost($request){
    
        /// 模型对象
        $permission_model = new PermissionModel;

        if( isset($request['id']) ){/// 编辑
            # 查询已有数据
            $row = $permission_model->where(['id', $request['id']])->find();

            # 新老数据对比，取得需要编辑的数据
            $_upd = Fun::data__need_update($request, $row, ['name', 'flag']);
            if( empty($_upd) ) Err::throw('您还没有修改任何数据！请先修改数据。');

            # 执行更新
            $re = $permission_model->update($_upd)->where(['id', $request['id']])->exec();
            if( !$re ) Err::throw('编辑权限操作失败！');

        }else{/// 新增

            # 数据是否重复，重复了没必要新增
            $duplicate = $permission_model->select('id')->where(['name', $request['name']])->limit(1)->find();
            if( !empty($duplicate) ) Err::throw('权限"'.$request['name'].'"已经存在！无需重复添加。');

            # 组装数据
            $insert = [
                'name'      => $request['name'],
                'flag'      => $request['flag'],
                'post_date' => time()
            ];

            # 执行新增
            $re = $permission_model->insert($insert)->exec();
            if( !$re ) Err::throw('新增权限操作失败！');
        }
    }

    /**
     * 删除 权限处理
     */
    public function permissionDel($request){

        /// 初始化参数
        $permission_id      = $request['id'];
        $permission_model   = new PermissionModel;

        /// 检查该权限是否有被关联使用

        
        /// 执行删除
        $re = $permission_model->where(['id', $permission_id])->delete();
        if( !$re ) Err::throw('删除失败');
    }

    /**
     * 获取菜单权限列表
     */
    public function getMenuPermissionList($request){

        /// 避开点分页时携带无效的搜索参数
        if(isset($request['s_request'])&&$request['s_request']=='') unset($request['s_request']);
        if(isset($request['s_display_name'])&&$request['s_display_name']=='') unset($request['s_display_name']);

        /// 初始化参数
        var_dump(11);
        $menu_permission_model = new MenuPermissionModel('mp');
        var_dump($menu_permission_model);
        echo '<hr/>';
        
        var_dump(22);
        

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
var_dump($menu_permission_model);
exit;

        # 分页参数
        $nowPage    = isset($request['pageNum']) ? intval($request['pageNum']) : 1;
        var_dump($menu_permission_model);
        $pagination = $menu_permission_model->pagination($nowPage)->pagination;
        exit;

        $pagination['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];

        # 查询数据  'u.*, ug.name as gname'
        $rows = $menu_permission_model->select('mp.*, p.name, p.flag, mp1.display_name as parent_name')->limit($pagination['limitM'], $pagination['numPerPage'])->get();

        return [
            'rows'  => $rows,
            'page'  => $pagination
        ];
    }


    
}