<?php

namespace cmd\service;

class GupiaoCommonService
{
    /**
     * curl
     */
    public function sendCurl($url,$params=false,$ispost=0){
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
     * 正则匹配指定开始到指定结束字符的内容
     */
    public function regexInfo($b_str, $e_str, $content, $mode='Us'){
    
        preg_match('/'.$b_str.'.*'.$e_str.'/'.$mode, $content, $matches);

        if( empty($matches) ) return '';

        $b_str = preg_replace('/\s+/', '', str_replace('\\', '', str_replace('\s+', '', $b_str)));
        $e_str = str_replace('\\', '', $e_str);
        $matches[0] = preg_replace('/\s+/', '', $matches[0]);
        
        return str_replace($e_str, '', str_replace($b_str, '', $matches[0]));
    }

    /**
     * 通过正则，将匹配到的目标替换为空字符串
     */
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
     * 输出百分比公共方法
     */
    public function outputPercent_old($countData, $type='init'){
    
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

    /**
     * 输出百分比公共方法
     */
    public function outputPercent($countData, $callback){
    
        $dividend   = 1;
        $divisor    = count($countData);
        foreach( $countData as $key=>$value){
 
            $percent = number_format(($dividend/$divisor)*100, 4) . '%';
         
            $callback($key, $value, $dividend, $percent);
 
            echo  '完成：'. $percent . PHP_EOL;
            $dividend++;
         }
    }
}
