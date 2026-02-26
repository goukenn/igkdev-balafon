<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocumentOnlyTrait.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
 * define traint for document only node
 */
trait HtmlDocumentOnlyTrait
{

    /**
    * auto generate doc.
    */
    public function getCanAddChilds()
    {
        return false;
    }

    /**
    * auto generate doc.
    */
    public function getCanRenderTag()
    {
        return false;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool
    {
        return $this->getIsVisible() && igk_getv($options, "Document");        
    }
}