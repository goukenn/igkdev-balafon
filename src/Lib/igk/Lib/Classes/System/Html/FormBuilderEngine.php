<?php
// @file: IGKFormBuilderEngine.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

use IIGKFormBuilderEngine;

class FormBuilderEngine implements IIGKFormBuilderEngine{
    protected $frm;
    var $group;
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="arguments"></param>
    public function __call($n, $arguments){
        if((strlen($n) > 3) && (substr($n, 0, 3) == "add")){
            $view=$this->getView();
            call_user_func_array(array($view, $n), $arguments);
        }
        if(strtolower($n) == "setfrm"){        }
        return $this;
    }
    ///<summary></summary>
    ///<param name="frm"></param>
    public function __construct($frm){
        $this->setView($frm);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __get($n){
        if(strtolower($n) == "frm"){
            return $this->frm;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v){
        if((strtolower($n) == "frm") && ($v != null)){
            $this->frm=$v;
        }
    }
    ///<summary></summary>
    ///<param name="$c"></param>
    ///<param name="entries"></param>
    ///<param name="filter" default="null"></param>
    ///<param name="id" default="null"></param>
    protected function _initEntries($c, $entries, $filter=null, $id=null){
        $fobj=["selected"=>0, "value"=>IGK_FD_ID, "key"=>"clName"];
        $callback=null;
        $useempty=0;
        if($filter){
            $fobj["value"]=igk_getv($filter, "value", $fobj["value"]);
            $fobj["key"]=igk_getv($filter, "key", $fobj["key"]);
            $fobj["selected"]=igk_getv($filter, "selected") ?? igk_get_form_args($id) ?? igk_getr($id);
            if(is_callable($fobj["key"])){
                $callback=$fobj["key"];
            }
            if(array_key_exists("emptyvalue", $filter)){
                $useempty=1;
            }
        }
        $rows=$entries;
        if(is_object($entries) && ($rc=igk_getv($rows, "Rows"))){
            $rows=$rc;
        }
        if($useempty){
            $op=$c->add('option');
            $op["value"]=igk_getv($filter, "emptyvalue");
        }
        foreach($rows as $k=>$v){
            $op=$c->add("option");
            $tv=0;
            if($filter){
                $tv=igk_getv($v, $fobj["value"]);
                $op["value"]=$tv;
                if($callback)
                    $op->Content=$callback($v);
                else
                    $op->Content=igk_getv($v, $fobj["key"]);
            }
            else{
                $tv=$k;
                $op["value"]=$k;
                $op->Content=igk_getv($v, $fobj["key"]);
            }
            if($tv == $fobj["selected"]){
                $op["selected"]=1;
            }
        }
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="type" default="'submit'"></param>
    ///<param name="text" default="null"></param>
    public function addButton($id, $type='submit', $text=null){
        $this->getView()->addButton($id, $type)->Content=$text ?? __('btn.'.$id);
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    ///<param name="attribs" default="null"></param>
    public function addCheckbox($id, $value=null, $attribs=null){
        extract(igk_html_extract_id($id));
        $i=$this->addControl($id, "checkbox", null, array("value"=>$value));
        if($attribs && isset($attribs["text"])){
            $span=$this->getView()->add("span");
            $span->Content=$attribs["text"];
        }
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="type" default="'text'"></param>
    ///<param name="style" default="null"></param>
    ///<param name="attribs" default="null"></param>
    public function addControl($id, $type='text', $style=null, $attribs=null){
        extract(igk_html_extract_id($id));
        $view=$this->getView();
        switch($type){default: 
            $i=$view->addInput($id, $type);
            if(isset($tip)){
                $i["placeholder"]=$tip;
            }
            $i->setAttributes($attribs);
            break;
        }
        return $this;
    }
    ///<summary></summary>
    public function addGroup(){
        $g=$this->frm->div();
        $g["class"]="igk-form-group";
        $this->group=$g;
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="class" default="null"></param>
    ///<param name="text" default="null"></param>
    public function addLabel($id, $class=null, $text=null){
        extract(igk_html_extract_id($id));
        $view=$this->getView();
        $lb=$view->add("label");
        $lb["for"]=$id;
        $lb->Content=isset($text) ? $text: (isset($label) ? $label: __("lb.".$id));
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    ///<param name="type" default="'text'"></param>
    ///<param name="style" default="null"></param>
    public function addLabelControl($id, $value=null, $type='text', $style=null){
        extract(igk_html_extract_id($id));
        $__rv=get_defined_vars();
        $this->addLabel($__rv);
        $this->addControl($__rv, $type, $value, $style);
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="entries"></param>
    ///<param name="filter" default="null"></param>
    public function addLabelSelect($id, $entries, $filter=null){
        extract(igk_html_extract_id($id));
        $this->addLabel(get_defined_vars());
        $this->addSelect($id, $entries, $filter);
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    public function addLabelTextarea($id, $value=null){
        extract(igk_html_extract_id($id));
        $this->addLabel(get_defined_vars());
        $this->addTextarea($id, $value);
        return $this;
    }
    ///<summary></summary>
    ///<param name="callback"></param>
    ///<param name="tag" default="'div'"></param>
    public function addObData($callback, $tag='div'){
        $this->getView()->addObData($callback, $tag);
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    ///<param name="attribs" default="null"></param>
    public function addRadioButton($id, $value=null, $attribs=null){
        extract(igk_html_extract_id($id));
        $this->addControl($id, "checkbox", null, array("value"=>$value));
        if($attribs && isset($attribs["text"])){
            $span=$this->getView()->add("span");
            $span->Content=$attribs["text"];
        }
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="entries"></param>
    ///<param name="filter" default="null"></param>
    public function addSelect($id, $entries, $filter=null){
        extract(igk_html_extract_id($id));
        $c=$this->getView()->addSelect($id);
        $c["class"]="igk-form-control -clselect";
        if($entries){
            $this->_initEntries($c, $entries, $filter);
        }
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    public function addTextarea($id, $value=null){
        extract(igk_html_extract_id($id));
        $a=$this->getView()->addTextarea($id);
        $a->setClass("igk-form-control textarea")->Content=$value == null ? igk_getr($id, $value): $value;
        return $this;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    ///<param name="attribs" default="null"></param>
    public function addTextfield($id, $value=null, $attribs=null){
        $this->addLabelControl($id);
    }
    ///<summary></summary>
    public function getLastChild(){
        $view=$this->getView();
        if($view && $view->ChildCount > 0){
            return $view->Childs[$view->ChildCount-1];
        }
        igk_die("failed");
        return null;
    }
    ///<summary></summary>
    public function getView(){
        $c=null;
        if($this->group){
            $c=$this->group;
        }
        else
            $c=$this->frm;
        return $c;
    }
    ///<summary></summary>
    ///<param name="frm"></param>
    public function setView($frm){
        if(!is_object($frm))
            igk_die("engine host required");
        $this->frm=$frm;
    }
}
