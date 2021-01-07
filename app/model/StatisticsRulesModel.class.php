<?php
namespace model;
use \BaseModel;

class StatisticsRulesModel extends BaseModel{

    protected $table = 'tl_statistics_rules';

    const C_FLAG=[
        0   =>  '无标识',
        1   =>  ['name'=>'近10年最大最小偏移率', 'descr'=>'近10年最大最小偏移率'],
        2   =>  ['name'=>'近5年最大最小偏移率', 'descr'=>'近5年最大最小偏移率'],
        3   =>  ['name'=>'近3年最大最小偏移率', 'descr'=>'近3年最大最小偏移率']
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

}