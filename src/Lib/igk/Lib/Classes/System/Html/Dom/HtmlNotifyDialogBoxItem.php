<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNotifyDialogBoxItem.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\ValueListener;
/**
* Represent IGKHtmlNotifyDialogBoxItem class
*/
final class HtmlNotifyDialogBoxItem extends HtmlNode {

    /**
    * Property: message.
    * @var mixed
    */
    private $m_Message;

    /**
    * Property: title.
    * @var mixed
    */
    private $m_title;

    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "div";
    /**
    * 
    */

    protected function initialize(){        
        $this["class"]="igk-notify-box";
        $nv=$this->div();
        $nv["class"]="content";
        $nv->div()->setClass("title")->Content=new ValueListener($this, 'Title');
        $nv->div()->setClass("msg")->Content=new ValueListener($this, 'Message');
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
    ///<summary></summary>
    ///<param name="title"></param>
    ///<param name="msg"></param>
    /**
    * 
    * @param mixed $title
    * @param mixed $msg
    */

    public function show($title, $msg){
        $this->m_title=$title;
        $this->m_Message=$msg;
        // $this->setIsVisible(null);
        return $this;
    }
}