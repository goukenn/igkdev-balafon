<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlRenderer.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

use Exception;
use IGK\Controllers\ControllerEnvParams;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlExpressionNode;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Http\IHeaderResponse;
use IGKApp;
use IGKException;
use IGKHtmlDoc;
use ReflectionException;
use ReflectionMethod;

function rgtrim($v)
{
    igk_wln("render:" . $v);
    return "-=" . $v;
}
/**
 * represent base renderer engine
 * @package IGK\System\Html
 */
class HtmlRenderer
{

    /**
     * append after rendering element
     * @param mixed $option 
     * @param mixed $node 
     * @return void 
     * @throws IGKException 
     */
    public static function AppendOptionNode($option, $node)
    {
        if (!($c = igk_getv($option, "__append__"))) {
            $c = [];
            $option->{"__append__"} = $c;
        }
        array_push($option->__append__, $node);
    }
    /**
     * 
     * @return object|IHtmlRenderOptions
     * @throws IGKException 
     */
    public static function CreateRenderOptions()
    {
        $o = new HtmlRendererOptions;
        $o->Cache = igk_sys_cache_require();
        if ($o->Cache) {
            $o->CacheUri = base64_decode(igk_sys_cache_uri());
            $o->CacheUriLevel = explode("/", $o->CacheUri);
        }
        return $o;
    }
    public static function GetValue($o, $options = null)
    {
        if ($o instanceof IHtmlGetValue) {
            return $o->getValue($options);
        }
        if ($o instanceof HtmlItemBase) {
            return $o->render($options);
        }
    }
    ///<summary>force to render global html document</summary>
    /**
     * force to render global html document
     */
    public static function RenderDocument($doc = null, $refreshDefault = 1, $ctrl = null)
    {
        //igk_wln_e("bind:ing");
        $igk = igk_app();
        $doc = $doc ?? $igk->getDoc();
        if ($refreshDefault) {
            $ctrl = $ctrl ?? igk_get_defaultwebpagectrl();
            if ($ctrl && (igk_environment()->get(IGK_KEY_FORCEVIEW) !== 1)) {
                if (!igk_environment()->get($key = "sys://tempdata")) {
                    igk_environment()->set($key, 1);
                }
                if (!igk_environment()->get(IGK_ENV_PAGEFOLDER_CHANGED_KEY)) {
                    $ctrl->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
                    $ctrl->setEnvParam('render_context', 'docview');
                    // gourou d'etranglement
                    $bbox = $doc->getBody()->getBodyBox()->clearChilds();
                    if ($t = $ctrl->getTargetNode()) {
                        $bbox->add($t);
                        $ctrl->{ControllerEnvParams::NoCompilation} = 1;
                        $ctrl->View();
                    }
                }
            }
        }
        // -------------------
        // + | Render document
        // -------------------  
        self::OutputDocument($doc);
    }
    /**
     * output the document
     * @param IGKHtmlDoc $doc 
     * @return void 
     * @throws IGKException 
     */
    public static function OutputDocument(IGKHtmldoc $doc)
    {
        $headers = [];
        if ($doc instanceof IHeaderResponse) {
            $headers = array_merge($headers, $doc->getResponseHeaders() ?? []);
        }
        //igk_dev_wln_e(__FILE__.":".__LINE__,  "data ", $headers);
        $response = new \IGK\System\Http\WebResponse($doc, 200, $headers);
        $response->cache = !igk_environment()->no_cache && igk_configs()->allow_page_cache;
        $response->output();
    }
    public static function SanitizeOptions($options)
    {
        if (!isset($options->{':sanitize'})) {
            $options->{':sanitize'} = 1;
        } else {
            return;
        }
        foreach ([
            "Stop" => 0,
            "Context" => "XML",
            "Depth" => 0,
            "Indent" => false,
            "header" => null,
        ] as $k => $v) {
            if (!isset($options->$k)) {
                $options->$k = $v;
            }
        }
    }
    public static function DefOptions(&$options = null)
    {
        if ($options == null) {
            $options = self::CreateRenderOptions();
        } else {
            // sanitize options property
            self::SanitizeOptions($options);
        }
        $options->LF = $options->Indent ? "\n" : "";
        $options->__invoke = [];
    }
    /**
     * retrieve tab stop
     * @param mixed|XmlRenderOptions $options 
     * @return string 
     */
    public static function GetTabStop($options)
    {
        $s = "";
        if ($options && $options->Indent) {
            return str_repeat("\t", $options->Depth);
        }
        return $s;
    }
    public static function UpdateInvoke(string $method, $options)
    {
        if (!isset($options->__invoke[$method])) {
            $options->__invoke[$method] = 1;
        } else {
            $options->__invoke[$method]++;
        }
    }
    private static function _GetHeader($o): ?string
    {
        if (is_string($o)) {
            return $o;
        }
        if (is_object($o)) {
            return self::GetValue($o);
        }
    }
    private static function reduceDepth($options, $tag = null)
    {

        $options->Depth = max(0, $options->Depth - 1);
        // igk_debug_wln("\nreduct to : ".$options->Depth.": ".$tag);
    }
    /**
     * a way to render node
     */
    public static function Render(HtmlItemBase $item, $options = null)
    {
        // + | render option definition
        self::DefOptions($options);
        $tab = [
            ["item" => $item, "close" => false]
        ];
        $options->Source = $item;
        //count the parent invoker
        self::UpdateInvoke(__METHOD__, $options);
        $s = "";
        $reflect = [];
        $ln = $options->LF;
        $engine = igk_getv($options, "Engine");
        $tab_start = false;
        if ($options->header) {
            $s = self::_GetHeader($options->header);
            $options->header = null;
        }

        while ((count($tab) > 0) && !$options->Stop) {
            if (!($q = array_pop($tab))) {
                continue;
            }
            $tag = null;
            $i = null;
            if (is_array($q))
                $i = $q["item"];
            else {
                $i = $q;
                $q = ["item" => $i, "close" => false];
            }
            if (!$q["close"]) {
                if ($ln && ($options->Depth > 0) && !$tab_start) {
                    $s .= self::GetTabStop($options);
                    $tab_start = true;
                }

                if ($i instanceof HtmlItemBase) {
                    if (!$i->AcceptRender($options)) {
                        continue;
                    }
                    if (isset($options->__append__)) {
                        $tab = array_merge($tab, $options->__append__);
                        unset($options->__append__);
                    }
                }

                if ($engine) {
                    $s .= $engine->render($i, $options);
                    self::reduceDepth($options, 'engine');
                    continue;
                }
                if ($options->Source !== $i) {
                    if (!isset($reflect[$cl = get_class($i)])) {
                        $reflect[$cl] = HtmlItemBase::class != (new ReflectionMethod($i, "render"))->getDeclaringClass()->name;
                    }
                    if ($reflect[$cl]) {
                        $options->lastRendering = $i;
                        if (!empty($v_c = $i->render($options))) {
                            if (is_object($v_c )){
                                igk_dev_wln_e("object return ", get_class($i), $v_c);
                            }
                            $s .=  $v_c . $ln;
                        }
                        self::reduceDepth($options, 'reflec_class');
                        continue;
                    }
                }
                $options->lastRendering = $i;
                $tag = $i->getCanRenderTag($options) ? $i->getTagName($options) : "";
                $havTag = !empty($tag);
                $tab_start = false;
                if (!$havTag) {
                    self::reduceDepth($options, 'notagnode');
                    $s = rtrim($s) . $ln . self::GetTabStop($options);
                }

                if ($havTag) {
                    $s .= "<" . $tag . "";
                    // render attribute 
                    if (!empty($attr = static::GetAttributeString($i,  $options))) {
                        $s .= " " . rtrim($attr);
                    }
                }
                $options->Depth++;
                $content = $i->getContent($options);
                $childs = $i->getRenderedChilds($options);

                // if (!empty($content))
                // igk_debug_wln("render :".$content);

                if (property_exists($options, "aside")) {

                    // if (!is_array($options->aside)) {
                    // }
                    $rf = array_reverse($options->aside);
                    $tab = array_merge($tab, $rf);
                    unset($options->aside, $rf);
                    //igk_wln_e("bind aside");
                }
                $have_childs = $childs && (count($childs) > 0);
                $have_content = $have_childs || !empty($content);
                $q["close_tag"] =  $have_content || $i->closeTag();
                $q["close"] = true;
                $q["tag"] = $tag;
                $q["have_childs"] = $have_childs;
                if ($havTag && $q["close_tag"]) {
                    $s = rtrim($s) . ">";
                }
                if (!empty($content) || is_numeric($content)) {
                    if (is_object($content)) {
                        $s .= HtmlRenderer::GetValue($content, $options);
                    } else {
                        if (is_array($content)) {
                            $s .= json_encode($content, JSON_UNESCAPED_SLASHES);
                        } else
                            $s .= $content;
                    }
                }

                if ($have_childs) {
                    if ($havTag)
                        $s .= $ln;
                    array_push($tab, $q);
                    $childs = array_reverse($childs);
                    $tab = array_merge($tab, $childs);
                    continue;
                }
            } else {
                $tag = $q["tag"];
            }
            if (!empty($tag)) {
                self::reduceDepth($options);
                if ($q["close_tag"]) {
                    if ($ln && $q["have_childs"] && ($options->Depth > 0)) {
                        $s = rtrim($s) . $ln . self::GetTabStop($options);
                    }
                    $s .=  "</" . $tag . ">" . $ln;
                } else {
                    $s .= "/>" . $ln;
                }
            }
        }
        return $s; // leave space after
    }
    public static function MailThemeRendering(HtmlItemBase $item, &$attribs = [],  $options = null)
    {
        //for mail rendering attribures
        if (!isset($options->renderTheme)) {
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
                        $matcher[] = $tagname . "." . $k;
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
                    $g = igk_css_treat($g, false, $options->renderTheme, null);
                }
            }
            if (!empty($g)) {
                $item->setStyle("{$g}");
            }
        }
    }
    /**
     * 
     * @param HtmlItemBase $item 
     * @param mixed $options 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetAttributeString(HtmlItemBase $item, $options)
    {
        $filter = $item->getPrefilterAttribute();
        $attribs = $item->getAttributes();
        if ($filter && $attribs){
            $v_fattribs = new HtmlFilterAttributeArray($attribs);
            $attribs = $filter->filter($v_fattribs); 
        }

        $out = IGK_STR_EMPTY;
        igk_get_defined_ns($item, $out, $options);
        if ($options && ($options->Context == "mail")) {
            self::MailThemeRendering($item, $attribs, $options);
        }   
        if ($item->getHasAttributes()) {
            $v_attrib = $attribs;  
            if (is_array($v_attrib)) {
                igk_dev_wln_e(
                    __FILE__ . ":" . __LINE__,
                    "attributes is an is array. contrustor missing initialize",
                    get_class($item),
                    $v_attrib
                );
                $attribs = $v_attrib;
            } else {
                $attrs = $v_attrib->to_array();
            }
            if (!empty($out)) {
                $out .= " ";
            }
            $out .= self::GetAttributeArrayToString($attrs , $options);
           
        }
        $event = $item->getFlag(HtmlItemBase::EVENTS);
        if ($event) {
            $s = "";
            foreach ($event as $k => $v) {
                $s .= $v->getValue() . " ";
            }
            $out .= $s;
        }
        return  rtrim($out);
    }
    public static function GetAttributeArrayToString($attrs , $options=null){
        $out = "";
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
                    igk_environment()->isDev() && igk_trace();
                    igk_dev_wln_e("/!\\ don't send array [$k] as attribute: ", $k, $v);
                }
                if ($v_is_obj && ($v instanceof IHtmlGetValue)) {
                    if (!empty($cv = $v->getValue($options)) || is_string($cv)) {
                        $out .= $k . "=\"" . $cv . "\" ";
                    }
                    continue;
                } else {
                    $c = static::GetStringAttribute($v, $options);
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
        return $out;
    }

    /**
     * return attribute array 
     * @param HtmlItemBase $item 
     * @param mixed $options 
     * @return array 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     */
    public static function GetAttributeArray(HtmlItemBase $item, $options=null):array{
        $attribs = $item->getAttributes();
     
        $_result = [];
        $k = null;
        $v = null;
        igk_get_defined_ns($item, $out, $options);
        if ($options && ($options->Context == "mail")) {
            self::MailThemeRendering($item, $attribs, $options);
        }   
        if ($item->getHasAttributes()) {
            $v_attrib = $item->getAttributes();
            if (is_array($v_attrib)) {
                igk_dev_wln_e(
                    __FILE__ . ":" . __LINE__,
                    "attributes is an array. constructor missing initialize",
                    get_class($item),
                    $v_attrib
                );
                $attribs = $v_attrib;
            } else {
                $attrs = $v_attrib->to_array();
            }            
            foreach ($attrs as $k => $v) {
                if (($k == "@activated") && is_array($v)) {
                    //$out .= " ";
                    foreach ($v as $ak => $av) {
                        // $out .= $ak . " ";
                        $_result[$ak] = $ak;
                    }
                    continue;
                }
                $v_is_obj = is_object($v);
                if ($v_is_obj && ($v instanceof HtmlActiveAttrib)) {
                    // if(!empty($out))
                    //     $out .= " ";
                    // $out .= $k . " ";
                    $_result[$k] = $k.'';
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
                        if (!empty($cv = $v->getValue()) || is_string($cv)) {
                            // $out .= $k . "=\"" . $cv . "\" ";
                            $_result[$k] = static::GetStringAttribute($cv, $options);
                        }
                        continue;
                    } else {
                        $c = static::GetStringAttribute($v, $options);
                    }
                }
                if (is_numeric($c) || !empty($c)) {
                    // if(!empty($out))
                    //     $out .= " ";
                    if ($options && !$r && igk_getv($options, "DocumentType") == 'xml') {
                        $c = str_replace('&', '&amp;', $c);
                    }
                    if ($r) {
                        $_result[$c] = $c;
                    } else
                        $_result[$k] = $c;
                }
            }
        }
        $event = $item->getFlag(HtmlItemBase::EVENTS);
        if ($event) {
            $s = "";
            foreach ($event as $k => $v) {
                $_result[] = $v->getValue();
            } 
        }
        return  $_result;
    }


    ///<summary>get attribute string</summary>
    ///<param name="v"></param>
    ///<param name="options"></param>
    /**
     * get attribute string
     * @param mixed $v
     * @param mixed $options
     */
    public static function GetStringAttribute($v, $options)
    {
        if (is_bool($v)) {
            return sprintf("\"%s\"", $v ? "true" : "false");
        }
        if (empty($v) && !is_numeric($v))
            return null;

        while (is_object($v)) {
            $v = HtmlUtils::GetValueObj($v, $options);
        }
        if (empty($v) && !is_numeric($v)) {
            return null;
        }
        if (is_string($v)) {
            if (strpos($v, "\"") === 0) {
                return $v;
            }
            if (strpos($v, "\'") === 0)
                return $v;
        }
        if (!igk_getv($options, "flag_no_attrib_escape")) {
            if ($options && igk_getv($options, "AttributeEntityEscape")) {
                $v = preg_replace_callback(
                    "/\&([^;=]+;)?/i",
                    function ($m) {
                        switch ($m[0]) {
                            case "&":
                                return "&amp;";
                            case "&copy;":
                                return "&#169;";
                        }
                        return $m[0];
                    },
                    $v
                );
            }
            $v = str_replace("\"", "&quot;", $v);
            if (is_array($v)) {
                igk_wln_e(__METHOD__ . "::attribute is array", igk_show_trace());
            }
        } else {
            $v = str_replace("\"", "\\\"", $v);
        }
        // clear flag for setting attribute
        unset($options->flag_no_attrib_escape);
        return "\"" . $v . "\"";
    }
    ///<summary>get node item inner content</summary>
    /**
     * get node item inner content
     * @param HtmlItemBase $item 
     * @param mixed $options 
     * @return string 
     * @throws IGKException 
     */
    public static function GetInnerHtml(HtmlItemBase $item, $options = null)
    {
        $s = "";
        $content = $item->getContent();
        if (!empty($content)) {
            if (is_object($content)) {
                $s .= HtmlRenderer::GetValue($content, $options);
            } else {
                $s .= $content;
            }
        }
        $childs = $item->getRenderedChilds($options);
        if (count($childs) > 0) {
            foreach ($childs as $k) {
                $s .= self::Render($k, $options);
            }
        }
        return $s;
    }
}
