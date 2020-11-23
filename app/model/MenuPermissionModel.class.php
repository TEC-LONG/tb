<?php
namespace model;
use \BaseModel;

class MenuPermissionModel extends BaseModel{

    protected $table = 'menu_permission';

    const C_REQUEST = ['无', 'GET', 'POST', 'REQUEST'];
    const C_LEVEL3_TYPE = ['内部跳转链接', '外部跳转链接', '无'];
}