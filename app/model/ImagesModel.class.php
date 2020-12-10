<?php
namespace model;
use \BaseModel;

class ImagesModel extends BaseModel{

    protected $table = 'tl_images';

    const C_POSITION    = ['内网链接',  '外网链接'];
    const C_HAS_USE     = ['未被使用',  '已被使用'];
    const C_IS_DEL      = ['未删除',    '已删除'];
    const C_TYPE        = ['文档图片',  '相片'];

}