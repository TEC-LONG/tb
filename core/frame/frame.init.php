<?php

/// 引入App应用类
include CORE_FRAME . '/App.class.php';

App::includes(['Config', 'Log', 'Err', 'Route']);

/// 构建全局配置数据
Config::builtGlobalConfigs();

/// 取得 平台 和 线路 参数
Route::getPlatAndWay();

/// 引入 平台 和 线路 中定义的常量
Config::builtConstants(Route::$plat, Route::$way);

/// 根据配置文件构建配置数据
Config::builtConfigs(Route::$plat, Route::$way);

/// debug模式
App::debug();

/// 执行路由预处理程序
Route::prepare();

/// 引入composer自动加载文件
App::includes(['Composer']);

App::includes(['Smarty']);
spl_autoload_register('App::autoload');

/// 父控制器
App::includes(['Controller']);
