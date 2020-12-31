<?php

namespace cmd\service;
use \Err;
use \Fun;
use \TB;

class GupiaoService
{

    protected $gupiao_b_time = '19901201';

    protected $dividend = null;
    protected $divisor  = null;
    protected $percent  = null;

    /**
     * 新增股票
     */
    public function gupiaoAdd($road){

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
        
                    $this->gupiaoInDB($road, $v[0], $tmp_whole_num, $this->gupiao_b_time, date('Ymd'));
                }
            }
        }
    }

    /**
     * 请求接口获取数据
     */
    protected function getCurlData($road, $type, $code, $start, $end){

        if( $road==0 ){
        
            /**
             * 自定义列可定义TCLOSE收盘价 ;HIGH最高价;LOW最低价;TOPEN开盘价;LCLOSE前收盘价;CHG涨跌额;PCHG涨跌幅;TURNOVER换手率;VOTURNOVER成交量;VATURNOVER成交金额;TCAP总市值;MCAP流通市值这些值
             */
            $getfields  = 'TCLOSE;HIGH;LOW;TOPEN;LCLOSE;CHG;PCHG;VOTURNOVER;VATURNOVER;TCAP;MCAP';
            $url        = 'http://quotes.money.163.com/service/chddata.html?code='.$type.$code.'&start='.$start.'&end='.$end.'&fields='.$getfields;
            // $url        = 'http://quotes.money.163.com/service/chddata.html?code=0601318&start='.$start.'&end='.$end.'&fields='.$getfields;
    
            /// 转码
            $content    = $this->sendCurl($url);
            $content    = mb_convert_encoding($content, "UTF-8", "GBK");
    
            if( strlen($content)<=121 ){
                return false;
            }
        }elseif( $road==1 ){
            
            $url = 'http://api.finance.ifeng.com/akdaily/?code='.$type.$code.'&type=last';
            $content = $this->sendCurl($url);
            $content = json_decode($content, true);

            if( empty($content['record']) ){
                return false;
            }
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
     *  $road=0 网易线路   $road=1 凤凰财经线路
     */
    protected function tbtype2linktype($tbtype, $road=0){
    
        if( $road==0 ){
        
            return $tbtype==1 ? $tbtype : ($tbtype==2 ? 0 : 100);
        }elseif( $road==1 ){
        
            return $tbtype==1 ? 'sz' : ($tbtype==2 ? 'sh' : '');
        }
    }

    /**
     * 新增股票数据入库
     */
    protected function gupiaoInDB($road, $type, $code, $start, $end){
    
        /// 请求接口获取数据
        $content = $this->getCurlData($road, $type, $code, $start, $end);
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
    public function updateOriginal($road){
    
        $end_time = time();

        /// 获取shares所有id
        $codes = TB::table('shares')->select('id, code, type, sdb_last_update_time')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($codes);
        foreach( $codes as $shares_row){
            
            /// 初始化参数
            $percent    = number_format(($dividend/$divisor)*100, 4) . '%';
            $type       = $this->tbtype2linktype($shares_row['type'], $road);
            $code       = $shares_row['code'];
            $start      = empty($shares_row['sdb_last_update_time']) ? $this->gupiao_b_time : date('Ymd', $shares_row['sdb_last_update_time']);
            // $end        = date('Ymd', $end_time-86400);
            $end        = date('Ymd', $end_time);
            $shares_id  = $shares_row['id'];

            /// 请求接口获取数据
            $content = $this->getCurlData($road, $type, $code, $start, $end);
            if( $content===false ){
                echo  '没有需要新增的detail数据-01! type: '.$type.'; code: '.$code . '；完成：'. $percent . PHP_EOL;
                $dividend++;
                continue;
            }

            if( $road==0 ){

                /// 剥离拆分数据
                $original_data = $this->splitData($content);
                # 初始化参数  日期,股票代码,名称,收盘价,最高价,最低价,开盘价,前收盘,涨跌额,涨跌幅,成交量,成交金额,总市值;流通市值
                #             0    1     2     3     4    5     6      7     8    9     0      11     12    13
                $first_row_arr  = isset($original_data[0]) ? explode(',', $original_data[0]) : [];
                if( empty($first_row_arr) ){
                    echo '没有需要新增的detail数据-02! type: ' . $type . '; code: ' . $code . '；完成：'. $percent . PHP_EOL;
                    $dividend++;
                    continue;
                }

            }elseif( $road==1 ){
            
                $original_data              = $content['record'];
                $original_data_active_date  = array_column($original_data, '0');

                # 已存在的数据中的最新一条
                $has_newest_row = TB::table('tl_shares_details_byday')->select('active_date')->where(['shares__id', $shares_id])->orderby('active_date_timestamp desc')->find();

                if( !empty($has_newest_row) ){
                    
                    $newest_key     = array_search($has_newest_row['active_date'], $original_data_active_date);
                    $original_data  = array_slice($original_data, $newest_key);# 不能从 $newest_key+1 开始算，因为索引为$newest的记录，起收盘价要作为下一天的 last_day_end_price

                    if( empty($original_data)||count($original_data)==1 ){
                        echo  '没有需要新增的detail数据-03! type: '.$type.'; code: '.$code . '；完成：'. $percent . PHP_EOL;
                        $dividend++;
                        continue;
                    }
                }

                /* foreach( $original_data as $kk=>$vv){
                
                    if( $vv[0]=='2012-05-30' ){
                    
                        print_r($vv);
                    }
                }

                exit; */
            }

            /// 录入shares_details_byday表数据
            $data = [];
            $sdb_last_update_time       = 0;
            $this_last_day_end_price    = '';
            foreach( $original_data as $k=>$v){

                if( $road==0 ){
                
                    /// 拆分原始数据
                    $this_row = explode(',', $v);
                    
                    $this_row = array_filter($this_row, function($elem){
                        return trim($elem);
                    });
                }elseif( $road==1 ){
                
                    /**
                     * 0  日期
                     * 1  开盘价
                     * 2  最高价
                     * 3  收盘价
                     * 4  最低价
                     * 5  成交量
                     * 6  涨跌额
                     * 7  涨跌幅
                     * 8  5日均价
                     * 9  10日均价
                     * 10 20日均价
                     * 11 5日均量
                     * 12 10日均量
                     * 13 20日均量
                     * 14 换手率
                     */
                    $this_row = $v;
                }

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

                $has_row = TB::table('tl_shares_details_byday')->select('id,channel')->where($check_row)->find();

                if( !empty($has_row) ){

                    if( $road==1 ){
                        $this_last_day_end_price = $this_row[3];
                    }elseif( $road==0 ){# 网易线路
                    
                        if( $has_row['channel']==2 ){# 旧数据来源于 凤凰，则更新成网易
                    
                            $this_original_data    = $v;
                            $uad_price             = (isset($this_row[8])&&!empty($this_row[8]) ) ? $this_row[8] : '';
                            $uad_range             = (isset($this_row[9])&&!empty($this_row[9]) ) ? $this_row[9] : '';
                            $transaction_amount    = (isset($this_row[11])&&!empty($this_row[11]) ) ? $this_row[11] : '';
                            $total_shizhi          = (isset($this_row[12])&&!empty($this_row[12]) ) ? $this_row[12] : '';
                            $deal_shizhi           = (isset($this_row[13])&&!empty($this_row[13]) ) ? $this_row[13] : '';
    
                            $_upd = [
                                'uad_price'             => $uad_price,
                                'uad_range'             => $uad_range,
                                'original_data'         => $this_original_data,
                                'transaction_amount'    => $transaction_amount,
                                'total_shizhi'          => $total_shizhi,
                                'deal_shizhi'           => $deal_shizhi,
                                'channel'               => 1
                            ];
    
                            /// 更新子表
                            $re = TB::table('tl_shares_details_byday')
                            ->update($_upd)
                            ->where(['id', '=', $has_row['id']])
                            ->exec();

                            if( !$re ){
                                echo '来源于凤凰，需要更新的shares_details_byday数据 id:' . $has_row['id'] . '更新失败' . PHP_EOL;
                            }

                            $sdb_last_update_time = (isset($this_row[0])&&!empty($this_row[0]) ) ? strtotime($this_row[0] . ' 15:00:00') : 0;

                            $shares_data = [
                                'sdb_last_update_time' => $sdb_last_update_time
                            ];
                
                            $re = TB::table('shares')
                            ->update($shares_data)
                            ->where(['id', '=', $shares_id])
                            ->exec();
                
                            if( !$re ){
                                echo '更新shares主表失败! original_data: id: ' . $shares_id . '; sdb_last_update_time: ' . $sdb_last_update_time . PHP_EOL;
                                echo 'type: '.$type.'; code: '.$code.'完成：'. $percent . PHP_EOL;
                            }
                        }
                    }
                    continue;
                }
                
                /// 组装数据
                if( $road==0 ){# 网易线路
                
                    $this_original_data    = $v;
                    $active_date           = (isset($this_row[0])&&!empty($this_row[0]) ) ? $this_row[0] : '';
                    $day_start_price       = (isset($this_row[6])&&!empty($this_row[6]) ) ? $this_row[6] : '';
                    $day_end_price         = (isset($this_row[3])&&!empty($this_row[3]) ) ? $this_row[3] : '';
                    $day_max_price         = (isset($this_row[4])&&!empty($this_row[4]) ) ? $this_row[4] : '';
                    $day_min_price         = (isset($this_row[5])&&!empty($this_row[5]) ) ? $this_row[5] : '';
                    $last_day_end_price    = (isset($this_row[7])&&!empty($this_row[7]) ) ? $this_row[7] : '';
                    $uad_price             = (isset($this_row[8])&&!empty($this_row[8]) ) ? $this_row[8] : '';
                    $uad_range             = (isset($this_row[9])&&!empty($this_row[9]) ) ? $this_row[9] : '';
                    $volume                = (isset($this_row[10])&&!empty($this_row[10]) ) ? $this_row[10] : '';
                    $transaction_amount    = (isset($this_row[11])&&!empty($this_row[11]) ) ? $this_row[11] : '';
                    $step                  = 1;
                    $created_time          = time();
                    $active_date_timestamp = (isset($this_row[0])&&!empty($this_row[0]) ) ? strtotime($this_row[0] . ' 15:00:00') : 0;
                    $total_shizhi          = (isset($this_row[12])&&!empty($this_row[12]) ) ? $this_row[12] : '';
                    $deal_shizhi           = (isset($this_row[13])&&!empty($this_row[13]) ) ? $this_row[13] : '';
                    
                }elseif( $road==1 ){# 凤凰财经线路

                    $this_original_data    = json_encode($v);
                    $active_date           = $this_row[0];
                    $day_start_price       = $this_row[1];
                    $day_end_price         = $this_row[3];
                    $day_max_price         = $this_row[2];
                    $day_min_price         = $this_row[4];
                    $last_day_end_price    = ($k==0) ? '' : $this_last_day_end_price;
                    $uad_price             = !empty($this_last_day_end_price) ? number_format(($this_row[3]-$this_last_day_end_price), '2', '.', '') : '';# 今日收盘价-昨日收盘价
                    $uad_range             = (!empty($uad_price)&&$uad_price!=0) ? (number_format((($uad_price/$this_last_day_end_price)*100), 2, '.', '')) : '';# 涨跌额/昨日收盘价
                    $volume                = $this_row[5];
                    $transaction_amount    = '';
                    $step                  = 1;
                    $created_time          = time();
                    $active_date_timestamp = (isset($this_row[0])&&!empty($this_row[0]) ) ? strtotime($this_row[0] . ' 15:00:00') : 0;

                    $this_last_day_end_price = $day_end_price;
                }

                $_f = [
                    'shares__id',
                    'original_data',
                    'active_date',   
                    'day_start_price', 
                    'day_end_price',
                    'day_max_price',
                    'day_min_price',
                    'last_day_end_price',
                    'uad_price',
                    'uad_range',
                    'volume',
                    'transaction_amount',
                    'step',
                    'created_time',
                    'active_date_timestamp'
                ];

                $data[$k] = [
                    'shares__id'            => $shares_id,
                    'original_data'         => $this_original_data,
                    'active_date'           => $active_date,
                    'day_start_price'       => $day_start_price,
                    'day_end_price'         => $day_end_price,
                    'day_max_price'         => $day_max_price,
                    'day_min_price'         => $day_min_price,
                    'last_day_end_price'    => $last_day_end_price,
                    'uad_price'             => $uad_price,
                    'uad_range'             => $uad_range,
                    'volume'                => $volume,
                    'transaction_amount'    => $transaction_amount,
                    'step'                  => $step,
                    'created_time'          => $created_time,
                    'active_date_timestamp' => $active_date_timestamp
                ];

                if( $road==1 ){

                    $_f[] = 'channel';
                    $data[$k]['channel'] = 2;
                }elseif( $road==0 ){

                    $_f[] = 'total_shizhi';
                    $data[$k]['total_shizhi']   = $total_shizhi;

                    $_f[] = 'deal_shizhi';
                    $data[$k]['deal_shizhi']    = $deal_shizhi;
                }

                $sdb_last_update_time = $active_date_timestamp;
            }
            
            if( empty($data) ){
                echo 'type: '.$type.'; code: '.$code . '已是最新!' . PHP_EOL;
                echo 'type: '.$type.'; code: '.$code.'完成：'. $percent . PHP_EOL;
                $dividend++;
                continue;
            }

            $re = TB::table('tl_shares_details_byday')
            ->fields(implode(',', $_f))
            ->insert($data)
            ->exec();

            if( !$re ){
                echo '录入子表数据失败! original_data: ' . serialize($original_data) . PHP_EOL;
                echo 'type: '.$type.'; code: '.$code.'完成：'. $percent . PHP_EOL;
                $dividend++;
                continue;
            }

            /// 更新主表数据
            if( $road==0 ){
                $shares_data = [
                    'sdb_last_update_time' => $sdb_last_update_time
                ];
    
                $re = TB::table('shares')
                ->update($shares_data)
                ->where(['id', '=', $shares_id])
                ->exec();
    
                if( !$re ){
                    echo '更新shares主表失败! original_data: id: ' . $shares_id . '; sdb_last_update_time: ' . $sdb_last_update_time . PHP_EOL;
                    echo 'type: '.$type.'; code: '.$code.'完成：'. $percent . PHP_EOL;
                    $dividend++;
                    continue;
                }
            }
            
            // echo 'shares_details_byday更新数据成功! type: '.$type.'; code: '.$code . PHP_EOL;
            echo 'type: '.$type.'; code: '.$code.'；完成：'. $percent . PHP_EOL;
            $dividend++;
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
                ['shares__id', $i]
            ];

            $tmp_shares_details_byday_row = TB::table('tl_shares_details_byday')->select('active_date')->where($tmp_where)->orderby('id desc')->find();
            
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
        $ids = TB::table('shares')->select('id')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($ids);
        foreach( $ids as $v){
            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            /// 获取当前票的所有记录
            $this_shares_details_byday_row = TB::table('tl_shares_details_byday as sdb')->select('
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
                    
                    // $ma4_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 4, $last_price);
                    // $ma['ma4_price'] = $ma4_price;
                    $ma5_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 5, $last_price);
                    $ma['ma5_price'] = $ma5_price;

                    // $ma9_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 9, $last_price, $ma5_price);
                    // $ma['ma9_price'] = $ma9_price;
                    $ma10_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 10, $last_price, $ma5_price);
                    $ma['ma10_price'] = $ma10_price;

                    // $ma14_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 14, $last_price, $ma10_price);
                    // $ma['ma14_price'] = $ma14_price;
                    $ma15_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 15, $last_price, $ma10_price);
                    $ma['ma15_price'] = $ma15_price;

                    // $ma19_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 19, $last_price, $ma15_price);
                    // $ma['ma19_price'] = $ma19_price;
                    $ma20_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 20, $last_price, $ma15_price);
                    $ma['ma20_price'] = $ma20_price;

                    // $ma29_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 29, $last_price, $ma20_price);
                    // $ma['ma29_price'] = $ma29_price;
                    $ma30_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 30, $last_price, $ma20_price);
                    $ma['ma30_price'] = $ma30_price;

                    // $ma59_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 59, $last_price, $ma30_price);
                    // $ma['ma59_price'] = $ma59_price;
                    $ma60_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 60, $last_price, $ma30_price);
                    $ma['ma60_price'] = $ma60_price;

                    // $ma119_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 119, $last_price, $ma60_price);
                    // $ma['ma119_price'] = $ma119_price;
                    $ma120_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 120, $last_price, $ma60_price);
                    $ma['ma120_price'] = $ma120_price;

                    // $ma239_price = $this->getAveragePrice($k, $this_shares_details_byday_row, 239, $last_price, $ma120_price);
                    // $ma['ma239_price'] = $ma239_price;
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

                    // if( empty($sdbr_v['ma4_price']) ){
                    //     $ma4_price          = $this->getAveragePrice($k, $this_shares_details_byday_row, 4, $last_price);
                    //     $ma['ma4_price']    = $ma4_price;
                    // }else{
                    //     $ma4_price = $sdbr_v['ma4_price'];
                    // }

                    if( empty($sdbr_v['ma5_price']) ){
                        $ma5_price          = $this->getAveragePrice($k, $this_shares_details_byday_row, 5, $last_price);
                        $ma['ma5_price']    = $ma5_price;
                    }else{
                        $ma5_price = $sdbr_v['ma5_price'];
                    }

                    // if( empty($sdbr_v['ma9_price']) ){
                    //     $ma9_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 9, $last_price, $ma5_price);
                    //     $ma['ma9_price']   = $ma9_price;
                    // }else{
                    //     $ma9_price = $sdbr_v['ma9_price'];
                    // }

                    if( empty($sdbr_v['ma10_price']) ){
                        $ma10_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 10, $last_price, $ma5_price);
                        $ma['ma10_price']   = $ma10_price;
                    }else{
                        $ma10_price = $sdbr_v['ma10_price'];
                    }

                    // if( empty($sdbr_v['ma14_price']) ){
                    //     $ma14_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 14, $last_price, $ma10_price);
                    //     $ma['ma14_price']   = $ma14_price;
                    // }else{
                    //     $ma14_price = $sdbr_v['ma14_price'];
                    // }

                    if( empty($sdbr_v['ma15_price']) ){
                        $ma15_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 15, $last_price, $ma10_price);
                        $ma['ma15_price']   = $ma15_price;
                    }else{
                        $ma15_price = $sdbr_v['ma15_price'];
                    }

                    // if( empty($sdbr_v['ma19_price']) ){
                    //     $ma19_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 19, $last_price, $ma15_price);
                    //     $ma['ma19_price']   = $ma19_price;
                    // }else{
                    //     $ma19_price = $sdbr_v['ma19_price'];
                    // }

                    if( empty($sdbr_v['ma20_price']) ){
                        $ma20_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 20, $last_price, $ma15_price);
                        $ma['ma20_price']   = $ma20_price;
                    }else{
                        $ma20_price = $sdbr_v['ma20_price'];
                    }

                    // if( empty($sdbr_v['ma29_price']) ){
                    //     $ma29_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 29, $last_price, $ma20_price);
                    //     $ma['ma29_price']   = $ma29_price;
                    // }else{
                    //     $ma29_price = $sdbr_v['ma29_price'];
                    // }

                    if( empty($sdbr_v['ma30_price']) ){
                        $ma30_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 30, $last_price, $ma20_price);
                        $ma['ma30_price']   = $ma30_price;
                    }else{
                        $ma30_price = $sdbr_v['ma30_price'];
                    }

                    // if( empty($sdbr_v['ma59_price']) ){
                    //     $ma59_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 59, $last_price, $ma30_price);
                    //     $ma['ma59_price']   = $ma59_price;
                    // }else{
                    //     $ma59_price = $sdbr_v['ma59_price'];
                    // }

                    if( empty($sdbr_v['ma60_price']) ){
                        $ma60_price         = $this->getAveragePrice($k, $this_shares_details_byday_row, 60, $last_price, $ma30_price);
                        $ma['ma60_price']   = $ma60_price;
                    }else{
                        $ma60_price = $sdbr_v['ma60_price'];
                    }

                    // if( empty($sdbr_v['ma119_price']) ){
                    //     $ma119_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 119, $last_price, $ma60_price);
                    //     $ma['ma119_price']  = $ma119_price;
                    // }else{
                    //     $ma119_price = $sdbr_v['ma119_price'];
                    // }

                    if( empty($sdbr_v['ma120_price']) ){
                        $ma120_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 120, $last_price, $ma60_price);
                        $ma['ma120_price']  = $ma120_price;
                    }else{
                        $ma120_price = $sdbr_v['ma120_price'];
                    }

                    // if( empty($sdbr_v['ma239_price']) ){
                    //     $ma239_price        = $this->getAveragePrice($k, $this_shares_details_byday_row, 239, $last_price, $ma120_price);
                    //     $ma['ma239_price']  = $ma239_price;
                    // }else{
                    //     $ma239_price = $sdbr_v['ma239_price'];
                    // }

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

        $now = time();
    
        /// 获取shares所有id
        $ids = TB::table('shares')->select('id, title, code')->where(['is_deprecated', 0])->get();

        $dividend   = 1;
        $divisor    = count($ids);
        foreach( $ids as $v){

            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            /// 获取当前票的所有记录
            $this_shares_details_byday_row = TB::table('tl_shares_details_byday as sdb')->select('
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

                $ma_angle['ma_angle_time'] = $now;

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

    /**
     * 计算均线偏离率
     */
    public function pianlilv(){
    
        /// 初始化参数
        $now = time();


    }
    
    /**
     * 抓取股票对应的企业信息
     */
    public function getCompanyDetails(){

        $codes = TB::table('shares')->select('id, code')->where(1)->get();

        $dividend   = 1;
        $divisor    = count($codes);
        foreach( $codes as $v){

            $percent = number_format(($dividend/$divisor)*100, 4) . '%';
        
            /// 初始化参数
            $url = 'http://basic.10jqka.com.cn/'.$v['code'].'/company.html';
    
            /// 转码
            $content    = $this->sendCurl($url);
            $content    = mb_convert_encoding($content, "UTF-8", "GBK");
    
            /// 提取数据
            # 公司名称   公司名称：</strong><span>中国船舶重工集团动力股份有限公司</span>
            $company_name = $this->regexInfo('公司名称：<\/strong><span>', '<\/span>', $content);
    
            # 公司曾用名
            $company_name_once_used = $this->regexInfo('曾 用 名：<\/strong><span>', '<\/span>', $content);
    
            # 所属申万行业
            $sw_cate = $this->regexInfo('所属申万行业：<\/strong><span>', '<\/span>', $content);
    
            # 主营业务
            $main_business = $this->regexInfo('主营业务：<\/strong>\s+<span>', '<\/span>', $content);
    
            # 产品名称
            $product_names = $this->regexInfo('产品名称：<\/strong>', '<\/td>', $content);
            $product_names = $this->regexDelTag($product_names);
    
            # 公司简介
            // $intro = $this->regexInfo('公司简介：<\/strong>', '<\/td>', $content);
            // $intro = $this->regexDelTag($intro);
            $intro = '';

            # 所属地域
            $province = $this->regexInfo('所属地域：<\/strong><span>', '<\/span>', $content);

            /// 更新数据
            $tmp_update = [
                'company_name'              => $company_name,
                'company_name_once_used'    => $company_name_once_used,
                'sw_cate'                   => $sw_cate,
                'main_business'             => $main_business,
                'product_names'             => $product_names,
                'intro'                     => $intro,
                'province'                     => $province,
            ];

            $re = TB::table('shares')
            ->update($tmp_update)
            ->where(['id', '=', $v['id']])
            ->exec();

            if( !$re ){
                echo '更新失败！--》》' . $v['code'] . PHP_EOL;
            }else{
                echo '更新成功 --》' . $v['code'] . PHP_EOL;
            }

            echo '完成：'. $percent . PHP_EOL;
            $dividend++;
        }
    }

    public function regexInfo($b_str, $e_str, $content, $mode='Us'){
    
        preg_match('/'.$b_str.'.*'.$e_str.'/'.$mode, $content, $matches);

        if( empty($matches) ) return '';

        $b_str = preg_replace('/\s+/', '', str_replace('\\', '', str_replace('\s+', '', $b_str)));
        $e_str = str_replace('\\', '', $e_str);
        $matches[0] = preg_replace('/\s+/', '', $matches[0]);
        
        return str_replace($e_str, '', str_replace($b_str, '', $matches[0]));
    }

    public function regexDelTag($target){

        if( empty($target) ) return '';
    
        preg_match_all('/<.*>/Us', $target, $matches);# U 懒惰模式，匹配最短的  s 使得.具有匹配换行符的作用

        if( !empty($matches) ){
            
            foreach( $matches as $v){
            
                $target = str_replace($v, '', $target);
            }
        }

        return $target;
    }

    /**
     * 额外零散处理功能
     */
    public function extraDoing(){

        $now = time();
    
        /// 分类拆分
        # 获取所有数据
        $shares = TB::table('shares')->select('id, code, sw_cate')->where([
            ['is_deprecated', 0],
            ['is_explode_cate', 0]
        ])->get();

        # 整理分类数据
        $sw_cates_lv1   = [];
        $sw_cates_lv2   = [];
        $dividend       = 1;
        $divisor        = count($shares);

        foreach( $shares as $k=>$v){

            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            $this_sw_cate = trim($v['sw_cate']);
            if( empty($this_sw_cate) ) continue;
        
            $this_explode_cate  = explode('—', $v['sw_cate']);
            $this_cate1         = trim($this_explode_cate[0]);
            $this_cate2         = trim($this_explode_cate[1]);

            if( $this_cate1=='-' ) echo $v['code'] . PHP_EOL;
            if( $this_cate1=='—' ) continue;

            # 更新shares数据表
            $re = TB::table('shares')->update([
                'tmp4' => $this_cate1,
                'tmp5' => $this_cate2,
                'is_explode_cate'   => 1,
                'explode_cate_time' => $now
            ])->where(['id', $v['id']])->exec();

            if( !$re ){
                exit('更新失败');
            }

            /* if( ($lv1_key=array_search($this_cate1, $sw_cates_lv1))===false ){
            
                $sw_cates_lv1[] = $this_cate1;
                $lv1_key        = array_search($this_cate1, $sw_cates_lv1);
            }

            if( isset($sw_cates_lv2[$lv1_key]) ){
            
                $this_lv2 = $sw_cates_lv2[$lv1_key];
                if( ($lv2_key=array_search($this_cate2, $this_lv2))===false ){
                
                    $sw_cates_lv2[$lv1_key][] = $this_cate2;
                
                }
            }else{

                $sw_cates_lv2[$lv1_key]     = [];
                $sw_cates_lv2[$lv1_key][]   = $this_cate2;
            } */

            echo '完成：'. $percent . PHP_EOL;
            $dividend++;
        }


        print_r($sw_cates_lv1);
        echo 'lv1共有：' . count($sw_cates_lv1) . '种分类' . PHP_EOL;
        print_r($sw_cates_lv2);
    }

    /**
     * 补充股票每日一年新高数据
     */
    public function yearXingao(){

        $now = time();
    
        /// 获取所有股票数据
        $shares = TB::table('shares')->select('
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
            $this_share_details_byday = TB::table('tl_shares_details_byday')->select('
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
                $now_day_max_price_ori = (empty($detail['day_max_price'])||$detail['day_max_price']=='0.0') ? 
                ( (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 0 : $detail['day_end_price']) : 
                $detail['day_max_price'];
                $now_day_max_price = (empty($detail['day_max_price'])||$detail['day_max_price']=='0.0') ? 
                ( (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 
                    0 : 
                    intval($detail['day_end_price']*10000000)) : 
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
                $has_date_record    = TB::table('date_record')->select('id')->where($_condi)->find();

                if( empty($has_date_record) ){
                
                    $_dr_data = [
                        'active_date'           => $detail['active_date'],
                        'active_date_timestamp' => $detail['active_date_timestamp'],
                        'created_time'          => $now,
                    ];

                    $re = TB::table('date_record')->insert($_dr_data)->exec();
                    if( !$re ){
                        echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                        continue;
                    }else{
                        $date_record__id = TB::last_insert_id();
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
                $has_xingao_and_xindi = TB::table('xingao_and_xindi')->select('id')->where($_condi)->find();

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

                    $re = TB::table('xingao_and_xindi')->insert($_xax_data)->exec();
                    if( !$re ){
                        echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                        continue;
                    }
                }

                $re = TB::table('tl_shares_details_byday')->update($_upd)->where(['id', $detail['id']])->exec();
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

        $now = time();

        $_condi = [
            ['date_record__id', $date_record__id],
            ['shares__id', $shares__id],
            ['type', $type]
        ];
        $has_xingao_and_xindi = TB::table('xingao_and_xindi')->select('id')->where($_condi)->find();

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

        $now = time();
    
        /// 获取所有股票数据
        $shares = TB::table('shares')->select('
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
            $this_share_details_byday = TB::table('tl_shares_details_byday')->select('
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
                $now_day_min_price_ori = (empty($detail['day_min_price'])||$detail['day_min_price']=='0.0') ? 
                ( (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 0 : $detail['day_end_price']) : 
                $detail['day_min_price'];
                $now_day_min_price = (empty($detail['day_min_price'])||$detail['day_min_price']=='0.0') ? 
                ( (empty($detail['day_end_price'])||$detail['day_end_price']=='0.0') ? 
                    0 : 
                    intval($detail['day_end_price']*10000000)) : 
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
                $has_date_record    = TB::table('date_record')->select('id')->where($_condi)->find();

                if( empty($has_date_record) ){
                
                    $_dr_data = [
                        'active_date'           => $detail['active_date'],
                        'active_date_timestamp' => $detail['active_date_timestamp'],
                        'created_time'          => $now
                    ];

                    $re = TB::table('date_record')->insert($_dr_data)->exec();
                    if( !$re ){
                        echo '更新失败！--》》' . $detail['id'] . PHP_EOL;
                        $this->passedShareHistory($now_day_min_price);
                        continue;
                    }else{

                        $date_record__id = TB::last_insert_id();
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
                
                    $re = TB::table('xingao_and_xindi')->fields(array_keys($_xindi_data[0]))->insert($_xindi_data)->exec();
                    if( !$re ){
                        echo '新增失败！--》》' . $detail['id'] . PHP_EOL;
                        continue;
                    }
                }

                # 更新
                $re = TB::table('tl_shares_details_byday')->update($_upd)->where(['id', $detail['id']])->exec();
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
     * test
     */
    public function test(){

        $road = 0;
    
        $end_time = time();

        /// 获取shares所有id
        $codes = TB::table('shares as s')->select('
            distinct s.id,
            s.`code`,
            s.type
        ')->leftjoin('tl_shares_details_byday sdb', 's.id=sdb.shares__id')
        ->where([
            ['s.is_deprecated', 0],
            ['sdb.total_shizhi', '=', ''],
            ['sdb.channel', 1],
            ['sdb.day_start_price', '<>', '']
        ])->get();

        // print_r($codes);
        // exit;

        $dividend   = 1;
        $divisor    = count($codes);
        foreach( $codes as $shares_row){

            // if( $shares_row['id']!='939' ) continue;

            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            $type       = $this->tbtype2linktype($shares_row['type'], $road);
            $code       = $shares_row['code'];
            // $start      = empty($shares_row['sdb_last_update_time']) ? $this->gupiao_b_time : date('Ymd', $shares_row['sdb_last_update_time']);
            $start      = $this->gupiao_b_time;
            // $end        = date('Ymd', $end_time-86400);
            $end        = date('Ymd', $end_time);
            $shares_id  = $shares_row['id'];

            /// 请求接口获取数据
            $content = $this->getCurlData($road, $type, $code, $start, $end);
            if( $content===false ){
                echo  '无数据-01! type: '.$type.'; code: '.$code . PHP_EOL;
                continue;
            }

            /// 剥离拆分数据
            $original_data = $this->splitData($content);

            # 初始化参数  日期,股票代码,名称,收盘价,最高价,最低价,开盘价,前收盘,涨跌额,涨跌幅,成交量,成交金额,总市值;流通市值
            #             0    1     2     3     4    5     6      7     8    9     0      11     12    13
            $first_row_arr  = isset($original_data[0]) ? explode(',', $original_data[0]) : [];
            if( empty($first_row_arr) ){
                echo '无数据-02! type: ' . $type . '; code: ' . $code . PHP_EOL;
                continue;
            }

            /// 录入shares_details_byday表数据
            $dividend1   = 1;
            $divisor1    = count($original_data);
            
            foreach( $original_data as $k=>$v){
                $percent1 = number_format(($dividend1/$divisor1)*100, 4) . '%';
                
                /// 拆分原始数据
                $this_row = explode(',', $v);
                
                $this_row = array_filter($this_row, function($elem){
                    return trim($elem);
                });

                /* if( $this_row[0]!='2020-10-12' ){
                    continue;
                } */

                // print_r($this_row);
                // exit;
                

                /// 没有日期则跳过
                if( 
                    !isset($this_row[0]) ||
                    empty($this_row[0])
                ){
                    echo '缺少日期数据，完成：'. $percent . '->' . $percent1 . PHP_EOL;
                    $dividend1++;
                    continue;
                }

                /// detail数据存在则跳过
                $check_row = [
                    ['shares__id', $shares_id],
                    ['active_date_timestamp', strtotime($this_row[0].' 15:00:00')]
                ];

                $has_row = TB::table('tl_shares_details_byday')->select('id, channel, total_shizhi')->where($check_row)->find();
                // var_dump(date('Y-m-d H:i:s', 1602514800));
                // var_dump(TB::dbug());
                if( !empty($has_row) ){

                    if( $has_row['channel']==1 ){# 已是 网易

                        if( !empty($has_row['total_shizhi']) ){
                            echo 'type1: '.$type.'; code: '.$code.'完成：'. $percent . '->' . $percent1 . PHP_EOL;
                            $dividend1++;
                            continue;
                        }

                        $this_original_data    = $v;
                        $total_shizhi          = (isset($this_row[12])&&!empty($this_row[12]) ) ? $this_row[12] : '';
                        $deal_shizhi           = (isset($this_row[13])&&!empty($this_row[13]) ) ? $this_row[13] : '';

                        $_upd = [
                            'original_data' => $this_original_data,
                            'total_shizhi'  => $total_shizhi,
                            'deal_shizhi'   => $deal_shizhi
                        ];

                        /// 更新子表
                        $re = TB::table('tl_shares_details_byday')
                        ->update($_upd)
                        ->where(['id', '=', $has_row['id']])
                        ->exec();

                        /* var_dump(TB::dbug());
                        exit; */
                        
                        if( !$re ){
                            echo '更新失败' . PHP_EOL;
                        }else{
                            echo 'type: '.$type.'; code: '.$code.'完成：'. $percent . '->' . $percent1 . PHP_EOL;
                        }
                    }

                    $dividend1++;
                    continue;
                }
            }

            // echo 'shares_details_byday更新数据成功! type: '.$type.'; code: '.$code . PHP_EOL;
            echo 'type: '.$type.'; code: '.$code.'；完成：'. $percent . PHP_EOL;
            $dividend++;
        }
        return true;
    }

    /**
     * 构建板块分类
     */
    public function constructPlate(){
    
        /// 获取股票shares(stock)数据
        $codes = TB::table('shares')->select('id, code, cate_1, cate_2, tmp6')->where(['is_deprecated', 0])->get();

        $_plates1           = [];
        $_plates2           = [];
        $_plates_shares__id = [];
        foreach( $codes as $k=>$v){

            if( empty($v['tmp6']) ) continue;

            $tmp6_arr = explode('|', $v['tmp6']);
            if( empty($tmp6_arr) ) continue;

            /// 一级key
            $_k1 = null;
            foreach( $tmp6_arr as $_ta_v){

                /// 一级
                if( $_ta_v=='1' ){
                    
                    # 是否已存在
                    $has_row = TB::table('plate')->select('id')->where([
                        ['name', $v['cate_1']],
                        ['come_from', 4],
                        ['type', 1],
                        ['pid', 0]
                    ])->find();

                    if( $has_row ){
                        break;
                    }

                    if( !in_array($v['cate_1'], $_plates1) ){
                    
                        $_plates1[] = $v['cate_1'];
                    }

                    $_k1 = array_search($v['cate_1'], $_plates1);
                }else{

                    /// 二级
                    if( $_ta_v=='2' ){# 值为2

                        if( is_null($_k1) ) continue;
                        if( !isset($_plates2[$_k1]) ){
                            $_plates2[$_k1]             = [];
                            $_plates_shares__id[$_k1]   = [];
                        }
                        
                        if( !in_array($v['cate_2'], $_plates2[$_k1]) ){
                        
                            $_plates2[$_k1][] = $v['cate_2'];
                        }

                        $_k2 = array_search($v['cate_2'], $_plates2[$_k1]);

                    }else {# 值为其他
                        
                        if( is_null($_k1) ) continue;
                        if( !isset($_plates2[$_k1]) ) $_plates2[$_k1]=[];
                        
                        if( !in_array($_ta_v, $_plates2[$_k1]) ){
                        
                            $_plates2[$_k1][] = $_ta_v;
                        }

                        $_k2 = array_search($_ta_v, $_plates2[$_k1]);
                    }

                    
                    if( !isset($_plates_shares__id[$_k1][$_k2]) ) $_plates_shares__id[$_k1][$_k2]=[];

                    $_plates_shares__id[$_k1][$_k2][] = $v['id'];
                }
            }
        }
        // print_r($_plates1);
        // print_r($_plates2);
        // print_r($_plates_shares__id);
        // exit;

        $now = time();

        /// 组装新增一级
        $_i = [];
        foreach( $_plates1 as $k=>$v){
        
            $_i[$k] = [
                'name'          => $v,
                'created_time'  => $now,
                'type'          => 1,
                'come_from'     => 4,
                'code'          => 'SF' . str_pad($k,4,'0',STR_PAD_LEFT)
            ];
        }

        if( !TB::table('plate')->fields(array_keys($_i[0]))->insert($_i)->exec() ) exit('新增一级板块失败');

        // print_r($_i);

        /// 组装新增二级
        $dividend   = 1;
        $divisor    = count($_plates1);
        foreach( $_plates1 as $k1=>$name1){
            $percent = number_format(($dividend/$divisor)*100, 4) . '%';

            # 一级id
            $_pid = TB::table('plate')->select('id')->where([
                ['name', $name1],
                ['come_from', 4],
                ['type', 1]
            ])->find();

            if(empty($_pid)) continue;
        
            $_i_plate           = [];
            $_i_shares_plate    = [];
            foreach( $_plates2[$k1] as $k2=>$v2){
            
                $last_plate_id = TB::table('plate')->select('id')->where([
                    ['name', $v2],
                    ['come_from', 4],
                    ['type', 1],
                    ['pid', $_pid['id']]
                ])->find();

                if( empty($last_plate_id) ){
                    $_i_plate = [
                        'name'          => $v2,
                        'created_time'  => $now,
                        'type'          => 1,
                        'come_from'     => 4,
                        'code'          => 'SFZ' . str_pad($k,4,'0',STR_PAD_LEFT),
                        'pid'           => $_pid['id']
                    ];
    
                    $re = TB::table('plate')->insert($_i_plate)->exec();
                    $last_insert_id = TB::last_insert_id();
                }else{
                    $last_insert_id = $last_plate_id['id'];
                }
                
                if( $re ){
                    
                    foreach( $_plates_shares__id[$k1][$k2] as $sk=>$sv){
                    
                        $_i_shares_plate[$sk] = [
                            'shares__id'    => $sv,
                            'plate__id'     => $last_insert_id,
                            'created_time'  => $now
                        ];
                    }

                    $re = TB::table('shares__plate')->fields(array_keys($_i_shares_plate[0]))->insert($_i_shares_plate)->exec();
                }
            }

            echo  '完成：'. $percent . PHP_EOL;
            $dividend++;
        }
    }

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
            $rocks = TB::table('shares__plate sp')->select('sp.shares__id')
            ->leftjoin('shares s', 's.id=sp.shares__id')
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
