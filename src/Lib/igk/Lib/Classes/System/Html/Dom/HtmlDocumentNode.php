<?php


namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlRenderer;

///<summary>reprensent a html document</summary>
class HtmlDocumentNode extends HtmlItemBase{
    protected $m_head;
    protected $m_body;
    protected $m_lang = "fr";
    protected $m_id;

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
    public function setTitle(string $value){
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
        if ($options==null){
            $options = HtmlRenderer::CreateRenderOptions();
        }
        $options->Document = $this;
        $s = "<!DOCTYPE html >\n";
        $attr = "";
        if (!empty($this->m_lang)){
            $attr = " lang=\"".$this->m_lang."\"";
        }
        if (igk_environment()->is("DEV")){
            $attr .= " document_id=\"".$this->getId()."\"";
        }
        $s .= "<html{$attr}>"; 
        $s .= HtmlRenderer::Render($this->m_head, $options);
        $s .= HtmlRenderer::Render($this->m_body, $options);
        $s .= "</html>";
        return $s;
    }
}