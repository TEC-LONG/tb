<?php
namespace model;
use \BaseModel;
use \Fun;

class DocDetailModel extends BaseModel{

    protected $table = 'tl_doc_detail';

    /**
     * ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ
     * ⒶⒷⒸⒹⒺⒻⒼⒽⒾⒿⓀⓁⓂⓃⓄⓅⓆⓇⓈⓉⓊⓋⓌⓍⓎⓏ
     * ❶❷❸❹❺❻❼❽❾❿⓫⓬⓭⓮⓯⓰⓱⓲⓳⓴
     * ㊀㊁㊂㊃㊄㊅㊆㊇㊈㊉
     * ⑴⑵⑶⑷⑸⑹⑺⑻⑼⑽⑾⑿⒀⒁⒂⒃⒄⒅⒆⒇
     */
    const LEVEL = [
        1 => ['一、', '二、', '三、', '四、', '五、', '六、', '七、', '八、', '九、', '十、'],
        2 => ['1. ', '2. ', '3. ', '4. ', '5. ', '6. ', '7. ', '8. ', '9. ', '10. '],
        3 => ['a. ', 'b. ', 'c. ', 'd. ', 'e. ', 'f. ', 'g. ', 'h. ', 'i. ', 'j. '],
        4 => ['① ', '② ', '③ ', '④ ', '⑤ ', '⑥ ', '⑦ ', '⑧ ', '⑨ ', '⑩ ', '⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳㉑㉒㉓㉔㉕㉖㉗㉘㉙㉚㉛㉜㉝㉞㉟㊱㊲㊳㊴㊵㊶㊷㊸㊹㊺㊻㊼㊽㊾㊿'],
        5 => ['㊀ ', '㊁ ', '㊂ ', '㊃ ', '㊄ ', '㊅ ', '㊆ ', '㊇ ', '㊈ ', '㊉ '],
    ];

    /**
     * 根据level与索引获取目录标号
     */
    public static function getSerialNumb($level, $index){
    
        return self::LEVEL[$level][$index];
    }

    /**
     * 根据doc__id获取数据
     */
    public function getDocDetailByDocid($request, $doc__id){
    
        /// 构建查询条件
        $_condi = Fun::tb__condition($request, [], [
            ['doc__id', $doc__id]
        ]);

        /// 查询数据
        return $this->where($_condi)->select([
            'id',
            'title',
            'doc__id',
            'level',
            'pid',
            'sort',
            'created_time',
            'update_time',
            '@(case WHEN LENGTH(content_html)>0 THEN 1 ELSE 0 END)' => 'c_len',
        ])->orderby('level desc')->get();
    }
}