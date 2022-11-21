<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClassAndStyleOffsetTrait.php
// @date: 20221107 19:19:56
namespace IGK\System\Html\Dom\Traits;

use IGK\System\Html\Dom\HtmlCssClassValueAttribute;
use IGK\System\Html\Dom\HtmlOptions;
use IGK\System\Html\HtmlStyleValueAttribute;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom\Traits
*/
trait ClassAndStyleOffsetTrait{

    
    /**
     * set property
     * @param mixed $k 
     * @param mixed $v 
     * @return void|$this 
     * @throws IGKException 
     */
    protected function _access_OffsetSet($k, $v)
    { 

        if ($v === null) {
            unset($this->m_attributes[$k]);
        } else {
            switch ($k) {
                case "class":
                    if ($v === null) {
                        unset($this->m_attributes[$k]);
                    } else {
                        if (!($cl = igk_getv($this->m_attributes, $k))) {
                            $cl = new HtmlCssClassValueAttribute();
                            $this->m_attributes[$k] = $cl;
                        }
                        $cl->add($v);
                    }
                    break;
                case "style":
                    if (!($cl = igk_getv($this->m_attributes, $k))) {
                        $cl = new HtmlStyleValueAttribute($this);
                    }
                    $cl->setValue($v);
                    $this->m_attributes[$k] = $cl;
                    break;
                default:
                    if (strpos($k, 'igk:') === 0) {
                        $ck = substr($k, 4);

                        if (!HtmlOptions::IsAllowedAttribute($ck)) {
                            return;
                        }
                        if (!$this->setSysAttribute($ck, $v, $this->getLoadingContext())) {
                            $this->offsetSetExpression($k, $v);
                        }
                    } else {
                        $this->m_attributes[$k] = $v;
                    }
                    break;
            }
        }
        return $this;
    }
    protected function setSysAttribute($k, $v, $context): bool{
        return false;
    }
    protected function offsetSetExpression($k, $v){
    }

    public function getLoadingContext(){
    }
}