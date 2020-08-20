<?php

namespace self\manage\controller;
use \Json;
use \Err;
class UserController
{
    public function userList(){
    
        // Json::var([
        //     'success'=>0,
        //     'message'=>'上传失败！'
        // ])->exec();

        Err::try(function (){
        
            Err::throw('aaaaa');
        }, 'exit');
    }
}
