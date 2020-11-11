<?php

namespace midware;
use \Controller;
use \Json;
use \Err;
use \Fun;
use \TB;

class AuthorMiddle
{
    public function checkLogin(){
    
        @session_start();
        /// 检查之前是否已经登陆过
        if( !isset($_SESSION['manager']) ){# 没有登录信息，则需要重新登录
            
            /// 校验免登陆
            //if( isset($_COOKIE['is_login']) ){# 没有SESSION登陆信息，但是存在7天免登录信息，则重新找回之前点击7天免登录的用户信息
                //根据记录的COOKIE信息找回用户所有的信息
                //$userModel = M('\\model\\UserModel');
                //$acc = T($_COOKIE['is_login']);
                //$sql = "select * from bl_user where account='{$acc}'";
                //$user = $userModel->getRow($sql);
                //将找回的用户信息重新存储进SESSION名为user的元素中
                //$_SESSION['user'] = $user;

            //}else{# 即不存在SESSION登陆信息，也没有之前记录的7天免登录信息，则重新登陆
                Fun::jump('/system/manage/login/index', '您尚未登录，请先登录！');
            //}
        }

        ///唤起登录的用户数据
        Controller::$manager = $_SESSION['manager'];
        
    }
}
