<?php

namespace IGK\System\Net;

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlRenderer;

class MailDocument extends HtmlNode{
    protected $tagname = "div";


    public function render($option=null){
        if ($option==null){
            $option = (object)["Context"=>HtmlContext::Mail];
        }
        return HtmlRenderer::Render($this, $option);
    }
}