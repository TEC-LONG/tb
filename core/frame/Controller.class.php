<?php

class Controller extends \Smarty{

    /**
     * 用于保存system后台登录者用户信息
     */
    public static $manager=null;

    /**
     * 构造方法
     */
    public function __construct(){ 

        parent::__construct();
        $path = APP . '/' . Route::$plat . '/' . Route::$way . '/';

        if( !empty(Config::C('SMARTY.DELIMITER.LEFT')) ){
        
            $this->left_delimiter = Config::C('SMARTY.DELIMITER.LEFT');
        }

        if( !empty(Config::C('SMARTY.DELIMITER.RIGHT')) ){
        
            $this->right_delimiter = Config::C('SMARTY.DELIMITER.RIGHT');
        }

        $this->setTemplateDir($path . Config::C('SMARTY.TEMPLATE_DIR_NAME'));
        $this->setCompileDir($path . Config::C('SMARTY.TEMPLATE_CACHE_DIR_NAME'));

        /// 启动session
        @session_start();
    }

}