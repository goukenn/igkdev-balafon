<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocCoreStyle.php
// @date: 20220823 14:11:34
// @desc: core style uri
namespace IGK\System\Html\Dom;

use IGK\Helper\ViewHelper;
use IGK\System\Html\Dom\HtmlCssLinkNode;

/**
 * Document core style manager
 * @package IGK\System\Html\Dom
 */
class HtmlDocCoreStyle extends HtmlCssLinkNode{
    public function __construct($link, $sys, $defer)
    {     
        parent::__construct($link, $sys, $defer);
    }
    protected function _acceptRender($options = null):bool
    { 
        $doc = null;
        $options && ($doc = igk_getv($options, "Document"));
        if ($doc && $doc->noCoreCss){
            return false;
        }  
        return parent::_acceptRender($options);
    } 
}