<?php
// @file: IGKNotifyStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\Exceptions\OperationNotAllowedException;
use function igk_resources_gets as __;

/**
* Igknotify storage.
*/
class IGKNotifyStorage{
    /**
    * Properties: name, tab.
    * @var mixed
    */
    private $m_name, $tab;
    /**
    * Property: autohide.
    * @var mixed
    */
    private $m_autohide;
    /**
    * Handles calls to undefined methods.
    * @param mixed $name
    * @param mixed $args
    */
    public function __call($name, $args){
        if(method_exists($this, $fc="add".$name)){
            return $this->$fc(...$args);
        }
        else{
            if(count($args) > 0){
                $this->tab[]=["type"=>"igk-".$name, "msg"=>$args[0]];
                return $this;
            }
        }
        throw new OperationNotAllowedException('notifyStorage');
    }
    /**
    * .ctr
    * @return mixed
    */
    private function __construct(){    }
    /**
    * Adds Danger.
    * @param mixed $msg
    */
    public function addDanger($msg){
        return $this->addError(...func_get_args());        
    }
    /**
    * Adds Error.
    * @param mixed $msg
    */
    public function addError($msg){
        $this->tab[]=["type"=>"igk-danger", "msg"=>$msg];
        return $this;
    }
    /**
    * Adds Errorr.
    * @param mixed $msg
    */
    public function addErrorr($msg){
        $this->addError(__($msg));
        return $this;
    }
    /**
    * Adds Msg.
    * @param mixed $msg
    * @param null|string $type
    */
    public function addMsg($msg, ?string $type='igk-defaul'){
        $this->tab[]=["type"=>$type, "msg"=>$msg];
        return $this;
    }
    /**
    * Adds Msgr.
    * @param mixed $msg
    */
    public function addMsgr($msg){
        $this->addMsg(__($msg));
        return $this;
    }
    /**
    * Adds Success.
    * @param mixed $msg
    */
    public function addSuccess($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>$msg];
        return $this;
    }
    /**
    * Adds Successr.
    * @param mixed $msg
    */
    public function addSuccessr($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>__($msg)];
        return $this;
    }
    /**
    * Adds Warning.
    * @param mixed $msg
    */
    public function addWarning($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>$msg];
        return $this;
    }
    /**
    * Adds Warningr.
    * @param mixed $msg
    */
    public function addWarningr($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>__($msg)];
        return $this;
    }
    /**
    * Clears.
    */
    public function clear(){
        array_splice($this->tab, 0);
        return $this;
    }
    /**
    * Creates.
    * @param mixed & $tab
    * @param mixed $name
    */
    public static function Create(& $tab, $name){
        if($tab === null){
            return null;
        }
        $cl=__CLASS__;
        $o=new $cl();
        $o->tab=& $tab;
        $o->m_name = $name;
        $o->m_autohide=true;
        return $o;
    }
    /**
    * Returns Name.
    */
    public function getName(){
        return $this->m_name;
    }
    /**
     * notity storage 
     * @return mixed 
     */
    public function & getTab(){  
        return $this->tab;
    }
    /**
    * Renders AJX.
    * @param null|mixed & $options
    */
    public function renderAJX(& $options=null){
        igk_die(__METHOD__. " Not implement");
    }
    /**
    * Sets Autohide.
    * @param bool $hide
    */
    public function setAutohide(bool $hide){
        $this->m_autohide = $hide;
        return $this;
    }
    /**
    * Returns Autohide.
    */
    public function getAutohide(){
        return $this->m_autohide;        
    }
    /**
    * Sets Response.
    * @param array $data
    */
    public function setResponse(array $data){
        $this->tab=[$data];
        return $this;
    }
    /**
     * get messages
     */
    public function getMessages(){
        return array_map(function($a){ return $a['msg']; }, $this->tab);
    }
}