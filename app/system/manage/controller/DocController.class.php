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

class DocController extends Controller {

    /**
     * 文档列表
     */
    public function list(){
    
        $ar1 = [12=>'a', 3=>'b', 4=>'c'];
        ksort($ar1);
        var_dump($ar1);
        exit;
        
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
     * 具体文档目录管理 页面
     */
    public function muluList(){
        
        /// 接收数据
        $request = Fun::request()->all();

        /// 初始化参数
        $doc_service = new DocService;

        /// 获取某个文档目录数据
        $info = $doc_service->getmuluList($request);

        /// 分配模板变量&渲染模板
        $this->assign($info);
        $this->display('doc_detail/index.tpl');
    }

    

}