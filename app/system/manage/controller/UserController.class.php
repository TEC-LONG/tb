<?php

namespace system\manage\controller;
use \system\manage\service\UserService;
use \model\UserModel;
use \controller;
use \Json;
use \Err;
use \Fun;
use \TB;
class UserController extends Controller {

    public function index(){
    
        $user_service = new UserService;
        /// 接收数据
        $request = Fun::request()->all();

        /* //查询条件(融合搜索条件)
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

        //分配模板变量&渲染模板
        $this->assign($this->_datas);
        $this->display('user/index.tpl'); */
    }


    /**
     * @method  _condition_string
     * 方法作用: 将符合要求的指定字段，处理为字符串类型的where条件
     * 
     * @param    $request       array    [表单传值的集合]
     * @param    $form_elems    array    [指定的条件字段及其规则，如：]
                $form_elems = [
                    ['acc',         'like'],
                    ['nickname',    'like']
                ];
     * @param    $con_arr       array    [默认的条件字段，如：$con_arr=['is_del', 0];]
     * 
     * @return    string    [字符串类型的条件语句]
     */
    protected function _condition_string($request, $form_elems, $con_arr){

        $con_search = $this->_condition($request, $form_elems);
        $con_default = $this->_condition($con_arr, [], 2);
        $con_arr = array_merge($con_default, $con_search);//将非查询的数据与查询的数据进行合并，形成完整的条件数组数据
        
        $con = [];
        /*
        $con_arr = [
            'name' => '="zhangsan"',
            'post_date' => [
                ['>=1234567'],
                ['<=7654321']
            ]
        ]
        */
        foreach( $con_arr as $field=>$val){
        
            if( is_array($val) ){
                $con[] = $field . $val[0];
                $con[] = $field . $val[1];
            }else{
                $con[] = $field . $val;
            }
        }

        $con = implode(' and ', $con);

        return $con;
    }
    /**
     * 方法名:_condition
     * 方法作用:处理条件初稿，得到可使用的条件数组集合
     * 参数：
     * $request
     * $form_elems
     * $type    处理方式，1=处理带限制规则的条件，当$type为1时，只需要传递第一个参数；2=处理不带限制规则的条件
     * return: array
     */
    protected function _condition($request, $form_elems=[], $type=1){
    
        $con = [];
        if( $type==1 ){

            foreach( $form_elems as $elem){

                if($elem[1]==='time-in'){
                    $has_begin = isset($request['b_'.$elem[0]])&&$request['b_'.$elem[0]]!=='';
                    $has_end = isset($request['e_'.$elem[0]])&&$request['e_'.$elem[0]]!=='';
                    if(!$has_begin&&!$has_end) continue;
                }else{
                    if(!isset($request[$elem[0]])||$request[$elem[0]]==='') continue;
                }
                
                if( isset($elem[1]) ){//y有特殊处理标记

                    if( $elem[1]==='mul' ){//数组
                        
                        $str_arr = [];
                        //        [1, 3, 4]
                        foreach( $request[$elem[0]] as $val){

                            $str_arr[] = $val;
                        }
                        //                             1|3|4
                        $con[$elem[0]] = ' REGEXP "' . implode('|', $str_arr) . '"';
                    }elseif( $elem[1]==='like' ){//模糊匹配

                        $con[$elem[0]] = ' like "%' . $request[$elem[0]] . '%"';
                    }elseif ( $elem[1]==='equal' ) {
                        
                        $con[$elem[0]] = '="' . $request[$elem[0]] . '"';
                    }elseif ( $elem[1]==='time-in' ) {
                        
                        $con[$elem[0]][0] = '>=' . strtotime($request['b_'.$elem[0]]);
                        $con[$elem[0]][1] = '<=' . strtotime($request['e_'.$elem[0]]);
                    }
                
                }else{//普通

                    //     'name'                     'name'
                    $con[$elem[0]] = '="' . $request[$elem[0]] . '"';
                }
            }
        }elseif ($type==2) {
            
            if( is_array($request[0]) ){
                    
                foreach( $request as $k=>$v){

                    if( count($v)==3 ){
                        $con[$v[0]] = $v[1] . '"' . $v[2] . '"';
                    }elseif( strpos($v[1], '=')!==false ){

                        // $con[$k][$v[0]] = $v[1];
                        $con[$v[0]] = $v[1];
                    }else{
                        // $con[$k][$v[0]] = '="' . $v[1] . '"';
                        $con[$v[0]] = '="' . $v[1] . '"';
                    }
                }
            }else{
                
                if( count($request)==3 ){

                    $con[$request[0]] = $request[1] . '"' . $request[2] . '"';
                }elseif( strpos($request[1], '=')!==false ){

                    $con[$request[0]] = $request[1];
                }else{
                    $con[$request[0]] = '="' . $request[1] . '"';
                }
            }
        }
        
        return $con;
    }

}