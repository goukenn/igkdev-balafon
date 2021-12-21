<?php
// @file: HtmlNotificationItemNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom; 

final class HtmlNotificationItemNode extends HtmlNode{
    private $m_autohided, $m_owner, $m_script;
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __AcceptRender($o=null){
        if(!$this->IsVisible || !$this->HasChilds)
            return false;
        if($this->m_autohided){
            $this->add($this->m_script);
        }
        else{
            $this->m_script->remove();
        }
        return true;
    }
    ///<summary></summary>
    ///<param name="owner"></param>
    public function __construct($owner, $name){
        parent::__construct("div");
        $this->m_autohided=true;
        $this->m_script=igk_createnode("script");
        $this->m_script->Content="\$ns_igk.winui.notifyctrl.init(\$ns_igk.getParentScript());";
        $this->m_owner=$owner;
        $this["class"]="igk-notify-ctrl";
        $this["igk-control-type"]="notifyctrl";
        $this["igk-control-name"]=$name;
    }
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __RenderComplete($o=null){
        /// TODO: Render Complete notificiation

        $this->clearChilds();
        if($this->m_owner->TargetNode === $this){
            $this->m_owner->setNotifyHost(null);
        }
    }
    ///<summary>Represente __wakeup function</summary>
    public function __wakeup(){    }
    ///<summary></summary>
    ///<param name="msg"></param>
    function addError($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content=$msg;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function addErrorr($key){
        $this->addError(__($key, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    function addInfo($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-info"))->Content=$msg;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function addInfor($key){
        $this->addInfo(__($key, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    ///<param name="type" default="'default'"></param>
    function addMsg($msg, $type='default'){
        $this->add("div", array("class"=>"igk-notify igk-notify-{$type}"))->Content=$msg;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function addMsgr($key){
        $this->addMsg(__($key, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    function addSuccess($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-success"))->Content=$msg;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function addSuccessr($key){
        $this->addSuccess(__($key, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    function addWarning($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-warning"))->Content=$msg;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function addWarningr($key){
        $this->addWarning(__($key, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    public function getAutoHide(){
        return $this->m_autohided;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setAutohide($v){
        $this->m_autohided=$v;
    }
}
