<?php

namespace system\manage\service;
use \model\UserModel;
use \model\UserGroupModel;
use \Fun;
use \TB;

class UserService {
    
    /**
     * 获取用户列表数据
     */
    public function getUserList($request){

        /// 初始化参数
        $user_model = new UserModel('u');

        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [
            ['acc', 'like'],
            ['nickname', 'like']
        ], [
            ['is_del', 0],
            ['level', 1]
        ]);

        /// 构建查询对象
        $user_model = $user_model->leftjoin('user_group as ug', 'ug.id=u.user_group__id')->where($_condi);

        # 分页参数
        $nowPage    = isset($request['pageNum']) ? intval($request['pageNum']) : 1;
        $pagination = $user_model->pagination($nowPage)->pagination;

        $pagination['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];

        # 查询数据  'u.*, ug.name as gname'
        $rows = $user_model->select([
            'u.*',
            'name' => 'gname'
        ])->limit($pagination['limitM'], $pagination['numPerPage'])->get();
        
        return [
            'rows'  => $rows,
            'page'  => $pagination
        ];
    }

    /**
     * 获取用户组列表
     */
    public function getGroupList($request){

        /// 初始化参数
        $user_group_model = new UserGroupModel;

        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [
            [['s_name', 'name'], 'like']
        ]);

        /// 构建查询对象
        $query = $user_group_model->where($_condi);

        # 分页参数
        $nowPage    = isset($request['pageNum']) ? intval($request['pageNum']) : 1;
        $pagination = $query->pagination($nowPage)->pagination;

        $pagination['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];

        # 查询数据  'u.*, ug.name as gname'
        $rows = $query->select('*')->limit($pagination['limitM'], $pagination['numPerPage'])->get();
        
        return [
            'rows'  => $rows,
            'page'  => $pagination
        ];
    }
}