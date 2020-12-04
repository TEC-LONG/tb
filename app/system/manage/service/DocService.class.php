<?php

namespace system\manage\service;
use \Fun;
use \Err;
use model\DocModel;
use model\DocDetailModel;

class DocService {
    
    /**
     * 获取文档列表数据
     */
    public function getList($request){
    
        /// 初始化参数
        $doc_model = new DocModel;

        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [
            [['s_name', 'name'], 'like']
        ], []);

        /// 构建查询对象
        $doc_model = $doc_model->where($_condi);

        /// 分页参数
        $nowPage    = isset($request['pageNum']) ? intval($request['pageNum']) : 1;
        $pagination = $doc_model->pagination($nowPage)->pagination;

        $pagination['numPerPageList'] = [20, 30, 40, 60, 80, 100, 120, 160, 200];

        # 查询数据
        $rows = $doc_model->select([
            'id',
            'title',
            'created_time',
            'update_time'
        ])->limit($pagination['limitM'], $pagination['numPerPage'])->get();

        return [
            'rows'  => $rows,
            'page'  => $pagination
        ];
    }

    /**
     * 新增/编辑 文档功能
     */
    public function post($request){
        /// 初始化参数
        $now        = time();
        $doc_model  = new DocModel;

        if( isset($request['id']) ){/// 编辑
            # 查询已有数据
            $row = $doc_model->where(['id', $request['id']])->find();

            # 新老数据对比，构建编辑数据
            $_upd = Fun::data__need_update($request, $row, ['title', 'descr']);
            if( empty($_upd) ) Err::throw('您还没有修改任何数据！请先修改数据。');

            $_upd['update_time'] = $now;

            # 执行更新
            $re = $doc_model->update($_upd)->where(['id', $request['id']])->exec();
            if( !$re ) Err::throw('编辑操作失败');

        }else{/// 新增

            # 数据是否重复，重复了没必要新增
            $duplicate = $doc_model->select('id')->where(['title', $request['title']])->find();
            if(!empty($duplicate)) Err::throw('文档"<'.$request['name'].'">已经存在！无需重复添加。');

            $insert = [
                'title'         => $request['title'],
                'descr'         => $request['descr'],
                'created_time'  => $now
            ];

            # 执行新增
            $re = $doc_model->insert($insert)->exec();
            if( !$re ) Err::throw('新增操作失败');
        }
    }

    /**
     * 获取某个文档目录数据
     */
    public function getmuluList($request){
    
        /// 初始化参数
        $doc__id            = $request['id'];
        $doc_detail_model   = new DocDetailModel;

        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [], [
            ['doc__id', $doc__id]
        ]);

        /// 查询数据
        $rows = $doc_detail_model->where($_condi)->select([
            'id',
            'doc__id',
            'level',
            'pid',
            'sort',
            'created_time',
            'update_time'
        ])->order('level desc')->get();

        /// 组装数据
        $re = [];
        if( !empty($rows) ){
            
            # 以level做下标进行归档
            $_all_level_rows = [];
            foreach( $rows as $this_row){
            
                $this_level = $this_row['level'];
                if( !isset($_all_level_rows[$this_level]) ){
                    $_all_level_rows[$this_level] = [];
                }

                $_all_level_rows[$this_level][] = $this_row;
            }

            # 根据level排序(升序)
            ksort($_all_level_rows);

            # 对所有level=1的数据按照sort升序排序
            $_level_1       = $_all_level_rows[1];
            $_level_1_sort  = array_reverse(array_column($_level_1, 'sort'));

            ksort($_level_1_sort);

            $_sort_asc_level_1 = [];
            foreach( $_level_1_sort as $_level_1_key){
            
                $_sort_asc_level_1[] = $_level_1[$_level_1_key];
            }

            #
            unset($_all_level_rows[1]);
            foreach( $_sort_asc_level_1 as $level_1_row){
            
            }

            # 将排序好的level=1的数据替换掉原来没有排序的数据
            $_all_level_rows[1] = $_sort_asc_level_1;


        }

        return ['rows' => $rows];
    }

    protected function recursiveDoc(){
    
    }
    
}