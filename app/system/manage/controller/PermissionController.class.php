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




        ///接收数据
        $request = REQUEST()->all();

        ///检查数据
        //check($request,  $this->_extra['form-elems'])

        ///模型对象
        $obj = M()->table('menu_permission');

        if( isset($request['id']) ){///编辑
            #查询已有数据
            $ori = $obj->select('*')->where(['id', $request['id']])->find();

            #新老数据对比，构建编辑数据
            $request['level'] = isset($this->flag[$request['permission_flag']]) ? $this->flag[$request['permission_flag']] : 4;
            $update = F()->compare($request, $ori, ['display_name', 'permission__id', 'route', 'request', 'navtab', 'parent_id', 'level3_type', 'level3_href', 'level', 'sort']);
            if( empty($update) ) JSON()->stat(300)->msg('您还没有修改任何数据！请先修改数据。')->exec();
            
            $update['update_time'] = time();
            $re = $obj->update($update)->where(['id', $request['id']])->exec();

        }else{///新增

            #数据是否重复，重复了没必要新增
            $duplicate = $obj->select('id')->where([
                ['display_name', $request['display_name']],
                ['permission__id', $request['permission__id']],
                ['menu__id', $request['menu__id']]
            ])->limit(1)->find();
            if(!empty($duplicate)) JSON()->stat(300)->msg('权限菜单已经存在！无需重复添加。')->exec();

            $insert = [
                'permission__id' => $request['permission__id'],
                'route' => $request['route'],
                'display_name' => $request['display_name'],
                'parent_id' => $request['parent_id'],
                'request' => $request['request'],
                'navtab' => $request['navtab'],
                'level3_type' => $request['level3_type'],
                'level3_href' => $request['level3_href'],
                'level' => isset($this->flag[$request['permission_flag']]) ? $this->flag[$request['permission_flag']] : 4,
                'navtab' => $request['navtab'],
                'sort' => $request['sort'],
                'post_date' => time()
            ];

            $re = $obj->insert($insert)->exec();
        }
        
        ///返回结果
        if( $re ){
            JSON()->navtab($this->_navTab.'_mpindex')->exec();
        }else{
            JSON()->stat(300)->msg('操作失败')->exec();
        }
    }
}