<?php

namespace self\manage\controller;
use \Json;
use \Err;
use \TB;
use \Controller;
class UserController extends Controller
{
    public function userList(){
    
        // Json::var([
        //     'success'=>0,
        //     'message'=>'上传失败！'
        // ])->exec();

        // Err::try(function (){
        
        //     Err::throw('aaaaa');
        // }, 'exit');

        $row = TB::table('user')->where('id=2')->find();
        // echo '<pre>';
        // var_dump($row);

        $this->display('index.tpl');
    }
}
