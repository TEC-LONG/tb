<?php

namespace system\manage\controller;
use \system\manage\service\GroupService;
use \system\manage\service\UserService;
use model\UserGroupModel;
use \model\UserModel;
use \controller;
use \Validator;
use \Route;
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
     * 新增/编辑 用户组页
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
     * 校验 groupPost方法 参数
     */
    private function groupPostValidate($request){
    
        $_rule = [
            'name' => 'required',
            'sort' => 'int$|max&:100$|min&:0'
        ];
        $_rule_msg = [
            'name.required' => '缺少【组名】',
            'sort.int'      => '【排序】值错误',
            'sort.int.max'  => '【排序】值不能超过100',
            'sort.int.min'  => '【排序】值不能小于0'
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
     * 新增/编辑 用户组功能
     */
    public function groupPost(){

        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            $this->groupPostValidate($request);

            /// 初始化参数
            $group_service = new GroupService;

            /// 执行处理
            $group_service->groupPost($request);

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