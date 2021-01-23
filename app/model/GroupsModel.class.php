<?php
namespace model;
use \BaseModel;
use \Fun;

class GroupsModel extends BaseModel{

    protected $table = 'tl_groups';

    const C_TYPE = ['无', '股票', '基金'];

    /**
     * 根据类型获得所有分组
     */
    public function getGroups($type=0){
    
        return $this->select('id, name')->where(['type', $type])->get();
    }
}