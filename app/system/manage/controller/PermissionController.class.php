<?php

namespace system\manage\controller;
use \system\manage\service\PermissionService;
use \model\PermissionModel;
use \controller;
use \Route;
use \Json;
use \Err;
use \Fun;
use \TB;

class PermissionController extends Controller {

    /**
     * 权限管理列表
     */
    public function list(){

        /// 接收数据
        $request = Fun::request()->all();

        /// 初始化参数
        $permission_service = new PermissionService;


        /// 获取权限列表数据
        $info = $permission_service->getPermissionList($request);

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('navatab', Route::$navtab);
        $this->assign('flag', PermissionModel::C_FLAG);
        $this->display('permission/index.tpl');
    }

    /**
     * 新增/编辑 权限页面
     */
    public function edit(){

        /// 接收数据
        $request = Fun::request()->all();

        /// 编辑部分
        $info = [];
        if( isset($request['id']) ){
            $info['row'] = (new PermissionModel)->where(['id', $request['id']])->find();
        }

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('navatab', Route::$navtab);
        $this->assign('flag', PermissionModel::C_FLAG);
        $this->display('permission/edit.tpl');
    }

    /**
     * 新增/编辑 权限功能处理
     */
    public function post(){

        try{
             /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            //check($request,  $this->_extra['form-elems'])

            /// 初始化参数
            $permission_service = new PermissionService;

            /// 执行处理
            $permission_service->permissionPost($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => '操作失败！'
            ])->exec('return');
            exit;
        }

        echo Json::vars([
            'statusCode'    => 200,
            'message'       => '操作成功！',
            'navTabId'      => Route::$navtab
        ])->exec('return');
    }

    /**
     * 菜单权限管理列表
     */
    public function menu(){
    
    }

}