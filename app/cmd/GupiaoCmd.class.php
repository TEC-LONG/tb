<?php

namespace cmd;
use \Json;
use \Err;
use \Fun;
use \TB;
use \cmd\service\GupiaoService;

class GupiaoCmd
{
    public function go(){

        $v1 = Fun::logic__src('www.aa.com', 'AA.BB.CC');
        var_dump($v1);

        $o1 = new GupiaoService;
        $o1->f1();
    }
}
