<?php

class baseCmd{

    protected $_datas=[];

    public function __construct($params=[]){
    
        if( 
            !empty($params) &&
            !empty($this->signal)
         ){
        
            foreach( $this->signal as $index=>$name){
            
                if( isset($params[$index]) ){
                
                    $this->_datas[$name] = $params[$index];
                }
            }
        }
    }

    protected function request($name, $default=null){
    
        return isset($this->_datas[$name]) ? $this->_datas[$name] : ($default===null ? null : $default);
    }

}

