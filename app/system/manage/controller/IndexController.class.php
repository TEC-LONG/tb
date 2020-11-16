<?php

namespace system\manage\controller;
use \model\PermissionModel;
use \model\UserModel;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;

class IndexController extends Controller {

    protected $_data;

    public function index(){

        /// 查询三级以内所有菜单数据
        $this->_data['menu1'] = TB::table('menu_permission as mp')->select('mp.display_name, mp.id, mp.parent_id')
        ->leftjoin('permission as p', 'mp.permission__id=p.id')
        ->where(['p.flag', array_search('PLAT', PermissionModel::C_FLAG)])->orderby('mp.sort desc')->get();

        echo '<pre>';
        print_r($this->_data['menu1']);
        

        $this->_data['menu2'] = TB::table('menu_permission as mp')->select('mp.display_name, mp.id, mp.parent_id')
        ->leftjoin('permission as p', 'mp.permission__id=p.id')
        ->where(['p.flag', array_search('M-LV2', PermissionModel::C_FLAG)])->orderby('mp.sort desc')->get();

        $this->_data['menu3'] = TB::table('menu_permission as mp')->select('mp.id, mp.display_name, mp.parent_id, mp.route, mp.navtab
        , mp.level3_type, mp.level3_href')
        ->leftjoin('permission as p', 'mp.permission__id=p.id')
        ->where(['p.flag', array_search('M-LV3', PermissionModel::C_FLAG)])->orderby('mp.sort desc')->get();

        /// 查询当前用户所具有的权限菜单
        $user_group__id = $_SESSION['admin']['user_group__id'];
        $user_menu = TB::table('user_group_permission')->select('menu_permission__id')->where(['user_group__id', $user_group__id])->get();
        $this->_data['mp_ids'] = [];
        foreach( $user_menu as $v){
            $this->_data['mp_ids'][] = $v['menu_permission__id'];
        }

        print_r($this->_data['mp_ids']);
        exit;
        

        /// 收藏网站
        $this->_data['nav_link'] = [# 最多八个大数组，每个大数组中最多12个元素
            [
                '百度统计' => 'http://tongji.baidu.com/web/welcome/login',
                '百度站长平台' => 'http://zhanzhang.baidu.com',
                '百度移动统计' => 'https://mtj.baidu.com/web/welcome/login',
                'just-my-socks' => 'https://justmysocks1.net/members/clientarea.php?action=productdetails&id=107355',
                'bootstrap4' => 'https://code.z01.com/v4/',
                'editor.md' => 'http://editor.md.ipandao.com/',
                'png转ico' => 'https://www.easyicon.net/covert/',
                'php在线手册' => 'https://www.php.net/manual/zh/function.base64-encode.php'
            ],
            [
                '百度网盘' => 'http://pan.baidu.com',
                'jq22官网' => 'https://www.jq22.com',
                'editplus插件' => 'https://www.editplus.com/files.html',
                'vscode-extension官网' => 'https://marketplace.visualstudio.com/VSCode',
                'composer包下载' => 'https://packagist.org/',
                '51前端' => 'https://www.51qianduan.com/'
            ],
            [
                '慕课网' => 'https://www.imooc.com/',
                'runoob菜鸟' => 'https://www.runoob.com/',
                '树莓派' => 'https://shumeipai.nxez.com/',
                '[x in y minutes]' => 'https://learnxinyminutes.com/'
            ],
            [
                '博客园' => 'https://www.cnblogs.com/',
            ],
            [
                'prismjs' => 'https://prismjs.com/',
                'bootstrap中文' => 'https://code.z01.com/v4/components/media-object.html',
            ]
        ];

        /// 分配模板变量  &  渲染模板
        $this->_data['manager'] = self::$manager;
        $this->assign($this->_data);
        $this->display('index.tpl');
    }

}