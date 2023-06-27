<?php


///<summary>Convert string argument to array list. in context</summary>
///<param name="s">parameter to convert</param>
///<param name="context">context object that will parameter to convert</param>

use IGK\Controllers\BaseController;
use IGK\System\DataArgs;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Helpers\HtmlEngineHelper;
use IGK\System\Html\HtmlLoadingContextOptions;

/**
 * Convert string argument to array list. in context
 * @param mixed $s parameter to convert
 * @param mixed $context context object that will parameter to convert
 */
function igk_engine_get_attr_arg($s, $context = null)
{
    $tb = igk_engine_read_args($s);
    if ((count($tb) == 0) || !is_object($context)) {
        return $tb;
    }
    $m = null;
    if ($context && (is_object($context) || is_array($context))) {
        $__g_context = (array)$context;
        extract($__g_context);
        unset($__g_context);
        $cs = array_keys((array)$context);
        $m = igk_str_join_tab(array_values($cs), '|', false);
        $rgx = "#^\[\[:@(?P<name>((" . $m . ")))(?P<data>(.)+)?\]\]$#i";
        $paramvar_rgx = "#@(?P<name>((" . $m . ")))#i";
        $callback = function ($m, $n) {
            if (isset($m["name"])) {
                return "\$" . $m["name"];
            }
            return "null";
        };
        for ($k = 0; $k < igk_count($tb); $k++) {
            $mk = trim($tb[$k]);
            if (preg_match_all($rgx, $mk, $stt)) {
                $n = $stt['name'][0];
                $d = $stt['data'][0];
                $d = preg_replace_callback($paramvar_rgx, $callback, $d);
                if (!empty($d)) {
                    $s = "\$context->" . $n . $d;
                    $m = "return {$s};";
                    igk_set_env(IGK_LAST_EVAL_KEY, $m);
                    $tb[$k] = @eval($m);
                } else {
                    $tb[$k] = $context->$n;
                }
            } else {
                if (preg_match('/^(\[|array\s*\()/i', $mk)) {

                    if ($gc = @eval("return " . $mk . ";")) {
                        $tb[$k] = $gc;
                    } else {
                        igk_dev_wln_e(__FILE__ . ":" . __LINE__, "Action not available [[:@]] " . $mk, $cs, $gc, $k);
                        $tb[$k] = @eval("return " . $mk . ";");
                    }
                }
            }
        }
    }
    return $tb;
}

///<summary>retrieve argument splitting</summary>
/**
 * retrieve argument splitting
 */
function igk_engine_read_args($s)
{
    if (empty($s))
        return [];
    $s = html_entity_decode($s);
    $args = [];
    $ln = strlen($s);
    $c = 0;
    $v = "";
    while ($c < $ln) {
        $ch = $s[$c];
        switch ($ch) {
            case "'":
            case '"':
                $k = trim($v . igk_str_read_brank($s, $c, $ch, $ch, null, 1));
                if ($k[0] == "@") {
                    $k = substr($k, 2, -1);
                } else {
                    $k = substr($k, 1, -1);
                }
                $v = "";
                $args[] = $k;
                break;
            case "{":
                $args[] = igk_str_read_brank($s, $c, "}", "{");
                $v = "";
                break;
            case "(":
                $v .= igk_str_read_brank($s, $c, ")", "(");
                break;
            case "[":
                $args[] = igk_str_read_brank($s, $c, "]", "[");
                break;
            case ",":
                if (strlen($v = trim($v)))
                    $args[] = $v;
                $v = "";
                break;
            default:
                $v .= $ch;
                break;
        }
        $c++;
    }
    if (strlen($v = trim($v)) > 0)
        $args[] = $v;
    return $args;
}


///<summary>bind attributes</summary>
///<param name="reader"></param>
///<param name="attr"></param>
///<param name="value"></param>
///<param name="context" default="null"></param>
///<param name="storecallback" default="null"></param>
/**
 * get tempory binding attributes
 * @param mixed $reader 
 * @param mixed $attr 
 * @param mixed $value 
 * @param ?array $context context to bind
 * @param mixed $storecallback 
 */
function igk_engine_temp_bind_attribute($reader, $attr, $value, $context = null, $storecallback = null)
{

    if ($context == null) {
        $context = $reader->context;
    }
    //+ copy root content if exists
    $context = igk_get_attrib_raw_context($context);
    if ($context === null) {
        $context = "[context]::" . __FUNCTION__;
    }
    $g = igk_get_template_bindingattributes();
    if (isset($g[$attr])) {
        $inf = $g[$attr];
        // decode with entity - maybe expression
        $value = html_entity_decode($value);
        list($k, $v) = $inf($reader, $attr, $value, $context, $storecallback);
        if ($k && $v && $storecallback) {
            $storecallback($k, $v);
        }
        return true;
    }
    return false;
}

///<summary>Represente igk_get_attrib_raw_context function</summary>
///<param name="n_context"></param>
/**
 * retrieve binding attribute info
 * @param mixed $context 
 */
function igk_get_attrib_raw_context($context)
{
    // + | init root context if exists 
    $o = igk_get_article_root_context();
    if ($o == null) {
        // no root context found - create a binding root info
        if ($context && !($context instanceof \IGK\System\Html\Templates\BindingContextInfo)) {
            return \IGK\Helper\Activator::CreateNewInstance(\IGK\System\Html\Templates\BindingContextInfo::class, $context);
        }
        return $context;
    }
    $raw = null;
    if (is_object($context) && property_exists($context, 'raw')) {
        $raw = is_array($context->raw) && array_key_exists("raw", $context->raw) ? $context->raw["raw"] : $context->raw;
    } else {
        if (is_array($context)) {
            $raw = igk_getv($context, "raw");
        }
        // esle passing root context
    }
    if (is_array($o)) {
        return ['raw' => $o]; //IGKRawDataBinding::Create($o);
    }
    return [
        "raw" => $raw,
        "root_context" => (object)[
            "ctrl" => $o->ctrl,
            "raw" => IGKRawDataBinding::Create($o->raw)
        ],
        "ctrl" => igk_getv($context, 'ctrl'),
        "transformToEval" => igk_getv($context, 'transformToEval') , // $o->transformToEval,
        "key" => igk_getv($context, 'key'),
        "type" => igk_getv($context, 'type'),
    ];
}


///<summary>article priority root context</summary>
/**
 * get root data stored to article chain
 * @return ?object|array|mixed root context
 */
function igk_get_article_root_context()
{
    $g = igk_get_env(IGKEnvironmentConstants::ARTICLE_CHAIN_CONTEXT);
    if (is_array($g) && (count($g) > 0)) {
        $c = igk_getv($g[0], "data");
        return $c;
    }
    return null;
}
///<summary>get current article chain data</summary>
/**
 * get current article chain data
 */
function igk_get_article_chain()
{
    $g = igk_get_env(IGKEnvironmentConstants::ARTICLE_CHAIN_CONTEXT);
    if (($c = count($g)) > 0) {
        return $g[$c - 1];
    }
    return null;
}
///<summary></summary>
///<param name="f"></param>
/**
 * 
 * @param mixed $f 
 */
function igk_pop_article_chain()
{
    $g = igk_get_env($key = IGKEnvironmentConstants::ARTICLE_CHAIN_CONTEXT);
    array_pop($g);
    igk_set_env($key, $g);
}
///<summary>push article in chain data</summary>
/**
 * push article in chain data
 * @var string $f context identification 
 * @var ?array|HtmlLoadingContextOptions $context definition
 */
function igk_push_article_chain(string $f, $context = null)
{
    $key = IGKEnvironmentConstants::ARTICLE_CHAIN_CONTEXT;
    $raw_var = IGKConstants::RAW_VAR;
    $ctx =  $context;
    $b = igk_get_env($key);
    if ( (!is_null($context)) && (!$b || (count($b) == 0))) {        
        if ($ctx instanceof HtmlLoadingContextOptions) {
            $raw = $ctx->raw;
            if (is_array($raw)) {
                if (array_key_exists($raw_var, $raw)) {
                    $r = igk_getv($raw, $raw_var);
                    unset($ctx->$raw_var[$raw_var]);
                    $ctx->raw = array_merge((array)$ctx->$raw_var, [$raw_var => $r]);
                }
            }
        } else {
            // TODO : fix logic
        }
    }
    igk_set_env_array($key, new \IGK\System\Articles\ChainInfo($f, $ctx)); //  ["n" => $f, "data" => $ctx]);
}
///<summary>get template binding attribute</summary>
/**
 * get template binding attribute
 */
function igk_get_template_bindingattributes()
{
    static $binding = null;
    if (!($o = igk_get_env($key = "sys://template/bindingProperties"))) {
        if (($binding === null) && file_exists($file = IGK_LIB_DIR . "/Inc/igk_default_template_register.php")) {
            include_once($file);
            $binding = 1;
            $o = igk_get_env($key);
        }
    }
    return $o;
}

///<summary>register template binding attributes</summary>
///<param name="$name">comma separated string of identifier for binding attribute</param>
///<param name="$callback">the callback</summary>
/**
 * register template binding attributes
 * @param mixed $$name comma separated string of identifier for binding attribute
 * @param mixed $$callback the callback
 */
function igk_reg_template_bindingattributes($name, $callback)
{
    $key = "sys://template/bindingProperties";
    if (!($g = igk_get_env($key))) {
        $g = array();
    }
    foreach (array_filter(explode(",", strtolower($name))) as $k) {
        $g[trim($k)] = $callback;
    }
    igk_set_env($key, $g);
}


if (!function_exists('igk_engine_html_load_content')) {
    /**
     * helper: article bind content
     * @param HtmlItemBase $node 
     * @param string $content 
     * @param mixed $args 
     * @return void 
     * @throws IGKException 
     */
    function igk_engine_html_load_content(HtmlItemBase $node, string $content, $args, ?BaseController $ctrl = null)
    {
        HtmlEngineHelper::BindContent($node, $content, $args, $ctrl);
    }
}

if (!function_exists('igk_engine_eval_pipe')) {
    /**
     * helper: evaluate pipe expression in context defition 
     * @param string $pipe expression
     * @param int position of the first separator
     */
    function igk_engine_eval_pipe(string $pipe, int $pos, array $tab, string $startMarker = '{{', string $endMarker = '}}')
    {
        $n = substr($pipe, 0, $pos);
        $ln = strlen($pipe);
        $v = '';
        while ($pos < $ln) {
            $npos = strpos($pipe,  $endMarker, $pos);
            if ($npos !== false) {
                $v = substr($pipe, $pos + 2, $npos - $pos - 2);
                $n .= igk_php_eval_in_context($v, $tab);
                $pos = $npos + 2;
                $npos = strpos($pipe, $startMarker, $pos);
                if ($npos === false) {
                    $n .= substr($pipe, $pos);
                    break;
                }
            } else {
                $n .= substr($pipe, $pos);
                break;
            }
            $pos++;
        }
        return $n;
    }
}
