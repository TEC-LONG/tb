<?php

namespace self\manage\controller;
use \Json;
use \Err;
use \Fun;
use \TB;
use \Controller;
class UserController extends Controller
{
    public function userList(){

        /// 3)
        // $re = TB::table('plate_prosperity_index_statistics_day')->fields([
        //     'plate__id',
        //     'numb',
        //     'active_date'
        // ])->insert("
        //     10,
        //     '30.6',
        //     '2020-10-01 12:01:02'
        // ")->insert("
        //     11,
        //     '30.6',
        //     '2020-10-01 12:01:02'
        // ")->exec();

        // var_dump($re); echo '<br/>';
        // $last_insert_id = TB::last_insert_id();
        // var_dump($last_insert_id);


var_dump(123);
        exit;
        


    
        // $v1 = Fun::logic__src('www.aa.com', 'AA.BB.CC');
        // var_dump($v1);
        // exit;
        
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
