<?php
// @file: IGKFormBuilderEngine.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\IFormBuilderEngine;
/**
* Igkform builder engine.
*/
class IGKFormBuilderEngine implements IFormBuilderEngine{
    /**
    * Property: frm.
    * @var mixed
    */
    protected $frm;
    /**
    * Property: group.
    * @var mixed
    */
    var $group;
    /**
     * Handles dynamic method calls, dispatching "add*" calls to the current view.
     *
     * @param string $n         The method name being called.
     * @param array  $arguments The arguments passed to the method.
     * @return static
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
     * Constructor.
     *
     * @param mixed $frm The form host object to use as the view.
     */
    public function __construct($frm){
        $this->setView($frm);
    }
    /**
     * Returns the value of a magic property by name.
     *
     * @param string $n The property name.
     * @return mixed
     */
    public function __get($n){
        if(strtolower($n) == "frm"){
            return $this->frm;
        }
        return null;
    }
    /**
     * Sets a magic property value by name.
     *
     * @param string $n The property name.
     * @param mixed  $v The value to assign.
     */
    public function __set($n, $v){
        if((strtolower($n) == "frm") && ($v != null)){
            $this->frm=$v;
        }
    }
    /**
     * Populates a select element with option entries from the given data source.
     *
     * @param mixed      $c       The select element to populate.
     * @param mixed      $entries The data entries (array or object with Rows).
     * @param array|null $filter  Optional filter configuration for value/key mapping.
     * @param mixed|null $id      Optional identifier used for the selected value lookup.
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
     * Adds a button element to the current view.
     *
     * @param string      $id   The button identifier.
     * @param string      $type The button type (default: 'submit').
     * @param string|null $text The button label text.
     * @return static
     */
    public function addButton($id, $type='submit', $text=null){
        $this->getView()->addButton($id, $type)->Content=$text ?? __('btn.'.$id);
        return $this;
    }
    /**
     * Adds a checkbox input and optional label span to the current view.
     *
     * @param string     $id      The checkbox identifier.
     * @param mixed|null $value   The checkbox value.
     * @param array|null $attribs Optional attributes, including 'text' for a label span.
     * @return static
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
     * Adds an input control of the specified type to the current view.
     *
     * @param string      $id      The control identifier.
     * @param string      $type    The input type (default: 'text').
     * @param mixed|null  $style   Optional style specification.
     * @param array|null  $attribs Optional HTML attributes to set on the input.
     * @return static
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
     * Adds a form group div to the form and sets it as the current view context.
     *
     * @return static
     */
    public function addGroup(){
        $g=$this->frm->div();
        $g["class"]="igk-form-group";
        $this->group=$g;
        return $this;
    }
    /**
     * Adds a label element associated with the given field identifier.
     *
     * @param string      $id    The field identifier the label is for.
     * @param string|null $class Optional CSS class for the label.
     * @param string|null $text  Optional label text; falls back to translation.
     * @return static
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
     * Adds a label and a paired input control to the current view.
     *
     * @param string     $id    The field identifier.
     * @param mixed|null $value Optional initial value for the control.
     * @param string     $type  The input type (default: 'text').
     * @param mixed|null $style Optional style specification.
     * @return static
     */
    public function addLabelControl($id, $value=null, $type='text', $style=null){
        extract(igk_html_extract_id($id));
        $__rv=get_defined_vars();
        $this->addLabel($__rv);
        $this->addControl($__rv, $type, $value, $style);
        return $this;
    }
    /**
     * Adds a label and a paired select element populated with the given entries.
     *
     * @param string     $id      The field identifier.
     * @param mixed      $entries The data entries for the select options.
     * @param array|null $filter  Optional filter configuration for value/key mapping.
     * @return static
     */
    public function addLabelSelect($id, $entries, $filter=null){
        extract(igk_html_extract_id($id));
        $this->addLabel(get_defined_vars());
        $this->addSelect($id, $entries, $filter);
        return $this;
    }
    /**
     * Adds a label and a paired textarea element to the current view.
     *
     * @param string     $id    The field identifier.
     * @param mixed|null $value Optional initial value for the textarea.
     * @return static
     */
    public function addLabelTextarea($id, $value=null){
        extract(igk_html_extract_id($id));
        $this->addLabel(get_defined_vars());
        $this->addTextarea($id, $value);
        return $this;
    }
    /**
     * Adds a data-bound observable element to the current view.
     *
     * @param callable $callback The callback used to bind the element's data.
     * @param string   $tag      The HTML tag for the element (default: 'div').
     * @return static
     */
    public function addObData($callback, $tag='div'){
        $this->getView()->addObData($callback, $tag);
        return $this;
    }
    /**
     * Adds a radio button input and optional label span to the current view.
     *
     * @param string     $id      The radio button identifier.
     * @param mixed|null $value   The radio button value.
     * @param array|null $attribs Optional attributes, including 'text' for a label span.
     * @return static
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
     * Adds a select element populated with the given entries to the current view.
     *
     * @param string     $id      The select field identifier.
     * @param mixed      $entries The data entries for the select options.
     * @param array|null $filter  Optional filter configuration for value/key mapping.
     * @return static
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
     * Adds a textarea element to the current view with an optional initial value.
     *
     * @param string     $id    The textarea identifier.
     * @param mixed|null $value Optional initial value for the textarea.
     * @return static
     */
    public function addTextarea($id, $value=null){
        extract(igk_html_extract_id($id));
        $a=$this->getView()->addTextarea($id);
        $a->setClass("igk-form-control textarea")->Content=$value == null ? igk_getr($id, $value): $value;
        return $this;
    }
    /**
     * Adds a text field (label + control) to the current view.
     *
     * @param string     $id      The field identifier.
     * @param mixed|null $value   Optional initial value.
     * @param array|null $attribs Optional HTML attributes.
     */
    public function addTextfield($id, $value=null, $attribs=null){
        $this->addLabelControl($id);
    }
    /**
     * Returns the last child element of the current view.
     *
     * @return mixed|null
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
     * Returns the active view context (group div if set, otherwise the main form).
     *
     * @return mixed|null
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
     * Sets the form host object as the active view.
     *
     * @param mixed $frm The form host object.
     */
    public function setView($frm){
        if(!is_object($frm))
            igk_die("engine host required");
        $this->frm=$frm;
    }
}