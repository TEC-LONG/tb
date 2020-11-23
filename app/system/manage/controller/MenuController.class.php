<?php

namespace system\manage\controller;
use \system\manage\service\MenuPermissionService;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;

class MenuController extends Controller {

    /**
     * 左侧菜单管理
     */
    public function list(){

        /// 初始化参数
        $menu_permission_service = new MenuPermissionService;

        /// 获取左侧菜单数据
        $info = $menu_permission_service->getLeftMenu();

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->display('menu/index.tpl');
    }

}