<?php

namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlEventProperty;
use IGK\System\Html\HtmlExpressionAttribute;
use IGK\System\Html\HtmlStyleValueAttribute;

/**
 * 
 * @package IGK\System\Html\Dom
 * @method HtmlNode div() add div
 * @method HtmlNode container() add a div container
 * @method HtmlFormNode form() add a div form
 * @method HtmlFormNode table() add a div form
 * @method HtmlNode br() add break node
 * @method HtmlNode p() add paragraph node 
 */
class HtmlNode extends HtmlItemBase{
    const HTML_NAMESPACE = "http://schemas.igkdev.com/balafon/html";
    ///<summary></summary>
    ///<param name="eventObj"></param>
    ///<return refout="true"></return>
    /**
    * bind event property.
    * ->on(string $type) : return HtmlEventProperty\
    * ->on(string $type, mixed $value): return chain HtmlNode 
    * @param mixed $eventObj event name
    * @return HtmlNode|HtmlEvenProperty depend of number of argument. 
    */
    public function on($eventObj){
        $c=$this->getFlag(self::EVENTS) ?? array();
        if(isset($c[$eventObj])){
            $b=$c[$eventObj];
            return $b;
        }
        $b= HtmlEventProperty::CreateEventProperty($eventObj);
        $c[$eventObj]=$b;
        $this->setFlag(self::EVENTS, $c);
        if(func_num_args() > 1){
            $b->content=func_get_args()[1];
            return $this;
        }
        return $b;
    }
    public function addNode($name){
        if ($this->getCanAddChilds()){
            $p = new HtmlNode($name);
            return $this->add($p);
        }        
    }
     ///<summary>set the class combination of this item</summary>
    /**
     * set the class combination of this item
     */
    public function setClass($value)
    {
        $this["class"] = $value;
        return $this;
    }
    /**
     * clear class
     * @return $this 
     */
    public function clearClass(){
       
        $this["class"] = null;         
        return $this;
    }

    public function clear(){
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $value
    */
    public function setStyle($value){
        if(empty($value))
            return;
        if(0===strpos($value, '+/')){
            $s=$this["style"];
            $value=substr($value, 2);
        }
        $this["style"]=igk_css_treatstyle($value);
        return $this;
    }
    public function __call($n, $arguments){
        if (in_array(strtolower($n), ["address"])){
            if ($this->getCanAddChilds()){
                $tab=array(strtolower($n), null, $arguments);
                return call_user_func_array([$this, IGK_ADD_PREFIX], $tab);
            } 
        }
        return parent::__call($n,$arguments);
    }
    /*
    address have a special meaning
    public function address(){
        if ($this->getCanAddChilds()){
            $c = new HtmlNode("address");
            $this->add($c);
            return $c;
        }
    }
    */

     ///<summary></summary>
    /**
     * 
     */
    public function getChildCount()
    {
        return $this->getChilds()->count();
    }
    /**
     * assert class
     * @param mixed $condition 
     * @param mixed $value 
     * @return $this 
     */
    public function setAssertClass($condition, $value)
    {
        if ($condition) {
            $this->setClass($value);
        }
        return $this;
    }
     ///<summary>set the id of this item</summary>
    /**
     * set the id of this item
     */
    public function setId($id)
    {
        $this["id"] = $this["name"] = $id;
        return $this;
    }
    public function __construct($tagname=null){
        parent::__construct();
        if ($tagname !==null)
            $this->tagname = $tagname;
        $this->initialize();        
    }
    /**
     * initialize this node
     * @return void 
     */
    protected function initialize(){

    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    ///<param name="context" default="null"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    * @param mixed $context the default value is null
    */
    public function setSysAttribute($key, $value, $context=null){
        if(($context !== null) && ($value !== null) && (is_string($value))){
            $tb=array();
            if((preg_match_all("%\[eval:(?P<value>[^\]]*)]%i", $value, $tb)) > 0){
                $e=igk_str_read_in_brancket($value, "[", "]");
                $script=substr($e[0], strpos($e[0], ":") + 1);
                if(!empty($script))
                    $value=igk_html_eval_value_in_context($script, $context);
            }
        }
        $t=array("style"=>"class");
        $m=igk_getv($t, strtolower($key));
        if($m){
            $this[$m]=strtolower("+igk-".$this->getItemType()."-". $value);
        }
        else{
            $k="set".$key;
            if(method_exists($this, $k)){
                $this->$k($value);
            }
            else{
                // $cond = igk_server_is_local() && (($context !== null) && ($context !== 'Load'));               
                // igk_assert_die($cond, "/!\\ Method not define [". $key. "] :::".$value. " :::".get_class($this). "::::Context[".$context."]");
                return false;
            }
        }
        return $this;
    }

    public function setProperty($name, $value){
        $n = igk_getv($this, $name);
        $this->$name = $value;
        if ($n!= $value){  
            // + | ------------------------------------------
            // + | dom property changed
            // + | 
            igk_hook(\IGKEvents::HOOK_DOM_PROPERTY_CHANGED, [
                "node"=>$this, 
                "name"=>$name, 
                "new"=>$value, 
                "old"=>$n]); 
        }
        return $this;
    }
    public function __set($n, $v){
        parent::__set($n, $v);
    }

    protected function _access_OffsetSet($k, $v){
        if ($v===null){
            unset($this->m_attributes[$k]);            
        }else{    
            switch($k){
                case "class":                    
                    if ($v === null){
                        unset($this->m_attributes[$k]);
                    }else {
                        if (!($cl = igk_getv($this->m_attributes, $k))){
                            $cl = new HtmlCssClassValueAttribute();
                            $this->m_attributes[$k] = $cl;                   
                        }
                        $cl->add($v);
                    }
                    break;
                case "style":
                    if (!($cl = igk_getv($this->m_attributes, $k))){
                        $cl = new HtmlStyleValueAttribute($this);
                    }
                    $cl->setValue($v);
                    $this->m_attributes[$k] = $cl;
                    break;
                default:
                    if(strpos($k, 'igk:') === 0){
                        $ck=substr($k, 4);
                        
                        if(!HtmlOptions::IsAllowedAttribute($ck)){ 
                            return;
                        }
                        if(!$this->setSysAttribute($ck, $v, $this->getLoadingContext())){
                            $this->offsetSetExpression($k, $v);
                        }
                    }
                    else{ 
                        $this->m_attributes[$k] = $v;
                    }
                break;
            }       
        }
        return $this;
    }

     ///<summary></summary>
    ///<param name="key">the key of expression to set</param>
    ///<param name="value">value to evaluate</param>
	///<remark >every expression key must start with '@igk:expression' name or value will be set to default </summary>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    function offsetSetExpression($key, $value){
        if(preg_match("/^@igk:expression/", $key)){
            if((($g=$this->getAttributes()) !==null) || (($g = $this->_initattribs())!==null))
			{
                if($value === null)
                    unset($g[$key]);
                else
                    $g[$key]=new HtmlExpressionAttribute($value);
				$this->_f->updateFlag(self::ATTRIBS, $g);
            }
            return $this;
        }
        return $this->Set($key, $value);
    }
    public function Set($key, $value){
        $this->m_attributes[$key] = $value;
        return $this;
    }

    public function getCanRenderTag()
    {
        if ($this->iscallback(__FUNCTION__)){
            $this->evalCallback(__FUNCTION__, $output);
            return $output;
        }
        return parent::getCanRenderTag();
    }

    public function activate($n){
        $this->m_attributes->activate($n);
        return $this;
    }
    public function deactivate($n){
        $this->m_attributes->deactivate($n);
        return $this;
    }
}