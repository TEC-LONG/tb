<?php

/// 引入App应用类
include CORE_FRAME . '/App.class.php';

App::includes(['Config', 'Log', 'Err']);

/// 构建全局配置数据
Config::builtGlobalConfigs();

/// debug模式
App::debug();

/// 自动加载
spl_autoload_register('App::autoload');