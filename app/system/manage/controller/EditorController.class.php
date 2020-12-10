<?php

namespace system\manage\controller;
use \controller;
use \Validator;
use \Fun;
use \Json;
use \Err;
use \Upload;
use \model\ImagesModel;

class EditorController extends Controller {

    /**
     * 适配 editorMD 图片上传 的功能
     */
    public function mdImgUp(){
    
        /// 跨域传输
        //header( 'Access-Control-Allow-Origin:*' ); 

        // $fileIMG = isset($_FILES['editormd-image-file']) ? $_FILES['editormd-image-file'] : 'none';
        //$token = isset($_GET['tk']) ? $_GET['tk'] : '';
        // $re = [];

        /// 没有令牌则需要记录到日志中（防止调取直接上传）
        //if ( !$token ) return;

        /// 以年份和月份分别来创建保存editor图片的一级和二级目录
        $first_folder       = date('Y');
        $first_folder_path  = PUB_UPLOAD . '/editormd/' . $first_folder;

        if ( !is_dir($first_folder_path) ) {

            @mkdir($first_folder_path);
            chmod($first_folder_path, 0757);
        }

        $second_folder      = date('m');
        $second_folder_path = $first_folder_path . '/' . $second_folder;

        if ( !is_dir($second_folder_path) ) {

            @mkdir($second_folder_path, 0757);
            chmod($second_folder_path, 0757);
        }

        /// 图片上传
        # 构建新图片的数据id（用于嵌入图片文件命名中，提高辨识度）
        $arr_maxid = ImagesModel::select(['max(id)' => 'maxid'])->find();
        if ( !$arr_maxid ) $arr_maxid=['maxid' => 0];

        # 图片的命名规则：前缀_随机字符串年月日时分秒.images表ID.jpg
        $img_name = uniqid('editorMD_') . date('YmdHis') . '.' . ($arr_maxid['maxid'] + 1);

        if ( $file = Upload::file('editormd-image-file', $second_folder_path)->exec('', $img_name) ) {

            //$img = 'http://xx.xxxx.com/upload/editormd/2019/11/xx.jpg';
            $img = 'upload/editormd/' . $first_folder . '/' . $second_folder . '/' . $file->getNameWithExtension();

            # 存储入editormd_img表
            ImagesModel::insert([
                'id'            => ($arr_maxid['maxid'] + 1),
                'img'           => $img,
                'created_time'  => time()
            ])->exec();

            echo Json::vars([
                'url'       => '/'.$img,
                'success'   => 1,
                'message'   => '上传成功！'
            ])->exec('return');
        } else {

            echo Json::vars([
                'success'   => 0,
                'message'   => '上传失败！'
            ])->exec('return');
        }
        exit;
    }



    

}