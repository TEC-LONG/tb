<?php

namespace system\manage\controller;
use \system\manage\service\DocService;
use \controller;
use \Validator;
use \Route;
use \Fun;
use \Json;
use \Err;
use model\DocModel;
use model\DocDetailModel;

use model\MenuPermissionModel;
use model\UserGroupPermissionModel;
use system\manage\service\IndexService;

class DocController extends Controller {

    /**
     * 文档列表
     */
    public function list(){
    
        // $ar1 = [12=>'a', 3=>'b', 4=>'c'];
        // ksort($ar1);
        // var_dump($ar1);
        // exit;
        
        /* $arr = [
            ['id'=>1, 'title'=>'a', 'level'=>1, 'pid'=>0, 'sort'=>1],
            ['id'=>2, 'title'=>'b', 'level'=>1, 'pid'=>0, 'sort'=>2],
            ['id'=>3, 'title'=>'c', 'level'=>1, 'pid'=>0, 'sort'=>2],
            ['id'=>4, 'title'=>'d', 'level'=>1, 'pid'=>0, 'sort'=>3],
            ['id'=>5, 'title'=>'e', 'level'=>1, 'pid'=>0, 'sort'=>4],
            
            ['id'=>6, 'title'=>'a-1', 'level'=>2, 'pid'=>1, 'sort'=>3],
            ['id'=>7, 'title'=>'a-2', 'level'=>2, 'pid'=>1, 'sort'=>5],

            ['id'=>8, 'title'=>'b-1', 'level'=>2, 'pid'=>2, 'sort'=>1],
            ['id'=>9, 'title'=>'b-2', 'level'=>2, 'pid'=>2, 'sort'=>2],
            ['id'=>10, 'title'=>'b-3', 'level'=>2, 'pid'=>2, 'sort'=>3],

            ['id'=>14, 'title'=>'e-1', 'level'=>2, 'pid'=>5, 'sort'=>1],
            ['id'=>15, 'title'=>'e-2', 'level'=>2, 'pid'=>5, 'sort'=>2],

            ['id'=>16, 'title'=>'a-1-1', 'level'=>3, 'pid'=>6, 'sort'=>1],
            ['id'=>17, 'title'=>'a-1-2', 'level'=>3, 'pid'=>6, 'sort'=>2],
            ['id'=>18, 'title'=>'a-1-3', 'level'=>3, 'pid'=>6, 'sort'=>3],
            ['id'=>19, 'title'=>'a-1-4', 'level'=>3, 'pid'=>6, 'sort'=>3],

            ['id'=>20, 'title'=>'a-2-1', 'level'=>3, 'pid'=>7, 'sort'=>1],
            ['id'=>21, 'title'=>'a-2-2', 'level'=>3, 'pid'=>7, 'sort'=>2],

            ['id'=>22, 'title'=>'b-2-1', 'level'=>3, 'pid'=>9, 'sort'=>1],
            ['id'=>23, 'title'=>'b-2-2', 'level'=>3, 'pid'=>9, 'sort'=>2],

            ['id'=>24, 'title'=>'b-3-1', 'level'=>3, 'pid'=>10, 'sort'=>1],
            ['id'=>25, 'title'=>'b-3-2', 'level'=>3, 'pid'=>10, 'sort'=>2],
            ['id'=>26, 'title'=>'b-3-3', 'level'=>3, 'pid'=>10, 'sort'=>3],
            ['id'=>27, 'title'=>'b-3-4', 'level'=>3, 'pid'=>10, 'sort'=>3],
        ];

        shuffle($arr);

        $obj = (new DocService);
        $re = $obj->getTidyDoc($arr);
        echo '<pre>';
        print_r($re);
        echo '<hr/>';
        

        $new_re = [];
        $obj->getDocTree($new_re, $re);
        print_r($new_re);

        exit; */


        /// 接收数据
        $request = Fun::request()->all();

        /// 初始化参数
        $doc_service = new DocService;

        /// 获取文档列表数据
        $info = $doc_service->getList($request);

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->display('doc/index.tpl');
    }


    /**
     * 新增/编辑 文档页
     */
    public function edit(){

        /// 接收数据
        $request = Fun::request()->all();

        ///编辑部分
        $info = [];
        if( isset($request['id']) ){
            $info['row'] = (new DocModel)->select('id, title, descr')->where(['id', $request['id']])->find();
        }

        ///分配模板变量&渲染模板
        $this->assign($info);   
        $this->display('doc/edit.tpl');
    }

    /**
     * 校验 groupPost方法 参数
     */
    private function postValidate($request){
    
        $_rule = [
            'title' => 'required'
        ];
        $_rule_msg = [
            'title.required' => '缺少【项目标题】'
        ];

        if( isset($request['id']) ){/// 编辑
        
            $_rule['id']            = 'int';
            $_rule_msg['id.int']    = '非法的id参数';
            $validator = Validator::make($request, $_rule, $_rule_msg);
        }else {/// 新增
            $validator = Validator::make($request, $_rule, $_rule_msg);
        }

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 新增/编辑 文档功能
     */
    public function post(){

        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            $this->postValidate($request);

            /// 初始化参数
            $doc_service = new DocService;

            /// 执行处理
            $doc_service->post($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
            ])->exec('return');
            exit;
        }

        echo Json::vars([
            'statusCode'    => 200,
            'message'       => '操作成功！',
            'navTabId'      => Route::$navtab
        ])->exec('return');
    }

    /**
     * 校验 groupPost方法 参数
     */
    private function muluListValidate($request){
    
        $_rule = [
            'id' => 'required'
        ];
        $_rule_msg = [
            'id.required' => '非法的请求@1'
        ];

        $validator = Validator::make($request, $_rule, $_rule_msg);

        if( !empty($validator->err) ){
        
            // Err::throw($validator->getErrMsg());
            exit($validator->getErrMsg());
        }
    }

    /**
     * 具体文档目录管理 页面
     */
    public function muluList(){
        
        /// 接收数据
        $request = Fun::request()->all();

        /// 校验数据
        $this->muluListValidate($request);

        /// 初始化参数
        $doc_service = new DocService;

        /// 获取某个文档目录数据
        $info = $doc_service->getmuluList($request);

        /// 获取当前文档标题
        $doc = DocModel::select('title')->where(['id', $request['id']])->find();

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('doc__id', $request['id']);
        $this->assign('doc_title', $doc['title']);
        $this->display('doc_detail/index.tpl');
    }

    /**
     * 目录项查找带回页面 页面
     */
    public function lookup(){
        
        /// 接收数据
        $request = Fun::request()->all();

        /// 校验数据
        $this->muluListValidate($request);

        /// 初始化参数
        $doc_service = new DocService;

        /// 获取某个文档目录数据
        $info = $doc_service->getmuluLookup($request);

        /// 获取当前文档标题
        $doc = DocModel::select('title')->where(['id', $request['id']])->find();

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('doc__id', $request['id']);
        $this->assign('doc_title', $doc['title']);
        $this->display('doc_detail/lookup.tpl');
    }

    /**
     * 新增/编辑 目录项 页面
     */
    public function muluEdit(){

        /// 接收数据
        $request = Fun::request()->all();

        ///编辑部分
        $info = [];
        if( isset($request['id']) ){

            # 当前目录项
            $info['row'] = (new DocDetailModel('dd'))->select([
                'dd.id',
                'dd.title',
                'dd.level',
                'dd.pid',
                'dd.sort',
                'dd1.title' => 'ptitle'
            ])->leftjoin('tl_doc_detail dd1', 'dd1.id=dd.pid')
            ->where(['dd.id', $request['id']])->find();
        }

        ///分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('navtab', Route::$navtab);
        $this->assign('doc__id', $request['doc__id']);
        $this->display('doc_detail/edit.tpl');
    }

    /**
     * 校验 muluPostValidate方法 参数
     */
    private function muluPostValidate($request){
    
        $_rule = [
            'title' => 'required'
        ];
        $_rule_msg = [
            'title.required' => '缺少【目录项标题】'
        ];

        if( isset($request['id']) ){/// 编辑
        
            $_rule['id']            = 'int';
            $_rule_msg['id.int']    = '非法的id参数';
            $validator = Validator::make($request, $_rule, $_rule_msg);
        }else {/// 新增
            $validator = Validator::make($request, $_rule, $_rule_msg);
        }

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 新增/编辑 目录项 功能
     */
    public function muluPost(){
    
        try{
            /// 接收数据
            $request = Fun::request()->all();

            /// 检查数据
            $this->muluPostValidate($request);

            /// 初始化参数
            $doc_service = new DocService;

            /// 执行处理
            $doc_service->muluPost($request);

        }catch(\Exception $err){

            echo Json::vars([
                'statusCode'    => 300,
                'message'       => $err->getMessage(),
            ])->exec('return');
            exit;
        }

        echo Json::vars([
            'statusCode'    => 200,
            'message'       => '操作成功！',
            'doc__id'       => $request['doc__id'],
            'navTabId'      => Route::$navtab
        ])->exec('return');
    }

    /**
     * 校验 muluEditContent方法 参数
     */
    private function muluEditContentValidate($request){
    
        $_rule = [
            'id' => 'required'
        ];
        $_rule_msg = [
            'id.required' => '非法的请求@1'
        ];

        $validator = Validator::make($request, $_rule, $_rule_msg);

        if( !empty($validator->err) ){
        
            // Err::throw($validator->getErrMsg());
            exit($validator->getErrMsg());
        }
    }

    /**
     * 编辑 文档内容 页面
     */
    public function muluEditContent(){

        /// 接收数据
        $request = Fun::request()->all();

        /// 校验数据
        $this->muluEditContentValidate($request);

        /// 已有数据
        $info['row'] = DocDetailModel::where(['id', $request['id']])->find();

        ///分配模板变量&渲染模板
        $this->assign($info);
        $this->display('doc_detail/content.tpl');
    }
    
    /**
     * 校验 muluEditContentPost方法 参数
     */
    private function muluEditContentPostValidate($request){
    
        $_rule = [
            'id' => 'required'
        ];
        $_rule_msg = [
            'id.required' => '非法的请求@1'
        ];

        $validator = Validator::make($request, $_rule, $_rule_msg);

        if( !empty($validator->err) ){
        
            Err::throw($validator->getErrMsg());
        }
    }

    /**
     * 编辑 文档内容 功能
     */
    public function muluEditContentPost(){
    
        try{
            /// 接收数据
            $request = Fun::request()->all('n');

            /// 检查数据
            $this->muluEditContentPostValidate($request);

            /// 初始化参数
            $doc_detail_model           = new DocDetailModel;
            $doc_detail_id              = $request['id'];
            $request['content_html']    = $request['editormd-html-code'];
            $request['content']         = str_replace('"', '&quot;',str_replace('\\', '\\\\', $request['content']));

            #查询已有数据
            $row    = $doc_detail_model->where(['id', $doc_detail_id])->find();
            $_upd   = Fun::data__need_update($request, $row, ['content', 'content_html']);
            if( empty($_upd) ) Err::throw('您还没有修改任何数据！请先修改数据。');

            /// 执行处理
            $_upd['update_time'] = time();

            # 更新数据
            $doc_detail_model->update($_upd)
            ->where(['id', $doc_detail_id])
            ->exec();

        }catch(\Exception $err){

            Fun::jump('/system/manage/doc/mulu/edit/content?id='.$doc_detail_id, $err->getMessage());
        }

        Fun::jump('/system/manage/doc/mulu/edit/content?id='.$doc_detail_id, '操作成功！');
    }

    /**
     * 校验 muluEditContentPost方法 参数
     */
    private function infoValidate($request){
    
        $_rule = [
            'id' => 'int'
        ];
        $_rule_msg = [
            'id.int' => '非法的参数@1'
        ];

        $validator = Validator::make($request, $_rule, $_rule_msg);

        if( !empty($validator->err) ){
        
            exit($validator->getErrMsg());
        }
    }

    /**
     * 文档 页面
     */
    public function info(){
    
        /// 接收数据
        $request = Fun::request()->all();

        /// 校验数据
        $this->infoValidate($request);

        /// 初始化参数
        $doc_service = new DocService;

        /// 获取某个文档目录数据
        $info = $doc_service->getmuluJui($request);

        /// 获取当前文档标题
        $doc = DocModel::select('title')->where(['id', $request['id']])->find();

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->assign('doc_title', $doc['title']);
        $this->assign('doc_detail_id', $request['doc_detail_id']);
        $this->display('doc/info.tpl');
    }

    /**
     * 文档内容navtab 页面
     */
    public function docDetailContent(){
    
        /// 接收数据
        $request = Fun::request()->all();

        /// 校验数据
        $this->infoValidate($request);

        /// 初始化参数
        $doc_detail_id      = $request['id'];
        $doc_model          = new DocModel;
        $doc_detail_model   = new DocDetailModel;

        /// 当前doc_detail
        $this_doc_detail = $doc_detail_model->select('doc__id, title, created_time, update_time, content_html, content')->where(['id', $doc_detail_id])->find();

        /// doc
        $doc = $doc_model->select('title')->where(['id', $this_doc_detail['doc__id']])->find();

        /// 渲染模板
        $this->assign('this_doc_detail', $this_doc_detail);
        $this->assign('ptitle', $doc['title']);

        if( strchr($this_doc_detail['title'], '.')=='.offical' ){
        
            $this->display('doc/content_offical.tpl');
        }else{
            $this->display('doc/content.tpl');
        }
    }

    

}