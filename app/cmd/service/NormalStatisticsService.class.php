<?php

namespace cmd\service;
use \cmd\service\GupiaoCommonService;
use model\DateRecordModel;
use model\SharesDetailsBydayModel;
use model\SharesModel;
use model\XingaoAndXindiModel;

class NormalStatisticsService
{

    /**
     * 补充股票每日一年新高数据
     */
    public function yearXingao(){

        ///初始化参数
        $now                        = time();
        $shares_model               = new SharesModel;
        $date_record_model          = new DateRecordModel;
        $xingao_and_xindi_model     = new XingaoAndXindiModel;
        $shares_details_byday_model = new SharesDetailsBydayModel;
    
        /// 获取所有股票数据
        $shares = $shares_model->select('
            id,
            code,
            title,
            code,
            company_name
        ')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($shares);
        foreach( $shares as $k=>$v){
            // $v['code'] = '000014';
            // $v['id'] = '1574';
            $percent    = number_format(($dividend/$divisor)*100, 4) . '%';
            $shares__id = $v['id'];
        
            /// 获取当前股票历史数据
            $this_share_details_byday = $shares_details_byday_model->select('
                id,
                day_max_price,
                day_end_price,
                has_statistics_year_xingao,
                active_date,
                active_date_timestamp
            ')->where(['shares__id', $shares__id])
            ->orderby('active_date_timestamp asc')
            ->get();

            if( empty($this_share_details_byday) ) continue;

            $passed_share_history_info = [];
            // $_c = 0;
            foreach( $this_share_details_byday as $detail_k=>$detail){

                /// 初始化参数
                $now_day_max_price_ori =    (empty($detail['day_max_price'])||$detail['day_max_price']=='0.0') ? 
                                            ( (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 0 : $detail['day_end_price'] ) : 
                                            $detail['day_max_price'];
                                            
                $now_day_max_price =    (empty($detail['day_max_price'])||$detail['day_max_price']=='0.0') ? 
                                        (
                                            (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 
                                            0 : 
                                            intval($detail['day_end_price']*10000000)
                                        ) : 
                                        intval($detail['day_max_price'] * 10000000);

                /// 已统计的跳过
                if( $detail['has_statistics_year_xingao']==1 ){
                
                    # 超过一年的，去除第一个
                    if( count($passed_share_history_info)>=238 ){## 一年365天 减去20个假日 减去周末约240个工作日 之所以是238，是因为接下来会将当天价格添加进去，那么下一轮将会取得239个中的
                        
                        array_shift($passed_share_history_info);
                    }

                    $passed_share_history_info[] = $now_day_max_price;
                    continue;
                }

                /// 修改
                $_upd = [
                    'is_year_xingao'                => 0,
                    'has_statistics_year_xingao'    => 1
                ];

                if( !empty($passed_share_history_info) ){

                    $pshi_bak = $passed_share_history_info;
                    # 倒序    
                    rsort($pshi_bak);

                    # 数据目标转整形
                    $history_year_max_price = $pshi_bak[0];

                    # 创1年新高
                    if( $now_day_max_price>$history_year_max_price ){
                    
                        $_upd['is_year_xingao'] = 1;
                    }

                    # 超过一年的，去除第一个
                    if( count($passed_share_history_info)>=238 ){## 一年365天 减去20个假日 减去周末约240个工作日  之所以是238，是因为接下来会将当天价格添加进去，那么下一轮将会取得239个中的
                        
                        array_shift($passed_share_history_info);
                    }

                }else{
                    $_upd['is_year_xingao'] = 1;
                }

                # 将当前价格添加进目标历史集合中
                $passed_share_history_info[] = $now_day_max_price;

                # 更新
                ## date_record
                $_condi             = ['active_date_timestamp', $detail['active_date_timestamp']];
                $has_date_record    = $date_record_model->select('id')->where($_condi)->find();

                if( empty($has_date_record) ){
                
                    $_dr_data = [
                        'active_date'           => $detail['active_date'],
                        'active_date_timestamp' => $detail['active_date_timestamp'],
                        'created_time'          => $now,
                    ];

                    $re = $date_record_model->insert($_dr_data)->exec();
                    if( !$re ){
                        echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                        continue;
                    }else{
                        $date_record__id = $date_record_model->last_insert_id();
                    }

                }else{
                    $date_record__id = $has_date_record['id'];
                }

                ## xingao_and_xindi
                $_condi = [
                    ['date_record__id', $date_record__id],
                    ['shares__id', $shares__id],
                    ['type', 1]
                ];
                $has_xingao_and_xindi = $xingao_and_xindi_model->select('id')->where($_condi)->find();

                if( empty($has_xingao_and_xindi)&&$_upd['is_year_xingao']==1 ){
                
                    $_xax_data = [
                        'date_record__id'   => $date_record__id,
                        'shares__id'        => $shares__id,
                        'title'             => $v['title'],
                        'code'              => $v['code'],
                        'company_name'      => $v['company_name'],
                        'price'             => $now_day_max_price_ori,
                        'type'              => 1,
                        'created_time'      => $now
                    ];

                    $re = $xingao_and_xindi_model->insert($_xax_data)->exec();
                    if( !$re ){
                        echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                        continue;
                    }
                }

                $re = $shares_details_byday_model->update($_upd)->where(['id', $detail['id']])->exec();
                if( !$re ){
                    echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                    continue;
                }
            }

            echo 'code: '.$v['code'].'；完成：'. $percent . PHP_EOL;
            $dividend++;
        }
    }

    /**
     * 补充股票每日一年新高数据
     */
    protected $passed_share_history_info          = [];
    protected $passed_share_history_info_3month   = [];
    protected $passed_share_history_info_month    = [];
    protected $passed_share_history_info_5day     = [];

    protected function passedShareHistory($now_day_min_price){
    
        if( !empty($this->passed_share_history_info) ){

            # 超过一年的，去除第一个
            if( count($this->passed_share_history_info)>=238 ){## 一年365天 减去20个假日 减去周末约240个工作日  之所以是238，是因为接下来会将当天价格添加进去，那么下一轮将会取得239个中的最低和下一轮的当前价比较，正好是238+当前一轮价格+下一轮价格=240
                            
                array_shift($this->passed_share_history_info);
            }

            # 超过一季的，去除第一个
            if( count($this->passed_share_history_info_3month)>=64 ){## 一个季度按66个工作日计
                
                array_shift($this->passed_share_history_info_3month);
            }

            # 超过一月的，去除第一个
            if( count($this->passed_share_history_info_month)>=20 ){## 一月按22个工作日计
                
                array_shift($this->passed_share_history_info_month);
            }

            # 超过1周的，去除第一个
            if( count($this->passed_share_history_info_5day)>=3 ){## 一周按5个工作日计
                
                array_shift($this->passed_share_history_info_5day);
            }
        }

        # 将当前价格添加进目标历史集合中
        $this->passed_share_history_info[]          = $now_day_min_price;
        $this->passed_share_history_info_3month[]   = $now_day_min_price;
        $this->passed_share_history_info_month[]    = $now_day_min_price;
        $this->passed_share_history_info_5day[]     = $now_day_min_price;
    }

    protected function hasXindi($date_record__id, $shares__id, $now_day_min_price, $info, $type, $is_xindi=1){

        /// 初始化参数
        $now                        = time();
        $xingao_and_xindi_model     = new XingaoAndXindiModel;

        $_condi = [
            ['date_record__id', $date_record__id],
            ['shares__id', $shares__id],
            ['type', $type]
        ];
        $has_xingao_and_xindi = $xingao_and_xindi_model->select('id')->where($_condi)->find();

        if( empty($has_xingao_and_xindi)&&$is_xindi==1 ){
        
            return [
                'date_record__id'   => $date_record__id,
                'shares__id'        => $shares__id,
                'title'             => $info['title'],
                'code'              => $info['code'],
                'company_name'      => $info['company_name'],
                'price'             => $now_day_min_price,
                'type'              => $type,
                'created_time'      => $now
            ];
        }

        return [];
    }

    public function yearXindi(){

        /// 初始化参数
        $now                        = time();
        $shares_model               = new SharesModel;
        $date_record_model          = new DateRecordModel;
        $xingao_and_xindi_model     = new XingaoAndXindiModel;
        $shares_details_byday_model = new SharesDetailsBydayModel;
    
        /// 获取所有股票数据
        $shares = $shares_model->select('
            id,
            code,
            title,
            code,
            company_name
        ')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($shares);
        foreach( $shares as $k=>$v){

            // $v['code'] = '000014';
            // $v['id'] = '1574';
            $percent    = number_format(($dividend/$divisor)*100, 4) . '%';
            $shares__id = $v['id'];
        
            /// 获取当前股票历史数据
            $this_share_details_byday = $shares_details_byday_model->select('
                id,
                day_min_price,
                day_end_price,
                has_statistics_year_xindi,
                has_statistics_month_xindi,
                has_statistics_5day_xindi,
                has_statistics_3month_xindi,
                active_date,
                active_date_timestamp
            ')
            ->where(['shares__id', $shares__id])
            ->orderby('active_date_timestamp asc')
            ->get();

            if( empty($this_share_details_byday) ) continue;

            // $_c = 0;
            foreach( $this_share_details_byday as $detail_k=>$detail){

                /// 初始化参数
                $now_day_min_price_ori =    ( empty($detail['day_min_price'])||$detail['day_min_price']=='0.0' ) ? 
                                            ( (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 0 : $detail['day_end_price']) : 
                                            $detail['day_min_price'];

                $now_day_min_price =    (empty($detail['day_min_price'])||$detail['day_min_price']=='0.0') ? 
                                        (
                                            (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 
                                            0 : 
                                            intval($detail['day_end_price']*10000000)
                                        ) : 
                                        intval($detail['day_min_price'] * 10000000);

                /// 已统计的跳过
                if( 
                    $detail['has_statistics_year_xindi']==1 &&
                    $detail['has_statistics_3month_xindi']==1 &&
                    $detail['has_statistics_month_xindi']==1 &&
                    $detail['has_statistics_5day_xindi']==1
                ){
                    $this->passedShareHistory($now_day_min_price);
                    continue;
                }

                /// 修改
                $_upd = [
                    'is_year_xindi'                => 0,
                    'has_statistics_year_xindi'    => 1,
                    'is_3month_xindi'              => 0,
                    'has_statistics_3month_xindi'  => 1,
                    'is_month_xindi'               => 0,
                    'has_statistics_month_xindi'   => 1,
                    'is_5day_xindi'                => 0,
                    'has_statistics_5day_xindi'    => 1
                ];

                # date_record是否已有对应的时间数据
                $_condi             = ['active_date_timestamp', $detail['active_date_timestamp']];
                $has_date_record    = $date_record_model->select('id')->where($_condi)->find();

                if( empty($has_date_record) ){
                
                    $_dr_data = [
                        'active_date'           => $detail['active_date'],
                        'active_date_timestamp' => $detail['active_date_timestamp'],
                        'created_time'          => $now
                    ];

                    $re = $date_record_model->insert($_dr_data)->exec();
                    if( !$re ){
                        echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                        $this->passedShareHistory($now_day_min_price);
                        continue;
                    }else{

                        $date_record__id = $date_record_model->last_insert_id();
                    }

                }else{
                    $date_record__id = $has_date_record['id'];
                }

                $_xindi_data = [];
                if( !empty($this->passed_share_history_info) ){/// 一年的数据存在，则其他小于一年的数据都存在

                    $pshi_bak           = $this->passed_share_history_info;
                    $pshi_bak_3month    = $this->passed_share_history_info_3month;
                    $pshi_bak_month     = $this->passed_share_history_info_month;
                    $pshi_bak_5day      = $this->passed_share_history_info_5day;

                    # 正序    
                    sort($pshi_bak);
                    sort($pshi_bak_3month);
                    sort($pshi_bak_month);
                    sort($pshi_bak_5day);

                    # 取出最小
                    $history_year_min_price     = $pshi_bak[0];
                    $history_3month_min_price   = $pshi_bak_3month[0];
                    $history_month_min_price    = $pshi_bak_month[0];
                    $history_5day_min_price     = $pshi_bak_5day[0];

                    # 创1年新低
                    // var_dump($pshi_bak);
                    // var_dump($now_day_min_price);
                    // var_dump($history_year_min_price);
                    // var_dump($now_day_min_price<$history_year_min_price);
                    /* echo '-----------------'.PHP_EOL;
                    if( $detail_k==10 ){
                        exit;
                    } */
                    if( $now_day_min_price<$history_year_min_price ){

                        if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 5) ){
                            $_xindi_data[] = $_this_xindi_data;
                        }
                        $_upd['is_year_xindi'] = 1;
                    }

                    # 创1个季度新低
                    if( $now_day_min_price<$history_3month_min_price ){
                    
                        if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 4) ){
                            $_xindi_data[] = $_this_xindi_data;
                        }
                        $_upd['is_3month_xindi'] = 1;
                    }

                    # 创1个月新低
                    if( $now_day_min_price<$history_month_min_price ){
                    
                        if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 3) ){
                            $_xindi_data[] = $_this_xindi_data;
                        }
                        $_upd['is_month_xindi'] = 1;
                    }

                    # 创5日新低
                    if( $now_day_min_price<$history_5day_min_price ){
                    
                        if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 2) ){
                            $_xindi_data[] = $_this_xindi_data;
                        }
                        $_upd['is_5day_xindi'] = 1;
                    }
                }else{
                    $_upd['is_year_xindi']      = 1;
                    $_upd['is_3month_xindi']    = 1;
                    $_upd['is_month_xindi']     = 1;
                    $_upd['is_5day_xindi']      = 1;

                    if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 5) ){
                        $_xindi_data[] = $_this_xindi_data;
                    }
                    if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 4) ){
                        $_xindi_data[] = $_this_xindi_data;
                    }
                    if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 3) ){
                        $_xindi_data[] = $_this_xindi_data;
                    }
                    if( $_this_xindi_data = $this->hasXindi($date_record__id, $shares__id, $now_day_min_price_ori, $v, 2) ){
                        $_xindi_data[] = $_this_xindi_data;
                    }
                }

                # 将当前价格添加进目标历史集合中
                $this->passedShareHistory($now_day_min_price);

                # xingao_and_xindi
                if( !empty($_xindi_data) ){
                
                    $re = $xingao_and_xindi_model->fields(array_keys($_xindi_data[0]))->insert($_xindi_data)->exec();
                    if( !$re ){
                        echo '新增失败！--》》' . $detail['id'] . PHP_EOL;
                        continue;
                    }
                }

                # 更新
                $re = $shares_details_byday_model->update($_upd)->where(['id', $detail['id']])->exec();
                if( !$re ){
                    echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                    continue;
                }
            }

            echo 'code: '.$v['code'].'；完成：'. $percent . PHP_EOL;
            $dividend++;
        }
    }
}
