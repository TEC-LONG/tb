<?php

Route::team(['prefix'=>'self'], function (){

    Route::team(['prefix'=>'manage'], function (){
    
        // Route::get('/user/list', 'UserController@userList')->name('userList')->midware('auth:check')->midware('auth:func1');
        Route::get('/user/list', 'UserController@userList')->name('userList');
        Route::get('/user/add', 'UserController@userEdit')->midware('auth:check');
        Route::get('/user/upd', 'UserController@userEdit')->midware('auth:check');
        Route::post('/user/post', 'UserController@userPost')->midware('auth:check');
    });

});


Route::team(['prefix'=>'system'], function (){

    Route::team(['prefix'=>'manage'], function (){
    
        // Route::get('/user/list', 'UserController@userList')->name('userList')->midware('auth:check')->midware('auth:func1');
        Route::get('/user/list', 'UserController@userList')->name('userList');
    });

});

