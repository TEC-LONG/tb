<?php
namespace model;
use \BaseModel;

class SharesGroupsModel extends BaseModel{

    protected $table = 'tl_shares__groups';

    /**
     * 根据股票id获取其所在组
     * @param   $shares__ids    int|array   股票单个id值 或 id数组集合
     */
    public function getInfoBySharesId($shares__ids){
    
        /// 初始化参数
        $re     = [];
        $condi  = [];

        /// 校验shares__ids
        if( empty($shares__ids) ){
            return $re;
        }

        if( is_array($shares__ids) ){
        
            $condi = ['shares__id', 'in', '('.implode(',', $shares__ids).')'];
        }else{
            $condi = ['shares__id', $shares__ids];
        }

        /// 查询数据
        $info = $this->select('shares__id, groups__id')->where($condi)->get();
        if( empty($info) ) return $re;

        /// 整理数据
        foreach( $info as $_r){

            $this_shares__id = $_r['shares__id'];
            $this_groups__id = $_r['groups__id'];
        
            if( !isset($re[$this_shares__id]) ){
            
                $re[$this_shares__id] = [];
            }

            if( !in_array($this_groups__id, $re[$this_shares__id]) ){
            
                $re[$this_shares__id][] = $this_groups__id;
            }
        }

        return $re;
    }

    /**
     * 新增或删除
     * @param   $type   int     =1 表示新增； =2 表示删除
     */
    public function edit($groups__id, $shares__id, $type){

        $_condi = [
            ['groups__id', $groups__id],
            ['shares__id', $shares__id]
        ];

        $has_row = $this->select('id')->where($_condi)->find();

        if( $type==1 ){/// 新增

            if( !$has_row ){
            
                $data = [
                    'groups__id' => $groups__id,
                    'shares__id' => $shares__id,
                    'created_time'  => time()
                ];

                if( $this->insert($data)->exec() ){
                    $re = '加入成功';
                }else{
                    $re = '加入失败';
                }
            }else{
                $re = '已经在组内，无需重复加入';
            }
        
        }elseif( $type==2 ){/// 删除
        
            if( $has_row ){
            
                if( $this->where($_condi)->delete() ){
                    $re = '退出组操作成功';
                }else {
                    $re = '退出组操作失败';
                }
            }else {
                $re = '不在组内，无法删除';
            }
        }
    
        return $re;
    }

    /**
     * 根据分组获取股票信息
     */
    public function getSharesByGroupName($name){
    
        /// 初始化参数
        $names      = [1=>'持仓组', 2=>'蓝筹组', 3=>'特别观察组'];
        $groups__id = array_search($name, $names);

        /// 查询股票信息
        $re = $this->alias('sg')->select([
            's.id',
            's.title',
            's.code',
            's.total_day_num'
        ])
        ->leftjoin('tl_shares s', 's.id=sg.shares__id')
        ->where([
            ['groups__id', $groups__id]
        ])->get();

        return $re;
    }

}
