<?php

namespace system\manage\controller;
use \system\manage\service\UserService;
use \model\UserModel;
use \controller;
use \Json;
use \Err;
use \TB;
class UserController extends Controller {

    public function index(){
    
        /// 初始化参数
        $user_service = new UserService;
        # 接收数据
        $request = Fun::request()->all();

        /// 获取用户列表数据
        $user_list = $user_service->getIndexList($request);



        /* //查询条件(融合搜索条件)
        $con_arr = [['is_del', 0], ['level', 1]];

        #需要搜索的字段
        $form_elems = [
            ['acc', 'like'],
            ['nickname', 'like']
        ];

        $con = $this->_condition_string($request, $form_elems, $con_arr);//将条件数组数据转换为条件字符串

        //将搜索的原始数据扔进模板
        $this->_datas['search'] = $this->_get_ori_search_datas($request, $form_elems);

        //分页参数
        $this->_datas['page'] = $page = $this->_page('user', $con, $request);

        //查询数据
        $this->_datas['rows'] = M()->table('user as u')->select('u.*, ug.name as gname')
        ->leftjoin('user_group as ug', 'ug.id=u.user_group__id')
        ->where($con)
        ->limit($page['limitM'] . ',' . $page['numPerPage'])
        ->get();

        //分配模板变量&渲染模板
        $this->assign($this->_datas);
        $this->display('user/index.tpl'); */
    }


    

}