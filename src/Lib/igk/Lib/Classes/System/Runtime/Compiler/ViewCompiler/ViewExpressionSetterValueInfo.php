<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionValueInfo.php
// @date: 20221015 12:29:53
namespace IGK\System\Runtime\Compiler\ViewCompiler;
/**
* for setter expression value
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionSetterValueInfo{
    /**
    * Property: in use.
    * @var mixed
    */
    private $m_inUse = false;
    /**
    * Property: value.
    * @var mixed
    */
    private $m_value;
    /**
    * Identifier: id.
    * @var mixed
    */
    private $m_id;
    /**
    * Callback handler for update callback.
    * @var mixed
    */
    private $m_updateCallback;
    /**
    * .ctr
    * @param mixed $id
    * @param mixed $v
    * @param mixed $update
    */
    public function __construct($id, $v, $update){
        $this->m_value = $v;
        $this->m_id = $id;
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
            $fc($this->m_id);
        }
        return $this->m_value;
    }
    /**
    * Returns Is In Use.
    */
    public function getIsInUse(){
        return !$this->m_inUse;
    }
}