<?php

Route::team(['prefix'=>'system'], function (){
    
    Route::team(['prefix'=>'manage'], function (){
    
        Route::team(['prefix'=>'login'], function (){
        
            Route::get('/index', 'LoginController@index');
        });
    });
});
