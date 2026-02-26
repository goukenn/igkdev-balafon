<?php
// @file: IGKMsDialogFrame.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use IGK\Resources\R;
use IGKEvents;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class HtmlDialogFrameNode extends HtmlNode{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_Box, $m_BoxContent, $m_Height, $m_Title, $m_Width, $m_callbackMethod, $m_closeBtn, $m_closeCallBackEvent, $m_closeMethodUri, $m_closeUri, $m_form, $m_framectrl, $m_id, $m_owner, $m_reloadcallbackMethod, $m_script;

    /**
    * auto generate doc.
    * @param null|mixed $options
    * @return bool
    */

    protected function _acceptRender($options = null):bool{
        if(!$this->m_framectrl || !$this->m_framectrl->ContainFrame($this->m_id, $this)){
            igk_html_rm($this);
            return false;
        }
        $def=IGK_STR_EMPTY;
        if($this->m_Width && $this->m_Height)
            $def=$def."width:".$this->m_Width."px; height:".$this->m_Height."px";
        $this->m_closeBtn->Uri=R::GetImgUri("btn_close");
        $this->m_closeBtn["datasrc"]=R::GetImgUri("btn_close");
        $this->m_Box["style"]=$def;
        return true;
    }

    /**
    * .ctr
    * @param mixed $framectrl
    * @param null|mixed $id
    * @param null|mixed $owner
    * @param null|mixed $reloadcallback
    */
    public function __construct($framectrl, $id=null, $owner=null, $reloadcallback=null){
        parent::__construct("div");
        if(!igk_reflection_class_implement($framectrl, "IFrameController")){
            igk_die("required IFrameController");
        }
        $this->m_framectrl=$framectrl;
        $this->m_closeCallBackEvent=new IGKEvents($this, "closeCallBackEvent");
        $this["class"]="framebox fitw fith posab loc_t loc_l";
        $this["id"]="framebox-".$id;
        $this["igk-control-type"]="frame";
        $this->setIsVisible(true);
        $this->m_id=$id;
        $this->m_owner=$owner;
        $this->m_reloadcallbackMethod=$reloadcallback;
        $this->m_Box=$this->add("div", array(
            "class"=>"igk-framebox-dialog posab no-overflow resizable",
            "id"=>"igk-framebox-dialog"
        ));
        $this->m_Title=$this->m_Box->add("div", array(
            "class"=>"framebox-title",
            "id"=>"framebox_".$id."_title"
        ))->div()->setClass("igk-framebox-dialog-title title");
        $tab=$this->m_Box->add("div", array("class"=>"disptable fitw fith framebox_bgcl"));
        $c=$this->m_Box;
        $this->m_BoxContent=$tab->add("div", array("class"=>"disptabr fith fitw"))->add("div", array("class"=>"igk-framebox-dialog-content disptabc alignl pad4"));
        $v_cdiv=$this->m_Title->div()->setClass("framebox_close");
        $this->m_closeBtn=$v_cdiv->addLinkBtn(IGK_STR_EMPTY, null, 48, 24);
        $this->m_closeBtn["class"]="-igk-btn-lnk igk-framebox-btn-close";
        $this->m_Box["data"]=igk_create_func_callback(array($this, '__get_dialog_attrib'), null);
    }

    /**
    * auto generate doc.
    */

    public function __get_dialog_attrib(){
        return "\"{w:'300px', h:'800px'}\"";
    }

    /**
    * auto generate doc.
    * @param mixed $obj
    * @param mixed $method
    */

    public function addCloseCallBackEvent($obj, $method){
        if($this->m_closeCallBackEvent != null){
            $this->m_closeCallBackEvent->add($obj, $method);
        }
    }

    /**
    * auto generate doc.
    */

    public function ClearChilds(){
        $this->m_BoxContent->clearChilds();
        return $this;
    }

    /**
    * auto generate doc.
    */

    public function closeMethod(){
        if($this->m_callbackMethod){
            $c=$this->m_callbackMethod;
            $c($this);
        }
        if($this->m_closeCallBackEvent != null){
            $this->m_closeCallBackEvent->Call($this, null);
        }
    }

    /**
    * auto generate doc.
    */

    public function getBox(){
        return $this->m_Box;
    }

    /**
    * auto generate doc.
    */

    public function getBoxContent(){
        return $this->m_BoxContent;
    }

    /**
    * auto generate doc.
    */

    public function getcallbackMethod(){
        return $this->m_callbackMethod;
    }

    /**
    * auto generate doc.
    */

    public function getCloseBtn(){
        return $this->m_closeBtn;
    }

    /**
    * auto generate doc.
    */

    public function getcloseMethodUri(){
        return $this->m_closeMethodUri;
    }

    /**
    * auto generate doc.
    */

    public function getcloseUri(){
        return $this->m_closeBtn["href"]->getValue();
    }

    /**
    * auto generate doc.
    */

    public function getForm(){
        return $this->m_form;
    }

    /**
    * auto generate doc.
    */

    public function getHeight(){
        return $this->m_Height;
    }

    /**
    * auto generate doc.
    */

    public function getId(){
        return $this->m_id;
    }

    /**
    * auto generate doc.
    */

    public function getIsVisible(){
        if(!parent::getIsVisible() && !$this->m_framectrl || !$this->m_framectrl->ContainFrame($this->m_id, $this)){
            return false;
        }
        return true;
    }

    /**
    * auto generate doc.
    */

    public function getOwner(){
        return $this->m_owner;
    }

    /**
    * auto generate doc.
    */

    public function getScript(){
        return $this->m_script;
    }

    /**
    * auto generate doc.
    */

    public function getTitle(){
        return $this->m_Title->Content;
    }

    /**
    * auto generate doc.
    */

    public function getWidth(){
        return $this->m_Width;
    }

    /**
    * auto generate doc.
    * @param mixed $obj
    * @param mixed $method
    */

    public function removeCloseCallBackEvent($obj, $method){
        if($this->m_closeCallBackEvent != null){
            $this->m_closeCallBackEvent->remove($obj, $method);
        }
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setcallbackMethod($value){
        $this->m_callbackMethod=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setcloseMethodUri($value){
        $this->m_closeMethod=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setcloseUri($value){
        $this->m_closeBtn["href"]=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setForm($value){
        $this->m_form=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setHeight($value){
        $this->m_Height=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setTitle($value){
        $this->m_Title->Content=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setWidth($value){
        $this->m_Width=$value;
    }
}