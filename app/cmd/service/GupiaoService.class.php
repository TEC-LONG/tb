<?php

namespace cmd\service;
use \Err;
use \Fun;
use \TB;

class GupiaoService
{

    protected $gupiao_b_time = '19901201';

    /**
     * 新增股票
     */
    public function gupiaoAdd(){

        /*
        深圳证券交易市场的股票代码以000开头的是主板、3开头的是创业板，上海股票交易市场的股票代码是以6开头的，全部的上海股票都是主板

        1、创业板 创业板的代码是300打头的股票代码；
        2、沪市A股 沪市A股的代码是以600、601或603打头；
        3、沪市B股 沪市B股的代码是以900打头；
        4、深市A股 深市A股的代码是以000打头；
        5、中小板 中小板的代码是002打头；
        6、深圳B股 深圳B股的代码是以200打头；
        7、新股申购 沪市新股申购的代码是以730打头 深市新股申购的代码与深市股票买卖代码一样；
        8、配股代码 沪市以700打头，深市以080打头 权证，沪市是580打头 深市是031打头。
        9、 400开头的股票是三板市场股票。
        10、科创板以688打头；

        1、沪市A股的代码是以600或601
        沪市B股的代码是以900
        沪市新股申购的代码是以730
        权证，沪市是580开头
        配股代码沪市以700开头，
        2、深市A股的代码是以000
        深圳B股的代码是以200
        深市新股申购的代码与深市股票买卖代码一样
        配股代码，深市以080开头
        权证，深市是031开头
        */
        $head_num = [
            'h_A' => [0, ['600', '601', '603', '688']],
            's_A' => [1, ['000', '002', '300']]
        ];
            
        foreach( $head_num as $v){# $v ==> [0, ['600', '601', '603', '688']]
        
            foreach( $v[1] as $h_num){# $h_num ==> 600
            
                for ($i=0; $i <= 999; $i++) { 
                    
                    $tmp_num = str_pad($i, 3, '0', STR_PAD_LEFT);# 不够三位数前面补零值，如：002
                    #                 600       002
                    $tmp_whole_num = $h_num . $tmp_num;
        
                    $this->gupiaoInDB($v[0], $tmp_whole_num, $this->gupiao_b_time, date('Ymd'));
                }
            }
        }
    }

    /**
     * 请求接口获取数据
     */
    protected function getCurlData($type, $code, $start, $end){
    
        $getfields  = 'TCLOSE;HIGH;LOW;TOPEN;LCLOSE;CHG;PCHG;VOTURNOVER;VATURNOVER';
        $url        = 'http://quotes.money.163.com/service/chddata.html?code='.$type.$code.'&start='.$start.'&end='.$end.'&fields='.$getfields;

        /// 转码
        $content    = $this->sendCurl($url);
        $content    = mb_convert_encoding($content, "UTF-8", "GBK");

        if( strlen($content)<=121 ){
            return false;
        }

        return $content;
    }

    /**
     * 剥离拆分数据
     */
    protected function splitData($content){
        /// 剥离标题部分
        preg_match('/^.*成交金额\s*/', $content, $matches);
        $content = str_replace($matches[0], '', $content);

        /// 拆分每一行内容
        preg_match_all('/\d{4}-\d{2}-\d{2}(.*?)\s+/', $content, $matches);
        $original_data = $matches[0];

        return $original_data;
    }

    /**
     * 链接type 转 数据表type 值
     * linktype 0=沪市  1=深市
     */
    protected function linktype2tbtype($linktype){
    
        return $linktype==1 ? $linktype : ($linktype==0 ? 2 : 0);
    }

    /**
     * 数据表type 转 链接type 值
     * linktype 0=沪市  1=深市
     */
    protected function tbtype2linktype($tbtype){
    
        return $tbtype==1 ? $tbtype : ($tbtype==2 ? 0 : 100);
    }

    /**
     * 新增股票数据入库
     */
    protected function gupiaoInDB($type, $code, $start, $end){
    
        /// 请求接口获取数据
        $content = $this->getCurlData($type, $code, $start, $end);
        if( $content===false ){
            echo  '无数据-01! type: '.$type.'; code: '.$code . PHP_EOL;
            return false;
        }

        /// 剥离拆分数据
        $original_data = $this->splitData($content);

        # 初始化参数  日期,股票代码,名称,收盘价,最高价,最低价,开盘价,前收盘,涨跌额,涨跌幅,成交量,成交金额
        $shares_type    = $this->linktype2tbtype($type);
        $first_row_arr  = isset($original_data[0]) ? explode(',', $original_data[0]) : [];
        $shares_title   = isset($first_row_arr[2]) ? trim($first_row_arr[2]) : '';

        if( empty($first_row_arr) ){
            echo '无数据-02! type: ' . $type . '; code: ' . $code . PHP_EOL;
            return false;
        }

        /// 录入shares表数据
        # 不存在才录入
        $tmp_condition = [
            ['title', $shares_title],
            ['code', $code],
            ['type', $shares_type]
        ];
        $has_row = TB::table('shares')->select('*')
        ->where($tmp_condition)
        ->find();

        if( $has_row ){
            
            echo '数据已存在！title: ' . $shares_title . '; type: ' . $type . '; code: ' . $code . PHP_EOL;
            return false;
        }else{

            $main_data = [
                'title' => $shares_title,
                'code'  => $code,
                'type'  => $shares_type,#`type` tinyint NOT NULL DEFAULT 0 COMMENT '股票类型，0=未知；1=深市；2=沪市；',
                'type_unknow_record'  => $shares_type==0 ? $type : '',
                'created_time'  => time()
            ];
        
            $re = TB::table('shares')
            ->insert($main_data)
            ->exec();
        
            if( !$re ){
                echo '录入主表数据失败! main_data: ' . serialize($main_data) . PHP_EOL;
                return false;
            }
        }
    }

    /**
     * curl
     */
    protected function sendCurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
     
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

    /**
     * 更新shares_details_byday原始数据
     */
    public function updateOriginal(){
    
        $end_time = time();

        /// 获取shares所有id
        $codes = TB::table('shares')->select('id, code, type, sdb_last_update_time')->where(1)->get();

        foreach( $codes as $shares_row){

            $type       = $this->tbtype2linktype($shares_row['type']);
            $code       = $shares_row['code'];
            $start      = empty($shares_row['sdb_last_update_time']) ? $this->gupiao_b_time : date('Ymd', $shares_row['sdb_last_update_time']);
            // $end        = date('Ymd', $end_time-86400);
            $end        = date('Ymd', $end_time);
            $shares_id  = $shares_row['id'];

            /// 请求接口获取数据
            $content = $this->getCurlData($type, $code, $start, $end);
            if( $content===false ){
                echo  '无数据-01! type: '.$type.'; code: '.$code . PHP_EOL;
                continue;
            }

            /// 剥离拆分数据
            $original_data = $this->splitData($content);

            # 初始化参数  日期,股票代码,名称,收盘价,最高价,最低价,开盘价,前收盘,涨跌额,涨跌幅,成交量,成交金额
            $first_row_arr  = isset($original_data[0]) ? explode(',', $original_data[0]) : [];
            if( empty($first_row_arr) ){
                echo '无数据-02! type: ' . $type . '; code: ' . $code . PHP_EOL;
                continue;
            }

            /// 录入shares_details_byday表数据
            $data = [];
            foreach( $original_data as $k=>$v){

                /// 拆分原始数据
                $this_row = explode(',', $v);
                
                $this_row = array_filter($this_row, function($elem){
                    return trim($elem);
                });

                /// 没有日期则跳过
                if( 
                    !isset($this_row[0]) ||
                    empty($this_row[0])
                ){
                    continue;
                }

                /// detail数据存在则跳过
                $check_row = [
                    ['shares__id', $shares_id],
                    ['active_date_timestamp', strtotime($this_row[0].' 15:00:00')]
                ];

                $has_row = TB::table('shares_details_byday')->select('id')->where($check_row)->find();

                if( !empty($has_row) ){
                    continue;
                }

                /// 组装数据
                $data[$k] = [
                    'shares__id'             => $shares_id,
                    'original_data'         => $v,
                    'active_date'           => (isset($this_row[0])&&!empty($this_row[0]) ) ? $this_row[0] : '',
                    'day_start_price'       => (isset($this_row[6])&&!empty($this_row[6]) ) ? $this_row[6] : '',
                    'day_end_price'         => (isset($this_row[3])&&!empty($this_row[3]) ) ? $this_row[3] : '',
                    'day_max_price'         => (isset($this_row[4])&&!empty($this_row[4]) ) ? $this_row[4] : '',
                    'day_min_price'         => (isset($this_row[5])&&!empty($this_row[5]) ) ? $this_row[5] : '',
                    'last_day_end_price'    => (isset($this_row[7])&&!empty($this_row[7]) ) ? $this_row[7] : '',
                    'uad_price'             => (isset($this_row[8])&&!empty($this_row[8]) ) ? $this_row[8] : '',
                    'uad_range'             => (isset($this_row[9])&&!empty($this_row[9]) ) ? $this_row[9] : '',
                    'volume'                => (isset($this_row[10])&&!empty($this_row[10]) ) ? $this_row[10] : '',
                    'transaction_amount'    => (isset($this_row[11])&&!empty($this_row[11]) ) ? $this_row[11] : '',
                    'step'                  => 1,
                    'created_time'          => time(),
                    'active_date_timestamp' => (isset($this_row[0])&&!empty($this_row[0]) ) ? strtotime($this_row[0] . ' 15:00:00') : 0
                ];
            }

            if( empty($data) ){
                echo 'type: '.$type.'; code: '.$code . '已是最新!' . PHP_EOL;
                continue;
            }

            $re = TB::table('shares_details_byday')
            ->fields('
                shares__id,
                original_data,
                active_date,   
                day_start_price, 
                day_end_price,
                day_max_price,
                day_min_price,
                last_day_end_price,
                uad_price,
                uad_range,
                volume,
                transaction_amount,
                step,
                created_time,
                active_date_timestamp
            ')
            ->insert($data)
            ->exec();

            if( !$re ){
                echo '录入子表数据失败! original_data: ' . serialize($original_data) . PHP_EOL;
                continue;
            }

            /// 更新主表数据
            $shares_data = [
                'sdb_last_update_time' => $end_time
            ];

            $re = TB::table('shares')
            ->update($shares_data)
            ->where(['id', '=', $shares_id])
            ->exec();

            if( !$re ){
                echo '更新shares主表失败! original_data: id: ' . $shares_id . '; sdb_last_update_time: ' . $end_time . PHP_EOL;
                continue;
            }
            
            echo 'shares_details_byday更新数据成功! type: '.$type.'; code: '.$code . PHP_EOL;
        }
        return true;
    }

    /**
     * 更新shares表发行日期
     */
    public function updateIssueDate(){
        /// 最大id
        $max_row_id = TB::table('shares')->select('max(id) as id')->where(1)->find();
        $max_row_id = $max_row_id['id'];

        for ($i=$max_row_id; $i>0 ; $i--) { 

            /// 校验数据
            # shares表是否存在此id的数据
            $shares_has_row = TB::table('shares')->select('*')->where(['id', $i])->find();
            if( empty($shares_has_row) ){
                echo '当前id: '.$i.' 在shares表无对应的记录！'.PHP_EOL;
                continue;
            }

            # 是否存在issue_date和issue_date_timestamp数据
            if( 
                !empty($shares_has_row['issue_date']) ||
                !empty($shares_has_row['issue_date_timestamp'])
            ){
                echo 'id为：'.$i.'的记录已经拥有发行日期数据，无需重复更新！'.PHP_EOL;
            }

            /// id最大的数据为最早的数据
            $tmp_where = [
                ['shares_id', $i]
            ];

            $tmp_shares_details_byday_row = TB::table('shares_details_byday')->select('active_date')->where($tmp_where)->orderby('id desc')->find();
            
            if( empty($tmp_shares_details_byday_row) ){
                echo '无关联数据 --》》》' . $i . PHP_EOL;
                continue;
            }

            /// 更新数据
            $tmp_update = [
                'issue_date'            => $tmp_shares_details_byday_row['active_date'],
                'issue_date_timestamp'  => !empty($tmp_shares_details_byday_row['active_date']) ? strtotime($tmp_shares_details_byday_row['active_date'] . ' 15:00:00') : 0
            ];

            $re = TB::table('shares')
            ->update($tmp_update)
            ->where(['id', '=', $i])
            ->exec();

            if( !$re ){
                echo '更新失败！--》》' . $i . PHP_EOL;
            }else{
                echo '更新成功 --》' . $i . PHP_EOL;
            }
        }
    }

    /**
     * 计算均价
     */
    public function maPrice(){

        $now = time();
    
        /// 获取shares所有id
        $ids = TB::table('shares')->select('id')->where(1)->get();

        foreach( $ids as $v){

            /// 获取当前票的所有记录
            $this_shares_details_byday_row = TB::table('shares_details_byday as sdb')->select('
                sdb.id,
                sdb.shares__id,
                sdb.active_date,
                sdb.active_date_timestamp,
                sma.id as sma_id,
                sdb.day_end_price,
                sma.ma5_price,
                sma.ma4_price,
                sma.ma10_price,
                sma.ma9_price,
                sma.ma15_price,
                sma.ma14_price,
                sma.ma20_price,
                sma.ma19_price,
                sma.ma30_price,
                sma.ma29_price,
                sma.ma60_price,
                sma.ma59_price,
                sma.ma120_price,
                sma.ma119_price,
                sma.ma240_price,
                sma.ma239_price
            ')
            ->leftjoin('sdb_statistics_moving_average as sma', 'sma.shares_details_byday__id=sdb.id')
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
                    
                    $ma4_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 4, $last_price);
                    $ma['ma4_price'] = $ma4_price;
                    $ma5_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 5, $last_price, $ma4_price);
                    $ma['ma5_price'] = $ma5_price;

                    $ma9_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 9, $last_price, $ma5_price);
                    $ma['ma9_price'] = $ma9_price;
                    $ma10_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 10, $last_price, $ma9_price);
                    $ma['ma10_price'] = $ma10_price;

                    $ma14_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 14, $last_price, $ma10_price);
                    $ma['ma14_price'] = $ma14_price;
                    $ma15_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 15, $last_price, $ma14_price);
                    $ma['ma15_price'] = $ma15_price;

                    $ma19_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 19, $last_price, $ma15_price);
                    $ma['ma19_price'] = $ma19_price;
                    $ma20_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 20, $last_price, $ma19_price);
                    $ma['ma20_price'] = $ma20_price;

                    $ma29_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 29, $last_price, $ma20_price);
                    $ma['ma29_price'] = $ma29_price;
                    $ma30_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 30, $last_price, $ma29_price);
                    $ma['ma30_price'] = $ma30_price;

                    $ma59_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 59, $last_price, $ma30_price);
                    $ma['ma59_price'] = $ma59_price;
                    $ma60_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 60, $last_price, $ma59_price);
                    $ma['ma60_price'] = $ma60_price;

                    $ma119_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 119, $last_price, $ma60_price);
                    $ma['ma119_price'] = $ma119_price;
                    $ma120_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 120, $last_price, $ma119_price);
                    $ma['ma120_price'] = $ma120_price;

                    $ma239_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 239, $last_price, $ma120_price);
                    $ma['ma239_price'] = $ma239_price;
                    $ma240_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 240, $last_price, $ma239_price);
                    $ma['ma240_price'] = $ma240_price;

                    $ma['shares__id']               = $sdbr_v['shares__id'];
                    $ma['shares_details_byday__id'] = $sdbr_v['id'];
                    $ma['active_date']              = $sdbr_v['active_date'];
                    $ma['active_date_timestamp']    = $sdbr_v['active_date_timestamp'];
                    $ma['ma_price_time']            = $now;
                    $ma['created_time']             = $now;

                }else{/// 存在则更新

                    $flag = 2;

                    // if( empty($sdbr_v['ma4_price']) ){
                        $ma4_price          = $this->getAveragePrice($k, $this_shares_details_byday_row, 4, $last_price);
                        $ma['ma4_price']    = $ma4_price;
                    // }else{
                    //     $ma4_price = $sdbr_v['ma4_price'];
                    // }

                    // if( empty($sdbr_v['ma5_price']) ){
                        $ma5_price          = $this->getAveragePrice($k, $this_shares_details_byday_row, 5, $last_price, $ma4_price);
                        $ma['ma5_price']    = $ma5_price;
                    // }else{
                    //     $ma5_price = $sdbr_v['ma5_price'];
                    // }

                    // if( empty($sdbr_v['ma9_price']) ){
                        $ma9_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 9, $last_price, $ma5_price);
                        $ma['ma9_price']   = $ma9_price;
                    // }else{
                        // $ma9_price = $sdbr_v['ma9_price'];
                    // }

                    // if( empty($sdbr_v['ma10_price']) ){
                        $ma10_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 10, $last_price, $ma9_price);
                        $ma['ma10_price']   = $ma10_price;
                    // }else{
                        // $ma10_price = $sdbr_v['ma10_price'];
                    // }

                    // if( empty($sdbr_v['ma14_price']) ){
                        $ma14_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 14, $last_price, $ma10_price);
                        $ma['ma14_price']   = $ma14_price;
                    // }else{
                    //     $ma14_price = $sdbr_v['ma14_price'];
                    // }

                    // if( empty($sdbr_v['ma15_price']) ){
                        $ma15_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 15, $last_price, $ma14_price);
                        $ma['ma15_price']   = $ma15_price;
                    // }else{
                    //     $ma15_price = $sdbr_v['ma15_price'];
                    // }

                    // if( empty($sdbr_v['ma19_price']) ){
                        $ma19_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 19, $last_price, $ma15_price);
                        $ma['ma19_price']   = $ma19_price;
                    // }else{
                    //     $ma19_price = $sdbr_v['ma19_price'];
                    // }

                    // if( empty($sdbr_v['ma20_price']) ){
                        $ma20_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 20, $last_price, $ma19_price);
                        $ma['ma20_price']   = $ma20_price;
                    // }else{
                    //     $ma20_price = $sdbr_v['ma20_price'];
                    // }

                    // if( empty($sdbr_v['ma29_price']) ){
                        $ma29_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 29, $last_price, $ma20_price);
                        $ma['ma29_price']   = $ma29_price;
                    // }else{
                    //     $ma29_price = $sdbr_v['ma29_price'];
                    // }

                    // if( empty($sdbr_v['ma30_price']) ){
                        $ma30_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 30, $last_price, $ma29_price);
                        $ma['ma30_price']   = $ma30_price;
                    // }else{
                    //     $ma30_price = $sdbr_v['ma30_price'];
                    // }

                    // if( empty($sdbr_v['ma59_price']) ){
                        $ma59_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 59, $last_price, $ma30_price);
                        $ma['ma59_price']   = $ma59_price;
                    // }else{
                    //     $ma59_price = $sdbr_v['ma59_price'];
                    // }

                    // if( empty($sdbr_v['ma60_price']) ){
                        $ma60_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 60, $last_price, $ma59_price);
                        $ma['ma60_price']   = $ma60_price;
                    // }else{
                    //     $ma60_price = $sdbr_v['ma60_price'];
                    // }

                    // if( empty($sdbr_v['ma119_price']) ){
                        $ma119_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 119, $last_price, $ma60_price);
                        $ma['ma119_price']  = $ma119_price;
                    // }else{
                    //     $ma119_price = $sdbr_v['ma119_price'];
                    // }

                    // if( empty($sdbr_v['ma120_price']) ){
                        $ma120_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 120, $last_price, $ma119_price);
                        $ma['ma120_price']  = $ma120_price;
                    // }else{
                    //     $ma120_price = $sdbr_v['ma120_price'];
                    // }

                    // if( empty($sdbr_v['ma239_price']) ){
                        $ma239_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 239, $last_price, $ma120_price);
                        $ma['ma239_price']  = $ma239_price;
                    // }else{
                    //     $ma239_price = $sdbr_v['ma239_price'];
                    // }

                    // if( empty($sdbr_v['ma240_price']) ){
                        $ma240_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 240, $last_price, $ma239_price);
                        $ma['ma240_price']  = $ma240_price;
                    // }else{
                    //     $ma240_price = $sdbr_v['ma240_price'];
                    // }

                    if( !empty($ma) ){
                    
                        $ma['ma_price_time'] = $now;
                    }
                }

                if( empty($ma) ) continue;

                if( $flag==1 ){/// 新增
                
                    $re = TB::table('sdb_statistics_moving_average')
                    ->insert($ma)
                    ->exec();

                    if( !$re ){
                        echo '新增失败！--》》shares_details_byday表id: ' . $sdbr_v['id'] . PHP_EOL;
                    }else{
                        $msg = '新增成功 --》shares_details_byday表id: ' . $sdbr_v['id'] . ' --》';
                        $arr = [];
                        foreach( $ma as $msg_k=>$msg_v){
                        
                            $arr[] =  $msg_k . ':' . $msg_v;
                        }
                        echo $msg . implode(' | ', $arr) . PHP_EOL;
                    }

                }else {/// 更新
                    
                    $re = TB::table('sdb_statistics_moving_average')
                    ->update($ma)
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
        }
    }

    /**
     * 计算均价
     */
    protected function getAveragePrice($k, $data, $count, &$pre, $last_ma_price=''){

        if( 
            $last_ma_price==='none' &&
            $count!=4
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

        $now = time();
    
        /// 获取shares所有id
        $ids = TB::table('shares')->select('id, title, code')->where(1)->get();

        $dividend   = 1;
        $divisor    = count($ids);
        foreach( $ids as $v){

            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            /// 获取当前票的所有记录
            $this_shares_details_byday_row = TB::table('shares_details_byday as sdb')->select('
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
            ->leftjoin('sdb_statistics_moving_average as sma', 'sma.shares_details_byday__id=sdb.id')
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
                    // var_dump($now_ma_price);
                    // var_dump($next_ma_price);

                    $ma_diff_price  = $now_ma_price-$next_ma_price;
                    $numb1          = ($ma_diff_price/$next_ma_price)*100;
                    $numb2          = 10;
                    $angle          = rad2deg(atan($numb1/$numb2));# red2deg弧度转角度
    
                    // var_dump($angle);
                    // echo '-------------'.PHP_EOL;
                    $ma_angle[$ma_num.'_angle'] = $angle;
                }

                if( empty($ma_angle) ) continue;

                // $ma_angle['ma_angle_time'] = $now;

                # 更新数据
                $re = TB::table('sdb_statistics_moving_average')
                ->update($ma_angle)
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
}
