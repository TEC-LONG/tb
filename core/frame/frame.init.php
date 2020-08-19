<?php

include CORE_FRAME . '/Config.class.php';
include CORE_FRAME . '/Log.class.php';
include CORE_FRAME . '/Err.class.php';
include CORE_FRAME . '/Route.class.php';

/// 构建全局配置数据
Config::builtGlobalConfigs();

/// 取得 平台 和 线路 参数
Route::getPlatAndWay();

/// 引入 平台 和 线路 中定义的常量
Config::builtConstants(Route::$plat, Route::$way);

/// 根据配置文件构建配置数据
Config::builtConfigs(Route::$plat, Route::$way);

/// 执行路由预处理程序
Route::prepare();

/// 引入App应用类
include CORE_FRAME . '/App.class.php';
spl_autoload_register('App::autoload');