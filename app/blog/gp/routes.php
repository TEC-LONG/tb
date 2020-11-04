<?php

Route::team(['prefix'=>'gp'], function (){

    Route::get('/everyday/xgAndxd', 'StatisticsPageController@xgAndxd');
});