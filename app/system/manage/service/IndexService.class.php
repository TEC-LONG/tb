<?php

namespace system\manage\service;
use \Fun;
use model\IntervalsModel;
use model\MaPianyilvStatisticsModel;
use model\MixedStatisticsModel;
use model\SdbStatisticsMovingAverageModel;
use model\SdbStatisticsMovingAveragePianyilvModel;
use model\SharesModel;
use model\StatisticsRulesModel;

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

    /**
     * 获取均线相关统计数据
     */
    public function getMaStatistics(){

        /// 初始化参数
        $re = [];
        $mixed_statistics_model = new MixedStatisticsModel;

        /// 均线偏移率极值
        # 近10年极值
        $re['_10years_pianyilv'] = $mixed_statistics_model->getContent(1);

        # 近5年极值
        $re['_5years_pianyilv'] = $mixed_statistics_model->getContent(2);

        # 近3年极值
        $re['_3years_pianyilv'] = $mixed_statistics_model->getContent(3);

        /// 返回
        return $re;
    }

    /**
     * 均偏率
     */
    public function junPianLv(&$chicang_group, $max_active_date_timestamp){
    
        if( empty($chicang_group) ) return false;

        /// 初始化参数
        $shares_ids                                 = array_column($chicang_group, 'id');
        $statistics_moving_average_pianyilv_model   = new SdbStatisticsMovingAveragePianyilvModel;

        /// 查询均偏率
        $all_pianyilv_info = $statistics_moving_average_pianyilv_model->select([
            'shares__id',
            'ma5_plv',
            'ma20_plv',
            'ma60_plv',
            'ma240_plv',
        ])->where([
            ['shares__id', 'in', '('.implode(',', $shares_ids).')'],
            ['active_date_timestamp', $max_active_date_timestamp]
        ])->get();

        if( empty($all_pianyilv_info) ) return false;

        # 以shares__id作为key
        $all_pianyilv_info = array_column($all_pianyilv_info, null, 'shares__id');

        /// 加入 均偏率 数据
        foreach( $chicang_group as &$row){
        
            $_shares__id = $row['id'];
            if( isset($all_pianyilv_info[$_shares__id]) ){
                
                $row['ma5_plv']     = $all_pianyilv_info[$_shares__id]['ma5_plv'];
                $row['ma20_plv']    = $all_pianyilv_info[$_shares__id]['ma20_plv'];
                $row['ma60_plv']    = $all_pianyilv_info[$_shares__id]['ma60_plv'];
                $row['ma240_plv']   = $all_pianyilv_info[$_shares__id]['ma240_plv'];
            }
        }

        return true;
    }

    /**
     * 均线角
     */
    public function junXianJiao(&$chicang_group, $max_active_date_timestamp){
    
        if( empty($chicang_group) ) return false;

        /// 初始化参数
        $shares_ids                         = array_column($chicang_group, 'id');
        $statistics_moving_average_model    = new SdbStatisticsMovingAverageModel;

        /// 查询均线角
        $all_angle_info = $statistics_moving_average_model->select([
            'shares__id',
            'ma5_angle',
            'ma20_angle',
            'ma60_angle',
            'ma240_angle',
        ])->where([
            ['shares__id', 'in', '('.implode(',', $shares_ids).')'],
            ['active_date_timestamp', $max_active_date_timestamp]
        ])->get();

        if( empty($all_angle_info) ) return false;

        # 以shares__id作为key
        $all_angle_info = array_column($all_angle_info, null, 'shares__id');

        /// 加入 均线角 数据
        foreach( $chicang_group as &$row){
        
            $_shares__id = $row['id'];
            if( isset($all_angle_info[$_shares__id]) ){
                
                $row['ma5_angle']   = round($all_angle_info[$_shares__id]['ma5_angle'], 4);
                $row['ma20_angle']  = round($all_angle_info[$_shares__id]['ma20_angle'], 4);
                $row['ma60_angle']  = round($all_angle_info[$_shares__id]['ma60_angle'], 4);
                $row['ma240_angle'] = round($all_angle_info[$_shares__id]['ma240_angle'], 4);
            }
        }

        return true;
    }

    /**
     * 获取股票的复现率
     * @param   $period     array   周期列表
     */
    public function fuxianlv(&$chicang_group, $period=[5, 20, 60, 240]){
    
        /// 初始化参数
        $intervals_model                = new IntervalsModel;
        $statistics_rules_model         = new StatisticsRulesModel;
        $ma_pianyilv_statistics_model   = new MaPianyilvStatisticsModel;

        /// 遍历周期
        foreach( $period as $_perio){
        
            # 获取规则id
            $statistics_rules__id = $statistics_rules_model->period2rulesId($_perio);
            
            # 遍历股票
            foreach( $chicang_group as &$row){
            
                ## 初始化参数
                $ma_plv_name        = 'ma' . $_perio . '_plv';### 如: ma5_plv
                $this_ma_plv        = $row[$ma_plv_name];
                $this_shares__id    = $row['id'];

                ## 区间对应的区间表id
                $this_intervals__id = $intervals_model->pianyilv2IntervalsId($this_ma_plv, $statistics_rules__id);

                if( !$this_intervals__id ) continue;

                ## 查询当前股票的复现率数据
                $_arr = $ma_pianyilv_statistics_model->where([
                    ['shares__id', $this_shares__id],
                    ['intervals__id', $this_intervals__id]
                ])->find();

                // if( $this_shares__id==3612 ){
                //     var_dump($ma_pianyilv_statistics_model->dbug());
                // }

                if( empty($_arr) ) continue;

                ### 计算复现率
                $_up_fuxianlv               = round(($_arr['next_day_up_num']/$row['total_day_num'])*100, 2);
                $row[$ma_plv_name.'_up']    = $_up_fuxianlv;
                $_dw_fuxianlv               = round(($_arr['next_day_dw_num']/$row['total_day_num'])*100, 2);
                $row[$ma_plv_name.'_dw']    = $_dw_fuxianlv;

                // if( $this_shares__id==3612 ){
                //     echo '<pre>';
                //     var_dump($row);
                //     echo '<pre>';
                // }
            }
            // exit;
        }
    }


}