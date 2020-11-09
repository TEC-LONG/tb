<?php
namespace model;
use \TB;

class UserModel extends TB{

    protected $table = 'user';

    const C_LEVEL       = ['普通用户',  '管理员'];
    const C_STATUS      = ['正常',      '禁用'];
    const C_ORI         = ['注册',      '后台添加'];
    const C_IS_ONLINE   = ['未知',      '在线',     '离线'];
    const C_USER_TYPE   = ['无',        '供应商'];

    public function make_pwd($pwd, $salt=''){
    
        if($salt==='') $salt = $this->make_salt();
        return md5($salt . md5($pwd) . $salt);
    }

    public function make_salt(){

        if( $this->_salt==='' ){
            // $this->_salt = F()->randStr();
        }

        return $this->_salt;
    }

}