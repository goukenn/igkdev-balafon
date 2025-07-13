<?php
// @file: HtmlNotificationItemNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom; 
final class HtmlNotificationItemNode extends HtmlNode{
    private $m_autohided, $m_owner, $m_script;
    protected function _acceptRender($options = null):bool{
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
    public function __construct($owner, $name){
        parent::__construct("div");
        $this->m_autohided=true;
        $this->m_script=igk_create_node("script");
        $this->m_script->Content="\$ns_igk.winui.notifyctrl.init(\$ns_igk.getParentScript());";
        $this->m_owner=$owner;
        $this["class"]="igk-notify-ctrl";
        $this["igk-control-type"]="notifyctrl";
        $this["igk-control-name"]=$name;
    }
    protected function __RenderComplete($o=null){ 
        $this->clearChilds();
        if($this->m_owner->TargetNode === $this){
            $this->m_owner->setNotifyHost(null);
        }
    }
    public function __wakeup(){    }
    function addError($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content=$msg;
    }
    function addErrorr($key){
        $this->addError(__($key, array_slice(func_get_args(), 1)));
    }
    function addInfo($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-info"))->Content=$msg;
    }
    function addInfor($key){
        $this->addInfo(__($key, array_slice(func_get_args(), 1)));
    }
    function addMsg($msg, $type='default'){
        $this->add("div", array("class"=>"igk-notify igk-notify-{$type}"))->Content=$msg;
    }
    function addMsgr($key){
        $this->addMsg(__($key, array_slice(func_get_args(), 1)));
    }
    function addSuccess($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-success"))->Content=$msg;
    }
    function addSuccessr($key){
        $this->addSuccess(__($key, array_slice(func_get_args(), 1)));
    }
    function addWarning($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-warning"))->Content=$msg;
    }
    function addWarningr($key){
        $this->addWarning(__($key, array_slice(func_get_args(), 1)));
    }
    public function getAutoHide(){
        return $this->m_autohided;
    }
    public function setAutohide($v){
        $this->m_autohided=$v;
    }
}