<?php

namespace system\manage\controller;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;
class LoginController extends Controller {

    public function index(){
    
        $this->display('login/index.tpl');
    }

    public function checklogin(){ 

        $request = Fun::request()->all();
        // @session_start();
        // var_dump($_SESSION);
        // F()->var_dump($request);

        //检查验证码
        @session_start();
        if( $_SESSION['checkcode']!==$request['checkcode'] ) Fun::jump('/system/manage/login/index', '验证码不正确！');

        //检查账号密码
        $row = 
        $row = M('UserModel')->select('*')->where([
            ['acc', $request['acc']],
            ['level', 1],
            ['status', '<>', 1]
        ])->limit(1)->find();

        if(empty($row)) J('账号错误！', '/tools/login/index');

        if( $row['pwd']===M('UserModel')->make_pwd($request['pwd'], $row['salt']) ){//账号密码正确，登陆成功
            $_SESSION['admin'] = $row;

            //如果勾选了七天免登录

            //跳转后台首页
            J('登陆成功', '/tools/index', 0);

        }else{//账号或密码不正确，登陆失败
            J('密码错误！', '/tools/login/index');
        }
    }
}