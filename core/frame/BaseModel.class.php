<?php

use \TB;

class BaseModel extends TB{

    public $pagination=[];

    /**
     * 构造方法
     */
    public function __construct($tb_new_alias=''){
    
        parent::__construct();

        /// 重置表名别名
        if( !empty($tb_new_alias) ){
        
            $this->alias = $tb_new_alias;
        }
    }

    /**
     * method: 获得适用于jui分页的参数
     */
    public function pagination($nowPage=1, $numPerPage=32){
        echo '<hr/>';
        
var_dump($this);
exit;

        /// 得到当前的查询语句
        $sql = $this->get_sql();

        /// 将select与from之间的内容替换成统计聚合函数
        $pattern = '/^select.*from/i';# 忽略大小写
        preg_match($pattern, $sql, $matches);

        if( isset($matches[0]) ){
            
            $this->sql = $sql = str_replace($matches[0], 'select count(*) as num from', $sql);
            
            $re = $this->query(1, $sql);
            $this->pagination['pageNum']    = $nowPage;
            $this->pagination['numPerPage'] = $numPerPage;

            if( $re ){
                $row = $this->pdostatement->fetch(\PDO::FETCH_ASSOC);
                
                $this->pagination['totalNum']       = $row['num'];
                $this->pagination['totalPageNum']   = intval(ceil(($this->pagination['totalNum']/$this->pagination['numPerPage'])));
                $this->pagination['limitM']         = ($this->pagination['pageNum']-1)*$this->pagination['numPerPage'];
            }

            return $this;
        }
    }
}