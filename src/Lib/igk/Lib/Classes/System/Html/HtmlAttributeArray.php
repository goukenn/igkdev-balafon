<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAttributeArray.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

use ArrayAccess;
use IGK\System\Collections\ArrayList;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException;
use ReflectionException;

require_once IGK_LIB_CLASSES_DIR . "/System/Html/HtmlActiveAttrib.php";

class HtmlAttributeArray extends ArrayList implements ArrayAccess
{
    use ArrayAccessSelfTrait;
    // protected $preserverKeys = true;
    private $m_protectedList;
    var $add_listener;
    /**
     * activate attribute
     * @param mixed $n 
     * @return $this 
     * @throws IGKException 
     */
    public function activate($n)
    {
        $t = array_filter(explode(" ", $n));
        while ($s = array_shift($t)) {
            $this->m_data[$s] = HtmlActiveAttrib::getInstance();
        }
        return $this;
    }
    public function keyExists($n)
    {
        return key_exists($n, $this->m_data);
    }
    /**
     * deactivate attribute
     * @param mixed $n 
     * @return void 
     */
    public function deactivate($n)
    {
        unset($this->m_data[$n]);
    }
    /**
     * get if attribute is active
     * @param mixed $n 
     * @return bool 
     */
    public function isactive($n)
    {
        return isset($this->m_data[$n]) && ($this->m_data[$n] instanceof HtmlActiveAttrib);
    }
    function __debugInfo()
    {
        return ["attribCount" => $this->count()];
    }
    /**
     * create a array attributes
     * @param mixed $protectedlist attribute to protected with data
     * @return void 
     */
    public function __construct(?array $protectedlist = null)
    {
        $this->m_protectedList = $protectedlist;
        if ($protectedlist) {
            $this->m_data += $protectedlist;
        }
    }
    protected function _access_OffsetGet($n)
    {
      
        if ($this->m_protectedList && isset($this->m_protectedList[$n])) {
            return $this->m_protectedList[$n];
        }
        return parent::_access_OffsetGet($n);
    }
    /**
     * 
     * @param mixed $n 
     * @param mixed $v 
     * @return $this|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _access_OffsetSet($n, $v)
    {      
        if (!is_null($this->add_listener)) {
            $fc = $this->add_listener;
            $fc($n) || igk_die("can't update attribute : " . $n);
        }
        if ($this->m_protectedList && isset($this->m_protectedList[$n])) {
            $this->m_protectedList[$n]->setValue($v);
            return $this;
        }
        return parent::_access_OffsetSet($n, $v);
    }
    protected function _access_OffsetUnset($n)
    {
        if ($this->m_protectedList && isset($this->m_protectedList[$n])) {
            return $this;
        }
        parent::_access_OffsetUnset($n);
        return $this;
    }
}
