<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocCoreStyle.php
// @date: 20220823 14:11:34
// @desc: core style uri
namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlCssLinkNode;

/**
 * parent css link
 * @package IGK\System\Html\Dom
 */
class HtmlDocCoreStyle extends HtmlCssLinkNode{
    public function __construct($link, $sys, $defer)
    {     
        parent::__construct($link, $sys, $defer);
    }
    protected function __AcceptRender($o = null)
    { 
        $doc = null;
        $o && ($doc = igk_getv($o, "Document"));
        if ($doc && $doc->noCoreCss){
            return false;
        }  
        return parent::__AcceptRender($o);
    }
    protected function initialize(){
        parent::initialize(); 
    }
}