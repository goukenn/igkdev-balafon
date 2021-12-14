<?php
// @file: IGKNotifyStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

class IGKNotifyStorage{
    private $m_name, $tab;
    var $autohide;
    ///<summary>Represente __call function</summary>
    ///<param name="name"></param>
    ///<param name="args"></param>
    public function __call($name, $args){
        if(method_exists($this, $fc="add".$name)){
            return $this->$fc(...$args);
        }
        else{
            if(count($args) > 0)
                $this->tab[]=["type"=>"igk-".$name, "msg"=>$args[0]];
        }
    }
    ///<summary>ctr.</summary>
    private function __construct(){    }
    ///<summary>Represente addError function</summary>
    ///<param name="msg"></param>
    public function addError($msg){
        $this->tab[]=["type"=>"igk-danger", "msg"=>$msg];
    }
    ///<summary>Represente addErrorr function</summary>
    ///<param name="msg"></param>
    public function addErrorr($msg){
        $this->addError(__($msg));
    }
    ///<summary>Represente addMsg function</summary>
    ///<param name="msg"></param>
    public function addMsg($msg){
        $this->tab[]=["type"=>"igk-default", "msg"=>$msg];
    }
    ///<summary>Represente addMsgr function</summary>
    ///<param name="msg"></param>
    public function addMsgr($msg){
        $this->addMsg(__($msg));
    }
    ///<summary>Represente addSuccess function</summary>
    ///<param name="msg"></param>
    public function addSuccess($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>$msg];
    }
    ///<summary>Represente addSuccessr function</summary>
    ///<param name="msg"></param>
    public function addSuccessr($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>__($msg)];
    }
    ///<summary>Represente addWarning function</summary>
    ///<param name="msg"></param>
    public function addWarning($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>$msg];
    }
    ///<summary>Represente addWarningr function</summary>
    ///<param name="msg"></param>
    public function addWarningr($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>__($msg)];
    }
    ///<summary>Represente clear function</summary>
    public function clear(){
        array_splice($this->tab, 0);
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
        $o->m_name=$name;
        $o->autohide=true;
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
    public function setAutohide($hide){
        $this->autohide=$hide;
    }
    ///<summary> set response</summary>
    public function setResponse(array $data){
        $this->tab=[$data];
    }
}
