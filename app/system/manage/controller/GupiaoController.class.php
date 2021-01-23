<?php

namespace system\manage\controller;
use \controller;
use \Validator;
use \Route;
use \Json;
use \Err;
use \Fun;
use model\GroupsModel;
use model\SharesGroupsModel;
use model\SharesModel;
use system\manage\service\GupiaoService;

class GupiaoController extends Controller {

    /**
     * 根据 股票代码 或 股票名称 模糊搜索股票
     */
    public function searchByCodeOrName(){

        /// 初始化参数
        $shares_model           = new SharesModel;
        $groups_model           = new GroupsModel;
        $shares__groups_model   = new SharesGroupsModel;
        $gupiao_service         = new GupiaoService;
        $request                = Fun::request()->all();
        $notice                 = 'no';

        if( isset($request['groups__id'])&&$request['groups__id']!=0 ){
        
            $notice = $shares__groups_model->edit($request['groups__id'], $request['shares__id'], $request['type']);
        }

        /// 模糊匹配数据
        $info = $shares_model->select([
            'id',
            'title',
            'code'
        ])->where('code like "%'.$request['code_or_name'].'%" or title like "%'.$request['code_or_name'].'%"')
        ->where(['is_deprecated', 0])
        ->limit(5)
        ->get();

        $shares__ids = array_column($info, 'id');

        # 获取当前匹配到的股票所加入的组
        $has_shares__groups = $shares__groups_model->getInfoBySharesId($shares__ids);

        /// 获取股票组别
        $groups = $groups_model->getGroups(1);

        /// 整理股票已加入的组和未加入的组
        foreach( $info as &$row){

            $this_shares__id = $row['id'];

            if( isset($has_shares__groups[$this_shares__id]) ){
            
                $row['groups'] = $has_shares__groups[$this_shares__id];
            }else{

                $row['groups'] = [];
            }
        }

        $this->assign('info', $info);
        $this->assign('notice', $notice);
        $this->assign('groups', $groups);
        $this->display('tes.tpl');
    }


}