<?php
namespace model;
use \BaseModel;
use \Fun;

class UserGroupPermissionModel extends BaseModel{

    protected $table = 'tl_user_group_permission';

    /**
     * 获取指定组的菜单权限id
     */
    public function getMenuPermissionIds($user_group__id){
    
        $user_menu = $this->select('menu_permission__id')->where(['user_group__id', $user_group__id])->get();

        $mp_ids = [];
        foreach( $user_menu as $v){
            $mp_ids[] = $v['menu_permission__id'];
        }

        return $mp_ids;
    }
}