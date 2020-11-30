<?php

namespace system\manage\controller;
use \system\manage\service\UserService;
use \model\UserModel;
use \controller;
use \Fun;
use \Json;
use \Err;
use model\UserGroupModel;

class UserController extends Controller {

    /**
     * 用户列表
     */
    public function index(){
    
        /// 初始化参数
        $user_service = new UserService;

        # 接收数据
        $request = Fun::request()->all();

        /// 获取用户列表数据
        $info = $user_service->getUserList($request);

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('ori', UserModel::C_ORI);
        $this->assign('level', UserModel::C_LEVEL);
        $this->assign('status', UserModel::C_STATUS);
        $this->display('user/index.tpl');
    }

    /**
     * 用户组管理列表
     */
    public function group(){
    
        /// 接收数据
        $request = Fun::request()->all();

        /// 初始化参数
        $user_service = new UserService;

        /// 获取用户组列表数据
        $info = $user_service->getGroupList($request);

        ///分配模板变量&渲染模板
        $this->assign($info);
        $this->display('group/index.tpl');
    }

    /**
     * 新增/编辑 用户组
     */
    public function groupEdit(){

        /// 接收数据
        $request = Fun::request()->all();
    
        ///编辑部分
        $info = [];
        if( isset($request['id']) ){
            $info['row'] = (new UserGroupModel)->where(['id', $request['id']])->find();
        }

        ///分配模板变量&渲染模板
        $this->assign($info);   
        $this->display('group/edit.tpl');
    }

    /**
     * 
     */

    

}