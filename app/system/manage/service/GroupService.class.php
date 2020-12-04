<?php

namespace system\manage\service;
use \model\UserGroupModel;
use \Fun;
use \Err;

class GroupService {
    
    /**
     * 获取用户列表数据
     */
    public function groupPost($request){

        /// 初始化参数
        $now                = time();
        $user_group_model   = new UserGroupModel;

        if( isset($request['id']) ){/// 编辑
            # 查询已有数据
            $row = $user_group_model->where(['id', $request['id']])->find();

            # 新老数据对比，构建编辑数据
            $_upd = Fun::data__need_update($request, $row, ['name', 'sort', 'comm']);
            if( empty($_upd) ) Err::throw('您还没有修改任何数据！请先修改数据。');

            # 执行更新
            $re = $user_group_model->update($_upd)->where(['id', $request['id']])->exec();
            if( !$re ) Err::throw('编辑操作失败');

        }else{/// 新增

            # 数据是否重复，重复了没必要新增
            $duplicate = $user_group_model->select('id')->where(['name', $request['name']])->find();
            if(!empty($duplicate)) Err::throw('用户组"'.$request['name'].'"已经存在！无需重复添加。');

            $insert = [
                'name'      => $request['name'],
                'sort'      => empty($request['sort']) ? 0 : $request['sort'],
                'comm'      => $request['comm'],
                'post_date' => $now
            ];

            # 执行新增
            $re = $user_group_model->insert($insert)->exec();
            if( !$re ) Err::throw('新增操作失败');
        }
    }

}