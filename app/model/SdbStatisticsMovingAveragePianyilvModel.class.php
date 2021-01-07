<?php
namespace model;
use \BaseModel;

class SdbStatisticsMovingAveragePianyilvModel extends BaseModel{

    protected $table = 'tl_sdb_statistics_moving_average_pianyilv';

    /**
     * 获取指定周期内均线偏移率极值
     * @param   $period     int     指定的周期，单位：年
     */
    public function getPianyilvMaxAndMin($period, $shares__id=null){
    
        /// 初始化参数
        $now    = time();
        $b_time = strtotime(date('Y-m-d 0:0:0', $now)) - $period*365*24*3600;

        /// 查询极值
        $where = [# '88888888'表示源数据无效导致偏移率无法计算；'99999999'表示源数据缺失导致偏移率无法计算（但以后源数据补全后也会重新计算）
            ['ma5_plv', 'not in', '(88888888, 99999999)'],
            ['ma10_plv', 'not in', '(88888888, 99999999)'],
            ['ma15_plv', 'not in', '(88888888, 99999999)'],
            ['ma20_plv', 'not in', '(88888888, 99999999)'],
            ['ma30_plv', 'not in', '(88888888, 99999999)'],
            ['ma60_plv', 'not in', '(88888888, 99999999)'],
            ['ma120_plv', 'not in', '(88888888, 99999999)'],
            ['ma240_plv', 'not in', '(88888888, 99999999)'],
            ['active_date_timestamp', '>=', $b_time]
        ];

        if( $shares__id ){
        
            $where[] = ['shares__id', $shares__id];
        }

        $info = $this->select([
            'max(ma5_plv)'      => 'max_ma5_plv',
            'min(ma5_plv)'      => 'min_ma5_plv',
            'max(ma10_plv)'     => 'max_ma10_plv',
            'min(ma10_plv)'     => 'min_ma10_plv',
            'max(ma15_plv)'     => 'max_ma15_plv',
            'min(ma15_plv)'     => 'min_ma15_plv',
            'max(ma20_plv)'     => 'max_ma20_plv',
            'min(ma20_plv)'     => 'min_ma20_plv',
            'max(ma30_plv)'     => 'max_ma30_plv',
            'min(ma30_plv)'     => 'min_ma30_plv',
            'max(ma60_plv)'     => 'max_ma60_plv',
            'min(ma60_plv)'     => 'min_ma60_plv',
            'max(ma120_plv)'    => 'max_ma120_plv',
            'min(ma120_plv)'    => 'min_ma120_plv',
            'max(ma240_plv)'    => 'max_ma240_plv',
            'min(ma240_plv)'    => 'min_ma240_plv'
        ])->where($where)->find();

        return $info;
    }
}