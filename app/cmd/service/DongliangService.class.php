<?php

namespace cmd\service;
use \Err;
use \Fun;
use \cmd\service\GupiaoCommonService;

class DongliangService
{
    /**
     * 计算动量
     */
    public function shineng(){

        // $str = '2.223470106e+12';
        // $str = '222347010.6';
        // preg_match_all('/^.*e\+.*$/', $str, $matches);
        // $matches = preg_split('/e\+/', $str);
        // print_r($matches);
        // var_dump(pow(10, $matches[1]));
        // $b_time     = strtotime('2020-10-1 12:10:01');
        // $b_time_1   = strtotime(date('Y-m-d', $b_time).' 0:0:0');
        // var_dump(date('Y-m-d H:i:s', $b_time_1));
        // var_dump(date('Y-m-d H:i:s', strtotime(date('Y-m-d', $b_time_1+86400).' 0:0:0')));
        // exit;
        

        /// 统计二类板块个股权重
        # 获取二类板块数据
        $plates = TB::table('plate')->select('id, name')->where([
            ['come_from', 4],# 4=同花顺1
            ['type', 1],# 1=行业板块
            ['pid', '<>', 0],
            ['is_deprecated', 0]
        ])->get();

        foreach( $plates as $k=>$v){

            // echo $v['name'] . PHP_EOL;
        
            $plate__id = $v['id'];
            # 获取当前板块下的所有股票
            $rocks = TB::table('tl_shares__plate sp')->select('sp.shares__id')
            ->leftjoin('tl_shares s', 's.id=sp.shares__id')
            ->where([
                ['sp.plate__id', $plate__id],
                ['s.is_deprecated', 0]
            ])->get();

            if( empty($rocks) ) continue;
            $rocks_ids = implode(',', array_column($rocks, 'shares__id'));

            # 获取板块所有股票中最早、最晚形成数据的时间
            $b_time = TB::table('daily_weight_ths1')->select('active_date_timestamp')->where(['shares__id', 'in', '('.$rocks_ids.')'])->orderby('active_date_timestamp')->find();
            $e_time = TB::table('daily_weight_ths1')->select('active_date_timestamp')->where(['shares__id', 'in', '('.$rocks_ids.')'])->orderby('active_date_timestamp desc')->find();

            if( empty($b_time) ) continue;

            $b_time     = $b_time['active_date_timestamp'];
            $e_time     = $e_time['active_date_timestamp'];
            $b_time_1   = strtotime(date('Y-m-d', $b_time).' 0:0:0');
            $b_time_2   = strtotime(date('Y-m-d', $b_time).' 23:59:59');
            $e_time_1   = strtotime(date('Y-m-d', $e_time).' 0:0:0');

            // var_dump(date('Y-m-d H:i:s', $b_time_1));
            // var_dump(date('Y-m-d H:i:s', $e_time_1));
            // exit;

            # 逐天取出板块数据统计
            while ($b_time_1<=$e_time_1) {
                
                ## 当天股票数据
                $rocks_this_day_info = TB::table('daily_weight_ths1')->select('
                    id,
                    shares__id,
                    total_shizhi
                ')->where([
                    ['shares__id', 'in', '('.$rocks_ids.')'],
                    ['active_date_timestamp', '>=', $b_time_1],
                    ['active_date_timestamp', '<=', $b_time_2]
                ])->get();

                if( empty($rocks_this_day_info) ){
                    
                    $b_time_1 = strtotime(date('Y-m-d', $b_time_1+86400).' 0:0:0');
                    $b_time_2 = strtotime(date('Y-m-d', $b_time_1).' 23:59:59');
                    continue;
                }

                ## 计算总市值
                $all_total_shizhi = array_column($rocks_this_day_info, 'total_shizhi');
                foreach( $all_total_shizhi as &$this_total_shizhi){
                
                    if( empty($this_total_shizhi) ) continue;

                    $matches = preg_split('/e\+/', $this_total_shizhi);
                    if( count($this_total_shizhi)==2 ){### 需处理指数
                    
                        $this_total_shizhi = $matches[0] * pow(10, $matches[1]);
                    }
                }
                $all_total_shizhi = intval(array_sum($all_total_shizhi) * 1000);### 统一1000倍转为整型
                if( empty($all_total_shizhi) ){
                    $b_time_1 = strtotime(date('Y-m-d', $b_time_1+86400).' 0:0:0');
                    $b_time_2 = strtotime(date('Y-m-d', $b_time_1).' 23:59:59');
                    continue;
                }

                ## 统计个股市值权重
                foreach( $rocks_this_day_info as $rock){

                    if( empty($rock['total_shizhi']) ) continue;
                
                    $this_rock_shizhi = intval($rock['total_shizhi']*1000);### 统一1000倍转为整型
                    $this_rock_weight = intval(number_format($this_rock_shizhi/$all_total_shizhi, 4)*10000);

                    /* var_dump($this_rock_weight);
                    if( $this_rock_weight==0 ){
                    
                        echo $this_rock_weight . ':' . $rock['id'] . ':' . $rock['shares__id'] . ':' . $rock['total_shizhi'] . PHP_EOL;
                    } */
                }
                // echo '--------------' . PHP_EOL;

                ## 时间递进
                $b_time_1 = strtotime(date('Y-m-d', $b_time_1+86400).' 0:0:0');
                $b_time_2 = strtotime(date('Y-m-d', $b_time_1).' 23:59:59');
            }

            exit;
        }

        /* /// 获取一类板块数据
        $rocks = TB::table('plate')->select('id')->where([
            ['come_from', 4],# 4=同花顺1
            ['type', 1],# 1=行业板块
            ['pid', 0],
            ['is_deprecated', 0]
        ])->get();

        $this->outputPercent($rocks);
        foreach( $rocks as $k=>$v){
            
            /// 获取当前股票历史数据
            $this_share_details_byday = TB::table('daily_weight_ths1')->select('
                id,
                
            ')
            ->where(['shares__id', $v['id']])
            ->orderby('active_date_timestamp asc')
            ->get();
            
            /// 每日个股在板块中的权重
            



            $this->outputPercent('output');
        } */
    }

    /**
     * 输出百分比公共方法
     */
    protected function outputPercent($countData, $type='init'){
    
        if( $type=='init' ){
        
            $this->dividend   = 1;
            $this->divisor    = count($countData);
            $this->percent    = number_format(($this->dividend/$this->divisor)*100, 4) . '%';
        }elseif( $type=='output' ){

            echo  '完成：'. $this->percent . PHP_EOL;
            $this->dividend++;
            $this->percent = number_format(($this->dividend/$this->divisor)*100, 4) . '%';
        }
    }
}
