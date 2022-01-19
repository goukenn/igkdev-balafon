<?php

namespace IGK\System\Html\Dom;

use IGKValueListener;

///<summary>Represente class: IGKHtmlNotifyDialogBoxItem</summary>
/**
* Represente IGKHtmlNotifyDialogBoxItem class
*/
final class HtmlNotifyDialogBoxItem extends HtmlNode {
    private $m_Message;
    private $m_title;
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("div");
        $this["class"]="igk-notify-box";
        $nv=$this->div();
        $nv["class"]="content";
        $nv->div()->setClass("title")->Content=new IGKValueListener($this, 'Title');
        $nv->div()->setClass("msg")->Content=new IGKValueListener($this, 'Message');
        $nv->script()->Content=<<<EOF
if(ns_igk)ns_igk.winui.notify.init();
EOF;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getMessage(){
        return $this->m_Message;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getTitle(){
        return $this->m_title;
    }
    public function render($options =null){
        igk_wln_e("render .... ");
        $o = parent::render($options); 
        $this->setIsVisible(false);
        return $o;
    }
    ///<summary></summary>
    ///<param name="title"></param>
    ///<param name="msg"></param>
    /**
    * 
    * @param mixed $title
    * @param mixed $msg
    */
    public function show($title, $msg){$this->m_title=$title;
        $this->m_Message=$msg;
        $this->setIsVisible(null);
        return $this;
    }
}