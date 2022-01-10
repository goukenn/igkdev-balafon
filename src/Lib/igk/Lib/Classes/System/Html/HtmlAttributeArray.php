<?php

namespace IGK\System\Html;

use ArrayAccess;
use IGK\System\Collections\ArrayList;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

class HtmlAttributeArray extends ArrayList implements ArrayAccess{
    use ArrayAccessSelfTrait; 
    // protected $preserverKeys = true;
    private $m_protectedList;

    public function activate($n){
        $t = array_filter(explode(" ", $n));
        while( $s = array_shift($t)){
            $this->m_data[$s] = HtmlActiveAttrib::getInstance();
        }
        return $this;
    }
    public function deactivate($n){
        unset($this->m_data[$n]);
    }
    function __debugInfo()
    {
        return ["attribCount"=>$this->count()];
    } 
    /**
     * create a array attributes
     * @param mixed $protectedlist 
     * @return void 
     */
    public function __construct(?array $protectedlist=null)
    {
        $this->m_protectedList = $protectedlist;
        if ($protectedlist){
            $this->m_data += $protectedlist;
        }
    }
    protected function _access_OffsetGet($n)
    {
        if ($this->m_protectedList && isset($this->m_protectedList[$n])){
            return $this->m_protectedList[$n];
        }
        return parent::_access_OffsetGet($n);
    }
    protected function _access_OffsetSet($n, $v)
    {
        if ($this->m_protectedList && isset($this->m_protectedList[$n])){
            $this->m_protectedList[$n]->setValue($v);
            return $this;
        }
        else  
            return parent::_access_OffsetSet($n, $v);
        
    }
    protected function _access_OffsetUnset($n)
    {
        if ($this->m_protectedList && isset($this->m_protectedList[$n])){           
            return $this;
        }
         parent::_access_OffsetUnset($n);
         return $this;
    }
}