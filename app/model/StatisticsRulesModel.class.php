<?php
namespace model;
use \BaseModel;

class StatisticsRulesModel extends BaseModel{

    protected $table = 'tl_statistics_rules';

    const C_FLAG=[
        0   =>  '无标识',
        1   =>  ['name'=>'近10年最大最小偏移率', 'descr'=>'近10年最大最小偏移率'],
        2   =>  ['name'=>'近5年最大最小偏移率', 'descr'=>'近5年最大最小偏移率'],
        3   =>  ['name'=>'近3年最大最小偏移率', 'descr'=>'近3年最大最小偏移率'],
        4   =>  ['name'=>'固定区间5日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        5   =>  ['name'=>'固定区间10日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        6   =>  ['name'=>'固定区间15日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        7   =>  ['name'=>'固定区间20日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        8   =>  ['name'=>'固定区间30日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        9   =>  ['name'=>'固定区间60日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        10   =>  ['name'=>'固定区间120日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68'],
        11   =>  ['name'=>'固定区间240日均线偏移率间隔统计', 'descr'=>'1)偏移率大于70为一个整体区间；2)偏移率小于等于-70为一个整体区间；3)-70<偏移率<=70则固定间隔2个点位一个区间，比如:68<偏移率<=70 或 -70<偏移率<=-68']
    ];

    /**
     * 统计数据入库
     */
    public function getId($flag){
    
        $has_row  = $this->where(['flag', $flag])->find();

        if( !$has_row ){# 无则新增
        
            $_data                  = [];
            $_data['name']          = self::C_FLAG[$flag]['name'];
            $_data['descr']         = self::C_FLAG[$flag]['descr'];
            $_data['flag']          = $flag;
            $_data['created_time']  = time();

            $this->insert($_data)->exec();
            $id = $this->last_insert_id();
        }else{

            $id = $has_row['id'];
        }

        return $id;
    }

    /**
     * 均线周期转规则id
     */
    public function period2rulesId($period){
    
        /// 初始化参数
        $_period_2_rule_flag    = [5=>4, 10=>5, 15=>6, 20=>7, 30=>8, 60=>9, 120=>10, 240=>11];
        $flag                   = $_period_2_rule_flag[$period];

        /// 查询id
        $row = $this->select('id')->where(['flag', $flag])->find();

        return $row['id'];
    }

}