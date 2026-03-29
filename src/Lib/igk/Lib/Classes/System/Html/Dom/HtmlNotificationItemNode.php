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
/**
* Html notification item node.
* @package IGK\System\Html\Dom
*/
final class HtmlNotificationItemNode extends HtmlNode{
    /**
    * Properties: autohided, owner, script.
    * @var mixed
    */
    private $m_autohided, $m_owner, $m_script;
    /**
     * Determines whether the notification node should be rendered.
     *
     * @param mixed $options Optional render options.
     * @return bool
     */
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
    /**
     * Constructor.
     *
     * @param mixed  $owner The owning controller of this notification node.
     * @param string $name  The control name identifier.
     */
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
    /**
     * Cleans up child nodes and resets the notify host after rendering is complete.
     *
     * @param mixed $o Optional render context object.
     */
    protected function __RenderComplete($o=null){
        $this->clearChilds();
        if($this->m_owner->TargetNode === $this){
            $this->m_owner->setNotifyHost(null);
        }
    }
    /**
     * Restores the object state after unserialization.
     */
    public function __wakeup(){    }
    /**
     * Adds a danger-styled error notification message.
     *
     * @param string $msg The error message to display.
     */
    function addError($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content=$msg;
    }
    /**
     * Adds a danger-styled error notification using a translated message key.
     *
     * @param string $key The translation key for the error message.
     */
    function addErrorr($key){
        $this->addError(__($key, array_slice(func_get_args(), 1)));
    }
    /**
     * Adds an info-styled notification message.
     *
     * @param string $msg The informational message to display.
     */
    function addInfo($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-info"))->Content=$msg;
    }
    /**
     * Adds an info-styled notification using a translated message key.
     *
     * @param string $key The translation key for the informational message.
     */
    function addInfor($key){
        $this->addInfo(__($key, array_slice(func_get_args(), 1)));
    }
    /**
     * Adds a notification message with the specified type style.
     *
     * @param string $msg  The message to display.
     * @param string $type The notification type (e.g. 'default', 'danger', 'info').
     */
    function addMsg($msg, $type='default'){
        $this->add("div", array("class"=>"igk-notify igk-notify-{$type}"))->Content=$msg;
    }
    /**
     * Adds a notification message using a translated message key.
     *
     * @param string $key The translation key for the message.
     */
    function addMsgr($key){
        $this->addMsg(__($key, array_slice(func_get_args(), 1)));
    }
    /**
     * Adds a success-styled notification message.
     *
     * @param string $msg The success message to display.
     */
    function addSuccess($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-success"))->Content=$msg;
    }
    /**
     * Adds a success-styled notification using a translated message key.
     *
     * @param string $key The translation key for the success message.
     */
    function addSuccessr($key){
        $this->addSuccess(__($key, array_slice(func_get_args(), 1)));
    }
    /**
    * Adds Warning.
    * @param mixed $msg
    */
    function addWarning($msg){
        $this->add("div", array("class"=>"igk-notify igk-notify-warning"))->Content=$msg;
    }
    /**
    * Adds Warningr.
    * @param mixed $key
    */
    function addWarningr($key){
        $this->addWarning(__($key, array_slice(func_get_args(), 1)));
    }
    /**
    * Returns Auto Hide.
    */
    public function getAutoHide(){
        return $this->m_autohided;
    }
    /**
    * Sets Autohide.
    * @param mixed $v
    */
    public function setAutohide($v){
        $this->m_autohided=$v;
    }
}