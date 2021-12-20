<?php
// @file: IGKHtmlAttribs.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\Dom\HtmlCssClassValueAttribute;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\XmlNode;
use IGK\System\Html\HtmlExpressionAttribute;

class IGKHtmlAttribs extends IGKObject implements ArrayAccess, Iterator{
    use IGK\System\Polyfill\ArrayAccessSelfTrait;
    use IGK\System\Polyfill\IteratorTrait;
    const ACTIVATE=1;
    const ATTRIBS=2;
    const ITERATOR=3;
    const OWNER=4;
    private $_f;
    ///<summary></summary>
    ///<param name="owner"></param>
    public function __construct(HtmlItemBase $owner){
        if ($owner === null)
            igk_die("owner must be a HTML Item Base");
        $this->_f=new IGKFv();
        $this->tobj="base";
        $this->_f->setFlag(self::OWNER, $owner);
    }
    ///<summary>display value</summary>
    public function __toString(){
        return "IGKHtmlAttribs [".$this->getcount()."] : ".$this->tobj;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    protected function _access_offsetExists($key): bool{
        if(is_object($key))
            igk_die("offsetExists ::keys is object ");
        $g=$this->getAttributes();
        return isset($g[$key]);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public function _access_offsetGet($key){
        $g=$this->getAttributes();
        if($g && isset($g[$key]))
            return $g[$key];
        return null;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    function _access_offsetSet($key, $value){
        $o=$this->getOwner();
        switch(strtolower($key)){
            case "class":{
                $attr=$this->getAttributes();
                if($value === null){
                    unset($attr[$key]);
                }
                else{
                    if(get_class($o) === XmlNode::class){
                        $attr[$key]=$value;
                    }
                    else{
                        $g=igk_getv($attr, $key);
                        if(($g == null) || !is_object($g)){
                            $g=new HtmlCssClassValueAttribute($this);
                            $attr[$key]=$g;
                        }
                        $g->add($value);
                    }
                }
                $this->_f->updateFlag(self::ATTRIBS, $attr);
            }
            break;
            case "rmclass":
            $tb=explode(" ", $value);
            foreach($tb as $v){
                $v=trim($v);
                if(empty($v))
                    continue;
                $this->offsetSet("class", "-".$v);
            }
            break;
            default: 
            if(strpos($key, 'igk:') === 0){
                $ck=substr($key, 4);
                if($ck == "param"){
                    return;                }
                if(!$o->setSysAttribute($ck, $value, $o->LoadingContext)){
                    $this->offsetSetExpression($key, $value);
                }
            }
            else{
                $this->offsetSetExpression($key, $value);
            }
            break;
        }
    }
    ///<summary></summary>
    ///<param name="key"></param>
    function _access_offsetUnset($key): void{
        $g=$this->getAttributes();
        if($g){
            unset($g[$key]);
            $this->_f->freeFlag(self::ATTRIBS);
        }
    }
    ///<summary></summary>
    private function _initActivateAttrib(){
        $g=array();
        $this->_f->setFlag(self::ACTIVATE, $g);
        return $g;
    }
    ///<summary></summary>
    private function _initattribs(){
        $g=array();
        $this->_f->setFlag(self::ATTRIBS, $g);
        return $g;
    }
    ///<summary></summary>
    protected function _iterator_current(){
        $o=$this->_f->getFlag(self::ITERATOR);
        $a=$this->getActivateAttribs();
        $v_out=isset($a[$o->it_key]) ? HtmlActiveAttrib::getInstance(): $o->it_vtab[$o->it_key];
        return $v_out;
    }
    ///<summary></summary>
    protected function _iterator_key(){
        $o=$this->_f->getFlag(self::ITERATOR);
        return $o->it_key;
    }
    ///<summary>used to activate an attributes or comma separated list of attributes</summary>
    public function activate($n){
        if(empty($n))
            return;
        $g=$this->getActivateAttribs() ?? $this->_initActivateAttrib();
        foreach(explode(",", $n) as $t){
            $g[trim($t)
            ]=1;
        }
        $this->_f->updateFlag(self::ACTIVATE, $g);
    }
    ///<summary></summary>
    public function Clear(){
        $this->_f->Clear();
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function deactivate($n){
        $g=$this->_f->getFlag(self::ACTIVATE);
        if($g){
            unset($g[$n]);
            $this->_f->updateFlag(self::ACTIVATE, $g);
        }
    }
    ///<summary></summary>
    public function Dispose(){
        foreach($this->getAttributes() as $v){
            if(is_object($v) && method_exists(get_class($v), __FUNCTION__)){
                $v->Dispose();
            }
        }
    }
    ///<summary></summary>
    public function getActivateAttribs(){
        $g=$this->_f->getFlag(self::ACTIVATE);
        if($g)
            return $g;
        return null;
    }
    ///<summary></summary>
    public function getAttributes(){
        return $this->_f->getFlag(self::ATTRIBS);
    }
    ///<summary></summary>
    public function getCount(){
        $c=0;
        $g=$this->_f->getFlag(self::ACTIVATE);
        $c=igk_count($this->getAttributes()) + ($g ? igk_count($g): 0);
        return $c;
    }
    ///<summary></summary>
    public function getNS(){
        return $this->_f->getFlag(IGK_DEFINEDNS_FLAG);
    }
    ///<summary></summary>
    public function getOwner(){
        return $this->_f->getFlag(self::OWNER);
    }
    ///<summary></summary>
    function next(): void{
        $o=$this->_f->getFlag(self::ITERATOR);
        $o->it_index++;
        if($o->it_index < $o->it_total){
            $o->it_key=$o->it_keys[$o->it_index];
        }
    }
    ///<summary></summary>
    ///<param name="key">the key of expression to set</param>
    ///<param name="value">value to evaluate</param>
    ///<remark >every expression key must start with '@igk:expression' name or value will be set to default </summary>
    function offsetSetExpression($key, $value): void{
        if(preg_match("/^@igk:expression/", $key)){
            if((($g=$this->getAttributes()) !== null) || (($g=$this->_initattribs()) !== null)){
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
    ///<summary></summary>
    function rewind(): void{
        $attr=$this->getAttributes() ?? array();
        $g=$this->_f->getFlag(self::ACTIVATE) ?? array();
        if($g){
            $attr["@activated"]=$g;
        }
        $o=igk_createobj();
        $o->it_vtab=$attr;
        $o->it_index=0;
        $o->it_total=igk_count($o->it_vtab);
        $o->it_keys=$o->it_vtab ? array_keys($o->it_vtab): null;
        if($o->it_total > 0)
            $o->it_key=$o->it_keys[0];
        else
            $o->it_key=null;
        $this->_f->setFlag(self::ITERATOR, $o);
    }
    ///<summary>set the attribute</summary>
    ///<remark>if value is null then the value will be unset</remark>
    function Set($key, $value){
        if(@preg_match("/^xmlns(:(?P<prefix>(.)+)){0,1}$/", trim($key), $tab)){
            $ns=$this->_f->getFlag(IGK_DEFINEDNS_FLAG) ?? array();
            if($value !== null){
                if(isset($tab["prefix"])){
                    $ns[$tab["prefix"]]=$value;
                }
                else{
                    $ns["@global"]=$value;
                }
            }
            else{
                if(isset($tab["prefix"])){
                    unset($ns[$tab["prefix"]]);
                }
                else
                    unset($ns["@global"]);
            }
            if(igk_count($ns)){
                $this->_f->setFlag(IGK_DEFINEDNS_FLAG, $ns);
            }
            else{
                $this->_f->unsetFlag(IGK_DEFINEDNS_FLAG);
            }
            return;
        }
        $g=$this->getAttributes() ?? $this->_initattribs();
        if($value === null){
            unset($g[$key]);
        }
        else
            $g[$key]=$value;
        $this->_f->updateFlag(self::ATTRIBS, $g);
    }
    ///<summary>transform to array</summary>
    public function to_array(){
        return $this->getAttributes();
    }
    ///<summary>transform key to lower</summary>
    public function to_arrayi(){
        $d=[];
        foreach($this->getAttributes() as $k=>$v){
            $d[strtolower($k)
            ]=$v;
        }
        return $d;
    }
    ///<summary></summary>
    function valid(): bool{
        $o=$this->_f->getFlag(self::ITERATOR);
        $v=(($o->it_index>=0) && ($o->it_index < $o->it_total));
        if(!$v){
            $this->_f->unsetFlag(self::ITERATOR);
        }
        return $v;
    }
}
