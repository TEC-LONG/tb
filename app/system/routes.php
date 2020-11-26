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
            Route::get('/post', 'PermissionController@post')->navtab('system_manage_permissionEdit');# 新增/编辑 权限功能
            Route::get('/menu', 'PermissionController@menu');# 
        });

        Route::team(['prefix'=>'menu'], function (){
        
            Route::get('/list', 'MenuController@list')->navtab('system_manage_menuList');# （左侧）菜单管理
            Route::post('/child', 'MenuController@menuChild');# 获取指定菜单的子菜单
            Route::post('/add', 'MenuController@add')->navtab('system_manage_menuList');# 新增菜单功能
            Route::post('/upd', 'MenuController@upd')->navtab('system_manage_menuList');# 编辑菜单功能
        });
    });
});
