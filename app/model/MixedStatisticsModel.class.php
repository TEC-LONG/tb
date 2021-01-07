<?php
namespace model;
use \BaseModel;

class MixedStatisticsModel extends BaseModel{

    protected $table = 'tl_mixed_statistics';

    /**
     * 统计数据入库
     */
    public function getIn($content, $flag){

        /// 获取tl_statistics_rules表id
        $statistics_rules__id = (new StatisticsRulesModel)->getId($flag);

        /// 是否有偏移率数据
        $_data          = ['content'=>json_encode($content)];
        $has_pianyilv   = $this->where(['statistics_rules__id', $statistics_rules__id])->find();

        if( $has_pianyilv ){# 有则更新
        
            $re = $this->update($_data)->where(['statistics_rules__id', $statistics_rules__id])->exec();
        }else{# 无则新增
        
            $_data['statistics_rules__id']  = $statistics_rules__id;
            $_data['created_time']          = time();

            $re = $this->insert($_data)->exec();
        }

        return $re;
    }

    /**
     * 查询某条数据content值
     */
    public function getContent($flag){

        /// 获取tl_statistics_rules表id
        $statistics_rules__id = (new StatisticsRulesModel)->getId($flag);
    
        $row        = $this->select('content')->where(['statistics_rules__id', $statistics_rules__id])->find();
        $content    = json_decode(str_replace('\'', '"', $row['content']), true);/// decode的字符串中不能有单引号

        return $content;
    }

}