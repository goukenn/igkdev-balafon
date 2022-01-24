<?php

namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlItemBase;
use IGKApp;
use IGKException;
use ReflectionMethod;

/**
 * represent base renderer engine
 * @package IGK\System\Html
 */
class HtmlRenderer{
  
    /**
     * append after rendering element
     * @param mixed $option 
     * @param mixed $node 
     * @return void 
     * @throws IGKException 
     */
    public static function AppendOptionNode($option, $node){
        if (!($c = igk_getv($option, "__append__"))) {
            $c = [];
            $option->{"__append__"} = $c;
        }
        array_push($option->__append__, $node);
    }
    public static function CreateRenderOptions(){
        $o = (object)[
            "AJX"=>false,
            "Indent"=>false,
            "Stop"=>0,
            "Context"=>HtmlContext::Html,
            "Depth"=>0,
            "Document"=>null,
            "BodyOnly"=>0,
            "Attachement"=>0,
            "StandAlone"=>0,
            "Cache"=>igk_sys_cache_require(),
            "CacheUri"=>0,
            "CacheUriLevel"=>0,
            "setnoAttribEscape"=>null,
            "Tab"=>[],
            "Chain"=>1,
            "TextOnly"=>false,
            "lastRendering"=>null
        ];
        if($o->Cache){
            $o->CacheUri=base64_decode(igk_sys_cache_uri());
            $o->CacheUriLevel=explode("/", $o->CacheUri);
        }
        return $o;
    }
    public static function GetValue($o, $options=null){
   
        if ($o instanceof IHtmlGetValue){  
            return $o->getValue($options);
        }
        if ($o instanceof HtmlItemBase){
            return $o->render($options);
        }
    }
     ///<summary>force to render global html document</summary>
    /**
    * force to render global html document
    */
    public static function RenderDocument($doc=null, $refreshDefault=1, $ctrl=null){ 
        $igk= igk_app(); 
        $doc= $doc ?? $igk->getDoc();
        if(!$igk->ConfigMode && $igk->Configs->allow_auto_cache_page){
            $ctrl=igk_getctrl("cache");
            if($ctrl){
                $ctrl->loadCache($igk->CurrentPage.".html", igk_app());
            }
        }
        else{
            if($refreshDefault){
                $ctrl=$ctrl ?? igk_get_defaultwebpagectrl();
                if($ctrl && (igk_environment()->get(IGK_KEY_FORCEVIEW) !== 1)){
                    if(!igk_environment()->get($key = "sys://tempdata")){
                        igk_environment()->set($key, 1);
                    }
                    if(!igk_environment()->get(IGK_ENV_PAGEFOLDER_CHANGED_KEY)){ 
                        $ctrl->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
                        $ctrl->setEnvParam('render_context', 'docview');                       
                        // gourou d'etranglement
                        $bbox = $doc->getBody()->getBodyBox()->clearChilds();
                        if ($t = $ctrl->getTargetNode()){
                            $bbox->add($t);
                            $ctrl->View();
                        }
                    }
                }
            } 
            // -------------------
            // + | Render document
            // -------------------  
            $response = new \IGK\System\Http\WebResponse($doc);
            $response->cache = !igk_environment()->no_cache && IGKApp::GetConfig("allow_page_cache");             
            $response->output();            
        
        }
    }
    /**
     * a way to render node
     */
    public static function Render(HtmlItemBase $item, $options=null){
        // + | render option definition
        
        if ($options==null){
            $options = self::CreateRenderOptions();
        } else {
            // sanitize options property
            foreach([ 
                "Stop"=>0,
                "Context"=>"XML",
                "Depth"=>0,
                "Indent"=>false
            ] as $k=>$v){
                if(!isset($options->$k)){
                    $options->$k=$v;
                }
            }
        }
        $tab = [
            ["item"=>$item, "close"=>false]
        ]; 
        $options->Source = $item;
        $s = "";
        $reflect = [];
        $ln= $options->Indent ? "\n" : "";
        // $renderer = null; //igk_getv($options, "renderer") ?? new HtmlRenderer();
        $engine = igk_getv($options, "Engine"); 
        $start = true;

        while(($q = array_pop($tab)) && !$options->Stop){
            $tag = null;
            $i = null;
            if (is_array($q))
                $i = $q["item"];
            else {
                $i = $q;
                $q = ["item"=>$i, "close"=>false ];
            }
            
            if (!$q["close"]){
                if ($ln && ($options->Depth >0)){
                    $s.= str_repeat("\t",$options->Depth );
                }
                $options->Depth++;
                if ($i instanceof HtmlItemBase) 
                {
                    if (!$i->AcceptRender($options)){
                        continue;
                    }  
                    if (isset($options->__append__)){
                        $tab = array_merge($tab, $options->__append__); 
                        unset($options->__append__);
                    }
                }

                if ($engine){ 
                    $s .= $engine->render($i, $options);
                    continue;
                }
                if ($options->Source !== $i){
                    if (!isset($reflect[$cl = get_class($i)])){
                        $reflect[$cl] = HtmlItemBase::class != (new ReflectionMethod($i, "render"))->getDeclaringClass()->name;                
                    }
                    if ($reflect[$cl]){
                        $options->lastRendering = $i;
                        $s .= $i->render($options);
                        continue;
                    } 
                }

                $options->lastRendering = $i;
                $tag = $i->getCanRenderTag() ? $i->getTagName() : "";
                $havTag = !empty($tag);
                if ($havTag){
                    $s .= "<".$tag."";
                    // render attribute 
                    if (!empty($attr = static::GetAttributeString($i,  $options))){
                        $s.= " ".$attr;
                    }
                }
                $content = $i->getContent();
                $childs = $i->getRenderedChilds($options);   
                $have_childs = (count($childs) > 0);
                $have_content = $have_childs || !empty($content);         
                $q["close_tag"] =  $have_content || $i->closeTag();
                $q["close"] = true;
                $q["tag"] = $tag;
                $q["have_childs"]=$have_childs;
                if ($havTag && $q["close_tag"]){
                    $s.=">";
                }
                if (!empty($content)){
                    if (is_object($content)){
                        $s .= HtmlRenderer::GetValue($content, $options);
                    }else{
                        if (is_array($content)){
                            $s .= json_encode($content);
                        }else 
                            $s .= $content;
                    }
                }
                if((count($childs) > 0)){
                    //$options->Depth++;
                    $s.= $ln;
                    array_push($tab, $q); 
                    $childs = array_reverse($childs);
                    $tab = array_merge($tab, $childs);
                    // foreach($childs as $k){
                    //     array_push($tab,["item"=>$k, "close"=>false]);
                    // }
                    continue;
                }
            } else {
                $tag = $q["tag"];
            }
            $options->Depth = max(0, $options->Depth-1);
            // igk_wln("depth : ".$options->Depth);
            if (!empty($tag)){
                if ($q["close_tag"]){
                    if ($ln  && $q["have_childs"] && ($options->Depth >0)){
                        $s.= str_repeat("\t",$options->Depth );
                    }
                    $s .= "</".$tag.">".$ln;

                }
                else {
                    $s.= "/>".$ln;
                } 
            }
            // $options->Depth = max(0, $options->Depth -1);
        } 
        return rtrim($s);
    }

    public static function GetAttributeString(HtmlItemBase $item, $options){      

        $attribs = $item->getAttributes();
        $out = IGK_STR_EMPTY;
        igk_get_defined_ns($item, $out, $options);
        if ($options && ($options->Context == "mail")) {
            //for mail rendering attribures
            if (!isset($options->renderTheme)){
                $options->renderTheme = igk_app()->getDoc()->getTheme();
            }

            if ($attribs) {

                $g = $attribs["style"];
                $cl = $attribs["class"];
                if (!empty($g)) {
                    $g = rtrim($g, ";") . "; ";
                }
                if ($cl) {
                    foreach ($cl->getKeys() as $k) {
                        $matcher = [];
                        if (!empty($tagname = $item->tagName)) {
                            $matcher[] = $tagname. "." . $k;
                        }
                        if (!empty($id = igk_getv($item, "id"))) {
                            $matcher[] = "#id.{$k}_$id";
                        }
                        $matcher[] = ".{$k}";
                        foreach ($matcher as $m) {
                            if ($p = $options->renderTheme[$m]) {
                                $g .= rtrim($p, ";") . ";";
                            }
                        }
                    }
                    if ($options->renderTheme && $g) {
                        $g = igk_css_treat($options->renderTheme, $g, $options->renderTheme);
                    }
                }
                $item->setStyle("{$g}");
            }
        }


        if ($item->getHasAttributes()) {
            if (is_array($item->getAttributes())){
                igk_wln_e("attributes is an is array", get_class($item));
            }
            $attrs = $item->getAttributes()->to_array();
            // igk_wln_e(__FILE__.":".__LINE__, $attrs);
            if (!empty($out)) {
                $out .= " ";
            }
            foreach ($attrs as $k => $v) {
                if (($k == "@activated") && is_array($v)) {
                    //$out .= " ";
                    foreach ($v as $ak => $av) {
                        $out .= $ak . " ";
                    }
                    continue;
                }
                $v_is_obj = is_object($v);
                if ($v_is_obj && ($v instanceof HtmlActiveAttrib)) {
                    // if(!empty($out))
                    //     $out .= " ";
                    $out .= $k . " ";
                    continue;
                }
                $r = (is_object($v) && ($v instanceof HtmlExpressionAttribute));
                if ($r)
                    $c = $v->getValue();
                else {
                    if (is_array($v)) {
                        igk_wln_e("/!\\ don't send array as attribute: ", $k, $v);
                    }
                    if ($v_is_obj && ($v instanceof IHtmlGetValue)) {
                        if (!empty($cv = $v->getValue())){
                            $out .= $k . "=\"" . $cv . "\" ";
                        }
                        continue;
                    } else {
                        $c = self::GetStringAttribute($v, $options);
                    }
                }
                if (is_numeric($c) || !empty($c)) {
                    // if(!empty($out))
                    //     $out .= " ";
                    if ($options && !$r && igk_getv($options, "DocumentType") == 'xml') {
                        $c = str_replace('&', '&amp;', $c);
                    }
                    if ($r) {
                        $out .= $c . " ";
                    } else
                        $out .= $k . "=" . $c . " ";
                }
            }
        }
        $event = $item->getFlag(HtmlItemBase::EVENTS);
        if ($event) {
            $s = "";
            foreach ($event as $k => $v) {
                $s .= $v->getValue() . " ";
            }
            $out .= $s;
        }
        return rtrim($out);
    }

   
    ///<summary>get attribute string</summary>
    ///<param name="v"></param>
    ///<param name="options"></param>
    /**
    * get attribute string
    * @param mixed $v
    * @param mixed $options
    */
    public static function GetStringAttribute($v, $options){
        if(empty($v) && !is_numeric($v))
            return null;
        $data=null;
        while(is_object($v)){
            $v= HtmlUtils::GetValueObj($v, $options);
        }
        if(empty($v) && !is_numeric($v)){
            return null;
        }
        if(is_string($v)){
            if(strpos($v, "\"")===0){
                return $v;
            }
            if(strpos($v, "\'") === 0 )
                return $v;
        }
        if (!igk_getv($options, "setnoAttribEscape")){
            if($options && igk_getv($options, "AttributeEntityEscape")){
                $v=preg_replace_callback("/\&([^;=]+;)?/i", function($m){
                    switch($m[0]){
                        case "&":
                        return "&amp;";
                        case "&copy;":
                        return "&#169;";
                    }
                    return $m[0];
                }
                , $v);
            }        
            $v=str_replace("\"", "&quot;", $v);
            if(is_array($v)){
                igk_wln_e(__METHOD__."::attribute is array", igk_show_trace());
            }
        }else{
            $v=str_replace("\"", "\\\"", $v);
        }
        unset($options->setnoAttribEscape);
        return "\"".$v."\"";
    }
    ///<summary>get node item inner content</summary>
    /**
     * get node item inner content
     * @param HtmlItemBase $item 
     * @param mixed $options 
     * @return string 
     * @throws IGKException 
     */
    public static function GetInnerHtml(HtmlItemBase $item, $options =null){
        $s = "";
        $content = $item->getContent();
        if (!empty($content)){
            if (is_object($content)){
                $s .= HtmlRenderer::GetValue($content, $options);
            }else{
                $s .= $content;
            }
        }
        $childs = $item->getRenderedChilds($options);  
        if (count($childs)>0){
            foreach($childs as $k){
                $s.= self::Render($k, $options);
            }
        }
        return $s;
    }
}