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
* auto generate doc.
*/
class IGKNotifyStorage{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_name, $tab;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_autohide;

    /**
    * auto generate doc.
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
    private function __construct(){    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addDanger($msg){
        return $this->addError(...func_get_args());        
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addError($msg){
        $this->tab[]=["type"=>"igk-danger", "msg"=>$msg];
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addErrorr($msg){
        $this->addError(__($msg));
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    * @param null|string $type
    */

    public function addMsg($msg, ?string $type='igk-defaul'){
        $this->tab[]=["type"=>$type, "msg"=>$msg];
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addMsgr($msg){
        $this->addMsg(__($msg));
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addSuccess($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>$msg];
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addSuccessr($msg){
        $this->tab[]=["type"=>"igk-success", "msg"=>__($msg)];
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addWarning($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>$msg];
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function addWarningr($msg){
        $this->tab[]=["type"=>"igk-warning", "msg"=>__($msg)];
        return $this;
    }

    /**
    * auto generate doc.
    */

    public function clear(){
        array_splice($this->tab, 0);
        return $this;
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
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
    * auto generate doc.
    * @param null|mixed & $options
    */

    public function renderAJX(& $options=null){
        igk_die(__METHOD__. " Not implement");
    }

    /**
    * auto generate doc.
    * @param bool $hide
    */

    public function setAutohide(bool $hide){
        $this->m_autohide = $hide;
        return $this;
    }

    /**
    * auto generate doc.
    */

    public function getAutohide(){
        return $this->m_autohide;        
    }

    /**
    * auto generate doc.
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