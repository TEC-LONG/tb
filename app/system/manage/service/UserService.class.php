<?php

namespace system\manage\service;
use \Fun;

class UserService {
    
    /**
     * 获取用户列表数据
     */
    public function getIndexList($request){

        /// 构建查询条件
        $_condi = Fun::tb_condition($request, [
            ['acc', 'like'],
            ['nickname', 'like']
        ], [
            ['is_del', 0],
            ['level', 1]
        ]);
    
        //查询条件(融合搜索条件)
        $con_arr = [['is_del', 0], ['level', 1]];

        #需要搜索的字段
        $form_elems = [
            ['acc', 'like'],
            ['nickname', 'like']
        ];

        $con = $this->_condition_string($request, $form_elems, $con_arr);//将条件数组数据转换为条件字符串

        //将搜索的原始数据扔进模板
        $this->_datas['search'] = $this->_get_ori_search_datas($request, $form_elems);

        //分页参数
        $this->_datas['page'] = $page = $this->_page('user', $con, $request);

        //查询数据
        $this->_datas['rows'] = M()->table('user as u')->select('u.*, ug.name as gname')
        ->leftjoin('user_group as ug', 'ug.id=u.user_group__id')
        ->where($con)
        ->limit($page['limitM'] . ',' . $page['numPerPage'])
        ->get();
    }

    /**
     * @method  _page
     * 方法作用: 构建分页参数
     * 
     * @param    $tb            string      [需要统计总的记录条数的表其表名]
     * @param    $condition     string      [统计总记录条数的条件，直接传递给模型，故条件的格式与模型where方法所需的条件格式保持统一]
     * @param    $request       array       [表单传值的集合，包含了分页所需的表单参数]
     * @param    $num_per_page  int         [每页显示的数据条数，默认为31条]
     * 
     * @return  array           [包含分页各项数据的数组]
     */
    protected function _page($tb, $condition, $request, $num_per_page=31){
        #分页参数
        $page = [];
        $page['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];
        $page['pageNum'] = $pageNum = isset($request['pageNum']) ? intval($request['pageNum']) : (isset($_COOKIE[$this->navtab.'pageNum']) ? intval($_COOKIE[$this->navtab.'pageNum']) : 1);
        setcookie($this->navtab.'pageNum', $pageNum);
        $page['numPerPage'] = $numPerPage = isset($request['numPerPage']) ? intval($request['numPerPage']) : $num_per_page;
        $tmp_arr_totalNum = M()->table($tb)->select('count(*) as num')->where($condition)->find();
        $page['totalNum'] = $totalNum = $tmp_arr_totalNum['num'];
        $page['totalPageNum'] = intval(ceil(($totalNum/$numPerPage)));
        $page['limitM'] = ($pageNum-1)*$numPerPage;

        return $page;
    }
}