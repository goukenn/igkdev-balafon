<?php

namespace IGK\System\Html;

use ArrayAccess;
use IGK\System\Collections\ArrayList;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

class HtmlChildArray extends ArrayList implements ArrayAccess{
    use ArrayAccessSelfTrait; 

    public function activate($n){
        $this->m_data[$n] = HtmlActiveAttrib::getInstance();
    }
    public function deactivate($n){
        unset($this->m_data[$n]);
    }
    function __debugInfo()
    {
        return ["childCount"=>$this->count()];
    } 
    public function remove($item){
        if (false !== ($index = array_search($item, $this->m_data))){
            unset($this->m_data[$index]);
        }
    }
    public function clear(){
        $this->m_data = [];
    }

}