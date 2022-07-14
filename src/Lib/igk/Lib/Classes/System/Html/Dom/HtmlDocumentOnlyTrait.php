<?php

namespace IGK\System\Html\Dom;

/**
 * define traint for document only node
 */
trait HtmlDocumentOnlyTrait
{
    public function getCanAddChilds()
    {
        return false;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    protected function __AcceptRender($opt = null)
    {
        return $this->getIsVisible() && igk_getv($opt, "Document");        
    }
}