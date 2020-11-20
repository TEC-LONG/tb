<?php

namespace system\manage\controller;
use \system\manage\service\UserService;
use \model\UserModel;
use \controller;
use \Fun;
use \Json;
use \Err;
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
    
        /// 初始化参数
        $user_service = new UserService;

        # 接收数据
        $request = Fun::request()->all();

        /// 获取用户组列表数据
        $info = $user_service->getGroupList($request);

        ///分配模板变量&渲染模板
        $this->assign($info);
        $this->display('user/group.tpl');
    }


    

}