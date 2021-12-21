<?php

namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlItemBase;

use ReflectionMethod;

/**
 * represent base renderer engine
 * @package IGK\System\Html
 */
class HtmlRenderer{
    public static function CreateRenderOptions(){
        $o = (object)[
            "Indent"=>true,
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
            "TextOnly"=>false
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
            (new \IGK\System\Http\WebResponse($doc))->output();            
        
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
                "Depth"=>0
            ] as $k=>$v){
                if(!isset($options->$k)){
                    $options->$k=$v;
                }
            }
        }
        $tab = [
            ["item"=>$item, "close"=>false]
        ]; 
        $s = "";
        $reflect = [];
        // $renderer = null; //igk_getv($options, "renderer") ?? new HtmlRenderer();
        // $engine = null; //igk_getv($options, "engine"); 

        while(($q = array_pop($tab)) && !$options->Stop){
            $tag = null;
            $i = null;
            if (is_array($q))
                $i = $q["item"];
            else {
                $i = $q;
                $q = ["item"=>$i, "close"=>false ];
            }
            // if ($engine){
            //     $engine->render($i, $options);
            //     continue;
            // }
        
            if (!isset($reflect[$cl = get_class($i)])){
                $reflect[$cl] = HtmlItemBase::class != (new ReflectionMethod($i, "render"))->getDeclaringClass()->name;                
            }
            if ($reflect[$cl]){
                $s .= $i->render($options);
                continue;
            } 
            
            if (!$i->AcceptRender($options)){
                continue;
            }            

            if (!$q["close"]){
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
                $have_content = (count($childs) > 0) || !empty($content);         
                $q["close_tag"] =  $have_content || $i->closeTag();
                $q["close"] = true;
                $q["tag"] = $tag;
                if ($havTag && $q["close_tag"]){
                    $s.=">";
                }
                if (!empty($content)){
                    if (is_object($content)){
                        $s .= HtmlRenderer::GetValue($content, $options);
                    }else{
                        $s .= $content;
                    }
                }
                if((count($childs) > 0)){
                    $options->Depth++;
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
            if (!empty($tag)){
                if ($q["close_tag"])
                    $s .= "</".$tag.">";
                else {
                    $s.= "/>";
                } 
            }
            $options->Depth = max(0, $options->Depth -1);
        }
        return $s;
    }

    public static function GetAttributeString(HtmlItemBase $item, $options){      

        $attribs = $item->getAttributes();
        $out = IGK_STR_EMPTY;
        igk_get_defined_ns($item, $out, $options);
        if ($options && ($options->Context == "mail")) {
            //for mail rendering attribures

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
                            $matcher[] = $item->tagName . "." . $k;
                        }
                        if (!empty($id = $item->Id)) {
                            $matcher[] = "#id.$k";
                        }
                        $matcher[] = ".{$k}";
                        foreach ($matcher as $m) {
                            if ($p = $options->renderTheme[$m]) {
                                $g .= rtrim($p, ";") . ";";
                            }
                        }
                    }
                    if ($options->renderTheme) {
                        $g = igk_css_treatstyle($g, $options->renderTheme, $options->renderTheme);
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
            $out .= " " . $s;
        }
        return rtrim($out);
    }

     ///<summary></summary>
    ///<param name="v"></param>
    ///<param name="options"></param>
    /**
    * 
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