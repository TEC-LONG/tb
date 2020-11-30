<?php

Route::team(['prefix'=>'system'], function (){
    
    Route::team(['prefix'=>'manage'], function (){

        // Route::get('/index', 'IndexController@index')->midware('auth:check');# 后台首页
        Route::get('/index', 'IndexController@index');# 后台首页
    
        Route::team(['prefix'=>'login'], function (){
        
            Route::get('/index', 'LoginController@index');# 后台登录页
            Route::post('/check', 'LoginController@check');# 后台登录验证
        });

        Route::team(['prefix'=>'user'], function (){
        
            Route::get('/list', 'UserController@index');# 用户列表
            Route::post('/edit', 'UserController@edit');# 添加/编辑用户
            Route::get('/group', 'UserController@group');# 用户组管理列表
        });

        Route::team(['prefix'=>'permission'], function (){
        
            Route::get('/list', 'PermissionController@list')->navtab('system_manage_permissionList');# 权限管理列表
            Route::get('/edit', 'PermissionController@edit')->navtab('system_manage_permissionEdit');# 新增/编辑 权限页面
            Route::post('/post', 'PermissionController@post')->navtab('system_manage_permissionList');# 新增/编辑 权限功能
            Route::post('/del', 'PermissionController@del')->navtab('system_manage_permissionList');# 删除 权限功能

            Route::post('/group', 'PermissionController@group')->navtab('system_manage_permissionGroup');#

            Route::get('/menu', 'PermissionController@menu')->navtab('system_manage_permissionMenu');# 菜单权限管理列表
            Route::get('/menu/edit', 'PermissionController@menuEdit')->navtab('system_manage_permissionMenuEdit');# 新增/编辑 权限菜单页面
            Route::get('/menu/post', 'PermissionController@menuPost')->navtab('system_manage_permissionMenu');# 新增/编辑 权限菜单功能
            Route::get('/menu/del', 'PermissionController@menuDel')->navtab('system_manage_permissionMenu');# 删除 权限菜单功能
        });

        Route::team(['prefix'=>'menu'], function (){
        
            Route::get('/list', 'MenuController@list')->navtab('system_manage_menuList');# （左侧）菜单管理
            Route::post('/child', 'MenuController@menuChild');# 获取指定菜单的子菜单
            Route::post('/add', 'MenuController@add')->navtab('system_manage_menuList');# 新增菜单功能
            Route::post('/upd', 'MenuController@upd')->navtab('system_manage_menuList');# 编辑菜单功能
        });
    });
});
