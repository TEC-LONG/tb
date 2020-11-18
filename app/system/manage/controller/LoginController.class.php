<?php

namespace system\manage\controller;
use \model\UserModel;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;
class LoginController extends Controller {

    public function index(){
    
        $this->display('login/index.tpl');
    }

    public function check(){ 

        $request = Fun::request()->all();

        /// 检查验证码
        @session_start();
        // if( $_SESSION['checkcode']!==$request['checkcode'] ) Fun::jump('/system/manage/login/index', '验证码不正确！');

        /// 检查账号密码
        $user_model = new UserModel;
        $row = $user_model->select('*')->where([
            ['acc', $request['acc']],
            ['level', 1],
            ['status', '<>', 1]
        ])->find();

        if(empty($row)) Fun::jump('/system/manage/login/index', '账号错误！');

        // if( $row['pwd']===$user_model->make_pwd($request['pwd'], $row['salt']) ){/// 账号密码正确，登陆成功
        if( $row['pwd']===$request['pwd'] ){/// 账号密码正确，登陆成功

            $_SESSION['manager'] = $row;

            # 如果勾选了七天免登录

            # 跳转后台首页
            Fun::jump('/system/manage/index', '登录成功', 2);
            
        }else{/// 账号或密码不正确，登陆失败
            Fun::jump('/system/manage/login/index', '密码错误');
        }
    }
}