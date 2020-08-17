<?php

///  admin/

    #用户管理
    Route::get('user/index', 'User@index')->navtab('admin_user_index')->name();
    Route::get('user/add', 'User@showEdit')->navtab('admin_user_add')->name('admin_user_index');
    Route::get('user/upd', 'User@showEdit')->navtab('admin_user_upd')->name('admin_user_index');
    Route::get('user/delete', 'User@del')->navtab('admin_user_index')->name('admin_user_index');
    Route::post('user/post', 'User@post')->name('admin_user_index');

