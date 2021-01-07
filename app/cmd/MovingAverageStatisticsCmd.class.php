<?php

namespace cmd;
use \cmd\service\GupiaoService;
use \BaseCmd;
use cmd\service\MovingAverageService;
use cmd\service\NormalStatisticsService;
use \Json;
use \Err;

class MovingAverageStatisticsCmd extends BaseCmd{

    /**
     * 参数名列表
     */
    protected $signal = [
        'type',
        'road',
        'bdate',
        'edate'
    ];

    public function go(){

        /// 初始化参数
        $type   = $this->request('type');

        /// 执行功能
        Err::try(function () use($type){

            $moving_average_service = new MovingAverageService;

            switch($type){

                /**
                 * 1. 统计均线偏移率极值（10年，5年，3年偏离各均线极大值和极小值）：$gupiao_service->maxAndMinPianyilv();
                 */
                case 1:# php cmd.php MovingAverageStatistics 1
                    // $moving_average_service->maxAndMinPianyilv();
                break;
                /**
                 * 1. 统计每支票在不同偏移率的涨幅情况：$gupiao_service->();
                 */
                case 2:# php cmd.php MovingAverageStatistics 2
                    $moving_average_service->afterPianyilvZhangfu();
                break;
            }
        });
    }
}
