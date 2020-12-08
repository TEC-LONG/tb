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

        /// 查询数据
        $rows = $doc_detail_model->getDocDetailByDocid($request, $doc__id);

        /* $rows = [
            ['id'=>1, 'create_time'=>'1223423423', 'title'=>'a', 'level'=>1, 'pid'=>0, 'sort'=>1],
            ['id'=>2, 'create_time'=>'1223423423', 'title'=>'b', 'level'=>1, 'pid'=>0, 'sort'=>2],
            ['id'=>3, 'create_time'=>'1223423423', 'title'=>'c', 'level'=>1, 'pid'=>0, 'sort'=>2],
            ['id'=>4, 'create_time'=>'1223423423', 'title'=>'d', 'level'=>1, 'pid'=>0, 'sort'=>3],
            ['id'=>5, 'create_time'=>'1223423423', 'title'=>'e', 'level'=>1, 'pid'=>0, 'sort'=>4],
            
            ['id'=>6, 'create_time'=>'1223423423', 'title'=>'a-1', 'level'=>2, 'pid'=>1, 'sort'=>3],
            ['id'=>7, 'create_time'=>'1223423423', 'title'=>'a-2', 'level'=>2, 'pid'=>1, 'sort'=>5],

            ['id'=>8, 'create_time'=>'1223423423', 'title'=>'b-1', 'level'=>2, 'pid'=>2, 'sort'=>1],
            ['id'=>9, 'create_time'=>'1223423423', 'title'=>'b-2', 'level'=>2, 'pid'=>2, 'sort'=>2],
            ['id'=>10, 'create_time'=>'1223423423', 'title'=>'b-3', 'level'=>2, 'pid'=>2, 'sort'=>3],

            ['id'=>14, 'create_time'=>'1223423423', 'title'=>'e-1', 'level'=>2, 'pid'=>5, 'sort'=>1],
            ['id'=>15, 'create_time'=>'1223423423', 'title'=>'e-2', 'level'=>2, 'pid'=>5, 'sort'=>2],

            ['id'=>16, 'create_time'=>'1223423423', 'title'=>'a-1-1', 'level'=>3, 'pid'=>6, 'sort'=>1],
            ['id'=>17, 'create_time'=>'1223423423', 'title'=>'a-1-2', 'level'=>3, 'pid'=>6, 'sort'=>2],
            ['id'=>18, 'create_time'=>'1223423423', 'title'=>'a-1-3', 'level'=>3, 'pid'=>6, 'sort'=>3],
            ['id'=>19, 'create_time'=>'1223423423', 'title'=>'a-1-4', 'level'=>3, 'pid'=>6, 'sort'=>3],

            ['id'=>20, 'create_time'=>'1223423423', 'title'=>'a-2-1', 'level'=>3, 'pid'=>7, 'sort'=>1],
            ['id'=>21, 'create_time'=>'1223423423', 'title'=>'a-2-2', 'level'=>3, 'pid'=>7, 'sort'=>2],

            ['id'=>22, 'create_time'=>'1223423423', 'title'=>'b-2-1', 'level'=>3, 'pid'=>9, 'sort'=>1],
            ['id'=>23, 'create_time'=>'1223423423', 'title'=>'b-2-2', 'level'=>3, 'pid'=>9, 'sort'=>2],

            ['id'=>24, 'create_time'=>'1223423423', 'title'=>'b-3-1', 'level'=>3, 'pid'=>10, 'sort'=>1],
            ['id'=>25, 'create_time'=>'1223423423', 'title'=>'b-3-2', 'level'=>3, 'pid'=>10, 'sort'=>2],
            ['id'=>26, 'create_time'=>'1223423423', 'title'=>'b-3-3', 'level'=>3, 'pid'=>10, 'sort'=>3],
            ['id'=>27, 'create_time'=>'1223423423', 'title'=>'b-3-4', 'level'=>3, 'pid'=>10, 'sort'=>3],
        ];
        shuffle($rows); */

        /// 组装数据
        $tree_html = '';
        if( !empty($rows) ){
            
            # 不同pid下的数据按sort排序
            $tidy_doc = $this->getTidyDoc($rows);

            # 整理目录树
            $tree = [];
            // $this->getDocTreeJui($tree, $tidy_doc);
            $this->getDocTree($tree, $tidy_doc);

            # 根据目录树得到html
            // $tree_html = $this->getDocTreeHtmlJui($tree);
            $tree_html = $this->getDocTreeHtml($tree);
        }

        return ['tree_html' => $tree_html];
    }

    /**
     * 获取 目录查找带回 页面数据
     */
    public function getmuluLookup($request){
    
        /// 初始化参数
        $doc__id            = $request['id'];
        $doc_detail_model   = new DocDetailModel;

        /// 查询数据
        $rows = $doc_detail_model->getDocDetailByDocid($request, $doc__id);

        /* $rows = [
            ['id'=>1, 'create_time'=>'1223423423', 'title'=>'a', 'level'=>1, 'pid'=>0, 'sort'=>1],
            ['id'=>2, 'create_time'=>'1223423423', 'title'=>'b', 'level'=>1, 'pid'=>0, 'sort'=>2],
            ['id'=>3, 'create_time'=>'1223423423', 'title'=>'c', 'level'=>1, 'pid'=>0, 'sort'=>2],
            ['id'=>4, 'create_time'=>'1223423423', 'title'=>'d', 'level'=>1, 'pid'=>0, 'sort'=>3],
            ['id'=>5, 'create_time'=>'1223423423', 'title'=>'e', 'level'=>1, 'pid'=>0, 'sort'=>4],
            
            ['id'=>6, 'create_time'=>'1223423423', 'title'=>'a-1', 'level'=>2, 'pid'=>1, 'sort'=>3],
            ['id'=>7, 'create_time'=>'1223423423', 'title'=>'a-2', 'level'=>2, 'pid'=>1, 'sort'=>5],

            ['id'=>8, 'create_time'=>'1223423423', 'title'=>'b-1', 'level'=>2, 'pid'=>2, 'sort'=>1],
            ['id'=>9, 'create_time'=>'1223423423', 'title'=>'b-2', 'level'=>2, 'pid'=>2, 'sort'=>2],
            ['id'=>10, 'create_time'=>'1223423423', 'title'=>'b-3', 'level'=>2, 'pid'=>2, 'sort'=>3],

            ['id'=>14, 'create_time'=>'1223423423', 'title'=>'e-1', 'level'=>2, 'pid'=>5, 'sort'=>1],
            ['id'=>15, 'create_time'=>'1223423423', 'title'=>'e-2', 'level'=>2, 'pid'=>5, 'sort'=>2],

            ['id'=>16, 'create_time'=>'1223423423', 'title'=>'a-1-1', 'level'=>3, 'pid'=>6, 'sort'=>1],
            ['id'=>17, 'create_time'=>'1223423423', 'title'=>'a-1-2', 'level'=>3, 'pid'=>6, 'sort'=>2],
            ['id'=>18, 'create_time'=>'1223423423', 'title'=>'a-1-3', 'level'=>3, 'pid'=>6, 'sort'=>3],
            ['id'=>19, 'create_time'=>'1223423423', 'title'=>'a-1-4', 'level'=>3, 'pid'=>6, 'sort'=>3],

            ['id'=>20, 'create_time'=>'1223423423', 'title'=>'a-2-1', 'level'=>3, 'pid'=>7, 'sort'=>1],
            ['id'=>21, 'create_time'=>'1223423423', 'title'=>'a-2-2', 'level'=>3, 'pid'=>7, 'sort'=>2],

            ['id'=>22, 'create_time'=>'1223423423', 'title'=>'b-2-1', 'level'=>3, 'pid'=>9, 'sort'=>1],
            ['id'=>23, 'create_time'=>'1223423423', 'title'=>'b-2-2', 'level'=>3, 'pid'=>9, 'sort'=>2],

            ['id'=>24, 'create_time'=>'1223423423', 'title'=>'b-3-1', 'level'=>3, 'pid'=>10, 'sort'=>1],
            ['id'=>25, 'create_time'=>'1223423423', 'title'=>'b-3-2', 'level'=>3, 'pid'=>10, 'sort'=>2],
            ['id'=>26, 'create_time'=>'1223423423', 'title'=>'b-3-3', 'level'=>3, 'pid'=>10, 'sort'=>3],
            ['id'=>27, 'create_time'=>'1223423423', 'title'=>'b-3-4', 'level'=>3, 'pid'=>10, 'sort'=>3],
        ];
        shuffle($rows); */

        /// 组装数据
        $tree_html = '';
        if( !empty($rows) ){
            
            # 不同pid下的数据按sort排序
            $tidy_doc = $this->getTidyDoc($rows);

            # 整理目录树
            $tree = [];
            $this->getDocTreeJui($tree, $tidy_doc);

            # 根据目录树得到html
            $tree_html = $this->getDocTreeHtmlJui($tree, 1);
        }

        return ['tree_html' => $tree_html];
    }

    /**
     * 整理得到树状菜单html
     */
    public function getDocTreeHtml($tree){
    
        $_html = '';
        foreach( $tree as $row){
        
            if( $row['level']>5 ) continue;

            $_this_str = '|--- '.date('Y.m.d H:i', $row['created_time']).' ----- '.date('Y.m.d H:i', (empty($row['update_time'])?$row['created_time']:$row['update_time'])).' ';
            for ($i=0; $i < ((5-$row['level']+1)*15); $i++) { 
            // for ($i=0; $i < ($row['level']*18); $i++) { 
                $_this_str .= '--';
            }
            $_html .= '<span><a href="http://www.baidu.com" target="_blank">'.$_this_str.$row['title'].' ['.$row['sort'].']</a></span><br/>';
        }

        return $_html;
    }

    /**
     * 整理得到树状菜单html(适配jui)
     */
    public function getDocTreeHtmlJui($tree, $is_lookup=0){
    
        $_innerHtml = '';
        $this->recursiveDocTreeLiJui($_innerHtml, $tree, $is_lookup);

        $_html = '';
        if( !empty($_innerHtml) ){
        
            $_html = '<ul class="tree">'.$_innerHtml.'</ul>';
        }
        
        return $_html;
    }

    public function recursiveDocTreeLiJui(&$innerHtml, $tree, $is_lookup){
    
        foreach( $tree as $row){

            /// 初始化参数
            $self   = $row['self'];
            $son    = isset($row['son']) ? $row['son'] : [];
            
            /// 自己
            if( $is_lookup==1 ){
            
                $innerHtml .= '<li><a href="javascript:" onclick="$.bringBack({id:'.$self['id'].',level:'.$self['level'].',title:\''.$self['title'].'\'})">'.$self['title'].'['.$self['sort'].']</a>';
            }else{

                $innerHtml .= '<li><a href="http://www.baidu.com" target="_blank">'.$self['title'].'</a>';
            }
            if( !empty($son) ){
            
                $innerHtml .= '<ul>';
                $this->recursiveDocTreeLiJui($innerHtml, $son, $is_lookup);
                $innerHtml .= '</ul></li>';
            }else{
                $innerHtml .= '</li>';
            }
        }
    }

    /**
     * 对某个指定的doc，整理得到最终的目录树(适配jui树状菜单)
     */
    public function getDocTreeJui(&$tree, $all, $pid=0){
    
        $counter = 0;
        foreach( $all as $row){

            /// 初始化参数
            $this_id    = $row['id'];
            $this_pid   = $row['pid'];

            if( $this_pid==$pid ){

                if( $this_pid!=0 ){/// 非一级
                    
                    if( !isset($tree['son']) ){
                    
                        $tree['son'] = [];
                    }

                    if( !isset($tree['son'][$this_id]) ){
                    
                        $tree['son'][$this_id] = [];
                    }

                    $serial_numb    = DocDetailModel::getSerialNumb($row['level'], $counter);
                    $row['title']   = $serial_numb . $row['title'];
                    $tree['son'][$this_id]['self'] = $row;
                    $counter++;

                    $this->getDocTreeJui($tree['son'][$this_id], $all, $this_id);

                }else{/// 一级
                    
                    if( !isset($tree[$this_id]) ){
                    
                        $tree[$this_id] = [];
                    }
    
                    if( !isset($tree[$this_id]['self']) ){
    
                        $serial_numb    = DocDetailModel::getSerialNumb($row['level'], $counter);
                        $row['title']   = $serial_numb . $row['title'];
                        $tree[$this_id]['self'] = $row;
                        $counter++;
                    }
                    $this->getDocTreeJui($tree[$this_id], $all, $this_id);
                }
            }
        }
    }

    /**
     * 对某个指定的doc，整理得到最终的目录树
     */
    public function getDocTree(&$tree, $all, $pid=0){
    
        $counter = 0;
        foreach( $all as $row){
        
            if( $row['pid']==$pid ){
            
                $serial_numb    = DocDetailModel::getSerialNumb($row['level'], $counter);
                $row['title']   = $serial_numb . $row['title'];
                $tree[] = $row;

                $this->getDocTree($tree, $all, $row['id']);

                $counter++;
            }
        }
    }

    /**
     * 对某个指定的doc，其所有的目录项，不同pid下的数据按sort排序
     */
    public function getTidyDoc($rows){
    
        /// 以level做下标进行归档
        $_all_level_rows = [];
        foreach( $rows as $this_row){
        
            $this_level = $this_row['level'];
            if( !isset($_all_level_rows[$this_level]) ){
                $_all_level_rows[$this_level] = [];
            }

            $_all_level_rows[$this_level][] = $this_row;
        }

        /// 根据下标level值排序(升序)
        ksort($_all_level_rows);

        /// 对所有数据 以pid归档，然后按照sort升序排序
        $_after_sort_asc_all = [];
        foreach( $_all_level_rows as $level_x){

            # 获得当前所有不重复的pid值
            $this_all_pid = array_column($level_x, 'sort');
            $this_all_pid = array_unique($this_all_pid);
            
            # 根据pid归档数据
            $_t = [];
            foreach( $level_x as $level_x_row){
            
                $this_pid = $level_x_row['pid'];
                if( !isset($_t[$this_pid]) ){
                    $_t[$this_pid] = [];
                }

                $_t[$this_pid][] = $level_x_row;
            }

            # 每个归档pid的集合按照sort升序排序
            foreach( $_t as $this_pid=>$_t_pid_set){
            
                $this_t_pid_set_sort = array_column($_t_pid_set, 'sort');
                natsort($this_t_pid_set_sort);## 保持键名不变，按sort值升序排序

                $this_after_sort_asc = [];
                foreach( $this_t_pid_set_sort as $_t_pid_set_key=>$_t_pid_set_val){
                    $this_after_sort_asc[] = $_t_pid_set[$_t_pid_set_key];
                }

                $_t[$this_pid] = $this_after_sort_asc;
            }

            ## 排序好的数据覆盖掉旧的数据
            foreach( $_t as $_t_pid_set){
            
                foreach( $_t_pid_set as $row){
                
                    $_after_sort_asc_all[] = $row;
                }
            }
        }

        return $_after_sort_asc_all;
    }

    /**
     * 新增/编辑 目录项
     */
    public function muluPost($request){
    
        /// 初始化参数
        $now        = time();
        $doc_detail_model  = new DocDetailModel;

        if( isset($request['id']) ){/// 编辑
            # 查询已有数据
            $row = $doc_detail_model->where(['id', $request['id']])->find();

            # 新老数据对比，构建编辑数据
            $_upd = Fun::data__need_update($request, $row, ['title', 'descr']);
            if( empty($_upd) ) Err::throw('您还没有修改任何数据！请先修改数据。');

            $_upd['update_time'] = $now;

            # 执行更新
            $re = $doc_detail_model->update($_upd)->where(['id', $request['id']])->exec();
            if( !$re ) Err::throw('编辑操作失败');

        }else{/// 新增

            # 数据是否重复，重复了没必要新增
            $_condi = [
                ['title', $request['title']],
                ['pid', $request['system_manage_docMuluEdit_parent_mulu_id']]
            ];
            $duplicate = $doc_detail_model->select('id')->where($_condi)->find();
            if(!empty($duplicate)) Err::throw('目录项 "'.$request['title'].'" 已经存在！无需重复添加。');

            $insert = [
                'title'         => $request['title'],
                'sort'          => $request['sort'],
                'doc__id'       => $request['doc__id'],
                'pid'           => $request['system_manage_docMuluEdit_parent_mulu_id'],
                'level'         => ($request['system_manage_docMuluEdit_parent_mulu_level']+1),
                'created_time'  => $now
            ];

            # 执行新增
            $re = $doc_detail_model->insert($insert)->exec();
            if( !$re ) Err::throw('新增操作失败');
        }
    }
    
}