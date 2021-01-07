<?php

namespace system\manage\controller;
use \controller;
use \Json;
use \Err;
use \Fun;
use model\MenuPermissionModel;
use model\UserGroupPermissionModel;
use system\manage\service\IndexService;

class IndexController extends Controller {


    public function index(){

        /// 初始化参数
        $index_service                  = new IndexService;
        $menu_permission_model          = new MenuPermissionModel('mp');
        $user_group_permission_model    = new UserGroupPermissionModel;

        /// 查询三级以内所有菜单数据
        $menu1 = $menu_permission_model->menu1();
        $menu2 = $menu_permission_model->menu2();
        $menu3 = $menu_permission_model->menu3();

        /// 查询当前用户所具有的权限菜单
        $user_group__id = $_SESSION['manager']['user_group__id'];
        // $user_group__id = self::$manager['user_group__id'];
        $mp_ids         = $user_group_permission_model->getMenuPermissionIds($user_group__id);

        /// 收藏网站
        $nav_link = $index_service->getNavLink();

        /// 偏移率相关统计数据
        $pianyilv = $index_service->getMaStatistics();

        /// 分配模板变量  &  渲染模板
        $this->assign('menu1', $menu1);
        $this->assign('menu2', $menu2);
        $this->assign('menu3', $menu3);
        $this->assign('mp_ids', $mp_ids);
        $this->assign('nav_link', $nav_link);
        $this->assign('manager', self::$manager);
        $this->assign('_10years_pianyilv', $pianyilv['_10years_pianyilv']);
        $this->assign('_5years_pianyilv', $pianyilv['_5years_pianyilv']);
        $this->assign('_3years_pianyilv', $pianyilv['_3years_pianyilv']);
        $this->display('index.tpl');
    }

}