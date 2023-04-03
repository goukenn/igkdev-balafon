<?php
// @file: IGKNotificationCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGK\System\Html\Dom\HtmlNotificationItemNode;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;
use IGKException;
use IGKNotifyStorage;
use IIGKNotifyMessage;
use function igk_resources_gets as __;


final class NotificationController extends BaseController implements IIGKNotifyMessage {
    private static $NotifyType=["success"=>"addSuccess", "danger"=>'addError'];
    private $m_marks;
    ///<summary>Represente __call function</summary>
    ///<param name="name"></param>
    ///<param name="c"></param>
    public function __call($name, $c){
        if(method_exists($this, $fc="add".$name)){
            return $this->$fc(...$c);
        }
        return parent::__call($name, $c);
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function addError($msg){
        $this->TargetNode->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content=$msg;
    }
    ///<summary></summary>
    ///<param name="msgcode"></param>
    public function addErrori($msgcode){
        $c=igk_error($msgcode);
        if($c){
            $li=igk_create_node("div", array("class"=>"alignl"));
            $li->addLabel()->Content="Message : ";
            $li->addspan()->Content=__($c["Msg"]);
            $this->addError($li->render(null));
        }
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public function addErrorr($key){
        $this->addError(__($key, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function addInfo($msg){
        $this->TargetNode->add("div", array("class"=>"igk-notify igk-notify-info"))->Content=$msg;
        $this->m_hasmsg=true;
    }
    ///<summary></summary>
    ///<param name="msgKeys"></param>
    public function addInfor($msgKeys){
        $this->addInfo(__($msgKeys, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function addMsg($msg){
        $mg=$this->getGlobalStorage();
        $mg->addMsg($msg);
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function addMsgr($msg){
        $this->addMsg(__($msg, array_slice(func_get_args(), 1)));
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    function addSuccess($msg){
        $mg=$this->getGlobalStorage();
        $mg->addSuccess($msg);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function addSuccessr($msg){
        $mg=$this->getGlobalStorage();
        $mg->addSuccessr($msg);
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function addWarning($msg){
        $mg=$this->getGlobalStorage();
        $mg->addWarning($msg);
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function addWarningr($msg){
        $this->addWarning(__($msg, array_slice(func_get_args(), 1)));
    }
    ///<summary>Represente bind function</summary>
    ///<param name="msg"></param>
    ///<param name="t" default="'success'"></param>
    public function bind($msg, $t='success'){
        $fc=igk_getv(self::$NotifyType, $t, "addMsg");
        call_user_func_array([$this, $fc], [$msg]);
    }
    ///<summary>get auto hided</summary>
    public function getAutoHided(){
        return $this->getGlobalStorage()->getAutoHided();
    }
    ///<summary>Represente getGlobalStorage function</summary>
    public function getGlobalStorage(){
        static $storage=null;
        if($storage === null){
            $storage=$this->getNotification("::global");
        }
        return $storage;
    }
    ///<summary></summary>
    public function getHasMsg(){
        $mg=$this->getGlobalStorage();
        return $mg->tab && count($mg->tab) > 0;
    }
    ///<summary></summary>
    public function getMsError(){
        return $this->m_hasmsg;
    }
    ///<summary></summary>
    public function getName(){
        return IGK_NOTIFICATION_CTRL;
    }
    ///<summary>get notification item array storage</summary>
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
    ///<summary></summary>
    ///<param name="name"></param>
    public function getNotificationEvent($name){
        return null;
    }
    ///<summary></summary>
    public function getNotifyHost(){
        if($this->m_notifyhost === null)
            $this->m_notifyhost=$this->app->Doc->body;
        return $this->m_notifyhost;
    }
    ///<summary></summary>
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $v=new HtmlNotificationItemNode($this, "global");
        return $v;
    }
    ///<summary></summary>
    ///<param name="tagid"></param>
    public function mark($tagid){
        if($this->m_marks == null){
            $this->m_marks=array();
        }
        $this->m_marks[$tagid]=1;
    }
    ///<summary>Render Noticiation node</summary>
    ///<param name="n"></param>
    ///<param name="name"></param>
    public function NotificationIsVisible($target, $host, $name){
        $c=null;
        if(empty($name)){
            $c=$this->getNotification("::global", true);
        }
        else
            $c=igk_notifyctrl($name);
        if($c){
            if(!$c->autohide){
                $host["class"]="-anim-autohide";
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
    ///<summary></summary>
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
    ///<summary></summary>
    ///<param name="sender" default="null"></param>
    ///<param name="args" default="null"></param>
    public function pageFolderChanged($sender=null, $args=null){
        if($this->HasMsg){
            $this->TargetNode->clearChilds();
            $this->View();
        }
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public function registerNotification($name, $callable){
        igk_die(__METHOD__." registerNotification ");
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public function resetNotification($name){    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setAutohide($v){
        $this->TargetNode->setAutohide($v);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setMsError($v){
        $this->m_hasmsg=$v;
    }
    ///<summary></summary>
    ///<param name="notifyhost"></param>
    ///<param name="name" default="null"></param>
    ///<param name="options" default="null"></param>
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
    ///<summary>unregister notification</summary>
    ///<remark>if obj is null will clear the notification event list</remark>
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
    ///<summary>free notification item</summary>
    public function unsetNotication($name){
        if(isset($this->m_notificationChilds[$name])){
            unset($this->m_notificationChilds[$name]);
        }
    }
    ///<summary>Render notification controller</summary>
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
}
