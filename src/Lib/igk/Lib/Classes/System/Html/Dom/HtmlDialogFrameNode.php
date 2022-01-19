<?php
// @file: IGKMsDialogFrame.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IGK\Resources\R;
use IGKEvents;

final class HtmlDialogFrameNode extends HtmlNode{
    private $m_Box, $m_BoxContent, $m_Height, $m_Title, $m_Width, $m_callbackMethod, $m_closeBtn, $m_closeCallBackEvent, $m_closeMethodUri, $m_closeUri, $m_form, $m_framectrl, $m_id, $m_owner, $m_reloadcallbackMethod, $m_script;
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __AcceptRender($o=null){
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
    ///<summary></summary>
    ///<param name="framectrl"></param>
    ///<param name="id" default="null"></param>
    ///<param name="owner" default="null"></param>
    ///<param name="reloadcallback" default="null"></param>
    public function __construct($framectrl, $id=null, $owner=null, $reloadcallback=null){
        parent::__construct("div");
        if(!igk_reflection_class_implement($framectrl, "IIGKFrameController")){
            igk_die("required IIGKFrameController");
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
    ///<summary></summary>
    public function __get_dialog_attrib(){
        return "\"{w:'300px', h:'800px'}\"";
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    public function addCloseCallBackEvent($obj, $method){
        if($this->m_closeCallBackEvent != null){
            $this->m_closeCallBackEvent->add($obj, $method);
        }
    }
    ///<summary></summary>
    public function ClearChilds(){
        $this->m_BoxContent->clearChilds();
        return $this;
    }
    ///<summary></summary>
    public function closeMethod(){
        if($this->m_callbackMethod){
            $c=$this->m_callbackMethod;
            $c($this);
        }
        if($this->m_closeCallBackEvent != null){
            $this->m_closeCallBackEvent->Call($this, null);
        }
    }
    ///<summary></summary>
    public function getBox(){
        return $this->m_Box;
    }
    ///<summary></summary>
    public function getBoxContent(){
        return $this->m_BoxContent;
    }
    ///<summary></summary>
    public function getcallbackMethod(){
        return $this->m_callbackMethod;
    }
    ///<summary></summary>
    public function getCloseBtn(){
        return $this->m_closeBtn;
    }
    ///<summary></summary>
    public function getcloseMethodUri(){
        return $this->m_closeMethod;
    }
    ///<summary></summary>
    public function getcloseUri(){
        return $this->m_closeBtn["href"]->getValue();
    }
    ///<summary></summary>
    public function getForm(){
        return $this->m_form;
    }
    ///<summary></summary>
    public function getHeight(){
        return $this->m_Height;
    }
    ///<summary></summary>
    public function getId(){
        return $this->m_id;
    }
    ///<summary></summary>
    public function getIsVisible(){
        if(!parent::getIsVisible() && !$this->m_framectrl || !$this->m_framectrl->ContainFrame($this->m_id, $this)){
            return false;
        }
        return true;
    }
    ///<summary></summary>
    public function getOwner(){
        return $this->m_owner;
    }
    ///<summary></summary>
    public function getScript(){
        return $this->m_script;
    }
    ///<summary></summary>
    public function getTitle(){
        return $this->m_Title->Content;
    }
    ///<summary></summary>
    public function getWidth(){
        return $this->m_Width;
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    public function removeCloseCallBackEvent($obj, $method){
        if($this->m_closeCallBackEvent != null){
            $this->m_closeCallBackEvent->remove($obj, $method);
        }
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setcallbackMethod($value){
        $this->m_callbackMethod=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setcloseMethodUri($value){
        $this->m_closeMethod=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setcloseUri($value){
        $this->m_closeBtn["href"]=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setForm($value){
        $this->m_form=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setHeight($value){
        $this->m_Height=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setTitle($value){
        $this->m_Title->Content=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setWidth($value){
        $this->m_Width=$value;
    }
}
