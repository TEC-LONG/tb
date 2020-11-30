<?php

namespace system\manage\controller;
use \system\manage\service\MenuPermissionService;
use \controller;
use \Validator;
use \Route;
use \Json;
use \Err;
use \Fun;
class MenuController extends Controller {

    /**
     * （左侧）菜单管理
     */
    public function list(){

        /// 接收数据
        $request = Fun::request()->all();

        /// 初始化参数
        $menu_permission_service = new MenuPermissionService;

        /// 获取左侧菜单数据
        $info = $menu_permission_service->getLv1Menu();

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('search', $request);
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
     * 校验 add方法 参数
     */
    private function addValidate($request){
    
        $validator = Validator::make($request, [
            'level'         => 'required$||int',
            'parent_id'     => 'required$||int',
            'display_name'  => 'required'
        ]);

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 新增菜单
     */
    public function add(){

        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 校验参数
            $this->addValidate($request);

            /// 初始化参数
            $menu_permission_service = new MenuPermissionService;

            /// 新增菜单
            $menu_permission_service->createMenu($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
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
     * 校验 add方法 参数
     */
    private function updValidate($request){
    
        $validator = Validator::make($request, [
            'id' => 'required$||int'
        ]);

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 修改菜单
     */
    public function upd(){

        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 校验参数
            $this->updValidate($request);

            /// 初始化参数
            $menu_permission_service = new MenuPermissionService;

            /// 修改菜单
            $menu_permission_service->editMenu($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
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