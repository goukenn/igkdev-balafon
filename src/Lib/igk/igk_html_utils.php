<?php
// author: C.A.D. BONDJE DOUE
// licence: IGKDEV - Balafon @ 2019

use IGK\Resources\IGKLangKey;
use IGK\Resources\R;

use function igk_resources_gets as __;

///<summary>pre render argument</summary>
function igk_html_pre(){
    echo "<pre>";
    foreach(func_get_args() as $k){
        print_r($k);
    }
    echo "</pre>";
}

function igk_html_reg_class($name, $class){
    $B = igk_environment()->get("html://class");
    if (!$B){
        $B = [];
    }
    $B[$name] = $class;
    igk_set_env("html://class", $B);
    return $B;
}
function igk_html_reg_method($name, $funcName, $callable){
    $key = "html://methods";
    $B = igk_environment()->get($key);
    if (!$B){
        $B = [];
    }
    $B[$name][$funcName] = $callable;
    igk_set_env($key, $B);
    return $B;
}
function igk_html_get_method($name, $method){
    $c = igk_environment()->get("html://methods"); 
    if (isset($c[$name])){
        return igk_getv($c[$name], $method);
    }
    return null;
}
function igk_html_get_class_callable($name, $method){
 
    $c = igk_environment()->get("html://class"); 
    if (isset($c[$name])){

        $c = igk_getv($c, $name);//, $method);
        if (!isset($instance[$c])){
        
        }
        $g = igk_environment()->GetClassInstance($c);
        
        if ($g && method_exists($g, $method)){
            return array($g, $method);
        }
    }
    return null;
}


function igk_html_validate(){
    //TODO: implement validation error
    throw new IGKException("Not Implement");
}
function igk_html_print_r($args){
    igk_wl("<pre>");
    print_r($args);
    igk_wln("</pre>");
}
/**
* @return mixed
*/
function igk_html_validate_error(){
    //TODO: implement validation error
    throw new IGKException("Not Implement");
}

///<summary></summary>
///<param name="t"></param>
///<param name="ctrl"></param>
/**
* 
* @param mixed $t
* @param mixed
*/
function igk_html_add_good_uri($t, $ctrl){
    if(($redirect=base64_decode(igk_getr("q")))){
        $redirect=igk_io_baseuri($redirect);
    }
    else if(!($redirect=igk_getr("goodUri"))){
        return;
    }
    $t->addInput("goodUri", "hidden", $redirect);
}
///<summary></summary>
///<param name="n"></param>
/**
* 
* @param mixed $n
*/
function igk_html_attribvalue($n){
    if(!$n){
        if(is_numeric($n)){
            $n="0";
        }
    }
    return "\"".$n."\"";
}
///<summary></summary>
///<param name="node"></param>
///<param name="title"></param>
/**
* 
* @param mixed $node
* @param mixed
*/
function igk_html_add_title($node, $title){
    if($node == null)
        return null;
    $d=$node->addDiv();
    $d["class"]="igk-title";
    $d->Content=__($title);
    return $d;
}
///<summary>get paget title function</summary>
///<param name="ctrl">the controller the application title. mixed string|control implement AppTitle property</summary>
///<param name="title" >the text title</param>
/**
* get paget title function
* @param mixed ctrl the controller the application title. mixed string|control implement AppTitle property
* @param mixed title the text title
*/
function igk_html_app_page_title($ctrl, $title){
    return IGKLangKey::GetValueKeys(IGKConstants::STR_PAGE_TITLE, array(
            __($title),
            is_string($ctrl) ? $ctrl: $ctrl->AppTitle
        ));
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="title"></param>
/**
* 
* @param mixed $ctrl
* @param mixed $title
*/
function igk_html_apptitle($ctrl, $title){
    return  __("title.app_2", $title, $ctrl->getAppTitle());
}
///<summary></summary>
///<param name="tab"></param>
///<param name="headercallback" default="null"></param>
/**
* 
* @param mixed $tab
* @param mixed $headercallback the default value is null
*/
function igk_html_array_table($tab, $headercallback=null){
    $n=igk_createnode("table");
    if($headercallback)
        $headercallback($n->add("tr"));
    foreach($tab as $k=>$v){
        $tr=$n->add("tr");
        $tr->add("td")->Content=$k;
        $tr->add("td")->Content=$v;
    }
    return $n;
}
///<summary>utility to build form data</summary>
///<code type="php">igk_html_build_form($dv, array(
///IGK_FD_NAME=>array("require"=>1),
///"clDisplayName"=>array("require"=>1),
///"clVersion"=>array("require"=>1, "attribs"=>array("value"=>"1.0"))
///), "div");
///</code>
/**
* utility to build form data
*/
function igk_html_build_form($t, $data, $defaultTarget="li"){
    foreach($data as $id=>$k){
        $type=strtolower(igk_getv($k, "type", "text"));
        $required=igk_getv($k, "require", 0);
        $args=igk_getv($k, "attribs", null);
        $title=igk_getv($k, "title", null);
        $li=$t->add($defaultTarget);
        $a=null;
        if($type !== "hidden"){
            $lb=$li->add("label", array("for"=>$id));
            $lb->Content=$title ?? R::ngets("lb.".$id)->getValue();
            if($required){
                $lb->setClass("clrequired");
            }
        }
        switch($type){
            case "select":
            $d=igk_getv($args, "options");
            if(!is_array($d)){
                igk_die("<b>options </b> argument is required for select input. attribs=&gt;options");
            }
            $sl=$li->addSelect($id);
            $selected=igk_getv($args, "options-selected");
            $selectattrib=igk_getv($args, "options-select-attribs");
            unset($args["options"]);
            unset($args["options-selected"]);
            unset($args["options-select-attribs"]);
            $sl["class"]="+cltext";
            igk_html_build_select_option($sl, $d, $selectattrib, $selected);
            break;
            case "textarea":
            $a=$li->addTextArea($id);
            $a->Content=$args;
            break;
            case "radio":
            case "checkbox":
            $a=$li->addInput($id, $type);
            if(igk_getr($id)){
                $a["checked"]="true";
            }
            if($args){
                $a->AppendAttributes($args);
            }
            break;
            case "hidden":
            case "text":
            case "password":                
            default:
            $a=$li->addInput($id, $type);
            $a["type"]=strtolower($type);
            if($args){
                $a->AppendAttributes($args);
            }
            break;
        }
        $a["id"]=
        $a["name"]=$id;
        $args=igk_getv($k, 3);
        if($args != null){
            $a->AppendAttributes($args);
        }
    }
}
///<summary>build entry</summary>
/**
* build entry
*/
function igk_html_build_form_array_entry($name, $type, $n, $value=null){    
    $pwd = $name == IGK_FD_PASSWORD;
    switch(strtolower($type)){
        case "text":
        case "string":
        case "varchar":
            $n->addSLabelInput($name, $pwd ? "password": "text", $pwd ? "": $value);
        break;
        case "blob":
            $t=$n->addSLabelTextarea($name, "lb.".$name, array("class"=>"-cltextarea"));
            $t->textarea->Content=$pwd ? "": $value;
        break;
        default: 
            $n->addSLabelInput($name, "text", $value);
        break;
    }
}
///<summary>shortcut to igk_html_load_menu_array. used to build menu</summary>
/**
* shortcut to igk_html_load_menu_array. used to build menu
*/
function igk_html_build_menu($target, $menuTab, $user=null, $ctrl=null, $default="li", $sub="ul"){
    $render=0;
    if($target == null){
        $target=igk_createnode($sub);
        $render=1;
    }
    igk_html_load_menu_array($target, $menuTab, $default, $sub, $user, $ctrl);
    if($render){
        $target->renderAJX();
    }
}


///<summary></summary>
///<param name="target"></param>
///<param name="tab"></param>
///<param name="item" default="li"></param>
///<param name="subnode" default="ul"></param>
///<param name="user" default="null"></param>
///<param name="ctrl" default="null"></param>
///<param name="callback" default="null"></param>
/**
* 
* @param mixed $target
* @param mixed $tab
* @param mixed $item the default value is "li"
* @param mixed $subnode the default value is "ul"
* @param mixed $user the default value is null
* @param mixed $ctrl the default value is null
* @param mixed $callback the default value is null
*/
function igk_html_load_menu_array($target, $tab, $item="li", $subnode="ul", $user=null, $ctrl=null, $callback=null){
    $mi=null;
	if ($user==null)
		$user = igk_app()->session->getUser();
	if ($ctrl == null){
		$ctrl = igk_app()->getBaseCurrentCtrl();
	}

	$_binduri = function($s, $ctrl){
            if($ctrl && !empty($s) && !IGKValidator::IsUri($s)){
                $s=ltrim($s, "/");
                $s=$ctrl->getAppUri($s);
            }
			return $s;
	};


    $sfc=function($tab){
        $o=array();
        $c=0;
        $level=array();
        $roots=0;
        foreach($tab as $k=>$v){
            $st=igk_io_basenamewithoutext($k);
            $v_isr=$st === $k;
            $c=0;
            if($v_isr){
                $c=$roots;
                $roots++;
            }
            else{
                if(isset($level[$st]))
                    $c=$level[$st];
            }
            $obj=(object)array(
                "key"=>$k,
                "index"=>null,
                "level"=>$v_isr ? 0: igk_count(explode(".",
                $st))
            );
            if(is_array($v)){
                $kk=igk_getv($v, "index");
                $obj->index=($kk ? $kk: $c++);
            }
            else{
                $obj->index=$c;
                $c++;
            }
            $o[]=$obj;
            if(!$v_isr)
                $level[$st]=$c;
        }
        return $o;
    };
    $h=$sfc($tab);
    usort($h, function($a, $b){
        if($a->level == $b->level){
            if($a->index == $b->index)
                return strcmp(strtolower($a->key), strtolower($b->key));
            return $a->index < $b->index ? -1: 1;
        }
        return $a->level < $b->level ? -1: 1;
    });
    $root=array();
    $sd=array();
    foreach($h as $obj){
        $s=$tab[$obj->key];
        $k=strtolower($obj->key);
        $pname=igk_io_basenamewithoutext($k);
        $mi=$target;
        $ii=null;
        if(!isset($root[$k])){
            if(isset($sd[$pname])){
                $ii=$sd[$pname];
                if($ii->ul == null){
                    $ii->ul=$ii->li->add($subnode);
                }
                $mi=$ii->ul;
            }
            else{
                if((empty($pname) || !isset($root[$pname]))){
                    $root[$k]=(object)array("li"=>null, "ul"=>null, 'level'=>$obj->level);
                    $sd[$k]=$root[$k];
                }
                else{
                    $ii=$root[$pname];
                    $mi=$ii->li->add($subnode);
                }
            }
            if($mi !== $target){
                $mi["class"]="sub s".$obj->level;
                if($callback)
                    $callback($mi, "subi", $pname);
            }
        }
        else{
            igk_die("10: already define ");
        }
        if(is_array($s)){
            $u= $_binduri(igk_getv($s, "uri"), $ctrl);
            $auth=igk_getv($s, "auth", true);
            $ajx=igk_getv($s, "ajx");
            $lkey=igk_getv($s, "lkey", "menu.".$k);
            $init=igk_getv($s, "init");
            if((false === $auth) || (is_string($auth) && !$user->auth($auth))){
                continue;
            }
            $hi=$mi->add($item);
            if($hi == null){
                igk_die($item." create null");
            }
            if($ajx){
                $hi->addAJXA($u)->Content=R::ngets($lkey);
            }
            else
                $hi->addA($u)->Content=R::ngets($lkey);
            if($init){
                $init($hi);
            }
        }
        else{
            if($mi == null){
                $mi=$target;
            }
            $hi=$mi->add($item);
			$s = $_binduri($s, $ctrl);
            $hi->addA($s)->Content=R::ngets("menu.".$k);
            $sd[$k]->li=$hi;
        }
    }
    $target->roots=$root;
}
///@attributes array of  [allowEmpty, valuekey, displaykey]
///@attr is html attributes
/**
*/
function igk_html_build_select($target, $name, $tab, $selectattributes=null, $selectedvalue=null, $attr=null){
    $sel=$target->addSelect($name);
    if($selectedvalue == null){
        $selectedvalue=igk_getr($name, null);
    }
    igk_html_build_select_option($sel, $tab, $selectattributes, $selectedvalue);
    $sel->AppendAttributes($attr);
    return $sel;
}
///<summary></summary>
/**
* 
*/
function igk_html_build_select_setting(){
    return (object)array(
            "allowEmpty"=>false,
            "keysupport"=>false,
            "valuekey"=>null,
            "displaykey"=>null,
            "resolvtext"=>null
        );
}
///<summary>utility to build table result</summary>
/**
* utility to build table result
*/
function igk_html_build_table($tab, $rows, $headers, $callback=null){
    igk_html_db_build_table_header($tab->add("tr"), $headers, null, $callback);
    foreach($rows as  $v){
        igk_html_db_build_table_row($tab->add("tr"), $v, $headers, "td", $callback);
    }
}
///<summary></summary>
///<param name="tab"></param>
///<param name="nav"></param>
///<param name="selected" default="null"></param>
/**
* 
* @param mixed $tab
* @param mixed $nav
* @param mixed $selected the default value is null
*/
function igk_html_buildmenu_nav($tab, $nav, $selected=null){
    foreach($tab as $k=>$v){
        $a=$nav->add('a')->setClass("menui");
        if(strtolower($k) == $selected){
            $a["class"]="+igk-active";
        }
        $a->setAttribute('href', $v)->Content=__("menu.{$k}");
    }
}
///<summary>build menu array for ul</summary>
///<param name='tab'> must be array of {key,'uri'}</param>
///<param name='ul'>the uri tab list</param>
/**
* build menu array for ul
* @param mixed $tab  must be array of {key,'uri'}
* @param mixed $ul the uri tab list
*/
function igk_html_buildmenu_ul($tab, $ul, $selected=null){
    foreach($tab as $k=>$v){
        $a=$ul->add('li')->add('a')->setClass("menui");
        if(strtolower($k) == $selected){
            $a["class"]="+igk-active";
        }
        $a->setAttribute('href', $v)->Content=__("menu.{$k}");
    }
}
///<summary></summary>
/**
* 
*/
function igk_html_cookie_warn(){
    $o="<div class=\"posfix\">";
    $o .= "____TODO: cookie warning___";
    $o .= "</div>";
    echo $o;
}
///<summary></summary>
///<param name="ctrl"></param>
/**
* 
* @param mixed $ctrl
*/
function igk_html_create_message($ctrl){
    $s=array(
            "host"=>$ctrl,
            "result"=>"",
            "message"=>"",
            "status"=>200,
            "notifyname"=>"",
            "ajx"=>(object)["type"=>"toast"],
            "style"=>"default",
            "replaceuri"=>0
        );
    return $s;
}
///<summary></summary>
///<param name="tr"></param>
///<param name="tab"></param>
///<param name="filter" default="null"></param>
/**
* 
* @param mixed $tr
* @param mixed $tab
* @param mixed $filter the default value is null
*/
function igk_html_db_build_table_entry($tr, $tab, $filter=null){
    igk_html_db_build_table_row($tr, $tab, $filter, "td");
}
///<summary></summary>
///<param name="tr"></param>
///<param name="tab"></param>
///<param name="filter" default="null"></param>
///<param name="callback" default="null"></param>
/**
* 
* @param mixed $tr
* @param mixed $tab
* @param mixed $filter the default value is null
* @param mixed $callback the default value is null
*/
function igk_html_db_build_table_header($tr, $tab, $filter=null, $callback=null){
    igk_html_db_build_table_row($tr, $tab, $filter, "th", $callback);
}
///<summary></summary>
///<param name="tr"></param>
///<param name="tab"></param>
///<param name="filter" default="null"></param>
///<param name="cell" default="td"></param>
///<param name="callback" default="null"></param>
/**
* 
* @param mixed $tr
* @param mixed $tab
* @param mixed $filter the default value is null
* @param mixed $cell the default value is "td"
* @param mixed $callback the default value is null
*/
function igk_html_db_build_table_row($tr, $tab, $filter=null, $cell="td", $callback=null){
    if($filter){
        foreach($filter as $k=>$v){
            if(is_array($v)){
                if(isset($v["auth"]) && !$v["auth"]){
                    continue;
                }
                if($cell == "td"){
                    $ccall=igk_getv($v, "callback") ?? $callback;
                    if($ccall){
                        $ccall($tr, $cell, $k, $tab);
                        continue;
                    }
                }
                $tr->add($cell)->Content=$k;
                continue;
            }
            if(empty($v)){
                if($callback){
                    $callback($tr, $cell, $k, $tab);
                }
                continue;
            }
            $e=igk_getv($tab, $v);
            if(empty($v)){
                $tr->add($cell)->addSpace();
            }
            else
                $tr->add($cell)->Content=$e;
        }
    }
    else{
        foreach($tab as $k=>$v){
            if(is_array($v)){
                if(isset($v["auth"]) && !$v["auth"]){
                    continue;
                }
                $ccall=igk_getv($v, "callback") ?? $callback;
                if($ccall){
                    $ccall($tr, $cell, $k, $tab);
                    continue;
                }
                if($cell == "th"){
                    $v=igk_getv($v, "name", igk_getv($v, "text", $k));
                }
            }
            if(empty($v)){
                $tr->add($cell)->addSpace();
            }
            else{
                if($cell == "th")
                    $tr->add($cell)->Content=__($v);
                else
                    $tr->add($cell)->Content=$v;
            }
        }
    }
}
///<summary></summary>
///<param name="dbResult"></param>
///<param name="sortcallback"></param>
///<param name="useempty"></param>
/**
* 
* @param mixed $dbResult
* @param mixed $sortcallback
* @param mixed $useempty the default value is 0
*/
function igk_html_db_select_filter($dbResult, $sortcallback, $useempty=0){
    if($useempty)
        $dbResult->addRow(is_object($useempty) ? $useempty: (object)array("clId"=>-1));
    $dbResult=$dbResult->sortBy($sortcallback);
    return $dbResult;
}
///<summary></summary>
///<param name="title"></param>
/**
* 
* @param mixed $title
*/
function igk_html_domaintitle($title){
    return  __("title.app_2", $title, igk_app()->Configs->website_domain);
}
///<summary></summary>
///<param name="obj"></param>
/**
* 
* @param mixed $obj
*/
function igk_html_dump($obj){
    $t=igk_createnode("div");
    $t->addDiv()->Content="Object: ";
    $tq=array(array($obj, $t));
    while($q=array_pop($tq)){
        $dv=$q[1]->addDiv();
        foreach($q[0] as $k=>$s){
            if(is_object($s) || is_array($s)){
                $dv->addLabel()->Content=$k;
                $ts=$dv->addDiv()->setStyle("margin-Left:32px; position:relative;");
                array_push($tq, array($s, $ts->addDiv()));
            }
            else{
                $ul=$dv->add("ul");
                $li=$ul->add("li");
                $li->addLabel()->Content=$k." : ";
                $li->addSpan()->Content=$s;
            }
        }
    }
    return $t;
}
///<summary></summary>
///<param name="id"></param>
/**
* 
* @param mixed $id
*/
function igk_html_extract_id($id){
    if(is_array($id)){
        if(!array_key_exists("id", $id)){
            igk_die("not a valid id");
        }
        return $id;
    }
    return array();
}

function igk_html_bind($node, $callback){
    $callback($node);
    return $node;
}

function igk_html_select_constants($type){
    $types = [];
    foreach(igk_get_class_constants($type) as $k=>$gt)
    {
        $types[] = ["i"=>$gt , "t"=>$k ] ;
    }
    return $types;
}


///<summary></summary>
///<param name="frm"></param>
///<param name="data"></param>
/**
* 
* @param mixed $frm
* @param mixed $data
*/
function igk_html_form_buildformfield($frm, $data){
    $frm->addObData(function() use ($data){
        igk_wl(igk_html_utils_buildformfield($data, 0));
    });
    return $frm;
}
///<summary>get select data</summary>
/**
 * get select data
 * @param array $data get select data
 * @param callback $callback callback to resolve data to field data
 */
function igk_html_form_select_data($data, $callback){
    $o = [];
    foreach($data as $r){
        $g = $callback($r);
        if (is_array($g)){
            $o[] = ["i"=>$g["i"], "t"=>$g["t"]];
        }
    }
    return $o;
}
///<summary>build form field on modele view </summary>
/**
* build form field on modele view
*/
function igk_html_form_fields($formFields, $render=0, $engine=null, $tag="div"){
    $o="";
    $clprop = new  IGKHtmlClassValueAttribute();
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
        $attr_key = $get_attr_key($v);
        $_value= key_exists("value", $v) ? $v["value"]: "";
        if ($attr_key){
            if (isset($v[$attr_key]["value"])){
                $_value = $v[$attr_key]["value"];
                unset( $v[$attr_key]["value"]);
            }
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
            $o .= "<input type=\"{$_type}\" {$_vt} {$_name}{$_id} ";
            if(isset($v["maxlength"])){
                $o .= "maxlength=\"{$v["maxlength"]}\" ";
            }
            if(isset($v["placeholder"])){
                $o .= "placeholder=\"{$v["placeholder"]}\" ";
            }
            $load_attr($v, $o);
            $o .= "/>";
            break;
        }
        if($_is_div){
            $o .= "</{$tag}>";
        }
    };
    $fieldset=0;
    foreach($formFields as $k=>$v){
        if ( ($cpos = strrpos($k, "[]")) !== false){   
            $name = substr($k, 0, $cpos);
            $ct = count($v);
            for ($i = 0; $i < $ct ; $i++){
                $b = $v[$i];
                $b["name"]= $k;  
                $bindValue($o, $fieldset, $name, $b );
            }
            //igk_wln_e("position : ", $cpos, count($v), $k, $name);
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


///<summary></summary>
/**
* 
*/
function igk_html_form_init(){
    $o=igk_createnode("input");
    $o["name"]="confirm";
    $o["value"]=1;
    $o["type"]="hidden";
    $o->renderAJX();
    igk_html_form_cref();
   
}
function igk_html_form_cref(){
    $o=igk_createnode("input");
    $o["name"]=base64_encode(igk_app()->Session->getCRef());
    $o["value"]=1;
    $o["type"]="hidden";
    $o->renderAJX();
}
///<include view inline>
///COMMENT : FORM FUNCTION
/**
*/
function igk_html_form_initfield($frm){
    $frm->addObData(function() use ($frm){
		igk_html_form_init();
    }, null);
}
///<summary></summary>
///<param name="ns"></param>
///<param name="e"></param>
/**
* 
* @param mixed $ns
* @param mixed $e
*/
function igk_html_js_lang($ns, $e){
    $data=json_encode((object)$e);
    $s=igk_createnode("script");
    $s->Content=<<<EFO
(function(){
	if (typeof(igk)!='undefined'){
		igk.system.createNS('{$ns}', {$data});
	}
})();
EFO;
$o=$s->render(null);
    unset($s);
    igk_wl($o);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="d"></param>
///<param name="source" default="null"></param>
/**
* 
* @param mixed $ctrl
* @param mixed $d
* @param mixed $source the default value is null
*/
function igk_html_login_form($ctrl, $d, $source=null){
    igk_app_load_login_form($ctrl, $d, $source);
}
///<summary></summary>
/**
* 
*/
function igk_html_loremipsum(){
    static $ipsum=null;
    if($ipsum == null){
        $ipsum = array();
        $ipsum[]=<<<EOF
<div id="lipsum">
<p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec hendrerit ligula ultricies est consectetur, nec rutrum tellus efficitur. Donec auctor vehicula mauris eget ullamcorper. Donec a vehicula erat, sit amet finibus eros. Maecenas auctor eros vel mauris lobortis, vitae eleifend felis maximus. Nulla eu bibendum nunc. Nam nec euismod turpis, in mattis diam. Duis lobortis porttitor lorem sed finibus. Etiam pellentesque dolor quis nisi varius ultricies.
</p>
<p>
Pellentesque consequat luctus mauris sit amet ultricies. Nulla in sapien a orci placerat scelerisque non id lacus. Vivamus quis consectetur augue. Pellentesque ut rutrum mi, non sagittis nulla. Donec eu venenatis tortor. Donec porta tellus faucibus libero suscipit, non dapibus lectus volutpat. Aliquam vel ante porta, ullamcorper libero vel, porttitor erat. Sed efficitur varius sem, sit amet eleifend purus accumsan ac. Sed volutpat ornare nunc.
</p>
<p>
Donec placerat ex a pretium aliquet. Praesent rutrum bibendum quam, at finibus mi euismod in. Aenean venenatis erat eu dignissim finibus. Donec venenatis iaculis velit, eget gravida odio gravida in. Praesent fringilla enim viverra rutrum fermentum. Etiam at odio at libero ultrices interdum eget eu metus. Sed ullamcorper metus eget nisi dictum, vel maximus magna hendrerit. Morbi dapibus tempor nisl, ut hendrerit nulla auctor quis. Vivamus porttitor accumsan justo vitae finibus. Donec nunc leo, ornare vitae consequat non, dignissim nec nisi. Curabitur interdum nisi dui, vitae maximus tellus dapibus nec. Morbi convallis elit non elit feugiat, nec maximus ex tincidunt. In hac habitasse platea dictumst. Curabitur sit amet turpis non neque luctus semper.
</p>
<p>
Proin faucibus, elit et egestas rhoncus, purus leo facilisis metus, vel malesuada diam sapien eu massa. Praesent mattis interdum enim eget viverra. Curabitur faucibus, velit ut suscipit suscipit, felis magna rhoncus ligula, feugiat lobortis eros neque in nisi. Nulla imperdiet elementum leo vitae sollicitudin. Etiam a massa at massa molestie semper a quis magna. Duis dictum laoreet arcu. Donec vulputate, risus et vehicula ultrices, massa ipsum cursus ex, sed viverra ante augue vel urna.
</p>
<p>
Mauris tempor orci eget dui eleifend, at tristique metus sollicitudin. Praesent purus quam, tincidunt eu gravida ut, dictum non nulla. In quis sem non ex egestas vehicula ut eget dui. Cras dictum venenatis egestas. Integer enim nulla, fringilla quis risus eu, accumsan condimentum nisl. Proin neque ligula, dapibus vel neque in, elementum facilisis neque. Phasellus sit amet vestibulum arcu.
</p></div>
EOF;
        $ipsum[]=<<<EOF
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec hendrerit ligula ultricies est consectetur, nec rutrum tellus efficitur. Donec auctor vehicula mauris eget ullamcorper. Donec a vehicula erat, sit amet finibus eros. Maecenas auctor eros vel mauris lobortis, vitae eleifend felis maximus. Nulla eu bibendum nunc. Nam nec euismod turpis, in mattis diam. Duis lobortis porttitor lorem sed finibus. Etiam pellentesque dolor quis nisi varius ultricies.
Pellentesque consequat luctus mauris sit amet ultricies. Nulla in sapien a orci placerat scelerisque non id lacus. Vivamus quis consectetur augue. Pellentesque ut rutrum mi, non sagittis nulla. Donec eu venenatis tortor. Donec porta tellus faucibus libero suscipit, non dapibus lectus volutpat. Aliquam vel ante porta, ullamcorper libero vel, porttitor erat. Sed efficitur varius sem, sit amet eleifend purus accumsan ac. Sed volutpat ornare nunc.
EOF;
        $ipsum[]=<<<EOF
<div id="lipsum">
<p>
</p><ul>
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
<li>Pellentesque ut elit vel tortor euismod egestas.</li>
<li>Pellentesque tempus velit non mauris euismod fermentum.</li>
<li>Fusce semper tortor vitae facilisis porta.</li>
<li>Maecenas eleifend nisi eget ornare fermentum.</li>
<li>Vivamus ut libero a augue ultricies rutrum at vitae odio.</li>
</ul>
<p></p>
<p>
</p><ul>
<li>Vestibulum tincidunt tortor vitae libero lacinia malesuada.</li>
<li>Integer quis quam sit amet ipsum facilisis ultricies id viverra lorem.</li>
<li>Sed eu velit sit amet augue egestas dapibus non ut tellus.</li>
</ul>
<p></p>
<p>
</p><ul>
<li>Nulla sagittis nulla vel nulla laoreet, faucibus dapibus mi auctor.</li>
<li>Phasellus id velit a ligula cursus suscipit.</li>
<li>Pellentesque eu purus ac tortor fermentum hendrerit.</li>
<li>Cras rutrum lorem at risus rhoncus, nec lobortis metus varius.</li>
</ul>
<p></p></div>
EOF;
    }
    return $ipsum[0];
}
///<summary></summary>
///<param name="setting"></param>
/**
* 
* @param mixed $setting
*/
function igk_html_match_message($setting){
    if(igk_is_ajx_demand()){
        $type=$setting["ajx"]->{"type"}
         ?? "toast";
        if(is_callable($type)){}
        else switch($type){
            case "toast":
            igk_ajx_toast($setting["message"], $setting["ajx"]->{"style"});
            break;default:
            break;
        }
        return;
    }
    igk_notifyctrl($setting["notifyname"])->addMsg($setting["message"], igk_getv($setting, "resulttype", "default"));
}
///<summary></summary>
///<param name="target"></param>
///<param name="pagingHost"></param>
///<param name="tab"></param>
///<param name="maxperpage"></param>
///<param name="callback"></param>
///<param name="uri"></param>
///<param name="selected" default="1"></param>
/**
* 
* @param mixed $target
* @param mixed $pagingHost
* @param mixed $tab
* @param mixed $maxperpage
* @param mixed $callback
* @param mixed $uri
* @param mixed $selected the default value is 1
*/
function igk_html_paginate($target, $pagingHost, $tab, $maxperpage, $callback, $uri, $selected=1, $ajxtarget=null){
    $max=$maxperpage;
    $count=igk_count($tab);
    $epagination=$max < $count;
    $it=new IGKIterator($tab);
    $it->setRewindStart($maxperpage * ($selected - 1));
    foreach($it as $k=>$v){
        $callback($target, $k, $v);
        $max--;
        if($max < 0)
            break;
    }
    if($epagination && $uri){
        $pagingHost->addDiv()->addAJXPaginationView($uri, $count, $maxperpage, $selected, $ajxtarget);
    }
}
///<summary></summary>
///<param name="t"></param>
///<param name="id"></param>
///<param name="auto" default="current-password"></param>
/**
* 
* @param mixed $t
* @param mixed $id
* @param mixed $auto the default value is "current-password"
*/
function igk_html_password($t, $id, $auto="current-password"){
    $i=$t->addInput($id, "password");
    $i["autocomplete"]=$auto;
    return $i;
}
///<summary></summary>
///<param name="tag"></param>
///<param name="array"></param>
///<param name="attr" default=""></param>
/**
* 
* @param mixed $tag
* @param mixed $array
* @param mixed $attr the default value is ""
*/
function igk_html_render($tag, $array, $attr=""){
    ob_start();
    foreach($array as $k){
        echo "<".$tag. " ".$attr.">".$k."</$tag>";
    }
    $o=ob_get_contents();
    ob_end_clean();
    echo $o;
}
///<summary></summary>
///<param name="s"></param>
/**
* 
* @param mixed $s
*/
function igk_html_render_message($s){
    if(igk_is_ajx_demand() || igk_getv($s, 'force_ajx')){
        $type=$s["ajx"]->{"type"}
         ?? "toast";
        if(is_callable($type)){
            $type($s);
        }
        else switch($type){
            case "toast":
                igk_ajx_toast($s["message"], $s["ajx"]->{"style"});
            break;
            case "notify":
            break;default:
            break;
        }
        return;
    }
    igk_notifyctrl($s["notifyname"])->addMsg($s["message"], igk_getv($s, "style", "default"));
}
///<summary>repalce uri</summary>
/**
* repalce uri
*/
function igk_html_replace_uri($d, $uri){
    $d->addBalafonJS()->Content="igk.winui.history.replace('{$uri}');";
}
///<summary></summary>
///<param name="data"></param>
///<param name="callback" default="null"></param>
/**
* 
* @param mixed $data
* @param mixed $callback the default value is null
*/
function igk_html_select_values($data, $callback=null){
    if($callback == null)
        $callback="igk_db_name_id";
    $tab=array();
    if(is_array($data)){
        foreach($data as $row){
            $tab[]=$callback($row);
        }
    }
    return $tab;
}
///<summary></summary>
/**
* 
*/
function igk_html_server_info(){
    $srv="<div style='font-size:1.6em; padding:10px; background-color:#fefefe; border:1px solid :#ddd; color:#444;' >".__('Server Info')."</div>";
    $srv .= "<table>";
    foreach($_SERVER as $k=>$v){
        $srv .= "<tr>";
        $srv .= "<td>".$k;
        $srv .= "</td>";
        $srv .= "<td>".$v;
        $srv .= "</td>";
        $srv .= "</tr>";
    }
    $srv .= "</table>";
    return $srv;
}
///<summary>render directly to content</summary>
/**
* render directly to content
*/
function igk_html_submit_button($value="submit", $id="submit"){
    $n=igk_createnode("input");
    $n["value"]=$value;
    $n["type"]="submit";
    $n["class"]="igk-btn igk-btn-default";
    $n->setId($id);
    $n->renderAJX();
}
///<summary></summary>
///<param name="title"></param>
/**
* 
* @param mixed $title
*/
function igk_html_subtitle($title){
    return $title." - [".igk_sys_getconfig("website_title")."]";
}
///<summary></summary>
///<param name="id"></param>
///<param name="value" default="null"></param>
/**
* 
* @param mixed $id
* @param mixed $value the default value is null
*/
function igk_html_textarea($id, $value=null){
    $g=igk_createnode("textarea");
    $g->setId($id);
    $g->Content=$value;
    $g->renderAJX();
}
///<summary></summary>
///<param name="t"></param>
///<param name="s"></param>
///<param name="level" default="2"></param>
/**
* 
* @param mixed $t
* @param mixed $s
* @param mixed $level the default value is 2
*/
function igk_html_title($t, $s, $level=2){
    return $t->add('h'.$level)->setContent($s);
}
///<summary></summary>
///<param name="doc"></param>
///<param name="message"></param>
///<param name="type" default="igk-default"></param>
/**
* 
* @param mixed $doc
* @param mixed $message
* @param mixed $type the default value is "igk-default"
*/
function igk_html_toast($doc, $message, $type="igk-default"){
    $t=igk_createnode("singlenodeviewer", null, array(IGK_HTML_NOTAG_ELEMENT));
    $n=$t->targetNode->addToast();
    $n["class"]="{$type}";
    $n->Content=$message;
    $doc->body->add($t);
    return $t;
}
///<summary></summary>
///<param name="formfields"></param>
/**
* 
* @param mixed $formfields
*/
function igk_html_utils_buildformfield($formfields, $render=1){
    return igk_html_form_fields($formfields, $render);
}

///<summary></summary>
///<param name="tag"></param>
///<param name="callback"></param>
///<param name="type" default="html"></param>
/**
* 
* @param mixed $tag
* @param mixed $callback
* @param mixed $type the default value is "html"
*/
function igk_html_view_node($tag, $callback, $type="html"){
    if($t=igk_createnode($tag)){
        $callback($t);
        if($type == "html"){
            $t->renderAJX();
        }
        else{
            $t->renderXML();
        }
    }
}
///<summary></summary>
/**
* 
*/
function igk_html_wdump(){
    return igk_html_wtag("pre", igk_ob_get_func("var_dump", func_get_args()), ["class"=>"igk-wdump"]);
}
///<summary>dump a table</summary>
function igk_html_dump_table($tab){
	$td = igk_createnode("table");
	$td["class"] = "igk-dump-table";
	foreach($tab as $k=>$v){
		$td->add("tr")
		->setClass("hd")
		->add("td")->setAttributes(["colspan"=>2])->Content =  $k;

		if (is_array($v)){
			foreach($v as $kk=>$vv){
				$tr = $td->add("tr");
				$tr->add("td")->Content = $kk;
				$tr->add("td")->Content = $vv;
			}
		} else {
			$tr = $td->add("tr");
			$tr->add("td")->addEmpty();
			$tr->add("td")->Content = $v;
		}
	}
	echo $td->render();
}

///<summary>build tag</summary>
///<param name="tag"></param>
///<param name="content"></param>
///<param name="attribs" default="null"></param>
///<param name="forcexml"></param>
/**
* 
* @param mixed $tag
* @param mixed $content
* @param mixed $attribs the default value is null
* @param mixed $forcexml the default value is 0
*/
function igk_html_wtag($tag, $content, $attribs=null, $forcexml=0){
    $o="<".$tag;
    if($attribs){
		$o.=" ";
		if (is_string($attribs)){
			$o .= $attribs;		}
		else
			$o .= igk_html_render_attribs($attribs);
    }
    if(!$forcexml && empty($content)){
        $o .= "/>";
    }
    else{
        $o .= ">";
        $o .= $content;
        $o .= "</".$tag.">";
    }
    return $o;
}
function igk_html_render_attribs($attribs){
	$o = "";
	if (!$attribs)
		igk_die("attrib is empty");
	foreach($attribs as $n=>$v){
		$r=HtmlUtils::GetValue($v);
		$o .= " {$n}=\"".$r."\"";
	}
	return ltrim($o);
}


function igk_html_installer_button($node, $class, $text, $update="/update", $update_target="#update_target"){
    $c_uri= igk_register_temp_uri($class);
    $n = igk_html_node_ajxpickfile(
    $c_uri."/upload", "{complete:igk.core.install('".$c_uri.$update."', '".
        $update_target."'),".
        "progress:igk.core.progress('".$update_target."'),"."accept:'.zip'"."}")
    ->setAttribute("value", $text);
    $src = file_get_contents(IGK_LIB_DIR.'/Views/Scripts/configs/installer.js'); 
 
    $n["class"] = "igk-btn igk-btn-primary";     
    $node->addScript()->Content= $src;
    $node->add($n);
    return $n; 
}

function igk_html_render_template($node)
{
    // engine is
    $option = igk_createobj([
        "Context" => "template",
        "Indent" => true,
        "Engine" => new IGK\System\Templates\TemplateEngine()
    ]);
    echo igk_html_render_node($node, $option);
}

igk_load_library("html_ob");
igk_load_library("html_json");