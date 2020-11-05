<?php

Route::team(['prefix'=>'blog'], function (){
    
    Route::team(['prefix'=>'gp'], function (){
    
        Route::get('/everyday/xgAndxd', 'StatisticsPageController@xgAndxd');
    });
});
