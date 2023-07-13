<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FormBuilder.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlCssClassValueAttribute;
use IGK\System\Html\Dom\HtmlItemBase;
use IGKEvents;

use function igk_resources_gets as __;

/**
 * default form builder
 * @package IGK\System\Html
 */
class FormBuilder
{
    var $datasource;
    static $ResolvType = [
        "number" => "text",
        "tel"=>"text",
        "float" => "text",
        "int" => "text",
        "email" => "email",
        "password" => "password",
        "text" => "text",
        "date" => "date",
        "json" => "text",
        "radio" => "radio",
        "checkbox" => "checkbox",
        "file" => "file",
        "hidden"=>"hidden"
    ];
    private static $ResolvClass = [
        "float" => "igk-form-control number",
        "double" => "igk-form-control number",
        "number"=>'igk-form-control number',
        "int" => "igk-form-control integer",
        "text"=>'igk-form-control text',
        "mail"=>'igk-form-control mail',
        "url"=>'igk-form-control url',
        "password"=>'igk-form-control password',
        "email"=>'igk-form-control email'
    ];
    public function build($formFields, $render = 0, $engine = null, $tag = "div")
    {
        $o = "";
        $clprop = new HtmlCssClassValueAttribute();
        $get_attr_key = function ($v) {
            $key = null;
            foreach (["attrs", "attribs", "attributes"] as $m) {
                if (isset($v[$m])) {
                    $key = $m;
                    break;
                }
            }
            return $key;
        };
        if (empty($tag)) {
            $tag = "div";
        }

        $clprop->add("data");

        $load_attr = function ($v, &$o) use ($get_attr_key,  $clprop) {
            $clprop->clear();
            $key = $get_attr_key($v);
            $v_def_form_control = igk_environment()->get("css/default/controlstyle", "igk-form-control form-control");
            if ($key === null) {
                //default engine form control
                $e = igk_get_selected_builder_engine();
                if ($e) {
                    $o .= $e->initAttributes($key, $v, $clprop);
                } else {
                    $clprop->setClasses($v_def_form_control);
                }
                if (!empty($defclass = $clprop->getValue())) {
                    $o .= "class=\"" . $defclass . "\" ";
                }
                return;
            } 
            $clprop->setClasses($v_def_form_control); 
            foreach ($v[$key] as $k => $v) {
                if ($k == 'class') {
                    $clprop->setClasses($v);
                } else {
                    $o .= $k . "=\"" . $v . "\" ";
                }
            }
            if (!empty($defclass = $clprop->getValue())) { 
                $o .=  "class=\"" . $defclass . "\" ";
            }
        };
        $bindValue = function (&$o, &$fieldset, $k, $v) use ($get_attr_key, $load_attr, $tag) {
            if (!is_array($v)) {
                $v = [];
            }
            $attr_key = $get_attr_key($v);
            $ResolvClass = self::$ResolvClass;
            $ResolvType = self::$ResolvType;

            $_value = is_array($v) && key_exists("value", $v) ? $v["value"] : "";
            if ($attr_key) {
                if (isset($v[$attr_key]["value"])) {
                    $_value = $v[$attr_key]["value"];
                    unset($v[$attr_key]["value"]);
                }
            }
            if (empty($_value) && $this->datasource && key_exists($k, $this->datasource)) {
                $_value = igk_getv($this->datasource, $k);
            }
            $_type = strtolower(isset($v["type"]) ? $v["type"] : "text");
            $_allow_empty = isset($v["allow_empty"]) ? $v["allow_empty"] : "";
            $_empty_value = isset($v["empty_value"]) ? $v["empty_value"] : "0";
            if ($_type == "fieldset") {
                if ($fieldset) {
                    $o .= "</fieldset>";
                    $fieldset = 0;
                }
                $o .= "<fieldset ";
                $load_attr($v, $o);
                $o .= ">";
                if (isset($v["legend"])) {
                    $o .= "<legend>" . $v["legend"] . "</legend>";
                }
                $fieldset = 1;
                return;
            }
            if ($_type === "efieldset") {
                if ($fieldset) {
                    $o .= "</fieldset>";
                    $fieldset = 0;
                }
                return;
            }
            $_id = "";
            if (isset($v["id"])) {
                $_id = 'id="' . $v["id"] . '" ';
            }
            $_name = "";
            if (isset($v["name"])) {
                $_name = "name=\"" . $v["name"] . "\" "; // name=\"{$k}\" ";
            } else {
                $_name = "name=\"{$k}\" ";
            }

            $_is_required = isset($v["required"]) ? $v["required"] : 0;
            $t_id = igk_getv($v, "id", $k);
            $label_text = ucfirst(igk_getv($v, "label_text", __($k)));
            if (!$this->isHtmlType($_type) && is_subclass_of($_type, FormBuilderItemAbstractType::class)){
                $v_ctype = new $_type();
                $v_ctype->setName($k);
                $v_ctype->setAttributes(array_merge($v, [
                    'label_text'=>$label_text
                ]));
                $v_ctype->setId($t_id);
                $o.= $v_ctype->render();
                return;
            }
            $_is_div = !preg_match("/(hidden|fieldset|button|submit|reset|datalist)/", $_type);
            $class_style = 'igk-form-group '.$_type;
            if ($_is_div) {
                $o .= "<" . $tag . " ";
                if ($_is_required) {
                    $class_style .= ' required';
                }
                $o .= "class=\"$class_style\" ";
                $o = rtrim($o) . ">";
            }
          
            if (!preg_match("/(hidden|fieldset|button|submit|reset|datalist)/", $_type)) {
                $g = HtmlUtils::GetFilteredAttributeString("label", [
                    'class'=>"igk-form-label"
                ]);               
                $o .= "<label for='{$t_id}'$g>" .$label_text . "</label>";
            }
            switch ($_type) {
                case "fieldset":
                    break;
                case "textarea":
                    $o .= "<textarea {$_name}{$_id}";
                    if (isset($v["placeholder"])) {
                        $o .= "placeholder=\"{$v["placeholder"]}\" ";
                    }
                    $load_attr($v, $o);
                    if ($_is_required) {
                        $o .= "required=\"true\" ";
                    }
                    $o = rtrim($o). ">{$_value}</textarea>"; 
                    break;
                case "radiogroup":
                    $o .= '<' . $tag . ' style="display:inline-block;">';
                    foreach ($v["data"] as $kk => $vv) {                       
                        $o .= '<span >' . __($kk) . '</span><input type="radio" name="' . $k . '"' . $_id . ' value="' . $vv . '" />';
                    }
                    $o .= "</{$tag}>";
                    break;
                            
                case "datalist":
                    if (empty($_id)) {
                        $_id = " id=\"{$k}\"";
                    }
                    $o .= "<datalist" . $_id;
                    $load_attr($v, $o);
                    $o .= ">";
                    if (isset($v["data"]) && is_array($_tab = $v["data"])) {
                        foreach ($_tab as $row) {
                            $o .= "<option ";
                            $o .= "value=\"{$row['i']}\" ";
                            $o .= ">";
                            $o .= isset($row["t"]) ? __($row["t"]) : "";
                            $o .= "</option>";
                        }
                    }
                    $o .= "</datalist>";
                    break;
                case "select":
                    $k_data = "";
                    $bas = isset($v["selected"]) ? $v["selected"] : null;
                    if (isset($v["data"]) && is_string($m_data = $v["data"])) {
                        $k_data = "data=\"" . $m_data . "\" ";
                    }
                    $_id = ' id="'.$t_id.'"';
                    // if ($bas){
                    //     $k_data.= "selected=\"{$bas}\" ";
                    // }
                    $o .= "<select {$_name}" . $_id . $k_data;
                    $load_attr($v, $o);
                    $o .= " >";
                    if ($_allow_empty) {
                        $o .= "<option ";
                        $o .= "value=\"{$_empty_value}\" ></option>";
                    }
                    if (isset($v["data"]) && is_array($_tab = $v["data"])) {

                        foreach ($_tab as $row) {
                            $o .= "<option ";
                            $o .= "value=\"{$row['i']}\" ";
                            if ((isset($bas) && ($bas == $row['i'])) || (igk_getv($row, 'selected'))) {
                                $o .= "selected ";
                                // igk_wln_e("load.....".$bas, $_name, $row['t']);
                            }
                            $o .= ">";
                            $o .= $row["t"];
                            $o .= "</option>";
                        }
                    }
                    $o .= "</select>";
                    break;

                case "text":
                case "hidden":
                case "password":
                default:
                    if (empty($_id)){
                        $v['id'] = $t_id;
                    }
                    // $_vt = "";
                    if (!empty($_value) || ($_value == "0")){
                        $v['value']=$_value;
                    }
                    // $_vt = "value=\"{$_value}\"";
                    $_otype = igk_getv($ResolvType, $_type, "text");
                    $def_type = igk_getv($ResolvClass, $_type, $_type);
                    $o .= "<input"; //type=\"{$_otype}\" {$_vt} {$_name}{$_id} ";
                    $keys = ['id', 'value', 'maxlength','pattern', 'placeholder'];
                    if ($no_place_holder = in_array($_type, ['checkbox', 'radio'])){
                        array_pop($keys);
                        $keys[] = 'checked';
                    }
                    $tattrib = ["name"=>$k]; 
                    foreach($keys as $kk){
                        $tattrib[$kk] = igk_getv($v, $kk);
                    }
                    if (!$no_place_holder && empty($tattrib['placeholder'])){
                        //igk_wln_e(get_defined_vars());
                        $tattrib['placeholder'] = __($k);
                    }
                    // if (isset($v["maxlength"])) {
                    //     $o .= "maxlength=\"{$v["maxlength"]}\" ";
                    // }
                    // if (isset($v["pattern"])) {
                    //     $o .= "pattern=\"{$v["pattern"]}\" ";
                    // }
                   
                    if (isset($v["attribs"]))
                        $tattrib["class"] = igk_getv($v["attribs"], "class") . " +" . $def_type;
                    else {
                        $tattrib["class"] = $def_type;
                    }
                    // + | -------------------------------------------------
                    // + | filter attribs
                    // + |
                    unset($v["attribs"]["class"]);
                    if ($p = igk_getv($v, 'attribs')){
                        $tattrib = array_merge($tattrib, $p ?? [] );                         
                    }

                    $jp = [                        
                        "type"=> $_otype,
                        "id"=>$v["id"],
                        "value"=>$_value,
                        // "class"=>new HtmlCssClassValueAttribute(),
                    ] + $tattrib;
                    $attrib = new HtmlFilterAttributeArray($jp);
                    //$attrib["class"] = $v["attribs"]["class"];
                    if ($_is_required) {
                        $attrib["required"] = 1;
                    }
                    $attrib = HtmlUtils::PrefilterAttribute("input", $attrib);
                    $o .=' '.HtmlRenderer::GetAttributeArrayToString($attrib);                  
                    $o .= "/>";

                    if (isset($v["tips"])) {
                        $o .= '<div class="tips">' . $v["tips"] . '</div>';
                    }
                    break;
            }
            if ($_is_div) {
                $o .= "</{$tag}>";
            }
        };
        $fieldset = 0;
        foreach ($formFields as $k => $v) {

            if (is_integer($k)) {
                if ($v == "-") {
                    // add separator
                    $o .= "<div class=\"igk-separator\"></div>";
                    continue;
                }
                if (is_string($v)) {
                    $k = $v;
                }
                if (is_object($v)) {
                    if ($v instanceof HtmlItemBase) {
                        $o .= $v->render();
                        continue;
                    }
                    igk_wln($k, $v);
                    igk_die("object not allowed");
                }
            }
            if (($cpos = strrpos($k, "[]")) !== false) {
                // + | --------------------------------------------------------------------
                // + | FORM FIELD DEFINITION
                // + |                
                $name = substr($k, 0, $cpos);
                if (is_array($v)){
                    $ct = count($v);
                    $jc = 0;
                    for ($i = 0; $i < $ct; $i++) {
                        $b = $v[$i];
                        if ($b instanceof FormFieldAttribute){
                            $b->attribs;
                            continue;
                        }
                        $b['id']= $name.str_pad($jc.'', 2, STR_PAD_LEFT, '0');
                        $b["name"] = $k;                       
                        $bindValue($o, $fieldset, $name, $b);
                        $jc++;
                    }
                } else if ($v instanceof FormFieldAttribute){
                    $data = $v->attribs;
                    $v_fname = igk_getv($data, 'name', $k);
                    $v_ftype = $data['type'];
                    $data = $data['data'];
                    if ($data){
                        $c = 0;
                        foreach($data as $c_data){
                            $c_data['name'] = $v_fname;
                            $c_data['type'] = $v_ftype;
                            if (empty($c_data['id']))
                                $c_data['id'] =  $name.'-'.$c;
                            $bindValue($o, $fieldset, $v_fname, $c_data);
                            $c++;
                        }
                    }
                }
                continue;
            }
            $bindValue($o, $fieldset, $k, $v);
        }
        if ($fieldset) {
            $o .= "</fieldset>";
        }
        if ($render) {
            echo $o;
        }
        return $o;
    }
    /**
     * check whether a string type is html type
     * @param string $type 
     * @return int|false 
     */
    protected function isHtmlType(string $type){
        return preg_match("/(text|checkbox|password|datetime|email|hidden|fieldset|button|submit|reset|datalist|select|number)/", $type);
    }
}
