<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocumentNode.php
// @date: 20220803 13:48:56
// @desc: 
// @file: HtmlDocumentNode.php
namespace IGK\System\Html\Dom;
use Exception;
use IGK\Resources\R;
use IGK\System\Html\HtmlRenderer;
use IGKEvents;

/** @package IGK\System\Html\Dom */
/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlDocumentNode extends HtmlItemBase{
    /**
    * Property: head.
    * @var mixed
    */
    protected $m_head;
    /**
    * Property: body.
    * @var mixed
    */
    protected $m_body;
    /**
    * Identifier: id.
    * @var mixed
    */
    protected $m_id;
    /**
    * Property: lang.
    * @var mixed
    */
    protected $m_lang = 'fr';
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = 'igk-document';
    /**
    * Constant: document injector key.
    * @var mixed
    */
    const DocumentInjectorKey = 'DocumentInjector';
    /**
     * define doc type 
     * @var ?string
     */
    var $docType;
    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo(){
        return [];
    }
    /**
     * define name spaces
     */
    protected $namespaces;
    /**
    * Returns Id.
    */
    public function getId(){
        return $this->m_id;
    }
    /**
    * auto generate doc.
    * @return HtmlBodyNode
    */
    public function getBody(): ?HtmlBodyNode{ return $this->m_body; }
    /**
    * auto generate doc.
    * @return HtmlHeadNode
    */
    public function getHead(): ?HtmlHeadNode{ return $this->m_head; }
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
    /**
    * Returns Metas.
    */
    public function getMetas(){
        return null;
    }
    /**
    * Returns Base Uri.
    */
    public function getBaseUri(){
        return null;
    }
    /**
    * Returns Favicon.
    */
    public function getFavicon(){
        return null;
    }
    /**
    * use igkdoc to handle theme
    * @param ?HtmlItemBase $head
    * @param ?HtmlItemBase $body
    */
    public function __construct(?HtmlItemBase $head = null, ?HtmlItemBase $body = null){
        $this->m_head = $head ?? $this->add(new HtmlHeadNode());
        $this->m_body = $body ?? $this->add(new HtmlBodyNode());
    }
    /**
    * auto generate doc.
    * @return HtmlDefaultMainPage
    */
    public function getDefaultMainPage(): HtmlDefaultMainPage{
        return HtmlDefaultMainPage::getInstance();
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options=null){
        HtmlRenderer::DefOptions($options); 
        $options->Document = $this; 
        $this->setParam(self::DocumentInjectorKey, new HtmlDocumentBodyContentInjector($this)); 
        $basic_doctype = $this->docType  ?? 'html';
        $s = "<!DOCTYPE {$basic_doctype }>\n";             
        $attr = "";
        $ln = $options->LF;
        $lang = $this->m_lang;
        if (!empty($lang)){
            $attr = " lang=\"".$lang."\"";
        }
        if ($this->namespaces){
            foreach($this->namespaces as $k=>$v)
                $attr.= " ".$k."=".HtmlRenderer::GetStringAttribute($v, $options);
        }
        if (igk_environment()->isDev()){
            if ($id = $this->getId())
                $attr .= " document_id=\"".$id."\"";
        }
        if (!empty($extra = $this->headerExtraAttribute())){
            $attr .= " ".$extra;
        }
        $s .= "<html{$attr}>"; 
        $sdepth = $options->Depth;
        $options->Depth++;
        // + | --------------------------------------------------------------------
        // + | hook global event before render document 
        // + |
        $tr = $this->getDefaultMainPage();
        igk_hook(IGKEvents::HOOK_HTML_BEFORE_RENDER_DOC, ["doc"=>$this]);
        if ($tr && $tr->getIsVisible()){
            $this->title = $tr->getPageTitle();
        }
        if (!empty($head = HtmlRenderer::Render($this->m_head, $options))){
            $s.= $head.$ln;
        }
        $options->Depth = $sdepth+1;
        if (!empty($body = HtmlRenderer::Render($this->m_body, $options))){
            $s = rtrim($s) . $body.$ln;
        };  
        $content = "";
        /**
         * hook global event afert render body node 
         */
        igk_hook(IGKEvents::HOOK_HTML_AFTER_RENDER_BODY, ["doc"=>$this, "content"=>& $content]); 
        if (is_array($cc = $this->getDocumentInjector()->getItems())){
            foreach($cc as $id=>$fc){
                $fc($content, $this);
            }
        }
        $options->Depth = $sdepth;
        if (!empty($content)){
            $s.= $content.$ln;
        }
        $s .= "</html>";
        $this->setParam(self::DocumentInjectorKey, null);
        return $s;
    }
    /**
     * retrieve document injector
     * @return mixed 
     * @throws Exception 
     */
    public function getDocumentInjector(){
        return $this->getParam(self::DocumentInjectorKey);
    }
    /**
     * get extra attribute
     * @return null 
     */
    protected function headerExtraAttribute(){
        return null;
    }
}