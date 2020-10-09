<?php

namespace cmd;
use \cmd\service\GupiaoService;
use \baseCmd;
use \Json;
use \Err;
use \Fun;
use \TB;

class GupiaoCmd extends baseCmd{

    /**
     * 参数名列表
     */
    protected $signal = [
        'type',
        'bdate',
        'edate'
    ];

    public function go(){

        /// 初始化参数
        $type   = $this->request('type');
        $bdate  = $this->request('bdate');
        $edate  = $this->request('edate');

        $gupiao_service = new GupiaoService;

        /// 执行功能
        Err::try(function () use($type, $bdate, $edate, $gupiao_service){
        
            switch($type){
            case 1:# 新增股票数据  php cmd.php Gupiao 1
            
                $gupiao_service->gupiaoAdd();
            break;
            case 2:# 更新shares_details_byday原始数据（先有1，再有本条）  php cmd.php Gupiao 2
            
                $gupiao_service->updateOriginal();
            break;
            case 3:# 补充shares表发行日期（先有1，2，再有本条）  php cmd.php Gupiao 3
            
                $gupiao_service->updateIssueDate();
            break;
            case 4:# 计算均价（先有2，再有本条）  php cmd.php Gupiao 4
            
                $gupiao_service->maPrice();
            break;
            case 5:# 计算均线角（先有4，再有本条）  php cmd.php Gupiao 5
            
                $gupiao_service->maAngle();
            break;
            case 6:# 补充量价指数  php cmd.php Gupiao 6
            
                $gupiao_service->maAngle();
            break;
            }
        });

/*
        $title = '推送通知数据';
        echo date('Y-m-d H:i:s') . ' ' . __FILE__ . ' ' . $title . '开始'. PHP_EOL;
        $str = '';
        try{
            $type   = $this->argument('type');//参数：'all'表示所有，1=新任务通知数据推送，2=新商品通知数据推送

            $im_message_service = new ImMessageService;
            $e_time = date('Y-m-d H:i:s');
            // $e_time = '2020-05-06 10:00:00';/////////测试wx1
            // $e_time = '2017-10-25 18:00:00';/////////测试wx2
            $b_time = date('Y-m-d H:i:s', strtotime($e_time)-3600);

            switch($type){
            case 1:
                echo date('Y-m-d H:i:s') . ' ' . __FILE__ . '新任务通知数据推送' . '开始'. PHP_EOL;
                $im_message_service->pushNewTasks($b_time, $e_time);
                echo date('Y-m-d H:i:s') . ' ' . __FILE__ . '新任务通知数据推送' . '结束'. PHP_EOL;
            break;
            case 'all':
                echo date('Y-m-d H:i:s') . ' ' . __FILE__ . '新任务通知数据推送' . '开始'. PHP_EOL;
                $im_message_service->pushNewTasks($b_time, $e_time);
                echo date('Y-m-d H:i:s') . ' ' . __FILE__ . '新任务通知数据推送' . '结束'. PHP_EOL;
            break;
            }

        }catch (\Exception $e){
            $str .= $e->getMessage();
        }
        echo date('Y-m-d H:i:s') . ' ' . __FILE__ . ' ' . $title . '结束'. PHP_EOL;

        */
    }
}
