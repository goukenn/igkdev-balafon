<?php

namespace IGK\System\Html;

use IGK\Helper\StringUtility as IGKString;
use IGK\Resources\R;
use IGK\System\Html\Dom\DomNodeBase;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlNode; 
use IGK\System\Html\XML\XmlNode;
use IGKException;
use ReflectionClass;
use ReflectionFunction;

use function igk_resources_gets as __;

///<summary>represent html utility </summary>
/**
 * represent html utility
 */
abstract class HtmlUtils extends DomNodeBase
{

    /**
     * get full query ars
     * @param string $uri 
     * @param array $args 
     * @return string 
     */
    public static function GetFullQueryUri(string $uri, array $args){
        $q = self::AppendQueryArgs($uri, $args);
        return explode("?", $uri)[0]."?".http_build_query($q);
    }
    /**
     * append query args
     * @param string $uri 
     * @param array $args 
     * @return array 
     */
    public static function AppendQueryArgs(string $uri, array $args){
        $data = parse_url($uri);
        $q = [];
        if (isset($data["query"])){
            parse_str($data["query"], $q);        
        }
        $q = array_merge($q, $args);
        return $q;
    }
    /**
     * get system tagname 
     * @param HtmlItemBase $node 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetGeneratedTagname(HtmlItemBase $node){
        $tagname = "";
        $inf = $node->getFlag(IGK_NODETYPE_FLAG); 
        if (!$inf || ($inf->type=="c")){
            $tagname = $node->getTagName();
        } else {
            $tagname = "igk:" . $node->getTagName();
        }
        return $tagname;
    }

    public static function GetAttributeArrayToString($attribs){ 
        $o = "";
        if (!$attribs)
            igk_die("attrib is empty");
        foreach($attribs as $n=>$v){
            $r= self::GetValue($v);
            $o .= " {$n}=\"".$r."\"";
        }
        return ltrim($o);       
    }

    private static $gRendering;
    /**
     * 
     * @param array|\IIGKArrayObject $n  item to convert
     * @return array 
     */
    public static function ToArray($n){
        if (is_array($n))
            return $n;
        return $n->to_array();
    }
    public static function GetAttributes($attr){
        return self::ToArray($attr);
    }
    public static function SubmitActionCallback($title = null, $name = "btn_submit")
    {
        return function ($a) use ($title, $name) {
            $a->addInput($name, "submit", $title)->setClass("igk-btn-default");
        };
    }
    public static function ConfirmAction()
    {
        return function ($a) {
            $a->addInput("btn.ok", "submit", __("restore"));
            $a->addInput("btn.cancel", "submit", __("cancel"))->on("click", "igk.winui.controls.panelDialog.close()");
        };
    }
    public static function GetInputType($type)
    {
        static $requireInput;
        if ($requireInput === null) {
            $requireInput  = explode("|", "text|email|password");
        }
        if (in_array($type, $requireInput)) {
            return $type;
        }
        return "text";
    }
    ///AddImgLnk add image link
    /**
     */
    public static function AddAnimImgLnk($target, $uri, $imgname, $width = "16px", $height = "16px", $desc = null, $attribs = null)
    {
        if (is_object($target)) {
            $a = $target->add("a", array("href" => $uri, "class" => "img_lnk"));
            $t = array();
            $t["a"] = $a;
            $t["img"] = $a->add("igk-anim-img", array(
                "width" => $width,
                "height" => $height,
                "src" => R::GetImgUri($imgname),
                "alt" => __($desc)
            ));
            $a->setAttributes($attribs);
            return (object)$t;
        }
        return null;
    }
    ///add button link
    /**
     */
    public static function AddBtnLnk($target, $langkey, $uri, $attributes = null)
    {
        if ($target == null)
            return;
        $a = $target->add("a", array("class" => "igk-btn igk-btn-lnk", "href" => $uri));
        $a->Content = is_string($langkey) ? __($langkey) : $langkey;
        if (is_array($attributes)) {
            $a->setAttributes($attributes);
        }
        return $a;
    }
    ///AddImgLnk add image link
    /**
     */
    public static function AddImgLnk($target, $uri, $imgname, $width = "16px", $height = "16px", $desc = null, $attribs = null)
    {
        if (is_object($target)) {
            $a = $target->addImgLnk($uri, $imgname, $width, $height, $desc);
            if ($attribs)
                $a->setAttributes($attribs);
            return $a;
        }
        return null;
    }
     
    ///<summary></summary>
    ///<param name="tr"></param>
    ///<param name="targetid" default="null"></param>
    /**
     * 
     * @param mixed $tr
     * @param mixed $targetid the default value is null
     */
    public static function AddToggleAllCheckboxTh($tr, $targetid = null)
    {
        if ($targetid != null)
            $targetid = ",'#$targetid'";
        $li = $tr->add("th", array("class" => "box_16x16"))->li();
        $i = $li->input(null, "checkbox")->setAttributes([
            "onchange" => "javascript: ns_igk.html.ctrl.checkbox.toggle(this, ns_igk.getParentByTagName(this, 'table'), this.checked, true $targetid);"
        ]);
        return $i;
    }
    ///<summary></summary>
    ///<param name="array"></param>
    /**
     * 
     * @param mixed $array
     */
    public static function BuildForm($array)
    {
        $frm = igk_createnode("form");
        foreach ($array as $k => $v) {
            switch (strtolower($k)) {
                case "label":
                    $lb = $frm->addLabel();
                    $lb->Content = __(IGK_STR_EMPTY);
                    break;
                case "radio":
                    $frm->addInput($v["id"], igk_getv($v, "text", null), "radio");
                    break;
                case "checkbox":
                    $frm->addInput($v["id"], igk_getv($v, "text", null), "checkbox");
                    break;
                case "hidden":
                    $frm->addInput($v["id"], igk_getv($v, "text", null), "hidden");
                    break;
                case "button":
                case "submit":
                case "reset":
                    $frm->addInput($v["id"], strtolower($k), igk_getv($v, "text", "r"));
                    break;
                case "textarea":
                    $frm->addTextArea($v["id"]);
                    break;
                case "br":
                    $frm->addBr();
                    break;
            }
        }
        return $frm;
    }
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="target"></param>
    /**
     * 
     * @param mixed $item
     * @param mixed $target
     */
    public static function CopyChilds($item, $target)
    {
        if (($item == null) || ($target == null) || !$item->HasChilds)
            return false;
        foreach ($item->getChilds() as $k) {
            $target->Load($k->render());
        }
        return true;
    }
    ///used to create sub menu in category
    /**
     */
    public static function CreateConfigSubMenu($target, $items, $selected = null)
    {
        $ul = $target->add("ul", array("class" => "igk-cnf-content_submenu"));
        foreach ($items as $k => $v) {
            $li = $ul->addLi();
            $li->add("a", array("href" => $v))->Content = __("cl" . $k);
            if ($selected == $k) {
                $li["class"] = "+igk-cnf-content_submenu_selected";
            } else {
                $li["class"] = "-igk-cnf-content_submenu_selected";
            }
        }
        return $ul;
    }
    ///get all element childs
    /**
     */
    public static function GetAllChilds($t)
    {
        $d = array();
        if (method_exists(get_class($t), "getChilds")) {
            $s = $t->getChilds();
            if (is_array($s)) {
                $d = array_merge($d, $s);
                foreach ($s as $k) {
                    $d = array_merge($d, self::GetAllChilds($k));
                }
            }
        }
        return $d;
    }
    ///<summary></summary>
    ///<param name="$c"></param>
    ///<param name="context" default="null"></param>
    /**
     * 
     * @param mixed $c
     * @param mixed $context the default value is null
     */
    public static function GetAttributeValue($c, $context = null)
    {
        $s = self::GetValue($c);
        $q = trim($s);
        $v_h = "\"";
        if (preg_match("/^\'/", $q) && preg_match("/\'$/", $q)) {
            $v_h = "'";
        }
        // if (igk_is_debug()){
        //     echo "debug \n<br />";
        // }
        if ((0===strpos($q, $v_h)) && (strrpos($q, $v_h, -1)!==false)) {
            $q = substr($q, 1);         
            $q = substr($q, 0, strlen($q) - 1); 
        }
        if (($context!="binding") && ($v_h == "\"")) {
            $q = str_replace("\"", "&quot;", $q);
        }
        if ($context && is_string($context) && (preg_match("/(xml|xsl)/i", $context))) {
            $q = str_replace("&amp;", "&", $q);
        }
        return $q;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="options" default="null"></param>
    /**
     * 
     * @param mixed $n
     * @param mixed $options the default value is null
     */
    public static function GetContentValue($n, $options = null)
    {


        if ($n->iscallback("handleRender")) {
            return $n->handleRender();
        }
        $c = $n->getContent();
        $inner = "";
        if (igk_is_callback_obj($c)) {
            return igk_invoke_callback_obj(igk_getv($c, 'clType') == 'node' ? $n : null, $c);
        }
        if (is_array($c)) {
            $r = igk_createnode('content');
            $i = 0;
            foreach ($c as $k => $v) {
                if (ord($k) == 0)
                    $r->add("item")->setAttribute("key", $k)->setContent($v);
                $i++;
            }
            return $r->getinnerHtml(null);
        }
        if (is_object($c) && ($c instanceof HtmlItemBase)) {
            if (self::$gRendering === $c)
                return "";
            self::$gRendering = $c;
            $o = $c->render($options);
            self::$gRendering = null;
            return $o;
        }
        return self::GetValue($c, $options);
    }
    ///<summary></summary>
    ///<param name="array"></param>
    /**
     * 
     * @param mixed $array
     */
    public static function GetTableFromSingleArray($array)
    {
        $tab = igk_createnode("table");
        foreach ($array as $k => $v) {
            $tr = $tab->addTr();
            $tr->addTd()->Content = $k;
            $tr->addTd()->Content = $v;
        }
        return $tab;
    }
    ///<summary>return value according to string</summary>
    /**
     * return value according to string
     */
    public static function GetValue($c, $options = null)
    {
        $out = IGK_STR_EMPTY;
        if (($c == "0") || (is_numeric($c) && ($c === "0")))
            return "0";
        if (is_numeric($c) || (is_string($c) && !empty($c))) {
            $out .= $c;
        } else if (is_object($c)) {
            if ($c instanceof HtmlItemBase) {
                return igk_html_render_node($c, $options, null);
            }
            while (is_object($c)) {
                $c = self::GetValueObj($c, $options);
            }
            if (empty($c))
                return null;
            $out .= $c;
        } else {
            $out = $c;
        }
        return $out;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    ///<param name="options"></param>
    /**
     * 
     * @param mixed $v
     * @param mixed $options
     */
    public static function GetValueObj($v, $options)
    {
        if (method_exists(get_class($v), IGK_FC_GETVALUE)) {
            $v = $v->getValue($options);
        } else {
            switch (igk_getv($v, IGK_OBJ_TYPE_FD)) {
                case 'callable':
                    $v = call_user_func_array($v->name, $v->attrs ? $v->attrs : array());
                    break;
                case '_callback':
                    $v = igk_invoke_callback_obj(null, $v);
                    break;
                default:
                    if (is_callable($fc = igk_getv($v, "callback"))) {
                        $v = (call_user_func_array($fc, array_merge(igk_getv($v, "params", []), $options ? [$options] : [])));
                    } else {
                        $v = "\"IGK:DATAOBJ\"";
                    }
                    break;
            }
        }
        return $v;
    }
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="target"></param>
    /**
     * 
     * @param mixed $item
     * @param mixed $target
     */
    public static function MoveChilds($item, $target)
    {
        if (($item == null) || ($target == null) || !$item->HasChilds)
            return false;
        foreach ($item->getChilds() as $k) {
            $target->add($k);
        }
        return true;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value" default="null"></param>
    ///<param name="type" default="text"></param>
    /**
     * 
     * @param mixed $id
     * @param mixed $value the default value is null
     * @param mixed $type the default value is "text"
     */
    public static function nInput($id, $value = null, $type = "text")
    {
        $btn = igk_createnode("input")->setAttributes(array("id" => $id, "name" => $id, "type" => $type, "value" => $value));
        switch (strtolower($btn["type"])) {
            case "button":
            case "submit":
            case "reset":
                $btn["class"] = "clbutton";
                break;
        }
        return $btn;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $id
     * @param mixed $value
     */
    public static function nTextArea($id, $value)
    {
        return igk_createnode("textarea")->setAttributes(array("id" => $id, "name" => $id, "value" => $value));
    }
    ///<summary></summary>
    ///<param name="item"></param>
    /**
     * 
     * @param mixed $item
     * @deprecated direct remove self remove item with the remove method
     */
    public static function RemoveItem($item)
    {  
        if (($item != null) && (($p = $item->getParentNode()) != null)) {
            if ($item->remove() === false){            
                igk_debug_wln("/!\\ Failed to remove an item");
                return false;
            }
            return true;
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="var"></param>
    /**
     * 
     * @param mixed $var
     */
    public static function ShowHierarchi($var)
    {
        $out = IGK_STR_EMPTY;
        if ($var->HasChilds) {
            $out .= "<ul>";
            foreach ($var->Childs as $k) {
                $out .= "<li>" . $k->TagName;
                if ($k->TagName == "a")
                    $out .= " : " . $k->Content;
                if ($k->TagName == "input")
                    $out .= " : " . $k["value"];
                $out .= self::ShowHierarchi($k);
                $out .= "</li>";
            }
            $out .= "</ul>";
        }
        return $out;
    }
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="type" default="tr"></param>
    ///<param name="startAt"></param>
    ///<param name="class1" default="table_darkrow"></param>
    ///<param name="class2" default="table_lightrow"></param>
    /**
     * 
     * @param mixed $target
     * @param mixed $type the default value is "tr"
     * @param mixed $startAt the default value is 0
     * @param mixed $class1 the default value is "table_darkrow"
     * @param mixed $class2 the default value is "table_lightrow"
     */
    public static function ToggleTableClassColor($target, $type = "tr", $startAt = 0, $class1 = "table_darkrow", $class2 = "table_lightrow")
    {
        if ($target == null)
            return;
        $k = 0;
        $tab = $target->getElementsByTagName($type);
        for ($i = $startAt; $i < count($tab); $i++) {
            if (isset($tab[$i])) {
                $tr = $tab[$i];
                if ($k == 0) {
                    $tr["class"] = $class1;
                    $k = 1;
                } else {
                    $tr["class"] = $class2;
                    $k = 0;
                }
            }
        }
    }

    public static function CreateHtmlComponent($name, $args = null, $initcallback = null, $class = HtmlItemBase::class, $context = HtmlContext::Html)
    {        
        require_once IGK_LIB_DIR . "/igk_html_func_items.php";
        static $createComponentFromPackage = null, $creator = null, $initiator = null;
        if ($initiator == null) {
            $initiator = igk_environment()->createArray("component_initiators");
        }
        if ($v_info = igk_getv($initiator, $name)) {
            $v_info["context"] = $context;
            $v_info["count"]++;
            
            return call_user_func_array($v_info["invoke"], [$v_info, $args]);
        }
        if ($creator != null) {
            if (isset($creator[$name])) {
                $fc = $creator[$name];
                if (!is_array($args))
                    $args = array();
                $args = array($name, $args);
                if ($c = call_user_func_array($fc, $args)) {
                    if (is_callable($initcallback)) {
                        $initcallback($c, array(
                            "type" => IGK_COMPONENT_TYPE_FUNCTION,
                            "name" => "__creator:" . $name
                        ));
                    }
                }
                return $c;
            }
        }
        $package = null;
        if ($createComponentFromPackage === null)
            $createComponentFromPackage = function ($g, $name, $args = null, $initcallback = null, $class = IGK_HTML_ITEMBASE_CLASS, $context = HtmlContext::Html) use (&$package) {
                if (isset($package[$g]["components"])) {
                    $components = $package[$g]["components"];
                    if (isset($components[$name]) && is_callable($c_fc = $components[$name])) {
                        return call_user_func_array($c_fc, func_get_args());
                    }
                }
                return null;
            };
        $package = igk_reg_component_package();
        if (($pos = strpos($name, ":")) !== false) {
            $g = substr($name, 0, $pos);
            $n = substr($name, $pos + 1);
            if (isset($package[$g])) {
                if ($comp = $createComponentFromPackage($g, $name, $args, $initcallback, $class, $context)) {
                    $creator[$name] = ["callback", "count" => 1];
                    return $comp;
                }
                $cc = $package[$g]["callback"];
                $fc = function () use ($cc) {
                    return call_user_func_array($cc, func_get_args());
                };
                if ($creator == null) {
                    $creator = array();
                }
                $creator[$name] = $fc;
                $ng = call_user_func_array($fc, array_merge(array($name), array_slice(func_get_args(), 1)));
                return $ng;
            }
        }
        if ($comp = $createComponentFromPackage("igk", $name, $args, $initcallback, $class, $context)) {
            return $comp;
        }
        $c = null;
        // $f = IGKString::Format(IGK_HTML_CLASS_NODE_FORMAT, $name);
        // if (class_exists($f) && !igk_reflection_class_isabstract($f) && igk_reflection_class_extends($f, $class)) {
        //     $p = igk_sys_reflect_class($f);
        //     $tb = array();
        //     if (is_array($args))
        //         $tb = $args;
        //     $initiator[$name] = [
        //         "type" => "class", "callback" => [$p, "newInstance"], "class" => $f, "count" => 1, "invoke" => function ($inf, $tb) {
        //             $f = $inf["class"];
        //             $c = $inf["callback"];
        //             $o = call_user_func_array($c, $tb);
        //             if ($o)
        //                 $c = $o;
        //             else {
        //                 $c = new $f();
        //             }
        //             return $c;
        //         }
        //     ];
        //     $c = call_user_func_array($initiator[$name]["invoke"], [$initiator[$name], $tb]);
        // } else {
            if (function_exists($fc = str_replace("-", "_", IGK_FUNC_NODE_PREFIX . $name))) {
                $s = new ReflectionFunction($fc);
                $v_rp = $s->getNumberOfRequiredParameters();
                $initiator[$name] = [
                    "type" => "function", "name" => $name, "callback" => $fc, "count" => 1, "requireArgs" => $v_rp, "invoke" => function ($inf, $args) use ($initcallback) {
                        $tb = is_array($args) ? $args : array();
                        $v_pcount = igk_count($tb);
                        $v_rp = $inf["requireArgs"];
                        $name = $inf["name"];
                        $c = null;
                        $fc = $inf["callback"];
                        if ($v_pcount >= $v_rp) {
                            $c = call_user_func_array($fc, $tb);
                            if ($c) {
                                if ($initcallback)
                                    $initcallback($c, array("type" => IGK_COMPONENT_TYPE_FUNCTION, "name" => $fc));
                                    $c->setInitNodeTypeInfo(HtmlInitNodeInfo::Create([
                                        "type"=>"f",
                                        "name"=>$name,
                                        "args"=>$tb
                                    ]));
                                    // $mc = new HtmlNode("h1");
                                    // igk_wln_e(__FILE__.":".__LINE__, serialize($c),
                                    // self::GetGeneratedTagname($c),
                                    // self::GetGeneratedTagname($mc)
                                    // );
                            }
                        } else {
                            igk_die("add <b>{$name}</b> : number of required parameters mismatch. Expected {$v_rp} but " . $v_pcount . " passed");
                        }
                        return $c;
                    }
                ];
                $fc = $initiator[$name]["invoke"];
              
                $c = $fc($initiator[$name], $args);
            } else {
                $initiator[$name] = [
                    "type" => "fallback", "name" => $name, "count" => 1, "context" => $context, "invoke" => function ($inf, $args) {
                        $name = $inf["name"];
                        $context = $inf["context"];
                        if ($context == HtmlContext::Html) {
                            $c = new HtmlNode($name);
                        } else {
                            $c = new XmlNode($name);
                        }
                        return $c;
                    }
                ];
                $c = call_user_func_array($initiator[$name]["invoke"], [$initiator[$name], $args]);
            }
        
        return $c;
    }

    ///<summary></summary>
    ///<param name="vsystheme"></param>
    /**
    * 
    * @param mixed $vsystheme
    */
    public static function InitSystemTheme($vsystheme){
      
        $vsystheme->Name="igk_system_theme";
        $vsystheme->def->Clear();
        $d=$vsystheme->get_media(HtmlDocThemeMediaType::SM_MEDIA);
        $d=$vsystheme->get_media(HtmlDocThemeMediaType::XSM_MEDIA);
        $d=$vsystheme->reg_media("(max-width:700px)");
        $lfile=[];
        $v_cache_file=igk_io_dir(IGK_LIB_DIR."/Cache/css.cache");
        if(file_exists($v_cache_file)){
            igk_css_include_cache($v_cache_file, $lfile);
        }
        else{
            $lfile[]=igk_io_dir(IGK_LIB_DIR."/".IGK_STYLE_FOLDER."/global.pcss");
            $lfile[]=igk_get_env("sys://css/file/global_color", igk_io_dir(IGK_LIB_DIR."/".IGK_STYLE_FOLDER."/igk_css_colors.phtml"));
            $lfile[]=igk_get_env("sys://css/file/global_template", igk_io_dir(IGK_LIB_DIR."/".IGK_STYLE_FOLDER."/igk_css_template.phtml"));
        }
        $g=implode(";", $lfile);
        $g=str_replace(IGK_LIB_DIR, "%lib%", $g);
        $vsystheme->def->setFiles($g);
    }


    public static function SkipAdd($value=1){
        if ($p = igk_html_parent_node()){
            igk_environment()->set(IGK_XML_CREATOR_SKIP_ADD, $value? $p : $value);
        }
    }
    public static function IsSkipped($autoreset){
        $p = igk_html_parent_node();     
        $o = igk_environment()->get(IGK_XML_CREATOR_SKIP_ADD);    
        if (($o === $p) && $autoreset){
            igk_html_skip_add(null); 
        }
        return $o != null;
    }
}
