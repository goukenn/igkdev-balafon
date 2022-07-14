<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

require_once( IGK_LIB_CLASSES_DIR. "/System/Html/Dom/HtmlDefaultMainPage.php");
require_once( IGK_LIB_CLASSES_DIR. "/System/Html/Dom/HtmlPoweredByNode.php");

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlBodyNode extends HtmlNode{
    protected $tagname = "body";
    private $m_bodybox;
    private $m_bodyMainScript;
    private $m_inlineTheme;
    /**
     * html node 
     * @var mixed
     */
    private $m_appendContent;
    public function __construct()
    {
        parent::__construct();
        $this->m_bodyMainScript = new HtmlBodyMainScript();
    }

    

    // ///<summary></summary>
    // ///<param name="id"></param>
    // ///<param name="n"></param>
    // public function addScriptNode($id, $n){
    //     return $this->m_bodyMainScript->addScriptNode($id, $n);
    // }
    public function removeScript($scriptFile){
        return $this->m_bodyMainScript->removeScript($scriptFile);
    }
    /**
     * append main body script
     * @param mixed $id 
     * @param mixed $scriptFile 
     * @return mixed 
     */
    public function appendScript($id, $scriptFile){ 
        return $this->m_bodyMainScript->addScript($id, $scriptFile);
    }

    public function getAppendContent(){
        if($this->m_appendContent === null){
            $this->m_appendContent = new HtmlNoTagNode();
        }
        return $this->m_appendContent;
    }

    ///load addition script content when page request loaded.
    /**
    */
    public function addScriptContent($key, $script){       
        return $this->m_bodyMainScript->addScript($key, $script);
    }
     
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
        $doc = igk_getv($options, "Document");
        $c = [];
        if ($this->getBodyBox()->getHasChilds()){
            $c[] = $this->m_bodybox;
        }        
        $c = array_merge($c,  parent::__getRenderingChildren($options));

        if (HtmlDefaultMainPage::getInstance()->getIsVisible()){
            $c[] = HtmlDefaultMainPage::getInstance();
        }

        $c[] = $this->m_bodyMainScript;   
        $c[] = $this->getAppendContent(); 
        if ($doc){
            $c[] = new HtmlDocumentCssHostNode($doc);
        }
        $c[] = HtmlPoweredByNode::getItem(); 
        $c[] = new HtmlHookNode(IGKEvents::HOOK_HTML_BODY, "body");
        return $c;
    }
}