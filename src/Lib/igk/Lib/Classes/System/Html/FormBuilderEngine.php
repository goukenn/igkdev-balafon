<?php
// @file: IGKFormBuilderEngine.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use IGK\IFormBuilderEngine;

/**
* auto generate doc.
* @package IGK\System\Html
*/
class FormBuilderEngine implements IFormBuilderEngine{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $frm;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $group;

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $arguments
    */

    public function __call($n, $arguments){
        if((strlen($n) > 3) && (substr($n, 0, 3) == "add")){
            $view=$this->getView();
            call_user_func_array(array($view, $n), $arguments);
        }
        if(strtolower($n) == "setfrm"){        }
        return $this;
    }

    /**
    * .ctr
    * @param mixed $frm
    */
    public function __construct($frm){
        $this->setView($frm);
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */

    public function __get($n){
        if(strtolower($n) == "frm"){
            return $this->frm;
        }
        return null;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */

    public function __set($n, $v){
        if((strtolower($n) == "frm") && ($v != null)){
            $this->frm=$v;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $c
    * @param mixed $entries
    * @param null|mixed $filter
    * @param null|mixed $id
    */

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

    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $type
    * @param null|mixed $text
    */

    public function addButton($id, $type='submit', $text=null){
        $this->getView()->addButton($id, $type)->Content=$text ?? __('btn.'.$id);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $value
    * @param null|mixed $attribs
    */

    public function addCheckbox($id, $value=null, $attribs=null){
        extract(igk_html_extract_id($id));
        $i=$this->addControl($id, "checkbox", null, array("value"=>$value));
        if($attribs && isset($attribs["text"])){
            $span=$this->getView()->add("span");
            $span->Content=$attribs["text"];
        }
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $type
    * @param null|mixed $style
    * @param null|mixed $attribs
    */

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

    /**
    * auto generate doc.
    */

    public function addGroup(){
        $g=$this->frm->div();
        $g["class"]="igk-form-group";
        $this->group=$g;
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $class
    * @param null|mixed $text
    */

    public function addLabel($id, $class=null, $text=null){
        extract(igk_html_extract_id($id));
        $view=$this->getView();
        $lb=$view->add("label");
        $lb["for"]=$id;
        $lb->Content=isset($text) ? $text: (isset($label) ? $label: __("lb.".$id));
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $value
    * @param mixed $type
    * @param null|mixed $style
    */

    public function addLabelControl($id, $value=null, $type='text', $style=null){
        extract(igk_html_extract_id($id));
        $__rv=get_defined_vars();
        $this->addLabel($__rv);
        $this->addControl($__rv, $type, $value, $style);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $entries
    * @param null|mixed $filter
    */

    public function addLabelSelect($id, $entries, $filter=null){
        extract(igk_html_extract_id($id));
        $this->addLabel(get_defined_vars());
        $this->addSelect($id, $entries, $filter);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $value
    */

    public function addLabelTextarea($id, $value=null){
        extract(igk_html_extract_id($id));
        $this->addLabel(get_defined_vars());
        $this->addTextarea($id, $value);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $callback
    * @param mixed $tag
    */

    public function addObData($callback, $tag='div'){
        $this->getView()->addObData($callback, $tag);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $value
    * @param null|mixed $attribs
    */

    public function addRadioButton($id, $value=null, $attribs=null){
        extract(igk_html_extract_id($id));
        $this->addControl($id, "checkbox", null, array("value"=>$value));
        if($attribs && isset($attribs["text"])){
            $span=$this->getView()->add("span");
            $span->Content=$attribs["text"];
        }
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $entries
    * @param null|mixed $filter
    */

    public function addSelect($id, $entries, $filter=null){
        extract(igk_html_extract_id($id));
        $c=$this->getView()->addSelect($id);
        $c["class"]="igk-form-control -clselect";
        if($entries){
            $this->_initEntries($c, $entries, $filter);
        }
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $value
    */

    public function addTextarea($id, $value=null){
        extract(igk_html_extract_id($id));
        $a=$this->getView()->addTextarea($id);
        $a->setClass("igk-form-control textarea")->Content=$value == null ? igk_getr($id, $value): $value;
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param null|mixed $value
    * @param null|mixed $attribs
    */

    public function addTextfield($id, $value=null, $attribs=null){
        $this->addLabelControl($id);
    }

    /**
    * auto generate doc.
    */

    public function getLastChild(){
        $view=$this->getView();
        if($view && $view->ChildCount > 0){
            return $view->Childs[$view->ChildCount-1];
        }
        igk_die("failed");
        return null;
    }

    /**
    * auto generate doc.
    */

    public function getView(){
        $c=null;
        if($this->group){
            $c=$this->group;
        }
        else
            $c=$this->frm;
        return $c;
    }

    /**
    * auto generate doc.
    * @param mixed $frm
    */

    public function setView($frm){
        if(!is_object($frm))
            igk_die("engine host required");
        $this->frm=$frm;
    }
}