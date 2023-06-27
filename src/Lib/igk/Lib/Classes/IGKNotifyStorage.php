<?php
// @file: IGKNotifyStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Exceptions\OperationNotAllowedException;

use function igk_resources_gets as __;


class IGKNotifyStorage{
    private $m_name, $tab;
    private $m_autohide;
    ///<summary>Represente __call function</summary>
    ///<param name="name"></param>
    ///<param name="args"></param>
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
    ///<summary>ctr.</summary>
    private function __construct(){    }

    public function addDanger($msg){
        return $this->addError(...func_get_args());        
    }

    ///<summary>Represente addError function</summary>
    ///<param name="msg"></param>
    public function addError($msg){
        $this->tab[]=["type"=>"igk-danger", "msg"=>$msg];
        return $this;
    }
    ///<summary>Represente addErrorr function</summary>
    ///<param name="msg"></param>
    public function addErrorr($msg){
        $this->addError(__($msg));
        return $this;
    }
    ///<summary>Represente addMsg function</summary>
    ///<param name="msg"></param>
    public function addMsg($msg, ?string $type='igk-defaul'){
        $this->tab[]=["type"=>$type, "msg"=>$msg];
        return $this;
    }
    ///<summary>Represente addMsgr function</summary>
    ///<param name="msg"></param>
    public function addMsgr($msg){
        $this->addMsg(__($msg));
        return $this;
    }
    ///<summary>Represente addSuccess function</summary>
    ///<param name="msg"></param>
    public function addSuccess($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>$msg];
        return $this;
    }
    ///<summary>Represente addSuccessr function</summary>
    ///<param name="msg"></param>
    public function addSuccessr($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>__($msg)];
        return $this;
    }
    ///<summary>Represente addWarning function</summary>
    ///<param name="msg"></param>
    public function addWarning($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>$msg];
        return $this;
    }
    ///<summary>Represente addWarningr function</summary>
    ///<param name="msg"></param>
    public function addWarningr($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>__($msg)];
        return $this;
    }
    ///<summary>Represente clear function</summary>
    public function clear(){
        array_splice($this->tab, 0);
        return $this;
    }
    ///<summary>Represente Create function</summary>
    ///<param name="tab" ref="true"></param>
    ///<param name="name"></param>
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
    ///<summary>Represente getName function</summary>
    public function getName(){
        return $this->m_name;
    }
    ///<summary>Represente getTab function</summary>
    ///<return refout="true"></return>
    public function & getTab(){  
        return $this->tab;
    }
    ///<summary>Represente renderAJX function</summary>
    ///<param name="options" default="null" ref="true"></param>
    public function renderAJX(& $options=null){
        igk_die(__METHOD__. " Not implement");
    }
    ///<summary>Represente setAutohide function</summary>
    ///<param name="hide"></param>
    public function setAutohide(bool $hide){
        $this->m_autohide = $hide;
        return $this;
    }
    public function getAutohide(){
        return $this->m_autohide;        
    }
    ///<summary> set response</summary>
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
