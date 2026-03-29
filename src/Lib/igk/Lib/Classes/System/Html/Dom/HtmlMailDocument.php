<?php
// @file: IGKHtmlMailDoc.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Net\MailAttachementContainer;
/**
* Igkhtml mail doc.
* @package IGK\System\Html\Dom
*/
final class IGKHtmlMailDoc extends HtmlNode{
    /**
    * Properties: attachement, app, message, theme.
    * @var mixed
    */
    private $_attachement, $m_app, $m_message, $m_theme;
    /**
    * Accept render.
    * @param null|mixed $options
    */
    public function _acceptRender($options=null){
        return true;
    }
    /**
    * .ctr
    */
    public function __construct(){
        $this->m_app=igk_app();
        if($this->m_app == null)
            igk_die("apps not initialize");
        parent::__construct("div");
        $this["class"]="igk-mail";
        $this->m_theme=new HtmlDocTheme($this->m_app->getDoc(), __CLASS__.":theme");
        $this->_initTheme();
        $this->m_message=$this->div();
        $this->setId("message");
    }
    /**
    * auto generate doc.
    * @param mixed $target
    * @param mixed $source
    * @return
    */
    private function _copyAddBuildDefinition($target, $source){
        $selector=array();
        foreach($source->def->Attributes as $k=>$v){
            $tab=explode(',', $k);
            if(empty($v))
                continue;
            foreach($tab as $s=>$t){
                if(!empty($t)){
                    $selector[trim($t)
                    ]=$v;
                }
            }
        }
        foreach($selector as $k=>$v){
            if(0===strpos($k, ".igk-mail")){
                $target->def[$k.",.a3s > div "]=$v;
            }
            else{
                $target->def[".igk-mail ".$k.",.a3s > div ".$k]=$v;
            }
        }
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _initTheme(){    }
    /**
    * Creates From Document.
    * @param mixed $doc
    */
    public static function CreateFromDocument($doc){
        if($doc == null)
            return null;
        $c=new IGKHtmlMailDoc();
        $c->m_doc=$doc;
        return $c;
    }
    /**
    * Returns Attachement.
    */
    public function getAttachement(){
        return $this->_attachement;
    }
    /**
    * Returns Message.
    */
    public function getMessage(){
        return $this->m_message;
    }
    /**
    * Returns Theme.
    */
    public function getTheme(){
        return $this->m_theme;
    }
    /**
    * Inner html.
    * @param null|mixed & $options
    */
    protected function innerHTML(& $options=null){
        $out="";
        $s=new HtmlStyleNode();        
        $s->Content=$this->m_theme->get_css_def(true);
        if($this->m_doc != null){
            $out .= "<head>".$this->m_doc->head->getInnerHtml()($options);
            $out .= $s->render($options)."</head>";
            $out .= $this->m_doc->body->render($options);
        }
        else{
            $out .= "<head>".$s->render($options)."</head>";
            $out .= parent::innerHTML($options);
        }
        unset($s);
        return $out;
    }
    /**
     * load theme
     * @param mixed $theme 
     * @return void 
     */
    public function loadTheme($theme){
        $this->_copyAddBuildDefinition($this->m_theme, $theme);
        foreach($theme->getMedias() as $k=>$m){
            $r=$this->m_theme->reg_media($k);
            if($r)
                $this->_copyAddBuildDefinition($r, $m);
        }
    }
    /**
    * Renders.
    * @param null|mixed $o
    */
    public function render($o=null){
        return $this->renderDoc();
    }
    /**
    * Renders Doc.
    */
    public function renderDoc(){
        $this->_attachement=new MailAttachementContainer();
        $p= HtmlRenderer::CreateRenderOptions();
        $p->Context="mail";
        $p->Attachement=$this->_attachement;
        $s="<!DOCTYPE ".IGK_DOC_TYPE." >";
        $s .= "<html ";
        $s .= trim($this->getAttributeString($p));
        $s .= ">";
        $s .= $this->getInnerHtml()($p);
        $s .= "</html>";
        return $s;
    }
    /**
    * Sends Mail.
    * @param mixed $to
    * @param mixed $from
    * @param mixed $subject
    */
    public function sendMail($to, $from, $subject){
        $src=$this->render();
        $g=igk_mail_sendmail($to, $from, $subject, $src, null, $this->_attachement ? $this->_attachement->getList(): null, "text/html");
        return $g;
    }
}