<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlBodyNode extends HtmlNode{
    protected $tagname = "body";
    private $m_bodybox;
    public function getBodyBox(){
        if ($this->m_bodybox ===null){
            $this->m_bodybox = new HtmlBodyBoxNode();
        }
        return $this->m_bodybox;
    }
    public function addBodyBox(){
        return $this->getBodyBox();
    }

    protected function __getRenderingChildren($options = null)
    {
        $c = [];
        if ($this->getBodyBox()->getHasChilds()){
            $c[] = $this->m_bodybox;
        }        
        $c = array_merge($c,  parent::__getRenderingChildren($options));
        $c[] = HtmlBodyMainScript::getItem();
        $c[] = new HtmlHookNode(IGKEvents::HOOK_HTML_BODY, [
            "options"=>$options,
            "body"=>$this
        ]);
        return $c;
    }
}