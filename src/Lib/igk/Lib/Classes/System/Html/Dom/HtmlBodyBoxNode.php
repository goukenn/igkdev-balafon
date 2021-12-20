<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlBodyBoxNode extends HtmlNode{
    protected $tagname = "div";
  
    public function __construct()
    {
        parent::__construct();
        $this["class"] = "igk-bodybox fit igk-parentscroll igk-powered-viewer overflow-y-a";

        
    }
} 