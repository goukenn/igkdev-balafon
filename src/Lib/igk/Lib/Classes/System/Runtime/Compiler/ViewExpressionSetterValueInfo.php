<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionValueInfo.php
// @date: 20221015 12:29:53
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* for setter expression value
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionSetterValueInfo{
    private $m_inUse = false;
    private $m_value;
    public function __construct($v, $update){
        $this->m_value = $v;
        $this->m_updateCallback = $update;
    }
    /**
     * get value and count
     * @return mixed 
     */
    public function getValue(){
        if (!$this->m_inUse){
            $this->m_inUse = 1;
        }else {
            $this->m_inUse++;
        }
        if ($fc = $this->m_updateCallback){
            $fc();
        }
        return $this->m_value;
    }
    public function getIsInUse(){
        return !$this->m_inUse;
    }
}