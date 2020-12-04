<?php

namespace system\manage\service;
use \Fun;

class IndexService {
    
    /**
     * 获取收藏网站
     */
    public function getNavLink(){

        return [# 最多八个大数组，每个大数组中最多12个元素
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
    }

}