<?php

namespace system\manage\controller;
use \model\UserModel;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;
class UserController extends Controller {

    public function index(){
    
        $this->display('user/list.tpl');
    }

}