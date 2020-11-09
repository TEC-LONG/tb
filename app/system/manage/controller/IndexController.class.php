<?php

namespace system\manage\controller;
use \model\UserModel;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;
class IndexController extends Controller {

    public function index(){
    
        $this->display('index.tpl');
    }

}