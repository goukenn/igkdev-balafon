<?php
// @file: IGKNotificationCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\System\Html\Dom\HtmlNotificationItemNode;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;
use IGKException;
use IGKNotifyStorage;
use IGK\INotifyMessage;
use function igk_resources_gets as __;

/**
* Notification controller.
* @package IGK\Controllers
*/
final class NotificationController extends BaseController implements INotifyMessage {
    /**
    * Type of notify type.
    * @var mixed
    */
    private static $NotifyType=["success"=>"addSuccess", "danger"=>'addError'];
    /**
    * Property: marks.
    * @var mixed
    */
    private $m_marks;
    /**
     * notification list table 
     * @return array 
     */
    public function getTab():array{
        return [];
    }
    /**
    * Handles calls to undefined methods.
    * @param mixed $name
    * @param mixed $c
    */
    public function __call($name, $c){
        if(method_exists($this, $fc="add".$name)){
            return $this->$fc(...$c);
        }
        return parent::__call($name, $c);
    }
    /**
    * Adds Error.
    * @param mixed $msg
    */
    public function addError($msg){
        $this->TargetNode->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content=$msg;
    }
    /**
    * Adds Errori.
    * @param mixed $msgcode
    */
    public function addErrori($msgcode){
        $c=igk_error($msgcode);
        if($c){
            $li=igk_create_node("div", array("class"=>"alignl"));
            $li->addLabel()->Content="Message : ";
            $li->addspan()->Content=__($c["Msg"]);
            $this->addError($li->render(null));
        }
    }
    /**
    * Adds Errorr.
    * @param mixed $key
    */
    public function addErrorr($key){
        $this->addError(__($key, array_slice(func_get_args(), 1)));
    }
    /**
    * Adds Info.
    * @param mixed $msg
    */
    public function addInfo($msg){
        $this->TargetNode->add("div", array("class"=>"igk-notify igk-notify-info"))->Content=$msg;
        $this->m_hasmsg=true;
    }
    /**
    * Adds Infor.
    * @param mixed $msgKeys
    */
    public function addInfor($msgKeys){
        $this->addInfo(__($msgKeys, array_slice(func_get_args(), 1)));
    }
    /**
    * Adds Msg.
    * @param mixed $msg
    */
    public function addMsg($msg){
        $mg=$this->getGlobalStorage();
        $mg->addMsg($msg);
    }
    /**
    * Adds Msgr.
    * @param mixed $msg
    */
    public function addMsgr($msg){
        $this->addMsg(__($msg, array_slice(func_get_args(), 1)));
    }
    /**
    * Adds Success.
    * @param mixed $msg
    */
    function addSuccess($msg){
        $mg=$this->getGlobalStorage();
        $mg->addSuccess($msg);
    }
    /**
    * Adds Successr.
    * @param mixed $msg
    */
    function addSuccessr($msg){
        $mg=$this->getGlobalStorage();
        $mg->addSuccessr($msg);
    }
    /**
    * Adds Warning.
    * @param mixed $msg
    */
    public function addWarning($msg){
        $mg=$this->getGlobalStorage();
        $mg->addWarning($msg);
    }
    /**
    * Adds Warningr.
    * @param mixed $msg
    */
    public function addWarningr($msg){
        $this->addWarning(__($msg, array_slice(func_get_args(), 1)));
    }
    /**
    * Binds.
    * @param mixed $msg
    * @param mixed $t
    */
    public function bind($msg, $t='success'){
        $fc=igk_getv(self::$NotifyType, $t, "addMsg");
        call_user_func_array([$this, $fc], [$msg]);
    }
    /**
    * Returns Auto Hided.
    */
    public function getAutoHided(){
        return $this->getGlobalStorage()->getAutoHided();
    }
    /**
    * Returns Global Storage.
    */
    public function getGlobalStorage(){
        static $storage=null;
        if($storage === null){
            $storage=$this->getNotification("::global");
        }
        return $storage;
    }
    /**
    * Returns Has Msg.
    */
    public function getHasMsg(){
        $mg=$this->getGlobalStorage();
        return $mg->tab && count($mg->tab) > 0;
    }
    /**
    * Returns Ms Error.
    */
    public function getMsError(){
        return $this->m_hasmsg;
    }
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return IGK_NOTIFICATION_CTRL;
    }
    /**
    * Returns Notification.
    * @param mixed $name
    */
    public function getNotification($name="::global"){
        static $storage;
        if(empty($name)){
            igk_die("notification name is empty");
        }
        if($storage === null){
            $storage=[];
        }
        if(isset($storage[$name])){
            return $storage[$name];
        }
        $notify=& igk_app()->session->getReference("notifications");
        $c=null;
        if($notify == null){
            $notify=array($name=>[]);
        }
        else{
            if(!isset($notify[$name])){
                $notify[$name]=[];
            }
        }
        $tab=& igk_app()->session->getData();
        $tab["notifications"]=& $notify;
        $tab=& $notify[$name];
        if($c=IGKNotifyStorage::Create($tab, $name)){
            $storage[$name]=$c;
            return $c;
        }
        return;
    }
    /**
    * Returns Notification Event.
    * @param mixed $name
    */
    public function getNotificationEvent($name){
        return null;
    }
    /**
    * Returns Notify Host.
    */
    public function getNotifyHost(){
        if($this->m_notifyhost === null)
            $this->m_notifyhost=$this->app->Doc->body;
        return $this->m_notifyhost;
    }
    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $v=new HtmlNotificationItemNode($this, "global");
        return $v;
    }
    /**
    * Mark.
    * @param mixed $tagid
    */
    public function mark($tagid){
        if($this->m_marks == null){
            $this->m_marks=array();
        }
        $this->m_marks[$tagid]=1;
    }
    /**
    * Notification is visible.
    * @param mixed $target
    * @param mixed $host
    * @param mixed $name
    */
    public function NotificationIsVisible($target, $host, $name){
        $c=null;
        if(empty($name)){
            $c=$this->getNotification("::global", true);
        }
        else
            $c=igk_notifyctrl($name);
        if($c){
            if(!$c->autohide){
                $host["class"]="-igk-anim-autohide";
            }
            $tab=$c->getTab();
            if(is_array($tab) && (count($tab) > 0)){
                foreach($tab as $inf){
                    if(isset($inf["type"]) && isset($inf["msg"])){
                        $host->add("div")->setClass("igk-panel ".$inf["type"])->Content=$inf["msg"];
                    }
                }
                $c->clear();
                return true;
            }
        }
        return false;
    }
    /**
    * Notifies ajx.
    */
    public function notify_ajx(){
        $view=igk_getr("rv");
        $render=false;
        if(igk_is_ajx_demand()){
            if($this->HasMsg){
                if($this->getParam("ajx:renderincontext") !== true){
                    $this->setParam("ajx:renderincontext", true);
                    $d=igk_create_node("div")->addScript();
                    $uri=$this->getUri("notify_ajx&rv=1");
                    $d->Content=<<<EOF
(function(){ ns_igk.ajx.post('{$uri}',null, ns_igk.ajx.fn.prepend_to_body); })();
EOF;
                    $d->renderAJX();
                }
                else if($view){
                    $render=true;
                }
            }
        }
        else{
            $render=$this->HasMsg;
        }
        if($render){
            $this->TargetNode->renderAJX();
            $this->m_hasmsg=false;
            $this->setParam("ajx:context", null);
            $this->setParam("ajx:renderincontext", null);
        }
    }
    /**
    * Page folder changed.
    * @param null|mixed $sender
    * @param null|mixed $args
    */
    public function pageFolderChanged($sender=null, $args=null){
        if($this->HasMsg){
            $this->TargetNode->clearChilds();
            $this->View();
        }
    }
    /**
    * Registers Notification.
    * @param mixed $name
    * @param mixed $callable
    */
    public function registerNotification($name, $callable){
        igk_die(__METHOD__." registerNotification ");
    }
    /**
    * Resets Notification.
    * @param mixed $name
    */
    public function resetNotification($name){    }
    /**
    * Sets Autohide.
    * @param mixed $v
    */
    public function setAutohide($v){
        $this->TargetNode->setAutohide($v);
    }
    /**
    * Sets Ms Error.
    * @param mixed $v
    */
    public function setMsError($v){
        $this->m_hasmsg=$v;
    }
    /**
     * bind notify controller
     * @param mixed $notifyhost 
     * @param string $name 
     * @param mixed $options 
     * @return $this 
     * @throws IGKException 
     */
    public function setNotifyHost($notifyhost, $name="::global", $options=null){
        if($notifyhost){
            $n=$this->getNotification($name);
            if($n){
                $m=igk_create_notagnode();
                $s=new HtmlSingleNodeViewerNode($m);
                $tab=igk_create_node_callback([$this, "NotificationIsVisible"], [$notifyhost, $name]);
                $notifyhost->setCallback("getIsVisible", $tab);
                $s->setCallback("getIsVisible", $tab);
                $notifyhost->add($s);
            }
        }
        return $this;
    }
    /**
    * Unregister notification.
    * @param mixed $name
    * @param null|mixed $obj
    * @param null|mixed $method
    */
    public function unregisterNotification($name, $obj=null, $method=null){
        if(($obj == null) && ($method == null)){
            $this->resetNotification($name);
            return 1;
        }
        else{
            $e=igk_getv($this->m_notifyevents, $name);
            if($e){
                if($name == IGK_GLOBAL_EVENT && is_object($obj)){
                    return $e->removeObject($obj);
                }
                return $e->remove($obj, $method);
            }
        }
        return 0;
    }
    /**
    * Unset notication.
    * @param mixed $name
    */
    public function unsetNotication($name){
        if(isset($this->m_notificationChilds[$name])){
            unset($this->m_notificationChilds[$name]);
        }
    }
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{
        $t = $this->getTargetNode();
        if(!$this->HasMsg){
            $t->remove();
        }
        else{
            $t->setIndex(-10000);
            $host=$this->NotifyHost;
            if($host !== null){
                $host->add($t);                
            }
        }
        return $this;
    }
    /**
    * Returns Messages.
    */
    public function getMessages(){
        $store = $this->getGlobalStorage();
        return $store->getMessages(); 
    }
    /**
    * Clears.
    */
    public function clear(){
        $store = $this->getGlobalStorage();
        return $store->clear(); 
    }
}