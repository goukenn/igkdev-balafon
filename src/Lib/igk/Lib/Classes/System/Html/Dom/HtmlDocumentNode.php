<?php
// @file: HtmlDocumentNode.php

namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlRenderer;
use IGKEvents;

///<summary>reprensent a html document</summary>
class HtmlDocumentNode extends HtmlItemBase{
    protected $m_head;
    protected $m_body;
    protected $m_lang = "fr";
    protected $m_id;

    /**
     * define name spaces
     */
    protected $namespaces;
    public function getId(){
        return $this->m_id;
    }

    /**
     * 
     * @return HtmlBodyNode 
     */
    public function getBody(){ return $this->m_body; }
    /**
     * @return HtmlHeadNode 
     */
    public function getHead(){ return $this->m_head; }

    /**
     * set document title
     * @param string $value 
     * @return $this 
     */
    public function setTitle(?string $value=null){
        $this->m_head->title = $value;
        return $this;
    }
    /**
     * get document title
     * @return mixed 
     */
    public function getTitle(){
        return $this->m_head->title;
    }
    public function getMetas(){
        return null;
    }
    public function getBaseUri(){
        return null;
    }
    public function getFavicon(){
        return null;
    }
    public function __construct(){
        $this->m_head = $this->add(new HtmlHeadNode());
        $this->m_body = $this->add(new HtmlBodyNode());
    }
    public function render($options=null){
        HtmlRenderer::DefOptions($options); 
        $options->Document = $this; 
        $s = "<!DOCTYPE html>\n";             
        $attr = "";
        $ln = $options->LF;
        if (!empty($this->m_lang)){
            $attr = " lang=\"".$this->m_lang."\"";
        }
        if ($this->namespaces){
            foreach($this->namespaces as $k=>$v)
                $attr.= " ".$k."=".HtmlRenderer::GetStringAttribute($v, $options);
        }
        if (igk_environment()->is("DEV")){
            if ($id = $this->getId())
                $attr .= " document_id=\"".$id."\"";
        }
        if (!empty($extra = $this->headerExtraAttribute())){
            $attr .= " ".$extra;
        }
        $s .= "<html{$attr}>"; 
        $sdepth = $options->Depth;
        $options->Depth++;
        igk_hook(IGKEvents::HOOK_HTML_BEFORE_RENDER_DOC, ["doc"=>$this]);
        if (!empty($head = HtmlRenderer::Render($this->m_head, $options))){
            $s.= $head.$ln;
        }
        $options->Depth = $sdepth+1;
        if (!empty($body = HtmlRenderer::Render($this->m_body, $options))){
            $s = rtrim($s) . $body.$ln;
        };   
        $options->Depth = $sdepth;
        $s .= "</html>";
        return $s;
    }
    /**
     * get extra attribute
     * @return null 
     */
    protected function headerExtraAttribute(){
        return null;
    }
}