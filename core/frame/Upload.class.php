<?php

use \Upload\Storage\FileSystem;
use \Upload\File;
use \Upload\Validation\Mimetype;
use \Upload\Validation\Size;

class Upload{

    private static $FILE;

    public static function __callStatic($name, $arguments){
        
        if( empty(self::$FILE) ){
            self::$FILE = new static;
        }
        
        return self::magicCommon($name, $arguments);
    }

    public function __call($name, $arguments){
    
        if( empty(self::$FILE) ){
            self::$FILE = $this;
        }

        return self::magicCommon($name, $arguments);
    }

    /**
     * __callStatic与__call 调取的公共方法
     */
    private static function magicCommon($name, $arguments){

        return Err::try(function () use ($name, $arguments){

            $allow_method = [
                'file',
                'exec',
            ];
        
            if( in_array($name, $allow_method) ){
            
                $method = 'go' . $name;
                return self::commonUse($method, $arguments);
            }

            Err::throw('非法的操作: '.__CLASS__.'::'.$name);

        }, 'exit');
    }

    private static function commonUse($method, $arguments=null){
    
        if( is_array($arguments)&&isset($arguments[0]) ){

            if( isset($arguments[1]) ){

                return self::$FILE->$method($arguments[0], $arguments[1]);
            }
            
            return self::$FILE->$method($arguments[0]);
            
        }
        return self::$FILE->$method();
    }

    private $_f;
    private $_file_new_name = '';
    private $_file_vars     = [];

    /**
     * 方法作用：初始化文件上传插件类对象
     * 参数：
     * @param    string    $input_name    某文件$_FILES的下标名（对应的是表单<input type="file" name="xxx" />的name值
     * @param    string    $save_path    文件的保存路径，可选参，不传则为常量UPLOAD_PATH所指定的路径（/upload/)
     * @return    object
     */
    protected function gofile($input_name, $save_path=''){

        $this->_file_new_name = '';//每次进来，先将文件新名字初始化一下

        $this->_file_vars = [
            'mime' => ['image/jpeg', 'image/png', 'image/gif'],//如果是单个可以是字符串，如：'image/png'
            'size' => '5M'//单位可以是："B", "K", M", or "G"
        ];

        if($save_path==='') $save_path=PUB_UPLOAD.'/';//保存路径

        $source     = new FileSystem($save_path);
        $this->_f   = new File($input_name, $source);

        return $this;
    }

    /**
     * 方法作用：执行文件上传操作
     * 参数：
     * @param    string    $new_file_name_prefix    上传的文件新名字前缀，该参数与第二个参数互斥，若同时都指定了值，则优先用第二个参数；两个只需指定一个即可
     * @param    string    $new_file_name    新文件的全名，不包含后缀，该参数与第一个参数互斥
     * @return    false|文件上传插件类对象（假设为$obj，则$obj可以调用以下方法完成相应功能：
                        $obj->getNameWithExtension()    获得包含后缀的文件名
                        $obj->getExtension()            获得文件后缀
                        $obj->getMimetype()             获得文件的mime类型
                        $obj->getSize()                 获得文件大小
                        $obj->getMd5()
                        $obj->getDimensions()           获得文件尺寸
                        $obj->getErrors()               获得上传出错信息
     */
    protected function goexec($new_file_name_prefix='', $new_file_name=''){


        $this->_file_new_name = $new_file_name === ''
                                ? ( $this->_file_new_name==='' ? (uniqid($new_file_name_prefix).Fun::str__rand(8).'_'.date('YmdHis')) : $this->_file_new_name )
                                : $new_file_name;

        $this->_f->setName($this->_file_new_name);
    
        $this->_f->addValidations([
            new Mimetype($this->_file_vars['mime']),
            new Size($this->_file_vars['size'])
        ]);

        try {
            // Success!
            $re = $this->_f->upload();
        } catch (\Exception $e) {
            // Fail!
            // $errors = $this->_f->getErrors();//换成记录日志
            return false;
        }
        return $this->_f;
    }

    /* public function path2src($path){
    
        $src = preg_replace('/^.*\/upload\//u', 'upload/', $path);
        return $src;
    } */

}