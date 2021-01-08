<?php

namespace cmd\service;
use \Err;
use \Fun;
use model\MixedStatisticsModel;
use model\SdbStatisticsMovingAverageModel;
use model\SdbStatisticsMovingAveragePianyilvModel;
use model\SharesDetailsBydayModel;
use model\SharesModel;
use \TB;

class MovingAverageService
{
    /**
     * 计算所有均价
     */
    public function maPrice(){

        /// 初始化参数
        $now                            = time();
        $shares_model                   = new SharesModel;
        $shares_details_byday_model     = new SharesDetailsBydayModel('sdb');
        $sdb_statistics_moving_average  = new SdbStatisticsMovingAverageModel;
    
        /// 获取shares所有id
        $ids = $shares_model->select('id')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($ids);
        foreach( $ids as $v){
            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            /// 获取当前票的所有记录
            $this_shares_details_byday_row = $shares_details_byday_model->select('
                sdb.id,
                sdb.shares__id,
                sdb.active_date,
                sdb.active_date_timestamp,
                sma.id as sma_id,
                sdb.day_end_price,
                sma.ma5_price,
                sma.ma10_price,
                sma.ma15_price,
                sma.ma20_price,
                sma.ma30_price,
                sma.ma60_price,
                sma.ma120_price,
                sma.ma240_price
            ')
            ->leftjoin('tl_sdb_statistics_moving_average as sma', 'sma.shares_details_byday__id=sdb.id')
            ->where(['sdb.shares__id', $v['id']])
            ->orderby('active_date_timestamp desc')
            ->get();

            if( empty($this_shares_details_byday_row) ) continue;

            $last_price = [
                'price' => 0
            ];
            foreach( $this_shares_details_byday_row as $k=>$sdbr_v){
            
                $ma = [];

                /// 不存在则新增
                if( empty($sdbr_v['sma_id']) ){

                    $flag = 1;
                    
                    $ma5_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 5, $last_price);
                    $ma['ma5_price'] = $ma5_price;

                    $ma10_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 10, $last_price, $ma5_price);
                    $ma['ma10_price'] = $ma10_price;

                    $ma15_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 15, $last_price, $ma10_price);
                    $ma['ma15_price'] = $ma15_price;

                    $ma20_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 20, $last_price, $ma15_price);
                    $ma['ma20_price'] = $ma20_price;

                    $ma30_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 30, $last_price, $ma20_price);
                    $ma['ma30_price'] = $ma30_price;

                    $ma60_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 60, $last_price, $ma30_price);
                    $ma['ma60_price'] = $ma60_price;

                    $ma120_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 120, $last_price, $ma60_price);
                    $ma['ma120_price'] = $ma120_price;

                    $ma240_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 240, $last_price, $ma120_price);
                    $ma['ma240_price'] = $ma240_price;

                    $ma['shares__id']               = $sdbr_v['shares__id'];
                    $ma['shares_details_byday__id'] = $sdbr_v['id'];
                    $ma['active_date']              = $sdbr_v['active_date'];
                    $ma['active_date_timestamp']    = $sdbr_v['active_date_timestamp'];
                    $ma['ma_price_time']            = $now;
                    $ma['created_time']             = $now;

                }else{/// 存在则更新

                    $flag = 2;

                    if( empty($sdbr_v['ma5_price']) ){
                        $ma5_price          = $this->getAveragePrice($k, $this_shares_details_byday_row, 5, $last_price);
                        $ma['ma5_price']    = $ma5_price;
                    }else{
                        $ma5_price = $sdbr_v['ma5_price'];
                    }

                    if( empty($sdbr_v['ma10_price']) ){
                        $ma10_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 10, $last_price, $ma5_price);
                        $ma['ma10_price']   = $ma10_price;
                    }else{
                        $ma10_price = $sdbr_v['ma10_price'];
                    }

                    if( empty($sdbr_v['ma15_price']) ){
                        $ma15_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 15, $last_price, $ma10_price);
                        $ma['ma15_price']   = $ma15_price;
                    }else{
                        $ma15_price = $sdbr_v['ma15_price'];
                    }

                    if( empty($sdbr_v['ma20_price']) ){
                        $ma20_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 20, $last_price, $ma15_price);
                        $ma['ma20_price']   = $ma20_price;
                    }else{
                        $ma20_price = $sdbr_v['ma20_price'];
                    }

                    if( empty($sdbr_v['ma30_price']) ){
                        $ma30_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 30, $last_price, $ma20_price);
                        $ma['ma30_price']   = $ma30_price;
                    }else{
                        $ma30_price = $sdbr_v['ma30_price'];
                    }

                    if( empty($sdbr_v['ma60_price']) ){
                        $ma60_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 60, $last_price, $ma30_price);
                        $ma['ma60_price']   = $ma60_price;
                    }else{
                        $ma60_price = $sdbr_v['ma60_price'];
                    }

                    if( empty($sdbr_v['ma120_price']) ){
                        $ma120_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 120, $last_price, $ma60_price);
                        $ma['ma120_price']  = $ma120_price;
                    }else{
                        $ma120_price = $sdbr_v['ma120_price'];
                    }

                    if( empty($sdbr_v['ma240_price']) ){
                        $ma240_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 240, $last_price, $ma120_price);
                        $ma['ma240_price']  = $ma240_price;
                    }else{
                        $ma240_price = $sdbr_v['ma240_price'];
                    }

                    if( !empty($ma) ){
                    
                        $ma['ma_price_time'] = $now;
                    }
                }

                if( empty($ma) ) continue;

                if( $flag==1 ){/// 新增
                
                    $re = $sdb_statistics_moving_average->insert($ma)->exec();

                    if( !$re ){
                        echo '新增失败！--》》》sdb_statistics_moving_average表id: ' . $sdbr_v['id'] . PHP_EOL;
                    }else{
                        $msg = '新增成功 --》》sdb_statistics_moving_average表id: ' . $sdbr_v['id'] . ' --》';
                        $arr = [];
                        foreach( $ma as $msg_k=>$msg_v){
                        
                            $arr[] =  $msg_k . ':' . $msg_v;
                        }
                        echo $msg . implode(' | ', $arr) . PHP_EOL;
                    }

                }else {/// 更新
                    
                    $re = $sdb_statistics_moving_average->update($ma)
                    ->where(['id', '=', $sdbr_v['sma_id']])
                    ->exec();

                    if( !$re ){
                        echo '更新失败！--》》sdb_statistics_moving_average: ' . $sdbr_v['sma_id'] . PHP_EOL;
                    }else{
                        $msg = '更新成功 --》sdb_statistics_moving_average: ' . $sdbr_v['sma_id'] . ' --》';
                        $arr = [];
                        foreach( $ma as $msg_k=>$msg_v){
                        
                            $arr[] =  $msg_k . ':' . $msg_v;
                        }
                        echo $msg . implode(' | ', $arr) . PHP_EOL;
                    }
                }
            }

            echo '完成：'. $percent . PHP_EOL;
            $dividend++;
        }
    }

    /**
     * 计算均价
     */
    protected function getAveragePrice($k, $data, $count, &$pre, $last_ma_price=''){

        if( 
            $last_ma_price==='none' &&
            $count!=5
        ){
            return $last_ma_price;
        }
    
        $i          = 0;
        $times      = 0;
        $ma_price   = 0;
        do {
            
            $now_k = $k+$i;
            $i++;
    
            if( !isset($data[$now_k]) ){
                $ma_price = 'none';
                return $ma_price;
            }
    
            if(
                empty($data[$now_k]['day_end_price']) ||
                $data[$now_k]['day_end_price']=='0.0'
            ){/// 收盘价为空  可能的情况：当天停牌
            
                if( $now_k==$k ){# 如果是第一条，则这条数据不计算均价(当天停牌，无均价可言)
                    return 'none';
                }
                continue;
            }else{
    
                // if( 
                //     $count==5 &&
                //     $times==4
                // ){
                //     $pre['price']    = $data[$now_k]['day_end_price'];
                //     $pre['m5_key']   = $now_k;
                // }elseif ( $times==$count-1 ) {
                    
                //     $ma_key         = 'm'.$count.'_key';
                //     $pre[$ma_key]   = $now_k;
                // }
    
                $ma_price = $ma_price+$data[$now_k]['day_end_price'];
                $times++;
            }
    
        } while ($times<$count);
    
        $price = round($ma_price/$count, 6);
    
        // if( $price==0 ){
        //     var_dump($ma_price);
        //     exit;
        // }
    
        return $price;
    }

    /**
     * 计算均线角
     */
    public function maAngle(){

        /// 初始化参数
        $now                                    = time();
        $shares_model                           = new SharesModel;
        $shares_details_byday_model             = new SharesDetailsBydayModel('sdb');
        $sdb_statistics_moving_average_model    = new SdbStatisticsMovingAverageModel;
    
        /// 获取shares所有id
        $ids = $shares_model->select('id, title, code')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($ids);
        foreach( $ids as $v){

            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            /// 获取当前票的所有记录
            $this_shares_details_byday_row = $shares_details_byday_model->select('
                sdb.id,
                sdb.shares__id,
                sdb.active_date,
                sdb.active_date_timestamp,
                sma.id as sma_id,
                sdb.day_end_price,
                sma.ma5_angle,
                sma.ma10_angle,
                sma.ma15_angle,
                sma.ma20_angle,
                sma.ma30_angle,
                sma.ma60_angle,
                sma.ma120_angle,
                sma.ma240_angle,
                sma.ma5_price,
                sma.ma10_price,
                sma.ma15_price,
                sma.ma20_price,
                sma.ma30_price,
                sma.ma60_price,
                sma.ma120_price,
                sma.ma240_price
            ')
            ->leftjoin('tl_sdb_statistics_moving_average as sma', 'sma.shares_details_byday__id=sdb.id')
            ->where(['sdb.shares__id', $v['id']])
            ->orderby('active_date_timestamp desc')
            ->get();

            if( empty($this_shares_details_byday_row) ){
                echo $v['title'].'；股票代码：'.$v['code'].' 无详情信息！'.PHP_EOL;
                echo '完成：'. $percent . PHP_EOL;
                continue;
            }

            $now_ma_price   = '';
            $next_ma_price  = '';
            foreach( $this_shares_details_byday_row as $k=>$sdbr_v){

                /// 初始化参数
                $ma_angle   = [];
                $days       = [5, 10, 15, 20, 30, 60, 120, 240];

                /// 计算均线角   横轴为时间，纵轴为均价；横轴时间一个单位值固定为10，表示一天
                $now_ma_k   = $k;
                $next_ma_k  = $k+1;

                # 下一条均值不存在则无法计算当前均值的均线角
                if( !isset($this_shares_details_byday_row[$next_ma_k]) ) continue;

                foreach( $days as $day){

                    $ma_num = 'ma'.$day;
                    
                    # 已经存在的无需再计算
                    if( !empty($sdbr_v[$ma_num.'_angle']) ){
                        // echo $v['title'].'；股票代码：'.$v['code'].'；shares_details_byday表id:'.$sdbr_v['id'].'已经存在'.$ma_num.'均线角'.PHP_EOL;
                        continue;
                    }
    
                    # 计算角度
                    $now_ma_price   = $this_shares_details_byday_row[$now_ma_k][$ma_num.'_price'];
                    $next_ma_price  = $this_shares_details_byday_row[$next_ma_k][$ma_num.'_price'];

                    if( 
                        is_null($now_ma_price) ||
                        is_null($next_ma_price) ||
                        $now_ma_price=='none' ||
                        $next_ma_price=='none' ||
                        trim($now_ma_price)==='' ||
                        trim($next_ma_price)===''
                     ){
                        continue;
                    }

                    $ma_diff_price  = $now_ma_price-$next_ma_price;
                    $numb1          = ($ma_diff_price/$next_ma_price)*100;
                    $numb2          = 10;
                    $angle          = rad2deg(atan($numb1/$numb2));# red2deg弧度转角度
    
                    // var_dump($angle);
                    // echo '-------------'.PHP_EOL;
                    $ma_angle[$ma_num.'_angle'] = $angle;
                }

                if( empty($ma_angle) ) continue;

                $ma_angle['ma_angle_time'] = $now;

                # 更新数据
                $re = $sdb_statistics_moving_average_model->update($ma_angle)
                ->where(['id', '=', $sdbr_v['sma_id']])
                ->exec();

                if( !$re ){
                    echo '更新失败！--》》sdb_statistics_moving_average: ' . $sdbr_v['sma_id'] . PHP_EOL;
                }else{
                    echo '更新成功 --》sdb_statistics_moving_average: ' . $sdbr_v['sma_id'] . PHP_EOL;
                    echo '完成：'. $percent . PHP_EOL;
                }
            }
            echo '完成：'. $percent . PHP_EOL;
            $dividend++;
        }
    }

    /**
     * 计算均线偏离率
     */
    public function pianlilv(){

        /// 初始化参数
        $now = time();
        $gupiao_common_service                          = new GupiaoCommonService;
        $shares_model                                   = new SharesModel;
        $shares_details_byday_model                     = new SharesDetailsBydayModel('sdb');
        $sdb_statistics_moving_average_pianyilv_model   = new SdbStatisticsMovingAveragePianyilvModel;

        /// 获取shares所有id
        $ids = $shares_model->select('id')->get();# 无论是否退市都需要统计

        /// 每只票单独处理
        $gupiao_common_service->outputPercent($ids, function ($key, $row, &$dividend, $parent_percent) use ($now, $shares_details_byday_model, $sdb_statistics_moving_average_pianyilv_model){
        
            $shares__id = $row['id'];

            # 当前股票所有历史数据
            $this_share = $shares_details_byday_model->leftjoin('tl_sdb_statistics_moving_average sma', 'sdb.id=sma.shares_details_byday__id')
            ->leftjoin('tl_sdb_statistics_moving_average_pianyilv smap', 'sdb.id=smap.shares_details_byday__id')->select([
                'sdb.day_end_price',
                'sdb.id as sdb_id',
                'sdb.active_date',
                'sdb.active_date_timestamp',
                'sma.ma5_price',
                'sma.ma10_price',
                'sma.ma15_price',
                'sma.ma20_price',
                'sma.ma30_price',
                'sma.ma60_price',
                'sma.ma120_price',
                'sma.ma240_price',
                'smap.ma5_plv',
                'smap.ma10_plv',
                'smap.ma15_plv',
                'smap.ma20_plv',
                'smap.ma30_plv',
                'smap.ma60_plv',
                'smap.ma120_plv',
                'smap.ma240_plv',
            ])->where([
                ['sdb.shares__id', $shares__id],
                ['sdb.day_end_price', '<>', ''],
                ['sdb.day_end_price', '<>', 'none'],
            ])->having('smap.ma5_plv is null')
            ->orderby('sdb.active_date_timestamp desc')->get();

            if( empty($this_share) ){
            
                return false;
            }

            # 遍历每日数据进行处理
            $this_dividend  = 1;
            $this_divisor   = count($this_share);
            $names          = [5, 10, 15, 20, 30, 60, 120, 240];
            foreach( $this_share as $one_day){

                $this_percent = number_format(($this_dividend/$this_divisor)*100, 4) . '%';
            
                if( in_array($one_day['day_end_price'], ['', 'none']) ){## 收盘价无有效值则无需计算，将所有偏移率值改为'88888888'
                    $this_dividend++;
                    continue;
                }

                $data = [];
                foreach( $names as $num){
                
                    $this_ma_price_name = 'ma'.$num.'_price';## $num天均价 名称
                    $this_ma_plv_name   = 'ma'.$num.'_plv';## $num天均线偏移率 名称

                    if( 
                        $one_day[$this_ma_plv_name]==99999999 ||
                        $one_day[$this_ma_plv_name]===NULL
                      ){## 1. 偏移率值等于'99999999'表示可以重新计算进行更新；偏移率值等于'88888888'表示计算过但当日数据无法得出有效值；2. 偏移率为NULL，表示需要新增
                    
                        if( in_array($one_day[$this_ma_price_name], ['', 'none']) ){### 均价无有效值则无需计算（不做处理则将会维持默认值'99999999'，则下一次执行脚本依然满足重新计算偏移率条件并将重新计算；这样做是为了滞后计算长周期均线偏移率）
                            continue;
                        }

                        ### 计算偏移率
                        $_this_day_end_price        = (int)($one_day['day_end_price'] * 1000000000);
                        $_this_ma_price             = (int)($one_day[$this_ma_price_name] * 1000000000);
                        
                        if( $_this_day_end_price>$_this_ma_price ){
                        
                            $data[$this_ma_plv_name] = (int)round((($_this_day_end_price - $_this_ma_price)/$_this_day_end_price) * 10000);
                        }else{
                        
                            $data[$this_ma_plv_name] = (int)round((($_this_day_end_price - $_this_ma_price)/$_this_ma_price) * 10000);
                        }
                    }
                }

                if( empty($data) ){## 没有需要新增或更新的数据
                    $this_dividend++;
                    continue;
                }

                ## 是否存在偏移率记录
                $_this_sdb_id       = $one_day['sdb_id'];
                $has_pianyilv_row   = $sdb_statistics_moving_average_pianyilv_model->where(['shares_details_byday__id', $_this_sdb_id])->find();

                if( $has_pianyilv_row ){### 更新

                    $re = $sdb_statistics_moving_average_pianyilv_model->update($data)->where(['shares_details_byday__id', $_this_sdb_id])->exec();

                    if( !$re ){
                    
                        echo 'sdb_statistics_moving_average_pianyilv更新失败，shares_details_byday__id：'.$_this_sdb_id.PHP_EOL;
                    }
                
                }else{### 新增

                    $data['shares__id']                 = $shares__id;
                    $data['shares_details_byday__id']   = $_this_sdb_id;
                    $data['active_date']                = $one_day['active_date'];
                    $data['active_date_timestamp']      = $one_day['active_date_timestamp'];
                    $data['created_time']               = $now;

                    $re = $sdb_statistics_moving_average_pianyilv_model->insert($data)->exec();

                    if( !$re ){
                    
                        echo 'sdb_statistics_moving_average_pianyilv新增失败，shares_details_byday__id：'.$_this_sdb_id.PHP_EOL;
                    }
                }

                echo '总体：'.$parent_percent.'; 当前：'.$this_percent.PHP_EOL;
                $this_dividend++;
            }

            return true;
        });
    }

    /**
     * 统计最大，最小偏移率，入库
     */
    public function maxAndMinPianyilv(){
    
        /// 初始化参数
        $mixed_statistics_model                         = new MixedStatisticsModel;
        $sdb_statistics_moving_average_pianyilv_model   = new SdbStatisticsMovingAveragePianyilvModel;

        /// 均线偏移率极值
        # 近10年极值
        $_10years_pianyilv = $sdb_statistics_moving_average_pianyilv_model->getPianyilvMaxAndMin(10);

        # 近5年极值
        $_5years_pianyilv = $sdb_statistics_moving_average_pianyilv_model->getPianyilvMaxAndMin(5);

        # 近3年极值
        $_3years_pianyilv = $sdb_statistics_moving_average_pianyilv_model->getPianyilvMaxAndMin(3);

        /// 数据入库
        # 近10年极值
        $mixed_statistics_model->getIn($_10years_pianyilv, 1);
        # 近5年极值
        $mixed_statistics_model->getIn($_5years_pianyilv, 2);
        # 近3年极值
        $mixed_statistics_model->getIn($_3years_pianyilv, 3);
    }

    /**
     * 统计不同偏移率区间每支票的涨跌幅历史复现率
     * 
     * 5日均线偏移后涨跌复现率
     * 10日均线偏移后涨跌复现率
     * ....
     */
    public function afterPianyilvZhangfu(){

        /// 初始化参数
        $that                       = $this;
        $shares_model               = new SharesModel;
        $gupiao_common_service      = new GupiaoCommonService;
        $shares_details_byday_model = new SharesDetailsBydayModel('sdb');

        /// 所有有效股票id
        $ids = $shares_model->select('id')->where(['is_deprecated', 0])->get();

        /// 遍历每支股票
        $gupiao_common_service->outputPercent($ids, function ($key, $row, &$dividend, $parent_percent) use ($shares_details_byday_model, $that){
        
            # 初始化参数
            $shares__id = $row['id'];

            # 查询当前股票数据
            $details = $shares_details_byday_model->select([
                'sdb.day_end_price',
                'sdb.active_date',
                'sdb.uad_range',
                'smap.ma5_plv'
            ])->leftjoin('tl_sdb_statistics_moving_average_pianyilv smap', 'sdb.id=smap.shares_details_byday__id')
            ->where([
                ['sdb.shares__id', $shares__id],
                ['sdb.uad_range', 'not in', '("", "None")']
            ])
            ->orderby('sdb.active_date_timestamp asc')
            ->get();

            if( empty($details) ){
                return false;
            }

            # 初始化参数
            $total_day_num = count($details);
            $info = [
                'gt100' => [## 偏离率>100
                    'day_num'   => 0,### 天数
                    'next_day_up_num'               => 0,### 第二天上涨天数
                    'next_day_up_uad_range'         => [ 'min'=>0, 'max'=>0],### 第二天上涨的涨幅区间
                    'continued_2_day_up_num'        => 0,### 持续2天上涨天数
                    'continued_2_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续2天上涨的涨幅区间
                    'continued_3_day_up_num'        => 0,### 持续3天上涨天数
                    'continued_3_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续3天上涨的涨幅区间
                    'continued_4_day_up_num'        => 0,### 持续4天上涨天数
                    'continued_4_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续4天上涨的涨幅区间
                    'continued_5_day_up_num'        => 0,### 持续5天上涨天数
                    'continued_5_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续5天上涨的涨幅区间
                    'continued_6_day_up_num'        => 0,### 持续6天上涨天数
                    'continued_6_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续6天上涨的涨幅区间
                    'continued_7_day_up_num'        => 0,### 持续7天上涨天数
                    'continued_7_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续7天上涨的涨幅区间
                    'continued_8_day_up_num'        => 0,### 持续8天上涨天数
                    'continued_8_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续8天上涨的涨幅区间
                    'continued_9_day_up_num'        => 0,### 持续9天上涨天数
                    'continued_9_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续9天上涨的涨幅区间
                    'continued_gt9_day_up_num'      => 0,### 持续上涨超过9天的天数
                    'next_day_dw_num'               => 0,### 第二天下跌天数
                    'next_day_dw_uad_range'         => [ 'min'=>0, 'max'=>0],### 第二天下跌的跌幅区间
                    'continued_2_day_dw_num'        => 0,### 持续2天下跌天数
                    'continued_2_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续2天下跌的跌幅区间
                    'continued_3_day_dw_num'        => 0,### 持续3天下跌天数
                    'continued_3_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续3天下跌的跌幅区间
                    'continued_4_day_dw_num'        => 0,### 持续4天下跌天数
                    'continued_4_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续4天下跌的跌幅区间
                    'continued_5_day_dw_num'        => 0,### 持续5天下跌天数
                    'continued_5_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续5天下跌的跌幅区间
                    'continued_6_day_dw_num'        => 0,### 持续6天下跌天数
                    'continued_6_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续6天下跌的跌幅区间
                    'continued_7_day_dw_num'        => 0,### 持续7天下跌天数
                    'continued_7_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续7天下跌的跌幅区间
                    'continued_8_day_dw_num'        => 0,### 持续8天下跌天数
                    'continued_8_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续8天下跌的跌幅区间
                    'continued_9_day_dw_num'        => 0,### 持续9天下跌天数
                    'continued_9_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续9天下跌的跌幅区间
                    'continued_gt9_day_dw_num'      => 0,### 持续下跌超过9天的天数
                ]
            ];

            for ($i=-50; $i <= 50; $i++) { 

                if( $i===0 ) continue;
                
                if( $i<0 ){

                    $_info_key = '>'.($i*2).'_<='.(($i+1)*2);
                }else{
                    $_info_key = '>'.(($i-1)*2).'_<='.($i*2);
                }

                $info[$_info_key]   = [
                    'day_num'   => 0,### 天数
                    'next_day_up_num'               => 0,### 第二天上涨天数
                    'next_day_up_uad_range'         => [ 'min'=>0, 'max'=>0],### 第二天上涨的涨幅区间
                    'continued_2_day_up_num'        => 0,### 持续2天上涨天数
                    'continued_2_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续2天上涨的涨幅区间
                    'continued_3_day_up_num'        => 0,### 持续3天上涨天数
                    'continued_3_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续3天上涨的涨幅区间
                    'continued_4_day_up_num'        => 0,### 持续4天上涨天数
                    'continued_4_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续4天上涨的涨幅区间
                    'continued_5_day_up_num'        => 0,### 持续5天上涨天数
                    'continued_5_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续5天上涨的涨幅区间
                    'continued_6_day_up_num'        => 0,### 持续6天上涨天数
                    'continued_6_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续6天上涨的涨幅区间
                    'continued_7_day_up_num'        => 0,### 持续7天上涨天数
                    'continued_7_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续7天上涨的涨幅区间
                    'continued_8_day_up_num'        => 0,### 持续8天上涨天数
                    'continued_8_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续8天上涨的涨幅区间
                    'continued_9_day_up_num'        => 0,### 持续9天上涨天数
                    'continued_9_day_up_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续9天上涨的涨幅区间
                    'continued_gt9_day_up_num'      => 0,### 持续上涨超过9天的天数
                    'next_day_dw_num'               => 0,### 第二天下跌天数
                    'next_day_dw_uad_range'         => [ 'min'=>0, 'max'=>0],### 第二天下跌的跌幅区间
                    'continued_2_day_dw_num'        => 0,### 持续2天下跌天数
                    'continued_2_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续2天下跌的跌幅区间
                    'continued_3_day_dw_num'        => 0,### 持续3天下跌天数
                    'continued_3_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续3天下跌的跌幅区间
                    'continued_4_day_dw_num'        => 0,### 持续4天下跌天数
                    'continued_4_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续4天下跌的跌幅区间
                    'continued_5_day_dw_num'        => 0,### 持续5天下跌天数
                    'continued_5_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续5天下跌的跌幅区间
                    'continued_6_day_dw_num'        => 0,### 持续6天下跌天数
                    'continued_6_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续6天下跌的跌幅区间
                    'continued_7_day_dw_num'        => 0,### 持续7天下跌天数
                    'continued_7_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续7天下跌的跌幅区间
                    'continued_8_day_dw_num'        => 0,### 持续8天下跌天数
                    'continued_8_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续8天下跌的跌幅区间
                    'continued_9_day_dw_num'        => 0,### 持续9天下跌天数
                    'continued_9_day_dw_uad_range'  => [ 'min'=>0, 'max'=>0],### 持续9天下跌的跌幅区间
                    'continued_gt9_day_dw_num'      => 0,### 持续下跌超过9天的天数
                    'continued_gt9_day_dw_uad_range'=> [ 'min'=>0, 'max'=>0],### 持续下跌超过9天的跌幅区间
                ];
            }

            // print_r($info);
            // exit;
            
            # 统计数据
            foreach( $details as $_k=>$_detail){

                ## 无效数据跳过
                if( in_array($_detail['ma5_plv'], [99999999, 88888888]) ) continue;

                ## 第二天涨跌幅为0的跳过
                $_next_k                            = $_k+1;
                $_next_detail_uad_range             = $details[$_next_k]['uad_range'];### 第二天涨跌幅
                $_next_detail_uad_range_10000bei    = (int)($_next_detail_uad_range*10000);### 因数据表中数据为百分数，有四位小数，故乘以10000转整型做比较

                if( $_next_detail_uad_range_10000bei===0 ) continue;
            
                ## 数据表的值是乘以10000倍后的值，除以100既可得到百分值
                $_100bei_plv = $_detail['ma5_plv']/100;### 3432/100=34.32

                ## 去除小数后是奇是偶
                $_100bei_plv_del_point  = (int)number_format($_100bei_plv);### 34.32 ==》34

                ## 确定偏移率区间
                if( $_100bei_plv_del_point>100 ){
                
                    $_info_key = 'gt100';
                }else{

                    $_is_oushu = $_100bei_plv_del_point%2==0;

                    if( $_is_oushu ){### 偶数
                    
                        $_compare_target                = (int)number_format($_100bei_plv*100);#### 34.32*100  ==》 3432
                        $_100bei_plv_del_point_10bei    = $_100bei_plv_del_point*100;#### 34*100 ==》 3400

                        if( $_compare_target>$_100bei_plv_del_point_10bei ){#### 3432>3400
                            ####                    34                             36
                            $_info_key = '>'.$_100bei_plv_del_point.'_<='.($_100bei_plv_del_point+2);
                        }else{#### 3400<=3400
                            ####                     34-2                            34
                            $_info_key = '>'.($_100bei_plv_del_point-2).'_<='.$_100bei_plv_del_point;
                        }

                    }else{### 奇数 如33   则$_info_key='>'.(33-1).'_<='.(33+1)

                        $_info_key = '>'.($_100bei_plv_del_point-1).'_<='.($_100bei_plv_del_point+1);
                    }
                }

                ## 统计数据
                $info[$_info_key]['day_num'] += 1;### 区间统计天数+1

                if( $_next_detail_uad_range_10000bei>0 ){### 表示上涨

                    $this->recursiveContinuedUp($details, $_k, $info[$_info_key]);

                }else{### 已经排除了涨跌幅为0的情况，故else为小于0，表示下跌

                    $this->recursiveContinuedDw($details, $_k, $info[$_info_key]);

                }
            }

            print_r($info);
            exit;
            
        });
    }

    /**
     * 递归统计连涨
     */
    protected function recursiveContinuedUp($details, $_k, &$info_row, $level=1){

        /// 下一天涨跌幅为0的跳过
        $_next_k                            = $_k+1;
        $_next_detail_uad_range             = $details[$_next_k]['uad_range'];### 第二天涨跌幅
        $_next_detail_uad_range_10000bei    = (int)($_next_detail_uad_range*10000);### 因数据表中数据为百分数，有四位小数，故乘以10000转整型做比较

        if( $_next_detail_uad_range_10000bei===0 ) return false;

        if( $_next_detail_uad_range_10000bei>0 ){### 表示上涨

            $_row_info_key  = $this->getRowInfoKey($level, 'u');
            $up_num_name    = $_row_info_key[0];
            $uad_range_name = $_row_info_key[1];

            $info_row[$up_num_name] += 1;

            if( 
                $_next_detail_uad_range_10000bei<$info_row[$uad_range_name]['min'] ||
                $info_row[$uad_range_name]['min']===0
            ){
                // 直接拿下一天的涨跌幅赋值是有问题的，如果时隔多天，那么数据就失真了，应该以基准天的收盘价做启示价重新计算
                // 需要记录时间
                // $info_row[$uad_range_name]['min'] = $_next_detail_uad_range_10000bei;

            }elseif ( $_next_detail_uad_range_10000bei>$info_row[$uad_range_name]['max'] ) {

                // $info_row[$uad_range_name]['max'] = $_next_detail_uad_range_10000bei;
            }

            $this->recursiveContinuedUp($details, $_next_k, $info_row, $level+1);
            return true;
        }

        return false;
    }

    /**
     * 递归统计连跌
     */
    protected function recursiveContinuedDw($details, $_k, &$info_row, $level=1){

        /// 下一天涨跌幅为0的跳过
        $_next_k                            = $_k+1;
        $_next_detail_uad_range             = $details[$_next_k]['uad_range'];### 第二天涨跌幅
        $_next_detail_uad_range_10000bei    = (int)($_next_detail_uad_range*10000);### 因数据表中数据为百分数，有四位小数，故乘以10000转整型做比较

        if( $_next_detail_uad_range_10000bei===0 ) return false;
    
        if( $_next_detail_uad_range_10000bei<0 ){### 表示下跌

            $_row_info_key  = $this->getRowInfoKey($level, 'd');
            $up_num_name    = $_row_info_key[0];
            $uad_range_name = $_row_info_key[1];

            $info_row[$up_num_name] += 1;

            if( $_next_detail_uad_range_10000bei<$info_row[$uad_range_name]['min'] ){
            
                // $info_row[$uad_range_name]['min'] = $_next_detail_uad_range_10000bei;

            }elseif ( 
                $_next_detail_uad_range_10000bei>$info_row[$uad_range_name]['max'] ||
                $info_row[$uad_range_name]['max']===0
            ) {

                // $info_row[$uad_range_name]['max'] = $_next_detail_uad_range_10000bei;
            }

            $this->recursiveContinuedDw($details, $_next_k, $info_row, $level+1);
            return true;
        }

        return false;
    }

    /**
     * 根据级别获取名称
     */
    protected function getRowInfoKey($level, $type){

        if( $type=='u' ){
            $flag = 'up';
        }elseif( $type=='d' ){
            $flag = 'dw';
        }
    
        if( $level==1 ){
        
            return ['next_day_'.$flag.'_num', 'next_day_'.$flag.'_uad_range'];
        }elseif( $level<=9 ){
        
            return [
                'continued_'.$level.'_day_'.$flag.'_num',
                'continued_'.$level.'_day_'.$flag.'_uad_range'
            ];
        }else {
            return [
                'continued_gt9_day_'.$flag.'_num',
                'continued_gt9_day_'.$flag.'_uad_range'
            ];
        }
    }

}
