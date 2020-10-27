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
        'road',
        'bdate',
        'edate'
    ];

    public function go(){

        /// 初始化参数
        $type   = $this->request('type');
        $road   = $this->request('road', 0);# 0: 网易线路  1: 凤凰财经线路
        $bdate  = $this->request('bdate');
        $edate  = $this->request('edate');

        $gupiao_service = new GupiaoService;

        /**
         * 1. 补充新的股票
         * 补充新发行股票:             $gupiao_service->gupiaoAdd();
         * 更新新增股票的每日数据:      $gupiao_service->updateOriginal();
         * 更新新增股票的发行日期:      $gupiao_service->updateIssueDate();
         * 计算新增股票每日均价:        $gupiao_service->maPrice();
         * 计算新增股票每日均线角:      $gupiao_service->maAngle();
         * 
         * 2. 更新股票每日数据
         * 更新股票的每日数据:      $gupiao_service->updateOriginal();
         * 计算股票每日均价:        $gupiao_service->maPrice();
         * 计算股票每日均线角:      $gupiao_service->maAngle();
         * 
         * 3. 更新股票对应的企业信息
         * 抓取更新股票对应企业最新相关信息:  $gupiao_service->getCompanyDetails();
         * 
         * 4. 计算每日最高价是否创一年新高
         * 抓取更新股票对应企业最新相关信息:  $gupiao_service->yearXingao();
         */

        /// 执行功能
        Err::try(function () use($type, $road, $bdate, $edate, $gupiao_service){

            switch($type){

                /**
                 * 1. 补充新的股票
                 * 补充新发行股票:             $gupiao_service->gupiaoAdd();
                 * 更新新增股票的每日数据:      $gupiao_service->updateOriginal();
                 * 更新新增股票的发行日期:      $gupiao_service->updateIssueDate();
                 * 计算新增股票每日均价:        $gupiao_service->maPrice();
                 * 计算新增股票每日均线角:      $gupiao_service->maAngle();
                 */
                case 1:# php cmd.php Gupiao 1
                    $gupiao_service->gupiaoAdd($road);
                    $gupiao_service->updateOriginal($road);
                    $gupiao_service->updateIssueDate();
                    $gupiao_service->maPrice();
                    $gupiao_service->maAngle();
                break;
                /**
                 * 2. 已网易线路更新股票每日数据
                 * 更新股票的每日数据:      $gupiao_service->updateOriginal();
                 */
                case 2:# php cmd.php Gupiao 2
                    $gupiao_service->updateOriginal(0);
                break;
                /**
                 * 3. 更新股票对应的企业信息
                 * 抓取更新股票对应企业最新相关信息:  $gupiao_service->getCompanyDetails();
                 */
                case 3:# php cmd.php Gupiao 3
                    $gupiao_service->getCompanyDetails();
                break;
                /**
                 * 4. 计算每日最高价是否创一年新高，新低
                 * 以凤凰线路更新最新数据:  $gupiao_service->updateOriginal(1);
                 * 计算股票每日均价:        $gupiao_service->maPrice();
                 * 计算股票每日均线角:      $gupiao_service->maAngle();
                 * 计算一年新高:  $gupiao_service->yearXingao();
                 * 计算一年新低:  $gupiao_service->yearXindi();
                 */
                case 4:# php cmd.php Gupiao 4
                    // $gupiao_service->updateOriginal(1);
                    // $gupiao_service->maPrice();
                    // $gupiao_service->maAngle();
                    // $gupiao_service->yearXingao();
                    $gupiao_service->yearXindi();
                break;
                /**
                 * 5. 分类归档
                 * 分类归档:  $gupiao_service->constructPlate();
                 */
                case 5:# 分类归档  php cmd.php Gupiao 5
            
                    $gupiao_service->constructPlate();
                break;
                case 0:# php cmd.php Gupiao 0
                    // $gupiao_service->test();
                    $gupiao_service->constructPlate();
                    $gupiao_service->shineng();
                break;
            }

            exit;
            
    
            /* switch($type){
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
            case 6:#   php cmd.php Gupiao 6
            
                // $gupiao_service->maAngle();
            break;
            case 7:# 抓取股票对应企业相关信息  php cmd.php Gupiao 7
            
                $gupiao_service->getCompanyDetails();
            break;
            case 8:# 分类拆分  php cmd.php Gupiao 8
            
                $gupiao_service->extraDoing();
            break;
            case 9:# 计算每日最高价是否创一年新高  php cmd.php Gupiao 9
            
                $gupiao_service->yearXingao();
            break;
            } */
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
