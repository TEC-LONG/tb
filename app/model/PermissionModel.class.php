<?php
namespace model;
use \TB;

class PermissionModel extends TB{

    protected $table = 'permission';
    protected $alias = 'p';

    /**
     * 说明：
     * 1. PLAT为平台菜单  无路由
     * 2. M-LV2为平台下的二级菜单  无路由
     * 3. 三级菜单以下都是有路由的，所以可以归为某个具体的功能属性flag
     */
    const C_FLAG = ['PLAT', 'M-LV2', 'LIST', 'ADD', 'UPD', 'DEL', 'SEARCH', 'TOURIST', 'M-LV3'];
}