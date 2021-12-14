<?php

namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlExpressionAttribute;

class HtmlNode extends HtmlItemBase{
     ///<summary>set the class combination of this item</summary>
    /**
     * set the class combination of this item
     */
    public function setClass($value)
    {
        $this["class"] = $value;
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
     ///<summary></summary>
    /**
     * 
     */
    public function getChildCount()
    {
        return igk_count($this->Childs);
    }
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
        $this["id"] =
            $this["name"] = $id;
        return $this;
    }
    public function __construct($tagname=null){
        if ($tagname !==null)
            $this->tagname = $tagname;
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
            if(($c=preg_match_all("%\[eval:(?P<value>[^\]]*)]%i", $value, $tb)) > 0){
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
    protected function _access_OffsetSet($k, $v){
        if ($v===null){
            unset($this->m_attributes[$k]);            
        }else{    
            switch($k){
                case "class":                    
                    if ($v == null){
                        unset($this->m_attributes[$k]);
                    }else {
                        if (!($cl = igk_getv($this->m_attributes, $k))){
                            $cl = new HtmlCssClassValueAttribute();
                            $this->m_attributes[$k] = $cl;                    }
                        }
                        $cl->add($v);
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
                        if($ck == "param"){
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
    function offsetSetExpression($key, $value):void{
        if(preg_match("/^@igk:expression/", $key)){
            if((($g=$this->getAttributes()) !==null) || (($g = $this->_initattribs())!==null))
			{
                if($value === null)
                    unset($g[$key]);
                else
                    $g[$key]=new HtmlExpressionAttribute($value);
				$this->_f->updateFlag(self::ATTRIBS, $g);
            }
            return;
        }
        $this->Set($key, $value);
    }
    public function Set($key, $value){
        $this->m_attributes[$key] = $value;
        return $this;
    }
}