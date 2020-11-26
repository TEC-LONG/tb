<?php

namespace system\manage\controller;
use \system\manage\service\MenuPermissionService;
use \controller;
use \Route;
use \Json;
use \Err;
use \Fun;
use \TB;

class MenuController extends Controller {

    /**
     * （左侧）菜单管理
     */
    public function list(){

        /// 初始化参数
        $menu_permission_service = new MenuPermissionService;

        /// 获取左侧菜单数据
        $info = $menu_permission_service->getLv1Menu();

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('navatab', Route::$navtab);
        $this->display('menu/index.tpl');
    }

    /**
     * 获取子菜单
     */
    public function menuChild(){ 

        /// 初始化参数
        $menu_permission_service = new MenuPermissionService;

        # 接收数据
        $request = Fun::request()->all();

        /// 获取子菜单
        $child_info = $menu_permission_service->getMenuChild($request);

        /// 返回数据
        echo Json::vars($child_info)->exec('return');
    }

    /**
     * 新增菜单
     */
    public function add(){

        try{
            /// 初始化参数
            $menu_permission_service = new MenuPermissionService;
            # 接收数据
            $request = Fun::request()->all();

            /// 新增菜单
            $menu_permission_service->createMenu($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => '操作失败！'
            ])->exec('return');
            exit;
        }
        
        echo Json::vars([
            'statusCode'    => 200,
            'navTabId'      => Route::$navtab,
            'message'       => '添加成功！'
        ])->exec('return');

    }

    /**
     * 修改菜单
     */
    public function upd(){

        try{
            /// 初始化参数
            $menu_permission_service = new MenuPermissionService;
            # 接收数据
            $request = Fun::request()->all();

            /// 修改菜单
            $menu_permission_service->editMenu($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => '操作失败！'
            ])->exec('return');
            exit;
        }

        echo Json::vars([
            'statusCode'    => 200,
            'navTabId'      => Route::$navtab,
            'message'       => '修改EXP分类成功！'
        ])->exec('return');
    }

}