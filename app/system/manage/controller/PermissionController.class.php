<?php

namespace system\manage\controller;
use \system\manage\service\MenuPermissionService;
use \system\manage\service\PermissionService;
use \model\MenuPermissionModel;
use \model\PermissionModel;
use \controller;
use \Validator;
use \Route;
use \Json;
use \Fun;
use \Err;

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
        $this->assign('search', $request);
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
     * 校验 post方法 参数
     */
    private function postValidate($request){
    
        if( isset($request['id']) ){/// 编辑
        
            $validator = Validator::make($request, [
                'id'   => 'int'
            ],[
                'id.int' => '非法的id参数'
            ]);
        }else {/// 新增
            $validator = Validator::make($request, [
                'name'  => 'required',
                'flag'  => 'int'

            ],[
                'name.required' => 'name为必填项',
                'flag.int'      => '非法的flag参数'
            ]);
        }

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 新增/编辑 权限功能处理
     */
    public function post(){

        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            $this->postValidate($request);

            /// 初始化参数
            $permission_service = new PermissionService;

            /// 执行处理
            $permission_service->permissionPost($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
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
     * 校验 del方法 参数
     */
    private function delValidate($request){
    
        $validator = Validator::make($request, [
            'id'   => 'required$||int'
        ],[
            'id.int'        => '非法的id参数',
            'id.required'   => '缺少id参数',
        ]);

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 删除 权限功能处理
     */
    public function del(){

        try{
             /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            $this->delValidate($request);

            /// 初始化参数
            $permission_service = new PermissionService;

            /// 执行处理
            $permission_service->permissionDel($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
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

        /// 接收数据
        $request = Fun::request()->all();

        /// 初始化参数
        $menu_permission_service = new MenuPermissionService;

        /// 获取权限列表数据
        $info = $menu_permission_service->getMenuPermissionList($request);

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('search', $request);
        $this->assign('navatab', Route::$navtab);
        $this->assign('flag', PermissionModel::C_FLAG);
        $this->assign('mp_request', MenuPermissionModel::C_REQUEST);
        $this->assign('mp_lv3_type', MenuPermissionModel::C_LEVEL3_TYPE);
        $this->display('menu_permission/index.tpl');
    }

    /**
     * 新增/编辑 权限菜单页面
     */
    public function menuEdit(){

        /// 接收数据
        $request = Fun::request()->all();

        /// 编辑部分
        $info = [];
        if( isset($request['id']) ){
            $info['row'] = (new MenuPermissionModel('mp'))->select('mp.*, m.name, p.name as pname, p.flag')
            ->leftjoin('menu as m', 'mp.menu__id=m.id')
            ->leftjoin('permission as p', 'p.id=mp.permission__id')
            ->where(['mp.id', $request['id']])->find();
        }

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('flag', PermissionModel::C_FLAG);
        $this->assign('mp_request', MenuPermissionModel::C_REQUEST);
        $this->assign('mp_lv3_type', MenuPermissionModel::C_LEVEL3_TYPE);
        $this->display('menu_permission/edit.tpl');
    }

    /**
     * 校验 menuPost方法 参数
     */
    private function menuPostValidate($request){
    
        $_rule = [
            'display_name'      => 'required',
            'permission_flag'   => 'required',
            'permission__id'    => 'required$||int'
        ];
        $_rule_msg = [
            'display_name.required'     => '缺少【页面名称】',
            'permission_flag.required'  => '缺少【权限名称】@01',
            'permission__id.required'   => '缺少【权限名称】@02',
            'permission__id.int'        => '非法的参数【02】',
        ];

        if( isset($request['id']) ){/// 编辑
        
            $_rule['id']            = 'int';
            $_rule_msg['id.int']    = '非法的id参数';
            $validator = Validator::make($request, $_rule, $_rule_msg);
        }else {/// 新增
            $validator = Validator::make($request, $_rule, $_rule_msg);
        }

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 新增/编辑 权限菜单功能
     */
    public function menuPost(){
    
        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            $this->menuPostValidate($request);

            /// 初始化参数
            $menu_permission_service = new MenuPermissionService;

            /// 执行处理
            $menu_permission_service->menuPermissionPost($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
            ])->exec('return');
            exit;
        }

        echo Json::vars([
            'statusCode'    => 200,
            'message'       => '操作成功！',
            'navTabId'      => Route::$navtab
        ])->exec('return');
    }
}