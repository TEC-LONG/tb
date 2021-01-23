<?php

Route::team(['prefix'=>'system'], function (){
    
    Route::team(['prefix'=>'manage'], function (){

        // Route::get('/index', 'IndexController@index')->midware('auth:check');# 后台首页
        Route::get('/index', 'IndexController@index')->navtab('system_manage_index');# 后台首页
    
        Route::team(['prefix'=>'login'], function (){
        
            Route::get('/index', 'LoginController@index');# 后台登录页
            Route::post('/check', 'LoginController@check');# 后台登录验证
        });

        Route::team(['prefix'=>'user'], function (){
        
            Route::get('/list', 'UserController@index');# 用户列表
            Route::post('/edit', 'UserController@edit');# 添加/编辑用户

            Route::get('/group', 'UserController@group')->navtab('system_manage_userGroup');# 用户组管理列表
            Route::get('/group/edit', 'UserController@groupEdit')->navtab('system_manage_userGroupEdit');# 新增/编辑 用户组
            Route::post('/group/post', 'UserController@groupPost')->navtab('system_manage_userGroup');# 新增/编辑 用户组功能
            Route::get('/group/permission', 'PermissionController@groupPermission')->navtab('system_manage_userGroupPermission');# 设置用户组权限页面
            Route::post('/group/permission/post', 'PermissionController@groupPermissionPost')->navtab('system_manage_userGroup');# 设置用户组权限功能
            
        });

        Route::team(['prefix'=>'permission'], function (){
        
            Route::get('/list', 'PermissionController@list')->navtab('system_manage_permissionList');# 权限管理列表
            Route::get('/edit', 'PermissionController@edit')->navtab('system_manage_permissionEdit');# 新增/编辑 权限页面
            Route::post('/post', 'PermissionController@post')->navtab('system_manage_permissionList');# 新增/编辑 权限功能
            Route::get('/del', 'PermissionController@del')->navtab('system_manage_permissionList');# 删除 权限功能

            Route::get('/menu', 'PermissionController@menu')->navtab('system_manage_permissionMenu');# 菜单权限管理列表
            Route::get('/menu/edit', 'PermissionController@menuEdit')->navtab('system_manage_permissionMenuEdit');# 新增/编辑 权限菜单页面
            Route::post('/menu/post', 'PermissionController@menuPost')->navtab('system_manage_permissionMenu');# 新增/编辑 权限菜单功能
            Route::get('/menu/del', 'PermissionController@menuDel')->navtab('system_manage_permissionMenu');# 删除 权限菜单功能
        });

        Route::team(['prefix'=>'menu'], function (){
        
            Route::get('/list', 'MenuController@list')->navtab('system_manage_menuList');# （左侧）菜单管理
            Route::post('/child', 'MenuController@menuChild');# 获取指定菜单的子菜单
            Route::post('/add', 'MenuController@add')->navtab('system_manage_menuList');# 新增菜单功能
            Route::post('/upd', 'MenuController@upd')->navtab('system_manage_menuList');# 编辑菜单功能
        });

        Route::team(['prefix'=>'editor'], function (){
        
            Route::post('/md/img/up', 'EditorController@mdImgUp');# 适配 editorMD 图片上传 的功能
        });

        Route::team(['prefix'=>'doc'], function (){
            
            Route::get('/list', 'DocController@list')->navtab('system_manage_docList');# 文档管理 页面
            Route::get('/edit', 'DocController@edit')->navtab('system_manage_docEdit');# 添加/编辑 文档 页面
            Route::post('/post', 'DocController@post')->navtab('system_manage_docList');# 添加/编辑 文档 功能
            
            Route::request('/mulu/list', 'DocController@muluList')->navtab('system_manage_docMuluList');# 具体文档目录管理 页面
            Route::get('/mulu/lookup', 'DocController@lookup')->navtab('system_manage_docMuluLookup');# 目录项 查找带回 页面
            Route::get('/mulu/edit', 'DocController@muluEdit')->navtab('system_manage_docMuluEdit');# 新增/编辑 目录项 页面
            Route::post('/mulu/post', 'DocController@muluPost')->navtab('system_manage_docMuluList');# 新增/编辑 目录项 功能
            Route::get('/mulu/edit/content', 'DocController@muluEditContent')->navtab('system_manage_docMuluEditContent');# 编辑 文档内容 页面
            Route::post('/mulu/edit/content/post', 'DocController@muluEditContentPost');# 编辑 文档内容 功能

            Route::get('/info', 'DocController@info')->navtab('system_manage_docInfo');# 文档 页面
            Route::get('/info/content', 'DocController@docDetailContent')->navtab('system_manage_docInfoContent');# 文档内容navtab 页面
            
        });

        Route::team(['prefix'=>'gupiao'], function (){
            
            Route::get('/search/by/code/or/name', 'GupiaoController@searchByCodeOrName')->navtab('system_manage_gupiaoSearchByCodeOrName');# 根据 股票代码 或 股票名称 模糊搜索股票
            
            
        });
    });
});
