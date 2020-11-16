<?php

Route::team(['prefix'=>'system'], function (){
    
    Route::team(['prefix'=>'manage'], function (){
    
        Route::team(['prefix'=>'login'], function (){
        
            Route::get('/index', 'LoginController@index');# 后台登录页
            Route::post('/check', 'LoginController@check');# 后台登录验证
        });

        // Route::get('/index', 'IndexController@index')->midware('auth:check');# 后台首页
        Route::get('/index', 'IndexController@index');# 后台首页

        Route::team(['prefix'=>'user'], function (){
        
            Route::get('/list', 'UserController@index');# 用户列表
            Route::post('/edit', 'UserController@edit')->midware('auth:check');# 添加/编辑用户
        });
    });
});
