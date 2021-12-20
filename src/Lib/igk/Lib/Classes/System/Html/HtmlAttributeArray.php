<?php

namespace IGK\System\Html;

use ArrayAccess;
use IGK\System\Collections\ArrayList;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

class HtmlAttributeArray extends ArrayList implements ArrayAccess{
    use ArrayAccessSelfTrait; 

    public function activate($n){
        $this->m_data[$n] = HtmlActiveAttrib::getInstance();
    }
    public function deactivate($n){
        unset($this->m_data[$n]);
    }
    function __debugInfo()
    {
        return ["attribCount"=>$this->count()];
    } 
}