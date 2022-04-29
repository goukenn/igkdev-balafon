<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlHeadNode extends HtmlNode{
    protected $tagname = "head";
    private $m_title; 

    public function setTitle($value){
        if ($this->m_title == null){
            $this->m_title = new HtmlNode("title");
            $this->add($this->m_title);
        }
        $this->m_title->content = $value;
        return $this;
    }
    public function getTitle(){
        if ($this->m_title){
            return $this->m_title->content;
        }
        return null;
    }

    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    protected function __getRenderingChildren($options=null){
       
        $v=parent::__getRenderingChildren($options);
        $t=array(
            HtmlHeadBaseUriNode::getItem(),
            HtmlFaviconNode::getItem(), 
        );
        $is_document = isset($options->Document);
        if($is_document){
            if($meta=$options->Document->getMetas()){
                $t[]=$meta;
            }
        }
        if ($is_document){
            $t[] = HtmlCoreJSScriptsNode::getItem();
        }

        if(is_array($v))
        $t=array_merge($t, $v);        
        // to load extra item on 
        $t[] =new HtmlHookNode(IGKEvents::HOOK_HTML_HEAD, $options);
        return $t;
    }
}