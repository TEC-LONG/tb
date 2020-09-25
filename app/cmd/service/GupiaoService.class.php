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
}
