<?php
// @file: IGKHtmlMailDoc.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlDocTheme; 

final class IGKHtmlMailDoc extends HtmlNode{
    private $_attachement, $m_app, $m_message, $m_theme;
    
    ///<summary></summary>
    ///<param name="opt" default="null"></param>
    public function __AcceptRender($opt=null){
        return true;
    }
    ///<summary>Construct mail document</summary>
    public function __construct(){
        $this->m_app=igk_app();
        if($this->m_app == null)
            igk_die("apps not initialize");
        parent::__construct("div");
        $this["class"]="igk-mail";
        $this->m_theme=new HtmlDocTheme($this->m_app->getDoc(), __CLASS__.":theme");
        $this->_initTheme();
        $this->m_message=$this->addDiv();
        $this->setId("message");
    }
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="source"></param>
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
    ///<summary></summary>
    private function _initTheme(){    }
    ///<summary></summary>
    ///<param name="doc"></param>
    public static function CreateFromDocument($doc){
        if($doc == null)
            return null;
        $c=new IGKHtmlMailDoc();
        $c->m_doc=$doc;
        return $c;
    }
    ///<summary></summary>
    public function getAttachement(){
        return $this->_attachement;
    }
    ///<summary>get the message node in this mail definition</summary>
    public function getMessage(){
        return $this->m_message;
    }
    ///<summary>get the theme used in this mail definition</summary>
    public function getTheme(){
        return $this->m_theme;
    }
    ///<summary></summary>
    ///<param name="options" default="null" ref="true"></param>
    protected function innerHTML(& $options=null){
        $out="";
        $s=new HtmlStyleNode();        
        $s->Content=$this->m_theme->get_css_def(true);
        if($this->m_doc != null){
            $out .= "<head>".$this->m_doc->head->innerHTML($options);
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
    ///<summary></summary>
    ///<param name="theme"></param>
    public function loadTheme($theme){
        $this->_copyAddBuildDefinition($this->m_theme, $theme);
        foreach($theme->getMedias() as $k=>$m){
            $r=$this->m_theme->reg_media($k);
            if($r)
                $this->_copyAddBuildDefinition($r, $m);
        }
    }
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    public function render($o=null){
        return $this->renderDoc();
    }
    ///<summary></summary>
    public function renderDoc(){
        $this->_attachement=new IGKMailAttachementContainer();
        $p=igk_xml_create_render_option();
        $p->Context="mail";
        $p->Attachement=$this->_attachement;
        $s="<!DOCTYPE ".IGK_DOC_TYPE." >";
        $s .= "<html ";
        $s .= trim($this->getAttributeString($p));
        $s .= ">";
        $s .= $this->innerHTML($p);
        $s .= "</html>";
        return $s;
    }
    ///<summary></summary>
    ///<param name="to"></param>
    ///<param name="from"></param>
    ///<param name="subject"></param>
    public function sendMail($to, $from, $subject){
        $src=$this->render();
        $g=igk_mail_sendmail($to, $from, $subject, $src, null, $this->_attachement ? $this->_attachement->getList(): null, "text/html");
        return $g;
    }
}
