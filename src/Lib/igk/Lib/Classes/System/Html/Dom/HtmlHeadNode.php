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
    private $m_scripts = [];

    public function __construct()
    {
        parent::__construct();
    }

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
    public function getPreload(){
        return HtmlHeadPreloadNode::getItem();
    }
    public function load_scripts(?array  $list, bool $temp = false){
        $this->m_scripts = $list;
        return $this;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    protected function _getRenderingChildren($options=null){
       // + | --------------------------------------------------------------------
       // + | for good header processing item order are important
       // + |
       $t = [];
       $v=parent::_getRenderingChildren($options);
       // igk_wln_e("do", $v);
        $is_document = isset($options->Document);
        // + 1. meta first
        if($is_document){
            if($meta=$options->Document->getMetas()){                
                array_unshift($t, $meta);
            } 
        }
        $t= array_merge($t, array(
            HtmlHeadBaseUriNode::getItem(),
            HtmlFaviconNode::getItem(), 
            HtmlHeadPreloadNode::getItem(),          
        ));
        if (!($doc = igk_getv($options, "Document")) || !($doc->noCoreScript)){
            $t[] = HtmlCoreJSScriptsNode::getItem();
            $t[] = HtmlControllerJSScriptsNode::getItem();                
        }
        if ($this->m_scripts){
            $t[] = HtmlExtraHeaderScriptHost::Create($this->m_scripts);
        }
        if(is_array($v))
            $t=array_merge($t, $v);        
        // to load extra item on 
        $t[] =new HtmlHookNode(IGKEvents::HOOK_HTML_HEAD, "head");
        return $t;
    }
}