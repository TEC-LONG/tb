<?php

/**
 * xxxx/xxxx/xx/tb/config/share/dir.path.php   => ROOT : xxx/xxx/xx/tb
 */
define('ROOT', dirname(dirname(dirname(__FILE__))));//定义根目录常量

/**
 * 一级目录
 */
define('APP',           ROOT.'/app');# APP_PATH => xxx/tb/app/
define('CORE',          ROOT.'/core');
define('PLUGS',         ROOT.'/plugs');
define('CONFIG',        ROOT.'/config');
define('PUB',           ROOT.'/public');
define('STORAGE',       ROOT.'/storage');

/**
 * /app
 */
define('APP_BLOG',  APP.'/blog');
define('APP_CMD',  APP.'/cmd');

define('APP_API',   APP.'/api');
define('APP_MODEL', APP.'/model');
define('APP_SELF',  APP.'/self');

/**
 * /config
 */
define('CONFIG_SHARE', CONFIG.'/share');

/**
 * /core
 */
define('CORE_FRAME',    CORE.'/frame');
define('CORE_FUN',      CORE.'/fun');
define('CORE_MIDWARE',  CORE.'/midware');

define('CORE_FRAME_SMARTY', CORE_FRAME.'/smarty');

/**
 * /plugs
 */
define('COMPOSER',  PLUGS.'/vendor');

/**
 * /public
 */
define('PUB_UPLOAD',  PUB.'/upload');

/**
 * /storage
 */
define('STORAGE_LOG',       STORAGE.'/log');
define('STORAGE_DW',        STORAGE.'/dw');
define('STORAGE_CACHE',     STORAGE.'/cache');

/**
 * /storage/log
 */
define('STORAGE_LOG_SHARE', STORAGE_LOG.'/share');



// define('SMARTY_DIR', PLUGINS_PATH.'smarty/');//定义SMARTY目录常量
// define('APP_MODEL_PATH', APP_PATH . 'model/');

// ## home
// define('APP_HOME_PATH', APP_PATH . 'home/');
// define('APP_HOME_VIEW_PATH', APP_HOME_PATH . 'view/');
// define('APP_HOME_CONTROLLER_PATH', APP_HOME_PATH . 'controller/');

// ## blog
// define('APP_BLOG_PATH', APP_PATH . 'blog/');
// define('APP_BLOG_VIEW_PATH', APP_BLOG_PATH . 'view/');
// define('APP_BLOG_CONTROLLER_PATH', APP_BLOG_PATH . 'controller/');

// ## admin
// define('APP_ADMIN_PATH', APP_PATH . 'admin/');//APP_ADMIN_PATH =>  xx/mvc/app/admin/
// define('APP_ADMIN_VIEW_PATH', APP_ADMIN_PATH . 'view/');//APP_ADMIN_VIEW_PATH  =>  xx/mvc/app/admin/view/
// define('APP_ADMIN_CONTROLLER_PATH', APP_ADMIN_PATH . 'controller/');//APP_ADMIN_CONTROLLER_PATH  =>  xx/mvc/app/admin/controller/

// ## tools
// define('TOOLS_PATH', APP_PATH . 'tools/');//  xx/mvc/app/tools/
// define('TOOLS_VIEW_PATH', TOOLS_PATH . 'view/');//  xx/mvc/app/tools/view/
// define('TOOLS_CONTROLLER_PATH', TOOLS_PATH . 'controller/');//  xx/mvc/app/tools/controller/
// define('TOOLS_CONF_PATH', CONFIG_PATH . 'tools/');// xx/mvc/config/tools/

// ##others
// define('EDITOR_IMG', PUBLIC_PATH . 'tools/editorimg/' );
// define('EDITORMD_IMG', UPLOAD_PATH . 'editormdimg/' );
// define('XHEDITOR_IMG', UPLOAD_PATH . 'xheditorimg/' );
// define('USER_IMG', UPLOAD_PATH . 'userimg/' );
