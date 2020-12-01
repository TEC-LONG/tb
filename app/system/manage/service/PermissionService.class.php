<?php

namespace system\manage\service;
use \model\PermissionModel;
use model\MenuPermissionModel;
use \Fun;
use \Err;
use model\UserGroupPermissionModel;

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
            $duplicate = $permission_model->select('id')->where(['name', $request['name']])->find();
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
     * 获取菜单权限页面数据
     */
    public function getGroupPermission($request){

        /// 初始化参数
        $menu_permission_model          = new MenuPermissionModel;
        $user_group_permission_model    = new UserGroupPermissionModel;
    
        /// 查询当前组所具有的权限
        $power_arr = $user_group_permission_model->select('menu_permission__id')
        ->where(['user_group__id', $request['id']])
        ->get();

        $power = [];
        foreach( $power_arr as $k=>$v){
        
            $power[] = $v['menu_permission__id'];
        }
        ///查询所有的权限菜单
        $menu = $menu_permission_model->getAllLevelMenu();

        return [
            'power' => $power,
            'menu'  => $menu
        ];
    }

    /**
     * 设置用户组权限功能
     */
    public function groupPermissionPost($request){
    
        /// 初始化参数
        $mp_id                          = isset($request['mp_id']) ? $request['mp_id'] : [];
        $user_group__id                 = $request['user_group__id'];
        $user_group_permission_model    = new UserGroupPermissionModel;
        
        /// 调整数据
        # 查询已有数据
        $ori = $user_group_permission_model->select('menu_permission__id')->where(['user_group__id', $user_group__id])->get();
        $ori = empty($ori) ? [] : $ori;

        $ori_mp_id = [];
        foreach( $ori as $v){
        
            $ori_mp_id[] = $v['menu_permission__id'];
        }

        # 比对数据
        ## 交集 不变
        // $id_intersect = array_intersect($mp_id, $ori_mp_id);

        ## 在提交中不在原始中的 新增
        $id_ad = array_diff($mp_id, $ori_mp_id);

        ## 在原始中不在提交中的 删除
        $id_del = array_diff($ori_mp_id, $mp_id);

        /// 操作数据表
        #新增
        $ad_flag = false;
        if( !empty($id_ad) ){
        
            $data_ad = [];
            $post_date = time();
            foreach( $id_ad as $v){
                array_push($data_ad, [$v, $post_date, $request['user_group__id']]);
            }
            $re = $user_group_permission_model->fields('menu_permission__id, post_date, user_group__id')
            ->insert($data_ad)
            ->exec();

            if($re) $ad_flag=true;
        }

        #删除
        $del_flag = false;
        if( !empty($id_del) ){

            $re = $user_group_permission_model->where([
                ['menu_permission__id', 'in', '('.implode(',', $id_del).')'],
                ['user_group__id', $request['user_group__id']]
            ])->delete();

            if($re) $del_flag=true;
        }

        if( !$ad_flag&&!$del_flag ) Err::throw('请先修改数据再提交！');
    }

    
}