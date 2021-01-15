<?php

namespace cmd\service;
use \Err;
use \Fun;
use model\IntervalsModel;
use model\MaPianyilvStatisticsModel;
use model\MixedStatisticsModel;
use model\SdbStatisticsMovingAverageModel;
use model\SdbStatisticsMovingAveragePianyilvModel;
use model\SharesDetailsBydayModel;
use model\SharesModel;
use model\StatisticsRulesModel;

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
                'sdb.id',
                'sdb.day_end_price',
                'sdb.active_date',
                'sdb.uad_range',
                'smap.ma5_plv',
                'smap.ma10_plv',
                'smap.ma15_plv',
                'smap.ma20_plv',
                'smap.ma30_plv',
                'smap.ma60_plv',
                'smap.ma120_plv',
                'smap.ma240_plv',
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
            $total_day_num  = count($details);
            // print_r($info);
            // exit;
            
            $_period = [5, 10, 15, 20, 30, 60, 120, 240];

            foreach( $_period as $_perio){

                $info       = $this->mkInfo();
                $_ma_name   = 'ma'.$_perio.'_plv';

                foreach( $details as $_k=>$_detail){

                    # 确定$info的键
                    ## 无效数据跳过
                    if( in_array($_detail[$_ma_name], [99999999, 88888888]) ) continue;

                    ## 第二天涨跌幅为0的跳过
                    $_next_k                            = $_k+1;
                    $_next_detail_uad_range             = $details[$_next_k]['uad_range'];### 第二天涨跌幅
                    $_next_detail_uad_range_10000bei    = (int)($_next_detail_uad_range*10000);### 因数据表中数据为百分数，有四位小数，故乘以10000转整型做比较

                    if( $_next_detail_uad_range_10000bei===0 ) continue;
                
                    ## 数据表的值是乘以10000倍后的值，除以100既可得到百分值
                    $_100bei_plv        = $_detail[$_ma_name]/100;### 3432/100=34.32
                    $_compare_target    = (int)number_format($_100bei_plv*100);#### 34.32*100  ==》 3432

                    ## 去除小数后是奇是偶
                    $_100bei_plv_del_point  = (int)number_format($_100bei_plv);### 34.32 ==》34

                    ## 确定偏移率区间
                    if( $_compare_target>(70*100) ){
                    
                        $_info_key = 'gt70';
                    }elseif( $_compare_target<=(-70*100) ){
                        
                        $_info_key = 'lt=-70';
                    }else{

                        $_is_oushu = $_100bei_plv_del_point%2==0;

                        if( $_is_oushu ){### 偶数
                        
                            $_100bei_plv_del_point_10bei = $_100bei_plv_del_point*100;#### 34*100 ==》 3400

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

                        // if( $_info_key=='>-18_<=-16' ){
                        
                            $this->recursiveContinuedUpOrDw($details, $_k, $info[$_info_key], 'up');
                        // }

                    }else{### 已经排除了涨跌幅为0的情况，故else为小于0，表示下跌

                        // if( $_info_key=='>-18_<=-16' ){
                        
                            $this->recursiveContinuedUpOrDw($details, $_k, $info[$_info_key], 'dw');
                        // }
                    }
                }

                # 数据入库
                $this->mpIndb($info, $_perio, $shares__id);

                // var_dump(count($info));
                // print_r($info['>-18_<=-16']);
                print_r($info);
                exit;
            }
            
        });
    }

    /**
     * 均线偏移--涨跌复现率 数据入库
     */
    protected function mpIndb($info, $period, $shares__id){

        /// 初始化参数
        $intervals_model                = new IntervalsModel;
        $statistics_rules_model         = new StatisticsRulesModel;
        $ma_pianyilv_statistics_model   = new MaPianyilvStatisticsModel;
        $statistics_rules__id           = $statistics_rules_model->period2rulesId($period);
    
        foreach( $info as $k=>$ma_pianyilv_statistics_row){
        
            if( $k=='gt70' ){
            
            }elseif( $k=='lt=-70' ){
            
            }else{

                /// 区间
                $k_arr = explode('_', $k);
                preg_match('/\d*$/', $k_arr[0], $matches1);
                preg_match('/\d*$/', $k_arr[1], $matches2);

                $_condi = [
                    ['b_interval', $matches1[0]],
                    ['is_equal_to_b_interval', 0],
                    ['e_interval', $matches2[0]],
                    ['is_equal_to_e_interval', 0],
                    ['statistics_rules__id', $statistics_rules__id]
                ];

                $intervals_row = $intervals_model->select('id')->where($_condi)->find();
                if( empty($intervals_row) ) continue;

                /// 录入数据
                # 初始化参数
                $intervals__id = $intervals_row['id'];

                # 组装数据
                $_data = [
                    'shares__id'                            => $shares__id,
                    'intervals__id'                         => $intervals__id,
                    'day_num'                               => $ma_pianyilv_statistics_row['day_num'],

                    'next_day_up_num'                       => $ma_pianyilv_statistics_row['next_day_up_num'],
                    'next_day_up_uad_range'                 => json_encode($ma_pianyilv_statistics_row['next_day_up_uad_range']),
                    'next_day_up_active_date_sets'          => json_encode($ma_pianyilv_statistics_row['next_day_up_active_date_sets']),

                    'continued_2_day_up_num'                => $ma_pianyilv_statistics_row['continued_2_day_up_num'],
                    'continued_2_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_2_day_up_uad_range']),
                    'continued_2_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_2_day_up_active_date_sets']),

                    'continued_3_day_up_num'                => $ma_pianyilv_statistics_row['continued_3_day_up_num'],
                    'continued_3_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_3_day_up_uad_range']),
                    'continued_3_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_3_day_up_active_date_sets']),

                    'continued_4_day_up_num'                => $ma_pianyilv_statistics_row['continued_4_day_up_num'],
                    'continued_4_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_4_day_up_uad_range']),
                    'continued_4_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_4_day_up_active_date_sets']),

                    'continued_5_day_up_num'                => $ma_pianyilv_statistics_row['continued_5_day_up_num'],
                    'continued_5_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_5_day_up_uad_range']),
                    'continued_5_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_5_day_up_active_date_sets']),

                    'continued_6_day_up_num'                => $ma_pianyilv_statistics_row['continued_6_day_up_num'],
                    'continued_6_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_6_day_up_uad_range']),
                    'continued_6_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_6_day_up_active_date_sets']),

                    'continued_7_day_up_num'                => $ma_pianyilv_statistics_row['continued_7_day_up_num'],
                    'continued_7_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_7_day_up_uad_range']),
                    'continued_7_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_7_day_up_active_date_sets']),

                    'continued_8_day_up_num'                => $ma_pianyilv_statistics_row['continued_8_day_up_num'],
                    'continued_8_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_8_day_up_uad_range']),
                    'continued_8_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_8_day_up_active_date_sets']),

                    'continued_9_day_up_num'                => $ma_pianyilv_statistics_row['continued_9_day_up_num'],
                    'continued_9_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_9_day_up_uad_range']),
                    'continued_9_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_9_day_up_active_date_sets']),

                    'continued_gt9_day_up_num'                => $ma_pianyilv_statistics_row['continued_gt9_day_up_num'],
                    'continued_gt9_day_up_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_gt9_day_up_uad_range']),
                    'continued_gt9_day_up_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_gt9_day_up_active_date_sets']),

                    'next_day_dw_num'                       => $ma_pianyilv_statistics_row['next_day_dw_num'],
                    'next_day_dw_uad_range'                 => json_encode($ma_pianyilv_statistics_row['next_day_dw_uad_range']),
                    'next_day_dw_active_date_sets'          => json_encode($ma_pianyilv_statistics_row['next_day_dw_active_date_sets']),

                    'continued_2_day_dw_num'                => $ma_pianyilv_statistics_row['continued_2_day_dw_num'],
                    'continued_2_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_2_day_dw_uad_range']),
                    'continued_2_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_2_day_dw_active_date_sets']),

                    'continued_3_day_dw_num'                => $ma_pianyilv_statistics_row['continued_3_day_dw_num'],
                    'continued_3_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_3_day_dw_uad_range']),
                    'continued_3_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_3_day_dw_active_date_sets']),

                    'continued_4_day_dw_num'                => $ma_pianyilv_statistics_row['continued_4_day_dw_num'],
                    'continued_4_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_4_day_dw_uad_range']),
                    'continued_4_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_4_day_dw_active_date_sets']),

                    'continued_5_day_dw_num'                => $ma_pianyilv_statistics_row['continued_5_day_dw_num'],
                    'continued_5_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_5_day_dw_uad_range']),
                    'continued_5_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_5_day_dw_active_date_sets']),

                    'continued_6_day_dw_num'                => $ma_pianyilv_statistics_row['continued_6_day_dw_num'],
                    'continued_6_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_6_day_dw_uad_range']),
                    'continued_6_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_6_day_dw_active_date_sets']),

                    'continued_7_day_dw_num'                => $ma_pianyilv_statistics_row['continued_7_day_dw_num'],
                    'continued_7_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_7_day_dw_uad_range']),
                    'continued_7_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_7_day_dw_active_date_sets']),

                    'continued_8_day_dw_num'                => $ma_pianyilv_statistics_row['continued_8_day_dw_num'],
                    'continued_8_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_8_day_dw_uad_range']),
                    'continued_8_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_8_day_dw_active_date_sets']),

                    'continued_9_day_dw_num'                => $ma_pianyilv_statistics_row['continued_9_day_dw_num'],
                    'continued_9_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_9_day_dw_uad_range']),
                    'continued_9_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_9_day_dw_active_date_sets']),

                    'continued_gt9_day_dw_num'                => $ma_pianyilv_statistics_row['continued_gt9_day_dw_num'],
                    'continued_gt9_day_dw_uad_range'          => json_encode($ma_pianyilv_statistics_row['continued_gt9_day_dw_uad_range']),
                    'continued_gt9_day_dw_active_date_sets'   => json_encode($ma_pianyilv_statistics_row['continued_gt9_day_dw_active_date_sets']),
                ];

                # 是否存在
                $has_row_condi = [
                    ['shares__id', $shares__id],
                    ['intervals__id', $intervals__id]
                ];
                $has_row = $ma_pianyilv_statistics_model->select('id')->where($has_row_condi)->find();

                if( $has_row ){## 更新
                

                }else{## 新增

                    $_data['created_time'] = time();

                }



            }
        }
    }

    /**
     * 递归统计连涨或连跌
     * @param   string  $type   up表示统计连涨；dw表示统计连跌
     */
    protected function recursiveContinuedUpOrDw($details, $_k, &$info_row, $type='up', $level=1, $first_day_key=''){

        /// 初始化参数
        if( $level==1 ){
            $first_day_key = $_k;
        }
        $_next_k                = $_k+1;
        $_first_detail          = $details[$first_day_key];
        $_next_detail           = $details[$_next_k];
        $first_day_id           = $_first_detail['id'];
        $first_day_end_price    = $_first_detail['day_end_price'];

        # 下一天涨跌幅
        $_uad_range_10000bei = (int)($_next_detail['uad_range']*10000);
        ## 下一天涨跌幅为0的跳过
        if( $_uad_range_10000bei===0 ) return false;

        if( $level==1 ){## 第二天的涨跌幅
            $_next_detail_uad_range = $_next_detail['uad_range'];
        }else{## 第n天离基准天的涨跌幅=(第n天的收盘价-基准天的收盘价)/基准天的收盘价
            $_next_detail_uad_range = ($_next_detail['day_end_price']-$first_day_end_price)/$first_day_end_price;
            $_next_detail_uad_range = round($_next_detail_uad_range, 4);
        }

        $_next_detail_uad_range_10000bei = (int)($_next_detail_uad_range*10000);### 因数据表中数据为百分数，有四位小数，故乘以10000转整型做比较

        switch($type){
        case 'up':# 统计连涨
            if( $_uad_range_10000bei>0 ){### 表示上涨

                $_row_info_key      = $this->getRowInfoKey($level, 'u');
                $up_num_name        = $_row_info_key[0];
                $uad_range_name     = $_row_info_key[1];
                $date_interval_name = $_row_info_key[2];
    
                $info_row[$up_num_name] += 1;

                #### 搜集涨幅集合
                $this->uadRangeDwAndUp($info_row[$uad_range_name], $_next_detail_uad_range_10000bei, $first_day_id, 'up');

                $end_active_date = $this->recursiveContinuedUpOrDw($details, $_next_k, $info_row, 'up', $level+1, $first_day_key);

                #### 时间范围
                if( $end_active_date ){
                
                    $info_row[$date_interval_name][$first_day_id] = ['b_date'=>$_first_detail['active_date'], 'e_date'=>$end_active_date];
                }else {
                    
                    $info_row[$date_interval_name][$first_day_id] = ['b_date'=>$_first_detail['active_date'], 'e_date'=>$_next_detail['active_date']];
                }
                
                if( $end_active_date ){
                
                    return $end_active_date;
                }else {
                    return false;
                }
            }
        break;
        case 'dw':# 统计连跌
            if( $_next_detail_uad_range_10000bei<0 ){### 表示下跌（不包括0）
    
                $_row_info_key      = $this->getRowInfoKey($level, 'd');
                $up_num_name        = $_row_info_key[0];
                $uad_range_name     = $_row_info_key[1];
                $date_interval_name = $_row_info_key[2];
    
                $info_row[$up_num_name] += 1;
    
                #### 搜集跌幅集合
                $this->uadRangeDwAndUp($info_row[$uad_range_name], $_next_detail_uad_range_10000bei, $first_day_id, 'dw');

                #### 下一次连跌持续
                $end_active_date = $this->recursiveContinuedUpOrDw($details, $_next_k, $info_row, 'dw', $level+1, $first_day_key);

                #### 时间范围
                if( $end_active_date ){
                    $info_row[$date_interval_name][$first_day_id] = ['b'=>$_first_detail['active_date'], 'e'=>$end_active_date];
                }else {
                    $info_row[$date_interval_name][$first_day_id] = ['b'=>$_first_detail['active_date'], 'e'=>$_next_detail['active_date']];
                }
                
                if( $end_active_date ){
                
                    return $end_active_date;
                }else {
                    return false;
                }
            }
        break;
        }

        return false;
    }

    /**
     * 搜集涨跌幅集合
     * @param   $type   string  可选值：'up'表示上涨幅度集合；'dw'表示下跌幅度集合
     */
    protected function uadRangeDwAndUp(&$uad_range, $_next_detail_uad_range_10000bei, $first_day_id, $type){
    

        if( !isset($uad_range[$type]) ){

            $uad_range[$type] = [];
        }
        $uad_range[$type][$first_day_id] = $_next_detail_uad_range_10000bei;

        // if( !isset($uad_range['min'])&&!isset($uad_range['max']) ){##### 仅当$level==1时,本条件才会成立
    
        //     if( is_null($last_uad_range_min) ){
            
        //         $uad_range['min'] = $_next_detail_uad_range_10000bei;
        //     }else {
                
        //         $uad_range['min'] = $last_uad_range_min>$_next_detail_uad_range_10000bei ? $_next_detail_uad_range_10000bei : $last_uad_range_min;
        //     }

        //     if( is_null($last_uad_range_max) ){
            
        //         $uad_range['max'] = $_next_detail_uad_range_10000bei;
        //     }else {
                
        //         $uad_range['max'] = $last_uad_range_max>$_next_detail_uad_range_10000bei ? $last_uad_range_max : $_next_detail_uad_range_10000bei;
        //     }

        // }else {

        //     if( $_next_detail_uad_range_10000bei>$uad_range['max'] ){
            
        //         $uad_range['max'] = $_next_detail_uad_range_10000bei;
        //     }

        //     if( $_next_detail_uad_range_10000bei<$uad_range['min'] ){
            
        //         $uad_range['min'] = $_next_detail_uad_range_10000bei;
        //     }
        // }
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
        
            return [
                'next_day_'.$flag.'_num',
                'next_day_'.$flag.'_uad_range',
                'next_day_'.$flag.'_active_date_sets'
            ];
        }elseif( $level<=9 ){
        
            return [
                'continued_'.$level.'_day_'.$flag.'_num',
                'continued_'.$level.'_day_'.$flag.'_uad_range',
                'continued_'.$level.'_day_'.$flag.'_active_date_sets'
            ];
        }else {
            return [
                'continued_gt9_day_'.$flag.'_num',
                'continued_gt9_day_'.$flag.'_uad_range',
                'continued_gt9_day_'.$flag.'_active_date_sets'
            ];
        }
    }

    /**
     * 生成info数组
     */
    protected function mkInfo(){

        /// 初始化参数
        $info               = [];
        $common_key_and_val = [
            'day_num'   => 0,### 天数
            'next_day_up_num'                       => 0,### 第二天上涨天数
            'next_day_up_uad_range'                 => [],### 第二天上涨的涨幅区间
            'next_day_up_active_date_sets'          => [],### 第二天上涨的历史日期区间集合

            'continued_2_day_up_num'                => 0,### 持续2天上涨天数
            'continued_2_day_up_uad_range'          => [],### 持续2天上涨的涨幅区间
            'continued_2_day_up_active_date_sets'   => [],### 持续2天上涨的历史日期区间集合

            'continued_3_day_up_num'                => 0,### 持续3天上涨天数
            'continued_3_day_up_uad_range'          => [],### 持续3天上涨的涨幅区间
            'continued_3_day_up_active_date_sets'   => [],### 持续3天上涨的历史日期区间集合

            'continued_4_day_up_num'                => 0,### 持续4天上涨天数
            'continued_4_day_up_uad_range'          => [],### 持续4天上涨的涨幅区间
            'continued_4_day_up_active_date_sets'   => [],### 持续4天上涨的历史日期区间集合

            'continued_5_day_up_num'                => 0,### 持续5天上涨天数
            'continued_5_day_up_uad_range'          => [],### 持续5天上涨的涨幅区间
            'continued_5_day_up_active_date_sets'   => [],### 持续5天上涨的历史日期区间集合

            'continued_6_day_up_num'                => 0,### 持续6天上涨天数
            'continued_6_day_up_uad_range'          => [],### 持续6天上涨的涨幅区间
            'continued_6_day_up_active_date_sets'   => [],### 持续6天上涨的历史日期区间集合

            'continued_7_day_up_num'                => 0,### 持续7天上涨天数
            'continued_7_day_up_uad_range'          => [],### 持续7天上涨的涨幅区间
            'continued_7_day_up_active_date_sets'   => [],### 持续7天上涨的历史日期区间集合

            'continued_8_day_up_num'                => 0,### 持续8天上涨天数
            'continued_8_day_up_uad_range'          => [],### 持续8天上涨的涨幅区间
            'continued_8_day_up_active_date_sets'   => [],### 持续8天上涨的历史日期区间集合

            'continued_9_day_up_num'                => 0,### 持续9天上涨天数
            'continued_9_day_up_uad_range'          => [],### 持续9天上涨的涨幅区间
            'continued_9_day_up_active_date_sets'   => [],### 持续9天上涨的历史日期区间集合

            'continued_gt9_day_up_num'              => 0,### 持续上涨超过9天的天数
            'continued_gt9_day_up_uad_range'        => [],### 持续上涨超过9天的涨幅区间
            'continued_gt9_day_up_active_date_sets' => [],### 持续上涨超过9天的历史日期区间集合

            'next_day_dw_num'                       => 0,### 第二天下跌天数
            'next_day_dw_uad_range'                 => [],### 第二天下跌的跌幅区间
            'next_day_dw_active_date_sets'          => [],### 第二天下跌的历史日期区间集合

            'continued_2_day_dw_num'                => 0,### 持续2天下跌天数
            'continued_2_day_dw_uad_range'          => [],### 持续2天下跌的跌幅区间
            'continued_2_day_dw_active_date_sets'   => [],### 持续2天下跌的历史日期区间集合

            'continued_3_day_dw_num'                => 0,### 持续3天下跌天数
            'continued_3_day_dw_uad_range'          => [],### 持续3天下跌的跌幅区间
            'continued_3_day_dw_active_date_sets'   => [],### 持续3天下跌的历史日期区间集合

            'continued_4_day_dw_num'                => 0,### 持续4天下跌天数
            'continued_4_day_dw_uad_range'          => [],### 持续4天下跌的跌幅区间
            'continued_4_day_dw_active_date_sets'   => [],### 持续4天下跌的历史日期区间集合

            'continued_5_day_dw_num'                => 0,### 持续5天下跌天数
            'continued_5_day_dw_uad_range'          => [],### 持续5天下跌的跌幅区间
            'continued_5_day_dw_active_date_sets'   => [],### 持续5天下跌的历史日期区间集合

            'continued_6_day_dw_num'                => 0,### 持续6天下跌天数
            'continued_6_day_dw_uad_range'          => [],### 持续6天下跌的跌幅区间
            'continued_6_day_dw_active_date_sets'   => [],### 持续6天下跌的历史日期区间集合

            'continued_7_day_dw_num'                => 0,### 持续7天下跌天数
            'continued_7_day_dw_uad_range'          => [],### 持续7天下跌的跌幅区间
            'continued_7_day_dw_active_date_sets'   => [],### 持续7天下跌的历史日期区间集合

            'continued_8_day_dw_num'                => 0,### 持续8天下跌天数
            'continued_8_day_dw_uad_range'          => [],### 持续8天下跌的跌幅区间
            'continued_8_day_dw_active_date_sets'   => [],### 持续8天下跌的历史日期区间集合

            'continued_9_day_dw_num'                => 0,### 持续9天下跌天数
            'continued_9_day_dw_uad_range'          => [],### 持续9天下跌的跌幅区间
            'continued_9_day_dw_active_date_sets'   => [],### 持续9天下跌的历史日期区间集合

            'continued_gt9_day_dw_num'              => 0,### 持续下跌超过9天的天数
            'continued_gt9_day_dw_uad_range'        => [],### 持续下跌超过9天的跌幅区间
            'continued_gt9_day_dw_active_date_sets' => [],### 持续下跌超过9天的历史日期区间集合
        ];
    
        $info['lt=-70'] = $common_key_and_val;## 偏离率<=-70

        for ($i=-35; $i <= 35; $i++) { 

            if( $i===0 ) continue;
            
            if( $i<0 ){

                $_info_key = '>'.($i*2).'_<='.(($i+1)*2);
            }else{
                $_info_key = '>'.(($i-1)*2).'_<='.($i*2);
            }

            $info[$_info_key] = $common_key_and_val;
        }

        $info['gt70'] = $common_key_and_val;## 偏离率>70

        return $info;
    }

    /**
     * 生成偏移率规则和区间
     */
    public function mkPianyilvRuleAndIntervals(){
    
        /// 初始化参数
        $now                    = time();
        $interval_model         = new IntervalsModel;
        $statistics_rules_model = new StatisticsRulesModel;

        /// 新增规则，已经新增则返回规则表id
        $rules = [4, 5, 6, 7, 8, 9, 10, 11];

        foreach( $rules as $_r){
        
            $statistics_rules__id = $statistics_rules_model->getId($_r);

            /// 组装数据
            $_insert = [
                ['b_interval'=>70, 'is_equal_to_b_interval'=>0, 'e_interval'=>111, 'is_equal_to_e_interval'=>0, 'statistics_rules__id'=>$statistics_rules__id],
                ['b_interval'=>-111, 'is_equal_to_b_interval'=>0, 'e_interval'=>-70, 'is_equal_to_e_interval'=>1, 'statistics_rules__id'=>$statistics_rules__id]
            ];

            for ($i=-35; $i <= 35; $i++) { 

                if( $i===0 ) continue;
                
                if( $i<0 ){

                    $_insert[] = ['b_interval'=>$i*2, 'is_equal_to_b_interval'=>0, 'e_interval'=>($i+1)*2, 'is_equal_to_e_interval'=>1, 'statistics_rules__id'=>$statistics_rules__id];
                }else{
                    $_insert[] = ['b_interval'=>($i-1)*2, 'is_equal_to_b_interval'=>0, 'e_interval'=>$i*2, 'is_equal_to_e_interval'=>1, 'statistics_rules__id'=>$statistics_rules__id];
                }
            }

            /// 无数据则添加
            foreach( $_insert as $row){
                $condition = Fun::tb__kv2condition($row);
                $_has_row = $interval_model->select('id')->where($condition)->find();

                if( !$_has_row ){
                    
                    $row['created_time'] = $now;
                    $interval_model->insert($row)->exec();
                }
            }
        }
        
    }

}
