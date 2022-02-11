<?php
namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlCssClassValueAttribute;
use function igk_resources_gets as __;


class FormBuilder{
    var $datasource;
    static $ResolvType = [
        "number"=>"text",
        "float"=>"text",
        "int"=>"text",
        "email"=>"email",
        "password"=>"password",
        "text"=>"text",
        "date"=>"date",
        "json"=>"text",
        "radio"=>"radio",
        "checkbox"=>"checkbox",
    ];
    private static $ResolvClass = [
        "float"=>"number",
        "double"=>"number",
        "int"=>"integer"
    ];
    public function build($formFields, $render=0, $engine=null, $tag="div"){
        $o = "";
        $clprop = new HtmlCssClassValueAttribute();
        $get_attr_key = function($v){
            $key = null;
            foreach(["attrs", "attribs","attributes"] as $m){
                if (isset($v[$m])){
                    $key = $m;
                    break;
                }
            }
            return $key;
        };
        if (empty($tag)){
            $tag = "div";
        } 
    
        $clprop->add("data");
       
        $load_attr=function($v, & $o) use( $get_attr_key ,  $clprop ) {
            $clprop->clear();
            $key = $get_attr_key($v);
            $v_def_form_control = igk_environment()->get("css/default/controlstyle", "igk-form-control form-control");
            if($key === null){
                //default engine form control
                $e = igk_get_selected_builder_engine();
                if ($e){
                    $o .= $e->initAttributes($key, $v, $clprop);
                }else { 
                    $clprop->setClasses($v_def_form_control);
                }
                if (!empty($defclass = $clprop->getValue())){  
                    $o .=  " class=\"".$defclass."\"";
                }
                return;
            } 
           // if (!isset($v[$key]["class"])){
                 $clprop->setClasses($v_def_form_control);
                // $o .= $v_def_form_control;
           // }
            foreach($v[$key] as $k=>$v){
                if ($k=="class"){
                    $clprop->setClasses($v);
                }else{
                    $o .= " ".$k."=\"".$v."\"";
                }
            }
            if (!empty($defclass = $clprop->getValue())){  
                // igk_wln_e($defclass);
                $o .=  " class=\"".$defclass."\"";
            }
        };
        $bindValue = function(&$o, & $fieldset, $k, $v) use ($get_attr_key, $load_attr, $tag){
            if (!is_array($v)){
                $v = []; 
            }
            $attr_key = $get_attr_key($v);
            $ResolvClass = self::$ResolvClass;
            $ResolvType = self::$ResolvType;
           
            $_value= is_array($v) && key_exists("value", $v) ? $v["value"]: "";
            if ($attr_key){
                if (isset($v[$attr_key]["value"])){
                    $_value = $v[$attr_key]["value"];
                    unset( $v[$attr_key]["value"]);
                }
            }
            if (empty($_value) && $this->datasource && key_exists($k, $this->datasource)){
                $_value = igk_getv($this->datasource, $k); 
            }
            $_type=strtolower(isset($v["type"]) ? $v["type"]: "text");    
            $_allow_empty=isset($v["allow_empty"]) ? $v["allow_empty"]: "";
            $_empty_value=isset($v["empty_value"]) ? $v["empty_value"]: "0";
            if($_type == "fieldset"){
                if($fieldset){
                    $o .= "</fieldset>";
                }
                $o .= "<fieldset";
                $load_attr($v, $o);
                $o .= ">";
                if(isset($v["legend"])){
                    $o .= "<legend>".$v["legend"]."</legend>";
                }
                $fieldset=1;
                return;
            }
            if ($_type ==="efieldset"){
                if ($fieldset){
                    $o.="</fieldset>";
                    $fieldsett = 0;
                }
                return;
            }
            $_id="";
            if(isset($v["id"])){
                $_id=' id="'.$v["id"].'"';
            }
            $_name="";
            if (isset($v["name"])){
                $_name = "name=\"". $v["name"] ."\" " ; // name=\"{$k}\" ";
            }else {
                $_name = "name=\"{$k}\" ";
            } 
            $_is_div = !preg_match("/(hidden|fieldset|button|submit|reset|datalist)/", $_type);
            $_is_required = isset($v["required"]) ? $v["required"]: 0;
            if($_is_div){
                $o .= "<".$tag;
                if($_is_required){
                    $o.= " class=\"required\"";
                }
                $o .= ">";
            }
            if(!preg_match("/(hidden|fieldset|button|submit|reset|datalist)/", $_type)){
                $o .= "<label for='{$k}'>".ucfirst(igk_getv($v, "label_text", __($k)))."</label>";
            }
            switch($_type){
                case "fieldset":
                break;
                case "textarea":
                $o .= "<textarea {$_name} {$_id}";
                if(isset($v["placeholder"])){
                    $o .= " placeholder=\"{$v["placeholder"]}\" ";
                }  
                $load_attr($v, $o);
                if($_is_required){
                    $o.= " required=\"true\"";
                }
                $o .= ">{$_value}</textarea>";
            
                break;
                case "radiogroup":
                $o .= '<'.$tag.' style="display:inline-block;">';
                foreach($v["data"] as $kk=>$vv){
                    $o .= '<span >'.__($kk).'</span><input type="radio" name="'.$k.'"'.$_id.' value="'.$vv.'" />';
                }
                $o .= "</{$tag}>";
                break;
                case "datalist":
                    if (empty($_id)){
                        $_id = " id=\"{$k}\"";
                    }
                $o .= "<datalist".$_id;
                $load_attr($v, $o);
                $o .= ">";
                if(isset($v["data"]) && is_array($_tab=$v["data"])){
                    foreach($_tab as $row){
                        $o .= "<option ";
                        $o .= "value=\"{$row['i']}\" ";
                        $o .= ">";
                        $o .= isset($row["t"]) ? __($row["t"]): "";
                        $o .= "</option>";
                    }
                }
                $o .= "</datalist>";
                break;
                case "select":
                $k_data="";
                $bas=isset($v["selected"]) ? $v["selected"]: null;
                if(isset($v["data"]) && is_string($m_data=$v["data"])){
                    $k_data="data=\"".$m_data."\" ";
                }
                // if ($bas){
                //     $k_data.= "selected=\"{$bas}\" ";
                // }
                $o .= "<select {$_name}".$_id.$k_data;
                $load_attr($v, $o);
                $o.= " >";
                if($_allow_empty){
                    $o .= "<option ";
                    $o .= "value=\"{$_empty_value}\" ></option>";
                }
                if(isset($v["data"]) && is_array($_tab=$v["data"])){
                    foreach($_tab as $row){
                        $o .= "<option ";
                        $o .= "value=\"{$row['i']}\" ";
                        if( (isset($bas) && ($bas == $row['i'])) || (igk_getv($row, 'selected'))){
                            $o .= "selected";
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
                if(empty($_id))
                    $_id=' id="'.$k.'"';
                $_vt ="";
                if (!empty($_value) || ($_value=="0"))
                    $_vt="value=\"{$_value}\"";
    
                $_otype = igk_getv($ResolvType, $_type, "text");
                $def_type = igk_getv($ResolvClass, $_type, $_type);    
                $o .= "<input type=\"{$_otype}\" {$_vt} {$_name}{$_id} ";
                if(isset($v["maxlength"])){
                    $o .= "maxlength=\"{$v["maxlength"]}\" ";
                }
                if(isset($v["pattern"])){
                    $o .= "pattern=\"{$v["pattern"]}\" ";
                }
                if(isset($v["placeholder"])){
                    $o .= "placeholder=\"{$v["placeholder"]}\" ";
                }     
                if (isset($v["attribs"]))
                    $v["attribs"]["class"] = igk_getv ($v["attribs"], "class"). " +".$def_type;
                else {
                    $v["attribs"]["class"] = $def_type;
                }
                $load_attr($v, $o);

                if ($_is_required){
                    $o.= 'required="1" ';
                }

                $o .= "/>";

                if (isset($v["tips"])){
                    $o.= '<div class="tips">'.$v["tips"].'</div>';
                } 
                break;
            }
            if($_is_div){
                $o .= "</{$tag}>";
            }
        };
    
    
    
        $fieldset=0;
        foreach($formFields as $k=>$v){
            
            if (is_integer($k)){
                if ($v=="-"){
                    // add separator
                    $o .= "<div class=\"igk-separator\"></div>";
                    continue;
                }
                if (is_string($v)){
                    $k = $v;
                }
            }
            if ( ($cpos = strrpos($k, "[]")) !== false){   
                $name = substr($k, 0, $cpos);
                $ct = count($v);
                for ($i = 0; $i < $ct ; $i++){
                    $b = $v[$i];
                    $b["name"]= $k;  
                    $bindValue($o, $fieldset, $name, $b );
                }
                continue;
            }
            $bindValue($o, $fieldset, $k, $v );
        }
        if($fieldset){
            $o .= "</fieldset>";
        }
        if($render){
            echo $o;
        }
        return $o;
    }
}