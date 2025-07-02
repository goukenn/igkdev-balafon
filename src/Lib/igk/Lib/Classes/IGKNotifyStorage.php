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


class IGKNotifyStorage{
    private $m_name, $tab;
    private $m_autohide;
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
    private function __construct(){    }

    public function addDanger($msg){
        return $this->addError(...func_get_args());        
    }

    public function addError($msg){
        $this->tab[]=["type"=>"igk-danger", "msg"=>$msg];
        return $this;
    }
    public function addErrorr($msg){
        $this->addError(__($msg));
        return $this;
    }
    public function addMsg($msg, ?string $type='igk-defaul'){
        $this->tab[]=["type"=>$type, "msg"=>$msg];
        return $this;
    }
    public function addMsgr($msg){
        $this->addMsg(__($msg));
        return $this;
    }
    public function addSuccess($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>$msg];
        return $this;
    }
    public function addSuccessr($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>__($msg)];
        return $this;
    }
    public function addWarning($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>$msg];
        return $this;
    }
    public function addWarningr($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>__($msg)];
        return $this;
    }
    public function clear(){
        array_splice($this->tab, 0);
        return $this;
    }
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
    public function getName(){
        return $this->m_name;
    }
    public function & getTab(){  
        return $this->tab;
    }
    public function renderAJX(& $options=null){
        igk_die(__METHOD__. " Not implement");
    }
    public function setAutohide(bool $hide){
        $this->m_autohide = $hide;
        return $this;
    }
    public function getAutohide(){
        return $this->m_autohide;        
    }
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
