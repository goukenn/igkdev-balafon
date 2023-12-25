<?php

// @file: igk_functions.php
// @author: C.A.D. BONDJE DOUE
// @description: global useage function
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Actions\Dispatcher;
use IGK\App\Bondje\Models\DbUtility;
use IGK\Controllers\BaseController;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;
use IGK\System\IO\Path;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\Cache\SystemFileCache as IGKSysCache;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\ControllerEnvParams;
use IGK\Controllers\ControllerTypeBase;
use IGK\Controllers\OwnViewCtrl;
use IGK\Css\CssThemeOptions;
use IGK\Css\ICssResourceResolver;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbExpression;
use IGK\Database\DbQueryDriver;
use IGK\Database\DbQueryResult;
use IGK\Database\DbSchemas;
use IGK\Helper\ActionHelper;
use IGK\Helper\Activator;
use IGK\System\Compilers\BalafonCacheViewCompiler;
use IGK\System\Diagnostics\Benchmark;
use IGK\System\Html\Css\CssStyle;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlAJXCtrlReplacementNode;
use IGK\System\Html\Dom\HtmlAJXReplacementNode;
use IGK\System\Html\Dom\HtmlComponentIdValue;
use IGK\System\Html\Dom\HtmlCssClassValueAttribute;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlNotifyDialogBoxItem;
use IGK\System\Html\Dom\HtmlProcessInstructionNode;
use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\Html\Dom\HtmlUri;
use IGK\System\Html\Dom\IGKHtmlMailDoc;
use IGK\System\Html\FormBuilderEngine;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlMetaManager;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\InvalidXmlReadException;
use IGK\System\Html\XML\XmlCDATA;
use IGK\System\Html\XML\XmlNode;
use IGK\System\Http\JsonResponse;
use IGK\System\Number;
use IGK\System\WinUI\Menus\MenuItemObject;
use IGK\XML\XMLNodeType;
use IGK\System\Drawing\SysColor as sysCL;

use function igk_resources_gets as __;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper;
use IGK\Server;
use IGK\System\Console\Commands\SitemapGeneratorCommand;
use IGK\System\Console\Commands\Sync\SyncProjectSettings;
use IGK\System\Console\Logger;
use IGK\System\Core\IProxyDataArgs;
use IGK\System\DataArgs;
use IGK\System\Html\Dom\HtmlCoreJSScriptsNode;
use IGK\System\Html\Dom\SvgListNode;
use IGK\System\Html\HtmlLoadingContext;
use IGK\System\Html\HtmlLoadingContextOptions;
use IGK\System\Http\Cookies;
use IGK\System\Http\RequestHandler;
use IGK\System\Http\WebResponse;
use IGK\System\IO\CSV\Helper\CSVHelper;
use IGK\System\Regex\Replacement;

///<summary></summary>
/**
 * 
 */
function igk_agent_androidversion()
{
    return IGKUserAgent::GetAndroidVersion();
}
///<summary></summary>
/**
 * 
 */
function igk_agent_ieversion()
{
    if (igk_agent_isie()) {
        $tab = array();
        $c = preg_match_all("/(?<type>(MSIE|Trident|Edge))(\/)?\s*(?P<version>[0-9\.]+)/i", $_SERVER["HTTP_USER_AGENT"], $tab);
        if ($c >= 1) {
            $t = igk_getv($tab["type"], 0);
            $v = igk_getv($tab["version"], 0);
            switch ($t) {
                case 'Trident':
                    return "11.0/" . $v;
                case 'Edge':
                    return "12.0/" . $v;
                case 'MSIE':
                default:
                    return $v;
            }
        }
    }
    return null;
}
///<summary></summary>
/**
 * 
 */
function igk_agent_isandroid()
{
    return IGKUserAgent::IsAndroid();
}
///<summary></summary>
/**
 * 
 */
function igk_agent_isie()
{
    return IGKUserAgent::IsIE();
}
///<summary>close a visible notify dialog</summary>
/**
 * close a visible notify dialog
 */
function igk_ajx_close_dialog()
{
    $s = igk_create_node("BalafonJS");
    $s->Content = "igk.balafonjs.utils.closeNotify(1);";
    $s->renderAJX();
}
///<summary></summary>
/**
 * 
 */
function igk_ajx_exit()
{
    if (igk_is_ajx_demand())
        igk_exit();
}
///<summary></summary>
///<param name="script"></param>
/**
 * 
 * @param mixed $script 
 */
function igk_ajx_include_script($script)
{
    if (igk_is_ajx_demand()) {
        $b = igk_create_node("balafonJS");
        $b->Content = "igk.js.load('{$script}');";
        $b->renderAJX();
    }
}
///<summary></summary>
///<param name="lnk"></param>
/**
 * 
 * @param mixed $lnk 
 */
function igk_ajx_link($lnk)
{
    if (igk_is_ajx_demand() || igk_is_webapp()) {
        $g = igk_io_basepath($lnk);
        // if (is_null($g)){
        //     igk_dev_wln_e("base path failed ".$lnk, $g);
        // }
        $p = "/" . (!is_null($g) ?  ltrim($g, "/") : ltrim($lnk, "/"));
        $u = igk_io_baseuri() . $p;
        return igk_uri($u);
    }
    return null;
}
///<summary></summary>
/**
 * 
 */
function igk_ajx_notify_close_dialog()
{
    $n = igk_create_node();
    $n->script()->Content = <<<EOF
ns_igk.winui.notify.close();
EOF;

    $n->renderAJX();
}
///<summary> shortcut to notify dialog</summary>
/**
 *  shortcut to notify dialog
 */
function igk_ajx_notify_dialog($title, $content, $type = "default", $render = true)
{
    $notbox = new HtmlNotifyDialogBoxItem();
    $notbox->setClass("igk-" . $type);
    $notbox->show($title, $content);
    if ($render) {
        $s = $notbox->renderAJX();
    }
    return $notbox;
}
///<summary></summary>
///<param name="title"></param>
///<param name="uri"></param>
///<param name="callbacks"></param>
///<param name="method" default="POST"></param>
/**
 * 
 * @param mixed $title 
 * @param mixed $uri 
 * @param mixed $callbacks 
 * @param mixed $method 
 */
function igk_ajx_notify_dialog_callback($title, $uri, $callbacks, $method = "POST")
{
    if (igk_qr_confirm()) {
        $type = igk_getr("dialog-type", "confirm");
        ($fc = igk_getv($callbacks, $type)) && $fc();
        return;
    }
    $fc = igk_getv($callbacks, "form") ?? igk_die(__("no callback defined"));
    $dv = igk_create_node("form");
    $dv["action"] = $uri;
    $dv["method"] = $method;
    $dv->addObData(function () use ($fc, $dv) {
        $fc($dv);
    });
    igk_html_add_confirm($dv);
    igk_ajx_notify_dialog($title, $dv);
    igk_exit();
}
///<summary>create inline panel dialog for ajx context</summary>
///<param name="title"></param>
///<param name="d"></param>
///<param name="closeBtn" default="'drop'"> mixed(string|array)  </param>
///<param name="callback" default="null"></param>
/**
 * create inline panel dialog for ajx context
 * @param string $title 
 * @param mixed $content data will be show 
 * @param mixed $closeBtn  mixed(string|array) 
 * @param mixed $callback 
 */
function igk_ajx_panel_dialog(string $title, $content, $closeBtn = 'drop', $callback = null, $render = true)
{
    $dv = igk_create_node('div');
    $dv['class'] = 'igk-ajx-panel-dialog-container';
    $dialog = $dv->paneldialog($title, $content, is_array($closeBtn) ? $closeBtn : ["closeBtn" => $closeBtn]);
    if (is_array($closeBtn)) {
        unset($closeBtn['closeBtn']);
        $dialog->setAttributes($closeBtn);
    }
    if (is_callable($callback)) {
        $callback($dialog);
    }
    if ($render)
        $dialog->renderAJX();
    return $dv;
}
///<summary></summary>
/**
 * 
 */
function igk_ajx_panel_dialog_close()
{
    $n = igk_create_node("BalafonJS");
    $n["autoremove"] = 1;
    $n->Content = "igk.winui.controls.panelDialog.close()";
    $n->renderAJX();
}
///<summary>return a panel dialog result</summary>
/**
 * return a panel dialog result
 */
function igk_ajx_panel_dialog_result($title, $d, $closeBtn = 'drop',  ?callable $callback = null)
{
    ob_start();
    igk_ajx_panel_dialog($title, $d, $closeBtn, $callback);
    return new IGK\System\Http\WebResponse(ob_get_clean());
}
///<summary>redirect to uri in ajx context</summary>
/**
 * redirect to uri in ajx context
 */
function igk_ajx_redirect($uri = null)
{
    if (igk_is_ajx_demand()) {
        $uri = $uri == null ? igk_sys_srv_referer() : $uri;
        igk_navto($uri);
    }
}
///<summary></summary>
///<param name="tab"></param>
///<param name="tagname" default="response"></param>
///<param name="type" default="IGK_CT_PLAIN_TEXT"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $tagname 
 * @param mixed $type 
 */
function igk_ajx_render_response($tab, $tagname = "response", $type = IGK_CT_PLAIN_TEXT)
{
    $r = igk_create_node($tagname);
    if (is_array($tab)) {
        foreach ($tab as $k => $v) {
            $r->addNode($k)->Content = $v;
        }
    }
    if ($type !== IGK_CT_PLAIN_TEXT) {
        header("Content-Type:" . $type);
    }
    $r->renderAJX();
}
///<summary>Replace controller view</summary>
///<note> it call view replace controller view.</note>
///<param name="ctrl"> controller to use in replacement strategie.</param>
///<param name="view"> demand to call the current view.</param>
/**
 * Replace controller view
 * @param mixed $ctrl  controller to use in replacement strategie.
 * @param mixed $view  demand to call the current view.
 */
function igk_ajx_replace_ctrl_view($ctrl, $view = 1)
{
    if (!$ctrl)
        return;
    if ($view) {
        $ctrl->setCurrentView($ctrl->MainView);
    }
    $c = new HtmlAJXCtrlReplacementNode();
    $c->addCtrl($ctrl);
    $c->renderAJX();
    $ctrl->regSystemVars(null);
}
///<summary>render a replacement node</summary>
///<param name="n">node to replace in ajx context</param>
///<param name="target">id to target</param>
///<param name="hash">hash to pass to ajx context</param>
/**
 * render a replacement node
 * @param mixed $n node to replace in ajx context
 * @param mixed $target id to target
 * @param mixed $hash hash to pass to ajx context
 */
function igk_ajx_replace_node($n, $target = null, $hash = null, $render = true)
{
    $c = new HtmlAJXReplacementNode();
    $c["target"] = $target;
    $c["hash"] = $hash;
    $c->addNode($n);
    if ($render)
        $c->renderAJX();
    return $c;
}
///<summary></summary>
///<param name="uri"></param>
/**
 * 
 * @param mixed $uri 
 */
function igk_ajx_replace_uri($uri)
{
    $n = igk_create_node('balafonJS');
    $n["autoremove"] = 1;
    $n->Content = "ns_igk.winui.history.replace('{$uri}', null); ";
    $n->renderAJX();
    return $n;
}
///<summary></summary>
///<param name="msg"></param>
///<param name="classtype" default="null"></param>
///<param name="noclose"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $classtype 
 * @param mixed $noclose 
 */
function igk_ajx_toast($msg, $classtype = null, $noclose = 0)
{
    $ajx = igk_create_node("toast");
    if ($classtype != null)
        $ajx["class"] = "+" . $classtype;
    if ($noclose) {
        $ajx["noHide"] = 1;
    }
    $ajx->Content = $msg;
    $ajx->renderAJX();
}
///<summary>Represente igk_ajx_update function</summary>
///<param name="uri"></param>
///<param name="target" default="'body'"></param>
///<param name="type" default="'get'"></param>
/**
 * Represente igk_ajx_update function
 * @param mixed $uri 
 * @param mixed $target 
 * @param mixed $type 
 */
function igk_ajx_update($uri, $target = 'body', $type = 'get')
{
    if ($type != 'get') {
        $type = 'post';
    }
    if ($target == 'body') {
        $target = 'null';
    } else {
        $target = "'$target'";
    }
    igk_create_node("script")->setAttribute("autoremove", "true")->setContent("ns_igk.ajx.{$type}('$uri', null, {$target});")->renderAJX();
}
///<summary>show an alert to document information </summary>
///<remark>document must be fully loaded. can be uses at the InitComplete </remark>
/**
 * show an alert to document information 
 */
function igk_alert($msg)
{
    $frame = igk_html_frame(igk_getctrl(IGK_FRAME_CTRL), "alert_frame");
    $frame->Title = __("title.alert");
    $frame->BoxContent->clearChilds();
    $frm = $frame->BoxContent->addForm();
    $frm->div()->Content = $msg;
}
///<summary></summary>
///<param name="args"></param>
/**
 * 
 * @param mixed $args 
 */
function igk_android_onrenderdoc($args)
{
    $doc = $args->args[0];
    $options = $args->args[1];
    $v_isandroid = igk_agent_isandroid();
    if ($options->Cache) {
        $js = igk_create_node("Script");
        $js->Content = "igk.android.init('width=device-width, initial-scale=1, user-scalable=no');";
        $m = new HtmlSingleNodeViewerNode($js);
        $doc->body->add($m);
    } else {
        if ($options->Context == HtmlContext::Html) {
            $sm = $doc->getMetas();
            if ($sm) {
                $meta = $sm->getMetaById("_page_viewport");
                if ($meta == null) {
                    $meta = igk_create_node("meta");
                    $meta["name"] = "viewport";
                }
                if ($v_isandroid) {
                    //+ | accesibility for android mobile = light root guide
                    $meta[HtmlMetaManager::ATTR_CONTENT] = "width=device-width, initial-scale=1, maximum-scale=6, user-scalable=yes";
                } else {
                    $meta[HtmlMetaManager::ATTR_CONTENT] = "initial-scale=1";
                }
                $sm->addMeta("_page_viewport", $meta);
            } else {
                igk_die("No meta defined for  document");
            }
            if ($v_isandroid) {
                $doc->body["class"] = "+igk-android";
            } else {
                $doc->body["class"] = "-igk-android";
            }
        }
    }
}
///<summary>replace string to utf8</summary>
///<param name="text"></param>
/**
 * replace string to utf8
 * @param mixed $text 
 */
function igk_ansi2utf8($text)
{
    $text = str_replace("¡", "\xc2\xa1", $text);
    $text = str_replace("¢", "\xc2\xa2", $text);
    $text = str_replace("£", "\xc2\xa3", $text);
    $text = str_replace("¤", "\xc2\xa4", $text);
    $text = str_replace("¥", "\xc2\xa5", $text);
    $text = str_replace("¦", "\xc2\xa6", $text);
    $text = str_replace("§", "\xc2\xa7", $text);
    $text = str_replace("¨", "\xc2\xa8", $text);
    $text = str_replace("©", "\xc2\xa9", $text);
    $text = str_replace("ª", "\xc2\xaa", $text);
    $text = str_replace("«", "\xc2\xab", $text);
    $text = str_replace("¬", "\xc2\xac", $text);
    $text = str_replace("­", "\xc2\xad", $text);
    $text = str_replace("®", "\xc2\xae", $text);
    $text = str_replace("¯", "\xc2\xaf", $text);
    $text = str_replace("°", "\xc2\xb0", $text);
    $text = str_replace("±", "\xc2\xb1", $text);
    $text = str_replace("²", "\xc2\xb2", $text);
    $text = str_replace("³", "\xc2\xb3", $text);
    $text = str_replace("´", "\xc2\xb4", $text);
    $text = str_replace("µ", "\xc2\xb5", $text);
    $text = str_replace("¶", "\xc2\xb6", $text);
    $text = str_replace("·", "\xc2\xb7", $text);
    $text = str_replace("¸", "\xc2\xb8", $text);
    $text = str_replace("¹", "\xc2\xb9", $text);
    $text = str_replace("º", "\xc2\xba", $text);
    $text = str_replace("»", "\xc2\xbb", $text);
    $text = str_replace("¼", "\xc2\xbc", $text);
    $text = str_replace("½", "\xc2\xbd", $text);
    $text = str_replace("¾", "\xc2\xbe", $text);
    $text = str_replace("¿", "\xc2\xbf", $text);
    $text = str_replace("À", "\xc3\x80", $text);
    $text = str_replace("Á", "\xc3\x81", $text);
    $text = str_replace("Â", "\xc3\x82", $text);
    $text = str_replace("Ã", "\xc3\x83", $text);
    $text = str_replace("Ä", "\xc3\x84", $text);
    $text = str_replace("Å", "\xc3\x85", $text);
    $text = str_replace("Æ", "\xc3\x86", $text);
    $text = str_replace("Ç", "\xc3\x87", $text);
    $text = str_replace("È", "\xc3\x88", $text);
    $text = str_replace("É", "\xc3\x89", $text);
    $text = str_replace("Ê", "\xc3\x8a", $text);
    $text = str_replace("Ë", "\xc3\x8b", $text);
    $text = str_replace("Ì", "\xc3\x8c", $text);
    $text = str_replace("Í", "\xc3\x8d", $text);
    $text = str_replace("Î", "\xc3\x8e", $text);
    $text = str_replace("Ï", "\xc3\x8f", $text);
    $text = str_replace("Ð", "\xc3\x90", $text);
    $text = str_replace("Ñ", "\xc3\x91", $text);
    $text = str_replace("Ò", "\xc3\x92", $text);
    $text = str_replace("Ó", "\xc3\x93", $text);
    $text = str_replace("Ô", "\xc3\x94", $text);
    $text = str_replace("Õ", "\xc3\x95", $text);
    $text = str_replace("Ö", "\xc3\x96", $text);
    $text = str_replace("×", "\xc3\x97", $text);
    $text = str_replace("Ø", "\xc3\x98", $text);
    $text = str_replace("Ù", "\xc3\x99", $text);
    $text = str_replace("Ú", "\xc3\x9a", $text);
    $text = str_replace("Û", "\xc3\x9b", $text);
    $text = str_replace("Ü", "\xc3\x9c", $text);
    $text = str_replace("Ý", "\xc3\x9d", $text);
    $text = str_replace("Þ", "\xc3\x9e", $text);
    $text = str_replace("ß", "\xc3\x9f", $text);
    $text = str_replace("à", "\xc3\xa0", $text);
    $text = str_replace("ý", "\xc3\xbd", $text);
    $text = str_replace("þ", "\xc3\xbe", $text);
    $text = str_replace("ÿ", "\xc3\xbf", $text);
    $text = str_replace("ä", "\xc3\xa4", $text);
    $text = str_replace("å", "\xc3\xa5", $text);
    $text = str_replace("æ", "\xc3\xa6", $text);
    $text = str_replace("ç", "\xc3\xa7", $text);
    $text = str_replace("è", "\xc3\xa8", $text);
    $text = str_replace("é", "\xc3\xa9", $text);
    $text = str_replace("ê", "\xc3\xaa", $text);
    $text = str_replace("ë", "\xc3\xab", $text);
    $text = str_replace("ì", "\xc3\xac", $text);
    $text = str_replace("í", "\xc3\xad", $text);
    $text = str_replace("î", "\xc3\xae", $text);
    $text = str_replace("ï", "\xc3\xaf", $text);
    $text = str_replace("ð", "\xc3\xb0", $text);
    $text = str_replace("ñ", "\xc3\xb1", $text);
    $text = str_replace("ò", "\xc3\xb2", $text);
    $text = str_replace("ó", "\xc3\xb3", $text);
    $text = str_replace("ô", "\xc3\xb4", $text);
    $text = str_replace("õ", "\xc3\xb5", $text);
    $text = str_replace("ö", "\xc3\xb6", $text);
    $text = str_replace("÷", "\xc3\xb7", $text);
    $text = str_replace("ø", "\xc3\xb8", $text);
    $text = str_replace("ù", "\xc3\xb9", $text);
    $text = str_replace("ú", "\xc3\xba", $text);
    $text = str_replace("û", "\xc3\xbb", $text);
    $text = str_replace("ü", "\xc3\xbc", $text);
    return $text;
}
///<summary>check if apache module is present</summary>
/**
 * check if apache module is present
 */
function igk_apache_module($n)
{
    if (function_exists("apache_get_modules")) {
        return in_array($n, apache_get_modules());
    } else {
        ob_start();
        phpinfo();
        $s = ob_get_contents();
        ob_end_clean();
        return strpos($s, $n) !== false;
    }
}
///<summary></summary>
/**
 * 
 */
function igk_app_destroy()
{
    return IGKApp::Destroy();
}
///<summary>get app environment key</summary>
/**
 * get app environment key
 */
function igk_app_env_key($app, $key)
{
    return "app://" . $app->Name . "/" . $key;
}
///<summary>store system session key</summary>
///<param name="app">object setting</param>
/**
 * store system session key
 * @param mixed $app object setting
 */
function igk_app_store_in_session($app)
{
    $_SESSION[IGK_APP_SESSION_KEY] = $app;
}
///<summary></summary>
/**
 * 
 */
function igk_app_version()
{
    return IGK_PLATEFORM_NAME . " " . IGK_VERSION;
}
///<summary></summary>
///<param name="cond"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $cond 
 * @param mixed $msg 
 */
function igk_assert_die($cond, $msg)
{
    if ($cond) {
        igk_die($msg);
    }
}
///<summary>Represente igk_assoc_keys function</summary>
///<param name="n"></param>
/**
 * Represente igk_assoc_keys function
 * @param mixed $n 
 */
function igk_assoc_keys($n)
{
    return json_encode(array_keys((array)$n));
}
///<summary>return the base uri name of a file</summary>
///<exemple>/igkdev/com/info/li.txt will return com.info.li_txt </exemple>
/**
 * return the base uri name of a file
 */
function igk_base_uri_name($f, $basedir = null)
{
    $s = $basedir ?? igk_io_basedir();
    $k = igk_io_basedir(igk_dir($f));
    $k = str_replace($s, "", $k);
    $k = str_replace(".", "_", $k);
    $k = str_replace(DIRECTORY_SEPARATOR, ".", $k);
    if ((strlen($k) > 0) && ($k[0] == "."))
        $k = substr($k, 1);
    return $k;
}
///<summary>bind attribute to type</summary>
///<exemple>bind attribute</exemple>
/**
 * used to bind attribuyte to type
 */
function igk_bind_attribute($type, $name, $attribute, $allowmultiple = true, $inherits = false)
{
    switch ($type) {
        case "class":
            IGKAttribute::Register($name, $attribute, $allowmultiple, $inherits);
            break;
        case "method":
            break;
    }
}
///<summary></summary>
///<param name="doc"></param>
/**
 * shortcut to CssUtils::GetInlineStyleRendering
 * @param mixed $doc 
 */
function igk_bind_host_css_style($doc)
{
    return CssUtils::GetInlineStyleRendering($doc, false);
}
///<summary>style function to bind file</summary>
/**
 * style function to bind file
 */
function igk_bind_host_css_style_file(string $file, ICssResourceResolver $doc, $host, bool $themeexport)
{
    $bvtheme = new HtmlDocTheme($doc->Parent, "temp://files");
    $out = "";
    $sys = $doc->SysTheme;
    igkOb::Start();
    igk_css_bind_file($bvtheme, $host, $file);
    $m = igk_css_treat(igkOb::Content(), $themeexport, $sys, $sys);
    igkOb::Clear();
    if (!empty($m)) {
        $out .= $m;
    }
    $o = "";
    if (!empty($out)) {
        $o .= $out;
    }
    $o .= $bvtheme->get_css_def(false, false, $doc);
    return $o;
}
///<summary>Represente igk_bind_session_id function</summary>
///<param name="id"></param>
/**
 * Represente igk_bind_session_id function
 * @param mixed $id 
 */
function igk_bind_session_id($id)
{
    return @session_id($id);
}
///<summary>Represente igk_bind_sitemap function</summary>
/**
 * Represente igk_bind_sitemap function
 */
function igk_bind_sitemap()
{
    extract(func_get_arg(0));
    if (!isset($c)) {
        return;
    }
    if ($ctrl && preg_match("/^" . IGK_SITEMAP_FUNC . "(\.(xml|php|pinc))?$/", $c)) {
        if (method_exists($ctrl, "sitemap")) {
            call_user_func_array([$ctrl, "sitemap"], []);
        } else {
            // + | Render xml file 
            $uri = $ctrl->getAppUri();
            $hash = sha1($uri);
            if (file_exists($file = $ctrl->getDeclaredDir() . "/.Caches/.{$hash}.sitemap.cache")) {
                header("Content-Type: application/xml");
                include($file);
                igk_exit();
            }
            $e = SitemapGeneratorCommand::GenerateSiteMap($ctrl->getViews(false, true), $uri);
            igk_io_w2file($file, $e);
            igk_xml($e);
        }
        igk_exit();
    }
}
///<summary>Represente igk_ca_edit_article function</summary>
///<param name="file"></param>
/**
 * Represente igk_ca_edit_article function
 * @param mixed $file 
 */
function igk_ca_edit_article($file)
{
    $ctrl = igk_getctrl(IGK_CA_CTRL);
    $t = igk_create_node("a");
    $t->div()->Content = igk_svg_use("edit");
    $t["href"] = "/" . $ctrl->getUri("ca_edit_articlewtiny&q=" . base64_encode(igk_io_basepath($file)));
    $t["title"] = __("Edit article");
    $t["igk-ajx-lnk"] = 1;
    $t->renderAJx();
}
///<summary>Represente igk_cache function</summary>
/**
 * helper: Represente igk_cache function
 * @return IGKCaches
 */
function igk_cache()
{
    return IGKCaches::getInstance();
}
///<summary>Represente igk_cache_array_content function</summary>
///<param name="m"></param>
///<param name="fc" default="null"></param>
/**
 * Represente igk_cache_array_content function
 * @param mixed $m 
 * @param mixed $fc 
 */
function igk_cache_array_content($m, $fc = null)
{
    return \IGK\System\IO\File\PHPScriptBuilderUtility::GetArrayReturn($m, $fc);
}
///<summary></summary>
///<param name="o"></param>
/**
 * helper: check for cache expire helper
 * @param mixed $o time
 */
function igk_cache_expired($o)
{
    $n = igk_date_now();
    $d1 = igk_time_span(IGK_DATETIME_FORMAT, $n);
    $d2 = igk_time_span(IGK_DATETIME_FORMAT, $o->date);
    $b = ($d1 - $d2);
    return $b > $o->duration;
}
///<summary>generate cache from folder</summary>
/**
 * generate cache from folder
 */
function igk_cache_gen_cache($sourcedir, $cachedir, $mergescript = null, $resregex = "/\.(js(on)?)$/")
{
    $ln = strlen($sourcedir) + 1;
    $resolver = IGKResourceUriResolver::getInstance();
    $mergeoutput = "";
    $dname = "";
    if ($mergescript) {
        $dname = $cachedir . "/{$mergescript}.js";
    }
    $rgx = $resregex;
    $mergescallback = function ($file) use ($sourcedir, $mergescript) {
        $header = "/*file:" . substr($file, strlen($sourcedir) + 1) . "*/" . IGK_LF;
        if ($mergescript) {
            echo $header . str_replace("\"use strict\";", "", igk_js_minify(file_get_contents($file))) . "\n";
        } else {
            igk_zip_output($header . igk_js_minify(file_get_contents($file)), 0, 0);
        }
    };
    if (is_callable($resregex)) {
        $rgx = "/\.(([a-z]+)$)/i";
    } else {
        $resregex = function ($file, $cfile, $mergescallback) {
            switch ($ext = igk_io_path_ext(basename($file))) {
                case "js":
                    $mergescallback($file);
                    break;
                default:
                    include($file);
                    break;
            }
        };
    }
    $tf = igk_io_getfiles($sourcedir, $rgx);
    foreach ($tf as $file) {
        ob_start();
        $cfile = $cachedir . "/" . substr($file, $ln);
        $c = $resregex($file, $cfile, $mergescallback);
        $output = ob_get_contents();
        ob_clean();
        if ($c) {
            continue;
        }
        $ext = igk_io_path_ext(basename($file));
        if ($mergescript && ($ext == "js")) {
            $mergeoutput .= $output;
        } else {
            igk_io_w2file($cfile, $output);
            $resolver->resolve($cfile, null);
        }
    }
    if ($mergescript && !empty($mergeoutput)) {
        $cfile = $dname;
        ob_start();
        igk_zip_output($mergeoutput, 0, 0);
        $mergeoutput = ob_get_contents();
        ob_end_clean();
        igk_io_w2file($cfile, $mergeoutput);
        $resolver->resolve(dirname($cfile), null);
    }
}
///<summary>Represente igk_cache_get_ctrl_info function</summary>
///<param name="className"></param>
///<param name="ctrl" type="BaseController"></param>
/**
 * Represente igk_cache_get_ctrl_info function
 * @param mixed $className 
 * @param BaseController $ctrl 
 */
function igk_cache_get_ctrl_info($className, BaseController $ctrl)
{
    return implode("|", [$className, $ctrl->getConfigs()->clRegisterName, $ctrl->getName()]);
}
///<summary> core cache handle base on request uri</summary>
/**
 * core cache handle base on request uri
 * @param ?string $uri
 */
function igk_cache_handle($uri = null)
{
    if ($uri == null)
        $uri = igk_server()->REQUEST_URI;
    $fs = IGKCaches::page_filesystem();
    if (file_exists($file = $fs->getCacheFilePath($uri))) {
        ob_start();
        readfile($file);
        (new IGK\System\Http\WebResponse(ob_get_clean()))->output();
        igk_exit();
    }
}
///<summary>Represente igk_cache_js_callback function</summary>
///<param name="file"></param>
///<param name="cfile"></param>
///<param name="mergescallback"></param>
/**
 * Represente igk_cache_js_callback function
 * @param mixed $file 
 * @param mixed $cfile 
 * @param mixed $mergescallback 
 */
function igk_cache_js_callback($file, $cfile, $mergescallback)
{
    $ext = igk_io_path_ext(basename($file));
    switch ($ext) {
        case "js":
            $mergescallback($file);
            break;
        default:
            IO::CreateDir(dirname($cfile));
            igk_io_symlink($file, $cfile);
            IGKResourceUriResolver::getInstance()->resolve($cfile);
            return 1;
    }
    return 0;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_call_env_closure($n)
{
    $fc = igk_getv(igk_get_env(IGK_ENV_CALLBACK_KEYS), $n);
    if ($fc) {
        return call_user_func_array($fc, array_slice(func_get_args(), 1));
    }
    return null;
}
///<summary></summary>
///<param name="callable"></param>
/**
 * 
 * @param mixed $callable 
 */
function igk_callable_id($callable)
{
    if (is_string($callable) && is_callable($callable))
        return $callable;
    if (!igk_is_callable($callable)) {
        return null;
    }
    if (is_array($callable) && (igk_count($callable) == 2)) {
        $cl = get_class(igk_getv($callable, 0));
        $m = igk_getv($callable, 1);
        $f = $cl . "::\x3a" . $m;
        return $f;
    }
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="parentName"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $parentName 
 */
function igk_can_set_ctrlparent($ctrl, $parentName)
{
    $p = igk_getctrl($parentName, false);
    while ($p) {
        if (($p == $ctrl) || (!$p->CanAddChild))
            return false;
        $p = $p->getWebParentCtrl();
    }
    return true;
}
///<summary></summary>
/**
 * 
 */
function igk_cancel_last_ref_number()
{
    $ct = igk_getctrl(IGK_UCB_REF_CTRL);
    $ct->cancel_last_ref_number();
}
///<summary></summary>
///<param name="v" default="11"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_check_ie_version($v = 11)
{
    return igk_agent_isie() ? (explode("/", igk_agent_ieversion())[0] <= $v) : 0;
}
///<summary>Represente igk_clean_globals function</summary>
/**
 * Represente igk_clean_globals function
 */
function igk_clean_globals()
{
    $g = array_diff(array_keys($GLOBALS), explode("|", "_GET|_POST|_COOKIE|_FILES|_SERVER|_REQUEST|GLOBALS|_SESSION"));
    foreach ($g as $k) {
        unset($GLOBALS[$k]);
    }
}
///<summary>short cut to clear cache</summary>
/**
 * short cut to clear cache
 */
function igk_clear_cache()
{
    \IGK\Helper\SysUtils::ClearCache();
}
///Clear config session and restart Application
/**
 */
function igk_clear_config_session()
{
    $ctrl = igk_getconfigwebpagectrl();
    $uri = $ctrl->getUri("startconfig&q=" . base64_encode('?' . http_build_query(array(
        "u" => $ctrl->User->clLogin,
        "pwd" => $ctrl->User->clPwd,
        "selectedCtrl" => $ctrl->getSelectedConfigCtrl()->getName(),
        "selectPage" => $ctrl->getSelectedMenuName()
    ))));
    igk_getctrl(IGK_SESSION_CTRL)->ClearS(false);
}
///partir un jour
///<summary>clear cookie</summary>
///<param name="n">cookie's name</param>
/**
 * clear cookie
 * @param mixed $n cookie's name
 */
function igk_clear_cookie($n)
{
    $m = igk_get_cookie_name(igk_sys_domain_name() . "/" . $n);
    $rs = igk_getv($_COOKIE, $m);
    igk_set_cookie($n, null, 1, time() - (7 * 24 * 60));
    if ($rs)
        unset($_COOKIE[$m]);
}
///<summary>clear header list</summary>
/**
 * clear header list
 */
function igk_clear_header_list($ignorelist = null)
{
    $ignorelist = $ignorelist ?? igk_get_env("sys://headers/ignorelist");
    $tab = headers_list();
    foreach ($tab as $v) {
        $n = igk_getv(explode(':', $v), 0);
        if ($ignorelist && in_array($n, $ignorelist))
            continue;
        header_remove($n);
    }
}
///<summary></summary>
/**
 * 
 */
function igk_clearall_cookie()
{
    $tab = $_COOKIE;
    foreach ($tab as $k => $v) {
        igk_set_global_cookie($k, "", 1, time() - (7 * 24 * 60));
        unset($_COOKIE[$k]);
    }
}
///<summary>close the current session id</summary>
/**
 * close the current session id
 */
function igk_close_session()
{
    if ($sess = igk_app()->getApplication()->getLibrary()->session) {

        $sess->close();
    }
}
///<summary></summary>
///<param name="v"></param>
///<param name="$c"></param>
/**
 * 
 * @param mixed $v 
 * @param mixed $$c 
 */
function igk_cmp_array_value($v, $c)
{
    if (is_array($v) && is_array($c)) {
        $dc = igk_count($v);
        $hc = igk_count($c);
        if ($dc == $hc) {
            for ($i = 0; $i < $dc; $i++) {
                if ($v[$i] !== $c[$i]) {
                    return false;
                }
            }
            return true;
        }
    }
    return false;
}
///<summary></summary>
///<param name="o"></param>
///<param name="i"></param>
/**
 * 
 * @param mixed $o 
 * @param mixed $i 
 */
function igk_cmp_refobj($o, $i)
{
    if (($o == null) && ($i == null))
        return true;
    if ((($o == null) && ($i != null)) || (($o != null) && ($i == null)))
        return false;
    $cmp = igk_new_id();
    $o->$cmp = true;
    $r = (!empty($i->$cmp));
    unset($o->$cmp);
    return $r;
}
///<summary>compare two version</summary>
///<exemple> 1.0.1 vs 1.0.5</exemple>
/**
 * compare two version
 */
function igk_cmp_version($v1, $v2)
{
    while (($tb1 = explode(".", trim($v1))) && count($tb1) < 4) {
        $v1 .= ".0";
    }
    while (($tb2 = explode(".", trim($v2))) && count($tb2) < 4) {
        $v2 .= ".0";
    }
    $c = igk_count($tb1);
    if ($c == igk_count($tb2)) {
        $i = 0;
        while (($i < $c) && ($tb1[$i] === $tb2[$i])) {
            $i++;
        }
        if ($i < $c) {
            if ($tb1[$i] < $tb2[$i]) {
                return -1;
            }
            return 1;
        }
    }
    return strcmp($v1, $v2);
}

/// TASK: FOLLOW US MUST BE REMOVED
///<summary></summary>
/**
 * 
 */
function igk_community_get_followus_service()
{
    return igk_get_env(Path::Combine(IGK_SERVICE_PREFIX_PATH, "/community/followus"));
}
///<summary></summary>
///<param name="name"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $callback 
 */
function igk_community_register_followus_service($name, $callback)
{
    return igk_register_service('community/followus', $name, $callback);
}
///<summary></summary>
/**
 * 
 */
function igk_conf_canconfigure()
{
    return igk_getctrl(IGK_CONF_CTRL)->getCanConfigure();
}
///<summary></summary>
///<param name="s"></param>
/**
 * 
 * @param mixed $s 
 */
function igk_conf_get_expression($s)
{

    $tab = array();
    $ln = strlen($s);
    $i = 0;
    $m = 0;
    $n = "";
    $q = null;
    /**
     * @var object $q
     */
    while ($i < $ln) {
        $ch = $s[$i];
        switch ($ch) {
            case "=":
            case "|":
            case "$":
            case "^":
                if ($m == 0) {
                    $q = (object)array("op" => $ch, "v" => "");
                    $tab[$n] = $q;
                    $m = 1;
                }
                $n = "";
                break;
            case ",":
                if (($m == 1) && (!empty($n))) {
                    $q->v = $n;
                }
                $m = 0;
                $n = "";
                break;
            default:
                $n .= $ch;
                break;
        }
        $i++;
    }
    if ($q && ($m == 1) && ((strlen($n) != 0) || !empty($n))) {
        $q->v = $n;
    }
    return (object)$tab;
}
///<summary>get if configuration match the request</summary>
/**
 * get if configuration match the request
 */
function igk_conf_match($t, $n, $op)
{
    if (isset($t->$n)) {
        switch ($op->op) {
            case '=':
                return $t->$n == $op->v;
            case '$':
                return igk_str_endwith($t->$n, $op->v);
            case '^':
                return igk_str_startwith($t->$n, $op->v);
            case '~':
                return strstr($t->$n, $op->v) !== null;
        }
    }
    return 0;
}
///<summary>set object at igkXpath selection model</summary>
/**
 * set object at igkXpath selection model
 */
function igk_conf_set(&$obj, $data, $path)
{
    $c = explode("/", $path);
    $s = $obj;
    while ($q = array_shift($c)) {
        $set = igk_count($c) == 0;
        if ($set) {
            $s->$q = $data;
            break;
        }
        if (!isset($s->$q))
            $s->$q = igk_createobj();
        $s = $s->$q;
    }
}
///<summary>store config value to node</summary>
///<param name="node">node where to add object</param>
///<param name="root">root name</param>
///<param name="v"></param>
/**
 * store config value to node
 * @param mixed $node node where to add object
 * @param mixed $root root name
 * @param mixed $v 
 */
function igk_conf_store_value($d, $k, $v)
{
    $bindv = function ($c, $v) {
        $tab = array();
        array_push($tab, (object)array('n' => $c, 'v' => $v));
        while ($q = array_pop($tab)) {
            $m = $q;
            if (is_null($m->v)){
                igk_die('property is null');
                continue;
            }
            foreach ($m->v as $s => $t) {
                if ($s[0] == "@") {
                    $m->n[substr($s, 1)] = $t;
                    continue;
                }
                if (is_string($t) || is_numeric($t)) {
                    $m->n->add($s)->Content = htmlspecialchars($t);
                } else {
                    if (is_array($t)) {
                        array_push($tab, (object)array('_from:table' => 1, 'n' => $m->n, 'v' => $t, 'a' => $s));
                    } else {
                        if (isset($m->{'_from:table'})) {
                            $a = $m->a;
                            igk_assert_die(!$a, "object not found. " . $s);
                            array_push($tab, (object)array('n' => $m->n->add($a), 'v' => $t));
                        } else {
                            if ($t instanceof XmlCDATA) {
                                $m->n->add($s)->Content = $t;
                                continue;
                            }
                            if ($t instanceof \IGK\System\Html\IHtmlGetValue) {
                                $m->n->add($s)->Content = $t->getValue();
                                continue;
                            }
                            array_push($tab, (object)array('n' => $m->n->add($s), 'v' => $t));
                        }
                    }
                }
            }
        }
    };
    if (is_string($v) || is_numeric($v) || is_bool($v)) {
        $d->add($k)->Content = $v;
        return;
    } else {
        if (is_object($v)) {
            $c = $d->add($k);
            if ($v instanceof \IGK\System\Html\IHtmlGetValue) {
                $c->Content = $v->getValue();
            } else {
                $bindv($c, $v);
            }
        } else if (is_array($v)) {
            $name = $k;
            foreach ($v as $k => $v) {
                $n = $d->add($name);
                $bindv($n, $v);
            }
        }
    }
}
///<summary>unset all object found in igkXpath selection model</summary>
/**
 * unset all object found in igkXpath selection model
 */
function igk_conf_unset(&$obj, $path)
{
    throw new IGKException("Not Implement " . __FUNCTION__);
}
///<summary></summary>
/**
 * 
 */
function igk_config_php_index_content()
{
    $index_file = "index.php";
    $date = date("Ymd m:i:s");
    $v = <<<ETF
<?php
// @file: index.php
// @desc: manual configuration's entry point
// @author: C.A.D. BONDJE DOUE
// @date : $date

if (!version_compare(PHP_VERSION, "7.3", ">=")){
    die("mandory version required. 7.3<=");
}
\$_SERVER["PHP_SELF"] = \$_SERVER["REDIRECT_URL"] = "/Configs";
\$_SERVER["REDIRECT_STATUS"] = "200";
chdir("../");
define("IGK_CONFIG_PAGE",1);
if (!defined("IGK_FRAMEWORK")){
    @require_once("{$index_file}");
} else {
    igk_sys_config_view(__FILE__);
}
ETF;

    return $v;
}
///<summary></summary>
/**
 * 
 */
function igk_core_dist_jscache()
{
    return igk_io_cacheddist_jsdir() . "/core/balafon.js";
}
///<summary></summary>
/**
 * 
 */
function igk_create_action_reponse()
{
    return igk_createobj(array("msg" => "Success", "type" => "igk-default"));
}
///<summary>crate a adapter from class name</summary>
/**
 * crate a adapter from class name
 */
function igk_create_adapter_from_classname($n, $ctrl = null, $params = null)
{
    $out = new $n(!is_string($ctrl) ? $ctrl : null);
    if (($out != null) && ($params != null)) {
        $out->configure($params);
    }
    if (!$out->IsAvailable) {
        igk_debug_wln("adapter " . $ctrl . " not available");
        return null;
    }
    return $out;
}
///<summary>create an attribute to render value by calling callback</summary>
///<param name="n" > name or array callback </param>
/**
 * create an attribute to render value by calling callback
 * @param mixed $n  name or array callback 
 */
function igk_create_attr_callback($n, $attrs)
{
    $s = null;
    if (is_callable($n)) {
        $s = (object)array(
            IGK_OBJ_TYPE_FD => "callable",
            "name" => $n,
            "attrs" => $attrs
        );
        return $s;
    }
    return $s;
}
///<summary></summary>
///<param name="classname"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $classname 
 * @param mixed $callback 
 */
function igk_create_component_callback($classname, $callback)
{
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
    $t = $ctrl->getParam("sys://class_component", array());
    if (!isset($t[$classname])) {
        $t[$classname] = $callback();
        $ctrl->setParam("sys://class_component", $t);
    }
    return $t[$classname];
}
///<summary>get single cref id for app</summary>
/**
 * get single cref id for app
 */
function igk_create_cref()
{
    static $cref;
    if (!isset($cref)) {
        $cref = "cref:" . date("Ymd-His") . "|" . igk_new_id();
    }
    return $cref;
}
///<summary>create a dynamic object with the array data</summary>
/**
 * create a dynamic object with the array data
 */
function igk_create_dynamic($data)
{
    $m = new IGKDynamicObject();
    $m->initProperties($data);
    return $m;
}
///<summary>used to create a evaluable expression callback</summary>
/**
 * used to create a evaluable expression callback
 */
function igk_create_expression_callback($exp, $param = null)
{
    return (object)array(
        IGK_OBJ_TYPE_FD => "_callback",
        "clType" => "exp",
        "clFunc" => $exp,
        "clParam" => $param
    );
}
///<summary>create a file callback object</summary>
/**
 * create a file callback object
 */
function igk_create_file_callback($ctrl, $file, $func = null, $param = null)
{
    return (object)array(
        IGK_OBJ_TYPE_FD => "_callback",
        "clType" => "file",
        "Ctrl" => $ctrl,
        "clFile" => $file,
        "clFunc" => $func,
        "clParam" => $param
    );
}
///<summary> used to create a filtered object </summary>
/**
 *  used to create a filtered object 
 */
function igk_create_filterobject($n, $initarray)
{
    if (!isset($n) || ($n == null))
        $n = (object)$initarray;
    else {
        if (is_array($n))
            $n = (object)$n;
        foreach ($initarray as $k => $v) {
            if (!isset($n->$k)) {
                $n->$k = $v;
            }
        }
    }
    return $n;
}
///<summary></summary>
///<param name="func"></param>
///<param name="param" default="null"></param>
/**
 * 
 * @param mixed $func 
 * @param mixed $param 
 */
function igk_create_func_callback($func, $param = null)
{
    return (object)array(
        IGK_OBJ_TYPE_FD => "_callback",
        "clType" => "func",
        "clFunc" => $func,
        "clParam" => $param
    );
}
///<summary>create guid value {}</summary>
/**
 * create guid value {xxxx-xxxx-}
 */
function igk_create_guid()
{
    if (function_exists("com_create_guid")) {
        return com_create_guid();
    }
    mt_srand(intval(floor((float)microtime() * 10000)));
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);
    $uuid = chr(123) . substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12) . chr(125);
    return $uuid;
}
/**
 * get guid value and remove the {...}
 */
function igk_create_guid_value()
{
    $g = igk_create_guid();
    return substr($g, 1, -1);
}
///<summary></summary>
///<param name="name"></param>
///<param name="args" default="null"></param>
///<param name="initcallback" default="null"></param>
///<param name="class" default="IGK_HTML_ITEMBASE_CLASS"></param>
///<param name="context" default="Html"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $args 
 * @param mixed $initcallback 
 * @param mixed $class 
 * @param mixed $context 
 */
function igk_create_html_component($name, $args = null, $initcallback = null, $class = HtmlItemBase::class, $context = HtmlContext::Html)
{
    return HtmlUtils::CreateHtmlComponent($name, $args, $initcallback, $class);
}
///<summary></summary>
///<param name="fcname"></param>
///<param name="node"></param>
/**
 * 
 * @param mixed $fcname 
 * @param mixed $node 
 */
function igk_create_invoke_callback($fcname, $node)
{
    $t = array("fc" => $fcname, "n" => $node);
    if (func_num_args() > 2)
        $t = array_merge($t, array_slice(func_get_args(), 2));
    return igk_create_expression_callback("return \$invokeParam(\$obj,\$fc,null,1);", $t);
}
///<summary></summary>
///<param name="path"></param>
/**
 * 
 * @param mixed $path 
 */
function igk_create_module($path)
{
    $v_k = "sys://require_mods";
    $g = igk_get_env($v_k, array());
    if (isset($g[$path]))
        return 0;
    $dir = IGK_LIB_DIR . "/Modules/{$path}";
    if (file_exists($dir)) {
        return 0;
    }
    if (!IO::CreateDir($dir))
        return 0;
    igk_io_save_file_as_utf8_wbom($dir . "/.module.pinc", igk_io_read_allfile(IGK_LIB_DIR . "/Model/module.php"));
    return 1;
}
///<summary></summary>
///<param name="callable"></param>
///<param name="params" default="null"></param>
/**
 * 
 * @param mixed $callable 
 * @param mixed $params 
 */
function igk_create_node_callback($callable, $params = null)
{
    return (object)array(
        IGK_OBJ_TYPE_FD => "_callback",
        "clType" => "node",
        "clFunc" => $callable,
        "clParam" => $params
    );
}
///<summary>create a session instance object</summary>
///<param name="n">identifier of the object</param>
///<param name="callback">mixed callable|classname callback object</param>
/**
 * create a session instance object
 * @param mixed $n identifier of the object
 * @param mixed $callback mixed callable|classname callback object
 */
function igk_create_session_instance($n, $callback)
{
    $create_instance = function ($callback) {
        $o = null;
        if (is_callable($callback)) ($o = $callback()) || igk_die(__("failed to create an instance"));
        else if (class_exists($callback, false)) {
            ($o = new $callback()) || igk_die(__("failed to create an instance"));
        }
        return $o;
    };
    //+ | no session started just create the object
    if (!isset($_SESSION)) {
        return $create_instance($callback);
    }
    //+ | create the object and replace if not null
    $o = igk_getv($_SESSION, $n);
    if (!$o && ($o = $create_instance($callback))) {
        $_SESSION[$n] = $o;
    }
    return $o;
}
///<summary>Represente igk_create_view_builder_option function</summary>
/**
 * Represente igk_create_view_builder_option function
 */
function igk_create_view_builder_option()
{
    return (object)["PHP.SkipComment" => true];
}
///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab 
 * @return \IGK\System\Project\Configurations\ConfigurationPropertyInfo
 */
function igk_createadditionalconfiginfo($tab): \IGK\System\Project\Configurations\ConfigurationPropertyInfo
{
    $o = Activator::CreateNewInstance(
        \IGK\System\Project\Configurations\ConfigurationPropertyInfo::class,
        $tab
    );
    return $o;
    // $o = new StdClass();
    // $o->clType = igk_getv($tab, "clType");
    // $o->clDefaultValue = igk_getv($tab, "clDefaultValue");
    // $o->clRequire = igk_getv($tab, "clRequire");
    // return $o;
}
///<summary>create an article node</summary>
/**
 * helper: create an article node
 */
function igk_create_articlenode($ctrl, $article, $raw)
{
    $n = igk_create_node("div");
    igk_html_article($ctrl, $article, $n, $raw);
    return $n;
}

///<summary></summary>
///<param name="name"></param>
///<param name="attributes" default="null"></param>
///<param name="index" default="null"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $attributes 
 * @param mixed $index 
 */
function igk_createforminput($name, $attributes = null, $index = null)
{
    $d = igk_create_node();
    $d["class"] = "igk-form-group";
    $f = $d->addSLabelInput($name, 'text');
    if ($attributes)
        $f->input->setAttributes($attributes);
    return $d;
}
///<summary> create loading article context </summary>
/**
 * create loading article context 
 * @return \IGK\System\Html\HtmlLoadingContextOptions
 */
function igk_createloading_context($ctrl, ?array $raw = null): HtmlLoadingContextOptions
{
    $t = new \IGK\System\Html\HtmlLoadingContextOptions;
    $t->ctrl = isset($ctrl) ? $ctrl : null;
    $t->raw = $raw;
    $t->load_expression = true;
    return $t;
}
///<summary>shorcut to create a balafon web node</summary>
/**
 * shorcut to create a balafon web node. helper
 * @param string $tag tagname
 * @param array $attributes associative array of attribute
 * @param null|int|array $index_or_args index or node required parameter
 * @return \IGK\System\Html\Dom\HtmlItemBase web node
 */
function igk_create_node($tagname = "div", $attributes = null, $index_or_args = null)
{
    return HtmlNode::CreateWebNode($tagname, $attributes, $index_or_args);
}

///<summary></summary>
///<param name="args" default="null"></param>
/**
 * 
 * @param mixed $args 
 */
function igk_create_node_with_package($args = null)
{
    $package = igk_get_current_package();
    if ($package !== null) {
        $tab = is_array($args) ? $args : func_get_args();
        while (igk_count($tab) < 3) {
            $tab[] = null;
        }
        list($name, $attributes, $indexOrArgs)
            = $tab;
        if (is_object($package) && method_exists($package, "CreateNode")) {
            $n = $package->CreateNode($name, $attributes, $indexOrArgs);
            return $n;
        }
        if (!is_callable($package))
            igk_die("not a callable");
        return call_user_func_array($package, $args);
    }
    return null;
}
///<summary>Represente igk_create_notagnode function</summary>
/**
 * Represente igk_create_notagnode function
 */
function igk_create_notagnode()
{
    return new \IGK\System\Html\Dom\HtmlNoTagNode();
}
///<summary>create an object by keys</summary>
/**
 * create an object by keys
 */
function igk_createobj_array($keys, $default = null)
{
    $o = igk_createobj();
    foreach ($keys as $m) {
        $o->$m = $default;
    }
    return $o;
}
///<summary>create filtered object </summary>
///<param name="src"> source data </param>
///<param name="filter"> filtered data </param>
/**
 * create filtered object 
 * @param mixed $src source data 
 * @param string|array $filter filtered data 
 */
function igk_createobj_filter($src, $filter)
{
    $o = igk_createobj();
    if (is_string($filter)) {
        $filter = array_fill_keys(array_filter(explode('|', $filter)), null);
    }
    foreach ($filter as $k => $v) {
        $o->$k = igk_getv($src, $k, $v);
    }
    return $o;
}
///<summary></summary>
///<param name="array_key"></param>
/**
 * create strict object storage
 * @param mixed $array_key 
 * @return \IGKObjectStrict
 */
function igk_createobj_strict($array_key)
{
    return IGKObjectStrict::Create($array_key);
}
///<summary>create objeect storage</summary>
///<param name="tab" default="null"></param>
/**
 * create object storage
 * @param mixed $tab source data
 * @return \IGKObjStorage
 */
function igk_createobjstorage($tab = null)
{
    return new \IGKObjStorage($tab);
}

///<summary></summary>
///<param name="name"></param>
///<param name="options"></param>
///<param name="selected" default="null"></param>
///<param name="callback" default="null"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $options 
 * @param mixed $selected 
 * @param mixed $callback 
 */
function igk_createselectinput($name, $options, $selected = null, $callback = null)
{
    $d = igk_create_node();
    $d["class"] = "igk-form-group";
    $f = $d->addLabel($name);
    $sl = $d->add("select");
    $sl["class"] = "igk-form-control";
    if ($options) {
        foreach ($options as $k => $v) {
            $opt = $sl->add("option");
            $opt["value"] = $k;
            $opt->Content = $callback ? $callback($v) : $v;
            if ($k == $selected) {
                $opt["selected"] = "true";
            }
        }
    }
    return $d;
}
///<summary>Represente igk_createtextnode function</summary>
///<param name="txt" default="null"></param>
/**
 * 
 * @param mixed $txt the default value is null
 */
function igk_createtextnode($txt = null)
{
    $n = new HtmlTextNode($txt);
    return $n;
}
///<summary>Represente igk_createxml_config_data function</summary>
///<param name="data"></param>
/**
 * store config data
 */
function igk_createxml_config_data($data)
{
    $d = igk_create_xmlnode("config");
    foreach ($data as $k => $v) {
        igk_conf_store_value($d, $k, $v);
    }
    return $d;
}
///<summary>Represente igk_createxml_document function</summary>
///<param name="tagName"></param>
///<param name="docType" default="null"></param>
/**
 * Represente igk_createxml_document function
 * @param 77  $tagName
 * @param 77  $docType the default value is null
 */
function igk_createxml_document($tagName, $docType = null)
{
    $c = new \IGK\System\Html\XML\XmlDocument($tagName, $docType);
    return $c;
}



///<summary></summary>
///<param name="doc"></param>
///<param name="name"></param>
///<param name="file"></param>
///<param name="format" default="TrueType"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $name 
 * @param mixed $file 
 * @param mixed $format 
 */
function igk_css_add_tempfont($doc, $name, $file, $format = "TrueType")
{
    $f = $doc->Theme;
    $ft = $f->Font;
    $r = igk_getv($ft, $name);
    if (($r == null) || !is_object($r)) {
        $r = (object)array(
            "Name" => $name,
            "Fonts" => array(),
            "Def" => "arial, sans-serif"
        );
        $ft[$name] = $r;
    }
    $r->Fonts[$file] = (object)array("File" => $file, "format" => $format);
}
///<summary>bind .pcss file for ajx content</summary>
/**
 * bind .pcss file for ajx content
 */
function igk_css_ajx_bind_file($f)
{
    $b = igk_get_env(IGK_AJX_BINDSTYLES, function () {
        return array();
    });
    if (!igk_get_env(__FUNCTION__)) {
        $vsystheme = igk_app()->getDoc()->getSysTheme();
        $vsystheme->initGlobalDefinition();
        igk_set_env(__FUNCTION__, 1);
    }
    if (!isset($b[$f])) {
        $o = igk_css_ob_get_tempfile($f, $from);
        igk_create_node("style")->setAttribute("type", "text/css")->setAttribute("igk:from", $from)->setContent($o)->renderAJX();
        $b[$f] = 1;
        igk_set_env(IGK_AJX_BINDSTYLES, $b);
    }
}
///<summary></summary>
///<param name="name"></param>
///<param name="value"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $value 
 */
function igk_css_append($name, $value)
{
    $igk = igk_app();
    if ($igk === null)
        return;
    $igk->getDoc()->Theme->Append[$name] = $value;
}
/**
 * get request uri controller 
 */
function igk_css_request_ctrl(?string $ref = null, ?string $uri = null): ?BaseController
{
    $c = igk_sys_get_projects_controllers();
    if (count($c) == 0) {
        return null;
    }
    $ctrl = null;
    $uri = $uri ?? igk_io_baseuri();
    $ref = $ref ?? igk_io_baseuri();
    if (!empty($ref) && strpos($ref, $uri) === 0) {
        $ruri = explode("?", substr($ref, strlen($uri)))[0];
        if ($ac = igk_getctrl(IGK_SYSACTION_CTRL, false)) {
            $ctrl = $ac::GetMatchCtrl($ruri);
        }
    }
    return $ctrl;
}
/**
 * render css text
 */
function igk_css($text)
{
    header("Content-Type: text/css");
    echo $text;
    igk_exit();
}
///<summary></summary>
///<param name="dir"></param>
/**
 * @param mixed $dir base directory 
 */
function igk_css_balafon_index($dir, $debug = null, ?bool $minfile = null)
{
    // + |
    // + | ENTRY DYNAMIC CSS
    // + | 
    require_once IGK_LIB_CLASSES_DIR . "/IGKGlobalColor.php";
    require_once IGK_LIB_CLASSES_DIR . "/Css/CssThemeCompiler.php";
    require_once IGK_LIB_CLASSES_DIR . "/System/IInjectable.php";
    require_once IGK_LIB_CLASSES_DIR . "/System/Http/IResponse.php";
    require_once IGK_LIB_CLASSES_DIR . "/System/Http/Response.php";
    require_once IGK_LIB_CLASSES_DIR . "/System/Http/RequestResponse.php";
    require_once IGK_LIB_CLASSES_DIR . "/System/Http/WebResponse.php";
    require_once IGK_LIB_CLASSES_DIR . "/Css/CssCoreResponse.php";

    igk_environment()->debug_configPage = true;

    $sess_id = session_id();
    $debug = $debug ?? (igk_environment()->isDev() || igk_server_is_local());
    $minfile = is_null($minfile) ? igk_environment()->isOPS() : $minfile;
    $ctrl = null;
    $vsystheme = null;
    $vtheme = null;
    $ref = null;
    $doc = null;
    $v_no_theme_rendering = false;


    $c = igk_getr("Cache");
    if ($c) {
        igk_wl("/*Request from cache*/");
        return;
    }
    if (!defined("IGK_BASE_DIR")) {
        define("IGK_BASE_DIR", $dir);
    }
    if (!defined("IGK_INIT") && empty($sess_id)) {
        $app = IGKApplication::Boot('css');
        IGKApp::StartEngine($app);
    }
    if (!IGKApp::IsInit()) {
        igk_ilog(__FUNCTION__ . " : application not initialise " . igk_server()->REQUEST_URI);
        igk_exit();
    }


    $is_ref_cache = false;
    if ($ctrl = igk_getr("c")) {
        $ctrl = igk_getctrl(base64_decode($ctrl), false) ??
            igk_getctrl($ctrl);
    }
    IGKOb::CleanAndStart();
    $defctrl = igk_get_defaultwebpagectrl();
    $ref = igk_server()->HTTP_REFERER;
    // + | ---------------------------------------------------------------------
    // + | when the dev tool is open the request may not send the HTTP_REFERER . 
    // + | check in session that the there was an previews request http_referer 
    // + | to help debugging 
    // + | -
    // + | -
    if (!$ref) {
        $ref = igk_app()->session->referer;
    } else {
        // update the referer
        igk_app()->session->referer = $ref;
    }




    if (!igk_environment()->noWebConfiguration()) {
        $cnfPath = igk_io_baseuri() . "/" . IGK_CONF_FOLDER;
        $in_conf_page = $ref && StringUtility::UriStart($ref, $cnfPath);
        if (igk_is_cmd() && $ref && $in_conf_page) {
            igk_app()->settings->appInfo->store('config', 1);
        }
        // + | --------------------------------------------------
        // + | reset config mode
        if (
            $ref && igk_app()->settings->appInfo->config &&
            !$in_conf_page
        ) {
            igk_app()->settings->appInfo->store("config", null);
        }
        // + | --------------------------------------------------
        // + | priority to config controller
        if (
            igk_app()->settings->appInfo->config &&
            (empty($ref) || $in_conf_page)
        ) {
            // igk_wln_e("data ref : ".$ctrl, $ref, IGKSubDomainManager::GetSubDomainCtrl());
            $ctrl = $defctrl;
            $doc = igk_app()->getDoc();
        }
    }

    if (!$ctrl) {
        // igk_wln("check subdomain .... ");
        if ($subctrl = IGKSubDomainManager::GetSubDomainCtrl()) {
            $ctrl = $subctrl;
        }
    }

    // igk_dev_wln_e("the controller ....", $ctrl, $ref);
    // check for refered controller 
    if (!$ctrl && $ref) {
        igk_set_session_redirection($ref);
        if (igk_environment()->isOPS() && igk_configs()->allow_page_cache) {
            // dectect same cache uri
            $buri = rtrim(igk_io_baseuri(), "/");
            $ref = rtrim($ref, "/");
            $luri = null;
            if (strpos($ref, $buri) === 0) {
                $luri = $buri . igk_getv(parse_url($ref), "path", "/");
                $is_ref_cache = igk_configs()->allow_page_cache &&  IGKCaches::IsCachedUri($luri);
            }
            // echo "/* detect ops " . $buri . " is cache ref " . $is_ref_cache . "  \nluri:" . $luri . " \n*/\n";
        }
        if (!$ctrl) {
            $ctrl = igk_css_request_ctrl($ref);
        }
    }
    if (!$doc) {
        if ($ctrl) {
            $doc = $ctrl->getCurrentDoc();
        } else {
            $doc = igk_get_last_rendered_document() ?? igk_app()->getDoc();
            $ctrl = $defctrl;
        }
        $doc_id = igk_app()->settings->CurrentDocumentIndex;
    }
    echo '@charset "utf-8";' . "\n";
    $debug = 1;

    if ($debug) {
        echo ("/* ------------------------------------------------ */\n");
        echo ("/* BALAFON Css DEBUG INFO : */\n");
        echo ("/* document is sys doc ? " . ($doc ? $doc->getIsSysDoc() : 'undefined') . "*/\n");
        echo ("/* referer : " . $ref . "*/ \n");
        echo ("/* controller : " . $ctrl . "*/ \n");
        echo ("/* defctrl : -- " . $defctrl . "*/ \n");
        echo ("/* ------------------------------------------------ */\n");
    }
    if ($doc) {
        igk_set_env("sys://css/cleartemp", __FUNCTION__);
        $vsystheme = $doc->getSysTheme();
        $vtheme = $doc->getTheme(false);
        $vdef = $vtheme->getDef();
        $in_config = igk_app()->settings->appInfo->config;
        $v_no_theme_rendering =  $in_config && $vtheme->getDef()->unsetStyleFlag('no_theme_rendering');

        if ($ref && $cnfPath) {
            if (strpos($ref, $cnfPath) !== 0) {
                $v_no_theme_rendering = false;
                $vtheme->getDef()->unsetStyleFlag('no_theme_rendering');
                $vtheme->getDef()->setStyleFlag('page', null);
            }
        }

        // + | ----------------------------------------------------
        // + | get binding temporary theme that need to be included
        // + | clear the list before save the session
        // + | - get copy of files to include, clear list, before
        // + | - closing the session.
        $v_binTempFiles = $vdef->getBindTempFiles(1);
        $v_tempFiles = $vdef->getTempFiles(1);
        $seridata = $vtheme->to_array();
        $vtheme->reset();
        igk_sess_write_close();

        $vtheme->load_data($seridata);
        // + | ---------------------------------------------------------------
        // + | bind controller definition   
        if ($ctrl && !$v_no_theme_rendering) {
            // + | attach temp files first - the first time
            $ctrl->bindCssStyle($vtheme, true);
        }
        if ($v_binTempFiles) {
            igk_css_bind_theme_files($vtheme, $v_binTempFiles);
        }
        if ($v_tempFiles) {
            $dc = &$vtheme->getDef()->getTempFiles();
            array_push($dc, ...$v_tempFiles);
        }
        // + | -------------------------------------------------------------
        // + | passing data to document with css
        // + |        
        if (igk_configs()->css_view_state) {
            echo ("/* document " . $ref . "::::*/  body:before{content:'referer {$ref} cached: {$is_ref_cache} {$doc_id} controller : {$ctrl} ';}");
        }

        $no_systheme = \IGK\Css\CssThemeCompiler::CompileAndRenderTheme(
            $vsystheme,
            $doc->getId(),
            "sys:global",
            true,
            true,
            false,
            null
        );

        $primaryTheme = igk_getr("theme", CssThemeOptions::DEFAULT_THEME_NAME);
        if ($ctrl && !$v_no_theme_rendering && ($def = \IGK\System\Html\Css\CssUtils::AppendDataTheme($ctrl, $vtheme, $primaryTheme))) {
            echo implode("", $def);
        }
    } else {
        echo "/* load - min.css */";
        include(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/balafon.min.css");
    }
    $c = IGKOb::Content();
    IGKOb::Clear();

    if (!igk_sys_env_production() && (igk_getr("recursion") == 1)) {
        $g = igk_get_env("sys://theme/colorrecursion");
        if (igk_count($g) === 0) {
            igk_wl("/*all good no recursion detected*/");
        } else {
            igk_wl("/*\n" . implode("\n", array_keys($g)) . "\n*/");
        }
        igk_exit();
    }
    $referer = null;
    igk_header_no_cache();
    // $c.="\n /* check why color */ body{background-color: indianred !important;";
    $response = new \IGK\Css\CssCoreResponse($c);
    $response->cache = false;
    $response->file = $referer;
    $response->no_cache = true;
    igk_do_response($response);
}
///<summary></summary>
///<param name="callback"></param>
/**
 * 
 * @param mixed $callback 
 */
function igk_css_bind_color_request($callback)
{
    igk_set_env("sys://css/bind_colorrequest", $callback);
}
///<summary>bind css file</summary>
///<param name="theme">for binding</param>
///<param name="ctrl">the global controller used to bind the css theme file</param>
///<param name="f">access to file</param>
/**
 * bind css file
 * @param HtmlDocTheme $theme for binding
 * @param BaseController $ctrl the global controller used to bind the css theme file
 * @param string $f access file
 */
function igk_css_bind_file(HtmlDocTheme $theme, ?BaseController $ctrl, string $f)
{
    if (!file_exists($f)) {
        igk_ilog('Bind file failed ' . $f);
        return;
    }
    $is_null = is_null($theme);
    if (defined("IGK_FORCSS")) {
        if ($is_null) {
            $key = "sys://css/IncludedFiled";
            $files = igk_get_env($key, array());
            $i = igk_env_count(__FUNCTION__);
            if ($i == 1) {
                // $theme->resetAll();
                $files[$f] = "root";
            } else {
                if (isset($files[$f]))
                    return;
                $files[$f] = "sub";
            }
            igk_set_env($key, $files);
        }
    }
    \IGK\System\Html\Css\CssUtils::Include($f, $ctrl, $theme);
}
///<summary></summary>
///<param name="theme" default="null"></param>
///<param name="bindfile" default="1"></param>
/**
 * helper: bind system global files
 * @param mixed $theme 
 * @param mixed $bindfile 
 */
function igk_css_bind_sys_global_files($theme = null, $bindfile = 1)
{
    $doc = igk_app()->getDoc();
    $theme = $theme ?? $doc->getSysTheme() ?? igk_die("can't bind global files");
    $tab = igk_get_env(IGK_ENV_CSS_GLOBAL_CONF_FILES) ?? array();
    foreach (array_keys($tab) as $d) {
        if (file_exists($d)) {
            igk_css_bind_file($theme, null, $d);
        }
    }
    if ($bindfile) {
        igk_css_bind_theme_files($theme);
    }
}
///<summary></summary>
///<param name="th"></param>
///<param name="files"></param>
/**
 * bind single line struct
 * @param mixed $th 
 * @param mixed $files 
 */
function igk_css_bind_theme_file(IGKHtmlDoc $doc, $th, $files)
{
    $f = igk_io_expand_path($files);
    $lfile = explode(";", $f);
    $v_lfiles = array();
    foreach ($lfile as $d) {
        $tab = explode('|', $d);
        list($file, $ctrl) = igk_count($tab) >= 2 ? $tab : [$tab[0], null];

        if (!isset($v_lfiles[$file]) && !empty($d) && file_exists($file)) {
            if ($ctrl) {
                $ctrl = igk_getctrl($ctrl, false);
            }
            igk_css_bind_file($th, $ctrl, $file);
            $v_lfiles[$file] = 1;
        }
    }
}
///<summary>bind file from theme</summary>
/**
 * bind file from theme
 */
function igk_css_bind_theme_files(HtmlDocTheme $theme, ?string $files = null)
{
    $files = $files ?? $theme->getDef()->getFiles() ?? "";
    $lfile = explode(";", igk_io_expand_path($files));

    foreach ($lfile as $d) {
        if (empty($d))
            continue;
        if (strpos($d, '|') === false)
            $d .= "|";
        list($file, $ctrl) = explode('|', $d);
        if (file_exists($file)) {
            $ctrl = !empty($ctrl) ? igk_getctrl($ctrl, false) : null;
            igk_css_bind_file($theme, $ctrl, $file);
        } else {
            igk_ilog("File not found - [{$d}]", __FUNCTION__);
        }
    }
}
///<summary>bind extra style to document</summary>
/**
 * bind extra style to document
 */
function igk_css_bind_wuistyle($document, $ctrl, $type)
{
    // if ($document === null){
    //     igk_trace();
    //     igk_exit();
    // }
    // igk_wln("document : , ", $document);
    $f = igk_realpath($ctrl->getStylesDir() . '/' . $type . '.' . IGK_DEFAULT_STYLE_EXT);
    if (!file_exists($f))
        return 0;
    igk_css_bind_wuistyle_file($document, $f);
    return 1;
}
///<summary></summary>
///<param name="doc"></param>
///<param name="f"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $f 
 */
function igk_css_bind_wuistyle_file($doc, $f)
{
    if (igk_is_ajx_demand()) {
        igk_css_ajx_bind_file($f);
    } else {
        if ($doc) {

            $doc->getTheme()->addTempFile($f);
        }
    }
    return 1;
}
///<summary>eval class state and return the affected value</summary>
/**
 * eval class state and return the affected value
 */
function igk_css_class_state($properties)
{
    $o = "";
    foreach ($properties as $k => $v) {
        if ($v) {
            if (is_callable($v) && !$v())
                continue;
            if (empty($o)) {
                $o .= " ";
            }
            $o .= $k;
        }
    }
    return $o;
}
///<summary></summary>
///<param name="value"></param>
///<param name="gcl"></param>
///<param name="v_designmode"></param>
/**
 * 
 * @param mixed $value 
 * @param mixed $gcl 
 * @param mixed $v_designmode 
 */
function igk_css_design_color_value($value, $gcl, $v_designmode)
{
    if ($v_designmode) {
        return "var(" . IGK_CSS_VAR_COLOR_PREFIX . $value . ")";
    }
    $b = igk_css_get_color_value($value, $gcl);
    return $b;
}
///<summary></summary>
/**
 * 
 */
function igk_css_design_mode()
{
    return igk_app()->session->CssDesignMode ?? false;
}
///<summary></summary>
///<param name="value"></param>
///<param name="properties"></param>
///<param name="v_designmode"></param>
/**
 * 
 * @param mixed $value 
 * @param mixed $properties 
 * @param mixed $v_designmode 
 */
function igk_css_design_property_value($value, $properties, $v_designmode)
{
    if ($v_designmode) {
        return "var(" . IGK_CSS_VAR_PROPERTY_PREFIX . $value . ")";
    }
    $b = igk_getv($properties, $value);
    return $b;
}
///<summary>get document style definition</summary>
/**
 * get document style definition
 */
function igk_css_doc_get_def($doc, $minfile = false, $themeexport = false)
{
    $el = $minfile ? IGK_STR_EMPTY : IGK_LF;
    $s = (object)array("name" => "systheme", "theme" => $doc->SysTheme);
    $data[] = $s;
    if ($files = $s->theme->def->getFiles()) {
        igk_css_bind_theme_file($doc, $s->theme, $files);
    }
    $files = $s->theme->def->getBindTempFiles();
    if ($files) {
        igk_css_bind_theme_file($doc, $s->theme, $files);
    }
    $outcl = [];
    igk_css_bind_color_request(function ($cl) use (&$outcl) {
        if (isset($outcl[$cl])) {
            return $outcl[$cl];
        }
        $outcl[$cl] = $cl;
        return "transparent";
    });
    $s = igk_css_init_style_def_workflow($doc);
    $data[] = $s;
    $o = "";
    Logger::info('dist project');
    foreach ($data as $v) {
        $theme = igk_getv($v, 'theme');
        $name = igk_getv($v, 'name');
        if (!$minfile)
            $o .= IGK_START_COMMENT . " CSS. Doc def - [" . $name . "] " . IGK_END_COMMENT . $el;
        $o .= $theme->get_css_def($minfile, $themeexport) . $el;
    }
    if (igk_server_is_local() && (igk_count($outcl) > 0) && ($f = igk_const("IGK_OUTCL_FILE"))) {
        igk_io_w2file($f, "<?php\n" . (function () use ($outcl) {
            $o = "";
            foreach ($outcl as $k => $v) {
                $o .= "\$cl[\"" . $k . "\"]='';" . IGK_LF;
            }
            return $o;
        })());
    }
    return $o;
}
///<return> the value of the key if found in the current theme</return>
/**
 */
function igk_css_get_cl($key)
{
    $theme = igk_app()->getDoc()->SysTheme;
    return igk_getv($theme->cl, $key);
}
///<summary></summary>
///<param name="clname"></param>
///<param name="defaultvalue" default="null"></param>
/**
 * 
 * @param mixed $clname 
 * @param mixed $defaultvalue 
 */
function igk_css_get_color($clname, $defaultvalue = null)
{
    $t = igk_css_get_color_value($clname);
    if ($t)
        return $t;
    return $defaultvalue;
}
///<summary></summary>
///<param name="clname"></param>
///<param name="tab" default="null" ref="true"></param>
/**
 * 
 * @param mixed $clname 
 * @param mixed $tab 
 */
function igk_css_get_color_value($clname, &$tab = null)
{
    $colors = &$tab;
    if ($tab == null) {
        $doc = null;
        if (defined("IGK_FORCSS")) {
            $doc = igk_get_last_rendered_document();
        }
        $theme = ($doc ?? igk_app()->getDoc())->Theme;
        if ($theme)
            $colors = &$theme->def->getCl();
    }
    return igk_css_treatcolor($colors, $clname);
}
///<summary>get css document style definition</summary>
/**
 * get css document style definition
 */
function igk_css_get_doc_style_def($doc, $minfile, $themeexport)
{
    $o = IGK_STR_EMPTY;
    if ($doc === null) {
        $o .= IGK_START_COMMENT . "balafon.css document theme not found ? is init " . IGKApp::IsInit() . " " . session_id() . " " . IGK_END_COMMENT;
        return $o;
    }
    $theme = $doc->Theme;
    if ($theme->files) {
        igk_css_bind_theme_file($doc, $theme, $theme->files);
    }
    $el = $minfile ? IGK_STR_EMPTY : IGK_LF;
    $v_name = $doc->getParam(IGK_DOC_ID_PARAM);
    if (!$minfile)
        $o .= IGK_START_COMMENT . " THEME For Document [" . $v_name . "] " . IGK_END_COMMENT . $el;
    $o .= $theme->get_css_def($minfile, $themeexport);
    return $o;
}
///<summary>get font definition utility</summary>
/**
 * get font definition utility
 */
function igk_css_get_fontdef($name, $definition, $lineseparator = IGK_STR_EMPTY)
{
    $out = IGK_STR_EMPTY;
    $v = $definition;
    if ($name && $definition) {
        if (is_string($v)) {
            $fdd = $v;
            if (file_exists($fdd)) {
                $fresolv = IGKResourceUriResolver::getInstance()->resolve($fdd);
                $format = "truetype";
                $f = "@font-face{";
                $f .= "font-family: \"" . $name . "\"; ";
                $f .= "src: url('" . $fresolv . "') format(\"$format\")";
                $f .= "}";
                $out .= $f . $lineseparator;
            } else {
                igk_ilog(__("Font file not found"));
                igk_wl(IGK_START_COMMENT . "/!\\ Font file not found.[" . $fdd . IGK_END_COMMENT . "]" . $lineseparator);
            }
        } else {
            if (!is_object($v)) {
                igk_ilog(__FILE__ . ":" . __LINE__ . " v not an object");
                return IGK_STR_EMPTY;
            }
            $f = "@font-face{";
            $f .= "font-family: \"{$v->Name}\"; ";
            $tab = $v->Fonts;
            if (is_array($tab)) {
                foreach ($tab as $s => $t) {
                    if (IGKValidator::IsUri($t->File)) {
                        $f .= "src: url('" . $t->File . "') format(\"{$t->format}\");";
                        continue;
                    }
                    $ok = 0;
                    $fdd = "";
                    if (file_exists($t->File)) {
                        $ok = 1;
                        $fdd = igk_io_basepath($t->File);
                    } else {
                        $fdd = ($ok = file_exists(igk_io_syspath($t->File))) ? $t->File : null;
                    }
                    if ($ok) {
                        $f .= "src: url('" . igk_io_baseuri() . "/" . igk_uri($fdd) . "') format(\"{$t->format}\");";
                    } else {
                        $f .= "/* file {$fdd} not exists*/";
                    }
                }
            }
            $f .= "}";
            $out .= $f . $lineseparator;
        }
    }
    return $out;
}
///<summary></summary>
/**
 * 
 */
function igk_css_get_from()
{
    return igk_get_env("sys://css/from");
}
///<summary></summary>
/**
 * 
 */
function igk_css_get_map_selector()
{
    $selector = array();
    $attr = igk_app()->getDoc()->SysTheme->def->getAttributes();
    if ($attr)
        foreach ($attr as $k => $v) {
            $tab = explode(',', $k);
            if (empty($v))
                continue;
            foreach ($tab as $s => $t) {
                if (!empty($t)) {
                    $selector[trim($t)] = $v;
                }
            }
        }
    $attr = igk_app()->getDoc()->Theme->def->getAttributes();
    if ($attr)
        foreach ($attr as $k => $v) {
            $tab = explode(',', $k);
            if (empty($v))
                continue;
            foreach ($tab as $s => $t) {
                if (!empty($t)) {
                    $selector[trim($t)] = $v;
                }
            }
        }
    return $selector;
}

///<summary></summary>
///<param name="propname"></param>
///<param name="value"></param>
/**
 * 
 * @param mixed $propname 
 * @param mixed $value 
 */
function igk_css_get_resolv_style($propname, $value)
{
    return "-webkit-{$propname}: {$value};-ms-{$propname}:{$value}; -moz-{$propname}:{$value}; -o-{$propname}: {$value}; {$propname}: {$value};";
}
///<summary></summary>
///<param name="propnameValue"></param>
/**
 * 
 * @param mixed $propnameValue 
 */
function igk_css_get_resolv_stylei($propnameValue)
{
    $propnameValue = igk_str_rm_last(trim($propnameValue), ";");
    return "-webkit-{$propnameValue}; -ms-{$propnameValue}; -moz-{$propnameValue}; -o-{$propnameValue}; {$propnameValue};";
}
///<summary></summary>
///<param name="classname"></param>
/**
 * get style for glack 
 * @param string $classname 
 */
function igk_css_get_style(string $classname, $theme = null, $systheme = null)
{
    $igk = igk_app();
    if ($igk === null)
        return;
    $theme = $theme ?? $igk->getDoc()->getTheme();
    $sys_theme = $systheme ?? $igk->getDoc()->getSysTheme();
    $b = $theme[$classname];
    if ($b == null) {
        // igk_ilog("/!\\ style not found : " . $classname);
        $b = $sys_theme[$classname];
        if ($b) {
            $o = igk_css_treat($b, false, $sys_theme);
            return $o;
        }
    } else {
        $o = igk_css_treat($b, false, $theme);
        return $o;
    }
    return IGK_STR_EMPTY;
}
///<summary>get css style from map</summary>
///<param name="node"></param>
///<param name="options" default="null" ref="true"></param>
///<param name="style" default="null"></param>
/**
 * get css style from map
 * @param mixed $node 
 * @param mixed $options 
 * @param mixed $style 
 */
function igk_css_get_style_from_map($node, &$options = null, $style = null)
{
    $s = null;
    if ($options) {
        if (isset($options->MapCssSelector)) {
            $s = $options->MapCssSelector;
        } else {
            $options->MapCssSelector = igk_css_get_map_selector();
            $s = $options->MapCssSelector;
        }
    }
    if ($s) {
        $map = igk_get_selector_map($node);
        $style = $style == null ? new CssStyle() : $style;
        $level = count($map);
        foreach ($s as $k => $v) {
            $tlevel = igk_count(explode(" ", $k));
            if ($tlevel > $level)
                continue;
            if (preg_match($map[0], $k)) {
                $tab = array();
                $c = preg_match_all($map[igk_count($map) - 1], $k, $tab);
                if ($c > 0) {
                    $st = $tab[0][0];
                    if ($st == $k) {
                        $style->Load($v, $tlevel, $k);
                    }
                }
            }
        }
        return $style->render();
    }
    return null;
}
///<summary>shortcut to get system media</summary>
/**
 * shortcut to get system media
 */
function igk_css_get_sys_media($id)
{
    $igk = igk_app();
    if ($igk === null)
        return;
    return $igk->getDoc()->SysTheme->getMedia($id);
}
///<summary></summary>
///<param name="theme"></param>
/**
 * 
 * @param mixed $theme 
 */
function igk_css_get_theme_files($theme)
{
    $lfile = explode(";", igk_io_expand_path($theme->def->getFiles()));
    $t = array();
    foreach ($lfile as $d) {
        if (empty($d))
            continue;
        if (strpos($d, '|') === false)
            $d .= "|";
        $file = explode('|', $d)[0];
        $t[$file] = $file;
    }
    return $t;
}
///change css process workflow : 08/02/2018
/**
 * get base style definition
 */
function igk_css_get_basedef(IGKHtmlDoc $doc, bool $no_systheme = false, bool $minfile = false, bool $themeexport = false)
{
    ob_clean();
    $o = IGK_STR_EMPTY;
    $srh = array();
    $el = $minfile ? IGK_STR_EMPTY : IGK_LF;
    $data = array();
    if (!$no_systheme) {
        $data[] = array("name" => "systheme", "theme" => $doc->getSysTheme());
        $data[] = igk_css_init_style_def_workflow($doc);
    } else {
        $data[] = array("name" => "maintheme", "theme" => $doc->getTheme());
    }
    foreach ($data as $v) {
        $theme = $v["theme"];
        $name = $v["name"];
        $o = "";
        Benchmark::mark("theme-export");
        $o .= "/* CSS - [\"{$name}\"] */" . $el;
        $o .= $theme->get_css_def($minfile, $themeexport) . $el;
        Benchmark::expect("theme-export", 0.500);
        $srh[] = $o;
        if (isset($v["doc"])) {
            $srh[] = $v["doc"]->getTemporaryCssDef($minfile, $themeexport) . $el;
        }
    }
    $o = implode("\n", $srh);
    return $o;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_css_get_bg_size($v)
{
    $o = IGK_STR_EMPTY;
    $o .= "-webkit-background-size: " . $v . ";";
    $o .= "-o-background-size: " . $v . ";";
    $o .= "-moz-background-size: " . $v . ";";
    $o .= "background-size: " . $v . ";";
    return $o;
}
///<summary></summary>
///<param name="v"></param>
///<param name="doc" default="null"></param>
/**
 * 
 * @param mixed $v 
 * @param mixed $doc 
 */
function igk_css_get_bgcl($v, bool $themexport, $theme, $systheme = null)
{
    if (empty($v))
        return null;
    $h = igk_css_treat($v, $themexport, $theme, $systheme);
    if ($h == null)
        return null;
    return "background-color: " . $v . ";";
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_css_get_bordercl($v)
{
    if (empty($v))
        return null;
    return "border-color: " . $v . ";";
}
///<summary></summary>
/**
 * 
 */
function igk_css_get_default_style()
{
    $v = <<<ETF
*{ margin : 0px; padding: 0px; }
ETF;

    return $v;
}
///<summary></summary>
///<param name="v"></param>
///<param name="doc" default="null"></param>
/**
 * get fore color
 * @param mixed $v 
 * @param mixed $doc 
 */
function igk_css_get_fcl(string $v, $theme, $systheme)
{
    $h = igk_css_treat($v, false, $theme, $systheme);
    if ($h) {
        return "color: " . $h . ";";
    } else {
        return "color: " . $v . ";";
    }
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_css_get_font($v)
{
    if (empty($v))
        return null;
    return "font-family: \"" . $v . "\";";
}
///<summary>get document theme media</summary>
///<param name="id"></param>
/**
 * get document theme media
 * @param mixed $id 
 */
function igk_css_get_media($id)
{
    return igk_app()->getDoc()->getTheme()->getMedia($id);
}
///<summary></summary>
/**
 * 
 */
function igk_css_header_comment()
{
    $const = "constant";
    $o = <<<EOF
Author: {$const('IGK_AUTHOR')}
Contact : {$const('IGK_AUTHOR_CONTACT')}
Copyright: {$const('IGK_COPYRIGHT')}
EOF;
    return $o;
}
///<summary></summary>
/**
 * 
 */
function igk_css_ie11()
{
    return defined("IGK_IE11_ENGINE") || igk_check_ie_version();
}
///<summary></summary>
///<param name="file"></param>
///<param name="tab" ref="true"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $tab 
 */
function igk_css_include_cache($file, &$tab)
{
    include($file);
}
///<summary> init css style</summary>
/**
 *  init css style
 */
function igk_css_init_doc($doc)
{
    $doc->Theme->resetAll();
}
///<summary></summary>
///<param name="doc"></param>
///<param name="theme" default="null"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $theme 
 */
function igk_css_init_style_def_workflow($doc, $theme = null)
{
    $theme = $theme ?? $doc->getTheme();
    $v_name = $doc->getParam(IGK_DOC_ID_PARAM, "private-document-theme");
    if ($theme) {
        if ($files = $theme->def->getFiles()) {
            igk_css_bind_theme_file($doc, $theme, $files);
            $theme->def->clearFiles();
        }
        if ($files = $theme->def->getBindTempFiles(1)) {
            igk_css_bind_theme_file($doc, $theme, $files);
        }
    }
    return array("name" => $v_name, "theme" => $theme);
}
///<summary></summary>
///<param name="r"></param>
/**
 * 
 * @param mixed $r 
 */
function igk_css_invoke_color_request($r)
{
    $callback = igk_get_env("sys://css/bind_colorrequest");
    if (is_callable($callback)) {
        return call_user_func_array($callback, array($r));
    }
    return null;
}
///<summary></summary>
///<param name="cl"></param>
///<param name="theme" default="null"></param>
/**
 * check if is known color
 * @param mixed $cl 
 * @param mixed $theme 
 */
function igk_css_is_webknowncolor($cl, $theme = null)
{
    if ($cl && preg_match("/#([0-9a-f]{3}|[0-9a-f]{6})$/i", $cl)) {
        return 1;
    }
    $tcl = $theme == null ? igk_app()->getDoc()->SysTheme->cl : $theme;
    if (igk_css_get_color_value($cl, $tcl) != $cl)
        return 1;
    if (igk_get_env("igk_css_render_balafon_style")) {
        $tab = igk_get_env(IGK_SESS_UNKCOLOR_KEY, array());
        $tab[$cl] = $cl;
        igk_set_env(IGK_SESS_UNKCOLOR_KEY, $tab);
    }
    return 0;
}
///<summary>used to bind a theme files to a theme</summary>
///<remark>if no theme defined then the system global theme will be used</remark>
///<remark2>theme is file that contain only color, font and properties definition</remark2>
/**
 * used to bind a theme files to a theme
 */
function igk_css_load_theme($th = null)
{
    $gt = igk_web_get_config("globaltheme", 'default');
    $env_path = igk_get_env("sys://theme/path", [igk_io_basedir(), igk_io_applicationdir()]);
    $r = 0;
    foreach ($env_path as $d) {
        $f = igk_dir($d . "/" . IGK_RES_FOLDER . "/Themes/{$gt}.theme");
        if (file_exists($f)) {
            $r = 1;
            break;
        }
    }
    if (!$r)
        return;
    $th = $th ? $th : igk_app()->getDoc()->getSysTheme();
    $t = array();
    $t["cl"] = &$th->def->getCl();
    $t["prop"] = &$th->def->getParams();
    igk_include_file($f, $t);
}

///<summary>bind css inline file</summary>
///<param name="f">full path to pcss file</param>
/**
 * bind css inline file
 * @param mixed $f full path to pcss file
 */
function igk_css_ob_get_tempfile($f, &$from = null)
{
    $doc = igk_app()->getDoc();
    $vtemp = new HtmlDocTheme($doc, "theme://inline/tempfiles");
    igk_set_env(IGK_CSS_TEMP_FILES_KEY, 1);
    IGKOb::Start();
    igk_css_bind_file($vtemp, null, $f, $vtemp);
    $m = IGKOb::Content();
    $out = "";
    IGKOb::Clear();
    if (!empty($m)) {
        $out .= $m;
    }
    $h = $vtemp->get_css_def(false, false);
    $out .= $h;
    igk_set_env(IGK_CSS_TEMP_FILES_KEY, null);
    $from = igk_css_get_from();
    igk_css_set_from(null);
    unset($vtemp);
    return $out;
}

///<summary>register font package</summary>
///<param name="fontname">the css name font-familly</param>
///<param name="file">basedir font path to existing font</param>
///<param name="document">the document that will bound the font. null if used global document</param>
///<param name="format">default format . TrueType</param>
/**
 * register font package
 * @param mixed $fontname the css name font-familly
 * @param mixed $file basedir font path to existing font
 * @param mixed $document the document that will bound the font. null if used global document
 * @param mixed $format default format . TrueType
 */
function igk_css_reg_font_package($fontname, $file, $document = null, $format = "TrueType")
{
    if (empty($file)) {
        return;
    }
    igk_die(__FUNCTION__ . ": register glyhicons: not implement");
    $igk = igk_app();
    $doc = $document == null ? $igk->getDoc() : $document;
    $ft = $doc->Theme->Font;
    $r = igk_getv($ft, $fontname);
    if (($r == null) || !is_object($r)) {
        $r = (object)array("Name" => $fontname, "Fonts" => array());
        $ft[$fontname] = $r;
    }
    $r->Fonts[$file] = (object)array("File" => $file, "format" => $format);
    $doc->Theme->def["." . $fontname] = "font-family: '" . $fontname . "';";
}
///<summary>register temporary global files</summary>
/**
 * register temporary global files
 */
function igk_css_reg_global_style_file(
    $fname,
    ?IGK\Css\ICssStyleContainer $theme = null,
    ?IGK\Controllers\BaseController $ctrl = null,
    $temp = 0
) {
    $s = igk_io_collapse_path(igk_uri(igk_realpath($fname)));
    $app = igk_uri($ctrl !== null ? "|" . $ctrl->getName() : "");
    if (empty($s)) {
        igk_ilog(__FILE__ . ':' . __LINE__, "filename not found " . $fname);
        return;
    }
    if (igk_current_context() != IGKAppContext::initializing) {
        $doc = igk_app()->getDoc();
        if ($doc) {
            $theme = $theme ?? $doc->getSysTheme();
            if ($temp) {
                $f = $theme->def->getBindTempFiles();
                $s .= $app;
                if (empty($f) || (IGKString::IndexOf($f, $s) === -1)) {
                    if (!empty($f))
                        $s = ";" . $s;
                    $theme->def->setBindTempFiles($f . $s);
                }
            } else {
                $f = $theme->def->getFiles();
                $s .= $app;
                if (empty($f) || (IGKString::IndexOf($f, $s) === -1)) {
                    if (!empty($f))
                        $s = ";" . $s;
                    $theme->def->setFiles($f . $s);
                }
            }
            return;
        }
    }
    if (file_exists($s) && preg_match("/(pgcss|css|pcss)/", igk_io_path_ext($s))) {
        $tkey = IGK_ENV_CSS_GLOBAL_CONF_FILES;
        $tab = igk_get_env($tkey, array());
        $tab[$s] = 1;
        igk_set_env($tkey, $tab);
    }
}
///<summary></summary>
///<param name="fname"></param>
///<param name="theme" default="null"></param>
///<param name="ctrl" default="null"></param>
/**
 * register global theme file
 * @param string $fname file to bind 
 * @param mixed $theme host
 * @param mixed $ctrl controller to use
 */
function igk_css_reg_global_tempfile(
    $fname,
    ?\IGK\Css\ICssStyleContainer $theme = null,
    ?\IGK\Controllers\BaseController $ctrl = null
) {
    return igk_css_reg_global_style_file($fname, $theme, $ctrl, 1);
}
///<summary></summary>
///<param name="th"></param>
///<param name="classname"></param>
///<param name="defaultvalue"></param>
///<param name="override" default="true"></param>
/**
 * 
 * @param mixed $th 
 * @param mixed $classname 
 * @param mixed $defaultvalue 
 * @param mixed $override 
 */
function igk_css_reg_mediaclass($th, $classname, $defaultvalue, $override = true)
{
    $igk = igk_app();
    if (($igk === null) || !$th || !IGKApp::IsInit())
        return;
    $s = $th[$classname];
    if (empty($s)) {
        $th[$classname] = $defaultvalue;
    } else {
        if ($override) {
            $th[$classname] = $defaultvalue;
        } else {
            $th[$classname] = $s . $defaultvalue;
        }
    }
}
///<summary></summary>
///<param name="theme" default="null"></param>
/**
 * 
 * @param mixed $theme 
 */
function igk_css_reg_reset($theme = null)
{
    $igk = igk_app();
    $theme = $theme ?? $igk->getDoc()->SysTheme;
    if ($theme) {
        $def = $theme->def;
        $def->resetParams();
    }
}
///<summary>register svgs symbols file package</summary>
/**
 * register svgs symbols file package
 */
function igk_css_reg_svg_symbol_files($file)
{
    if (!file_exists($file))
        return;
    igk_app()->getDoc()->getSysTheme()->def->regSymbol($file);
}
///<summary></summary>
///<param name="classname"></param>
///<param name="defaultvalue"></param>
///<param name="override" default="true"></param>
/**
 * 
 * @param mixed $classname 
 * @param mixed $defaultvalue 
 * @param mixed $override 
 */
function igk_css_regclass($classname, $defaultvalue, $override = true)
{
    $igk = igk_app();
    if (($igk === null) || !IGKApp::IsInit())
        return;
    igk_css_reg_mediaclass($igk->getDoc()->Theme, $classname, $defaultvalue, $override);
}
///<summary>register color to current theme</summary>
/**
 * register color to current theme
 * @var string $clname color name
 * @var string $value color value
 * @var int $global define global
 */
function igk_css_regcolor($clname, $value, $global = 0, $override = true)
{
    $igk = igk_app();
    if ($igk === null) {
        return;
    }
    if (defined("IGK_FORCSS"))
        $doc = igk_get_last_rendered_document() ?? $igk->getDoc();
    else
        $doc = $igk->getDoc();

    if ($global || (igk_get_env("sys://globalcolor") == 1)) {
        IGKGlobalColor::getInstance()->setGlobalColor($clname, $value);
    } else {
        $def = $doc->getTheme()->def;
        if ($def) {
            $cl = &$def->getCl();
            if (!isset($cl[$clname]) || ($override)) {
                $cl[$clname] = $value;
            }
        }
    }
}
///<summary> register global font to current document</summary>
/**
 *  register global font to current document
 */
function igk_css_regfont($doc, $name, $path)
{
    $doc->Theme->addFont($name, $path);
}
///<summary></summary>
///<param name="doc"></param>
/**
 * 
 * @param mixed $doc 
 */
function igk_css_regglobalfont($doc)
{
    igk_css_regfont($doc, "global", igk_io_basepath(IGK_LIB_DIR . "/Default/" . IGK_RES_FOLDER . "/Fonts/global.ttf"));
}
///<summary>used to register a media query to the default document</summary>
/**
 * used to register a media query to the default document
 */
function igk_css_regmedia($mediaExpression)
{
    $igk = igk_app();
    if ($igk === null)
        return;
    return $igk->getDoc()->Theme->reg_media($mediaExpression);
}
///<summary></summary>
///<param name="picname"></param>
///<param name="link"></param>
/**
 * 
 * @param mixed $picname 
 * @param mixed $link 
 */
function igk_css_regpic($picname, $link)
{
    igk_getctrl(IGK_PIC_RES_CTRL)->regPicture($picname, $link);
}
function igk_css_get_core_comment($id = null)
{
    $o = "/*\r\nBalafon.css Dynamic css-defition \r" . IGK_LF;
    $o .= igk_css_header_comment();
    $o .= "\r\n*/\n";
    if ($id) {
        $o .= "\r\n/* info: { id: " . $id . " }*/" . IGK_LF;
    }
    return $o;
}
///<summary></summary>
/**
 * 
 */
function igk_css_render_balafon_style(IGKHtmlDoc $doc, bool $no_systheme = false, $debug = false)
{
    ob_start();
    $o = "";
    if (!$no_systheme) {
        $o = igk_css_get_core_comment($doc->getId());
    }
    $f = igk_io_currentrelativepath("Caches/cssstyle.cache");
    $t = ($doc === null) || igk_configs()->UseCssCache;
    igk_set_env(__FUNCTION__, 1);
    if ($t) {
        if (file_exists($f)) {
            $o .= IO::ReadAllText($f);
        } else {
            $o = "/*balafon: doc or no-cache available*/" . IGK_LF;
            $o .= IO::ReadAllText(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/balafon.min.css");
        }
    } else {
        $mfile = igk_getr("minfile") || igk_sys_env_production();
        if ($mfile) {
            $o = implode('|', explode("\\r\\n", $o));
        }
        $o .= igk_css_get_basedef($doc, $no_systheme, $mfile);
        if ($t) {
            IO::WriteToFile($f, $o);
        }
    }
    $debug && $o .= "/* finish */";
    $o .= ($r = ob_get_contents()) ? "\n" . $r : "";
    ob_end_clean();
    igk_wl($o);
}
///<summary>render default style</summary>
///<param name="tab">array of theme file</param>
///<param name="doc">document to render </param>
/**
 * helper: render default style
 * @param mixed $tab array of theme file
 * @param mixed $doc document to render 
 */
function igk_css_render_style($tab, $doc = null)
{
    if (defined("IGK_FORCSS"))
        return "";
    $doc = $doc ?? igk_app()->getDoc() ?? igk_die("can't render css style");
    $systheme = $doc->getSysTheme();
    $th = $doc->getTheme();
    igk_css_bind_sys_global_files();
    igk_css_bind_theme_files($th);
    $o = "";
    foreach ($tab as $k => $v) {
        $o .= "{$k}{ " . igk_css_treat($v, false, $th, $systheme) . " }";
    }
    $th->resetAll();
    $systheme->resetAll();
    return $o;
}
///retrieve base.css file. Management
/**
 */
function igk_css_sdk_style_def($ctrl = null, $callback = null)
{
    $doc = igk_get_document("zip_icore");
    $cl = &$doc->SysTheme->getCl();
    $cl["menuLinkColor"] = "#ddd";
    if ($callback) {
        $callback($doc, $cl);
    }
    $o = igk_css_doc_get_def($doc);
    return $o;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_css_set_from($n)
{
    igk_set_env("sys://css/from", $n);
}
///<summary>store no color def </summary>
/**
 * store no color def 
 */
function igk_css_store_no_colordef($f)
{
    $tab = igk_get_env("sys://not_get_colors");
    if (igk_count($tab) > 0) {
        $o = "";
        foreach ($tab as $v) {
            if (!empty($o))
                $o .= IGK_LF;
            $o .= "igk_css_regcolor(\"{$v}\", \"transparent\");";
        }
        igk_io_save_file_as_utf8_wbom($f, $o, true);
    }
}
///<summary>convert string to igk css class name</summary>
/**
 * convert string to igk css class name
 */
function igk_css_str2class_name($s)
{
    $s = preg_replace_callback(
        "/[^0-9a-z_]/i",
        function ($m) {
            return "_";
        },
        $s
    );
    $s = str_replace(" ", "_", $s);
    $s = str_replace(".", "-", $s);
    return $s;
}
///<summary>treat a css theme. evaluate the expression</summary>
///<arg name="theme">source theme of the expression</arg>
///<arg name="v">expression to treat</arg>
///<arg name="themeexport">in theme export</arg>
///<arg name="themeexport">in theme export</arg>
/**
 * treat a css theme. evaluate the expression
 * @param \IGK\Css\ICssStyleContainer  $theme current theme
 * @param string $v value to treat
 * @param \IGK\Css\ICssStyleContainer $systheme parent theme
 * 
 */
function igk_css_treat(
    string $value,
    bool $themeexport,
    \IGK\Css\ICssStyleContainer $theme,
    ?\IGK\Css\ICssStyleContainer $systheme = null
) {
    //+ -----------------------
    //+ | Treat global theme .
    //+ | value can be resolved with other view class chain
    //+ | sample : (sys:.igk-def-c); overflow:hidden
    //+ -----------------------

    $builder = new \IGK\Css\CssThemeResolver();
    $builder->theme = $theme;
    $builder->parent = $systheme;
    return $builder->treat($value, $themeexport);
}
///<summary>treat style properties with sys</summary>
/**
 * treat style properties with sys
 */
function igk_css_treat_bracket($v, $theme, $systheme = null, $gtheme = null, $doc = null)
{
    $doc = $doc ?? igk_app()->getDoc();
    if ($doc == null) {
        igk_wln_e("can't treat bracket\n" . igk_show_trace());
    }
    $systheme = $systheme ? $systheme : $doc->getSysTheme();
    $gtheme = $gtheme ? $gtheme : $doc->Theme;
    $reg2 = IGK_CSS_TREAT_REGEX_2;
    $src = $v;
    $chain = [];
    $syschain = igk_get_env("sys://css/syschain");
    while (($c = preg_match_all($reg2, $v, $match))) {
        for ($i = 0; $i < $c; $i++) {
            $v_m = $match[0][$i];
            $type = $match['name'][$i];
            if (isset($chain[$v_m])) {
                igk_die("looping resolution : " . $v_m . " for [" . $src . "]");
                return $v;
            }
            for ($i = 0; $i < $c; $i++) {
                $v_m = $match[0][$i];
                $v_n = trim($match["name"][$i]);
                if (isset($theme[$v_n])) {
                    $v = str_replace($v_m, igk_css_treat($theme, $theme[$v_n], $systheme), $v);
                } else {
                    $rtab = explode(':', $v_n);
                    if (count($rtab) >= 2) {
                        $v_from = strtolower(trim($rtab[0]));
                        switch ($v_from) {
                            case \IGK\Css\CssThemeResolver::ATTR_G_RESOLV_MODE:
                                $v_nn = explode(',', trim($rtab[1]));
                                $sk = IGK_STR_EMPTY;
                                foreach ($v_nn as $r) {
                                    $g = trim($r);
                                    $sk .= igk_getv($syschain, $g) ?? $systheme->def["." . $g];
                                    $chain[$g] = $sk;
                                }
                                $sk = trim($sk);
                                $v = str_replace($v_m, $sk, $v);
                                break;
                        }
                    } else {
                        $v_nn = explode(',', trim($v_n));
                        $sk = IGK_STR_EMPTY;
                        $rv = IGK_STR_EMPTY;
                        foreach ($v_nn as $r) {
                            $mk = trim($r);
                            $sk .= $gtheme->def[$mk];
                            $rv .= igk_css_treat($theme, $gtheme->def[$mk], $systheme);
                        }
                        $v = str_replace($v_m, $rv, $v);
                    }
                }
            }
        }
    }
    igk_set_env("sys://css/syschain", $syschain);
    return $v;
}
///<param name="v" refout="true">value to update</param>
///<param name="theme">Theme script</param>
///<param name="type">style to resolv value </param>
///<param name="value">style to resolv value </param>
///<param name="document">document </param>
///<exemple type="css" >: body { background-color: [cl:red]}
///where entry is  'background-color: [cl:red]'
///use : igk_css_treat_value('css expression ')
///</exemple>
/**
 * @param mixed $v value to update
 * @param mixed $theme Theme script
 * @param mixed $type style to resolv value 
 * @param mixed $value style to resolv value 
 * @param mixed $document document 
 * @return mixed 
 *  * */
function igk_css_treat_entries(&$v, \IGK\Css\ICssStyleContainer $theme, $type, $value, \IGK\Css\ICssStyleContainer $systheme, $a = "", $stop = "", $themeexport = 0)
{
    igk_die("not allowed. " . __FUNCTION__ . ". please use CssThemeResolver");
}
function &igk_css_get_treat_colors(?array $defColor = null)
{
    static $gcolor;
    if ($gcolor === null) {
        if ($defColor !== null)
            $gcolor = $defColor;
        else
            $gcolor = [];
    }
    return $gcolor;
}

///<summary></summary>
///<param name="colors" ref="true"></param>
///<param name="value"></param>
///<param name="defined" default="false"></param>
/**
 * 
 * @param mixed $colors 
 * @param mixed $value 
 * @param mixed $defined 
 */
function igk_css_treatcolor(&$colors, $value, $defined = false)
{
    if (is_object($value)) {
        igk_die("/!\\ object not allowed to be treated as color ");
    }
    if (empty($value)) {
        return $value;
    }
    $tabpush = array();
    $q = $value;
    $v = "";
    $reg2 = '/{\s*(?P<name>[\w_-]+)\s*\}/i';
    $is_color_chain = is_array(igk_getv($colors, 0));
    $chain_index = 0;
    $rcolors = 0;
    if ($is_color_chain) {
        $rcolors = $colors;
        $colors = igk_getv($rcolors, $chain_index);
    }
    while ($q) {
        if (isset($tabpush[$q])) {
            $g = igk_css_invoke_color_request($q);
            if (!$g) {
                igk_set_env_keys("sys://theme/colorrecursion", $q, $q);
                return 'initial';
            }
            return $g;
        }
        $tabpush[$q] = 1;
        if (isset($colors[$q])) {
            $q = $colors[$q];
        } else {
            if (($c = preg_match_all($reg2, $q, $match))) {
                for ($i = 0; $i < $c; $i++) {
                    $v_m = $match[0][$i];
                    $type = $match['name'][$i];
                    for ($i = 0; $i < $c; $i++) {
                        $v_m = $match[0][$i];
                        $v_n = trim($match["name"][$i]);
                        $q = $v_n;
                    }
                }
            } else {
                if ($is_color_chain && ($chain_index < count($rcolors))) {
                    $colors = array_merge($colors, $rcolors[$chain_index++]);
                    unset($tabpush[$q]);
                } else {
                    break;
                }
            }
        }
    }
    if (!empty($q) && IGKGlobalColor::IsGlobalColor($q)) {
        $q = IGKGlobalColor::getInstance()->Get($q);
    }
    return $q;
}

///<summary></summary>
///<param name="b"></param>
/**
 * 
 * @param mixed $b 
 */
function igk_css_type($b)
{
    $tab = ["igk-default", "igk-success", "igk-warning", "igk-info", "igk-danger"];
    return igk_getv($tab, $b, "igk-default");
}
///<summary></summary>
/**
 * 
 */
function igk_css_type_styles()
{
    return array(
        "default",
        "success",
        "danger",
        "warning",
        "info",
        "active",
        "disable"
    );
}
///<summary>unregister font package</summary>
/**
 * unregister font package
 */
function igk_css_unreg_font_package($fontname)
{
    $igk = igk_app();
    if ($doc = $igk->getDoc()) {
        $ft = $doc->Theme->Font;
        if ($ft !== null) {
            $r = igk_getv($ft, $fontname);
            if ($r !== null) {
                $ft[$fontname] = null;
            }
            $doc->Theme->def["." . $fontname] = null;
        }
    }
}
///<summary></summary>
/**
 * 
 */
function igk_css_var_support()
{
    require_once IGK_LIB_CLASSES_DIR . "/IGKUserAgent.php";

    if (IGKUserAgent::IsSafari()) {
        if (IGKUserAgent::IsOldSafariAgent())
            return false;
        return true;
    }
    return !(!IGKUserAgent::IsMod() && (igk_css_ie11()));
}
///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab 
 */
function igk_csv_get_value_array($tab)
{
    $o = "";
    $i = 0;
    foreach ($tab as $v) {
        if ($i) {
            $o .= igk_csv_sep();
        } else
            $i = 1;
        $o .= igk_csv_getvalue($v);
    }
    return $o;
}

if (!function_exists('igk_csv_readline')){
    /**
     * helper: shorcut to readlines
     * @param string $src source to treat
     * @param string $delimiter string delimiter
     * @param mixed $last_segment will contain last invalid segment
     * @param callable|null $callback 
     * @param ?int $flgas CSVHelper constant flags
     * @return array 
     */
    function igk_csv_readline(string $src, $delimiter = '"', &$last_segment = null, callable $callback = null, ?int $flags=null)
    { 
        return CSVHelper::ReadLines($src, $delimiter, $last_segment, $callback, $flags);
    }
}


///<summary>return a csv entry for a value data</summary>
/**
 * return a csv entry for a value data
 */
function igk_csv_getvalue($v)
{
    return IGKCSVDataAdapter::GetValue($v);
}
///<summary></summary>
/**
 * 
 */
function igk_csv_sep()
{
    return igk_configs()->get("csv_separator", ",");
}
/**
 * get is default controller
 */
function igk_ctrl_is_default_controller(BaseController $ctrl): bool
{
    $s = igk_configs()->default_controller;
    $m = get_class($ctrl);
    return $s && $m && strtolower($s) == strtolower($m);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="k" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $k 
 */
function igk_ctrl_auth_key($ctrl, $k = null)
{
    $s = "sys://auth/" . $ctrl->AppName;
    if ($k)
        $s .= "/" . $k;
    return strtolower($s);
}
///<summary>helper: bind controller's class name to a node </summary>
///<param name="ctrl">a controller </param>
///<param name="n">target node </param>
///<param name="classdef" default="null">extra class</param>
/**
 * helper: bind controller's class name to a node 
 * @param BaseController $ctrl a controller 
 * @param \IGK\System\Html\Dom\HtmlNode $n target node 
 * @param ?string $classdef extra class 
 */
function igk_ctrl_bind_css(BaseController $ctrl, $n, ?string $classdef = null)
{
    $n["class"] = igk_css_str2class_name(strtolower($ctrl->getName())) . ($classdef != null ? " " . $classdef : null);
}
///<summary>used to bind controller css file</summary>
///<param name="ctrl">controller that will be used to bind extra css setting</param>
///<param name="file">pcss file to bind. if null then the primarayCssFile of the controller is used</param>

/**
 * helper: used to bind controller css file 
 * @param mixed $ctrl controller that will be used to bind extra css setting
 * @param \IGK\System\Html\Dom\HtmlDocTheme $theme document used
 * @param string $file pcss file toInitBindingCssFileull then the primarayCssFile of the controller is used
 * @param bool $cssRendering mode for direct rending
 * @param bool $temp tempory
 * */
function igk_ctrl_bind_css_file(\IGK\Controllers\BaseController $ctrl, \IGK\System\Html\Dom\HtmlDocTheme $theme, string $file, bool $cssRendering, $temp = 0)
{
    return \IGK\System\Html\Css\CssUtils::InitBindingCssFile($ctrl, $theme, $file, $cssRendering, $temp);
}
///<summary> controller request to change the lang</summary>
/**
 *  controller request to change the lang
 */
function igk_ctrl_change_lang($ctrl, $p)
{
    $lang = igk_getv($p, 'lang');
    if ($lang) {
        if (R::ChangeLang($lang)) {
            $ctrl->setEnvParam(BaseController::IGK_ENV_PARAM_LANGCHANGE_KEY, 1);
        }
    }
    unset($lang);
}
///<summary>Represente igk_ctrl_current_doc function</summary>
/**
 * Represente igk_ctrl_current_doc function
 */
function igk_ctrl_current_doc()
{
    return igk_getv(igk_get_view_args(), "doc");
}
///<summary>return the current acting view controller</summary>
/**
 * return the current acting view controller
 */
function igk_ctrl_current_view_ctrl()
{
    return igk_get_env(IGKEnvironment::CURRENT_CTRL);
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_ctrl_env_param_key($ctrl)
{
    return "sys://ctrl/" . sha1(get_class($ctrl));
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * shortcut to get environment view args
 * @param mixed $ctrl 
 */
function igk_ctrl_env_view_arg_key(\IGK\Controllers\BaseController $ctrl)
{
    return $ctrl::getEnvKey("view/args");
}
///<summary></summary>
///<param name="ctrn"></param>
///<param name="path" default="null"></param>
/**
 * 
 * @param mixed $ctrn 
 * @param mixed $path 
 */
function igk_ctrl_get_app_uri($ctrn, $path = null)
{
    $c = igk_getctrl($ctrn, false);
    if ($c) {
        return $c->getAppUri($path);
    }
    return null;
}
///<summary>return a controller direct command</summary>
///<param name="ctrl">controller identifier</param>
///<param name="u">function request</param>
///<param name="type">system type</param>
///<param name="port">port request</param>
/**
 * return a controller direct command
 * @param mixed $ctrl controller identifier
 * @param mixed $u function request
 * @param mixed $type system type
 * @param mixed $port port request
 */
function igk_ctrl_get_cmd_uri(BaseController $ctrl, $u = null, $type = 'sys', $port = null)
{
    return \IGK\Helper\UriHelper::GetCmdAction($ctrl, $u, $type, $port);
}
///<summary>get controller info</summary>
/**
 * get controller info
 */
function igk_ctrl_get_ctrl_info($name)
{
    $m = IGKControllerTypeManager::GetControllerTypes();
    $keys = array_keys($m);
    if (isset($m[$name])) {
        $type = $m[$name];
        $i = new IGKCtrlInfo($name, $type);
        return $i;
    }
    return null;
}
///<summary>get configuration setting</summary>
/**
 * get configuration setting
 */
function igk_ctrl_get_setting($ctrl, $n)
{
    return igk_getv($ctrl->Configs, $n);
}
///<summary>include controller view as content. the view to include is the first item of the params table</summary>
///<param name="file">first file of the param </param>
/**
 * include controller view as content. the view to include is the first item of the params table
 * @param mixed $file first file of the param 
 */
function igk_ctrl_include_content($ctrl, $file, $t, $params, $source = null, $dir = null)
{
    $c = igk_getv($params, 0);
    if ($c) {
        if ($dir == null)
            $dir = dirname($file);
        $f = $dir . "/{$c}.phtml";
        if (($f != $file) && file_exists($f)) {
            $dt = array_slice($params, 1);
            igk_ctrl_include_view_file($ctrl, $source, $t->div(), $f, $dt);
        } else {
            $t->div()->addPanel()->Content = __("e.pagenotfound_1", $c);
        }
    }
}
///<summary>include controller view</summary>
/**
 * include controller view
 */
function igk_ctrl_include_view_file($ctrl, $source, $t, $file, $args = null, $fname = null)
{
    $fc = function ($ctrl, $source, $t, $file, $params = null, $fname = null) {
        $dir = dirname(igk_realpath($file));
        $fname = $fname ?? igk_io_basenamewithoutext($file);
        $__local_uri__ = $ctrl->getAppUri(($source ? $source . "/" : "") . $fname);
        igk_set_env("sys://currenturiaccess", $__local_uri__);
        igk_include_set_view($file);
        $viewargs = get_defined_vars();
        include($file);
        igk_include_unset_view($file);
    };
    $of = $fc->bindTo($ctrl);
    $of($ctrl, $source, $t, $file, $args, $fname);
}
///<summary>init controller node css class </summary>
/**
 * init controller node css class 
 */
function igk_ctrl_init_css($ctrl, $node, $classdef = null)
{
    if ($ctrl->getEnvParam(IGK_KEY_CSS_NOCLEAR) == 1)
        return;
    $c = $node["class"];
    if ($c) {
        $c->Clear();
    }
    igk_ctrl_bind_css($ctrl, $node, $classdef);
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_ctrl_is_current_subdomain($ctrl)
{
    return igk_sys_is_subdomain() && get_class(igk_app()->SubDomainCtrl) === get_class($ctrl);
}
///<summary>CHECK if the name is a reserved name</summary>
/**
 * CHECK if the name is a reserved name
 */
function igk_ctrl_is_reservedname($name)
{
    if (preg_match(IGK_PHP_RESERVEDNAME_REGEX, trim($name)))
        return true;
    return false;
}
///<summary>utility load contoller menu stored from database</summary>
///<param name="callback">the callback to call for head menu</param>
/**
 * utility load contoller menu stored from database
 * @param mixed $callback the callback to call for head menu
 */
function igk_ctrl_loadmenu($ctrl, $menutable, $callback)
{
    $t = igk_db_table_select_where($menutable, null, $ctrl, false, array("Sort" => "Asc", "SortColumn" => "clIndex"));
    foreach ($t->Rows as $v) {
        $callback($v);
    }
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_ctrl_notify_key($ctrl)
{
    return "sys://notify/" . $ctrl->getName();
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="u"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $u 
 */
function igk_ctrl_private_folder($ctrl, $u)
{
    return $ctrl->getDataDir() . "/" . IGK_RES_FOLDER . "/.private/" . $u->clLogin;
}
///<summary>render controller document</summary>
///<param name="ctrl">the controller</param>
///<param name="view">the requested view file</param>
///<param name="ctrl">param to pass</param>
///<param name="ctrl">the controller</param>
/**
 * render controller document
 * @param mixed $ctrl the controller
 * @param mixed $view the requested view file
 * @param mixed $ctrl param to pass
 * @param mixed $ctrl the controller
 */
function igk_ctrl_render_doc($ctrl, $view, $param = null, $exit = 1)
{
    $f = $ctrl->getViewFile($view);
    if (file_exists($f)) {
        $ctrl->getView($view, false, $param);
        igk_render_node($ctrl->getTargetNode(), igk_get_document($ctrl, true));
        if ($exit)
            igk_exit();
    }
}
///<summary>used to reset parameters</summary>
///<param name="rg" >regular expression to match pattern</param>
/**
 * used to reset parameters
 * @param mixed $rg regular expression to match pattern
 */
function igk_ctrl_reset_params($ctrl, $rg = "/(.)+/i")
{
    $keys = $ctrl->getParamKeys();
    foreach ($keys as $v) {
        if (preg_match($rg, $v)) {
            $ctrl->unsetParam($v);
        }
    }
}
///<summary>view the current controllerr and render it to cibling targetnode</summary>
///<remark>viewmode might be one in other to check if this view is visible or not</remark>
/**
 * view the current controllerr and render it to cibling targetnode
 */
function igk_ctrl_view($ctrl, $targetnode, $requestedview = IGK_DEFAULT_VIEW, $params = null, $viewmode = 1)
{
    if ($ctrl == null)
        return;
    $bck = $ctrl->getCurrentView();
    $tn = $ctrl->TargetNode;
    $ctrl->TargetNode = $targetnode;
    if ($viewmode == 1) {
        $ctrl->setCurrentView($requestedview, true, null, $params);
    } else {
        $f = $ctrl->getViewFile($requestedview);
        if (file_exists($f)) {
            igk_set_env(IGK_ENV_CTRL_VIEW, $viewmode);
            $ctrl->getView($requestedview, false, $params);
        }
    }
    $ctrl->TargetNode = $tn;
    $ctrl->setCurrentView($bck, false);
}
///<summary>used to load view automacally view for controller according to pattern</summary>
///<param name="ctrl">controller</param>
///<param name="t">target node</param>
///<param name="pattern">pattern prefix filename </param>
/**
 * used to load view automacally view for controller according to pattern
 * @param mixed $ctrl controller
 * @param mixed $t target node
 * @param mixed $pattern pattern prefix filename 
 */
function igk_ctrl_view_load_pattern($ctrl, $t, $pattern = "main_")
{
    $tab = igk_io_getfiles(
        $ctrl->getViewDir(),
        function ($s) use ($pattern) {
            return preg_match("/^" . $pattern . "_(.)+\\.phtml$/", basename($s));
        },
        false
    );
    sort($tab);
    foreach ($tab as $v) {
        $t->addCtrlView($v, $ctrl);
    }
}
///<summary>call View function of this controller. if this controller is not a child or call the parent view</summary>
/**
 * call View function of this controller. if this controller is not a child or call the parent view
 */
function igk_ctrl_viewparent($ctrl)
{
    if ($ctrl->Configs->clParentCtrl && ($p = igk_getctrl($ctrl->Configs->clParentCtrl))) {
        if ($p != null)
            igk_ctrl_viewparent($p);
    } else {
        if ($ctrl->IsVisible)
            $ctrl->View();
    }
}
///<summary></summary>
///<param name="filepath"></param>
/**
 * 
 * @param mixed $filepath 
 */
function igk_ctrl_zone($filepath)
{
    return igk_getv(igk_get_env("sys://ctrl/zone/files"), $filepath);
}
///<summary></summary>
///<param name="filepath"></param>
/**
 * 
 * @param mixed $filepath 
 */
function igk_ctrl_zone_init($filepath)
{
    if (!file_exists($filepath))
        return null;
    $b = igk_get_env("sys://ctrl/zone/files");
    if (!$b) {
        $b = array();
    }
    if (!isset($b[$filepath])) {
        $b[$filepath] = new IGKCtrlZone($filepath);
    }
    igk_set_env("sys://ctrl/zone/files", $b);
    return $b[$filepath];
}
///<summary></summary>
///<param name="amout"></param>
/**
 * 
 * @param mixed $amout 
 */
function igk_currency_getamount($amout)
{
    $c = $amout . IGK_STR_EMPTY;
    if (!strstr($c, "."))
        $amout = $c . ".00";
    return $amout;
}
///<summary>add cron controller</summary>
/**
 * add cron controller
 */
function igk_data_addcron($ctrl)
{
    $n = "";
    if (is_string($ctrl) && IGKValidator::IsUri($ctrl)) {
        $n = $ctrl;
    } else if (is_object($ctrl) && igk_is_controller($ctrl)) {
        $n = $ctrl->getName();
    } else {
        igk_die("can't add " . $ctrl);
    }
    $f = igk_data_get_cron_file();
    $t = (array)igk_get_env("sys://con/settings") ?? igk_json_parse(igk_io_read_allfile($f));
    $b = igk_getctrl(IGK_SESSION_CTRL)->getUri("RunCron&ctrl=" . $n);
    $t[$b] = 1;
    igk_set_env("sys://con/settings", $t);
    igk_io_save_file_as_utf8_wbom($f, igk_json_encode($t), true);
}
///<summary></summary>
/**
 * 
 */
function igk_data_get_cron_file()
{
    return igk_io_basedir() . "/" . IGK_DATA_FOLDER . "/.crons.json";
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_datatypes_getinfo($n)
{
    $c = igk_getctrl(IGK_DATA_TYPE_CTRL);
    return $c->getInfo($n);
}
///<summary> compare 2 date string. format: YYYYMMDD</summary>
/**
 *  compare 2 date string. format: YYYYMMDD
 */
function igk_date_compare($date1, $date2)
{
    try {
        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);
        if ($date1 > $date2)
            return 1;
        if ($date1 < $date2)
            return -1;
        return 0;
    } catch (Exception $ex) {
        igk_wln("some exception: " . $ex->getMessage());
        return -2;
    }
}
///<summary>return the higher date according to format</summary>
/**
 * return the higher date according to format
 */
function igk_date_last_date($date1, $date2, $v_format = "Y-m-d")
{
    $s1 = igk_time_span($v_format, $date1);
    $s2 = igk_time_span($v_format, $date2);
    return $s1 <= $s2 ? $date2 : $date1;
}
///<summary></summary>
///<param name="format" default="null"></param>
/**
 * 
 * @param mixed $format 
 */
function igk_date_now($format = null)
{
    if ($format)
        return date($format);
    return date(IGK_DATETIME_FORMAT);
}
///<summary> backup controller data as xml object</summary>
/**
 * helper:: backup controller data as xml object
 * @return node
 */
function igk_db_backup_ctrl($ctrl, $defentries = 1)
{
    return \IGK\System\Database\Helper\DbUtility::BackupDataSchema($ctrl, $defentries);
}
///<summary></summary>
///<param name="text"></param>
/**
 * 
 * @param mixed $text 
 */
function igk_db_clean_text($text)
{
    igk_load_library("treat");
    $c = array($text);
    igk_protect_request($c);
    return $c[0];
}
///<summary></summary>
///<param name="ctrlOrAdapterName"></param>
///<param name="table"></param>
///<param name="dbname" default="null"></param>
/**
 * 
 * @param mixed $ctrlOrAdapterName 
 * @param mixed $table 
 * @param mixed $dbname 
 */
function igk_db_clear_table($ctrlOrAdapterName, $table, $dbname = null)
{
    $adapt = igk_get_data_adapter($ctrlOrAdapterName, false);
    if ($adapt) {
        $adapt->connect($dbname);
        $r = $adapt->deleteAll($table);
        $adapt->close();
        return $r;
    }
    return null;
}
///<summary>close db and die with a message</summary>
/**
 * close db and die with a message
 */
function igk_db_close_die($db, $msg)
{
    $db->close();
    igk_die($msg);
}
///<summary>compare time</summary>
/**
 * compare time
 */
function igk_db_cmp_time($type, $format, $oldd, $newd)
{
    $o = igk_time_span($format, $oldd);
    $n = igk_time_span($format, $newd);
    if ($o !== $n) {
        return $n > $o ? 1 : -1;
    }
    return 0;
}

///<summary>Copy filter dataobj value value only. Erase the src data attributes if key not found in data object </summary>
/**
 * Copy filter dataobj value value only. Erase the src data attributes if key not found in data object 
 */
function igk_db_copy_row(&$src, $dataobj, $erase = 1)
{
    foreach ($src as $k => $v) {
        if (isset($dataobj->$k)) {
            $src->$k = $dataobj->$k;
        } else {
            if ($erase)
                $src->$k = null;
        }
    }
}
///<summary>Represente igk_db_count_rows function</summary>
///<param name="table"></param>
///<param name="conditions" default="null"></param>
///<param name="adapter" default="IGK_MYSQL_DATAADAPTER"></param>
/**
 * Represente igk_db_count_rows function
 * @param mixed $table 
 * @param mixed $conditions 
 * @param mixed $adapter 
 */
function igk_db_count_rows($table, $conditions = null, $adapter = IGK_MYSQL_DATAADAPTER)
{
    if ($ad = igk_get_data_adapter($adapter)) {
        if ($ab = $ad->selectCount($table, $conditions)) {
            $r = $ab->getRowAtIndex(0);
            return $r->count;
        }
    }
    return null;
}
///<summary> utility function. used to create object from data. the filter exclude data</summary>
/**
 *  utility function. used to create object from data. the filter exclude data
 */
function igk_db_create_data($obj, $filter)
{
    $c = new StdClass();
    if ($obj) {
        foreach ($obj as $k => $v) {
            if (array_key_exists($k, $filter)) {
                continue;
            }
            $c->$k = $v;
        }
    }
    return $c;
}
///<summary> utility function. create data by filtering with table info</summary>
/**
 *  utility function. create data by filtering with table info
 * @deprecated
 */
function igk_db_create_datafrominfo($adapter, $table, $obj, $tabinfo)
{
    if ($obj === null)
        $c = igk_db_create_row($table);
    foreach ($tabinfo as $k => $v) {
        if ($v->clNotNull) {
            if ($v->clDefault) {
                $c->$k = $v->clDefault;
            } else if ($v->clLinkType) {
                $r = $adapter->select($v->clLinkType)->getRowAtIndex(0);
                if ($r) {
                    $c->$k = $r->clId;
                }
            }
        }
    }
    if ($obj) {
        foreach ($obj as $k => $v) {
            if (array_key_exists($k, $tabinfo)) {
                $c->$k = $v;
            }
        }
    }
    return $c;
}
///<summary>create an empty result</summary>
/**
 * create an empty result
 */
function igk_db_create_emptyresult($ctrl, $result = false)
{
    $g = igk_get_data_adapter($ctrl);
    $s = $g ? $g->CreateEmptyResult($result) : null;
    return $s;
}
///<summary>create a db expression that will be evaluated</summary>
///<exemple></exemple>
/**
 * create a db expression that will be evaluated
 */
function igk_db_create_expression($value)
{
    return new DbExpression($value);
}
///<summary></summary>
///<param name="key"></param>
///<param name="user" default="null"></param>
///<param name="base" default="36"></param>
///<param name="length" default="3"></param>
/**
 * create a db reference number 
 * @param string $key key to use
 * @param ?\IGK\Models\Users $user to use
 * @param int $base encoding counter base
 * @param int $length expected number 
 */
function igk_db_create_identifier($key, $user = null, $base = 36, $length = 3)
{
    $u = $user ?? igk_app()->session->User;
    if ($u == null) {
        $u = igk_get_system_user();
    }
    $ct = igk_getctrl(IGK_UCB_REF_CTRL);
    $number = intval($ct->get_ref_nextnumber($u->clId, $key));
    $c = intval(date("Ymd") . $u->clId);
    $bill_ref = Number::ToBase($number, $base, $length);
    $ct->update_ref_nextnumber($u->clId, $key);
    $c = intval(date("Ymd") . $u->clId);
    $format = Number::ToBase($c, 36, 8);
    $bill_ref = $format . "-" . Number::ToBase($number, 36, 3);
    igk_set_env(__FUNCTION__ . "/number", $number);
    return $bill_ref;
}
///<summary></summary>
/**
 * 
 */
function igk_db_create_opt_obj()
{
    $obj = igk_createobj();
    $obj->Operand = "AND";
    $s = DbQueryResult::CALLBACK_OPTS;
    $obj->$s = null;
    $obj->Sort = null;
    $obj->SortColumn = null;
    return $obj;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="model" default="null"></param>
///<param name="base" default="36"></param>
///<param name="length" default="4"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $model 
 * @param mixed $base 
 * @param mixed $length 
 */
function igk_db_create_ref($ctrl, $model = null, $base = 36, $length = 4)
{
    return igk_getctrl(IGK_CB_REF_CTRL)->get_ref($ctrl, $model, $base, $length);
}
///<summary>create and empty row from global system datatable name</summary>
///<param name="tablename">registrated global sql data table</param>
///<param name="dataobj">object that will fill the value</param>
///<param name="schema">schema file </param>
/**
 * create and empty row from global system datatable name
 * @param mixed $tablename registrated global sql data table
 * @param mixed $dataobj object that will fill the value 
 * @deprecated
 */
function igk_db_create_row($tablename, $dataobj = null, $schema = null)
{
    return DbSchemas::CreateRow($tablename, null, $dataobj);
}
///<summary>create and empty row from ctrl data table datatable na.me</summary>
/**
 * create and empty row from ctrl data table datatable na.me
 */
function igk_db_create_row_obj($tablename, $ctrl, $dataobj = null)
{
    $tb = igk_db_ctrl_datatable_info_key($ctrl, $tablename);
    return igk_db_create_obj_from_infokey($tb, $dataobj);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="table"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $table 
 */
function igk_db_ctrl_datatable_info_key($ctrl, $table)
{
    $tc = $ctrl->getEnvParam(IGK_CTRL_TABLE_INFO_KEY, array());
    $h = igk_getv($tc, $table);
    if ($h) {
        return $h;
    }
    $c = $ctrl->getDataTableInfo();
    if (!$c) {
        return null;
    }
    if ($ctrl->getUseDataSchema()) {
        $h = igk_getv($c, $table) ?? igk_die(__FUNCTION__ . ", no table {$table} found in [{$ctrl->getName()}]");
        $d = igk_db_ref_keyinfo($h);
        if ($d != null) {
            $tc[$table] = $d;
            $ctrl->setParam(IGK_CTRL_TABLE_INFO_KEY, $tc);
        }
        return $d;
    } else {
        if ($ctrl->getDataTableName() != $table)
            return null;
        if (igk_count($c) > 0) {
            $m = igk_array_object_refkey($c, IGK_FD_NAME);
            $ctrl->setEnvParam(IGK_CTRL_TABLE_INFO_KEY, array($table => $m));
            return $m;
        }
        return null;
    }
}
///<summary>Represente igk_db_current_data_adapter function</summary>
/**
 * Represente igk_db_current_data_adapter function
 */
function igk_db_current_data_adapter()
{
    if ($d = igk_db_current_data_driver()) {
        $adapter_name = DbQueryDriver::$Config[$d]["data_adapter"];
        return igk_get_data_adapter($adapter_name);
    }
    return null;
}
///<summary>Represente igk_db_current_data_driver function</summary>
/**
 * Represente igk_db_current_data_driver function
 */
function igk_db_current_data_driver()
{
    return igk_getv(DbQueryDriver::$Config, "db");
}
///<summary></summary>
///<param name="adapter">data adapter</param>
///<param name="table">table name</param>
///<param name="entry"></param>
///<param name="tabinfo" default="null"></param>
///<param name="present" default="null" ref="true"></param>
///<param name="ptype" default="null" ref="true"></param>
/**
 * 
 * @param mixed $adapter data adapter
 * @param mixed $table table name
 * @param mixed $entry 
 * @param mixed $tabinfo 
 * @param mixed $present 
 * @param mixed $ptype 
 */
function igk_db_data_is_present($adapter, $table, $entry, $tabinfo = null, &$present = null, &$ptype = null)
{
    if ($tabinfo == null) {
        $tabinfo =  DbSchemas::GetTableColumnInfo($table);
    }
    if (($entry == null) || ($tabinfo == null))
        return false;
    $t = array();
    $uniquecolumn = array();
    foreach ($entry as $k => $v) {
        if (!isset($tabinfo[$k])) {
            igk_die("[" . __FUNCTION__ . "] - Column [$k] not found in table `{$table}` definition. maybe you must reset  datatable cache");
            igk_exit();
        }
        $s = $tabinfo[$k];
        if ($s->clAutoIncrement) {
            continue;
        }
        if ($s->clIsUnique) {
            $t[$k] = $v;
        }
        if ($s->clIsUniqueColumnMember) {
            $rtab = null;
            if (!empty($s->clColumnMemberIndex)) {
                $rindexes = explode("-", $s->clColumnMemberIndex);
                foreach ($rindexes as $gk) {
                    if (!is_numeric($gk))
                        continue;
                    if (isset($uniquecolumn[$gk]))
                        $rtab = $uniquecolumn[$gk];
                    else
                        $rtab = array();
                    $rtab[$k] = $v;
                    $uniquecolumn[$gk] = $rtab;
                }
            } else {
                if (isset($uniquecolumn[0]))
                    $rtab = $uniquecolumn[0];
                else
                    $rtab = array();
                $rtab[$k] = $v;
                $uniquecolumn[0] = $rtab;
            }
        }
    }
    if (igk_count($t) > 0) {
        foreach ($t as $k => $v) {
            $m = array($k => $v);
            if (($g = igk_db_table_select_where($table, $m, $adapter)) && $g->RowCount > 0) {
                $present = $g;
                $ptype = 1;
                return true;
            }
        }
    }
    if (igk_count($uniquecolumn) > 0) {
        foreach ($uniquecolumn as $k => $v) {
            if (($g = igk_db_table_select_where($table, $v, $adapter)) && ($g->RowCount > 0)) {
                $present = $g;
                $ptype = 2;
                return true;
            }
        }
    }
    return false;
}
///<summary></summary>
///<param name="controllerOrAdpaterName"></param>
///<param name="table"></param>
///<param name="where" default="null"></param>
///<param name="dbname" default="null"></param>
///<param name="leaveOpen" default="false"></param>
/**
 * 
 * @param mixed $controllerOrAdpaterName 
 * @param mixed $table 
 * @param mixed $where 
 * @param mixed $dbname 
 * @param mixed $leaveOpen 
 */
function igk_db_delete($controllerOrAdpaterName, $table, $where = null, $dbname = null, $leaveOpen = false)
{
    $adapt = igk_get_data_adapter($controllerOrAdpaterName, false);
    if ($adapt) {
        $adapt->connect($dbname);
        $r = $adapt->delete($table, $where);
        $adapt->close($leaveOpen);
        return $r;
    } else {
        igk_wln("adapt is null");
    }
    return null;
}
///<summary></summary>
///<param name="r"></param>
/**
 * 
 * @param mixed $r 
 */
function igk_db_delete_cookie($r)
{
    $table = igk_db_get_table_name(IGK_TB_COOKIESTORE);
    $ctrl = igk_db_get_datatableowner($table);
    return igk_db_delete($ctrl, $table, is_object($r) ? $r->clId : $r);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="where"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $where 
 */
function igk_db_deletec($ctrl, $where)
{
    return igk_db_delete($ctrl, $ctrl->getDataTableName(), $where, null);
}
///<summary>drop application table from system config</summary>
/**
 * drop application table from system config
 * @deprecated use [ControllerClasss::dropDb] macros
 */
function igk_db_drop_ctrl_db($ctrl, $tb = null, $fc = null)
{
    $s = igk_is_conf_connected() || igk_user()->auth($ctrl->getName() . ":" . $fc);
    if (!$s) {
        if (igk_app_is_uri_demand($ctrl, $fc)) {
            igk_navto($ctrl->getAppUri());
        }
        return;
    }
    if (!$ctrl->getUseDataSchema()) {
        $db = igk_get_data_adapter($ctrl, true);
        if ($db && $db->connect()) {
            $db->dropTable($ctrl->getDataTableName());
            $db->close();
        }
    } else {
        $db = igk_get_data_adapter($ctrl, true);
        if ($db) {
            if ($db->connect()) {
                $v_tblist = array();
                foreach ($tb as $k => $v) {
                    $v_tblist[$k] = $k;
                }
                $rdb = $ctrl->db;
                if ($rdb && method_exists($rdb, "onStartDropTable")) {
                    $rdb->onStartDropTable();
                }
                igk_hook(IGKEvents::HOOK_DB_START_DROP_TABLE, $ctrl);
                $db->dropTable($v_tblist);
                $db->close();
            }
        }
    }
    if (igk_app_is_uri_demand($ctrl, "dropdb")) {
        igk_navto($ctrl->getAppUri());
    }
}
///<summary></summary>
///<param name="key"></param>
/**
 * 
 * @param mixed $key 
 */
function igk_db_drop_identifier($key)
{
    $ct = igk_getctrl(IGK_UCB_REF_CTRL);
    $ct->delete_key($key);
}
///<summary></summary>
///<param name="ctrlorName"></param>
///<param name="table"></param>
///<param name="leaveOpen" default="false"></param>
/**
 * 
 * @param mixed $ctrlorName 
 * @param mixed $table 
 * @param mixed $leaveOpen 
 */
function igk_db_drop_table($ctrlorName, $table, $leaveOpen = false)
{
    $apt = igk_get_data_adapter($ctrlorName);
    if ($apt) {
        $apt->connect();
        $apt->dropTable($table);
        $apt->close($leaveOpen);
    }
}
///<summary> used to dump query result</summary>
/**
 *  used to dump query result
 */
function igk_db_dump_result($result)
{
    $r = igk_create_node();
    if ($result === null) {
        $r->Content = "No result";
    } else {
        $r->div()->Content = "Count : " . $result->RowCount;
        $tb = $r->addTable();
        $tr = $tb->addTr();
        foreach ($result->Columns as $v) {
            $td = $tr->add("th");
            $td->Content = $v->name;
        }
        foreach ($result->Rows as $v) {
            $tr = $tb->addTr();
            foreach ($v as $t) {
                $tr->addTd()->Content = $t;
            }
        }
    }
    $r->renderAJX();
}
///<summary></summary>
///<param name="message"></param>
/**
 * 
 * @param mixed $message 
 */
function igk_db_error($message)
{
    igk_app()->session->setParam("db_error_msg", $message);
}
///<summary>create a field objet</summary>
/**
 * create a field objet
 */
function igk_db_field($n, $op = '=')
{
    $o = (object)array(
        IGK_FD_NAME => $n,
        "clOperator" => $op,
        "obj:type" => __FUNCTION__
    );
    $o->getValue = function () use ($o) {
        igk_wln(array_keys(get_defined_vars()));
        return $o->clName;
    };
    return $o;
}
///<summary>create and filter an object data table to insert in form</summary>
/**
 * create and filter an object data table to insert in form
 */
function igk_db_form_data($tablanename, $callbackfilter = null)
{
    $fi = $callbackfilter !== null;
    $row = igk_db_create_row($tablanename);
    $t = array();
    foreach ($row as $k => $v) {
        if ($fi && $callbackfilter($k, $t)) {
            continue;
        }
        $t[$k] = array();
    }
    return $t;
}
///<summary>get gloval config properties</summary>
/**
 * get gloval config properties
 */
function igk_db_get_config($n, $default = null, $comment = null, $init = 0)
{
    return igk_getctrl(IGK_BDCONFIGS_CTRL)->getConfigv($n, $default, $comment, $init);
}
///<summary>register configuration properties</summary>
/**
 * register configuration properties
 */
function igk_db_get_configp($ctrl, $n, $default = null)
{
    return igk_db_get_config(strtolower($ctrl->getName()) . "://" . $n, $default);
}
///<summary>get registered user configuration </summary>
/**
 * get registered user configuration 
 */
function igk_db_get_configup($ctrl, $u, $n, $default = null, $comment = null, $init = 0)
{
    return igk_db_get_config(strtolower($ctrl->getName()) . "://" . $u->clLogin . "/" . $n, $default, $comment, $init);
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_db_get_ctrl_tables($ctrl)
{
    return igk_getctrl(IGK_MYSQL_DB_CTRL, true)->getTablesFor($ctrl);
}
///<summary></summary>
///<param name="tablename"></param>
/**
 * 
 * @param mixed $tablename 
 */
function igk_db_get_datatableowner($tablename)
{
    return igk_getctrl(IGK_MYSQL_DB_CTRL, true)->getDataTableCtrl($tablename);
}
///<summary></summary>
///<param name="controllerOrAdpaterName"></param>
///<param name="table"></param>
///<param name="dbname" default="null"></param>
/**
 * 
 * @param mixed $controllerOrAdpaterName 
 * @param mixed $table 
 * @param mixed $dbname 
 */
function igk_db_get_entries($controllerOrAdpaterName, $table, $dbname = null)
{
    $adapt = igk_get_data_adapter($controllerOrAdpaterName, false);
    if ($adapt) {
        $adapt->connect($dbname);
        $r = $adapt->selectAll($table);
        $adapt->close();
        return $r;
    }
    return null;
}
///<summary></summary>
/**
 * 
 */
function igk_db_get_error()
{
    return igk_app()->session->getParam("db_error_msg");
}
///<summary>Represente igk_db_get_model_class_name function</summary>
///<param name="name"></param>
///<param name="ctrl" default="null"></param>
/**
 * Represente igk_db_get_model_class_name function
 * @param mixed $name 
 * @param mixed $ctrl 
 */
function igk_db_get_model_class_name($name, $ctrl = null)
{
    $b = $ctrl ? $ctrl : igk_getctrl(IGK_MYSQL_DB_CTRL)->getDataTableCtrl($name);
    if ($b === null) {
        if (!($c = \IGK\Controllers\SysDbControllerManager::GetDataTableDefinition($name))) {
            igk_wln_e("failed to get controller from " . $name, $c);
        }
        $b = $c->controller;
    }
    $nss = $b->getEntryNamespace();
    $ns = igk_db_get_table_name("%prefix%", $b);
    $k = $name;
    $gs = !empty($ns) && strpos($k, $ns) === 0;
    $t = $gs ? str_replace($ns, "", $k) : $k;
    $name = preg_replace("/\\s/", "_", $t);
    $name = implode("", array_map("ucfirst", array_filter(explode("_", $name))));
    foreach (array_filter([$nss, "IGK"]) as $b) {
        $tn = implode("\\", array_filter([$b, "Models", $name]));
        if (class_exists($tn)) {
            return $tn;
        }
    }
    return null;
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_db_get_schema_filename($ctrl)
{
    return \IGK\Controllers\ControllerExtension::getDataSchemaFile($ctrl);
}
///<summary></summary>
///<param name="ad"></param>
///<param name="table"></param>
///<param name="tabinfo"></param>
///<param name="row"></param>
///<param name="syncRefTable" ref="true"></param>
/**
 * 
 * @param mixed $ad 
 * @param mixed $table 
 * @param mixed $tabinfo 
 * @param mixed $row 
 * @param mixed $syncRefTable 
 */
function igk_db_get_sync_row_data($ad, $table, $tabinfo, $row, &$syncRefTable)
{
    $obj = igk_db_create_row($table);
    if ($row) {
        foreach ($row as $k => $v) {
            $clinfo = igk_getv($tabinfo, $k);
            if (!$clinfo)
                continue;
            if (isset($clinfo->clLinkType) && isset($row->$k)) {
                if (!isset($syncRefTable[$clinfo->clLinkType])) {
                    $syncRefTable[$clinfo->clLinkType] = array();
                }
                $ii = $syncRefTable[$clinfo->clLinkType];
                $g = null;
                if (!isset($ii[$row->$k])) {
                    $g = $ad->select($clinfo->clLinkType, array(IGK_FD_ID => $row->$k))->getRowAtIndex(0);
                    unset($g->clId);
                    $ii[$row->$k] = $g;
                    $syncRefTable[$clinfo->clLinkType] = $ii;
                }
                $obj->$k = "{\"t\":\"{$clinfo->clLinkType}\",\"id\":\"{$row->$k}\"}";
                continue;
            }
            $obj->$k = $row->$k;
        }
    }
    return $obj;
}
///<summary>return the declared table definition structure</summary>
/**
 * return the declared table definition structure
 */
function igk_db_get_table_def($adaptername = IGK_MYSQL_DATAADAPTER)
{
    $v_dictionary = array();
    foreach (igk_sys_getall_ctrl() as $k) {
        if ((($k->getDataAdapterName() != $adaptername) || (($v_info = $k->getDataTableInfo()) === null)) || empty($v_name = $k->getDataTableName()))
            continue;
        $te = null;
        if (isset($v_info["data-schema"]) || is_array($v_info) && is_array(igk_getv(array_values($v_info), 0))) {
            foreach ($v_info as $table => $rinfo) {
                if ($table == "data-schema")
                    continue;
                $info = (object)array(
                    "tableName" => $table,
                    "tableInfo" => DbColumnInfo::AssocInfo(
                        $rinfo,
                        $table
                    ),
                    "Ctrl" => $k,
                    IGK_ENTRIES_TAGNAME => $te,
                    "AdapterName" => $adaptername
                );
                $v_dictionary[$info->tableName] = $info;
            }
        } else {
            $info = (object)array(
                "tableName" => $v_name,
                "tableInfo" => DbColumnInfo::AssocInfo(
                    $v_info,
                    $v_name
                ),
                "Ctrl" => $k,
                IGK_ENTRIES_TAGNAME => $te,
                "AdapterName" => $adaptername
            );
            $v_dictionary[$info->tableName] = $info;
        }
    }
    return $v_dictionary;
}
///<summary>get table defition of a mysql db definition</summary>
/**
 * get table defition of a mysql db definition
 */
function igk_db_get_table_info($table)
{
    $tab = igk_getctrl(IGK_MYSQL_DB_CTRL)->getDataTableDefinition($table);
    return $tab;
}
///<summary>Resolv table name</summary>
///<param name="name"></param>
/**
 * shortcut: resolv table name
 * @param mixed $name 
 * @return null|string resolved table name
 */
function igk_db_get_table_name(?string $name, ?BaseController $ctrl = null)
{ 
    return \IGKSysUtil::DBGetTableName($name, $ctrl);
}
///<summary>get table that contains specified column</summary>
/**
 * get table that contains specified column
 */
function igk_db_get_table_with_column($columnName, $adaptername = IGK_MYSQL_DATAADAPTER)
{
    $dic = igk_db_get_table_def($adaptername);
    $tab = array();
    $columnName = strtolower($columnName);
    foreach ($dic as $v) {
        $tb = array_keys($v->tableInfo);
        foreach ($tb as $t) {
            if (strtolower($t) == $columnName) {
                $tab[] = $v->tableName;
                break;
            }
        }
    }
    return $tab;
}
///<summary>get the definition key in this table</summary>
/**
 * get the definition key in this table
 * @deprecated use DbSchemas::GetTableColumnInfo($table)
 */
function igk_db_getdatatableinfokey(string $tablename)
{
    if (!is_string($tablename)) {
        igk_die("tablename not a string");
    }
    if (($ctrl = igk_getctrl(IGK_MYSQL_DB_CTRL)) && ($tab = $ctrl->getDataTableDefinition($tablename))) {
        return igk_array_object_refkey(igk_getv($tab, 'ColumnInfo'), IGK_FD_NAME);
    }
    return null;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_db_getdefaultv($v)
{
    if ($v->clNotNull) {
        switch (strtolower($v->clType)) {
            case "int":
            case "float":
                if (empty($v->clDefault))
                    return 0;
                break;
        }
        if ($v->clDefault === null) {
            return "";
        }
    }
    return $v->clDefault;
}
///<summary></summary>
///<param name="ctrlorName"></param>
///<param name="table"></param>
///<param name="condition"></param>
/**
 * 
 * @param mixed $ctrlorName 
 * @param mixed $table 
 * @param mixed $condition 
 */
function igk_db_getid($ctrlorName, $table, $condition)
{
    $r = igk_db_table_select_where($table, $condition, $ctrlorName);
    if ($r && ($r->RowCount == 1)) {
        return igk_getv($r->getRowAtIndex(0), IGK_FD_ID);
    }
    return null;
}
///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab 
 */
function igk_db_getobj($tab)
{
    $t = array();
    foreach ($tab as $k) {
        $t[$k->clName] = null;
    }
    return (object)$t;
}
///<summary>return an array of table column used to synchronise data</summary>
/**
 * return an array of table column used to synchronise data
 */
function igk_db_getsync_key($tablename)
{
    $tab = igk_getctrl(IGK_MYSQL_DB_CTRL)->getDataTableDefinition($tablename);
    if ($tab) {
        $t = igk_getv($tab, 'SyncKey');
        if ($t === null) {
            $h = igk_array_object_refkey(igk_getv($tab, 'ColumnInfo'), IGK_FD_NAME);
            $m = array();
            $km = array();
            $m['key'] = IGK_STR_EMPTY;
            foreach ($h as $k => $v) {
                if (($k == IGK_FD_ID) || isset($v->clLinkType))
                    continue;
                if ($v->clIsUnique || $v->clIsPrimary) {
                    $m[$k] = 1;
                    igk_db_sync_push_data($km, $m, $k);
                } else if ($v->clIsUniqueColumnMember) {
                    if (!isset($m["_unique_columns"])) {
                        $m["_unique_columns"] = array();
                    }
                    $m["_unique_columns"][] = $k;
                    igk_db_sync_push_data($km, $m, $k);
                }
            }
            unset($k);
            if (igk_count($m) > 0) if (isset($m["_unique_columns"])) {
                if (igk_count($m["_unique_columns"]) == 1) {
                    $s = $m["_unique_columns"][0];
                    igk_db_sync_push_data($km, $m, $s);
                    unset($m["_unique_columns"]);
                }
            }
            $t = explode(',', $m['key']);
        }
        if (is_string($t)) {
            $t = explode(',', $t);
        }
        return $t;
    }
    return null;
}
///<summary> convert array to ossociation key value array</summary>
///<param name="key"> mixed . string|callable to get the identifier</param>
/**
 *  convert array to ossociation key value array
 * @param mixed $key  mixed . string|callable to get the identifier
 */
function igk_db_identifier_array($tb, $key)
{
    $tab = array();
    if ($tb) {
        $is_callable = is_callable($key);
        $s = "";
        foreach ($tb as $v) {
            $s = $key;
            if ($is_callable) {
                $s = $key($v);
                $tab[$s] = $v;
            } else {
                $tab[$v->$s] = $v;
            }
        }
    }
    return $tab;
}

///<summary>Represente igk_db_init_dataschema function</summary>
///<param name="ctrl"></param>
///<param name="dataschema"></param>
///<param name="adapter"></param>
/**
 * helper: init data schema 
 * @param mixed $ctrl controller 
 * @param mixed $dataschema datachema definition
 * @param mixed $adapter 
 */
function igk_db_init_dataschema($ctrl, $dataschema, $adapter)
{
    return DbSchemas::InitData($ctrl, $dataschema, $adapter);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="schema"></param>
///<param name="entries" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $schema 
 * @param mixed $entries 
 */
// function igk_db_init_from_data_schema($ctrl, $schema, $entries = null)
// {
//     $tb = null;
//     $db = igk_get_data_adapter($ctrl, true);
//     if ($db) {
//         if ($db->connect()) {
//             foreach ($schema as $k => $v) {
//                 $n = igk_db_get_table_name($k);
//                 $data = $entries ? igk_getv($entries, $k) : null;
//                 $r = $db->createTable($n, igk_getv($v, 'ColumnInfo'), $data, igk_getv($v, 'Description'), $db->DbName);
//             }
//             $db->close();
//         } else {
//             igk_debug_wln("db not connected");
//         }
//     }
//     return $tb;
// }



///<summary></summary>
///<param name="file"></param>
/**
 * 
 * @param mixed $file 
 */
function igk_db_load_data_and_entries_schemas($file, $ctrl = null)
{
    if (file_exists($file)) {
        $d = HtmlReader::LoadXMLFile($file);
        return igk_db_load_data_and_entries_schemas_node($d, $ctrl);
    }
    return null;
}
///<summary> load data schema from loaded node </summary>
/**
 *  load data schema from loaded node 
 */
function igk_db_load_data_and_entries_schemas_node($d, $ctrl = null)
{
    if ($d === null) {
        igk_wln_e(__FILE__ . ":" . __LINE__, "data is null");
        return;
    }
    $n = $d->TagName == IGK_SCHEMA_TAGNAME ? $d : igk_getv($d->getElementsByTagName(IGK_SCHEMA_TAGNAME), 0);
    if ($n) {
        $obj = (object)array(
            "Data" => null,
            "Entries" => null,
            "Relations" => null,
            "Version" => 1
        );
        $tab = array();
        $relation = array();
        $migrations = [];
        $v_result = igk_db_load_data_schema_array($n, $tab, $relation, $migrations, $ctrl);
        $obj->Data = $tab;
        $obj->Relations = $relation;
        $obj->RelationDef = igk_getv($v_result, "relations");
        $obj->Version = $n["Version"] ?? $n["version"] ?? 1;
        $e = $n ? igk_getv($n->getElementsByTagName(IGK_ENTRIES_TAGNAME), 0) : null;
        $tab = array();
        if ($e)
            igk_db_load_entries_array($e, $tab, $ctrl);
        $obj->Entries = $tab;
        return $obj;
    }
    return null;
}
///<summary>get default entries from data schema</summary>
/**
 * get default entries from data schema
 */
function igk_db_load_data_entries_schemas($file, $ctrl)
{
    $tab = array();
    if (file_exists($file)) {
        $d = HtmlReader::LoadXMLFile($file);
        $n = igk_getv($d->getElementsByTagName(IGK_SCHEMA_TAGNAME), 0);
        $n = $n ? igk_getv($n->getElementsByTagName(IGK_ENTRIES_TAGNAME), 0) : null;
        if ($n) {
            igk_db_load_entries_array($n, $tab, $ctrl);
        }
    }
    return $tab;
}
///<summary>load data schema in array</summary>
/**
 * load data schema in array
 */
function igk_db_load_data_schema_array($n, &$tables, &$tbrelations = null, &$migrations = null, $ctrl = null, $resolvname = true, $reload = false)
{
    return DbSchemas::LoadSchemaArray(...func_get_args());
}
///<summary>load data from schema files</summary>
/**
 * load data from schema files
 */
function igk_db_load_data_schemas($file, $ctrl = null, $resolvname = true, $operation = \IGK\Database\DbSchemasConstants::Migrate)
{
    return DbSchemas::LoadSchema($file, $ctrl, $resolvname, $operation);
}
///<summary>Represente igk_db_load_data_schemas_node function</summary>
///<param name="d"></param>
///<param name="ctrl" default="null"></param>
///<param name="resolvname" default="true"></param>
/**
 * Represente igk_db_load_data_schemas_node function
 * @param mixed $d 
 * @param mixed $ctrl 
 * @param mixed $resolvname 
 */
function igk_db_load_data_schemas_node($d, $ctrl = null, $resolvname = true)
{
    return DbSchemas::GetDefinition($d, $ctrl, $resolvname);
}
///<summary>load db controller entries</summary>
///<param name="ctrl">controller</param>
///<param name="tablename">tablename</param>
///<param name="entries">entry to add</param>
/**
 * load db controller entries
 * @param BaseController $ctrl controller
 * @param string $tablename tablename
 * @param array $entries entry to add
 */
function igk_db_load_entries(BaseController $ctrl, string $tablename, $entries)
{
    $v_r = igk_db_create_row($tablename);
    if (!$v_r) {
        return false;
    }
    $tabinfo =  DbSchemas::GetTableColumnInfo($tablename);
    foreach ($entries as $e => $ee) {
        $v = igk_createobj();
        $b = (object)$ee;
        foreach ($v_r as $k => $v) {
            if (($linktable = $tabinfo[$k]->clLinkType) && (isset($b->$k))) {
                $mv = $b->$k;
                $cond = array();
                $r = null;
                if (preg_match("/\[\s*link\s*:\s*(?P<cond>([^\]]+))\]/", $mv, $cond)) {
                    $r = $ctrl->select($linktable, array(igk_db_create_expression($cond["cond"])), ["Columns" => [IGK_FD_ID => "id"]])->getRowAtIndex(0);
                    if ($r) {
                        $b->$k = intval($r->id);
                    } else
                        $b->$k = null;
                }
            }
            if (isset($b->$k)) {
                $b->$k = $b->$k;
            } else {
                $b->$k = null;
            }
        }
        if (!$ctrl->insert($tablename, $b)) {
            return false;
        }
    }
    return true;
}
///<summary>load entry data</summary>
///<param name="n"></param>
///<param name="tab" ref="true"></param>
/**
 * load entry data
 * @param mixed $n 
 * @param mixed $tab 
 */
function igk_db_load_entries_array($n, &$tab, $ctrl)
{
    $child = $n->Childs;
    if (!$child)
        return;
    foreach ($child as $v) {
        if ($v->TagName !== IGK_ROWS_TAGNAME)
            continue;
        $tb = igk_db_get_table_name($v["For"], $ctrl);
        if (empty($tb))
            continue;
        $c = array();
        $ttb = $v->getElementsByTagName(IGK_ROW_TAGNAME);
        foreach ($ttb as $kk => $vv) {
            $attr = $vv->Attributes;
            if ($attr)
                $c[] = $attr->to_array();
        }
        if (igk_count($c) > 0) {
            $tab[$tb] = $c;
        }
    }
}
///<summary></summary>
///<param name="src"></param>
///<param name="dest" ref="true"></param>
/**
 * 
 * @param mixed $src 
 * @param mixed $dest 
 */
function igk_db_load_row($src, &$dest)
{
    $t = (array)$src;
    foreach ($dest as $k => $v) {
        if (isset($t[$k]))
            $dest->$k = $t[$k];
    }
}
///<summary></summary>
///<param name="result"></param>
///<param name="p"></param>
/**
 * 
 * @param mixed $result 
 * @param mixed $p 
 */
function igk_db_load_to_node($result, $p)
{
    if ($result && $result->RowCount > 0) {
        foreach ($result->Rows as $v) {
            $p->add("Item")->setAttributes($v);
        }
    }
}
///<summary>represent id-name</summary>
/**
 * represent id-name
 */
function igk_db_name_id($r)
{
    return array("text" => __($r->clName), "value" => $r->clId);
}
///<summary>get or set the exception</summary>
/**
 * get or set the exception
 */
function igk_db_no_exception($d = null)
{
    $k = "db://no_exception";
    if (func_num_args() == 0)
        return igk_get_env($k);
    igk_set_env($k, $d);
    return $d;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="tab"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $tab 
 */
function igk_db_objentries($ctrl, $tab)
{
    $ttab = $ctrl->getDataTableInfo();
    if (!$tab)
        return null;
    $v_otab = array();
    foreach ($ttab as $v) {
        $tk = igk_getv($v, IGK_FD_NAME);
        $v_otab[$tk] = igk_getv($tab, $tk);
    }
    return $v_otab;
}
///<summary>used to prefilter unique element entry for selection</summary>
///<note>auto increment is ignored for that reason.</note>
/**
 * used to prefilter unique element entry for selection
 */
function igk_db_prefilter_for_select($entry, $tabinfo)
{
    if ($entry == null)
        return null;
    if ($tabinfo == null) {
        return null;
    }
    $t = array();
    $uniquecolumn = array();
    foreach ($entry as $k => $v) {
        if (!isset($tabinfo[$k])) {
            igk_wln("Column [$k] not found in table definition " . __FUNCTION__);
            igk_html_wln_log("TabInfo", $tabinfo);
            igk_html_wln_log("Entry", $entry);
            igk_wln(igk_show_trace());
            igk_exit();
        }
        $s = $tabinfo[$k];
        if ($s->clAutoIncrement)
            continue;
        if ($s->clIsUnique) {
            $t[$k] = $v;
        }
        if ($s->clIsUniqueColumnMember) {
            $uniquecolumn[$k] = $v;
        }
    }
    if (igk_count($t) > 0)
        return $t;
    return null;
}
///<summary></summary>
///<param name="h"></param>
/**
 * 
 * @param mixed $h 
 */
function igk_db_ref_keyinfo($h)
{
    $c = igk_getv($h, 'ColumnInfo');
    if ($c)
        return igk_array_object_refkey($c, IGK_FD_NAME);
    return $h;
}
///<summary>call update of the reference obj</summary>
/**
 * call update of the reference obj
 */
function igk_db_ref_update(&$ref)
{
    return igk_obj_call($ref, "update");
}
///<summary>register a global system controller</summary>
///<param name="n">name for global controller</param>
///<param name="ctrlobj">controller or objet for that purpose</param>
/**
 * register a global system controller
 * @param mixed $n name for global controller
 * @param mixed $ctrlobj controller or objet for that purpose
 */
function igk_db_reg_sys_ctrl($n, $ctrlobj)
{
    $t = igk_app()->session->getParam(IGKSession::SYSDB_CTRL);
    if (!is_array($t))
        $t = array();
    $t[$n] = $ctrlobj;
    igk_app()->session->setParam(IGKSession::SYSDB_CTRL, $t);
}
///<summary></summary>
///<param name="name"></param>
///<param name="cgctrl" default="null"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $cgctrl 
 */
function igk_db_register_auth($name, $cgctrl = null)
{
    $table = igk_db_get_table_name(IGK_TB_AUTHORISATIONS);
    $cgctrl = $cgctrl ?? igk_db_get_datatableowner($table);
    if ($cgctrl)
        igk_db_insert_if_not_exists($cgctrl, $table, array(IGK_FD_NAME => $name));
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_db_register_group($name)
{
    $table = igk_db_get_table_name(IGK_TB_GROUPS);
    $cgctrl = igk_db_get_datatableowner($table);
    if ($cgctrl)
        igk_db_insert_if_not_exists($cgctrl, $table, array(IGK_FD_NAME => $name));
}

///<summary></summary>
///<param name="u"></param>
///<param name="app"></param>
/**
 * 
 * @param mixed $u 
 * @param mixed $app 
 */
function igk_db_resolv_app_uri($u, $app)
{
    if (preg_match("/^app:/i", $u)) {
        $u = substr($u, 4);
    }
    if (IGKValidator::IsUri($u))
        return $u;
    return $app->getAppUri($u);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="schema"></param>
///<param name="error" default="null" ref="true"></param>
///<param name="ad" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $schema 
 * @param mixed $error 
 * @param mixed $ad 
 */
function igk_db_restore_backup_data($ctrl, $schema, &$error = null, $ad = null)
{
    if (is_string($schema)) {
        $schema = HtmlReader::Load($schema, "xml");
    }
    $e = igk_getv($schema->getElementsByTagName("Entries"), 0);
    $ad = $ad ?? igk_get_data_adapter($ctrl);
    $error = $error ?? [];
    if ($e && $ad->connect()) {
        $ad->stopRelationChecking();
        foreach ($e->getElementsByTagName("Rows") as $row) {
            $table = $row["For"];
            foreach ($row->getElementsByTagName("Row") as $item) {
                $tab = $item->getAttributes()->to_array();
                if (igk_count($tab) > 0) {
                    try {
                        igk_db_insert_if_not_exists($ad, $table, $tab);
                    } catch (Exception $ex) {
                        $error[] = $ex->getMessage();
                    }
                }
            }
        }
        $ad->restoreRelationChecking();
        $ad->close();
    } else {
        igk_ilog("no entries or no adapter found");
    }
    return $schema;
}
///<summary> restore backup data from schema </summary>
///<param name="adapter">the adapter to use</param>
///<param name="schema">mixed : schema to use. if string load xml, or xml schema load object </param>
/**
 *  restore backup data from schema 
 * @param mixed $adapter the adapter to use
 * @param mixed $schema mixed : schema to use. if string load xml, or xml schema load object 
 * @deprecated
 */
function igk_db_restore_backup_data_adapter($adapter, $schema, &$error)
{
    if (is_string($schema)) {
        $schema = HtmlReader::Load($schema, "xml");
    }
    $e = igk_getv($schema->getElementsByTagName("Entries"), 0);
    $ktinfo = igk_db_load_data_and_entries_schemas_node($schema);
    $ad = $adapter;
    $error = $error ?? [];
    if ($e && $adapter->connect()) {
        $ad->stopRelationChecking();
        foreach ($e->getElementsByTagName("Rows") as $row) {
            $table = $row["For"];
            $autoinc = null;
            $tabinfo = igk_db_column_info($ktinfo, $table, $autoinc);
            foreach ($row->getElementsByTagName("Row") as $item) {
                $tab = $item->getAttributes()->to_array();
                if (igk_count($tab) > 0) {
                    try {
                        if ($autoinc)
                            unset($tab[$autoinc]);
                        $present = igk_db_data_is_present($adapter, $table, $tab, $tabinfo);
                        if (!$present) {
                            $adapter->insert($table, $tab);
                            // igk_db_insert($adapter, $table, $tab);
                        }
                    } catch (Exception $ex) {
                        $error[] = $ex->getMessage();
                    }
                }
            }
        }
        $ad->restoreRelationChecking();
        $ad->close();
    } else {
        igk_ilog("no entries or no adapter found", __FUNCTION__);
    }
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="tablename"></param>
///<param name="conditions" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $tablename 
 * @param mixed $conditions 
 */
// function igk_db_select($ctrl, $tablename, $conditions = null)
// {
//     $db = igk_get_data_adapter($ctrl);
//     $r = null;
//     if ($db) {
//         if ($db->connect()) {
//             try {
//                 $r = $db->selectAndWhere($tablename, $conditions);
//             } catch (Exception $ex) {
//                 igk_push_env("sys://lasterror", $ex);
//             }
//             $db->close();
//         }
//     }
//     return $r;
// }
///<summary></summary>
///<param name="ctrl"></param>
///<param name="tablename" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $tablename 
 */
// function igk_db_select_all($ctrl, $tablename = null)
// {
//     return igk_db_select($ctrl, $tablename ? $tablename : $ctrl->DataTableName);
// }
///<summary></summary>
///<param name="ctrl"></param>
///<param name="andcondition"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $andcondition 
 */
// function igk_db_select_wherec($ctrl, $andcondition)
// {
//     $db = igk_get_data_adapter($ctrl);
//     $r = null;
//     if ($db) {
//         $db->connect();
//         try {
//             $r = $db->selectAndWhere($ctrl->DataTableName, $andcondition);
//         } catch (Exception $ex) {
//             igk_log_write_i("error", $ex);
//         }
//         $db->close();
//         return $r;
//     }
//     return null;
// }
///<summary></summary>
///<param name="controllerOrAdpaterName"></param>
///<param name="query"></param>
///<param name="dbname" default="null"></param>
///<param name="leaveOpen" default="false"></param>
/**
 * 
 * @param mixed $controllerOrAdpaterName 
 * @param mixed $query 
 * @param mixed $dbname 
 * @param mixed $leaveOpen 
 */
function igk_db_send_query($controllerOrAdpaterName, $query, $dbname = null, $leaveOpen = false)
{
    $adapt = igk_get_data_adapter($controllerOrAdpaterName, false);
    if ($adapt && method_exists(get_class($adapt), "sendQuery")) {
        $adapt->connect($dbname);
        foreach (explode(";", $query) as $v) {
            $v = trim($v);
            if (!empty($v)) {
                try {
                    $r = $adapt->sendQuery($v);
                } catch (Exception $exception) {
                }
            }
        }
        $adapt->close($leaveOpen);
        return $r;
    }
    return null;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="u"></param>
///<param name="n"></param>
///<param name="value"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $u 
 * @param mixed $n 
 * @param mixed $value 
 */
function igk_db_set_configup($ctrl, $u, $n, $value)
{
    $key = strtolower($ctrl->getName()) . "://" . $u->clLogin . "/" . $n;
    return igk_getctrl(IGK_BDCONFIGS_CTRL)->setConfigv($key, $value);
}
///<summary></summary>
///<param name="identifier"></param>
///<param name="name"></param>
///<param name="date" default="null"></param>
/**
 * 
 * @param mixed $identifier 
 * @param mixed $name 
 * @param mixed $date 
 * @deprecated
 */
function igk_db_store_cookie($identifier, $name, $date = null)
{
    $identifier || igk_die("store cookie identifier is null. Not Allowed");
    $table = igk_db_get_table_name(IGK_TB_COOKIESTORE);
    $ctrl = igk_db_get_datatableowner($table);
    return igk_db_insert_if_not_exists($ctrl, $table, array(
        "clIdentifier" => $identifier,
        IGK_FD_NAME => $name,
        "clDateTime" => $date
    ));
}
///<summary>evaluate expression according to row</summary>
/**
 * evaluate expression according to row
 */
function igk_db_sync_key_eval($row, $expression)
{
    return eval("return \"{$expression}\";");
}
///<summary></summary>
///<param name="km" ref="true"></param>
///<param name="m" ref="true"></param>
///<param name="k"></param>
/**
 * 
 * @param mixed $km 
 * @param mixed $m 
 * @param mixed $k 
 */
function igk_db_sync_push_data(&$km, &$m, $k)
{
    if (!isset($km[$k])) {
        $m['key'] .= empty($m['key']) ? $k : ',' . $k;
        $km[$k] = 1;
    }
}
///<summary>synchronise data of this controller to dataadapter</summary>
///<remark>function init with required links</remark>
/**
 * synchronise data of this controller to dataadapter
 */
function igk_db_sync_todb($ctrl, $adapter = null, $callinit = null)
{
    $ad = $adapter != null ? igk_get_data_adapter($adapter) : igk_get_data_adapter($ctrl);
    if (!$ad || !$ad->connect() || !igk_is_conf_connected())
        return false;
    $dbctrl = igk_getctrl(IGK_MYSQL_DB_CTRL);
    $tables = igk_db_get_ctrl_tables($ctrl); //$dbctrl->getTablesFor($ctrl, true);
    $ad->initForInitDb();
    $tc = $tables;
    $init = [];
    foreach ($tc as $v) {
        $c = $dbctrl->getDataTableCtrl($v);
        if ($c === $ctrl)
            continue;
        if ($c && !isset($init[$c->Name])) {
            $c->initDb();
            $init[$c->Name] = 1;
            unset($tables[$c->Name]);
        }
    }
    $ctrl->initDb();
    $ad->flushForInitDb();
    $ad->close();
    return true;
}
///<summary>get installed system controller</summary>
///<param name="n">name for global controller</param>
/**
 * get installed system controller
 * @param mixed $n name for global controller
 */
function igk_db_sys_ctrl($n)
{
    $t = igk_app()->session->getParam(IGKSession::SYSDB_CTRL);
    if ($t) {
        return igk_getv($t, $n);
    }
    return null;
}
///<summary></summary>
///<param name="table"></param>
///<param name="andcondition" default="null"></param>
///<param name="adapter" default="IGK_MYSQL_DATAADAPTER"></param>
///<param name="leaveOpen" default="false"></param>
/**
 * 
 * @param mixed $table 
 * @param mixed $andcondition 
 * @param mixed $adapter 
 * @param mixed $leaveOpen 
 */
function igk_db_table_count_where($table, $andcondition = null, $adapter = IGK_MYSQL_DATAADAPTER, $leaveOpen = false)
{
    $db = igk_get_data_adapter($adapter);
    $r = null;
    if ($db) {
        if ($db->connect()) {
            try {
                $r = $db->selectCount($table, $andcondition);
            } catch (Exception $ex) {
                igk_ilog($ex, "BLF - SQL Error");
            }
            $db->close();
        }
        return $r;
    }
    return null;
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_db_table_exists($ctrl)
{
    $v = igk_db_table_count_where(igk_db_get_table_name($ctrl->getDataTableName(), $ctrl), null, $ctrl);
    return $v;
}
///<summary>filter object to fit table definition data</summary>
/**
 * filter object to fit table definition data
 * @deprecated
 */
function igk_db_table_filter_data($table, $obj)
{
    $tobj = igk_db_create_row($table);
    if ($tobj) {
        if ($obj && (is_object($obj) || is_array($obj))) {
            foreach ($tobj as $k => $v) {
                $tobj->$k = igk_getv($obj, $k);
            }
        }
    }
    return $tobj;
}


///<summary>select single row</summary>
/**
 * select single row
 */
function igk_db_table_select_row($table, $id, $controllerOrAdapterName = IGK_MYSQL_DATAADAPTER, $leaveopen = false)
{
    $k = null;
    if (is_array($id))
        $k = $id;
    else if (is_object($id))
        $k = (array)$id;
    else
        $k = array(IGK_FD_ID => $id);
    $r = igk_db_table_select_where($table, $k, $controllerOrAdapterName, $leaveopen);
    if ($r && ($r->RowCount == 1))
        return $r->getRowAtIndex(0);
    return null;
}
///<summary></summary>
///<param name="table"></param>
///<param name="andcondition" default="null"></param>
///<param name="adapter" default="IGK_MYSQL_DATAADAPTER"></param>
///<param name="leaveOpen" default="false"></param>
///<param name="options" default="null"></param>
/**
 * 
 * @param mixed $table 
 * @param mixed $andcondition 
 * @param mixed $adapter 
 * @param mixed $leaveOpen 
 * @param mixed $options 
 * @return mixed
 */
function igk_db_table_select_where($table, $andcondition = null, $adapter = IGK_MYSQL_DATAADAPTER, $leaveOpen = false, $options = null)
{
    $db = igk_get_data_adapter($adapter);
    $isad = $adapter == $db;
    $r = null;
    if ($db) {
        if (!$isad && !$db->connect()) {
            if (!igk_sys_env_production()) {
                igk_ilog(__FUNCTION__ . ":Connexion failed");
            }
            return $r;
        }
        try {
            $r = $db->selectAndWhere($table, $andcondition, $options);
        } catch (Exception $ex) {
            igk_elog("error", $ex);
        }
        if (!$isad)
            $db->close($leaveOpen);
    }
    return $r;
}
///<summary></summary>
///<param name="tablename"></param>
///<param name="where" default="null"></param>
/**
 * 
 * @param mixed $tablename 
 * @param mixed $where 
 */
function igk_db_table_xmlview_response($tablename, $where = null)
{
    $tb = igk_create_node("DataTable");
    $tb["name"] = $tablename;
    $dataentry = $tb->addDataEntry();
    $dataentry->LoadData(igk_db_table_select_where($tablename, $where));
    $e = HtmlNode::CreateWebNode("XmlViewer");
    $e->Load($dataentry);
    return $e->render(null);
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_db_unreg_sys_ctrl($n)
{
    $t = igk_app()->session->getParam(IGKSession::SYSDB_CTRL);
    if (isset($t[$n])) {
        unset($t[$n]);
        igk_app()->session->setParam(IGKSession::SYSDB_CTRL, $t);
    }
}
///<summary></summary>
///<param name="controllerOrAdpaterName"></param>
///<param name="table"></param>
///<param name="entry"></param>
///<param name="where" default="null"></param>
///<param name="dbname" default="null"></param>
///<param name="leaveOpen" default="false"></param>
/**
 * 
 * @param mixed $controllerOrAdpaterName 
 * @param mixed $table 
 * @param mixed $entry 
 * @param mixed $where 
 * @param mixed $dbname 
 * @param mixed $leaveOpen 
 */
function igk_db_update($controllerOrAdpaterName, $table, $entry, $where = null, $dbname = null, $leaveOpen = false)
{
    $adapt = igk_get_data_adapter($controllerOrAdpaterName, false, $leaveOpen);
    if ($adapt) {
        $adapt->connect($dbname);
        $r = $adapt->update($table, $entry, $where == null ? array(IGK_FD_ID => $entry->clId) : $where);
        if (!$r && igk_environment()->isDebug()) {
            igk_log_write_i("udpate error", igk_debuggerview()->getMessage());
        }
        $adapt->close($leaveOpen);
        return $r;
    }
    return null;
}
///<summary></summary>
///<param name="identifier"></param>
///<param name="name"></param>
///<param name="date" default="null"></param>
/**
 * 
 * @param mixed $identifier 
 * @param mixed $name 
 * @param mixed $date 
 */
function igk_db_update_cookie($identifier, $name, $date = null)
{
    $table = igk_db_get_table_name(IGK_TB_COOKIESTORE);
    $ctrl = igk_db_get_datatableowner($table);
    return igk_db_update($ctrl, $table, array(
        "clIdentifier" => $identifier,
        IGK_FD_NAME => $name,
        "clDateTime" => $date
    ));
}

///<summary>get system user groups</summary>
///<param name="u">mixed id or user object</param>
/**
 * get system user groups
 * @param mixed $u mixed id or user object
 */
function igk_db_user_groups($u)
{
    $id = $u;
    if (is_object($u))
        $id = $u->clId;
    // $r = \IGK\Models\Usergroups::select_all(array("clUser_Id" => $id));

    $o = \IGK\Models\Groups::prepare()
        ->join([
            \IGK\Models\Usergroups::table() => [
                \IGK\Models\Groups::column("clId") . "=" . \IGK\Models\Usergroups::column("clGroup_Id")
            ]
        ])
        ->join([
            \IGK\Models\Users::table() => [
                \IGK\Models\Users::column("clId") . "=" . \IGK\Models\Usergroups::column("clUser_Id"),
                "type" => "left"
            ],
        ])->distinct(true)
        ->where([
            \IGK\Models\Users::column("clId") => $id
        ])
        ->columns([
            \IGK\Models\Groups::column("*"),
            \IGK\Models\Users::column("clId") => "userid",
            \IGK\Models\Usergroups::column("clId") => "usergroup_id"
        ])
        ->query();
    return $o->getRows();
    // igk_wln_e($o->getRows(), $o->getRows()[0]);

    // igk_db_table_select_where(igk_db_get_table_name(IGK_TB_GROUPS));
    // $o = array();
    // foreach ($r as $v) {
    //     $o[$v->clGroup_Id] = $g->Rows[$v->clGroup_Id]->clName;
    // }
    // return $o;
}
///<summary>Represente igk_db_util_init_row_script function</summary>
///<param name="table"></param>
///<param name="name" default="c"></param>
/**
 * Represente igk_db_util_init_row_script function
 * @param mixed $table 
 * @param mixed $name 
 * @deprecated
 */
function igk_db_util_init_row_script($table, $name = "c")
{
    $c = igk_db_create_row($table);
    $s = "";
    foreach ($c as $k => $v) {
        if (!is_numeric($v) && empty($v)) {
            $v = '""';
        }
        $s .= " \$" . $name . "->" . $k . " = " . $v . ";\n";
    }
    igk_text($s);
}
///<summary>Represente igk_db_view_result_node function</summary>
///<param name="result"></param>
///<param name="uri"></param>
///<param name="selected"></param>
///<param name="max" default="-1"></param>
///<param name="target" default="null"></param>
/**
 * Represente igk_db_view_result_node function
 * @param mixed $result 
 * @param mixed $uri 
 * @param mixed $selected 
 * @param mixed $max 
 * @param mixed $target 
 */
function igk_db_view_result_node($result, $uri, $selected, $max = -1, $target = null)
{
    if (!$result || !igk_reflection_class_implement($result, 'IIGKQueryResult')) {
        return null;
    }
    $n = igk_create_notagnode();
    $r = $n->addTable();
    if ($result->getResultType() == "boolean") {
        $tr = $r->addTr();
        $tr->add("th")->Content = "result";
        $tr = $r->addTr();
        $tr->addTd()->Content = $result->getRowAtIndex(0);
        return $r;
    }
    $tr = $r->addTr();
    $key = array();
    foreach ($result->Columns as $v) {
        $tr->add("th")->Content = $v->name;
        $key[] = $v->name;
    }
    if ($max > 0) {
        igk_html_paginate(
            $r,
            $n,
            $result->Rows,
            $max,
            function ($table, $k, $v) use ($key) {
                $tr = $table->addTr();
                foreach ($key as $j) {
                    $tr->addTd()->Content = $v->$j;
                }
            },
            $uri,
            $selected,
            $target
        );
    } else {
        foreach ($result->Rows as $v) {
            $tr = $r->addTr();
            foreach ($key as $j) {
                $tr->addTd()->Content = $v->$j;
            }
        }
    }
    return $n;
}
///<summary>set if APP DEBUG activity</summary>
/**
 * set if APP DEBUG activity
 */
function igk_debug(?bool $d = null)
{
    igk_environment()->set(IGKEnvironment::DEBUG, $d);
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_debug_die($msg)
{
    if (igk_is_debug()) {
        igk_die($msg);
    }
}
///<summary></summary>
/**
 * 
 */
function igk_debug_flush_data($msg = "")
{
    if (igk_environment()->isDebug()) {
        igk_flush_data($msg);
    }
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_debug_or_local_die($msg)
{
    if (igk_is_debug() || igk_server_is_local()) {
        igk_die($msg);
    }
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_debug_show($msg)
{
    if (!IGKApp::IsInit())
        return;
    if (Server::IsLocal()) {
        $tab = explode(':', $msg);
        $ctrl = igk_getctrl(IGK_DEBUG_CTRL, false);
        if ($ctrl == null)
            return;
        $lb = igk_create_node("div");
        if (count($tab) > 1) {
            $args = array();
            preg_match_all('/((?P<name>([^:]+)):(?P<value>(.)*))$/i', $msg, $args);
            $n = strtolower(trim($args["name"][0]));
            switch ($n) {
                case "warning":
                case "error":
                case "notice":
                case "info":
                    $lb["class"] = "igk-debug-" . $n;
                    $lb->Content = $args["value"][0];
                    $ctrl->addMessage($lb);
                    break;
                default:
                    $lb["class"] = "igk-debug-msg";
                    $lb->Content = $msg;
                    $ctrl->addMessage($lb);
                    break;
            }
        } else {
            $lb["class"] = "igk-debug-msg";
            $lb->Content = $msg;
            $ctrl->addMessage($lb);
        }
    }
}
///<summary></summary>
/**
 * 
 */
function igk_debug_show_dump_info()
{
    igk_show_prev($_REQUEST);
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_debug_wl($msg)
{
    if (igk_environment()->isDebug()) {
        igk_wln($msg);
    }
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_debug_wln($msg)
{
    if (igk_environment()->isDebug()) {
        call_user_func_array('igk_wln', func_get_args());
    }
}
///<summary></summary>
///<param name="condition"></param>
///<param name="tag"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $condition 
 * @param mixed $tag 
 * @param mixed $msg 
 */
function igk_debug_wln_a_i($condition, $tag, $msg)
{
    if ($condition) {
        igk_debug_wln_i($tag, $msg);
    }
}
///<summary>Represente igk_debug_wln_e function</summary>
/**
 * Represente igk_debug_wln_e function
 */
function igk_debug_wln_e()
{
    if (igk_is_debug()) {
        igk_debug_wln(...func_get_args());
        igk_exit();
    }
}
///<summary></summary>
///<param name="tag"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $tag 
 * @param mixed $msg 
 */
function igk_debug_wln_i($tag, $msg)
{
    igk_debug_wln("[$tag] - $msg");
}
///<summary></summary>
///<param name="die" default="true"></param>
/**
 * 
 * @param mixed $die 
 */
function igk_debuggerview($die = true)
{
    if (IGKApp::IsInit() && ($ctrl = igk_getctrl(IGK_DEBUG_CTRL))) {
        return $ctrl->getDebuggerView();
    }
    return null;
}

///<summary></summary>
///<param name="path"></param>
/**
 * 
 * @param mixed $path 
 */
function igk_delete_module($path)
{
    $dir = igk_get_module_dir() . "/{$path}";
    if (!file_exists($dir)) {
        return 0;
    }
    $r = 1;
    IO::RmDir($dir, true);
    return $r;
}
///<summary></summary>
///<param name="obj"></param>
///<param name="closures" ref="true"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $closures 
 */
function igk_detect_closure($obj, &$closures)
{
    $tq = array(array("v" => $obj, "path" => "."));
    $found = false;
    $where = null;
    $path = ".";
    $obj_found = array();
    $idx = 0;
    $depth = 0;
    $closures = array();
    $first = 0;
    $debug = 0;
    while ($cq = array_pop($tq)) {
        $q = $cq["v"];
        $path = $cq["path"];
        $depth++;
        if (igk_is_closure($q)) {
            $found = 1;
            $closures[] = $path;
            if ($first)
                break;
            continue;
        }
        if (is_object($q)) {
            $hash = spl_object_hash($q);
            if (isset($obj_found[$hash])) {
                $obj_found[$hash]->ref++;
                continue;
            }
            $obj_found[$hash] = (object)array("idx" => $idx, "ref" => 1, "path" => $path);
            $idx++;
            $r = igk_sys_reflect_class($q);
            $tab = $r->getProperties(ReflectionProperty::IS_PRIVATE);
            $gr = $r;
            while ($parent = $gr->getParentClass()) {
                $pc = $parent->getName();
                $gr = igk_sys_reflect_class($pc);
                $cpt = $gr->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PUBLIC);
                $tab = array_merge($tab, $cpt);
            }
            $treated = array();
            foreach ($tab as $v) {
                $prop = $v;
                $ns = "\0" . $v->class . "\0" . $v->name;
                if ($prop->isStatic() || isset($treated[$ns]) || isset($treated[$v->name]))
                    continue;
                if ($prop->isPublic()) {
                    $treated[$v->name] = 1;
                }
                $treated[$ns] = 1;
                $prop->setAccessible(true);
                $gvb = $prop->getValue($q);
                if ($gvb === null)
                    continue;
                if (igk_is_closure($gvb)) {
                    $found = true;
                    $closures[] = $path . "/{$v->name}";
                    if ($first)
                        break 2;
                    continue;
                }
                if (is_array($gvb)) {
                    foreach ($gvb as $kk => $vv) {
                        array_unshift($tq, array("v" => $vv, "path" => $path . "/{$v->name}/{$kk}"));
                    }
                } else if (is_object($gvb)) {
                    array_unshift($tq, array("v" => $gvb, "path" => $path . "/{$v->name}"));
                }
            }
            foreach ($q as $c => $m) {
                if (($m === null) || isset($treated[$c]))
                    continue;
                if (igk_is_closure($m)) {
                    $found = 1;
                    $closures[] = $path . "/{$c}";
                    if ($first)
                        break 2;
                } else if (is_object($m)) {
                    array_push($tq, array("v" => $m, "path" => $path . "/{$c}"));
                } else if (is_array($m)) {
                    foreach ($m as $kk => $cc) {
                        array_push($tq, array("v" => $cc, "path" => $path . "/{$c}/{$kk}"));
                    }
                }
            }
        } else if (is_array($q)) {
            foreach ($q as $kk => $vv) {
                array_unshift($tq, array("v" => $vv, "path" => $path . "/{$kk}"));
            }
        }
    }
    return $found;
}
///<summary>die by setting code values</summary>
/**
 * die by setting code values
 */
function igk_die_e($c)
{
    $e = igk_getv(igk_get_env("sys://errors"), $c, $c);
    igk_die($e);
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_die_format($msg)
{
    return "<div>message : <i>" . $msg . "</i></div>";
}
///<summary>Represente igk_die_m function</summary>
///<param name="m"></param>
/**
 * Represente igk_die_m function
 * @param mixed $m 
 */
function igk_die_m($m)
{
    igk_die_notimplement($m);
}
///<summary>Represente igk_die_notimplement function</summary>
///<param name="methodName"></param>
/**
 * Represente igk_die_notimplement function
 * @param mixed $methodName 
 */
function igk_die_notimplement($methodName)
{
    igk_die(__("{0} Not implement", $methodName));
}
///<summary></summary>
///<param name="callable"></param>
///<param name="obj"></param>
///<param name="method"></param>
///<param name="args"></param>
/**
 * 
 * @param mixed $callable 
 * @param mixed $obj 
 * @param mixed $method 
 * @param mixed $args 
 */
function igk_dispatch_call($callable, $obj, $method, $args = array())
{
    if (method_exists($obj, $method)) {
        call_user_func_array($callable, array_merge(array($obj, $method), $args));
    }
}
///<summary>dispatch message to a specific controller</summary>
/**
 * dispatch message to a specific controller
 */
function igk_dispatch_message($source, $c, $params)
{
    $bck = array("sess_id" => session_id(), "sess" => $_SESSION);
    igk_sess_write_close();
    $actionctrl = igk_getctrl(IGK_SYSACTION_CTRL);
    $uri = igk_io_baseuri() . "/" . $actionctrl->getUri("dispatchMessage");
    igk_wln(igk_curl_post_uri($uri, array(
        "ctrl" => $c->getName(),
        "classname" => get_class($c),
        "source" => $source->getName(),
        "uri" => "/" . implode(
            "/",
            $params
        )
    ), null, igk_get_platform_header_array()));
    if ($bck) {
        igk_bind_session_id($bck["sess_id"]);
        session_start();
        $_SESSION = $bck["sess"];
    }
}
///<summary>call this func to render a display object. other way for toString class method</summary>
/**
 * call this func to render a display object. other way for toString class method
 */
function igk_display($obj, $keyTab = null)
{
    if ($keyTab) {
        $c = igk_get_env("sys://tabdisplay/" . $keyTab);
        if ($c) {
            if (is_callable($c)) {
                return $c($obj);
            } else {
                igk_wln("no display bind");
            }
        }
    }
    if (isset($obj->clName))
        return $obj->clName;
    if (isset($obj->clId))
        return $obj->clId;
}
///<summary>enable display error</summary>
/**
 * enable display error
 */
function igk_display_error($a)
{
    if ($a) {
        switch (igk_server()->ENVIRONMENT) {
            case "development":
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;
            default:
                error_reporting(E_ALL | E_STRICT | E_NOTICE);
                ini_set('error_reporting', E_ALL | E_STRICT);
                break;
        }
    } else {
        ini_set("display_errors", "0");
    }
}
///<summary> helper: handler response</summary>
///<param name="r"></param>
/**
 * helper: handler response
 * @param mixed|array|object $r response to handle
 */
function igk_do_response($r)
{
    \IGK\System\Http\Response::HandleResponse($r);
    return $r;
}
///<summary></summary>
///<param name="doc"></param>
/**
 * 
 * @param mixed $doc 
 */
function igk_doc_add_ie_meta_compatibility($doc)
{
    $meta = igk_create_node("meta");
    $meta["http-equiv"] = "X-UA-Compatible";
    $meta[HtmlMetaManager::ATTR_CONTENT] = "IE=edge";
    $doc->Metas->addMeta("X-UA-Compatible-Edge", $meta);
}
///<summary></summary>
///<param name="doc"></param>
///<param name="script"></param>
///<param name="tag"></param>
///<param name="global" default="true"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $script 
 * @param mixed $tag 
 * @param mixed $global 
 */
function igk_doc_add_lib_script($doc, $script, $tag, $global = true)
{
    if (file_exists($script))
        return $doc->addScript($script, $tag, $global);
    return null;
}
///<summary>use this to add tempory script to document</summary>
/**
 * use this to add tempory script to document
 */
function igk_doc_add_tempscript($doc, $script, $onlyonce = 1, $attr = null)
{
    if (igk_is_ajx_demand()) {
        $k = "sys://js/tempscripts/";
        $c = igk_get_env($k, array());
        if (isset($c[$script]))
            return $c[$script];
        $js = igk_create_notagnode();
        $o = null;
        if (file_exists($script)) {
            $o = $js->script()->setContent(igk_io_read_allfile($script));
            $o->deactivate("defer");
        } else {
            $o = $js->addScript($script);
        }
        if ($attr) {
            $o->setAttributes($attr);
        }
        $o["autoremove"] = "1";
        if (!defined("IGK_NO_WEB")) {
            $js->renderAJX();
        }
        $c[$script] = $js;
        igk_set_env($k, $c);
        return $o;
    } else if ($doc) {
        return $doc->addTempScript($script, $onlyonce);
    }
}
///<summary></summary>
///<param name="doc"></param>
/**
 * 
 * @param mixed $doc 
 */
function igk_doc_enable_mobile_app($doc)
{
    $meta = igk_create_node("meta");
    $meta["name"] = "mobile-web-app-capable";
    $meta[HtmlMetaManager::ATTR_CONTENT] = "yes";
    $doc->Metas->addMeta("mobile-web-app-capable", $meta);
}
///<summary></summary>
///<param name="doc"></param>
/**
 * 
 * @param mixed $doc 
 */
function igk_doc_is_global($doc)
{
    return igk_app()->getDoc() === $doc;
}
///<summary> load temporary script to tempScriptListener</summary>
///<param name="doc" > the document where to load</param>
///<param name="folder" type="mixed"> string|array the folder of target scripts</param>
/**
 *  load temporary script to tempScriptListener
 * @param mixed $doc  the document where to load
 * @param mixed $folder  string|array the folder of target scripts
 */
function igk_doc_load_temp_script($doc, $folder, $tag = null, $strict = 0)
{
    $options = "";
    $created = 0;
    $is_prod = igk_environment()->isOPS();
    if (is_array($folder)) {
        $btab = $folder;
        $folder = igk_getv($btab, "folder");
        $init = igk_getv($btab, "callback");
        $mergescript = igk_getv($btab, "mergescript");
        $cachedir = igk_io_cacheddist_jsdir();
        if ($tag)
            $cachedir .= "/{$tag}";
        else
            igk_die("tagname is required");
        if ($is_prod) {
            if (!file_exists($cachegen = $cachedir . "/.cache")) {
                igk_cache_gen_cache($folder, $cachedir, $mergescript);
                igk_io_w2file($cachegen, json_encode((object)array("date" => date("Ymd"), "tag" => $tag)));
                igk_hook(IGKEvents::HOOK_CACHE_RES_CREATED, array());
                $created = 1;
            }
        }
        if (is_callable($init)) {
            $init($doc, $folder, $created);
        }
        return;
    }
    $c = 0;
    if ($is_prod) {
        if (is_dir($folder)) {
            $uid = "";
            if ($strict && $tag) {
                $uid = $tag;
            } else
                $uid = (($tag) ? ($tag . "-") : '') . uniqid();
            $f = igk_uri(igk_io_cacheddist_jsdir() . "/{$uid}.js");
            if (file_exists($f)) {
                $doc->addTempScript($f, 1)->activate("defer");
            } else {
                $u = "!@res/Scripts/" . $uid;
                $doc->addTempScript($u . ($strict ? "?strict=1" . $options : ""), 1)->activate("defer");
                $doc->setParam("scripts/" . $uid, $folder);
            }
            $c = 1;
        }
        return $c;
    }
    igk_io_getfiles($folder, function ($f) use (&$c, $doc) {
        if (preg_match("/\.js$/i", $f)) {
            $doc->addTempScript($f)->activate("defer");
            $c++;
        }
    });
    return $c;
}
///<summary>set the favicon to this document</summary>
///<param name="doc">IGKHtmlDocument </param>
///<param name="f">relative or fullpath to the favicon file </param>
/**
 * set the favicon to this document
 * @param mixed $doc IGKHtmlDocument 
 * @param mixed $f relative or fullpath to the favicon file 
 */
function igk_doc_set_favicon($doc, $f)
{
    $doc->setFavicon($f);
}
///<summary>shortcut :  set meta do document</summary>
/**
 * shortcut : set meta do document
 */
function igk_doc_set_meta($doc, $name, $content)
{
    $sm = $doc->Metas;
    $meta = $sm->getMetaById($name);
    if ($meta == null) {
        $meta = igk_create_node("meta");
        $doc->Metas->addMeta($name, $meta);
    }
    if ($content == null) {
        $doc->Metas->rmMeta($name);
    } else {
        $meta["name"] = $name;
        $meta[HtmlMetaManager::ATTR_CONTENT] = $content;
    }
}
///<summary>@@@used to download content</summary>
/**
 * @@@used to download content
 */
function igk_download_content($name, $size, $content, $mimeType = null, $encoding = "binary", $exit = true)
{
    if ($mimeType) {
        header("Content-Mime-Type: " . $mimeType);
    }
    header("Content-Type: Application/force-download; name=\"" . $name . "\"");
    header("Content-Transfer-Encoding: " . $encoding);
    header("Content-Length: $size");
    header("Content-Disposition: attachment; filename=\"" . $name . "\"");
    header("Expires: 0");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    igk_set_env(IGK_ENV_NO_TRACE_KEY, 1);
    ob_get_level() && ob_clean();
    igk_wl($content);
    if ($exit)
        igk_exit();
}
///ask to download file
/**
 */
function igk_download_file($name, $filename, $mimeType = null, $encoding = "binary", $exit = 1)
{
    if (file_exists($filename)) {
        $size = @filesize($filename);
        igk_download_content($name, $size, IO::ReadAllText($filename), $mimeType, $encoding, $exit);
    }
}
///<summary>dump value</summary>
/**
 * dump value
 */
function igk_dump($v)
{
    $callers = debug_backtrace();
    $from = igk_getv($callers, 0);
    $d = igk_create_node('div');
    $d["class"] = "igk-dump";
    $l = $d->add('span');
    $l->add("i")->Content = $from["file"];
    $l->addText(':');
    $l->add("b")->Content = $from["line"];
    if (is_object($v)) {
        $st = "{";
        $h = 0;
        foreach ($v as $k => $s) {
            if ($h) {
                $st .= ', <br />';
            }
            $st .= "<font color='red'>\"{$k}\"</font>=&gt;";
            if (is_object($s)) {
                $st .= 'Object T';
            } else if (is_array($s)) {
                $st .= 'Array T';
            } else {
                $st .= $s;
            }
            $h = 1;
        }
        $st .= "}";
        $d->div()->Content = $st;
    } else if (is_array($v)) {
        $st = "[";
        $h = 0;
        foreach ($v as $k => $s) {
            if ($h) {
                $st .= '</span>, <br /> ';
            }
            $st .= "<span><font color='red'>\"{$k}\"</font>=&gt;";
            if (is_object($s)) {
                $st .= 'Object T';
            } else if (is_array($s)) {
                $st .= 'Array T';
            } else {
                $st .= $s;
            }
            $h = 1;
        }
        $st .= '</span>]';
        $d->div()->Content = $st;
    }
    return $d->render();
}
///<summary> dump array</summary>
/**
 *  dump array
 */
function igk_html_dump_array($tab)
{
    $n = igk_create_node('div');
    $n["class"] = "dumparray igk-row";
    foreach ($tab as $k => $v) {
        $r = $n->div();
        $r->addSpan()->setClass("k")->Content = $k;
        $r->addSpan()->setClass("v")->Content = is_string($v) ? $v : (is_object($v) ? get_class($v) : $v);
    }
    $n->renderAJX();
}
///<summary>write error log</summary>
/**
 * write error log
 */
function igk_elog($msg, $tag = null)
{
    $f = "";
    if (!($f = igk_const("IGK_LOG_ERROR_FILE")))
        $f = igk_dir(igk_io_sys_datadir() . "/Logs/.error." . date("Y-m-d") . ".log");
    igk_log($msg, $f, $tag);
}
///<summary></summary>
///<param name="k"></param>
/**
 * 
 * @param mixed $k 
 */
function igk_env_count($k)
{
    $sk = 'sys://counter/' . $k;
    $c = igk_get_env($sk, 0) + 1;
    igk_set_env($sk, $c);
    return $c;
}
///<summary>get the current value of the counter</summary>
/**
 * get the current value of the counter
 */
function igk_env_count_get($k)
{
    $sk = 'sys://counter/' . $k;
    return igk_get_env($sk, null);
}
///<summary></summary>
///<param name="k"></param>
/**
 * 
 * @param mixed $k 
 */
function igk_env_count_reset($k)
{
    $sk = 'sys://counter/' . $k;
    igk_set_env($sk, null);
}
///<summary>Represente igk_env_file function</summary>
///<param name="file"></param>
/**
 * Represente igk_env_file function
 * @param mixed $file 
 */
function igk_env_file($file)
{
    return igk_environment()->get_file($file);
}
///<summary> for chain ajx mecanism ask for target node replacement</summary>
/**
 *  for chain ajx mecanism ask for target node replacement
 */
function igk_env_get_replace_view()
{
    $g = igk_get_env("sys://nodes/replaceview");
    if ($g) {
        igk_set_env("sys://nodes/replaceview", null);
    }
    return $g;
}
///<summary> for chain ajx mecanism set  target node replacement</summary>
/**
 *  for chain ajx mecanism set target node replacement
 */
function igk_env_set_replace_view($target)
{
    igk_set_env("sys://nodes/replaceview", $target);
}
///<summary>function igk_error</summary>
///<param name="code"></param>
/**
 * function igk_error
 * @param mixed $code 
 */
function igk_error($code)
{
    return igk_getv(igk_get_env("sys://error_codes"), $code);
}
///<summary>function igk_error_def_error</summary>
///<param name="msg"></param>
///<param name="code"></param>
///<param name="msg_key"></param>
/**
 * function igk_error_def_error
 * @param mixed $msg 
 * @param mixed $code 
 * @param mixed $msg_key 
 */
function igk_error_def_error($msg, $code, $msg_key)
{
    $igk_error_codes = igk_get_env("sys://error_codes", function () {
        return array();
    });
    $igk_error_codes[$msg] = array(
        "Key" => $msg,
        "Code" => $code,
        "Msg" => ($msg_key == null) ? str_replace(
            "_",
            ".",
            $msg
        ) : $msg_key
    );
    define($msg, $msg);
    igk_set_env("sys://error_codes", $igk_error_codes);
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_error_page404($msg)
{
    $file = IGK_LIB_DIR . "/Views/error/404.phtml";
    if (file_exists($file)) {
        $title = "Error - " . IGKErrors::ConfigMisConfiguration;
        $headers = "";
        $trace_css = "";
        $buri = igk_io_baseuri();
        if (file_exists($d = igk_io_resourcesdir() . "/Fonts/google/Roboto100,200,400,700,900.css")) {
            $headers .= "<link rel=\"stylesheet\" href='" . igk_uri(igk_io_baseuri() . "/" . igk_io_baserelativepath($d)) . "'/>";
            $trace_css .= "body h1{ font-family: 'Roboto', arial, sans-serif; font-weight: 100; }";
        } else {
            igk_wln_e("not exit");
        }
        $uri = igk_io_html_link(IGK_LIB_DIR . "/Default/" . IGK_RES_FOLDER . "/Img/cfavicon.ico")->getValue();
        $headers .= '<link title="Favicon" rel="shortcut icon" type="image/x-icon" href="' . $uri . '" />';
        $headers .= "<style>" . igk_io_read_allfile(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/error404.css") . "</style>";
        $headers .= "<style>" . igk_io_read_allfile(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/trace.css") . "</style>";
        $txtoptions = "$(-><-)";
        igk_set_header(404);
        include($file);
        igk_exit();
    }
}
///<summary>igk eval source script</summary>
///<param name="src">source to evaluate</param>
///<param name="ctrl">controller to pass to source</param>
///<param name="raw">data to pass</param>
///<remark>if raw to pass is an array data will override to pass
/**
 * igk eval source script
 * @param mixed $src source to evaluate
 * @param mixed $ctrl controller to pass to source
 * @param mixed $raw data to pass
 */
function igk_eval_in_context($src, $ctrl, $raw)
{
    if ($ctrl) {
        extract(igk_extract_context($ctrl));
    }
    ($raw) && is_array($raw) && extract($raw, EXTR_OVERWRITE, "__scope");
    extract(igk_get_context_args());
    igk_environment()->set("eval_script", $src);
    $__request_data = false;
    if ((empty($raw)) && (strpos($src, '$raw') !== false) &&
        !($raw instanceof IProxyDataArgs)
    ) {
        // request for add a proxy raw data  
        $raw = new DataArgs($raw);
        $__request_data = true;
    } else if (is_array($raw)) {
        extract($raw);
    }

    $__result = @eval($src);
    if ($g = error_get_last()) {
        igk_dev_wln_e("error: for : ", $g);
        error_clear_last();
        if ($__request_data) {
            $__result = null;
        }
    }
    return $__result;
}
///<summary></summary>
///<param name="$c"></param>
/**
 * 
 * @param mixed $$c 
 */
function igk_eval_last_script($c)
{
    igk_set_env("sys://eval/lastscript", $c);
}
///<summary>evaluation script in context</summary>
///<remark>script must be free of " symbol</remark>
/**
 * evaluation script in context
 */
function igk_eval_script_in_context($context, $script)
{
    if ($context)
        extract($context);
    unset($context);
    $gp = get_defined_vars();
    $script = <<<EOF
return "{$script}";
EOF;

    try {
        igk_eval_last_script($script);
        $r = igk_eval_in_context($script, isset($ctrl) ? $ctrl : null, $gp);
        igk_eval_last_script(null);
        igk_sys_handle_error($script);
        return $r;
    } catch (\Throwable $ex) {
        igk_wln_e("ERROR:Script eval error : " . $ex->getMessage(), "{$script}");
    }
    return null;
}
///<summary>write a message in a stderror</summary>
/**
 * write a message in a stderror
 */
function igk_ewln($msg)
{
    if (defined("STDERR")) {
        if (is_array($msg)) {
            ob_start();
            igk_wln($msg);
            $s = ob_get_contents();
            ob_end_clean();
            $msg = $s;
        }
        fwrite(STDERR, $msg . IGK_LF);
    }
}
///<summary>calculate execution time</summary>
/**
 * calculate execution time
 */
function igk_execute_time($name = null, $time = null)
{
    $t = igk_get_env("sys://env/starttime" . ($name ? "/{$name}" : ""), 0);
    return (float)($time ?? microtime(true)) - (float)$t;
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_extract_context($ctrl)
{
    $script_obj = igk_html_databinding_getobjforscripting($ctrl);
    if ($script_obj && $script_obj->args) {
        return $script_obj->args;
    }
    return [];
}
///<summary></summary>
///<param name="dirorfile"></param>
///<param name="mode"></param>
///<param name="recursif" default="false"></param>
/**
 * 
 * @param mixed $dirorfile 
 * @param mixed $mode 
 * @param mixed $recursif 
 */
function igk_file_chmod($dirorfile, $mode, $recursif = false)
{
    $out = true;
    if ($recursif && is_dir($dirorfile)) {
        $hdir = @opendir($dirorfile);
        if ($hdir) {
            while ($r = readdir($hdir)) {
                if (($r == ".") || ($r == ".."))
                    continue;
                $f = igk_dir($dirorfile . "/" . $r);
                if (is_dir($f)) {
                    $out = igk_file_chmod($f, $mode, $recursif) && $out;
                } else if (file_exists($f)) {
                    if (!@chmod($f, $mode))
                        $out = false;
                }
            }
            closedir($hdir);
        }
    }
    $out = @chmod($dirorfile, $mode) && $out;
    return $out;
}
///<summary></summary>
///<param name="file"></param>
///<param name="parentFile"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $parentFile 
 */
function igk_file_isdirectchildof($file, $parentFile)
{
    $tab = get_included_files();
    $c = igk_count($tab) - 1;
    $r = 0;
    while ($c > 0) {
        if (!$r) {
            $r = ($tab[$c] == $file);
        } else {
            $tab[$c] == $parentFile;
            return 1;
        }
        $c--;
    }
    return 0;
}
///<summary>check if a file is included</summary>
/**
 * check if a file is included
 */
function igk_file_isnotincluded($file)
{
    $tab = get_included_files();
    if (igk_phar_available()) {
        $index = 1;
    } else {
        $index = 0;
    }
    return ($index >= 0) && ($index < count($tab)) && $tab[$index] == $file;
}
///<summary>flush data.</summary>
///<note>flush data cause the header to be send.</note>
/**
 * flush data.
 */
function igk_flush_data()
{
    $l = ob_get_level();
    if ($l > 0) {
        @ob_flush();
    }
    flush();
}
///<summary>begin flushing data </summary>
/**
 * begin flushing data 
 */
function igk_flush_start()
{
    while (ob_get_level()) {
        ob_end_flush();
    }
    ob_start();
}
///<summary>write data</summary>
/**
 * write data
 */
function igk_flush_write($data, $eventtype = null)
{
    if ($eventtype) {
        igk_wl('event:' . $eventtype . "\n");
    }
    igk_wl("data: " . $data . "\n\n");
}
///<summary>utility that combine flush_write and flush_data</summary>
/**
 * utility that combine flush_write and flush_data
 */
function igk_flush_write_data($data)
{
    igk_flush_write($data);
    igk_flush_data();
}
///<summary>utility to do foreach on table or object with a callback</summary>
/**
 * utility to do foreach on table or object with a callback
 */
function igk_foreach($i, $callback)
{
    if (!$i) {
        return null;
    }
    if (is_string($i)) {
        return $callback(0, $i);
    }
    foreach ($i as $k => $v) {
        $callback($k, $v);
    }
    return null;
}
///<summary>Represente igk_form_input_type function</summary>
///<param name="type"></param>
/**
 * Represente igk_form_input_type function
 * @param mixed $type 
 */
function igk_form_input_type($type)
{
    return HtmlUtils::GetInputType($type);
}
///<summary>convert format data</summary>
///<param name="v">value to convert</param>
///<param name="in">value format</param>
///<param name="out">output format</param>
/**
 * convert format data
 * @param mixed $v value to convert
 * @param mixed $in value format
 * @param mixed $out output format
 */
function igk_format_date($v, $in, $out)
{
    $span = igk_time_span($in, $v);
    return date($out, $span);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="id"></param>
///<param name="uri" default="null"></param>
///<param name="closeuri" default="."></param>
///<param name="title" default="null"></param>
///<param name="target" default="null"></param>
///<param name="buttonmodel"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $id 
 * @param mixed $uri 
 * @param mixed $closeuri 
 * @param mixed $title 
 * @param mixed $target 
 * @param mixed $buttonmodel 
 */
function igk_frame_add_confirm($ctrl, $id, $uri = null, $closeuri = ".", $title = null, $target = null, $buttonmodel = 0)
{
    $_id = base64_encode($id);
    $frame = igk_html_frame($ctrl, $_id, $closeuri, $target) ?? die("frame not created");

    $frame->Title = ($title == null) ? __(IGK_CONFIRM_TITLE) : $title;
    $frame->closeMethodUri = $uri;
    $frame->callbackMethod = "igk_frame_close_frame_callback";
    $d = $frame->BoxContent;
    $d->clearChilds();
    $igk = igk_app();
    $frame->Form = $d->addForm();
    $frame->Form["action"] = $frame->closeUri;
    $frame->Form["igk-confirmframe-response-target"] = $ctrl->TargetNode["id"];
    $frame->Form->Div = $frame->Form->div();
    $frame->Form->addHSep();
    $frame->Form->addInput("confirm", "hidden", 1);
    $frame->Form->addInput("frame-id", "hidden", $_id);
    $frame->Form->addInput("frame-close-uri", "hidden", igk_getctrl(IGK_FRAME_CTRL)->getUri("closeFrame_ajx&navigate=false&id=" . $_id));
    $frame->Form->script()->Content = "window.igk.winui.framebox.init_confirm_frame(ns_igk.getLastScript(), '" . $frame->closeUri . "&cancel=1" . "', " . ($igk->Session->URI_AJX_CONTEXT ? 'true' : 'false') . ")";
    $canceluri = $frame->closeUri . "&cancel=1";
    $acbar = $frame->Form->actionbar();
    switch ($buttonmodel) {
        case 0:
            $btn = $acbar->addBtn("btn_yes", __("btn.yes"), "submit");
            $btn["onclick"] = $igk->Session->URI_AJX_CONTEXT ? "javascript: return ns_igk.winui.framebox.btn.yes(this);" : null;
            HtmlUtils::AddBtnLnk($acbar, __("btn.cancel"), "javascript: " . igk_js_post_frame_cmd($canceluri) . " this['igk:framebox'].close(); return false;");
            break;
        case 1:
            $acbar->addBtn("btn_ok", __("btn.ok"), "submit", array("onclick" => $igk->Session->URI_AJX_CONTEXT ? "javascript:return ns_igk.winui.framebox.btn.yes(this);" : null));
            HtmlUtils::AddBtnLnk($acbar, __("btn.cancel"), "javascript: " . igk_js_post_frame_cmd($canceluri) . " this['igk:framebox'].close(); return false;");
            break;
    }
    if ($ctrl->getCurrentPageFolder() == IGK_CONFIG_MODE) {
        $frame["class"] = "+igk-cnf-framebox";
    }
    return $frame;
}
///<summary></summary>
///<param name="target"></param>
///<param name="buttonmodel"></param>
///<param name="canceluri" default=""></param>
/**
 * 
 * @param mixed $target 
 * @param mixed $buttonmodel 
 * @param mixed $canceluri 
 */
function igk_frame_bind_action($target, $buttonmodel = 0, $canceluri = "")
{
    $igk = igk_app();
    switch ($buttonmodel) {
        case 0:
            $btn = $target->addBtn("btn_yes", __("btn.yes"), "submit");
            $btn["onclick"] = $igk->Session->URI_AJX_CONTEXT ? "javascript: return ns_igk.winui.framebox.btn.yes(this);" : null;
            HtmlUtils::AddBtnLnk($target, __("btn.cancel"), "javascript: " . igk_js_post_frame_cmd($canceluri) . " this['igk:framebox'].close(); return false;");
            break;
        case 1:
            $target->addBtn("btn_ok", __("btn.ok"), "submit", array("onclick" => $igk->Session->URI_AJX_CONTEXT ? "javascript:return ns_igk.winui.framebox.btn.yes(this);" : null));
            HtmlUtils::AddBtnLnk($target, __("btn.cancel"), "javascript: " . igk_js_post_frame_cmd($canceluri) . " this['igk:framebox'].close(); return false;");
            break;
    }
}
///<summary></summary>
///<param name="name"></param>
///<param name="navigate" default="null"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $navigate 
 */
function igk_frame_close($name, $navigate = null)
{
    return igk_getctrl(IGK_FRAME_CTRL)->closeFrame($name, $navigate);
}
///<summary></summary>
///<param name="frame"></param>
/**
 * 
 * @param mixed $frame 
 */
function igk_frame_close_frame_callback($frame)
{
    $p = null;
    if (igk_qr_confirm()) {
        igk_set_env("sys://notsystemurihandle", 1);
        $p = $frame->Owner->App->getControllerManager()->InvokeUri($frame->closeMethodUri);
        igk_set_env("sys://notsystemurihandle", null);
    }
    return $p;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="id"></param>
///<param name="title" default="null"></param>
///<param name="closeuri" default="."></param>
///<param name="target" default="null"></param>
///<param name="reloadcallback" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $id 
 * @param mixed $title 
 * @param mixed $closeuri 
 * @param mixed $target 
 * @param mixed $reloadcallback 
 */
function igk_frame_confirm($ctrl, $id, $title = null, $closeuri = ".", $target = null, $reloadcallback = null, $buttonmodel = 0)
{
    $frame = igk_getctrl(IGK_FRAME_CTRL)->createFrame($id, $ctrl, $closeuri, $reloadcallback);
    if ($target === null)
        $target = igk_app()->getDoc()->body;
    $target->add($frame);
    $frame->Title = ($title == null) ? __(IGK_CONFIRM_TITLE) : $title;
    $d = $frame->BoxContent;
    $d->clearChilds();
    $igk = $ctrl->App;
    $frame->Form = $d->addForm();
    $frame->Form["action"] = $frame->closeUri;
    $frame->Form["igk-confirmframe-response-target"] = $ctrl->TargetNode["id"];
    $frame->Form->Div = $frame->Form->div();
    $frame->Form->addHSep();
    $frame->Form->addInput("confirm", "hidden", 1);
    $frame->Form->addInput("frame-id", "hidden", $id);
    $frame->Form->addInput("frame-close-uri", "hidden", igk_getctrl(IGK_FRAME_CTRL)->getUri("closeFrame_ajx&navigate=false&id=" . $id));
    $frame->Form->script()->Content = "igk.winui.framebox.init_confirm_frame(igk.getLastScript(), '" . $frame->closeUri . "&cancel=1" . "', " . ($igk->Session->URI_AJX_CONTEXT ? 'true' : 'false') . ")";
    $canceluri = $frame->closeUri . "&cancel=1";
    switch ($buttonmodel) {
        case 0:
            $btn = $frame->Form->addBtn("btn_yes", __("btn.yes"), "submit");
            $btn["onclick"] = $igk->Session->URI_AJX_CONTEXT ? "javascript: return ns_igk.winui.framebbox.btn.yes(this);" : null;
            HtmlUtils::addBtnLnk($frame->Form, __("btn.no"), "javascript: " . igk_js_post_frame_cmd($canceluri) . " this.frame.close(); ");
            break;
        case 1:
            $frame->Form->addBtn("btn_ok", __("btn.ok"), "submit", array("onclick" => $igk->Session->URI_AJX_CONTEXT ? "javascript:return ns_igk.winui.framebox.btn.yes(this);" : null));
            HtmlUtils::addBtnLnk($frame->Form, __("btn.cancel"), "javascript: " . igk_js_post_frame_cmd($canceluri) . " this.frame.close()");
            break;
    }
    $frame->Script->Content = new IGKFrameScript($frame, "c");
    return $frame;
}
///<summary></summary>
///<param name="frame"></param>
/**
 * 
 * @param mixed $frame 
 */
function igk_frame_is_available($frame)
{
    return igk_getctrl(IGK_FRAME_CTRL)->IsFrameAvailable($frame);
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_frame_js_postform_ref($ctrl)
{
    return "javascript: return (function(q,s){ igk.winui.frameBox.postForm(q.form, q.form.action, s); return false;})(this, '" . $ctrl->TargetNode["id"] . "');";
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="id"></param>
///<param name="closeuri" default="."></param>
///<param name="target" default="null"></param>
///<param name="reloadcallback" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $id 
 * @param mixed $closeuri 
 * @param mixed $target 
 * @param mixed $reloadcallback 
 */
function igk_frame_new($ctrl, $id, $closeuri = ".", $target = null, $reloadcallback = null)
{
    $frm = igk_getctrl(IGK_FRAME_CTRL)->createFrame($id, $ctrl, $closeuri, $reloadcallback);
    if ($target === null)
        $target = igk_app()->getDoc()->body;
    $target->add($frm);
    $frm->Script->Content = new IGKFrameScript($frm, "confirm");
    return $frm;
}
///<summary></summary>
///<param name="classname"></param>
/**
 * 
 * @param mixed $classname 
 */
function igk_free_component($classname)
{
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
    $t = $ctrl->getParam("sys://class_component");
    if ($t) {
        unset($t[$classname]);
    }
}
///<summary></summary>
///<param name="k"></param>
/**
 * 
 * @param mixed $k 
 */
function igk_free_document($k)
{
    $v = igk_app()->session->getParam(IGK_KEY_DOCUMENTS);
    if (is_object($k)) {
        $k->Dispose();
        $k = $k->getParam(IGK_DOC_ID_PARAM);
    }
    if ($v) {
        $doc = igk_getv($v, $k);
        if ($doc) {
            unset($v[$k]);
            igk_app()->session->setParam(IGK_KEY_DOCUMENTS, $v);
        }
    }
}
///<summary></summary>
///<param name="name"></param>
///<param name="args"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $args 
 */
function igk_get_action_uri($name, $args)
{
    return igk_io_baseuri() . "/@!actions/{$name}/" . $args;
}
///<summary></summary>
///<param name="ad"></param>
/**
 * 
 * @param mixed $ad 
 */
function igk_get_adapter_name($ad)
{
    if (!is_object($ad))
        return;
    $c = get_class($ad);
    if (preg_match_all("/IGK(?P<name>(.)+)DataAdapter/i", $c, $tab)) {
        return igk_getv($tab["name"], 0);
    }
    return null;
}
///<summary>Retrieve all default page controller</summary>
/**
 * Retrieve all default page controller
 */
function igk_get_all_default_pagectrl()
{
    $igk = igk_app();
    if ($igk == null)
        return;
    $t = array();
    foreach ($igk->getControllerManager()->getControllers() as $k) {
        $cl = get_class($k);
        $v_rc = igk_sys_reflect_class($cl);
        if ($v_rc->isAbstract() || !igk_reflection_class_extends($cl, IGK_CTRLWEBPAGEBASECLASS))
            continue;
        $t[] = $k;
    }
    return $t;
}
///<summary>Represente igk_get_all_session_file_infos function</summary>
///<param name="max" default="null"></param>
/**
 * Represente igk_get_all_session_file_infos function
 * @param mixed $max 
 */
function igk_get_all_session_file_infos($max = null)
{
    $d = ini_get("session.save_path");
    $tab = igk_io_getfiles($d);
    sort($tab);
    $sess = [];
    $prefix = igk_get_session_prefix();
    foreach ($tab as $k) {
        if (preg_match("/^" . $prefix . "/i", basename($k))) {
            $id = substr(basename($k), strlen($prefix));
            $sess[$id] = (object)["file" => $k, "size" => IO::GetFileSize(filesize($k)), "createtime" => date(
                \IGKConstants::MYSQL_DATETIME_FORMAT,
                filemtime($k)
            )];
        }
        if ($max && (igk_count($sess[$id]) >= $max)) {
            break;
        }
    }
    return $sess;
}
///<summary>Represente igk_get_all_session_files function</summary>
/**
 * Represente igk_get_all_session_files function
 */
function igk_get_all_session_files()
{
    $d = ini_get("session.save_path");
    $tab = igk_io_getfiles($d);
    sort($tab);
    $sess = [];
    $prefix = igk_get_session_prefix();
    foreach ($tab as $k) {
        if (preg_match("/^" . $prefix . "/i", basename($k))) {
            $id = substr(basename($k), strlen($prefix));
            $sess[$id] = $k;
        }
    }
    return $sess;
}
///<summary>retrieve an array of session info</summary>
/**
 * retrieve an array of session info
 */
function igk_get_all_sessions()
{
    $d = ini_get("session.save_path");
    $dt = null;
    $prefix = igk_get_session_prefix();
    $ssid = session_id();
    $v_capp = igk_app();
    igk_sess_write_close();
    $sess_key = IGK_APP_SESSION_KEY;
    $_SESSION[$sess_key] = null;
    if (is_dir($d)) {
        $dt = array();
        $tab = igk_io_getfiles($d);
        sort($tab);
        foreach ($tab as $k) {
            if (preg_match("/^" . $prefix . "/i", basename($k))) {
                $id = substr(basename($k), strlen($prefix));
                $obj = (object)["location" => $k, "sessid" => $id, "start" => "00:00:00", "ip" => "127.0.0.1"];
                if ($ssid == $id) {
                    $app = $v_capp;
                } else {
                    unset($_SESSION[$sess_key]);
                    igk_bind_session_id($id);
                    session_start();
                    $app = $_SESSION[$sess_key];
                    igk_sess_write_close();
                    unset($_SESSION[$sess_key]);
                }
                if ($app) {
                    $obj->start = $app->getcreateAt();
                    $serverInfo = $app->getServerInfo();
                    if ($serverInfo) {
                        $obj->ip = igk_getv($serverInfo, "ip");
                        $obj->serverInfo = $serverInfo;
                    }
                }
                $dt[] = $obj;
            }
        }
    }
    unset($_SESSION[$sess_key]);
    igk_bind_session_id($ssid);
    session_start();
    $_SESSION[$sess_key] = $v_capp;
    return $dt;
}
///<summary>Retrieve all user uri page controller</summary>
/**
 * Retrieve all user uri page controller
 * @return array
 */
function igk_get_all_uri_page_ctrl()
{
    $t = array(
        "@base" => igk_app()->getControllerManager()->getUserControllers(function ($v) {
            return $v instanceof IIGKUriActionRegistrableController;
        }),
        "@templates" => function_exists('igk_template_get_ctrls') ? call_user_func_array("igk_template_get_ctrls", []) : []
    );
    $t["total"] = count($t["@base"])  + count($t["@templates"]);
    return $t;
}


///<summary></summary>
///<param name="v"></param>
///<param name="currency" default="'EUR'"></param>
/**
 * 
 * @param mixed $v 
 * @param mixed $currency 
 */
function igk_get_amount($v = 0, $currency = 'EUR')
{
    return __("lb.amount_2", $v, igk_get_currency_symbol($currency));
}
///<summary> shortcut : get conntroller's article </summary>
/**
 *  shortcut : get conntroller's article 
 */
function igk_get_article($ctrl, $name)
{
    return $ctrl->getArticle($name);
}

///<summary></summary>
///<param name="lang" default="null"></param>
/**
 * 
 * @param mixed $lang 
 */
function igk_get_article_ext($lang = null)
{
    if ($lang) {
        return strtolower("." . $lang . "." . IGK_DEFAULT_VIEW_EXT);
    }
    return strtolower("." . R::GetCurrentLang() . "." . IGK_DEFAULT_VIEW_EXT);
}

///<summary></summary>
/**
 * 
 */
function igk_get_basestyle()
{
    $bf = IGKCaches::ResolvPath(__FILE__);
    $rdir = IGKCaches::ResolvPath(igk_realpath(IGK_BASE_DIR));
    $date = date('Ymd H:i:s');
    $v = <<<EOF
<?php
// desc: generate balafon.css file
// date: {$date}
// @author: C.A.D. BONDJE DOUE

defined("IGK_FORCSS") || define("IGK_FORCSS", 1);
define("IGK_NO_WEB", 1);
if (!defined('IGK_FRAMEWORK')){
	require_once('{$bf}');
}
igk_css_balafon_index('{$rdir}');
EOF;

    return $v;
}
///<summary>get a builder engine</summary>
/**
 * get a builder engine
 */
function igk_get_builder_engine($name, $frm, $selected = 0)
{
    $tab = igk_get_env("sys://form/builderengines");
    if ((($name) || ($name = igk_get_selected_builder_engine())) && isset($tab[$name])) {
        $c = $tab[$name];
        if (is_object($c)) {
            $c->setView($frm);
        } else {
            $c = new $c($frm);
            $tab[$name] = $c;
        }
        if ($selected)
            igk_set_selected_builder_engine($name);
        return $c;
    } else {
        return new FormBuilderEngine($frm);
    }
}
///<summary></summary>
///<param name="n"></param>
///<param name="def" default="null"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $def 
 */
function igk_get_cached($n, $def = null)
{
    $igk = igk_app();
    $c = $igk->Session->getParam("sys://cache");
    if ($c == null) {
        $c = array();
        $igk->Session->setParam("sys://cache", $c);
    }
    if (isset($c[$n]) && !igk_cache_expired($c[$n]))
        return $c[$n]->data;
    return $def;
}
///<summary></summary>
/**
 * 
 */
function igk_get_cached_manifest()
{
    header("Content-Type: text/cache-manifest");
    igk_wl(igk_get_manifest_content());
}
///<summary>utility function get call_user_func_array argument</summary>
/**
 * utility function get call_user_func_array argument
 */
function igk_get_call_args($t)
{
    if (!is_array($t))
        $t = array($t);
    return $t;
}

/**
 * return class namespace
 * @return string namespace or empty class
 */
function igk_get_class_namespace(string $class)
{
    return str_replace("/", "\\", dirname(IGK\Helper\StringUtility::Dir($class)));
}
///<summary>return constants within the class</summary>
/**
 * return constants within the class
 */
function igk_get_class_constants($classname)
{
    $r = igk_sys_reflect_class($classname);
    return $r->getConstants();
}
///<summary>return class instance for atomic design pattern.  store it in session</summary>
/**
 * return class instance for atomic design pattern. store it in session
 */
function igk_get_class_instance($classname, $callback)
{
    return igk_environment()->createClassInstance($classname, $callback);
    // $app = igk_app();
    // if (!$app){
    //     igk_trace();
    //     igk_wln("can't create a class e instance");
    //     igk_exit();
    // }
    // $k = igk_get_instance_key($classname);
    // $s = igk_app()->session;
    // if (!$s) {
    //     return null;
    // }
    // $keyid = IGKSession::IGK_INSTANCES_SESS_PARAM;
    // $tab = $s->getParam($keyid, array());
    // $o = igk_getv($tab, $k);
    // if (!$o) {
    //     if (method_exists($callback, "bindTo"))
    //         $callback->bindTo(null, $classname);
    //     $o = $callback();
    //     $tab[$k] = $o;
    //     $s->setParam($keyid, $tab);
    // }
    // return $o;
}
///<summary></summary>
///<param name="classname" default="null"></param>
/**
 * 
 * @param mixed $classname 
 */
function igk_get_class_location($classname = null)
{
    $c = igk_get_reg_class_file($classname);
    if ($c) {
        $t = igk_sys_reflect_class($classname);
        return (object)array("file" => $c, "line" => $t->getStartLine(), "included" => 1);
    }
    $c = igk_sys_reflect_class($classname);
    return (object)array("file" => $c->getFileName(), "line" => $c->getStartLine());
}
///<summary></summary>
///<param name="ob"></param>
///<param name="name"></param>
/**
 * 
 * @param mixed $ob 
 * @param mixed $name 
 */
function igk_get_class_method_location($ob, $name)
{
    $cl = get_class($ob);
    if (empty($cl))
        return null;
    $c = new ReflectionMethod($cl, $name);
    return (object)array("file" => $c->getFileName(), "line" => $c->getStartLine());
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_get_cmd_command($name)
{
    $t = igk_get_env("sys://cmd/commands");
    return ($t && $name) ? igk_getv($t, $name) : null;
}
///<summary></summary>
///<param name="id"></param>
/**
 * 
 * @param mixed $id 
 */
function igk_get_component($id)
{
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL);
    return $ctrl->getParam("sys://globalcomponent/{$id}");
}
///<summary>get component by id</summary>
/**
 * get component by id
 */
function igk_get_component_by_id($id)
{
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
    if ($ctrl) {
        return $ctrl->getComponentById($id);
    }
    return null;
}
///<summary>register and get the component id</summary>
/**
 * register and get the component id
 */
function igk_get_component_id($n)
{
    $id = $n->getParam(IGK_COMPONENT_ID_KEY)->getValue();
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
    if ($id && $ctrl->Exists($n)) {
        return $id;
    } else {
        if ($ctrl->Register($n, false)) {
            $n->setParam(IGK_COMPONENT_ID_KEY, new HtmlComponentIdValue($n));
            return $n->getParam(IGK_COMPONENT_ID_KEY)->getValue();
        }
    }
    return null;
}
///<summary></summary>
///<param name="cmpname"></param>
/**
 * 
 * @param mixed $cmpname 
 */
function igk_get_component_info($cmpname)
{
    return igk_get_env(IGK_ENV_HTML_COMPONENTS);
}
///<summary>register a node as a component uri</summary>
///<param name="node">the item that will host function callback</param>
///<param name="func">the fonction name registrated to a node</param>
///<return>the component registrated uri</return>
///<exemple> igk_get_component_uri($div, 'getIsVisible')</exemple>
/**
 * register a node as a component uri
 * @param mixed $node the item that will host function callback
 * @param mixed $func the fonction name registrated to a node
 */
function igk_get_component_uri($node, $func)
{
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
    $u = null;
    if ($ctrl->Exists($node)) {
        $u = $ctrl->getUri($func, $node);
    } else {
        if ($ctrl->Register($node, false)) {
            $node->setParam(IGK_COMPONENT_ID_KEY, new HtmlComponentIdValue($node));
            $u = $ctrl->getUri($func, $node);
        }
    }
    return $u;
}
///<summary>Represente igk_get_component_uri_key function</summary>
///<param name="guid"></param>
/**
 * Represente igk_get_component_uri_key function
 * @param mixed $guid 
 */
function igk_get_component_uri_key($guid)
{
    $s = !igk_io_basedir_is_root() ? "index.php/" : "";
    return igk_io_query_info()->root_uri . $s . "{" . $guid . "}";
}
///<summary></summary>
///<param name="name"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $callback 
 */
function igk_get_config_action($name, $callback)
{
    $t = igk_get_env("sys://configs/options", array());
    $t[$name] = $callback;
    igk_get_env("sys://configs/options", array());
}
///<summary></summary>
/**
 * 
 */
function igk_get_configs_menu_settings()
{
    return igk_get_env("sys://configs/menu");
}
///<summary>get controller view content</summary>
/**
 * get controller view content
 */
function igk_get_contents($ctrl, $type, $params = null)
{
    return $ctrl->viewContent($type, $params);
}
///<summary>Represente igk_get_context_args function</summary>
///<param name="arg" default="null"></param>
///<param name="reset" default="0"></param>
/**
 * Represente igk_get_context_args function
 * @param mixed $arg 
 * @param mixed $reset 
 */
function igk_get_context_args($arg = null, $reset = 0)
{
    if (!($r = igk_get_env($k = "context/args"))) {
        if ($arg !== null) {
            return null;
        }
        return [];
    }
    if ($arg !== null) {
        $c = igk_getv($r, $arg);
        if ($reset) {
            unset($r[$arg]);
            igk_set_env($k, $r);
        }
        return $c;
    }
    return $r;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_get_cookie($n)
{
    $n = igk_get_cookie_name(igk_sys_domain_name() . "/" . $n);
    if (!isset($_COOKIE)) {
        if (!igk_sys_env_production()) {
            igk_die("cookie variable not found. " . $n);
        }
        return null;
    }
    return igk_getv($_COOKIE, $n);
}
///<summary>retrieve the cookie domain</summary>
/**
 * retrieve the cookie domain
 * @return ?string 
 */
function igk_get_cookie_domain()
{
    $srv = igk_server();
    $p = igk_server()->HTTP_HOST;
    // + -------------------------------------------------------------------
    // + BUGFIX: Safari destroy IP address base session     
    // if (IGKValidator::IsIPAddress($p)){
    //     return null;
    // }
    if ($p && (IGKValidator::IsIPAddress($p) &&
        ((explode(":", $p)[0] == $srv->SERVER_NAME) ||
            ($srv->SERVER_NAME == $srv->SERVER_ADDR)))) {
        return null;
    }
    if ($p) {
        $tab = parse_url($p);
        if (isset($tab['path']))
            $p = $tab['path'];
        else if (isset($tab['host'])) {
            $p = $tab['host'];
        }
        return (strpos($p, ".") !== false ? "." : "") . $p;
    }
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_get_cookie_name($n)
{
    return str_replace('.', '_', $n);
}
///<summary></summary>
///<param name="b" default="null"></param>
/**
 * 
 * @param mixed $b 
 */
function igk_get_cookie_path($b = null)
{
    return $b ? "/" . $b : "/";
}
///<summary>get controller parameter shorcut</summary>
/**
 * get controller parameter shorcut
 */
function igk_get_cp($ctrl, $name, $reset = 1)
{
    ($n = $ctrl->getParam($name)) && $reset && $ctrl->setParam($name, null);
    return $n;
}
///<summary></summary>
///<param name="curr" default="'EUR'"></param>
/**
 * 
 * @param mixed $curr 
 */
function igk_get_currency_symbol($curr = 'EUR')
{
    $t = array("EUR" => "€", "USD" => "$");
    return igk_getv($t, $curr);
}
///<summary>get the current base controller</summary>
/**
 * get the current base controller
 * @return null|BaseController return the current base controller shortcut
 */
function igk_get_current_base_ctrl()
{
    return \IGK\Helper\SysUtils::CurrentBaseController();
}
///<summary></summary>
///<param name="dir" default="null"></param>
///<param name="secured" default="false"></param>
/**
 * 
 * @param mixed $dir 
 * @param mixed $secured 
 */
function igk_get_current_base_uri($dir = null, $secured = false)
{
    $igk = igk_app();
    if ($igk->getCurrentPageFolder() != IGK_HOME_PAGEFOLDER)
        $out = igk_io_baseuri($igk->getCurrentPageFolder(), $secured);
    else
        $out = igk_io_baseuri(null, $secured);
    if ($dir)
        $out .= "/" . $dir;
    return $out;
}
///<summary></summary>
/**
 * 
 */
function igk_get_current_package()
{
    $key = "sys://components/packages";
    $n = igk_get_env("sys://components/currentpackage");
    if ($n)
        return ($t = igk_getv(igk_get_env($key), $n)) ? $t["callback"] : null;
    return null;
}
///<summary>get the current page controller.</summary>
/**
 * get the current page controller.
 */
function igk_get_currentpagectrl()
{
    $igk = igk_app();
    $tab = igk_get_all_uri_page_ctrl();
    $page = $igk->getCurrentPage();
    if ($tab && (count($tab) > 0)) {
        foreach ($tab as $v) {
            if (igk_getv($v->Configs, "clDefaultPage") == $page) {
                return $v;
            }
        }
    }
    return null;
}
///<summary>get adapter system data adapter</summary>
/**
 * get adapter system data adapter
 * @return ?\IGK\Database\DataAdapterBase
 */
function igk_get_data_adapter($controllerOrAdpaterName, $throwException = false)
{
    return \IGK\Database\DataAdapterBase::GetAdapter($controllerOrAdpaterName, $throwException);
}
///<summary>Return the core default style file</summary>
/**
 * Return the core default style file
 */
function igk_get_default_style()
{
    return igk_io_read_allfile(IGK_LIB_DIR . "/Default/" . IGK_STYLE_FOLDER . "/default.pcss");
}
///<summary>get default view content</summary>
/**
 * get default view content
 */
function igk_get_default_view_content($ctrl)
{
    return "<?php \n";
}
///<summary>get default configuration data</summary>
/**
 * get default configuration data
 */
function igk_get_defaultconfigdata()
{
    $servername = igk_getv($_SERVER, "SERVER_NAME", "igkdev.com");
    $data = [
        "admin_login" => "admin", "admin_pwd" => "21232f297a57a5a743894a0e4a801fc3", "allow_article_config" => 0, "allow_auto_cache_page" => 0, "allow_debugging" => 0, "allow_log" => 0, "app_default_controller_tag_name" => "div", "cache_loaded_file" => 0, "company_name" => "igkdev", "copyright" => IGK_COPYRIGHT, "db_name" => "igkdev", "db_prefix" => IGK_DEFAULT_DB_PREFIX, "db_pwd" => "",
        "db_server" => "localhost",
        "db_user" => "root",
        "default_controller" => null,
        "mail_admin" => "administrator@igkdev.com", "mail_contact" => "info@igkdev.com", "mail_port" => 25, "mail_server" => "relay.skynet.be", "menu_defaultPage" => "default", "meta_copyright" => "igkdev@igkdev.com", "meta_description" => "default page description", "meta_enctype" => "text/html; charset=utf-8", "meta_keywords" => "IGKDEV, .NET, C#, BONDJE DOUE, Developper, PHP, HTML5, WEBDEV, PLATEFORM, BALAFON", "meta_title" => "igkdev.be",
        "powered_message" => "IGKDEV",
        "powered_uri" => "https://igkdev.com",
        "show_debug" => 0, "show_powered" => 1, "website_domain" => $servername, "website_prefix" => "igk", "website_title" => $servername, "default_lang" => "fr",
    ] + include(IGK_LIB_DIR . "/.setting.global.pinc");

    ksort($data);
    return igk_cache_array_content(igk_map_array_to_str($data));
}
function igk_get_defaultcron_data($file = "cronjob.php")
{
    $bal = IGK_APP_DIR . "/Lib/igk/bin/balafon";
    $rootdir = igk_io_workingdir();
    $author = igk_configs()->get("author", IGK_AUTHOR);
    $o = "#!/usr/bin/env php\n";
    $o .= "<?php\n";
    $o .= "// @file: {$file}\n";
    $o .= "// @date: " . date("Ymd H:i:s") . "\n";
    $o .= "// @author: " . $author . "\n";
    $o .= "// @license: " . "\n\n";

    $o .= "shell_exec(\"{$bal} --run:cron --wdir:{$rootdir}\");";
    return $o;
}
///str_replace(" ", "_", str_replace("=","_", $n));
/**
 */
function igk_get_defaultview_content()
{
    $date = date("Y/m/d - H:i:s");
    $author = igk_configs()->defaultAuthor ?? IGK_AUTHOR;
    return <<<EOF
<?php
// +-
// @author: {$author}
// view file: {$date}
// +-

\$t->clearChilds();
\$doc->Title = \$fname." - ".\$this->Name;
\$t->div()->Content = "200 - Empty view";

EOF;
}
///<summary></summary>
///<param name="node"></param>
///<param name="s" ref="true"></param>
///<param name="options"></param>
/**
 * 
 * @param mixed $node 
 * @param mixed $s 
 * @param mixed $options 
 */
function igk_get_defined_ns($node, &$s, $options)
{
    $attr = $node->getAttributes();
    $g = is_object($attr) && method_exists($attr, "getNS") ? $attr->getNS() : null;
    if ($g) {
        $b = 0;
        foreach ($g as $k => $v) {
            if ($b)
                $s .= " ";
            if ($k == "@global")
                $s .= "xmlns";
            else
                $s .= "xmlns:" . $k;
            $b = 1;
            $s .= "=\"" . $v . "\"";
        }
    }
}
///<summary>get the document</summary>
///<param name="key">key to register document </param>
/**
 * get the document
 * @param string $key key to register document 
 * @return ?IGKHtmlDoc
 */
function igk_get_document(string $key, $clear = false, $init = false)
{
    // in application setting we store the document key => $index
    /**
     * @var IGKHtmlDoc $doc
     */
    if (!IGKApp::IsInit() || empty($key))
        return null;
    $k = $key;
    $app = igk_app();
    $v_appInfo = $app->settings->appInfo;
    if (!isset($v_appInfo->{IGK_KEY_DOCUMENTS})) {
        $v_appInfo->{IGK_KEY_DOCUMENTS} = [];
    }
    if (!isset(igk_environment()->documents)) {
        igk_environment()->documents = array();
    }
    $v = &$v_appInfo->{IGK_KEY_DOCUMENTS};
    $doc_index = igk_getv($v, $k);

    $rdoc = &igk_environment()->get("documents");
    if ($doc_index === null) {
        $doc_index = max(1, count($v) ?
            max(array_keys($v)) : 0);

        $doc = IGKHtmlDoc::CreateDocument($doc_index);
        $doc->setParam(IGK_DOC_ID_PARAM, $key);
        $v_appInfo->{IGK_KEY_DOCUMENTS}[$k] = $doc->getId();
        $doc_index = $doc->getId();
        if ($v === null) {
            $v = array($k => $doc_index);
        } else {
            $v[$k] = $doc_index;
            if ($doc === $app->getDoc()) {
                igk_ilog("2. your new document is equal to global document some error behaviour");
                igk_die("strange behaviour . not valid");
            }
        }
        $rdoc[$doc_index] = $doc;
    } else {
        if (!isset(igk_environment()->documents[$doc_index])) {
            $doc = IGKHtmlDoc::CreateDocument($doc_index);
            $rdoc[$doc_index] = $doc;
        } else {
            $doc = igk_environment()->documents[$doc_index];
        }
    }
    if ($doc && $clear) {
        $doc->getBody()->getBodyBox()->clearChilds();
    }
    // if ($_obj && $init && method_exists($key, 'initDocument')) {
    //     $key->initDocument($doc);
    // }
    return $doc;
}
///<summary>return the sub created documents attached to system</summary>
/**
 * return the sub created documents attached to system
 */
function igk_get_documents()
{
    return igk_app()->session->getParam(IGK_KEY_DOCUMENTS);
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_get_domain($n)
{
    if (preg_match_all(IGK_DOMAIN_REGEX, $n, $v)) {
        return $v["domain"][0];
    }
    return IGK_STR_EMPTY;
}
///<summary>extract root domain from uri</summary>
/**
 * extract root domain from uri
 */
function igk_get_domain_name(string $n): ?string
{
    $q = parse_url($n);
    if ($host = igk_getv($q, 'host')) {
        if (IGKValidator::IsIpAddress($host)) {
            return null;
        }
    }
    if (is_null($host)) {
        $host = $n;
    }
    if (preg_match_all(IGK_DOMAIN_NAME_REGEX, $host, $v)) {
        return $v["domain"][0];
    }
    return null;
}
///<summary>get all environment variable that match the pattern</summary>
/**
 * get all environment variable that match the pattern
 */
function igk_get_env_all($match)
{
    $tab = igk_environment()->getEnvironments();
    foreach ($tab as $k => $v) {
        if (strstr($k, $match)) {
            $t[$k] = $v;
        }
    }
    return $t;
}
///<summary></summary>
///<param name="key"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $key 
 * @param mixed $callback 
 */
function igk_get_env_init($key, $callback)
{
    return igk_environment()->init($key, $callback);
}
///<summary></summary>
///<param name="v" default="null"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_get_env_lib_loaded($v = null)
{
    $k = "sys://libloaded";
    igk_set_env($k, $v);
    return igk_get_env($k, 0);
}
///<summary>retrieve environment variable as object</summary>
/**
 * retrieve environment variable as object
 */
function igk_get_env_obj($ns)
{
    $c = igk_get_env_all($ns);
    if (!$c)
        return null;
    $t = array();
    $ln = strlen($ns) + 1;
    $ln -= $ns[$ln - 2] == '/' ? 1 : 0;
    foreach ($c as $k => $v) {
        $n = substr($k, $ln);
        $tt = explode("/", $n);
        if (igk_count($tt) == 1) {
            $t[$n] = $v;
        } else {
            $g = null;
            $nn = array_pop($tt);
            foreach ($tt as $m) {
                if (($g == null)) {
                    if (isset($t[$m])) {
                        $g = $t[$m];
                    } else {
                        $g = igk_createobj();
                        $t[$m] = $g;
                    }
                } else {
                    if (!isset($g->$m)) {
                        $g->$m = igk_createobj();
                        $g = $g->$m;
                    }
                }
            }
            $g->$nn = $v;
        }
    }
    return (object)$t;
}
///<summary>get all global environments table</summary>
/**
 * get all global environments table
 */
function igk_get_envs()
{
    return igk_environment()->getEnvironments();
}
///<summary>return all error</summary>
/**
 * return all error
 */
function igk_get_error($tag = null)
{
    $tab = igk_get_env("sys://igk_set_error");
    if ($tag != null) {
        $o = array();
        foreach ($tab as $v) {
            if ($v["tag"] == $tag) {
                $o[] = $v;
            }
        }
        return $o;
    }
    igk_set_env("sys://igk_set_error", null);
    return $tab;
}
///<summary>parse error key</summary>
///<param name="code"></param>
/**
 * parse error key
 * @param mixed $code 
 */
function igk_get_error_key($code)
{
    return igk_error($code)["Msg"];
}
///<summary></summary>
/**
 * 
 */
function igk_get_eval_global_script_actions()
{
    return igk_get_env("sys://article/eval_script_global", []);
}
///<summary>get event keys</summary>
/**
 * get event keys
 */
function igk_get_event_key($ctrl, $name)
{
    return strtolower("sys://" . $ctrl->getName() . "/event/" . $name);
}
///<summary>get exception from eval inclusion</summary>
/**
 * get exception from eval inclusion
 */
function igk_get_exception_eval($Ex, $traces = null)
{
    $content = "";
    $traces = $traces ?? $Ex->getTrace();
    if (strpos($Ex->getFile(), "eval()") !== false) {
        if ($traces && ($tr = igk_getv($traces, 0))) {
            $s = "";
            $c = igk_getv($tr, "class");
            if ($c) {
                $s = "_class_:" . igk_get_reg_class_file($c);
            } else {
                $s = igk_get_reg_func_file(strtolower($tr["function"]));
            }
            if ($s) {
                if (igk_is_cmd()) {
                    $content .= $s . ":" . $Ex->getLine() . "";
                } else
                    $content .= "<li><b>Eval on file</b>: " . $s . ":" . $Ex->getLine() . "</li>";
            }
        }
    }
    return $content;
}
///<summary></summary>
///<param name="id" default="null"></param>
/**
 * 
 * @param mixed $id 
 */
function igk_get_form_args($id = null)
{
    $tab = igk_get_env("sys://form_args");
    if ($id == null)
        return $tab;
    if ($tab != null)
        return igk_getv($tab, $id);
    return null;
}
///<summary></summary>
/**
 * get system register form builder engines
 */
function igk_get_form_builder_engines()
{
    $tab = igk_get_env("sys://form/builderengines");
    if ($tab)
        return array_keys($tab);
    return null;
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_get_frame($name)
{
    return igk_getctrl(IGK_FRAME_CTRL)->getFrame($name);
}
///<summary></summary>
/**
 * 
 */
function igk_get_frame_ext()
{
    return strtolower(".frame.phtml");
}
///<summary>return function location</summary>
/**
 * return function location
 */
function igk_get_func_location($func)
{
    if (function_exists($func)) {
        $fc = new ReflectionFunction($func);
        return (object)array(
            "file" => $fc->getFileName(),
            "line" => $fc->getStartLine()
        );
    }
    return null;
}
///<summary></summary>
///<param name="func"></param>
/**
 * 
 * @param mixed $func 
 */
function igk_get_func_location_str($func)
{
    return ($g = igk_get_func_location($func)) ? $g->file : null;
}
///COOKIE FUNCTION
/**
 */
function igk_get_global_cookie($n, $default = 0)
{
    return igk_getv($_COOKIE, $default);
}
///<summary>get plateform header info</summary>
/**
 * get plateform header info
 */
function igk_get_header_obj()
{
    $obj = igk_createobj();
    $m = "/^IGK_/i";
    foreach (igk_get_allheaders() as $k => $v) {
        if (!preg_match($m, $k))
            continue;
        $s = substr($k, 4);
        $obj->$s = $v;
    }
    return $obj;
}

///<summary> used to created a hosted component</summary>
/**
 *  used to created a hosted component
 */
function igk_get_host_component($host, $name, $callback)
{
    $t = $host->getParam(IGK_NAMED_NODE_PARAM);
    if ($n = ($t != null ? igk_getv($t, $name) : null) ?? $callback()) {
        $t[$name] = $n;
        $host->setParam(IGK_NAMED_NODE_PARAM, $t);
    }
    return $n;
}
///<summary>get package name component</summary>
///<param name="name">name of registrated package. name can be null or empty to list all components</param>
/**
 * get package name component
 * @param mixed $name name of registrated package. name can be null or empty to list all components
 */
function igk_get_html_components($name, $sortbykey = 0)
{
    $t = array();
    $nk = IGK_ENV_HTML_COMPONENTS;
    $cp = igk_get_env($nk);
    $userfunc = get_defined_functions()["user"];
    foreach ($userfunc as $l => $v) {
        if (preg_match("/^" . IGK_FUNC_NODE_PREFIX . "{$name}(?P<name>(.)+)$/i", $v, $tab))
            $t[$tab["name"]] = ["type" => "f", "name" => $tab[0]];
    }
    $classes = get_declared_classes();
    foreach ($classes as $l => $v) {
        if (preg_match("/^IGKHtml{$name}(?P<name>(.)+)Item$/i", $v, $tab)) {
            $t[$tab["name"]] = ["type" => "c", "name" => $tab[0]];
        }
    }
    if ($sortbykey)
        igk_array_sortbykey($t);
    return $t;
}
///<summary>convert string to identifier</summary>
/**
 * convert string to identifier
 */
function igk_get_identifier($n)
{
    return preg_replace("/([^0-9a-z])/i", "_", $n);
}
///<summary></summary>
///<param name="options"></param>
///<param name="src"></param>
/**
 * 
 * @param mixed $options 
 * @param mixed $src 
 */
function igk_get_image_uri($options, $src)
{
    $lnk = $src;
    $m = igk_xml_is_mailoptions($options);
    if ($m) {
        $uri = new HtmlUri();
        $uri->setValue($lnk);
        $r = new HtmlNode("img");
        if ($options->Attachement) {
            $d = null;
            if (file_exists($lnk)) {
                $d = $options->Attachement->attachFile(igk_realpath($lnk), "images/pictures", null);
            } else {
                $q = igk_curl_post_uri($lnk);
                if (!empty($q)) {
                    $d = $options->Attachement->attachContent($q, "images/pictures", null);
                }
            }
            if ($d)
                $s = "cid:" . $d->CID;
        } else {
            $s = $uri->getValue($options);
        }
        return $s;
    }
    if (strstr($lnk, "..")) {
        $lnk = str_replace("../", IGK_STR_EMPTY, $lnk);
    }
    $s = IGK_STR_EMPTY;
    $k = igk_uri($lnk);
    $rf = null;
    if (!empty($k)) {
        if (IGKValidator::IsUri($k) || (preg_match("#^file://#i", $k)) || (preg_match("#^\{(.)*\}$#i", $k))) {
            $s = $k;
        } else {
            $f = igk_getv(explode("?", $lnk), 0);
            if (file_exists($f)) {
                $rf = igk_realpath($f);
                if (igk_is_ajx_demand()) {
                    $s = igk_io_baseuri() . "/" . igk_uri(igk_io_basepath($rf));
                } else {
                    $s = igk_io_fullpath2uri($rf, true);
                }
            } else {
                $s = "";
                $bf = igk_uri(igk_io_currentrelativepath($f));
                if (!file_exists($bf)) {
                    $bf = null;
                }
                $s = $bf;
            }
        }
    } else
        $s = $k;
    if (($s != null) && (($kc = strpos($lnk, "?")) !== false)) {
        $s .= "?" . substr($lnk, $kc + 1);
    }
    return $s;
}
///<summary>get index file basename</summary>
/**
 * get index file basename
 */
function igk_get_index($dir, $default = 'index.php')
{
    $dab = igk_io_getfiles($dir, "/\index\.(php|phtml|html|htm)$/", false);
    return basename(igk_getv($dab, 0, $default));
}
///<summary></summary>
///<param name="classname"></param>
/**
 * 
 * @param mixed $classname 
 */
function igk_get_instance_key($classname)
{
    $r = igk_get_env(strtolower(trim("sys://instance/key/" . $classname)));
    $g = $r ?? strtolower(\IGK\Helper\StringUtility::Uri("sys://" . $classname . "::ise"));
    return $g;
}
///<summary>get last rendered document</summary>
/**
 * get last rendered document
 */
function igk_get_last_rendered_document()
{
    return IGKHtmlDoc::LastRenderedDocument();
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="name"></param>
///<param name="callback"></param>
///<param name="duration" default="10"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $name 
 * @param mixed $callback 
 * @param mixed $duration 
 */
function igk_get_live_data($ctrl, $name, $callback = null, $duration = 10)
{
    $t = $ctrl->getParam("sys://liveddata", array());
    $mt = igk_getv($t, $name);
    if ($mt && $mt->duration) {
        $duration = $mt->duration;
    }
    if (!$mt || ((time() - $mt->time) > $duration)) {
        if ($callback) {
            $o = $callback();
            $mt = (object)array("time" => time(), "data" => $o, "duration" => $duration);
            $t[$name] = $mt;
            $ctrl->setParam("sys://liveddata", $t);
        } else {
            igk_wln("duration exeed : " . $duration, $mt);
            return null;
        }
    } else
        $o = $mt->data;
    return $o;
}
///<summary></summary>
///<param name="fi"></param>
///<param name="ctrl" default="null"></param>
/**
 * 
 * @param mixed $fi 
 * @param mixed $ctrl 
 */
function igk_get_local_file($fi, $ctrl = null)
{
    $s = igk_dir($fi);
    $s = igk_html_resolv_img_uri($s);
    return $s;
}
///<summary></summary>
/**
 * 
 */
function igk_get_manifest_content()
{
    $info = "igkapp_manifest";
    $out = <<<EOF
CACHE MANIFEST
#{$info} - manifest
EOF;
}
///<summary></summary>
///<param name="id"> path to module </param>
/**
 * 
 * @param mixed $id  path to module 
 */
function igk_get_module($id)
{
    return igk_init_module($id, null, false);
}
///<summary></summary>
/**
 * helper: get module directory 
 */
function igk_get_module_dir()
{
    return Path::getInstance()->getModuleDir();
}
///<summary></summary>
///<param name="dir"></param>
/**
 * retrieve module name by real module directory
 * @param string $dir 
 */
function igk_get_module_name(string $dir)
{
    $moddir = realpath(igk_get_module_dir());
    $dir = realpath($dir);
    if (stristr($dir, $moddir)) {
        return igk_uri(substr($dir, strlen($moddir) + 1));
    }
    return null;
}
///<summary>retrieve the active installed module</summary>
///<note>only module with module.json file in root folder.</note>
/**
 * retrieve the active installed module.
 */
function igk_get_modules()
{
    return \IGK\System\Modules\ModuleManager::GetInstalledModules();
}
///<summary>create a new data adapter from existing</summary>
/**
 * create a new data adapter from existing
 */
function igk_get_new_data_adapter($controllerOrAdpaterName, $throwException = false)
{
    $ad = igk_get_data_adapter($controllerOrAdpaterName, false);
    if ($ad !== null) {
        $n = get_class($ad);
        $r = igk_create_adapter_from_classname($n);
        return $r;
    }
    return null;
}
///<summary>get new relative html relative uri value</summary>
/**
 * get new relative html relative uri value
 */
function igk_get_nhru($value)
{
    return new IGKHtmlRelativeUriValueAttribute($value);
}
///<summary></summary>
///<param name="attr"></param>
///<param name="value"></param>
/**
 * 
 * @param mixed $attr 
 * @param mixed $value 
 */
function igk_get_node_attr_value($attr, $value)
{
    if ($value == null)
        return null;
    if (is_string($value))
        return HtmlRenderer::GetStringAttribute($value, null);
    switch ($attr) {
        case "Ctrl":
            return HtmlRenderer::GetStringAttribute($value->getName(), null);
    }
    return HtmlRenderer::GetStringAttribute($value, null);
}
///<summary> used to retrieve node expression. inverse selection</summary>
/**
 *  used to retrieve node expression. inverse selection
 */
function igk_get_node_expression($node, $dp = 0)
{
    $d = get_class($node);
    if ($node->tagName == "igk:text") {
        return $node->Content;
    }
    $o = "";
    $tag = $node->tagName;
    $n = "";
    if (preg_match(IGK_HTML_NODE_REGEX, $d)) {
        $n = igk_preg_match(IGK_HTML_NODE_REGEX, $d, 'name');
        $tag = "igk:" . $n;
    }
    $o .= "<" . $tag;
    if ($n) {
        $attr = $node->getExpressionAttributes();
        if ($attr != null) {
            foreach ($attr as $v => $t) {
                $o .= " igk:" . $v . "=" . igk_get_node_attr_value($v, $t);
            }
        }
    }
    if ($node->HasAttributes) {
        $s = trim($node->getAttributeString(null));
        if (!empty($s))
            $o .= " " . $s;
    }
    $c = HtmlUtils::GetContentValue($node);
    $cc = trim($c);
    if (!$node->hasChilds && empty($cc)) {
        $o .= "/>";
    } else {
        $o .= ">";
        $t = $node->Childs;
        $o .= $cc;
        if ($t) {
            foreach ($t as $v) {
                $o .= igk_get_node_expression($v, $dp);
            }
        }
        $o .= "</" . $tag . ">";
    }
    return $o;
}
///<summary>get registrated namespace</summary>
/**
 * get registrated namespace
 */
function igk_get_ns()
{
    return igk_reg_ns();
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_get_ns_func($name)
{
    $ns = __NAMESPACE__;
    if (!empty($ns))
        return $ns . "\\" . $name;
    return $name;
}
///<summary></summary>
/**
 * 
 */
function igk_get_packages_dir()
{
    if (defined("IGK_PACKAGE_DIR"))
        return constant('IGK_PACKAGE_DIR');
    if (defined("IGK_APP_DIR"))
        return igk_dir(IGK_APP_DIR . "/" . IGK_PACKAGES_FOLDER);
}
///<summary></summary>
///<param name="palname" default="default"></param>
/**
 * 
 * @param mixed $palname 
 */
function igk_get_palette($palname = "default")
{
    $cp = igk_getctrl(IGK_PALETTE_CTRL, false);
    if ($cp == null) {
        return null;
    }
    $p = $palname == "default" ? igk_configs()->get("CurrentPaletteName", "default") : $palname;
    return igk_getv($cp->Palettes, $p, null);
}
///<summary></summary>
///<param name="ext"></param>
/**
 * 
 * @param mixed $ext 
 */
function igk_get_path_exec($ext)
{
    $t = igk_get_env("sys://env//path_exec");
    return igk_getv($t, $ext);
}
///<summary></summary>
/**
 * 
 */
function igk_get_platform_header_array()
{
    return array(
        'igk-server: ' . IGK_PLATEFORM_NAME,
        'igk-server-admin: bondje.doue@igkdev.com',
        'igk-server-build: ' . IGK_PLATEFORM_NAME . '(' . IGK_VERSION . ')',
        'igk-cref: ' . igk_app()->session->getCRef()
    );
}
///<summary>get query option array from string</summary>
/**
 * get query option array from string
 * @param string $query_option
 * @return array 
 */
function igk_get_query_options(string $query_options): array
{

    $data = array();
    preg_replace_callback(
        "/(?P<name>[^;]+)=(?P<value>([^;]+|;;))/i",
        function ($m) use (&$data) {
            $v = $m["value"];
            if ($v == ';;')
                $v = ';';
            $data[$m["name"]] = $v;
        },
        $query_options
    );

    return $data;
}
///<summary></summary>
///<param name="className"></param>
/**
 * 
 * @param mixed $className 
 */
function igk_get_reg_class_file($className)
{
    return igk_get_reg_file("sys://reflection/class", $className);
}
///<summary></summary>
///<param name="key"></param>
///<param name="name"></param>
/**
 * 
 * @param mixed $key 
 * @param mixed $name 
 */
function igk_get_reg_file($key, $name)
{
    $tkey = "sys://functable";
    $fkey = "sys://files";
    $g = igk_get_env($key);
    if ($g && isset($g[$tkey][$name])) {
        return $g[$fkey][$g[$tkey][$name]];
    }
    return null;
}
///<summary></summary>
///<param name="functionname"></param>
/**
 * 
 * @param mixed $functionname 
 */
function igk_get_reg_func_file($functionname)
{
    return igk_get_reg_file("sys://reflection/funcs", $functionname);
}
///<summary>register global system controller</summary>
/**
 * register global system controller
 * @return ?BaseController controller
 */
function igk_get_regctrl(string $name)
{
    if (!IGKApp::IsInit()) {
        return null;
    }
    return igk_app()->getControllerManager()->getRegistratedNamedController($name);
}
///<summary> get rendering node</summary>
/**
 *  get rendering node
 */
function igk_get_rendering_node()
{
    return igk_get_env("sys://igk_html_rendered_node/node");
}
///<summary>retreive requested object as object</summary>
///<param name="callbackfilter">callable that will filter the request available key</param>
/**
 * retreive requested object as object
 * @param callback|string|null $callbackfilter callable that will filter the request available key
 * @param bool $replace in case on callable filter use replacement
 * @param ?array $the request array source. if not spécifier de default $_REQUEST will be used. 
 */
function igk_get_robj($callbackfilter = null, $replace = 0, $request = null)
{
    $t = array();
    $m = $callbackfilter;
    if ($m === null) {
        $callbackfilter = function (&$k, $v, $rp) {
            $rgx = "/^cl/i";
            $p = preg_match($rgx, $k);
            if ($p && $rp)
                $k = preg_replace($rgx, "", $k);
            return $p;
        };
    } else {
        if (is_string($m)) {
            if (strpos($m, '|') !== false) {
                $t = array_fill_keys(explode('|', $m), null);
            }
            $m = str_replace("-", '(_|-)', $m);
            $rgx = "/^(" . $m . ")$/i";
            $callbackfilter = function ($k, $rp) use ($rgx) {
                $p = preg_match($rgx, $k);
                if ($p && $rp) {
                    $k = preg_replace($rgx, "", $k);
                }
                return $p;
            };
        } else if (is_array($m)) {
            $t = array_fill_keys($m, null);
            $callbackfilter = function (&$k, $v, $rp) use ($m) {
                if (in_array($k, $m)) {
                    return true;
                }
                return false;
            };
        }
    }
    if ($request = $request ?? $_REQUEST) {
        $_fc_ = $callbackfilter;
        // igk_wln($_REQUEST, $request, var_export($_REQUEST, true));
        foreach ($request as $k => $v) {
            $nk = '' . $k;
            // igk_wln("call ", $nk,  $v, $callbackfilter);
            if (
                is_callable($_fc_)
                && $_fc_($nk, $v, $replace)
            ) {
                $nk = str_replace("-", "_", $nk);
                $t[$nk] = is_string($v) ? igk_str_quotes($v) : $v;
            }
        }
    }
    return (object)$t;
}
///<summary>Represente igk_get_robjs function</summary>
///<param name="list"></param>
///<param name="replace" default="0"></param>
///<param name="request" default="null"></param>
/**
 * Represente igk_get_robjs function
 * @param mixed $list 
 * @param mixed $replace 
 * @param mixed $request 
 */
function igk_get_robjs($list, $replace = 0, $request = null)
{
    return igk_get_robj(is_string($list) ? explode("|", $list) : $list, $replace, $request);
}
///<summary>get run script</summary>
/**
 * get run script
 */
function igk_get_run_script_path()
{
    return igk_configs()->get("php_run_script");
}
///<summary></summary>
/**
 * 
 */
function igk_get_selected_builder_engine()
{
    return igk_get_env("sys://form/selectedbuilderengine");
}
///<summary>return the map selection fo the node</summary>
///<return >array of selection</return>
/**
 * return the map selection fo the node
 */
function igk_get_selector_map($node)
{
    $o = array();
    $toi = IGK_STR_EMPTY;
    $c = 0;
    while ($node) {
        $oi = "((\s|^)(" . $node->tagName;
        $h = ($cl = $node["class"]) ? $cl->getValue() : null;
        $id = $node["id"];
        $tf = IGK_STR_EMPTY;
        if (!empty($id)) {
            $tf .= "(#" . $id . ")";
        }
        if (empty($h) == false) {
            $b = explode(" ", $h);
            if (count($b) > 0) {
                $i = 0;
                $rk = IGK_STR_EMPTY;
                foreach ($b as $r => $s) {
                    if (!empty($s)) {
                        if ($i)
                            $rk .= "|";
                        $rk .= "\." . $s;
                        $i = 1;
                    }
                }
                if ($i) {
                    if (!empty($tf)) {
                        $tf .= "|(" . $rk . ")";
                    } else
                        $tf .= "(" . $rk . ")";
                }
            }
        }
        if (!empty($tf))
            $oi .= $tf . "*|" . $tf;
        $oi .= ")";
        if (empty($toi))
            $toi = $oi . "$)" . $toi;
        else {
            $toi = "(((" . $oi . ")" . "(|>))*" . $toi . "))";
        }
        $node = $node->getParentNode();
        if (!$node || ($node->tagName == "body")) {
            break;
        }
        $c++;
        $o[] = "/(" . $toi . ")/im";
    }
    $o[] = "/(" . $toi . ")/im";
    return $o;
}
///<summary></summary>
///<param name="servicename"></param>
/**
 * get registrated service by name 
 * @param string $service_type service root type name
 */
function igk_get_services(string $service_type)
{
    $k = Path::Combine(IGK_SERVICE_PREFIX_PATH, $service_type);
    return igk_get_env($k);
}

/**
 * retrieve the registrate service 
 * @param string $service_type
 * @param string $name
 */
function igk_get_service(string $service_type, string $name)
{
    $g = igk_get_services($service_type);
    if ($g) {
        return igk_getv($g, $name);
    }
    return null;
}
///<summary>shortcut to get session param value</summary>
/**
 * shortcut to get session param value
 */
function igk_get_session($name)
{
    return igk_app()->session->$name;
}
///<summary>get session event by name </summary>
///<param name="name">the key of the name session to get</param>
/**
 * get session event by name 
 * @param mixed $name the key of the name session to get
 */
function igk_get_session_event($name)
{
    $ctx = igk_current_context();
    $key = "sys://global_events";
    $e = strpos("running|starting", $ctx) !== false;
    $empty = array();
    $primary = igk_get_env($key, array());
    if ($e) {
        $c = array_merge_recursive($primary, igk_app()->session->Events ?? array());
        $s = igk_getv($c, $name);
        return $s;
    } else {
        return igk_getv($primary, $name);
    }
}
///<summary>get controller session event handler</summary>
/**
 * get controller session event handler
 */
function igk_get_session_event_handler()
{
    $evtlist_controller = igk_get_env("sys://event/ctrl/handler");
    $m = igk_app()->getControllerManager();
    if (($evtlist_controller == null) && $m) {
        $evtlist_controller = array();
        $ctrls = $m->getControllers();
        foreach ($ctrls as $k) {
            if ((get_class($k) != __PHP_Incomplete_Class::class) && method_exists($k, "onHandleSystemEvent")) {
                $evtlist_controller[] = $k;
            }
        }
        igk_set_env("sys://event/ctrl/handler", $evtlist_controller);
    }
    return $evtlist_controller;
}
///<summary>Represente igk_get_session_prefix function</summary>
/**
 * Represente igk_get_session_prefix function
 */
function igk_get_session_prefix()
{
    $prefix = "sess_";
    if (defined("IGK_SESS_DIR"))
        $prefix = IGK_SESSION_FILE_PREFIX;
    return $prefix;
}
///<summary>convert value to presentation</summary>
/**
 * convert value to presentation
 */
function igk_get_sizev($v, $round = 4)
{
    $sm_sizeFormat = array(
        "Tb" => 1099511627776,
        "Gb" => 1073741824,
        "Mb" => 1048576,
        "Kb" => 1024,
        "B" => 1
    );
    if ($v == 0)
        return "0 byte";
    foreach ($sm_sizeFormat as $k => $vv) {
        if ($v > $vv) {
            return round(($v / $vv), $round) . " " . __("enum.memoryunit." . $k, $k);
        }
    }
    return "0 byte";
}
///<summary></summary>
/**
 * 
 */
function igk_get_stack_depth()
{
    return igk_count(debug_backtrace()) - 1;
}
///<summary>create a formatted string data object by retreive the {index} with args</summary>
/**
 * create a formatted string data object by retreive the {index} with args
 */
function igk_get_string_format($str)
{
    $n = null;
    $tab = func_get_args();
    if (defined("__NAMESPACE__"))
        $n = __NAMESPACE__ . 'IGKFormatString::Create';
    else
        $n = 'IGKFormatString::Create';
    return call_user_func_array($n, $tab);
}
///<summary></summary>
///<param name="obj"></param>
///<param name="property"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $property 
 */
function igk_get_string_propvalue($obj, $property)
{
    return IGK\System\Html\HtmlFormatGetValueString::Create($obj, $property);
}
///<summary></summary>
/**
 * 
 */
function igk_get_system_user()
{
    return igk_get_user_bylogin(IGK_SYS_USER_LOGIN) ?? igk_die("no system user found");
}
function igk_sys_default_user()
{
    $login = igk_configs()->default_user;
    if ($login) {
        return igk_get_user_bylogin($login);
    }
}
///<summary> used to get traceable array</summary>
/**
 *  used to get traceable array
 */
function igk_get_trace_array($Ex, $level = 0)
{
    $t = array();
    $i = 0;
    foreach ($Ex->getTrace() as $v) {
        if ($i >= $level) {
            if (isset($v["file"])) {
                $t[] = array($v["file"] => $v["line"]);
            }
        }
        $i++;
    }
    return $t;
}
///<summary>get the type of the var </summary>
/**
 * get the type of the var 
 */
function igk_get_type($n)
{
    if (is_string($n))
        return "String";
    if (is_array($n)) {
        return "Array";
    }
    if (is_object($n))
        return get_class($n);
    if (is_numeric($n))
        return "Number";
    return "Unknow";
}
///<summary></summary>
///<param name="uid"></param>
/**
 * 
 * @param mixed $uid 
 */
function igk_get_user($uid, ?array $options = null)
{
    return IGK\Models\Users::select_row($uid, $options);
}
///<summary></summary>
///<param name="login"></param>
/**
 * helper: get user by login
 * @param string $login 
 * @return ?\IGK\Models\Users
 */
function igk_get_user_bylogin(string $login)
{
    return \IGK\Models\Users::GetUserByLogin($login);
}
///<summary></summary>
///<param name="name"></param>
///<param name="default" default="null"></param>
///<param name="reg" default="false"></param>
///<param name="comment" default="null"></param>
/**
 * 
 * @param string $name 
 * @param mixed $default 
 * @param mixed $reg 
 * @param mixed $comment 
 */
function igk_get_uvar(string $name, $default = null, $reg = false, $comment = null)
{
    if (empty($name))
        return $default;
    $ctrl = igk_getctrl(IGK_USERVARS_CTRL);
    if ($ctrl == null)
        return $default;
    $t = igk_getv($ctrl->Vars, $name);
    if ($t)
        return $t["value"];
    if ($reg && $ctrl) {
        $ctrl->regVars($name, $default, $comment);
        $ctrl->__storeVars();
    }
    return $default;
}
///<summary></summary>
///<param name="obj"></param>
///<param name="path"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $path 
 */
function igk_get_value($obj, $path)
{
    $c = explode("/", $path);
    $o = null;
    while ($obj && (igk_count($c) > 0)) {
        $g = null;
        $n = array_shift($c);
        if (is_object($obj)) {
            $g = $obj->$n;
        } else if (is_array($obj)) {
            if (!isset($obj[$n])) {
                igk_die("faile to ge value " . $n);
            }
            $g = $obj[$n];
        }
        $obj = $g;
        $o = $g;
    }
    return $obj;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="name"></param>
///<param name="default" default="null"></param>
/**
 * helper get view arg
 * @param mixed $ctrl 
 * @param mixed $name 
 * @param mixed $default 
 */
function igk_get_view_arg($ctrl, $name, $default = null)
{
    $vars = $ctrl->getSystemVars();
    return igk_getv($vars, $name, $default);
}
///<summary>get current view args</summary>
/**
 * get current view args
 */
function igk_get_view_args()
{
    return \IGK\Helper\ViewHelper::GetViewArgs();
}
///<summary>Represente igk_get_viewfile function</summary>
/**
 * Represente igk_get_viewfile function
 */
function igk_get_viewfile()
{
    return \IGK\Helper\ViewHelper::File();
}
///<summary>convert to view param </summary>
///<param name="param">param to convert</param>
/**
 * convert to view param 
 * @param mixed $param param to convert
 */
function igk_get_viewparam($param)
{
    if (is_string($param)) {
        return array($param);
    }
    return $param;
}

///<summary>get web page content utility</summary>
/**
 * get web page content utility
 */
function igk_get_webpagecontent()
{
    if (igk_app()->CurrentPageFolder == IGK_CONFIG_PAGEFOLDER) {
        return igk_getconfigwebpagectrl()->ConfigNode;
    } else {
        $c = igk_get_defaultwebpagectrl();
        if ($c)
            return $c->page_content;
        else {
            $igk = igk_app();
            return $igk->getDoc()->bodycontent;
        }
    }
}
///<summary></summary>
/**
 * 
 */
function igk_get_widgets()
{
    return igk_get_env(IGK_ENV_WIDGETS_KEY);
}
///<summary></summary>
/**
 * generate base access
 */
function igk_getbase_access(
    string $root_dir,
    $config = IGK_CONFIG_PAGEFOLDER,
    $domain = "",
    $servername = null
) {
    // $rdir = igk_io_rootdir();
    // $bdir = igk_io_basedir();
    //$is_baseroot = $rdir == $bdir;
    // $root_dir = igk_uri(igk_io_rootbasedir());
    // $root_dir = ltrim(igk_str_rm_last($root_dir, "/"), "/");
    // $root_dir = !empty($root_dir) ? "\"/" . $root_dir : "\""; 
    $_rd =  $root_dir . "/Lib/igk/igk_redirection.php";
    extract([
        "const" => "constant",
        "error_404" => $_rd . "?__c=404&__e=1\"",
        "error_403" => $_rd . "?__c=403&__e=1\"",
        "error_901" => $_rd . "?__c=901\"",
        "servername" => $servername ?? igk_server_name()
    ]);
    // $domain = igk_io_currentbasedomainuri(); 
    $out = eval("?>" . igk_io_read_allfile(IGK_LIB_DIR . "/Inc/.htaccess.index.default"));
    return $out;
}
///<summary>return the base index.php content</summary>
/**
 * return the base index.php content
 */
function igk_getbaseindex_src($libfile)
{
    $showError = "";
    $inf = igk_createobj();
    $inf->date = igk_date_now();
    $inf->lib = $libfile;
    $relative = "./";
    if (IGK_APP_DIR != IGK_BASE_DIR) {
        $relative = "";
        $wdir = igk_io_workingdir();
        $bdir = IGK_BASE_DIR;
        while (($bdir != $wdir) && ($cdir = dirname($bdir)) != $bdir) {
            $relative .= "../";
            $bdir = $cdir;
        }
    }

    $inf->comment = __(
        <<<EOF
this file was generated by balafon service. please do not modify until you know what you are doing.
EOF

    );
    $s = <<<EOF
<?php
// @file: index.php
// @date : {$inf->date}
// @author : C.A.D. BONDJE DOUE
// @mail: bondje.doue@igkdev.com
// @generator: balafon service
// @note: {$inf->comment}
if (!version_compare(PHP_VERSION, "7.3", ">=")){
    die("mandory version required. 7.3<=");
}

{$showError}
// + |------------------------------------------------------------
// + | require framework
// + |
\$appdir = realpath('{$relative}');
// + |------------------------------------------------------------
// + | define application directory
// + |
define("IGK_APP_DIR", \$appdir."/application");
// + |------------------------------------------------------------
// + | define application session directory
// + |
define("IGK_SESS_DIR", \$appdir."/sesstemp");
// + |------------------------------------------------------------
// + | define application projects directory
// + |
define("IGK_PROJECT_DIR", IGK_APP_DIR."/Projects");  
unset(\$appdir);
// + |------------------------------------------------------------
// + | include required framework 
// + |
@require_once(IGK_APP_DIR . '/Lib/igk/igk_framework.php');
// + |------------------------------------------------------------
// + | boot and run application 
// + |
IGKApplication::Boot('web')->run(__FILE__); 
EOF;

    return $s;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_getbool($v)
{
    if (is_bool($v))
        return $v;
    if (is_string($v)) {
        switch (strtolower($v)) {
            case "true":
            case "1":
                return true;
        }
        return false;
    }
    if ($v)
        return true;
    return false;
}
///<summary></summary>
/**
 * 
 */
function igk_getconfig_access()
{
    $root_dir = igk_uri(igk_io_rootbasedir());
    $root_dir = ltrim(igk_str_rm_last($root_dir, "/"), "/");
    $root_dir = !empty($root_dir) ? "\"/" . $root_dir : "\"";
    $error_404 = $root_dir . "/Lib/igk/igk_redirection.php?__c=404&m=config\"";
    $error_403 = $root_dir . "/Lib/igk/igk_redirection.php?__c=403&m=config\"";
    $out = <<<EIO
#SetEnv PHP_VER 5_4
Options -Indexes
ErrorDocument 404 {$error_404}
ErrorDocument 403 {$error_403}

#no mod rewrite
<IfModule rewrite_module>
RewriteEngine On
#redirect all to index.php
RewriteCond "%{REQUEST_FILENAME}" !-f
RewriteCond "%{REQUEST_FILENAME}" !-d
RewriteRule ^(/)?(.)*$  "index.php" [QSA,L]
</IfModule>
EIO;

    return $out;
}
///<summary>get the configuration web page controller. </summary>
/**
 * get the configuration web page controller. 
 */
function igk_getconfigwebpagectrl()
{
    $igk = igk_app();
    return igk_getctrl(IGK_CONF_CTRL);
}

///<summary></summary>
///<param name="className"></param>
/**
 * 
 * @param mixed $className 
 */
function igk_getctrl_from_classname($className)
{
    if (!IGKApp::IsInit()) {
        return null;
    }
    if (class_exists($className)) {
        return $className::ctrl();
    }
    return null;
}
///<summary></summary>
/**
 * 
 */
function igk_getctrls()
{
    return igk_app()->getControllerManager()->getControllers();
}
///<summary>get conditional value. is condition return $value or null</summary>
/**
 * get conditional value. is condition return $value or null
 */
function igk_getcv($condition, $value, $default = null)
{
    if ($condition)
        return $value;
    return $default;
}
///<summary></summary>
///<param name="v"></param>
///<param name="default" default="null"></param>
/**
 * 
 * @param mixed $v 
 * @param mixed $default 
 */
function igk_getdv($v, $default = null)
{
    if (isset($v))
        return $v;
    return $default;
}
///<summary>function igk_geterror_code</summary>
///<param name="code"></param>
/**
 * function igk_geterror_code
 * @param mixed $code 
 */
function igk_geterror_code($code)
{
    return igk_error($code)["Code"];
}
///<summary>get the value between value and default. if $value is empty or null default. default can be a callable expression</summary>
/**
 * get the value between value and default. if $value is empty or null default. default can be a callable expression
 */
function igk_getev($value, $default)
{
    if (($value == null) || empty($value)) {
        if (is_callable($default) && ($default instanceof Closure))
            return $default();
        return $default;
    }
    return $value;
}
///<summary>get uploaded file info</summary>
/**
 * get uploaded file info
 */
function igk_getf($file)
{
    if ($c = igk_getv($_FILES, $file)) {
        if (!isset($c["error"]) && is_array($c)) {
            $t = [];
            foreach ($c as $m) {
                if ($m["error"] == 0) {
                    $t[] = (object)$m;
                }
            }
            if (count($t) > 0) {
                return $t;
            }
            return null;
        }
        if ($c["error"] == 0) {
            return (object)$c;
        }
    }
    return null;
}
///<summary>get font name</summary>
/**
 * get font name
 */
function igk_getfn($f)
{
    return str_replace(" ", "-", $f);
}
///get object property value
/**
 */
function igk_getprop($obj, $prop)
{
    if ($obj == null)
        return;
    return $obj->$prop;
}
///<summary></summary>
///<param name="uri"></param>
/**
 * parse query args
 * @param string $uri 
 * @return array 
 */
function igk_getquery_args(?string $uri = null)
{
    $tab = array();
    if (!is_null($uri)) {
        $q = parse_url($uri);
        if (isset($q["query"])) {
            $uri = $q["query"];
        }
        parse_str($uri, $tab);
    }
    return $tab;
}
///<summary></summary>
///<param name="tab"></param>
///<param name="inTab" default="null"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $inTab 
 */
function igk_getr_k($tab, $inTab = null)
{
    $o = array();
    $inTab = $inTab ?? $_REQUEST;
    foreach ($tab as $k) {
        $o[$k] = igk_getv($inTab, $k);
    }
    return (object)$o;
}
///<summary></summary>
///<param name="values"></param>
///<param name="tab" default="null"></param>
/**
 * 
 * @param mixed $values 
 * @param mixed $tab 
 */
function igk_getr_kv($values, $tab = null)
{
    if ($tab == null)
        $tab = $_REQUEST;
    $o = [];
    foreach ($values as $k => $v) {
        $o[$k] = igk_getv($tab, $k, $v);
    }
    return $o;
}
///<summary>Represente igk_getre function</summary>
///<param name="n"></param>
///<param name="default" default="null"></param>
/**
 * Represente igk_getre function
 * @param mixed $n 
 * @param mixed $default 
 */
function igk_getre($n, $default = null)
{
    if (empty($b = igk_getr($n))) {
        $b = $default;
    }
    return $b;
}
///<summary>get filtered request values</summary>
///<exemple>igk_getrs("data", "file", "text") | igk_getrs([..])</exemple>
/**
 * get filtered request values
 */
function igk_getrs()
{
    if (($c = func_num_args()) > 0) {
        $tab = array();
        $tm = func_get_args();
        if (($c == 1) && is_array($m = func_get_arg(0))) {
            $tm = $m;
        }
        foreach ($m as $v) {
            $t = igk_getr($v);
            $tab[$v] = $t;
        }
        return $tab;
    }
    return $_REQUEST;
}
///<summary></summary>
/**
 * 
 */
function igk_getserverinfo()
{
    return array();
}
///<summary>get the setted-value</summary>
/**
 * get the setted-value
 */
function igk_getsv($value, $default = null)
{
    if (isset($value)) {
        return $value;
    }
    return $default;
}
///<summaryt>get path value</summary>
///<param name="d">object where to get value</param>
///<param name="n">path to value.</param>
///<param name="def">default value in case path not match</summary>
///<exemple>igk_gettsv($r, "info/index")</exemple>
///<test ref="../test/test_gettsv"></test>
/**
 * @param mixed $d object where to get value
 * @param mixed $n path to value.
 * @param mixed $def default value in case path not match
 */
function igk_gettsv($d, $n, $def = null)
{
    return igk_conf_get($d, $n, $def);
}
///<summary>Represente igk_getv_fallback function</summary>
///<param name="item"></param>
///<param name="keys"></param>
/**
 * Represente igk_getv_fallback function
 * @param mixed $item 
 * @param mixed $keys 
 */
function igk_getv_fallback($item, $keys)
{
    if (is_string($keys)) {
        $keys = explode("|", $keys);
    }
    foreach ($keys as $r) {
        if (!($m = igk_getv($item, $r))) {
            return $m;
        }
    }
    return null;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_globalvars($n)
{
    $s = igk_app()->session->getParam(IGKSession::GLOBALVARS);
    return igk_getv($s, $n);
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_globalvars_isset($n)
{
    $s = igk_app()->session->getParam(IGKSession::GLOBALVARS);
    if (($s == null) || !isset($s[$n]))
        return false;
    return true;
}
///<summary>Represente igk_glue function</summary>
///<param name="glue">glue all values</param>
/**
 * helper : glue all values
 * @param string $glue glue string
 * @param array $params array list to glue
 */
function igk_glue(string $glue, ...$params)
{
    if (func_num_args() > 1) {
        return call_user_func_array("implode", array($glue, $params));
    }
}
///<summary></summary>
/**
 * 
 */
function igk_handle_cmd_line()
{
    $tab = array_slice(igk_getv($_SERVER, "argv"), 1);
    $n = igk_getv($tab, 0);
    if ($n && ($g = igk_getv(igk_get_env("sys://cmd/args"), $n))) {
        call_user_func_array($g->callback, array_slice($tab, 1));
        return true;
    }
    return false;
}
///<summary>handle component uri</summary>
/**
 * handle component uri
 */
function igk_handle_component_uri()
{
    $uri = trim(igk_getv(explode('?', igk_io_request_uri()), 1));
    if (!empty($uri) && ($uri[0] == "!")) {
        $k = "^!/:id(/|(/:function(/|/:params+)?)?)";
        $s = igk_pattern_matcher_get_pattern($k);
        if (preg_match($s, $uri)) {
            $keys = igk_str_get_pattern_keys($k);
            $s = igk_pattern_get_matches($s, $uri, $keys);
            extract($s);
            $doc = igk_get_last_rendered_document();
            $comp = $doc->getComponent($id);
            $c = igk_getv($comp->getParam(IGK_COMPONENT_REG_FUNC_KEY), $function);
            if (is_callable($c)) {
                $t = array($comp);
                if (is_string($params) && (strlen($params = trim($params)) > 0))
                    $params = array($params);
                if ($params)
                    $t = array_merge($t, $params);
                if (is_array($c))
                    call_user_func_array($c, $t);
                else
                    call_user_func_array($c, $t);
            }
            return true;
        }
    }
    return false;
}
///<summary> used to handle a view command with function list</summary>
/**
 *  used to handle a view command with function list
 */
function igk_handle_view_cmd($params, $fclist)
{
    include(IGK_LIB_DIR . "/Inc/igk_fc_call.pinc");
    return $fc_result;
}
///<summary>set content type from file</summary>
///<param name="file"></param>
/**
 * set content type from file
 * @param mixed $file 
 */
function igk_header_content_file($file)
{
    igk_header_set_contenttype(strtolower(igk_io_path_ext($file)));
}
///<summary>return mime type</summary>
/**
 * return mime type
 */
function igk_header_mime()
{
    $is_chrome = IGKUserAgent::IsChrome();
    if ($is_chrome && !isset($_SERVER["HTTP_REFERER"])) {
        $is_chrome = 0;
    }
    $mime = igk_get_env("sys://mimetype") ?? array(
        "svg" => "image/svg+xml",
        "xml" => $is_chrome ? "text/html" : "application/xml",
        "pdf" => "application/pdf",
        "json" => (igk_check_ie_version() || $is_chrome) ? "text/html" : "application/json",
        "png" => "image/png",
        "jpeg" => "image/jpeg",
        "jpg" => "image/jpeg",
        "ico" => "image/png",
        "html" => "text/html",
        "txt" => IGK_CT_PLAIN_TEXT,
        "js" => "text/javascript",
        "css" => "text/css",
        "ics" => "text/calendar",
        "woff" => "application/font-woff",
        "woff2" => "application/font-woff2",
        "ttf" => "application/x-font-ttf",
        "vue" => "text/javascript",
        "xsl" => igk_check_ie_version(12) ? IGK_CT_PLAIN_TEXT : "text/xsl"
    );
    return $mime;
}
///<summary></summary>
/**
 * 
 */
function igk_header_no_cache()
{
    if (headers_sent()) {
        return;
    }
    $t = "Thu, 04 Aug 1983 21:00:00 GMT";
    header("Expires: " . $t);
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Last-Modified: " . $t);
    header("Connection: close");
    // + | make safari to no cache file
    header("Vary: *");
}
///<summary></summary>
///<param name="type"></param>
///<param name="charset" default="charset=utf-8"></param>
/**
 * 
 * @param mixed $type 
 * @param mixed $charset 
 */
function igk_header_set_contenttype($type, $charset = "charset=utf-8")
{
    $data = "";
    $mime = igk_header_mime();
    if ($charset)
        $charset = ";" . $charset;
    $data = igk_getv($mime, $type, IGK_CT_PLAIN_TEXT) . $charset;
    header("Content-Type: " . $data);
}
///<summary>convert header string to associative array</summary>
/**
 * convert header string to associative array
 */
function igk_header_str2array($s)
{
    $tab = array();
    $ot = array();
    $n = "";
    $tab = explode("\r\n", $s);
    foreach ($tab as $line) {
        if (empty(trim($line)))
            continue;
        $ton = explode(":", $line);
        $on = trim($ton[0]);
        if (!empty($on) && preg_match("/^[a-z-]+$/i", trim($on))) {
            $n = $on;
            $v = trim(substr($line, strpos($line, ":") + 1));
        } else {
            $v = $ot[$n] . "\r\n" . $line;
        }
        $ot[$n] = $v;
    }
    return $ot;
}
///<summary></summary>
///<param name="target"></param>
///<param name="hrefuri"></param>
///<param name="clickuri" default="null"></param>
/**
 * 
 * @param mixed $target 
 * @param mixed $hrefuri 
 * @param mixed $clickuri 
 */
function igk_html_a_link($target, $hrefuri, $clickuri = null)
{
    $a = $target->add("a");
    if ($a) {
        $a["href"] = $hrefuri;
        if ($clickuri)
            $a["onclick"] = "javascript:" . $clickuri . " return false;";
    }
    return $a;
}
///<summary></summary>
///<param name="item"></param>
///<param name="target"></param>
///<param name="index" default="null"></param>
/**
 * 
 * @param mixed $item 
 * @param mixed $target 
 * @param mixed $index 
 */
function igk_html_add($item, $target, $index = null)
{
    return $item->add($target, null, $index);
}
///<summary></summary>
///<param name="doc"></param>
///<param name="file"></param>
///<param name="temp"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $file 
 * @param mixed $temp 
 */
function igk_html_add_balafonjsscriptfile($doc, $file, $temp = 0)
{
    $g = $doc->getParam("sys://igk/tempbalafonjs", array());
    $host = $doc->getParam("sys://igk/tempbalafonjs/node", function () use ($doc) {
        $b = igk_create_notagnode();
        $doc->add($b);
        $doc->setParam("sys://igk/tempbalafonjs/node", $b);
        return $b;
    });
    $b = null;
    if (isset($g[$file])) {
        $b = $g[$file];
    } else if (file_exists($file)) {
        $b = igk_create_node("balafonJS");
        if ($temp) {
            $host->add(new HtmlSingleNodeViewerNode($b, igk_create_func_callback("igk_rm_balafonscriptfile_callback", array("file" => $file, "doc" => $doc))));
        } else {
            $host->add($b);
        }
        $g[$file] = $b;
    }
    if ($b) {
        $b->Content = igk_io_read_allfile($file);
    }
    $doc->setParam("sys://igk/tempbalafonjs", $g);
}
///<summary>utility add confirm input</summary>
/**
 * utility add confirm input
 */
function igk_html_add_confirm($frm)
{
    $frm->addInput("confirm", "hidden", 1);
}
///<summary></summary>
///<param name="doc"></param>
///<param name="file"></param>
///<param name="temp"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $file 
 * @param mixed $temp 
 */
function igk_html_add_doc_script($doc, $file, $temp = 0)
{
    $g = $doc->getParam("sys://igk/tempbalafonjs", array());
    $host = $doc->getParam("sys://igk/tempbalafonjs/node", function () use ($doc) {
        $b = igk_create_notagnode();
        $doc->head->add($b);
        $doc->setParam("sys://igk/tempbalafonjs/node", $b);
        return $b;
    });
    $b = null;
    if (isset($g[$file])) {
        $b = $g[$file];
    } else if (file_exists($file)) {
        $b = igk_create_node("script");
        if ($temp) {
            $host->add(new HtmlSingleNodeViewerNode($b, igk_create_func_callback("igk_rm_balafonscriptfile_callback", array("file" => $file, "doc" => $doc))));
        } else {
            $host->add($b);
        }
        $g[$file] = $b;
    }
    if ($b) {
        $b->Content = igk_io_read_allfile($file);
    }
    $doc->setParam("sys://igk/tempbalafonjs", $g);
}
///<summary>html utility . get attributes string presentation</summary>
/**
 * html utility . get attributes string presentation
 */
function igk_html_array_attrs($tab)
{
    $s = "";
    foreach ($tab as $k => $v) {
        $kv = HtmlRenderer::GetStringAttribute($v, null);
        if ($kv) {
            $s .= " " . $k . "=" . $kv . "";
        }
    }
    return $s;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="name"></param>
///<param name="target"></param>
///<param name="data" default="null"></param>
///<param name="tagname" default="null"></param>
///<param name="forcecreation" default="true"></param>
///<param name="evalExpression" default="true"></param>
///<param name="articleoptions" default="true"></param>
/**
 * 
 * @param BaseController $ctrl 
 * @param mixed $name 
 * @param mixed $target 
 * @param mixed $data 
 * @param mixed $tagname 
 * @param mixed $forcecreation 
 * @param mixed $evalExpression 
 * @param mixed $articleoptions 
 */
function igk_html_article_bck(BaseController $ctrl, $name, $target, $data = null, $tagname = null, $forcecreation = true, $evalExpression = true, $articleoptions = true)
{
    $f = $name;
    $n = null;
    $d = dirname($name);
    if (!file_exists($f)) {
        if (!empty($d) && ($d != ".") && is_dir($d) || (strpos($d, igk_io_basedir()) === 0)) {
            $f = $ctrl->getArticleInDir(basename($name), $d);
            if (!is_dir($d) && !IO::CreateDir($d)) {
                igk_ilog(__FUNCTION__ . " create directory [{$d}] failed.");
                return;
            }
        } else {
            if (!file_exists($name) && ($ctrl != null))
                $f = $ctrl->getArticle($name);
            else
                return;
        }
    }
    if ($forcecreation && !file_exists($f)) {
        igk_io_save_file_as_utf8_wbom($f, IGK_STR_EMPTY, true);
    }
    if (($target == null) && ($tagname == null)) {
        return;
    }
    if ($tagname == null) {
        $n = $target;
    } else {
        $n = $target->add($tagname);
    }
    if ($n == null)
        igk_die(__FUNCTION__ . "::target is null");
    if (is_file($f) && !empty($content = igk_io_read_allfile($f))) {
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_push_article_chain($f, $ldcontext);
        igk_html_bind_article_content($n, $content, $data, $ctrl, basename($f), true, $ldcontext);
        if ($articleoptions) {
            igk_html_article_options($ctrl, $n, $f);
        }
        igk_pop_article_chain();
        $n->setFlag("NO_CHILD", 1);
    }
    return $n;
}

/**
 * load article 
 */
function igk_html_article(BaseController $ctrl, $name, $target, ?array $data = null, $tagname = null, $forcecreation = true, $evalExpression = true, $articleoptions = true)
{
    $f = $name;
    $n = null;
    $d = dirname($name);
    if (!file_exists($f)) {
        if (!empty($d) && ($d != ".") && is_dir($d) || (strpos($d, igk_io_basedir()) === 0)) {
            $f = $ctrl->getArticleInDir(basename($name), $d);
            if (!is_dir($d) && !IO::CreateDir($d)) {
                igk_ilog(__FUNCTION__ . " create directory [{$d}] failed.");
                return;
            }
        } else {
            if (!file_exists($name) && ($ctrl != null))
                $f = $ctrl->getArticle($name);
            else
                return;
        }
    }
    if ($forcecreation && !file_exists($f)) {
        igk_io_save_file_as_utf8_wbom($f, IGK_STR_EMPTY, true);
    }
    if (($target == null) && ($tagname == null)) {
        return;
    }
    if ($tagname == null) {
        $n = $target;
    } else {
        $n = $target->add($tagname);
    }

    if (igk_environment()->caching_result) {
        $tn = new \IGK\System\Html\Dom\HtmlBindingArticleNode;
        $tn->file = $f;
        $tn->data = $data;
        $tn->ctrl = $ctrl;
        $tn->target = $n;
        $tn->caching = true;
        $n->add($tn);
        return $tn;
    }
    $ldcontext = igk_createloading_context($ctrl, $data);
    $ldcontext->engineNode = $n;
    if (!is_dir($f) && file_exists($f) && ($content = igk_io_read_allfile($f))) {
        igk_html_article_bind_content(
            $n,
            $content,
            $evalExpression,
            $ldcontext,
            $f,
            $ctrl,
            $data,
            $articleoptions
        );
    }
    $n->setFlag("NO_CHILD", 1);
    return $n;
}

/**
 * bind article content
 */
function igk_html_article_bind_content(
    HtmlNode $n,
    string $content,
    bool $evalExpression,
    ?HtmlLoadingContextOptions $ldcontext,
    string $f,
    BaseController $ctrl,
    $data,
    $articleoptions = false
) {
    $c = HtmlLoadingContext::GetCurrentContext();
    if ($c && !$c->load_expression)
        $ldcontext->load_expression = false;
    igk_push_article_chain($f, $ldcontext);

    try {
        if ($evalExpression) {
            $content = igk_html_eval_global_script($content, $ctrl, $data, basename($f));
        }
        if (!empty($content)) {
            $n->load($content, $ldcontext);
        }
        igk_html_treatinput($n);
        if ($articleoptions) {
            igk_html_article_options($ctrl, $n, $f);
        }
    } catch (\Exception $ex) {
        throw $ex;
    } finally {
        igk_pop_article_chain();
    }
}


/**
 * init loading context 
 * @var mixed
 */
function igk_init_binding_context(HtmlItemBase $n, BaseController $ctrl, ?array $raw)
{
    $ldcontext = igk_createloading_context($ctrl, $raw);
    $ldcontext->engineNode = $n;
    $c = HtmlLoadingContext::GetCurrentContext();
    if ($c && !$c->load_expression)
        $ldcontext->load_expression = false;
    return $ldcontext;
}

function igk_html_bind_article_content(HtmlNode $n, string $content, $data, $ctrl, $id, $evalExpression = true, $ldcontext = null)
{
    if ($evalExpression) {
        $content = igk_html_eval_global_script($content, $ctrl, $data, $id);
    }
    if (!empty($content)) {
        //evaluate content :
        $n->load($content, $ldcontext);
    }
    igk_html_treatinput($n);
}
///<summary>add articles options</summary>
/**
 * add articles options
 */
function igk_html_article_options($ctrl, $node, $filename, $force = 0)
{
    $app = igk_app();
    if (!$force && !($app->Configs->allow_article_config && $app->ConfigMode && ($node != null))) {
        return;
    }
    $c = new IGK\System\Html\Dom\HtmlArticleConfigNode($ctrl, $node, $filename, $force);
    $c->Index = -1000;
    return $c;
}
///<summary></summary>
///<param name="value"></param>
/**
 * 
 * @param mixed $value 
 */
function igk_html_beginbinding($value)
{
    igk_die("obselete :" . __FUNCTION__);
}
///<summary>bind content</summary>
///<remark></remark>
///<param name="ctrl">source controller.can be null</param>
///<param name="content">the content to evaluate</param>
///<param name="raw">row data to pass</param>
/**
 * bind content
 * @param mixed $ctrl source controller.can be null
 * @param mixed $content the content to evaluate
 * @param mixed $raw row data to pass
 */
function igk_html_bind_content($ctrl, $content, $raw = null)
{
    $t = igk_create_node("div");
    igk_html_bind_target($ctrl, $t, $content, $raw);
    return $t->getInnerHtml();
}
///<summary>bind node to data model</summary>
///<param name="ctrl">the controller</param>
///<param name="model">the node model to bind</param>
///<param name="targetnode">cibing node</param>
/**
 * bind node to data model
 * @param mixed $ctrl the controller
 * @param mixed $model the node model to bind
 * @param mixed $targetnode cibing node
 */
function igk_html_bind_node($ctrl, $model, $targetnode, $entries = null, $rendertarget = true, &$bindchild = false)
{
    $o = array();
    if ($model && $model->HasChilds) {
        $c = $model;
        if ($c == null)
            return;
        $tabinfo = 0;
        $v_info_count = 0;
        $tabinfo = igk_html_bindinginfo($c);
        $v_info_count = igk_count($tabinfo);
        if (($v_info_count > 0) && $entries) {
            igk_html_bindentry($ctrl, $entries, $tabinfo, $o, $c);
            $bindchild = true;
        } else {
            $i = $c->getInnerHtml();
            if (is_array($entries)) {
                igk_set_env("sys://html/bindentries", 1);
                foreach ($entries as $k => $v) {
                    igk_set_env("sys://html/bindkey", $k);
                    $o[] = (object)array(
                        "src" => igk_html_treat_content(
                            $i,
                            $ctrl,
                            $v
                        )->render(),
                        "raw" => $v,
                        "key" => $k
                    );
                }
                igk_set_env("sys://html/bindentries", null);
                igk_set_env("sys://html/bindkey", null);
            } else {
                $o[] = (object)array(
                    "src" => igk_html_treat_content(
                        $i,
                        $ctrl,
                        $entries
                    )->render(),
                    "raw" => $entries,
                    "key" => 0
                );
            }
        }
    }
    if ($targetnode == null)
        $targetnode = $c;
    $ctx = igk_createobj(array(
        "ctrl" => $ctrl,
        "entries" => $entries,
        "parent" => $targetnode,
        "raw" => $entries,
        "key" => null
    ));
    foreach ($o as $k => $v) {
        if (!is_object($v)) {
            $b = $targetnode->Load($v, $ctx);
            continue;
        }
        $ctx->row = $v->row;
        $ctx->key = $v->key;
        $b = $targetnode->Load($v->src, $ctx);
    }
    if ($rendertarget) {
        return $targetnode->render();
    }
    return IGK_STR_EMPTY;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="targetnode"></param>
///<param name="textcontent"></param>
///<param name="entries" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $targetnode 
 * @param mixed $textcontent 
 * @param mixed $entries 
 */
function igk_html_bind_target($ctrl, $targetnode, $textcontent, $entries = null)
{
    $d = igk_create_node("div");
    $d->LoadExpression($textcontent);
    return igk_html_bind_node($ctrl, $d, $targetnode, $entries);
}
///<summary>html bind data to target node</summary>
///<param name="ctrl">the controller</param>
///<param name="targetnode">cibling node</param>
///<param name="templateArticleName">template or article for bindding</param>
///<param name="entries">data entries</param>
///<param name="rendertarget">render target node</param>
///<param name="createIfNoExists" default="true" >create template view if not exists</param>
///<param name="showAdminOption" default="true" >attach administration options</param>
/**
 * html bind data to target node
 * @param mixed $ctrl the controller
 * @param mixed $targetnode cibling node
 * @param mixed $templateArticleName template or article for bindding
 * @param mixed $entries data entries
 * @param mixed $rendertarget render target node
 * @param mixed $createIfNoExists create template view if not exists
 * @param mixed $showAdminOption attach administration options
 */
function igk_html_binddata($ctrl, $targetnode, $templateArticleName, $entries = null, $rendertarget = true, $createIfNoExists = true, $showAdminOption = 1)
{
    $d = igk_create_node("div");
    if (!file_exists($templateArticleName)) {
        if ($ctrl)
            $templateArticleName = $ctrl->getArticle($templateArticleName);
        if (!file_exists($templateArticleName)) {
            if (!$createIfNoExists)
                return;
            IO::WriteToFileAsUTF8WBOM($templateArticleName, "", true);
        }
    }
    $d->LoadExpression(igk_html_initbindexpression(IO::ReadAllText($templateArticleName)), null);
    $n = igk_html_bind_node($ctrl, $d, $targetnode, $entries, $rendertarget);
    if ($showAdminOption)
        $s = igk_html_article_options($ctrl, $targetnode, $templateArticleName);
    return $n;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="entries"></param>
///<param name="tabinfo"></param>
///<param name="o" ref="true"></param>
///<param name="$c" ref="true"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $entries 
 * @param mixed $tabinfo 
 * @param mixed $o 
 * @param mixed $$c 
 */
function igk_html_bindentry($ctrl, $entries, $tabinfo, &$o, &$c)
{
    $r = igk_getv($entries, "Rows");
    if ($r != null) {
        foreach ($entries->Rows as $keys => $k) {
            igk_html_bindsetup($ctrl, $tabinfo, $k, $keys);
        }
    } else {
        if (is_array($entries)) {
            foreach ($entries as $keys => $k) {
                igk_html_bindsetup($ctrl, $tabinfo, $k, $keys);
            }
        } else {
            igk_html_bindsetup($ctrl, $tabinfo, $entries);
        }
    }
    $s = $c->getinnerHtml(null);
    $o[] = $s;
    $c->clearChilds();
}
///<summary>return array of data binding node info</summary>
/**
 * return array of data binding node info
 */
function igk_html_bindinginfo($node)
{
    $tab = igk_html_getallchilds($node);
    $c = array();
    $visiblerows = igk_configs()->get("app_visible_row", 50);
    foreach ($node->Childs as $k) {
        if (($h = igk_getv($k, "igk-data-binding")) != null) {
            $c[] = (object)array(
                "node" => $k,
                "binding" => $h,
                "type" => "igk-data-binding",
                "visiblerow" => igk_getv(
                    $k,
                    "igk-data-binding-visible-row",
                    $visiblerows
                )
            );
        } else if (igk_getbool(($h = igk_getv($k, "igk-data-row-binding"))) == true) {
            $p = $k->getParentNode();
            $c[] = (object)array(
                "node" => $k,
                "parent" => $p,
                "Index" => $k->Index,
                "binding" => $h,
                "data" => igk_getv(
                    $k,
                    "igk-data-row-data",
                    null
                ),
                "type" => "igk-data-row-binding",
                "visiblerow" => igk_getv(
                    $k,
                    "igk-data-binding-visible-row",
                    $visiblerows
                ),
                "rowcheckExpression" => igk_getv(
                    $k,
                    "igk-data-row-checkexpression",
                    null
                ),
                "key" => 0
            );
            igk_html_unset_template_properties($k);
            igk_html_rm($k);
            return $c;
        } else if (igk_getbool(($h = igk_getv($k, "igk-data-full-row-binding"))) == true) {
            $c[] = (object)array(
                "node" => $k,
                "parent" => $k->getParentNode(),
                "binding" => $h,
                "type" => "igk-data-full-row-binding",
                "tag" => igk_getv(
                    $k,
                    "igk-data-full-row-binding-tag"
                ),
                "visiblerow" => igk_getv(
                    $k,
                    "igk-data-binding-visible-row",
                    igk_configs()->get("app_visible_row", 50)
                )
            );
            igk_html_rm($k);
        }
        igk_html_unset_template_properties($k);
        if ($k->HasChilds) {
            $c = array_merge(igk_html_bindinginfo($k));
        }
    }
    return $c;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="tabinfo"></param>
///<param name="row"></param>
///<param name="keys"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $tabinfo 
 * @param mixed $row 
 * @param mixed $keys 
 */
function igk_html_bindsetup($ctrl, $tabinfo, $row, $keys = 0)
{
    foreach ($tabinfo as $info) {
        $info->key = $keys;
        igk_html_treatbinding($info, $row, $ctrl);
    }
}
///<summary>utility used to build a form</summary>
///<exemple>
///igk_html_build_form_array($node, [IGK_FD_NAME=>["type"=>]]
///</exemple>
/**
 * utility used to build a form
 */
function igk_html_build_form_array($ul, $param, $targettagname = "li")
{
    foreach ($param as $k) {
        $id = $k[0];
        $type = strtolower($k[1]);
        $required = igk_getv($k, 2);
        $args = igk_getv($k, 3);
        $li = $ul->add($targettagname);
        $a = null;
        $lb = $li->add("label", array("for" => $id));
        $lb->Content = __("lb." . $id);
        if ($required) {
            $lb->setClass("clrequired");
        }
        switch ($type) {
            case "textarea":
                $a = $li->addTextArea($id);
                $a->Content = $args;
                break;
            case "radio":
            case "checkbox":
                $a = $li->addInput($id, $type);
                if (igk_getr($id)) {
                    $a["checked"] = "true";
                }
                break;
            case "hidden":
            case "text":
            case "password":
            default:
                $a = $li->addInput($id, $type);
                $a["type"] = strtolower($type);
                $a["value"] = $args ? igk_getv($args, "value", igk_getr($id)) : igk_getr($id);
                break;
        }
        $a["id"] =
            $a["name"] = $id;
        $args = igk_getv($k, 3);
        if ($args != null) {
            $a->setAttributes($args);
        }
    }
}
///<summary></summary>
///<param name="queryresult"></param>
/**
 * 
 * @param mixed $queryresult 
 */
function igk_html_build_query_result_table($queryresult)
{
    $table = igk_create_node("table");
    if (!is_bool($queryresult))
        igk_html_db_build_table($table, $queryresult);
    return $table;
}
///<summary>build select options with array</summary>
///<note>data is array of object with (text | select) property .
///selected will be used to set the selected option. if not null
///</note>
/**
 * build select options with array
 */
function igk_html_build_select_array($sl, $data, $selected = null)
{
    foreach ($data as $k => $v) {
        $opt = $sl->add("option");
        $opt["value"] = $k;
        $opt->Content = $v->text;
        if (($selected == $k) || (($selected == null) && igk_getv($v, "select")))
            $opt->setAttribute("selected", "true");
    }
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="target"></param>
///<param name="name"></param>
///<param name="option"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $target 
 * @param mixed $name 
 * @param mixed $option 
 */
function igk_html_build_select_model($ctrl, $target, $name, $option)
{
    igk_html_binddata($ctrl, $target, $name, $option);
}
///<summary>build select options</summary>
///<param name="target">select node</param>
///<note>
///in selectedattributes[] displaykey: pipe expression
///</note>
/**
 * build select options
 * @param mixed $target select node
 */
function igk_html_build_select_option($target, $tab, $selectattributes = null, $selectedvalue = null)
{
    $a = $selectattributes;
    if (igk_getv($a, "allowEmpty"))
        $target->add("option", array(
            "value" => igk_getsv(igk_getv(
                $a,
                "emptyValue",
                IGK_STR_EMPTY
            ))
        ))->Content = IGK_HTML_SPACE;
    $kv = igk_getsv(igk_getv($a, "valuekey"));
    $dv = igk_getsv(igk_getv($a, "displaykey"));
    $piping = null;
    if ($dv) {
        $_p = array_values(array_filter(explode("|", $dv)));
        if (count($_p) > 1) {
            $dv = $_p[0];
            $piping = implode('|', array_slice($_p, 1));
        }
    }
    $resolvtext = igk_getv($a, "resolvtext");
    $selectedv = igk_getv($a, "selected", $selectedvalue);
    if (is_array($tab)) {
        foreach ($tab as $k => $v) {
            $opt = $target->add("option");
            $_v = igk_getv($v, $kv, $k);
            $opt["value"] = $_v;
            if ($resolvtext)
                $opt->Content = __("enum." . igk_getv($v, $dv, $v));
            else
                $opt->Content = ($_s = igk_getv($v, $dv, $v)) && $piping ? igk_str_pipe_value($_s, $piping) : $_s;
            if ($_v == $selectedv) {
                $opt["selected"] = true;
            }
        }
    }
}
///<summary>utility render mixed value data</summary>
/**
 * utility render mixed value data
 */
function igk_html_buildview($mix, $target = "div", $item = "li")
{
    $t = igk_create_node($target);
    if (is_object($mix)) {
        foreach ($mix as $k => $v) {
            $t->add($item)->setClass("{$k}")->Content = $v;
        }
    } else if (is_array($mix)) {
        foreach ($mix as $k => $v) {
            $t->add($item)->setClass("i i-{$k}")->Content = $v;
        }
    } else {
        $t->Content = $mix;
    }
    return $t;
}
///<summary>is webmaster callback</summary>
/**
 * is webmaster callback
 */
function igk_html_callback_is_webmaster()
{
    return IGKViewMode::IsSupportViewMode(IGKViewMode::WEBMASTER);
}
///<summary></summary>
///<param name="a"></param>
/**
 * 
 * @param mixed $a 
 */
function igk_html_callback_production_minifycontent($a)
{
    $s = $a->Content;
    if (igk_sys_env_production()) {
        if (igk_is_callback_obj($s)) {
            $s = igk_invoke_callback_obj($a, $s);
        } else if (!is_string($s) && method_exists($s, IGK_GET_VALUE_METHOD)) {
            $s = $s->getValue();
        }
        return igk_js_minify($s);
    }
    return $s;
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_html_clearbindinfo($ctrl)
{
    $key = IGK_DATABINDING_RESPONSE_NAME;
    $obj = $ctrl->getEnvParam($key);
    if ($obj != null) {
        unset($obj);
        $ctrl->setEnvParam($key, null);
    }
}
///<summary></summary>
///<param name="node"></param>
/**
 * 
 * @param mixed $node 
 */
function igk_html_clonenode($node)
{
    $g = HtmlReader::Load($node->render());
    return $g ? igk_getv($g->Childs, 0) : null;
}
///<summary>utility function to create tab selection</summary>
/**
 * utility function to create tab selection
 */
function igk_html_create_db_tab_select($tag, $ctrl, $id, $table)
{
    $sl = igk_create_node($tag);
    $sl->addLabel($id);
    $sl->addCtrlSelect()->setId($id)->setCtrl($ctrl)->setTable($table)->loadingComplete();
    return $sl;
}
///<summary>utility function. create html menu node</summary>
/**
 * utility function. create html menu node
 */
function igk_html_createmenu($name, $uri)
{
    return (object)array(
        IGK_FD_ID => igk_new_id(),
        IGK_FD_NAME => $name,
        "clUri" => $uri
    );
}
///<summary>used to create submenu item</summary>
/**
 * used to create submenu item
 */
function igk_html_createmenui($uri, $submenu)
{
    return new MenuItemObject($uri, $submenu);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="target"></param>
///<param name="titlekey"></param>
///<param name="descfile" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $target 
 * @param mixed $titlekey 
 * @param mixed $descfile 
 */
function igk_html_ctrl_view_config($ctrl, $target, $titlekey, $descfile = null)
{
    $box = $target->addPanelBox();
    igk_html_add_title($box, $titlekey);
    if ($descfile && file_exists($f = $ctrl->getArticle($descfile))) {
        igk_html_article($ctrl, $f, $box->div());
    }
    return $box;
}
///<summary>get registrated for object's scripting</summary>
/**
 * get registrated for object's scripting
 */
function &igk_html_databinding_getobjforscripting($ctrl)
{
    $name = IGK_DATABINDING_RESPONSE_NAME;
    $obj = null;
    if ($ctrl == null)
        return $obj;
    if ($ctrl instanceof BaseController) {
        $obj = $ctrl->getEnvParam($name);
        if ($obj == null) {
            $obj = new IGKDataBindingScript();
            $ctrl->setEnvParam($name, $obj);
        }
    }
    return $obj;
}
///<summary></summary>
///<param name="obj" ref="true"></param>
///<param name="value"></param>
///<param name="ctrl"></param>
///<param name="row"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $value 
 * @param mixed $ctrl 
 * @param mixed $row 
 */
function igk_html_databinding_read_obj_litteral(&$obj, $value, $ctrl, $row)
{
    $pos = 0;
    $obj->args = array();
    $ln = strlen($value);
    $tab = array();
    $_NS = "_abcdefghijklmnopqrstuvwxyz123456789";
    $_mode = 0;
    $_v = "";
    $_n = "";
    while ($pos < $ln) {
        $ch = $value[$pos];
        $pos++;
        if ($_mode == 0) {
            if (strpos($_NS, strtolower($ch)) === false) {
                $_v = trim($_v);
                if (!empty($_v)) {
                    if ($_mode == 0) {
                        $tab[$_v] = "";
                        $_mode = 1;
                        $_n = $_v;
                    } else {
                        $_mode = 0;
                        $tab[$_n] = $_v;
                    }
                    $_v = "";
                }
                continue;
            }
        } else {
            $depth = array();
            $dp = 0;
            $pos--;
            while ($pos < $ln) {
                $ch = $value[$pos];
                $r = 0;
                switch ($ch) {
                    case '{':
                    case '(':
                    case '[':
                        isset($depth[$ch]) ? $depth[$ch]++ :
                            $depth[$ch] = 1;
                        $dp++;
                        break;
                    case '}':
                        $depth['{']--;
                        $dp--;
                        break;
                    case ')':
                        $depth['(']--;
                        $dp--;
                        break;
                    case ']':
                        $depth['[']--;
                        $dp--;
                        break;
                    case "'":
                    case "\"":
                        $g = igk_str_read_brank($value, $pos, $ch, $ch);
                        $_v .= $g;
                        $pos++;
                        $r = 1;
                        break;
                    case ',':
                        if ($dp <= 0) {
                            $tab[$_n] = $_v;
                            $_v = "";
                            $_mode = 0;
                            $pos++;
                            break 2;
                        }
                        break;
                }
                if (!$r) {
                    $_v .= $ch;
                }
                $pos++;
            }
            $pos++;
            continue;
        }
        $_v .= $ch;
    }
    if (!empty($_v)) {
        $tab[$_n] = $_v;
    }
    foreach ($tab as $k => $v) {
        $obj->args[$k] = $v;
    }
}
///<summary>treat string data binding response before eval</summary>
///<param name="rep">reponse</param>
///<param name="$ctrl">controller</param>
///<param name="$row">row data to pass</param>
///<param name="$ctx" type="mixed" >extra context data. </param>
/**
 * treat string data binding response before eval
 * @param mixed $rep response
 * @param mixed $$ctrl controller
 * @param mixed $$row row data to pass
 * @param mixed $$ctx extra context data. 
 */
function igk_html_databinding_treatresponse($rep, $ctrl, $raw, $ctx = null, $a = 0, $transformEval = false)
{
    if (!is_string($rep)) {
        igk_die("operation not allowed. \$rep is not a string");
    }
    igk_wln(__FILE__ . ":" . __LINE__,  "treating....", $rep);

    // TODO : replace with \IGK\System\Html\Templates\BindingExpressionInfo 
    $script_obj = null;
    $global_scope = 1;
    if (is_array($ctx) && isset($ctx["scope"])) {
        $global_scope = $ctx["scope"];
        $script_obj = igk_html_databinding_getobjforscripting($ctrl);
    }
    if (is_array($raw) && key_exists("raw", $raw)) {
        $raw = array_merge($raw, ["raw" => $raw["raw"]]);
    }
    $regexpression = IGK_TEMPLATE_EXPRESSION_REGEX;
    $reg_comment = '/(?P<comment>(\<\!--(?P<value>(.)+)--\>))/i';
    $comment = array();
    if (preg_match($reg_comment, $rep)) {
        if (($c = preg_match_all($reg_comment, $rep, $match))) {
            for ($i = 0; $i < $c; $i++) {
                $value = $match['comment'][$i];
                $rep = str_replace($value, "__COMMENT__" . $i . "__", $rep);
                $comment["__COMMENT__" . $i .
                    "__"] = $value;
            }
        }
    }
    $tmatch = array();
    $match = array();
    $c = 0;
    $v = $rep;
    $c = preg_match_all($regexpression, $rep, $match, PREG_OFFSET_CAPTURE, 0);
    if ($c) {
        $tmatch[] = $match;
    }
    $offset = 0;
    foreach ($tmatch as $match) {
        $c = igk_count($match[0]);
        if (isset($match["expression"])) {
            if (!empty($match["escape"][0][0])) {
                $ln = strlen($match["escape"][0][0]);
                $offset = $match["escape"][0][1];
                $v = $offset == 0 ? substr($v, $ln) : $v;
                continue;
            }
            $express_data = [];
            for ($i = 0; $i < $c; $i++) {
                $v_m = $match[0][$i];
                if ($v_m[0][0] == "@") {
                    igk_die("context expression not allowed", "core", $rep);
                }
                $value = $match['expression'][$i];
                if (!isset($express_data[$value[0]])) {
                    $rm = $value[0];
                    $args = [];
                    if ($script_obj)
                        $args = $script_obj->args;
                    $m = igk_html_php_eval($args, $ctrl, $raw, $rm, $a);
                    if ($m && !is_string(($m))) {
                        if (is_array($m)) {
                            if ($transformEval) {
                                $m = "<?= \$raw ?>";
                            } else {
                                $m = "[array_expression]";
                            }
                        } else if (is_object($m)) {
                            if (method_exists($m, "__toString"))
                                $m = (string)$m; // get_class($m) . "]";
                            else {
                                $m = "[object: " . get_class($m) . "]";
                            }
                        } else if (is_numeric($m)) {
                            $m = "" . $m;
                        }
                    }
                    $express_data[$value[0]] = $m;
                } else {
                    $m = $express_data[$value[0]];
                }
                $y = $v_m[1] - $offset;
                $g = strlen($v_m[0]);
                $v = substr($v, 0, $y) . $m . substr($v, $y + $g);
                if (!is_string($m)) {
                    $m = "";
                }
                $offset += ($g - strlen($m));
            }
        } else {
            igk_wln_e("evaluate global script here is obsolete: please use eval global_expression method");
        }
    }
    if (($ctx == null) && (igk_get_env("sys://html/bindentries") === 1)) {
        $e = igk_get_env("sys://html/bindkey");
        $v = preg_replace("#\[\[:@raw]]#i", "[[:@entries[" . $e . "]]]", $v);
    }
    foreach ($comment as $k => $s) {
        $v = str_replace($k, $s, $v);
    }
    return $v;
}
///<summary></summary>
///<param name="table"></param>
///<param name="queryresult"></param>
/**
 * 
 * @param mixed $table 
 * @param mixed $queryresult 
 */
function igk_html_db_build_table($table, $queryresult)
{
    $r = $queryresult;
    if (!$r || is_bool($r))
        return;
    if ($r->resultTypeIsBoolean()) {
        $tr = $table->addTr();
        $tr->addTd()->Content = "Result";
        $tr = $table->addTr();
        $xr = ((object)$r->getRowAtIndex(0));
        $tr->addTd()->Content = isset($xr->clResult) && igk_parsebool($xr->clResult);
        return;
    }
    $tr = $table->addTr();
    foreach ($r->Columns as $v) {
        $tr->add("th")->Content = $v->name;
    }
    foreach ($r->Rows as $v) {
        $tr = $table->addTr();
        foreach ($v as $mm) {
            $tr->addTd()->Content = $mm;
        }
    }
}
///<summary></summary>
///<param name="m"></param>
/**
 * 
 * @param mixed $m 
 */
function igk_html_debug_m($m)
{
    return "<div style=\"background-color:#B92900; color:FFC193;\">{$m}</div>";
}
///<summary></summary>
///<param name="value"></param>
/**
 * 
 * @param mixed $value 
 */
function igk_html_endbinding($value)
{
    igk_die("deprecated :" . __FUNCTION__);
}
///<summary>Represente igk_html_engine_parent_node function</summary>
/**
 * Represente igk_html_engine_parent_node function
 */
function igk_html_engine_parent_node()
{
    $p = igk_get_env(IGK_XML_HTML_TEMPLATE_PARENT_KEY);
    if (($c = igk_count($p)) > 0) {
        return $p[$c - 1];
    }
    return null;
}
///<summary>Represente igk_html_engine_parent_pop_node function</summary>
/**
 * Represente igk_html_engine_parent_pop_node function
 */
function igk_html_engine_parent_pop_node()
{
    return igk_pop_env(IGK_XML_HTML_TEMPLATE_PARENT_KEY);
}
///<summary>Represente igk_html_engine_parent_push_node function</summary>
///<param name="n"></param>
/**
 * Represente igk_html_engine_parent_push_node function
 * @param mixed $n 
 */
function igk_html_engine_parent_push_node($n)
{
    igk_push_env(IGK_XML_HTML_TEMPLATE_PARENT_KEY, $n);
}
///<summary>Represente igk_html_escape_tag function</summary>
///<param name="s"></param>
///<param name="offset" default="0"></param>
///<param name="strict" default="1"></param>
///<param name="entityflag" default="ENT_NOQUOTES"></param>
///<param name="encoding" default="UTF-8"></param>
/**
 * Represente igk_html_escape_tag function
 * @param mixed $s 
 * @param mixed $offset 
 * @param mixed $strict 
 * @param mixed $entityflag 
 * @param mixed $encoding 
 */
function igk_html_escape_tag($s, $offset = 0, $strict = 1, $entityflag = ENT_NOQUOTES, $encoding = "UTF-8")
{
    $ln = strlen($s);
    $pos = $offset;
    $out_tag = 0;
    $o = "";
    $rgxs = "";
    if ($strict)
        $rgxs = "/<(\/)?(?P<tag>(script|img|object|iframe|style|link|video|audio|embed))(\s+(\/?>)?|>)/i";
    else {
        $rgxs = "/<(\/)?(?P<tag>([a-z_]([a-z0-9_]+)?(:[[a-z_]([a-z0-9_]+)?])?))(\s+(\/?>)?|>)/i";
    }
    while ($pos < $ln) {
        $ch = $s[$pos];
        switch ($ch) {
            case "<":
                if (preg_match($rgxs, $s, $tab, PREG_OFFSET_CAPTURE, $pos) && ($tab[0][1] == $pos)) {
                    igk_str_escape_tag_replace($s, $pos, $tab, $entityflag, $encoding);
                    $o .= substr($s, $tab[0][1], $pos - $tab[0][1]);
                    $ln = strlen($s);
                    $pos--;
                } else {
                    $o .= $ch;
                }
                break;
            default:
                $o .= $ch;
                break;
        }
        $pos++;
    }
    return $o;
}
///<summary></summary>
///<param name="content"></param>
///<param name="params" default="null"></param>
/**
 * evaluate article content 
 * @param mixed $content content to evaluate 
 * @param mixed $params parameter to bind
 */
function igk_html_eval_article($content, $params = null, $evalExpression = false)
{
    igk_push_article_chain(__FUNCTION__, $params);
    extract(func_get_arg(1));
    $t = igk_createtextnode();
    if ($evalExpression) {
        $content = igk_html_eval_global_script($content, null, $params, "::SRC");
    }
    $content = igk_html_treat_content($content, null, $params);
    $src = $content->render();
    igk_pop_article_chain();
    return $src;
}
///<summary> evaluate global script</summary>
/**
 *  evaluate global script
 */
function igk_html_eval_global_script($src, $ctrl, $raw, $context = null)
{
    $match = [];
    if (empty($src) || !($g = preg_match_all(IGK_TEMPLATE_GLOBAL_EXPRESSION_REGEX, $src, $match, PREG_OFFSET_CAPTURE, 0))) {
        return $src;
    }
    $tab = igk_get_eval_global_script_actions();
    $c = 0;
    $regex_script = "";
    $c = igk_count($match[0]);
    $v = $src;
    for ($i = 0; $i < $c; $i++) {
        $v_m = $match[0][$i];
        $exp = $match["exp"][$i];
        $v_pos = $v_m[1];
        $bar = igk_str_read_brank($src, $v_pos, "]", "[", null, 0, 0);
        if ($v_pos > strlen($src))
            igk_die("/!\\ failed to read global expression : ");
        $v_m = $bar;
        $g = strpos($bar, ":");
        $type = trim(substr($bar, 1, $g - 1));
        $value = trim(substr($bar, $g + 1, -1));
        if (isset($tab[$type])) {
            $fc = $tab[$type];
            igk_wln_e("handle with regitered global action");
            continue;
        }
        if (($match[1][$i]) == '[' && ($match[6][$i] == ']')) {
            $v = str_replace($v_m, $exp, $v);
            continue;
        }
        switch ($type) {
            case "curi":
                if ($ctrl) {
                    $v = str_replace($v_m, igk_io_baseuri() . "/" . $ctrl->getUri($value), $v);
                } else
                    $v = str_replace($v_m, igk_io_baseuri() . "/" . igk_uri($value), $v);
                break;
            case "uri":
                $v = str_replace($v_m, igk_io_baseuri() . "/" . igk_uri($value), $v);
                break;
            case "guri":
                $uri = igk_io_baseuri() . "/" . igk_uri($value);
                $result = igk_io_baseuri() . "/" . igk_getctrl(IGK_SYSACTION_CTRL)->getUri($value);
                $v = str_replace($v_m, $result, $v);
                break;
            case "lang":
                $value = trim($value);
                if (!empty($value)) {
                    $v = str_replace($v_m, __($value), $v);
                } else
                    $v = str_replace($v_m, $value, $v);
                break;
            case "conf":
                $v = str_replace($v_m, igk_sys_getconfig($value), $v);
                break;
            case "func":
                $obj = igk_html_databinding_getobjforscripting($ctrl);
                $rm = $value;
                $m = igk_html_php_eval($obj->args, $ctrl, $raw, $rm, 1);
                $v = str_replace($v_m, $m, $v);
                break;
            case "funce":
                $tab = explode("=", trim($value));
                if (igk_count($tab) > 1) {
                    $obj = igk_html_databinding_getobjforscripting($ctrl);
                    igk_html_databinding_read_obj_litteral($obj, $value, $ctrl, $raw);
                    $out = $src;
                    foreach ($obj->args as $k => $tv) {
                        $rm = igk_html_treat_content($out, $ctrl, $raw)->render();
                        $obj->args[$k] = igk_html_php_eval($obj, $ctrl, $raw, $rm, 1);
                        $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                        $v = preg_replace("/^(\\s)+$/i", "", $v);
                    }
                } else
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                break;
            case "exp":
                $tab = explode(":", $value);
                if (igk_count($tab) > 1) {
                    $rm = substr($value, strpos($tab[0], $value) + strlen($tab[0]) + 1);
                    $obj = igk_html_databinding_getobjforscripting($ctrl);
                    $obj->args[$tab[0]] = $rm;
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                }
                break;
            case "eval":
                $v = str_replace($v_m, str_replace("$", "\\$", $v_m), $v);
                break;
            default:
                break;
        }
    }
    return $v;
}
///<summary>used to evaluate expression script</summary>
///<param name="script">used to evaluate expression script</summary>
/**
 * used to evaluate expression script
 * @param mixed $script used to evaluate expression script
 */
function igk_html_eval_script($src, $ctrl, $raw, $context = null)
{
    error_log(__FUNCTION__ . " is obselete will be remove on next version . please use ");
    igk_die("obselete " . __FUNCTION__);
}
///<summary></summary>
///<param name="script"></param>
///<param name="context"></param>
/**
 * 
 * @param mixed $script 
 * @param mixed $context 
 */
function igk_html_eval_value_in_context($script, $context)
{
    $__g_context = (array)$context;
    extract($__g_context);
    unset($__g_context);
    $vars = get_defined_vars();
    $m = trim($script);
    if (IGKString::EndWith($m, ";") == false)
        $m .= ";";
    $c = preg_match_all("/(?P<name>(\\$(?P<value>([0-9a-z_]+))))/i", $m, $tab);
    if ($c > 0) {
        for ($i = 0; $i < $c; $i++) {
            $n = $tab["name"][$i];
            $key = $tab["value"][$i];
            if (!isset($vars[$key])) {
                return "[eval:" . $script . "]";
            }
        }
    }
    $d = eval(<<<EOF
{$m}
EOF);
    if (!empty($s)) {
        igk_wl("Error on execution " . $m . "<br />" . $s);
    }
    return $d;
}
///<summary></summary>
///<param name="token"></param>
/**
 * 
 * @param mixed $token 
 */
function igk_html_form_is_valid_token($token)
{
    return igk_html_form_tokenid() == $token;
}
///<summary>cref doken id</summary>
/**
 * cref doken id
 */
function igk_html_form_tokenid()
{
    $cref = igk_app()->session->getCRef();
    return md5($cref);
}
///<summary>validate form's object field</summary>
/**
 * validate form's object field
 */
function igk_html_form_validate($o, $settings, &$errors)
{
    $r = true;
    foreach ($settings as $k => $v) {
        $s = igk_getv($o, $k);
        if (!$s) {
            $errors[$k] = is_string($v) ? $v : igk_getv($v, "Message");
            $r = $r && false;
        } else {
            $rgx = igk_getv($v, "Regex");
            $callback = igk_getv($v, "Callback");
            if ($rgx && !preg_match($rgx, $s)) {
                $errors[$k] = igk_getv($v, "Message", __("err.validation_1", $k));
                $r = $r && false;
            } else if ($callback && is_callable($callback) && !call_user_func_array($callback, [$s])) {
                $errors[$k] = igk_getv($v, "Message", __("err.validation_1", $k));
                $r = $r && false;
            }
        }
    }
    return $r;
}
///<summary>craete a balafon uri frame</summary>
///<remark> Remark: closeuri : uri callback when closing</remark>
///<note>target: receive that will the frame. if null will be add to global document body and will be render at the when body requested</note>
/**
 * craete a balafon uri frame
 */
function igk_html_frame($ctrl, $name, $closeuri = null, $target = null, $reloadcallback = null)
{
    $frame = igk_getctrl(IGK_FRAME_CTRL)->createFrame($name, $ctrl, $closeuri, $reloadcallback);
    if ($frame) {
        if ($target === null) {
            $target = igk_app()->getDoc()->getBody();
        }
        $target->add($frame);
    }
    return $frame;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="id"></param>
///<param name="title"></param>
///<param name="uri"></param>
///<param name="form" ref="true"></param>
///<param name="closed" default="false"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $id 
 * @param mixed $title 
 * @param mixed $uri 
 * @param mixed $form 
 * @param mixed $closed 
 */
function igk_html_frame_ex($ctrl, $id, $title, $uri, &$form, $closed = false)
{
    $frame = igk_html_frame($ctrl, $id);
    $div = $frame->BoxContent;
    $div->clearChilds();
    $frame->Title = $title;
    $frm = $div->addForm();
    $frm["action"] = $uri;
    $frm["igk-ajx-lnk-tg-response"] = $ctrl->TargetNode["id"];
    if ($closed)
        $frm["igk-frame-close"] = "1";
    $form = $frm;
    return $frame;
}
///<summary>convert string litteral to string html text presentation</summary>
/**
 * convert string litteral to string html text presentation
 */
function igk_html_from_string($str, $block = "p")
{
    $tstr = explode(IGK_LF, $str);
    $t = igk_create_node("div");
    foreach ($tstr as $v) {
        $g = trim($v);
        if (empty($g)) {
            $t->addBr();
        } else
            $t->add($block)->setContent(trim($v));
    }
    return $t->render();
}
///<summary>used to get the component demo keys</summary>
/**
 * used to get the component demo keys
 */
function igk_html_get_component_demo()
{
    return igk_get_env("sys://html/components/demos");
}
///<summary>shortcut list igk_html_reg_compoent_package</summary>
/**
 * shortcut list igk_html_reg_compoent_package
 */
function igk_html_get_component_packages()
{
    return igk_html_reg_component_package();
}
///<summary></summary>
///<param name="src"></param>
///<param name="options" default="null" ref="true"></param>
/**
 * 
 * @param mixed $src 
 * @param mixed $options 
 */
function igk_html_get_depth_indent($src, &$options = null)
{
    if (($options == null) || (isset($options->Indent) && (!$options->Indent)))
        return null;
    $c = igk_getv($options, "Depth", 0);
    $q = $src;
    $s = IGK_STR_EMPTY;
    while ($c > 0) {
        $s .= "\t";
        $c--;
    }
    return $s;
}
///<summary>Represente igk_html_get_document_class function</summary>
///<param name="doc"></param>
/**
 * Represente igk_html_get_document_class function
 * @param mixed $doc 
 */
function igk_html_get_document_class($doc)
{
    $cl = $doc->getTempFlag(IGK_DOCUMENT_CLASS);
    if ($cl) {
        return $cl->getValue();
    }
    return "igk-web-page";
}
///<summary></summary>
///<param name="expression"></param>
///<param name="tab" ref="true"></param>
/**
 * 
 * @param mixed $expression 
 * @param mixed $tab 
 */
function igk_html_get_expression($expression, &$tab)
{
    $ln = strlen($expression);
    $ch = "";
    $pos = 0;
    $v = "";
    $pipe = "";
    while ($pos < $ln) {
        $ch = $expression[$pos];
        switch ($ch) {
            case "'":
            case '"':
                $v .= igk_str_read_brank($expression, $pos, $ch, $ch, null, 1);
                break;
            case "|":
                if (($rp = $pos - 1) > 0) {
                    if ($expression[$rp] == "\\") {
                        $v = substr($v, 0, -1) . $ch;
                        break;
                    }
                }
                if ((($rp = ($pos + 1)) < $ln) && ($expression[$rp] != '|')) {
                    $pipe = substr($expression, $rp);
                    break 2;
                }
                break;
            default:
                $v .= $ch;
                break;
        }
        $pos++;
    }
    $tab["value"] = trim($v);
    $tab["pipe"] = $pipe;
}


///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_html_get_func_param($name)
{
    return igk_getv(igk_app()->session->getParam("system://igk_html_func_param"), $name);
}
///<summary></summary>
///<param name="node"></param>
///<param name="type" default="text"></param>
///<param name="depth"></param>
/**
 * 
 * @param mixed $node 
 * @param mixed $type 
 * @param mixed $depth 
 */
function igk_html_get_heararchi($node, $type = "text", $depth = 0)
{
    $o = "";
    $dp = "";
    switch ($type) {
        case "text":
            $o .= $dp . get_class($node) . IGK_LF;
            $depth++;
            if ($depth > 0) {
                $dp = igk_str_repeat("\t", $depth);
            }
            foreach ($node->Childs as $k) {
                $o .= $dp . igk_html_get_heararchi($k, $type, $depth);
            }
            break;
        case "expression":
            $o .= $dp . igk_get_node_expression($node, $depth) . IGK_LF;
            break;
    }
    return $o;
}
///<summary></summary>
///<param name="node"></param>
///<param name="type" default="text"></param>
///<param name="depth"></param>
/**
 * 
 * @param mixed $node 
 * @param mixed $type 
 * @param mixed $depth 
 */
function igk_html_get_inner_heararchi($node, $type = "text", $depth = 0)
{
    $o = "";
    if ($node && $node->HasChilds) {
        foreach ($node->Childs as $v) {
            $o .= igk_html_get_heararchi($v, $type, $depth);
        }
    }
    return $o;
}
///<summary>get system uri for link</summary>
/**
 * get system uri for link
 */
function igk_html_get_system_uri($link, $option = null, $webapp = null)
{
    $webapp = $webapp ?? igk_is_webapp();
    if ($option && !property_exists($option, 'StandAlone')) {
        $option->StandAlone = 1;
    }
    $src = "";

    if (!empty($link)) {
        if (IGKValidator::IsUri($link)) {
            return $link;
        }
        $buri = igk_io_baseuri();
        $bdir = igk_io_basedir();
        $sdir = igk_server()->script_dir();

        if ($webapp) {
            if (strpos($link, $bdir . "/") === 0) {
                $path = substr($link, strlen($bdir) + 1);
                return igk_io_baseuri() . "/" . $path;
            }
        }
        if (file_exists(Path::Combine($bdir, $link))) {
            return $link;
        }

        // igk_wln_e("try ... \n".implode("\n", [$bdir, $webapp,  $sdir,
        //     file_exists(Path::Combine($bdir, $link)),
        //     file_exists(Path::Combine($sdir, $link)),
        //     "webapp?".igk_is_webapp(),
        //     "tf?" .$tf,
        //     realpath($tf)
        // ]
        // ));
        $s = "";
        $k = explode("?", $link);
        if (file_exists($k[0])) {
            $s = igk_io_basepath($k[0]);

            if (count($k) > 1)
                $s .= "?" . implode("&", array_slice($k, 1));
        } else {
            $s = $link;
        }
        if ($option && ($option->Context == HtmlContext::Html) && $option->Cache) {
            $src = igk_uri($s);
        } else if ($option && ($option->StandAlone) || $webapp) {
            $r = Path::FlattenPath($s);
            if (isset($option->Document)) {
                if ($curi = $option->Document->getBaseUri()) {
                    $curi = rtrim($curi, '/');
                    if ($curi != $buri) {
                        $buri = '';
                    }
                }
                //$buri = rtrim($option->Document->getBaseUri() ?? $buri, '/'); 
            }
            $src = igk_uri($buri . '/' . ltrim($r, '/'));
        } else {
            $cpath = igk_io_currentrelativeuri();
            if (strpos($link, $bdir) === 0) {
                $tlink = substr($link, strlen($bdir));
                $src = $cpath . ltrim($tlink, "/");
            } else {
                $src = $cpath . ltrim($link, "/");
            }
        }
    } else {
        $src = igk_io_currentrelativeuri();
    }
    return $src;
}
///<summary>summary get context controller title</summary>
/**
 * summary get context controller title
 */
function igk_html_get_title($ctrl, $def = null)
{
    return igk_getv($ctrl->Configs, 'clTitle', igk_sys_getconfig("website_title"));
}
///<summary></summary>
///<param name="node"></param>
/**
 * 
 * @param mixed $node 
 */
function igk_html_getallchilds($node)
{
    $tab = array();
    if ($node->HasChilds) {
        foreach ($node->Childs as $k) {
            $tab[] = $k;
            $c = igk_html_getallchilds($k);
            if (igk_count($c) > 0)
                $tab = array_merge($tab, $c);
        }
    }
    return $tab;
}
///<summary></summary>
///<param name="n"></param>
///<param name="level" default="-1"></param>
///<param name="mode" default="t"></param>
///<param name="callback" default="null"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $level 
 * @param mixed $mode 
 * @param mixed $callback 
 */
function igk_html_hearachi($n, $level = -1, $mode = "t", $callback = null)
{
    $q = array((object)array("node" => $n, "level" => 0));
    $so = "";
    while ($s = array_pop($q)) {
        if (($level > 0) && $s->level > $level)
            continue;
        $so .= str_repeat("\t", $s->level);
        $so .= get_class($s->node) . ":" . ($s->node->TagName ?? get_class($s->node));
        if ($callback) {
            $callback($s->node, $so);
        }
        $so .= IGK_LF;
        $ch = $s->node->Childs;
        $tab = $ch ? $ch->to_array() : array();
        for ($c = count($tab) - 1; $c >= 0; $c--) {
            array_push($q, (object)array("node" => $tab[$c], "level" => $s->level + 1));
        }
    }
    return $so;
}
///<summary></summary>
///<param name="option" default="null"></param>
/**
 * 
 * @param mixed $option 
 */
function igk_html_indent_line($option = null)
{
    if (($option == null) || (!isset($option->Indent) || (!$option->Indent)))
        return null;
    return IGK_LF;
}
///<summary>get real index of node in parent</summary>
/**
 * get real index of node in parent
 */
function igk_html_index_of($node)
{
    $p = $node->getParentNode();
    if ($p == null)
        return null;
    $i = 0;
    foreach ($p->Childs as $v) {
        if ($v === $node)
            return $i;
        $i++;
    }
    return $i;
}
///<summary></summary>
///<param name="t"></param>
/**
 * 
 * @param mixed $t 
 */
function igk_html_init_node_page($t)
{
    $t->setClass("fit igk-parentscroll igk-powered-viewer overflow-y-a");
}
///<summary></summary>
///<param name="text"></param>
/**
 * 
 * @param mixed $text 
 */
function igk_html_initbindexpression($text)
{
    $text = preg_replace("/<!--\\s*(.)+\\s*-->/i", "", $text);
    return $text;
}

///<summary>used to initalize a form with data object</summary>
/**
 * used to initalize a form with data object
 */
function igk_html_initform($form, $obj, $formtab)
{
    $s = array();
    foreach ($obj as $k => $v) {
        if ($formtab && array_key_exists($k, $formtab)) {
            $s[$k] = $formtab[$k];
        } else {
            $d = igk_create_node();
            if (is_array($v)) {
                $d->addSLabelInput($k, igk_getv($v, "type", "text"), igk_getv($v, "value"));
            } else {
                if (igk_reflection_class_extends($v, IGK_HTML_ITEMBASE_CLASS))
                    $d->add($v);
                else
                    $d->addSLabelInput($k, "text", $v);
            }
            $s[$k] = $d;
        }
    }
    foreach ($s as $i => $v) {
        if ($v == null)
            continue;
        $form->add($v);
    }
}
///<summary>shortcut to init menu</summary>
/**
 * shortcut to init menu
 */
function igk_html_initmenu($name, $ctrl, $target, $tab, $tag = "li", $selected = null)
{
    igk_getctrl(IGK_MENU_CTRL)->initCustomMenu($name, $ctrl, $target, $tab, $tag, $selected);
}
///<summary></summary>
///<param name="type"></param>
///<param name="content"></param>
/**
 * 
 * @param string $type data type
 * @param string $content content 
 */
function igk_html_inlinedata(string $type, string $content)
{
    return 'data:' . $type . ";base64," . base64_encode($content);
}
///<summary>check if sytem request a full uri</summary>
/**
 * check if sytem request a full uri
 */
function igk_html_is_fullurirequest($options = null)
{
    return igk_is_ajx_demand() || preg_match("#^\/!@#i", igk_io_request_uri()) || ($options && (igk_xml_is_mailoptions($options) || $options->StandAlone));
}
///<summary>check if this node is in ns</summary>
///<retrurn >true if empty ns or parent ns is on the same ns. true to render xmlns or false</retrurn>
/**
 * check if this node is in ns
 */
function igk_html_is_ns_child($n)
{
    $q = $n;
    $g = $n->getParam(IGK_NS_PARAM_KEY);
    if (empty($g))
        return true;
    $q = $q->getParentNode();
    if ($q) {
        $ns = $q->getParam(IGK_NS_PARAM_KEY);
        if ($ns !== $g)
            return true;
        return false;
    }
    return true;
}
///<summary>get if this is a valid tag name</summary>
/**
 * get if this is a valid tag name
 */
function igk_html_is_tagname($v)
{
    return preg_match("/^" . IGK_TAGNAME_CHAR_REGEX . "+$/i", $v);
}
///<summary></summary>
///<param name="t"></param>
/**
 * 
 * @param mixed $t 
 */
function igk_html_loading_frame($t)
{
    $uri = R::GetImgUri("waitcursor");
    if ($uri)
        $t->div()->setAttributes(array("class" => "dispib"))->addBalafonJS()->Content = "igk.media.webplayer.init(this.parentNode,'{$uri}');";
}
///<summary></summary>
///<param name="tagname"></param>
/**
 * 
 * @param mixed $tagname 
 */
function igk_html_mustclosetag($tagname)
{
    return !igk_html_emptytag($tagname);
}
///<summary></summary>
///<param name="node"></param>
///<param name="attributes" default="null"></param>
/**
 * 
 * @param mixed $node 
 * @param mixed $attributes 
 */
function igk_html_new($node, $attributes = null)
{
    return igk_create_node($node, $attributes);
}
///<summary>create new node</summary>
/**
 * create new node
 */
function igk_html_newnode($tag)
{
    return new HtmlNode($tag);
}

///<summary>get the base of the creation node</summary>
/**
 * get the base of the creation node
 */
function igk_html_parent_node()
{
    $p = igk_get_env(IGK_XML_CREATOR_PARENT_KEY);
    if (($c = igk_count($p)) > 0) {
        return $p[$c - 1];
    }
    return null;
}
/**
 * skip creation adding 
 */
function igk_html_skip_add($value = 1)
{
    HtmlUtils::SkipAdd($value);
}
function igk_html_is_skipped($autoreset = true)
{
    return HtmlUtils::IsSkipped($autoreset);
}
///<summary>Represente igk_html_parent_result function</summary>
///<param name="r"></param>
/**
 * Represente igk_html_parent_result function
 * @param mixed $r 
 */
function igk_html_parent_result($r)
{
    if (($r == null) || ($r instanceof HtmlItemBase)) {
        igk_environment()->set(IGK_XML_CREATOR_NODE_RESULT, $r);
    }
}
///<summary>php eval data binding</summary>
///<param name="obj"> object that will contains data calculated in script. in args. (note : obj, ctrl , row, expression, trimExpression, and script are reserved )</param>
///<param name="ctrl"></param>
///<param name="row"></param>
///<param name="expression">expression string</param>
/**
 * php eval data binding
 * @param mixed $obj  object that will contains data calculated in script. in args. (note : obj, ctrl , row, expression, trimExpression, and script are reserved )
 * @param mixed $ctrl 
 * @param mixed $row 
 * @param mixed $expression expression string
 */
function igk_html_php_eval($obj, $ctrl, $raw, $expression, $a = 0)
{
    if (!is_string($expression)) {
        igk_die("expression is not a string");
    }
    if (($expression == null) || empty($expression))
        return null;
    $bindingInfo = igk_get_env("sys://html-data");
    // $key = null;
    if ($bindingInfo)
        $key = $bindingInfo->key;
    try {
        // $trimExpression = trim($expression);
        // $piped = 0;
        igk_html_get_expression($expression, $tab);
        $v = $tab["value"];
        $pipe = $tab["pipe"];
        if (preg_match("/^(?P<a>@)?(\'|\")(?P<v>([^'\"]*))\\2$/", $v, $otab)) {
            if (isset($otab['a']) && !empty($otab['a'])) {
                $a = 1;
                $v = substr($v, 1);
            }
        }
        $src_expression = $v;
        if (!(strpos($v, "return ") === 0)) {
            $v = "return " . $v;
            if (!IGKString::EndWith($v, ";"))
                $v .= ";";
        }
        IGKOb::Start();
        igk_eval_last_script($v);
        if ($obj && is_array($obj)) {
            $raw = array_merge($obj, ["raw" => $raw]);
        }
        if ($raw instanceof \IGK\System\Html\HtmlBindingRawTransform) {
            $raw->data = $src_expression;
            $m = $raw;
        } else {
            $m = igk_eval_in_context($v, $ctrl, $raw);
        }
        igk_eval_last_script(null);
        $c = IGKOb::Content();
        IGKOb::Clear();
        igk_sys_handle_error($v, $c);
        $tb = ["v" => $m, "pipe" => !empty($pipe) ? $pipe : [], "a" => $a,  "ctrl" => $ctrl, "raw" => $raw];
        $m = igk_html_php_evallocalized_expression($v, $tb);
    } catch (Exception $ex) {
        igk_notifyctrl()->addError(igk_getv($ex, "Message"));
        igk_show_prev("ERROR: " . htmlentities($ex));
        igk_exit();
    } catch (Throwable $ex) {
        igk_notifyctrl()->addError(igk_getv($ex, "Message"));
        igk_show_prev("ERROR: " . htmlentities($ex));
        igk_exit();
    }
    return $m;
}
///<summary>localize une expression </summary>
///<exemple> 'donnee: {0|upper}', info => 'donnee : INFO'</exemple>
///<remark> passer des options tab array of info :
///v: the value to eval
///a: 0|1 use real value
///pipe : | list of pipe expression
///</remark>
/**
 * localize une expression 
 */
function igk_html_php_evallocalized_expression($expression, $tab = null)
{
    if ($tab == null) {
        igk_html_get_expression($expression, $tab);
        if (!$expression || !preg_match(IGK_LOCALIZE_EXPRESSION_REGEX, $expression, $tab)) {
            return null;
        }
    }
    $v = igk_getv($tab, "v", $expression);
    $pipe = igk_getv($tab, "pipe");
    if ($v instanceof \IGK\System\Html\HtmlBindingRawTransform) {
        $v->pipe = $pipe ? trim($pipe) : null;
        $v = "" . $v;
    } else {
        if (empty($v) && (!is_numeric($v))) {
            return '';
        }
        if (is_string($v) && !igk_getv($tab, 'a')) {
            $v = __($v);
        }
        if ($pipe) {

            if (($pos = strpos($pipe, "{{")) !== false) {
                // replace detected expression with evaluate expression
                $raw = $tab['raw'];
                if ($raw instanceof \IGK\System\Html\Templates\BindingContextInfo) {
                    $raw = ['raw' => $raw->raw];
                } else if (!is_array($raw)) {
                    $raw = ['raw' => $raw];
                }
                $pipe = igk_engine_eval_pipe($pipe, $pos, $raw);
            }
            $v = igk_str_pipe_value($v, $pipe);
        }
    }
    return $v;
}

function igk_php_eval_in_context($exp)
{
    extract(igk_extract_data(igk_getv(array_slice(func_get_args(), 1), 0, [])));
    return @eval('return ' . $exp . ';');
}
function igk_extract_data($tab)
{
    if (is_array($tab)) {
        return $tab;
    }
    if (method_exists($tab, 'to_array')) {
        return $tab->to_array();
    }
    return (array)$tab;
}
///<summary></summary>
/**
 * 
 */
function igk_html_pop_node_parent()
{
    $n = igk_environment()->pop(IGK_XML_CREATOR_PARENT_KEY);
    if ($n instanceof \IGK\System\Html\IHtmlContextContainer) {
        if ($g = $n->getContext()) {
            \IGK\System\Html\HtmlLoadingContext::PopContext($g);
        }
    }

    return $n;
}


///<summary>used to pop the target node data</summary>
/**
 * used to pop the target node data
 */
function igk_html_popt(&$t)
{
    $s = igk_pop_env("sys://push/targetnode");
    if ($s)
        $t = $s;
}
///<summary>store the creator node parent</summary>
/**
 * store the creator node parent
 */
function igk_html_push_node_parent($n)
{
    $p = igk_get_env(IGK_XML_CREATOR_PARENT_KEY);
    if ($p == null) {
        $p = array();
    }
    array_push($p, $n);
    igk_set_env(IGK_XML_CREATOR_PARENT_KEY, $p);
    if ($n instanceof \IGK\System\Html\IHtmlContextContainer) {
        if ($g = $n->getContext()) {
            \IGK\System\Html\HtmlLoadingContext::PushContext($g);
        }
    }
}
///<summary>push targetnode. in inclusion scenario required</summary>
/**
 * push targetnode. in inclusion scenario required
 */
function igk_html_pusht(&$t)
{
    igk_push_env("sys://push/targetnode", $t);
    return $t;
}
///<summary></summary>
///<param name="s"></param>
/**
 * 
 * @param mixed $s 
 */
function igk_html_query_parse($s)
{
    $ln = strlen($s);
    $pos = 0;
    $o = "";
    while ($ln > $pos) {
        $ch = $s[$pos];
        switch ($ch) {
            case '`':
                $h = igk_str_read_brank($s, $pos, '`', '`');
                $o .= "<font color='" . sysCL::STRING_CL . "'>" . $h . "</font>";
                break;
            case "'":
                $h = igk_str_read_brank($s, $pos, '\'', '\'');
                $o .= "<font color='" . sysCL::LITTERAL_CL . "'>" . $h . "</font>";
                break;
            default:
                $o .= $ch;
                break;
        }
        $pos++;
    }
    return $o;
}
///<summary>used to register a component demonstration</summary>
///<remark>in case you don't what to populate the framework with {igk_html_demo_[compenentNanem]} function convention</remark>
///<param name="ns">the full namespace to  component</param>
///<param name="callback">the callback that will be called by system to initialize a demonstration view</param>
/**
 * used to register a component demonstration
 * @param mixed $ns the full namespace to component
 * @param mixed $callback the callback that will be called by system to initialize a demonstration view
 */
function igk_html_reg_component_demo($ns, $callback)
{
    igk_set_env_keys("sys://html/components/demos", $ns, $callback);
}
///<summary>Represente igk_html_reg_component_package function</summary>
///<param name="component" default="null"></param>
/**
 * Represente igk_html_reg_component_package function
 * @param mixed $component 
 */
function igk_html_reg_component_package($component = null)
{
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_html_render_all($n)
{
    $t = null;
    $s = null;
    return igk_html_render_node($n, $s, $t, false, 0);
}
///<summary>Represente igk_html_render_append_item function</summary>
///<param name="option"></param>
///<param name="node"></param>
/**
 * Represente igk_html_render_append_item function
 * @param mixed $option 
 * @param mixed $node 
 */
function igk_html_render_append_item($option, $node)
{
    HtmlRenderer::AppendOptionNode($option, $node);
}
///<summary>render node in chain hierachie</summary>
/**
 * render node in chain hierachie
 */
function igk_html_render_node($n, &$options, $tab = null, $textonly = false, $chain = 1)
{
    return HtmlRenderer::Render($n, $options);
}
///<summary> render text node</summary>
/**
 *  render text node
 */
function igk_html_render_text_node($n)
{
    $options = HtmlRenderer::CreateRenderOptions();
    $options->TextOnly = true;
    return igk_html_render_node($n, $options);
}
///<summary></summary>
///<param name="item"></param>
/**
 * 
 * @param mixed $item 
 */
function igk_html_render_xml($item)
{
    if (!$item)
        return null;
    ob_start();
    $opts = HtmlRenderer::CreateRenderOptions();
    $opts->Indent = true;
    igk_wl(igk_xml_header() . IGK_LF);
    igk_wl($item->render($opts));
    $s = ob_get_clean();
    return $s;
}
///<summary></summary>
/**
 * 
 */
function igk_html_reset_func_param()
{
    igk_app()->session->setParam("system://igk_html_func_param", null);
}
///<summary></summary>
///<param name="path"></param>
/**
 * 
 * @param mixed $path 
 */
function igk_html_resolv_img_uri($path)
{
    $f = igk_io_currentrelativepath($path);
    if (!empty($f) && file_exists($f)) {
        $r = igk_realpath($f);
        $s = igk_io_basepath($f);
        if ($s == $r) {
            $m = igk_uri(igk_io_baseuri() . "/" . $f);
            return $m;
        }
        $v_uri = igk_uri(igk_io_baseuri() . "/" . $s);
        return $v_uri;
    }
    return null;
}
///<summary></summary>
///<param name="item"></param>
///<param name="dispose" default="false"></param>
/**
 * 
 * @param mixed $item 
 * @param mixed $dispose 
 */
function igk_html_rm($item, $dispose = false)
{
    return $item->remove();
}
///<summary>remove base uri</summary>
/**
 * remove base uri
 */
function igk_html_rm_base_uri($v)
{
    $s = igk_io_baseuri();
    $rg = "/((" . str_replace("/", "\\/", $s) . "(\/)?)+)/i";
    if (preg_match($rg, $v)) {
        $v = preg_replace($rg, IGK_STR_EMPTY, $v);
    }
    return $v;
}
///<summary></summary>
///<param name="doc"></param>
///<param name="file"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $file 
 */
function igk_html_save_doc_formail($doc, $file)
{
    $opt = HtmlRenderer::CreateRenderOptions();
    $opt->Context = "mail";
    $maildoc = IGKHtmlMailDoc::CreateFromDocument($doc);
    igk_io_save_file_as_utf8_wbom($file, $maildoc->render($opt), true);
}
///<summary>used to select in node expression</summary>
/**
 * used to select in node expression
 */
function igk_html_select($node, $expression)
{
    $r = array();
    $q = $node;
    $list = array();
    $searchmode = 1;
    while ($q) {
        switch ($searchmode) {
            case 1:
            default:
                if (strtolower($q->TagName) == $expression) {
                    $r[] = $q;
                }
                break;
        }
        if ($q->HasChilds) {
            foreach ($q->Childs as $k) {
                array_push($list, $k);
            }
        }
        $q = array_pop($list);
    }
    return $r;
}
///<summary>Represente igk_html_set_document_class function</summary>
///<param name="doc"></param>
///<param name="classname"></param>
/**
 * Represente igk_html_set_document_class function
 * @param mixed $doc 
 * @param mixed $classname 
 */
function igk_html_set_document_class($doc, $classname)
{
    $cl = $doc->getTempFlag(IGK_DOCUMENT_CLASS);
    if (!$cl) {
        $cl = new HtmlCssClassValueAttribute();
        $doc->setTempFlag(IGK_DOCUMENT_CLASS, $cl);
    }
    $cl->add($classname);
}
///<summary></summary>
///<param name="name"></param>
///<param name="v"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $v 
 */
function igk_html_set_func_param($name, $v)
{
    $tab = igk_app()->session->getParam("system://igk_html_func_param");
    if ($tab == null)
        $tab = array();
    $tab[$name] = $v;
    igk_getv(igk_app()->session->setParam("system://igk_html_func_param"), $tab);
}

///<summary></summary>
///<param name="doc"></param>
///<param name="file"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $file 
 */
function igk_html_store_doc_form_mailtransport($doc, $file)
{
    $opt = HtmlRenderer::CreateRenderOptions();
    $opt->Context = "mail";
    igk_io_save_file_as_utf8_wbom($file, $doc->render($opt), true);
}
///<summary>remove html comment </summary>
///<param name="$c"></param>
/**
 * remove html comment 
 * @param string $c 
 * @return string
 */
function igk_html_strip_comment(string $c)
{
    while (($t = strpos($c, "<!--")) !== false) {
        $end = strpos($c, "-->");
        if ($end === false) {
            $c = substr($c, 0, $t);
        } else {
            $c = substr($c, 0, $t) . substr($c, $end + 3);
        }
    }
    return $c;
}
///push child into the list
/**
 */
function igk_html_toggle_class($target, $childtag = "tr", $startindex = 1, $class1 = "table_darkrow", $class2 = "table_lightrow")
{
    HtmlUtils::ToggleTableClassColor($target, $childtag, $startindex, $class1, $class2);
}
///<summary>treat html content. Evaluate expression within </summary>
///<param name="$content" type="string"> string to evaluate </summary>
///<param name="$ctrl" type="controller"> the controller that request . it can be null</summary>
///<param name="$raw" type="mixed"> string or context array definition</summary>
///<param name="$target" type="node"> node for requesting . it can be null</summary>
/**
 * treat html content. Evaluate expression within 
 * @param string $content  string to evaluate 
 * @param ?BaseController $ctrl  the controller that request . it can be null
 * @param mixed $raw  string or context array definition
 * @param HtmlItemBase|null $target  node for requesting . it can be null
 */
function igk_html_treat_content($content, $ctrl, $raw, $target = null)
{
    if (empty($content))
        return;
    $ldcontext = igk_createloading_context($ctrl, $raw);
    if ($target == null) {
        $target = igk_create_notagnode();
    }
    $target->Load($content, $ldcontext);
    igk_html_treatinput($target);
    return $target;
}
///<summary></summary>
///<param name="info"></param>
///<param name="row"></param>
///<param name="ctrl" default="null"></param>
///<param name="artcontext" default="null"></param>
/**
 * 
 * @param mixed $info 
 * @param mixed $row 
 * @param mixed $ctrl 
 * @param mixed $artcontext 
 */
function igk_html_treatbinding($info, $row, $ctrl = null, $artcontext = null)
{
    if ($info->visiblerow <= 0) {
        return;
    }
    $output = array();
    $info->visiblerow--;
    $ctx = array(
        "ctrl" => $ctrl,
        "raw" => $row,
        "key" => igk_getv(
            $info,
            "key",
            0
        )
    );
    if ($ctrl) {
        $ctx = array_merge($ctrl->getSystemVars(), $ctx);
    }
    switch (strtolower($info->type)) {
        case "igk-data-binding":
            if (preg_match_all("/^(?P<tagname>(" . IGK_TAGNAME_CHAR_REGEX . ")+)\s*:(?P<value>(.)+)$/i", $info->binding, $output)) {
                $s = $output["tagname"];
                $r = $info->node->add($output["tagname"][0]); {
                    $s = igk_html_treatbinding_evaldata($output['value'][0], $row, $ctrl, $artcontext);
                    $r->Load($s);
                }
            } else {
                $rtab = explode(":", $info->binding);
                $c = igk_count($rtab);
                switch ($c) {
                    case 1:
                        $info->node->Content = igk_html_treatbinding_evaldata($rtab[0], $row, $ctrl, $artcontext);
                        break;
                    case 2:
                        if (igk_html_is_tagname($rtab[0])) {
                            $r = $info->node->add($rtab[0]); {
                                $s = igk_html_treatbinding_evaldata($rtab[1], $row, $ctrl, $artcontext);
                                $r->Content = $s;
                            }
                        } else {
                            return false;
                        }
                        break;
                    default:
                        return false;
                }
            }
            break;
        case "igk-data-row-binding":
            if (isset($info->rowcheckExpression)) {
                $o = igk_html_eval_value_in_context("return " . $info->rowcheckExpression . ";", $ctx);
                if ($o == false)
                    break;
            }
            $r = igk_create_node($info->node->TagName);
            $r->copyAttributes($info->node);
            $m = "";
            $m = igk_html_get_inner_heararchi($info->node, "expression");
            $r->LoadExpression($m);
            if (isset($info->data)) {
                $g = igk_html_eval_value_in_context($info->data, $ctx);
                if ($g) {
                    $it = 0;
                    foreach ($g as $k => $v) {
                        $tt = $info->parent->add($info->node->TagName);
                        $tt->copyAttributes($info->node);
                        igk_html_bind_node($ctrl, $r, $tt, $v);
                        $it++;
                        $tt->Index = $info->Index + $it;
                    }
                }
            } else {
                $bindchild = false;
                $k = $info->parent->add($info->node->TagName, null, $info->Index);
                $k->copyAttributes($info->node);
                $key = "sys://html-data";
                igk_set_env($key, $info);
                igk_html_bind_node($ctrl, $r, $k, $row, false, $bindchild);
                igk_set_env($key, null);
                $k->Index = $info->Index;
                $info->Index++;
                if ($bindchild) {
                    $m = igk_html_get_inner_heararchi($k, "expression");
                    $k->clearChilds();
                    $m = str_replace("\"", "__''__", $m);
                    $m = igk_html_treatbinding_evaldata($m, $row, $ctrl, $artcontext);
                    $m = str_replace("__''__", "\"", $m);
                    $k->Load($m, igk_createloading_context($ctrl, $row));
                }
            }
            break;
        case "igk-data-full-row-binding":
            $m = $info->node->getinnerHtml();
            $tag = $info->tag;
            if ($tag) {
                $r = igk_create_node($info->node->TagName);
                foreach ($row as $k) {
                    $r->add($tag)->Content = $k;
                }
                $s = $info->parent->add($r);
            } else {
                foreach ($row as $k) {
                    $r = igk_create_node($info->node->TagName);
                    $r->copyAttributes($info->node);
                    $r->Content = $k;
                    $s = $info->parent->add($r);
                }
            }
            break;
    }
    return true;
}
///<summary>Evaluate data by applying current row entries</summary>
///<remark>used to evaluate $value by replacing the current row data expression column</remark>
///<param name="value">expression to evaluate</param>
///<param name="row">current cibling row</param>
///<param name="ctrl">passed controller</param>
///<param name="ctx">context  if IGK_HTML_BINDING_EVAL_CONTEXT or null</param>
/**
 * Evaluate data by applying current row entries
 * @param mixed $value expression to evaluate
 * @param mixed $row current cibling row
 * @param mixed $ctrl passed controller
 * @param mixed $ctx context if IGK_HTML_BINDING_EVAL_CONTEXT or null
 */
function igk_html_treatbinding_evaldata($value, $raw, $ctrl = null, $ctx = null)
{
    igk_trace();
    igk_wln('evaluate data:', igk_html_wtag("textarea", $value, ["style" => "width:300px; height: 200px"]));
    return $value;
}
///<summary></summary>
///<param name="node"></param>
/**
 * 
 * @param mixed $node 
 */
function igk_html_treatinput($node)
{
    if ($node == null)
        return;
    $d = $node->getElementsByTagName("input");
    if ($d && (igk_count($d) > 0)) {
        foreach ($d as $k) {
            if (($k["class"] == null) && ($k["type"] != null)) {
                $k["class"] = "cl" . strtolower($k["type"]);
            }
        }
    }
}
///<summary>call this to unregister a callback node</summary>
/**
 * call this to unregister a callback node
 */
function igk_html_unreg_callback_node($sender)
{
    $p = $sender->getParentNode();
    $s = $sender->getParam(IGK_NAMED_ID_PARAM);
    if ($s && $p) {
        $t = $p->getParam(IGK_NAMED_NODE_PARAM);
        if (isset($t[$s])) {
            unset($t[$s]);
            $p->setParam(IGK_NAMED_NODE_PARAM, $t);
        }
    }
}
///unscape text area content
/**
 */
function igk_html_unscape($out)
{
    $reg = "/(?P<name>([\\][\"\'\\\\]))/i";
    $c = preg_match_all($reg, $out, $t);
    for ($i = 0; $i < $c; $i++) {
        switch ($t["name"][$i]) {
            case '\\"':
                $out = str_replace('\\"', '"', $out);
                break;
            case "\\'":
                $out = str_replace("\\'", '\'', $out);
                break;
            case '\\':
                $out = str_replace('\\', "\\", $out);
                break;
        }
    }
    return $out;
}
///<summary>unset template default attribute properties</summary>
/**
 * unset template default attribute properties
 */
function igk_html_unset_template_properties($node)
{
    $node["igk-data-binding"] = null;
    $node["igk-data-binding-visible-row"] = null;
    $node["igk-data-full-row-binding"] = null;
    $node["igk-data-full-row-binding-tag"] = null;
    $node["igk-data-row-checkexpression"] = null;
    $node["igk-data-row-binding"] = null;
    $node["igk-data-row-data"] = null;
    if ($t = igk_get_env("sys://template/bindingProperties")) {
        foreach ($t as $k) {
            $node[$k] = null;
        }
    }
}
///<summary>use preset</summary>
///<param name="p"></param>
/**
 * use preset
 * @param mixed $p 
 */
function igk_html_use($p)
{
    static $presets;
    if ($presets === null)
        $presets = [];
    if (!isset($presets[$p])) {
        $f = IGK_LIB_DIR . '/Data/presets/' . $p . '.html';
        if (file_exists($f)) {
            $presets[$p] = file_get_contents($f);
        }
    }
    return igk_getv($presets, $p, "preset view : " . $p);
}
///<summary>render on node type shortcut function</summary>
/**
 * render on node type shortcut function
 */
function igk_html_view($nodeType, $content)
{
    $n = igk_create_node($nodeType);
    $n->addObData($content);
    $n->renderAJX();
}
///<summary></summary>
///<param name="key"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $key 
 * @param mixed $msg 
 */
function igk_html_wln_log($key, $msg)
{
    $s = igk_wln_ob_get($msg);
    $d = igk_create_node();
    $d->setStyle("font-size:0.94em");
    $d->div()->Content = $key;
    $d->div()->setStyle("color: #ddd; background-color: #444")->addQuote()->Content = $s;
    $d->renderAJX();
}
///<summary></summary>
///<param name="cond"></param>
///<param name="msg"></param>
///<param name="tag" default="null"></param>
/**
 * 
 * @param mixed $cond 
 * @param mixed $msg 
 * @param mixed $tag 
 */
function igk_ilog_assert($cond, $msg, $tag = null)
{
    if ($cond) {
        igk_ilog($msg, $tag);
    }
}
///<summary></summary>
/**
 * 
 */
function igk_ilog_clear()
{
    $logfile = igk_ilog_file();
    igk_io_w2file($logfile, "", true);
}
///<summary>shortcut utilisty to dump variable: to log</summary>
/**
 * shortcut utilisty to dump variable: to log
 */
function igk_ilog_dump($o)
{
    igk_ilog(igk_ob_get_func("var_dump", $o));
}
///<summary></summary>
/**
 * helper: get Ilog file 
 */
function igk_ilog_file(): string
{
    return \IGKLog::GetSystemLogFile();
}
///<summary></summary>
/**
 * 
 */
function igk_ilog_get_trace()
{
    return igk_get_env("sys://ilog/trace");
}
///<summary></summary>
///<param name="trace" default="null"></param>
/**
 * 
 * @param mixed $trace 
 */
function igk_ilog_trace($trace = null)
{
    igk_set_env("sys://ilog/trace", $trace);
}
///<summary></summary>
///<param name="glue"></param>
///<param name="tab"></param>
///<param name="callback"></param>
///<param name="ignore" default="1"></param>
/**
 * 
 * @param mixed $glue 
 * @param mixed $tab 
 * @param mixed $callback 
 * @param mixed $ignore 
 */
function igk_implode($glue, $tab, $callback, $ignore = 1)
{
    $o = "";
    $g = 0;
    foreach ($tab as $k) {
        $s = $callback($k);
        if (empty($s) && $ignore)
            continue;
        if ($g)
            $o .= $glue;
        $o .= $s;
        $g = 1;
    }
    return $o;
}
///<summary>add article and treat image source as link</summary>
/**
 * add article and treat image source as link
 */
function igk_in_article($ctrl, $name, $target, $tagname = null)
{
    if ($target == null)
        igk_die("igk_in_article::target is null");
    $f = $ctrl->getArticle($name);
    if (file_exists($f)) {
        $uri = igk_io_baseuri() . "/" . igk_io_basepath($f);
        if ($tagname == null) {
            $target->Load(igk_io_read_allfile($f));
        } else {
            $s = $target->add($tagname);
            $s->Load(igk_io_read_allfile($f));
        }
    }
    $m = $target->getElementsByTagName("image");
    if ($m) {
        $dir = dirname(igk_io_basepath($f));
        foreach ($m as $v) {
            if (!IGKValidator::IsUri($v->lnk) && is_file(igk_dir($dir . "/" . $v->lnk))) {
                $v->lnk = igk_io_baseuri() . "/" . $dir . "/" . $v->lnk;
            }
        }
    }
    return null;
}
///<summary>include file: with params</summary>
///<param name='file'>full path </param>
/**
 * include file: with params
 * @param mixed $file full path 
 */
function igk_include($file, $params = null, $target = null)
{
    $__tfile = $file;
    $params = $params ?? igk_get_view_args();
    is_array($params) ? extract($params) : null;
    $file = $__tfile;
    unset($__tfile);
    if ($target)
        $t = $target;
    $args = get_defined_vars();
    if (isset($bindto)) {
        unset($args["bindto"]);
        $fc = function () use ($args, $file) {
            $args["context"] = "igk_include::callback";
            extract($args);
            if (file_exists($file)) {
                return include($file);
            }
        };
        $fc = $fc->bindTo($bindto);
        $fc();
    } else {
        $args["context"] = "igk_include::inline";
        extract($args);
        if (file_exists($file)) {
            return include($file);
        }
    }
}
///<summary>include file</summary>
/**
 * include file
 */
function igk_include_file($file, $args = null)
{
    if (!file_exists($file))
        return;
    if ($args) {
        foreach ($args as $k => $v) {
            $$k = &$args[$k];
        }
        unset($k, $v);
    }
    include($file);
}

/**
 * include if exists helpers
 */
function igk_include_if_exists()
{
    if (!file_exists(func_get_arg(0)))
        return null;
    if ((func_num_args() > 1) && func_get_arg(1))
        extract(func_get_arg(1));
    return include(func_get_arg(0));
}
///<summary>include on global context</summary>
///<param name="f">file to include</param>
///<param name="g">global argument</param>
/**
 * include on global context
 * @param mixed $f file to include
 * @param mixed $g global argument
 */
function igk_include_on_global($f, $g = null)
{
    extract($GLOBALS);
    include($f);
}
///<summary>include script in plugin lib</summary>
///<param name="file">mixed file or array </param>
///<param name="extra">preload script content </param>
///<param name="namespace">namespace to define</param>
/**
 * include script in plugin lib
 * @param mixed $file mixed file or array 
 * @param mixed $extra preload script content 
 * @param mixed $namespace namespace to define
 */
function igk_include_script($file, $namespace = null, $extra = null)
{
    if (is_string($file) && !file_exists($file))
        return;
    $n = "";
    $l = igk_get_env("sys://include/init") ?? igk_init_include();
    $tab = $file;
    if (!is_array($file)) {
        $tab = array($tab);
    }
    $to = [];
    while ($file = array_pop($tab)) {
        if ($namespace)
            $n = "namespace " . $namespace . "; {$extra} ?>" . igk_io_read_allfile($file);
        else
            $n = "?>" . "<?php return include(igk_realpath('{$file}')); ?>";
        igk_set_env(__FUNCTION__, $file);
        $o = @eval($n);
        igk_update_include($file);
        igk_set_env(__FUNCTION__, null);
        $to[] = $file;
    }
    return $to;
}
///<summary></summary>
///<param name="f"></param>
/**
 * 
 * @param mixed $f 
 */
function igk_include_set_view($f)
{
    $t = igk_get_env(IGKEnvironment::VIEW_INC_VIEW);
    if ($t == null) {
        $t = array();
    }
    $t[igk_dir($f)] = 1;
    igk_set_env(IGKEnvironment::VIEW_INC_VIEW, $t);
}
///<summary></summary>
///<param name="f"></param>
/**
 * 
 * @param mixed $f 
 */
function igk_include_unset_view($f)
{
    $t = igk_get_env(IGKEnvironment::VIEW_INC_VIEW);
    unset($t[$f]);
    igk_set_env(IGKEnvironment::VIEW_INC_VIEW, $t);
}
///<summary></summary>
/**
 * 
 */
function igk_include_utils()
{
    include_once(IGK_LIB_DIR . "/Inc/igk_utils.func.pinc");
}
///<summary>include view</summary>
///<param name="ctrl">the controller that will be used</param>
///<param name="target">target node that will recieve the content of the view</param>
///<param name="file">file path or relative name to view file</param>
///<param name="create">create file if not exists. default is false</param>
///<param name="args">array data to pass to view file </param>
/**
 * include view
 * @param mixed $ctrl the controller that will be used
 * @param mixed $target target node that will recieve the content of the view
 * @param mixed $file file path or relative name to view file
 * @param mixed $create create file if not exists. default is false
 * @param mixed $args array data to pass to view file 
 */
function igk_include_view($ctrl, $target, $file, $args = null, $create = false)
{
    if ($f = file_exists($file) ? $file : $ctrl->getViewFile($file)) {
        $s_args = igk_get_view_args();
        if ($s_args && !igk_is_included_view($f)) {
            $id = spl_object_hash($ctrl);
            if (!($c = igk_get_env(IGKEnvironment::CTRL_CONTEXT_SOURCE_VIEW_ARGS))) {
                $c = [];
            }
            if (!isset($c[$id])) {
                $c[$id] = $s_args;
                igk_set_env(IGKEnvironment::CTRL_CONTEXT_SOURCE_VIEW_ARGS, $c);
            }
        }
        igk_include_set_view($f);
        $ctrl->getViewContent($file, $target, $create, $args);
        igk_include_unset_view($f);
    }
}
///<summary>Represente igk_include_view_file function</summary>
///<param name="ctrl"></param>
///<param name="file"></param>
/**
 * Represente igk_include_view_file function
 * @param mixed $ctrl 
 * @param mixed $file 
 */
function igk_include_view_file($ctrl, $file, $no_cache = false)
{
    $args = array_slice(func_get_args(), 3);
    $cache = igk_cache()::view();
    $key = IGKEnvironmentConstants::VIEW_FILE_CACHES;
    igk_environment()->push($key, $file);

    $_bindfc = (function () {
        if ((func_num_args() >= 2) && (is_array(func_get_arg(1)))) {
            extract(func_get_arg(1));
        }
        // + | include view file.
        extract($this->getExtraArgs(), EXTR_SKIP);
        return include(func_get_arg(0));
    })->bindTo($ctrl);


    if ($no_cache) {
        array_unshift($args, $file);
    } else {
        $_f = $cache->getCacheFilePath($file);
        $_bindfc = BalafonCacheViewCompiler::GetBindViewCompilerHandler($ctrl);
        if ($cache->cacheExpired($file)) {
            // + | ---------------------------------------------------------------
            // + | Build cache view from article file 
            // + | 
            $output = BalafonCacheViewCompiler::Compile($ctrl, $file, $args);

            // $output2 = BalafonCacheViewCompiler::ViewCompile($ctrl, $file, $args);


            igk_io_w2file($_f, $output);
            // igk_io_w2file($_f, $output2);
        }
        array_unshift($args, $_f);
    }
    $response = null;
    try {
        $response = $_bindfc(...$args);
    } catch (TypeError $ex) {
        igk_dev_wln_e("fatal error: " . $ex->getMessage());
        throw $ex;
    } catch (Exception $ex) {
        if (!igk_environment()->no_handle_error && igk_environment()->isDev() && !defined("IGK_TEST_INIT")) {
            igk_ilog("INC VIEW ERROR:::" . $ex->getMessage());
            $rp = realpath(igk_environment()->last($key));
            $src = $ex->getFile();
            igk_wln_e(
                "<h2>INC VIEW ERROR</h2>" . $rp,
                "<div>" . $ex->getMessage() . "</div>",
                $rp == $ex->getFile() ? $ex->getFile() . ":" . $ex->getLine() : '',
                array_map(function ($e) use ($src) {
                    $file = igk_getv($e, "file");
                    $line = igk_getv($e, "line");
                    if ($src == $file) {
                        return "__CACHE__:" . basename($file) . "." . $line;
                    }
                    return implode(":", [empty($file) ? null : igk_io_collapse_path($file) . ':' . $line]);
                }, $ex->getTrace())
            );
        }
        ob_end_clean();
        throw $ex;
    } finally {
        igk_environment()->pop($key);
    }
    return $response;
}
///<summary></summary>
///<param name="dirname" default="null"></param>
/**
 * 
 * @param mixed $dirname 
 */
function igk_init_access($dirname = null)
{
    $v_access = "";
    if ($dirname == null)
        $dirname = IGK_APP_DIR;
    $v_access = $dirname . "/.htaccess";
    igk_io_save_file_as_utf8($v_access, igk_getbase_access(
        $dirname
    ), true);
}
///<summary>Represente igk_init_context_array_diff function</summary>
///<param name="args"></param>
/**
 * Represente igk_init_context_array_diff function
 * @param mixed $args 
 */
function igk_init_context_array_diff($args)
{
    igk_set_env("context/args", $args);
}
///<summary>init controller with a source creation listener</summary>
/**
 * init controller with a source creation listener
 * @var IIGKControllerInitListener $listener 
 */
function igk_init_controller(IIGKControllerInitListener $listener)
{
    $grantaccess = "allow from all";
    $denyaccess = 'deny from all';
    $listener->addDir(IGK_VIEW_FOLDER);
    $listener->addDir(IGK_ARTICLES_FOLDER);
    $listener->addDir(IGK_DATA_FOLDER);
    $listener->addDir(IGK_SCRIPT_FOLDER);
    $listener->addDir(IGK_STYLE_FOLDER);
    $listener->addDir(IGK_CONTENT_FOLDER);
    $listener->addDir(IGK_CONF_FOLDER);
    $listener->addDir(implode("/", [IGK_LIB_FOLDER, IGK_CLASSES_FOLDER]));
    $listener->addDir(implode("/", [IGK_LIB_FOLDER, IGK_TESTS_FOLDER]));
    $listener->addDir(IGK_CONF_FOLDER);
    $listener->addSource(IGK_DATA_FOLDER . "/.htaccess", $grantaccess);
    $listener->addSource(IGK_SCRIPT_FOLDER . "/.htaccess", $grantaccess);
    $listener->addSource(IGK_STYLE_FOLDER . "/.htaccess", $grantaccess);
    $listener->addSource(IGK_CONTENT_FOLDER . "/.htaccess", $denyaccess);
    $listener->addSource(IGK_LIB_FOLDER . "/.htaccess", $denyaccess);
    $listener->addSource(IGK_CONF_FOLDER . "/.htaccess", $denyaccess);
    $listener->addSource(IGK_STYLE_FOLDER . "/default.pcss", igk_get_default_style(), false);
}
///<summary>init html basic method</summary>
/**
 * init html basic method
 */
function igk_init_html_basic_method()
{
    die(__FUNCTION__);
}
///<summary></summary>
/**
 * 
 */
function igk_init_include()
{
    $functions = get_defined_functions()["user"];
    $classes = get_declared_classes();
    $source = igk_count($functions);
    $clcount = igk_count($classes);
    $t = array("funcs" => $source, "classes" => $clcount);
    igk_set_env("sys://include/init", $t);
    return $t;
}
///<summary></summary>
///<param name="path"></param>
/**
 * initialize module
 */
// function igk_init_module($path,  ?callable $init = null, $initialize = true)
// {
//     $k = "sys://modules/" . strtolower(str_replace("/", ".", igk_uri($path)));
//     $b = igk_get_env("sys://modules", array());
//     igk_debug_wln_e("the module loadsddd", $k, $b);

//     if (isset($b[$k]))
//         return $b[$k];
//     $dir = igk_dir(igk_get_module_dir() . "/{$path}");
//     if (!file_exists($dir))
//         return null;
//     $ob = new \IGK\Controllers\ApplicationModuleController($dir, $path);
//     $b[$k] = $ob;
//     igk_set_env("sys://modules", $b);
//     if ($initialize) {
//         if (!$init && (method_exists($ob, "initDoc") || $ob->supportMethod("initDoc")) && ($dc = igk_ctrl_current_doc())) {
//             $ob->initDoc($dc);
//         } else if ($init) {
//             $init($ob);
//         }
//     }
//     return $ob;
// }


///<summary>init user global info setting</summary>
/**
 * init user global info setting
 */
function igk_init_user_info()
{
    igk_user_set_info("TOKENID", "", "(.)+", 1, 1);
}
///<summary>init environment</summary>
///<param name="dirname">application directory</param>
/**
 * helper: init environment 
 * @param mixed $dirname application directory
 * @param IGKApp $app application instance
 */
function igk_initenv(string $dirname, IGKApp $app)
{
    return IGKAppSystem::InitEnv($dirname, $app);
}
///<summary></summary>
///<param name="zipfile"></param>
/**
 * 
 * @param mixed $zipfile 
 */
function igk_install_module($zipfile)
{
    throw new \IGK\System\Exceptions\NotImplementException(__FUNCTION__);
}
///<summary></summary>
/**
 * 
 */
function igk_internal_reslinkaccess()
{
    if (!igk_get_env($key = "sys://res/linkaccess")) {
        $fdir = igk_io_cacheddist_jsdir();
        $access = $fdir . "/.htaccess";
        $resolver = IGKResourceUriResolver::getInstance();
        $bck = igk_server()->REQUEST_URI;
        igk_server()->REQUEST_URI = "/";
        $access2 = $resolver->resolveOnly($access);
        if (!file_exists($access2)) {
            $resolver->resolve($access);
        }
        igk_server()->REQUEST_URI = $bck;
        igk_set_env($key, 1);
    }
}
///GLOBAL FUNCTION DEFINITION
///<summary>utility invalidate opcache</summary>
/**
 * utility invalidate opcache
 */
function igk_invalidate_opcache($f)
{
    if (function_exists("opcache_invalidate")) {
        @opcache_invalidate($f, true);
    }
}
///<summary>invoke a callback object</summary>
///<param name="bind">object that will be the host </param>
///<param name="bind">object to bind</param>
///<param name="extra">extra information</param>
/**
 * invoke a callback object
 * @param mixed $bind object that will be the host 
 * @param object $obj object to bind
 * @param mixed $extra extra information
 */
function igk_invoke_callback_obj($bind, $obj, $extra = null)
{
    if (is_callable($obj)) {
        if (Closure::class == get_class($obj)) {
            $fc = $obj->bindTo($bind);
            return call_user_func_array($fc, $extra ?? array());
        }
        igk_die(__FUNCTION__ . ": object is callable. Not allowed. " . get_class($obj));
    }

    switch ($obj->clType) {
        case "node":
            if (is_callable($obj->clFunc)) {
                if (igk_is_closure($obj->clFunc)) {
                    $tab = array_merge(array(), (array)$obj->clParam, $extra != null ? $extra : array());
                    $fc = $obj->clFunc->bindTo($bind);
                    return call_user_func_array($fc, $tab);
                }
                $tab = array($bind);
                $tab = array_merge($tab, (array)$obj->clParam, $extra != null ? $extra : array());
                return call_user_func_array($obj->clFunc, array_values($tab));
            }
            igk_ilog("/!\\ not a valid callable: ", __FUNCTION__);
            return false;
        case "func":
            if (is_callable($obj->clFunc)) {
                $tab = array_values(array_merge((array)$obj->clParam, $extra != null ? $extra : array()));
                if (igk_is_closure($obj->clFunc)) {
                    $fc = $obj->clFunc->bindTo($bind);
                    return call_user_func_array($fc, $tab);
                }
                if ($bind) {
                    array_push($tab, $bind);
                }
                return call_user_func_array($obj->clFunc, $tab);
            }
            igk_ilog("/!\\ not a valid callable: ", __FUNCTION__);
            return false;
        case "exp":
            if ($obj->clParam)
                extract($obj->clParam);
            $fc_args = igk_getv($obj->clParam, "func:args");
            igk_set_env(IGK_LAST_EVAL_KEY, $obj->clFunc);
            $o = @eval($obj->clFunc);
            igk_set_env(IGK_LAST_EVAL_KEY, null);
            return $o;
        case "file":
            if (class_exists("Closure")) {
                $ex = function () use ($bind, $obj) {
                    $this->_include_view($obj->clFile);
                    $func = $obj->clFunc;
                    ob_clean();
                    if (isset($func))
                        $func($bind, $obj);
                    igk_exit();
                };
                $f = $ex->bindTo($obj->Ctrl, $obj->Ctrl);
                $f();
            }
            break;
        default:
            igk_debug_wln("failed to send ");
            return false;
    }
    return false;
}
///<summary></summary>
/**
 * 
 */
function igk_invoke_export_callback()
{
    $func = igk_get_env("sys://export_callback");
    if ($func) {
        call_user_func_array($func, func_get_args());
    }
}
///<summary>invoke a function in the session id context</summary>
///<remark>the current session must be write first</remark>
/**
 * invoke a function in the session id context
 */
function igk_invoke_in_session($sid, $callback)
{
    $app = igk_app();
    igk_bind_session_id($sid);
    session_start();
    $o = $callback($app);
    igk_sess_write_close();
    return $o;
}
///<summary></summary>
///<param name="obj"></param>
///<param name="n"></param>
///<param name="k"></param>
///<param name="offset" default="1"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $n 
 * @param mixed $k 
 * @param mixed $offset 
 */
function igk_invoke_param($obj, $n, $k, $offset = 1)
{
    if (igk_is_callable($n)) {
        return call_user_func_array($n, array_slice($obj->clParam, $offset));
    }
    return call_user_func_array(array($n, $k), array_slice($obj->clParam, $offset));
}
///<summary>invoke pipe expression</summary>
/**
 * invoke pipe expression
 */
function igk_invoke_pipe($name, $value, $options = null)
{
    $loc_t = igk_reg_pipe(null);
    $s = $name;
    $args = [$value];
    if (($pos = strpos($s, ";")) !== false) {
        $exp = substr($s, $pos + 1);
        $s = substr($s, 0, $pos);
        $tab = igk_get_query_options($exp);
        if ($tab)
            $args[] = $tab;
    }
    $fc = igk_getv($loc_t, $s);
    $v = $value;
    if ($fc && igk_is_callable($fc)) {
        $v = igk_invoke_callback_obj(null, $fc, $args);
    }
    return $v;
}
///<summary></summary>
///<param name="f"></param>
///<param name="args" default="null"></param>
/**
 * 
 * @param mixed $f 
 * @param mixed $args 
 */
function igk_invoke_script($f, $args = null)
{
    $f = igk_dir($f);
    if (file_exists($f)) {
        $o = igk_get_path_exec(igk_io_path_ext($f)) . " " . $f;
        if ($args)
            $o .= " " . $args;
        $c = shell_exec($o);
        return $c;
    }
    return -1;
}
///<summary>Represente igk_invoke_session_event function</summary>
/**
 * Represente igk_invoke_session_event function
 */
function igk_invoke_session_event()
{
}
///<summary>from wordpress template edition</summary>
/**
 * from wordpress template edition
 */
function igk_invoke_widget_zone($name, $args = null)
{
}
///<summary>append to file</summary>
/**
 * append to file
 */
function igk_io_a2file($file, $content, $overwrite = true)
{
    return igk_io_save_file_as_utf8_wbom($file, $content, $overwrite, IGK_DEFAULT_FILE_MASK, "a+");
}
///<summary></summary>
/**
 * 
 */
function igk_io_access()
{
    return igk_get_env("sys://currenturiaccess", igk_io_baseuri());
}
///<summary>Represente igk_io_access_path function</summary>
///<param name="path"></param>
/**
 * resolv style access path
 * @param mixed $path 
 */
function igk_io_access_path($path)
{
    if (file_exists($path)) {
        return igk_io_expand_path(igk_io_collapse_path($path));
    }
    return null;
}
///<summary></summary>
///<param name="file"></param>
///<param name="data"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $data 
 */
function igk_io_append_to_file($file, $data)
{
    $r = @fopen($file, file_exists($file) ? "a+" : "w+");
    if ($r) {
        fwrite($r, $data);
        fclose($r);
    } else {
        igk_log_write_i("func:" . __FUNCTION__, "file " . $file . " can't be writed ");
    }
}
///<summary>return application data folder</summary>
/**
 * return application data folder
 */
function igk_io_applicationdatadir()
{
    return igk_io_applicationdir() . DIRECTORY_SEPARATOR . IGK_DATA_FOLDER;
}
///<summary>get path from assets</summary>
/**
 * get path from assets
 */
function igk_io_asset($path)
{
    return implode("/", ["", IGK_RES_FOLDER, ltrim($path, "/")]);
}
///<summary> parse base request uri info</summary>
/**
 *  parse base request uri info
 */
function igk_io_base_request_uri_info()
{
    $t = parse_url(igk_io_base_request_uri());
    return $t;
}
///<summary>get global base dir: </summary>
/**
 * get global base dir: 
 */
function igk_io_basedatadir($dir = null)
{
    return igk_dir(igk_io_basedir() . "/" . IGK_DATA_FOLDER . $dir);
}
///<summary> get if the base directory is equal to server document root</summary>
/**
 *  get if the base directory is equal to server document root
 */
function igk_io_basedir_is_root()
{
    $doc_root = igk_io_rootdir();
    $base_dir = igk_io_basedir();
    return $doc_root == $base_dir;
}
///<summary> get full current domain uri</summary>
/**
 *  get full current domain uri
 */
function igk_io_basedomainuri($secured = null)
{
    $app = igk_app();
    if (Server::IsLocal()) {
        $s = igk_io_baseuri();
    } else {
        $s = igk_sys_srv_uri_scheme() . "://" . igk_getv($app->Configs, "website_domain");
    }
    $s = igk_secure_uri($s, $secured);
    return $s;
}
///<summary></summary>
///<param name="dir"></param>
///<param name="basedir" default="null"></param>
///<param name="separator" default="DIRECTORY_SEPARATOR"></param>
/**
 * 
 * @param mixed $dir 
 * @param mixed $basedir 
 * @param mixed $separator 
 */
function igk_io_baserelativepath($dir, $basedir = null, $separator = DIRECTORY_SEPARATOR)
{
    if (empty($dir)) {
        return IGK_STR_EMPTY;
    }
    $dir = igk_uri($dir);
    $bdir = igk_uri($basedir == null ? igk_io_basedir() : $basedir);
    return igk_io_relativepath($dir, $bdir);
}
///<summary></summary>
///<param name="dir"></param>
/**
 * 
 * @param mixed $dir 
 */
function igk_io_baserelativeuri($dir)
{
    return igk_uri(igk_io_baserelativepath($dir));
}
///<summary></summary>
///<param name="dir"></param>
///<param name="secured" default="null"></param>
/**
 * 
 * @param mixed $dir 
 * @param mixed $secured 
 */
function igk_io_baseuri_i($dir, $secured = null)
{
    if (empty($dir))
        return IGK_STR_EMPTY;
    return igk_io_baseuri($dir, $secured);
}
///<summary>return cached distribution script</summary>
/**
 * return cached distribution script
 */
function igk_io_cacheddist_jsdir()
{
    return igk_io_cacheinfo()->js;
}
///<summary></summary>
/**
 * 
 */
function igk_io_cacheinfo()
{
    static $cache = null;
    if ($cache == null) {
        $dir = igk_io_cachedir();
        $cache = (object)array(
            "dir" => $dir,
            "js" => $dir . "/dist/js",
            "css" => $dir . "/dist/css",
            "img" => $dir . "/dist/img",
            "value" => $dir . "/dist/value"
        );
    }
    return $cache;
}
///io shortcut
///<summary> used to check if uri is directory. used internally </summary>
/**
 *  used to check if uri is directory. used internally 
 */
function igk_io_check_request_file($uri, $failedcallback = null)
{
    $c = ltrim($uri, '/');
    if (!empty($c)) {
        $bdir = igk_io_basedir();
        $dir = igk_dir(dirname(igk_io_basedir() . "/" . $c));
        if (($bdir != $dir) && is_dir($dir)) {
            igk_set_header(404);
            if ($failedcallback) {
                $failedcallback();
            }
            igk_exit();
        }
    }
}
///<summary>collapse system path</summary>
/**
 * helper: collapse system path
 */
function igk_io_collapse_path(string $str)
{
    return IO::CollapsePath($str);
}
/**
 * transform collapse path to constant 
 */
function igk_io_collapse_const_path(string $str, array $keys = ["%app%" => "IGK_APP_DIR"])
{
    if ($path = igk_io_collapse_path($str)) {
        foreach ($keys as $k => $v) {
            $path = str_replace($k, "[" . $v . "]|", $path);
        };
        return implode(" . ", array_map(function ($a) {
            if ($a[0] == "[") {
                $a = substr($a, 1, -1);
            }
            if (defined($a)) {
                return $a;
            }
            return escapeshellarg($a);
        }, explode("|", $path)));
    }
}
///<summary>combine path</summary>
///<param name="list*">list of string arguments</param>
/**
 * combine path
 * @param mixed $list* list of string arguments
 */
function igk_io_combine()
{
    $dir = igk_dir(implode(DIRECTORY_SEPARATOR, func_get_args()));
    if (strpos($dir, 'phar:') === 0)
        return igk_uri($dir);
    return $dir;
}
///<summary>return a controller working classes path</summary>
/**
 * return a controller working classes path
 */
function igk_io_controller_classes_lib_dir($ctrl)
{
    return igk_io_combine($ctrl->getDeclaredDir(), implode("/", [IGK_LIB_FOLDER, IGK_CLASSES_FOLDER]));
}
///<summary>return a controller working tests classes path</summary>
/**
 * return a controller working tests classes path
 */
function igk_io_controller_tests_lib_dir($ctrl)
{
    return igk_io_combine($ctrl->getDeclaredDir(), implode("/", [IGK_LIB_FOLDER, IGK_TESTS_FOLDER]));
}
///<summary>copy stream function</summary>
/**
 * copy stream function
 */
function igk_io_copy_stream($in, $out, $buffer = 4096, $close = 0)
{
    $size = 0;
    while (!feof($in)) {
        $size += fwrite($out, fread($in, $buffer));
    }
    if ($close) {
        fclose($in);
        fclose($out);
    }
    return $size;
}
///<summary>get corejs entry uri</summary>
/**
 * get corejs entry uri
 */
function igk_io_corejs_uri(): string
{
    return igk_io_baseuri(IGK_RES_FOLDER . "/" . IGK_SCRIPT_FOLDER . "/balafon.js?v=" . IGK_BALAFON_JS_VERSION);
}
///<summary></summary>
/**
 * 
 */
function igk_io_corestyle_uri(): ?string
{
    return Path::getInstance()->getStyleUri();
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_io_ctrl_db_dir($ctrl)
{
    $dir = $ctrl->getDataDir() . "/Database";
    if (!is_dir($dir) && (igk_io_createdir($dir) || igk_die("can't create directory"))) {
        igk_io_save_file_as_utf8_wbom($dir . "/.htaccess", "deny from all");
    }
    return $dir;
}
///<summary>utility to handle controller app uri</summary>
///<param name="ctrl">the controller</param>
///<param name="u">mixed . uri:string or SysParamInfo </param>
///<param name="fc">callback to call with request of func and parameters</param>
/**
 * utility to handle controller app uri
 * @param mixed $ctrl the controller
 * @param mixed $u mixed . uri:string or SysParamInfo 
 * @param mixed $fc callback to call with request of func and parameters
 */
function igk_io_ctrl_handle_uri($ctrl, $u, $fc)
{
    if (igk_app()->getControllerManager()->InvokeUri()) {
        igk_exit();
    }
    $p = "";
    $params = null;
    if (is_string($u)) {
        $p = igk_str_array_rm_empty(explode("/", explode("?", $u)[0]));
    } else {
        $params = $u->getQueryParams();
        return $fc($params["function"], $params["params"]);
    }
    return $fc(igk_getv($p, 0), array_slice($p, 1));
}
///<summary>return the current request uri according to IGK_APP_DIR</summary>
/**
 * return the current request uri according to IGK_APP_DIR
 */
function igk_io_current_request_uri()
{
    $rq = igk_io_request_uri();
    $doc_request = igk_io_doc_root_request_uri();
    $bdir = igk_io_basedir();
    if (!$bdir) {
        $lib = IGK_LIB_DIR;
        $root_dir = igk_uri(igk_io_rootdir());
        if (!empty($root_dir) && strpos($lib, $root_dir) === 0) {
            $t = explode('/', substr($lib, strlen($root_dir) + 1));
            $q = explode('/', ltrim(explode('?', $rq)[0], '/'));
            $c = 0;
            while (($m = array_shift($t)) && ($n = array_shift($q)) && ($m == $n)) {
                if ($c === 0)
                    $c = "";
                $c .= "/" . $m;
            }
            if ($c !== 0) {
                $rq = substr($rq, strlen($c));
            }
            return $rq;
        } else {
            return "/";
        }
    }
    return substr($doc_request, strlen($bdir));
}
///<summary>get base current domain uri</summary>
/**
 * get base current domain uri
 */
function igk_io_currentbasedomainuri()
{
    $n = igk_sys_srv_uri_scheme() . "://" . IGKSubDomainManager::GetBaseDomain();
    return $n;
}
///<summary></summary>
/**
 * 
 */
function igk_io_currentdomainuri()
{
    $n = igk_sys_srv_uri_scheme() . "://" . (igk_sys_is_subdomain() ? igk_sys_current_domain_name() : IGKSubDomainManager::GetBaseDomain());
    return $n;
}
///<summary>shortcut to IO::GetCurrentRelativePath</summary>
///<remark></remark>
///<param name="dir"> $dir : sever absolute path or basedir relative path</param>
///<param name="mustexists" default="1"> directery must exists</param>
///<param name="separator" default="DIRECTORY_SEPARATOR">directory separator</param>
/**
 * shortcut to IO::GetCurrentRelativePath
 * @param mixed $dir  $dir : sever absolute path or basedir relative path
 * @param mixed $mustexists  directery must exists
 * @param mixed $separator directory separator
 */
function igk_io_currentrelativepath($dir, $mustexists = 1, $separator = DIRECTORY_SEPARATOR)
{
    return IO::GetCurrentDirRelativePath($dir, $mustexists, $separator);
}
///<summary> shortcut to IO::GetCurrentRelativeUri</summary>
/**
 *  shortcut to IO::GetCurrentRelativeUri
 */
function igk_io_currentrelativeuri($dir = IGK_STR_EMPTY)
{
    return IO::GetCurrentRelativeUri($dir);
}
///<summary>return request base uri without query string</summary>
/**
 * return request base uri without query string
 */
function igk_io_currenturi()
{
    $s = IGK_STR_EMPTY;
    $rq_uri = igk_io_request_uri();
    if (!empty($rq_uri)) {
        $root = igk_str_rm_last(igk_io_rootdir(), '/');
        $bdir = igk_uri($root . "/" . IO::GetRootBaseDir());
        $fdir = igk_uri($root . igk_getv(explode("?", $rq_uri), 0));
        $s = igk_io_baseuri(substr($fdir, strlen($bdir)));
    }
    return $s;
}
///<summary></summary>
///<param name="dir"></param>
/**
 * helper
 * @param mixed $dir 
 */
function igk_io_cwdrelativepath($dir)
{
    return igk_io_get_relativepath($dir, getcwd());
}
///<summary></summary>
/**
 * 
 */
function igk_io_dir_level()
{
    return igk_get_env("sys://io/relative_dir_level", 0);
}

if (!function_exists('igk_dirname')) {
    /**
     * @return string  empty string in case of '.' | '/' 
     */
    function igk_dirname(string $dir)
    {
        if (empty($dir) || ($dir == '/') || (($dir = dirname($dir)) == '.')) {
            return '';
        }
        return $dir;
    }
}
///<summary>get child directories</summary>
/**
 * get child directories
 */
function igk_io_dirs($dir, $match = IGK_ALL_REGEX, $recursive = true, $ignoredname = null, &$ignored_dirs = null)
{
    $tab = array();
    $tq = array($dir);
    $fc = function () {
        return false;
    };
    if (is_string($ignoredname)) {
        $fc = function ($d, $m, $ignoredname) {
            return preg_match($ignoredname, $m);
        };
    } else if (is_array($ignoredname)) {
        $fc = function ($d, $m, $ignoredname) {
            return isset($ignoredname[$m]) || isset($ignoredname[$d]);
        };
    }
    while ($q = array_pop($tq)) {
        if (is_dir($q) && ($hdir = opendir($q))) {
            while ($f = readdir($hdir)) {
                if (($f == ".") || ($f == "..")) {
                    continue;
                }
                if (is_dir($c = igk_realpath($q . DIRECTORY_SEPARATOR . $f))) {
                    if ($fc($c, $f, $ignoredname)) {
                        $ignored_dirs[$c] = $c;
                        continue;
                    }
                    $tab[] = $c;
                    if ($recursive) {
                        array_push($tq, $c);
                    }
                }
            }
            closedir($hdir);
        }
    }
    return $tab;
}
///<summary>function used to serve file from view directory</summary>
/**
 * function used to serve file from view directory
 */
function igk_io_dispatch_file($dir, $params, $callback = null, $cacheout = 3600)
{
    if ((igk_count($params) > 0) && (file_exists($f = $dir . "/" . implode("/", $params))) && (!$callback || $callback($f))) {
        igk_header_content_file($f);
        igk_header_cache_output($cacheout);
        igk_zip_output(igk_io_read_allfile($f));
        igk_exit();
    }
    return 0;
}
///<summary>get the full document uri</summary>
/**
 * get the full document uri
 */
function igk_io_doc_root_request_uri()
{
    return igk_uri(igk_io_rootdir() . igk_io_request_uri());
}
///<summary>existing file path to root entry path</summary>
/**
 * existing file path to root entry path
 */
function igk_io_entry_path_uri($file)
{
    return igk_uri(igk_io_query_info()->entryuri . igk_io_basepath($file));
}
///<summary>return entry relative path from existing file</summary>
/**
 * return entry relative path from existing file
 */
function igk_io_entry_relative_path_uri($file)
{
    return (new IGKHtmlRelativeUriValueAttribute(igk_io_basepath($file)))->getValue();
}
///<summary> expand system path </summary>
/**
 *  expand system path 
 */
function igk_io_expand_path(string $str, $callback = null)
{
    if (preg_match("/^(\"|')/", $str)) {
        $str = substr($str, 1, -1);
    }
    $tab = igk_environment()->getEnvironmentPath();
    foreach ($tab as $k => $f) {
        if (is_null($f))
            continue;
        $str = str_replace($k, $f, $str);
    }
    if ($callback) {
        $str = $callback($str);
    }
    return $str;
}
///<summary></summary>
///<param name="type"></param>
/**
 * 
 * @param mixed $type 
 */
function igk_io_fileispicture($type)
{
    $t = array(
        "image/png" => 1,
        "image/jpeg" => 1,
        "image/svg+xml" => 1,
        "image/tiff" => 1,
        "image/gkds" => 1
    );
    return isset($t[$type]);
}
///<summary>force directory entry on view context</summary>
///<param name="ctrl">controller that will request</param>
///<param name="fname">view entry name path</param>
///<param name="redirect_request" ref="true"> retrieve the redirected uri</param>
/**
 * force directory entry on view context
 * @param mixed $ctrl controller that will request
 * @param mixed $fname view entry name path
 * @param mixed $redirect_request  retrieve the redirected uri
 */
function igk_io_force_dir_entry($ctrl, $fname, &$redirect_request = null)
{
    \IGK\Helper\ViewHelper::ForceDirEntry($ctrl, $fname, $redirect_request);
}
///<summary>return the full base uri</summary>
/**
 * return the full base uri
 */
function igk_io_fullbaserequesturi()
{
    return igk_uri(igk_str_rm_last(IO::GetRootUri(), "/") . "/" . igk_str_rm_last(igk_getv(explode('?', ltrim(igk_io_request_uri(), '/')), 0), '/'));
}
///<summary></summary>
///<param name="$c"></param>
///<param name="base" default="null" ref="true"></param>
/**
 * 
 * @param mixed $$c 
 * @param mixed $base 
 */
function igk_io_fullpath($c, &$base = null)
{
    $scheme = "";
    $base = $base ?? getcwd();
    $d = igk_uri($c);
    if (preg_match("/^(\/|[a-z]+:\/\/)/i", $d, $data)) {
        $scheme = $data[0];
        $d = substr($d, strlen($scheme));
    }
    $t = explode('/', $d);
    $b = [];
    foreach ($t as $k) {
        if (empty($k))
            continue;
        if ($k == '..') {
            if (count($b) > 0) {
                array_pop($b);
            } else {
                $base = dirname($base);
            }
        } else if ($k == '.') {
        } else {
            $b[] = $k;
        }
    }
    return $scheme . implode('/', $b);
}
///<summary>get the full uri of full path</summary>
/**
 * get the full uri of full path
 */
function igk_io_fullpath2fulluri($file)
{
    $root_uri = IO::GetRootUri();
    if ($ruri = IGKResourceUriResolver::getInstance()->resolve($file, null, 0)) {
        $s = $root_uri . "/" . str_replace("../", "", $ruri);
        return $s;
    }
    $s = igk_io_currentrelativepath($file);
    if (igk_io_is_subdir(IGK_APP_DIR, $file)) {
        $s = str_replace("../", "", igk_uri($s));
        return igk_uri($root_uri . "/" . $s);
    }
    $s = igk_uri(igk_get_current_base_uri() . "/" . $s);
    return $s;
}
///<summary>return the relative uri according to BASEDIR</summary>
/**
 * return the relative uri according to BASEDIR
 */
function igk_io_fullpath2uri($file, $img = false)
{
    $f = igk_io_currentrelativeuri(igk_io_baserelativepath($file));
    if ($img) {
        $f = igk_io_treat_lnk_referer($f);
    }
    return $f;
}
///<summary>convert full path to base directory</summary>
/**
 * convert full path to base directory
 */
function igk_io_fulluri2basedir($uri)
{
    $bdir = igk_io_baseuri();
    if (strstr($uri, $bdir)) {
        return igk_dir(igk_io_basedir() . "/" . substr($uri, strlen($bdir) + 1));
    }
    return null;
}
///<summary>get article in folder</summary>
/**
 * get article in folder
 */
function igk_io_get_article($name, $dir = null)
{
    return IO::GetArticleInDir($dir, $name);
}
///<summary></summary>
///<param name="name"></param>
///<param name="dir"></param>
///<param name="lang" default="null"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $dir 
 * @param mixed $lang 
 */
function igk_io_get_article_file($name, $dir, $lang = null)
{
    if ($lang == null) {
        $lang = R::GetDefaultLang();
    }
    $file = IGK_STR_EMPTY;
    if (preg_match(IGK_ARTICLE_TEMPLATE_REGEX, $name))
        $file = $dir . "/" . $name;
    else {
        $file = $dir . "/" . $name . "." . strtolower($lang) . "." . IGK_DEFAULT_VIEW_EXT;
        if (!file_exists($file) && file_exists($cf = $dir . "/" . $name . "." . IGK_DEFAULT_VIEW_EXT)) {
            $file = $cf;
        }
    }
    return $file;
}
///<summary>get application entry request uri</summary>
///<param name="uri">/uri according to view application view files</param>
/**
 * get application entry request uri
 * @param mixed $uri uri according to view application view files
 */
function igk_io_get_entry_uri($ctrl, $uri)
{
    $s = "";
    $appuri = $ctrl->getAppUri();
    $baseuri = igk_io_baseuri();
    $s = substr($appuri, strlen($baseuri));
    return $s . $uri;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="params"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $params 
 */
function igk_io_get_full_entry_uri($ctrl, $params)
{
    $e = igk_io_get_entry_uri($ctrl, "/" . implode("/", $params));
    return igk_io_baseuri($e);
}
///<summary></summary>
///<param name="uri" default="null"></param>
/**
 * get current relative in case $uri match existing folder
 * @param mixed $uri 
 */
function igk_io_get_relative_currenturi(?string $uri = null): ?string
{
    if (!is_null($uri) && strpos($uri, '#') == 0)
        $uri = "./" . $uri;
    $page = igk_app()->getCurrentPageFolder();
    $t = null;
    if (strtolower($page) != IGK_HOME_PAGEFOLDER) {
        $t = ($uri) ? IGK_STR_EMPTY . ($page) . "/" . $uri : $page;
    } else {
        if ($uri !== null)
            $t = $uri;
    }
    return $t;
}
///<summary>php://input data</summary>
/**
 * helper: retrieve uploaded data . \
 * environement : set RequestFakeJsonInput
 */
function igk_io_get_uploaded_data(bool $usefaker = true)
{
    if ($usefaker && ($input = igk_environment()->RequestFakeJsonInput())) {
        return $input->getRaw();
    }
    $fin = fopen('php://input', "r");
    if (!$fin)
        return 0;
    $buffsize = 4096;
    $s = "";
    while (($c = fread($fin, $buffsize))) {
        $s .= $c;
    }
    fclose($fin);
    return $s;
}
///<summary>retrieve the current uri</summary>
/**
 * retrieve the current uri
 */
function igk_io_get_uri()
{
    $s = igk_sys_srv_uri_scheme() . "://" . igk_server_name() . igk_io_request_uri();
    return $s;
}
///<summary>get a list of include files that have a BOM header. testing functions</summary>
/**
 * get a list of include files that have a BOM header. testing functions
 */
function igk_io_get_wbom_files()
{
    $i = 0;
    $t = array();
    foreach (get_included_files() as $v) {
        if (filesize($v) < 3)
            continue;
        $txt = igk_io_read_allfile($v);
        if ((ord($txt[0]) === 239) && (ord($txt[1]) === 187) && (ord($txt[2]) === 191)) {
            $i++;
            $t[] = $v;
            igk_wln($v);
        }
    }
    return $t;
}
///<summary></summary>
///<param name="dir"></param>
/**
 * 
 * @param mixed $dir 
 */
function igk_io_getconf_file($dir)
{
    return igk_dir($dir . "/" . IGK_DATA_FOLDER . "/" . IGK_CTRL_CONF_FILE);
}
///<summary></summary>
///<param name="dir"></param>
/**
 * 
 * @param mixed $dir 
 */
function igk_io_getdbconf_file($dir)
{
    return igk_dir($dir . "/" . IGK_DATA_FOLDER . "/" . IGK_SCHEMA_FILENAME);
}
///<summary>shortcut to get files from directory</summary>
///<param name="dir">directory</param>
///<param name="match">mixed, string regex expression or callback</param>
///<param name="recursive">recursive</param>
///<param name="excludir" ref="true" >list of directory to exclude</param>
/**
 * shortcut to get files from directory
 * @param mixed $dir directory
 * @param mixed $match mixed, string regex expression or callback
 * @param mixed $recursive recursive
 * @param mixed $excludir array<dir,1> directory to exclude. dir_name of full_dir_name to exclude
 */
function igk_io_getfiles($dir, $match = IGK_ALL_REGEX, $recursive = true, &$excludedir = null)
{
    return IO::GetFiles($dir, $match, $recursive, $excludedir);
}
///retourne le chemin complet . si chemin relatif fournit c'est le cwd qui intervient
/**
 */
function igk_io_getfullpath($path)
{
    return igk_realpath($path);
}
///<summary></summary>
///<param name="source"></param>
///<param name="target"></param>
/**
 * helper: get relative path
 * @param string $source source path to get relative path from. Note: must have a trailing '/' if consider as directory 
 * @param string $target path destination
 */
function igk_io_get_relativepath(string $source, string $target):?string
{
    return \IGK\System\IO\Path::GetRelativePath($source, $target);
}
///<summary></summary>
///<param name="file"></param>
///<param name="dir"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $dir 
 */
function igk_io_getviewname($file, $dir)
{
    $v_s = "";
    if ($h = strstr(igk_dir($file), $g = $dir)) {
        $h = igk_uri(substr($h, strlen($g) + 1));
        $dir = dirname($h);
        $v_s = (($dir != '.') ? $dir . "/" : "") . igk_io_basenamewithoutext(basename($h));
        unset($h);
        unset($g);
    } else
        $v_s = igk_io_basenamewithoutext($file);
    return $v_s;
}
///<summary></summary>
/**
 * 
 */
function igk_io_global_uri()
{
    return igk_get_env("sys://io/globaluri");
}
///<summary>shortcut to create new IGKHtmlRelativeUriValueAttribute</summary>
/**
 * shortcut to create new IGKHtmlRelativeUriValueAttribute
 */
function igk_io_html_link($file)
{
    return new IGKHtmlRelativeUriValueAttribute($file);
}
///<summary>get the web site full uri according to uri file pass form basedir</summary>
///<note>if you pass string "info" to this function and your website is http://www.igkdev.com the response will be
///http://www.igkdev.be/info
///</note>
/**
 * get the web site full uri according to uri file pass form basedir
 */
function igk_io_htmluri($uri = null)
{
    return igk_str_rm_last(igk_io_baseuri(), '/') . (($uri) ? "/" . igk_uri($uri) : IGK_STR_EMPTY);
}
///<summary></summary>
///<param name="dir"></param>
///<param name="match" default="IGK_ALL_REGEX"></param>
///<param name="recursive" default="true"></param>
///<param name="ignoredname" default="null"></param>
/**
 * 
 * @param mixed $dir 
 * @param mixed $match 
 * @param mixed $recursive 
 * @param mixed $ignoredname 
 */
function igk_io_idirs($dir, $match = IGK_ALL_REGEX, $recursive = true, $ignoredname = null)
{
    $idir = array();
    igk_io_dirs($dir, $match, $recursive, $ignoredname, $idir);
    return $idir;
}
///<summary></summary>
///<param name="uri"></param>
///<param name="render" default="1"></param>
/**
 * 
 * @param mixed $uri 
 * @param mixed $render 
 */
function igk_io_invoke_uri($uri, $render = 1)
{
    $uri = igk_str_rm_last($uri, "/");
    $v_buri = igk_io_baseuri();
    $s = strstr($uri, $v_buri);
    if (!$s)
        return false;
    $c = igk_sys_get_subdomain_ctrl($uri);
    $app = igk_app();
    $v_ruri = ltrim(substr($uri, strlen($v_buri)), "/");
    $tab = explode('?', $v_ruri);
    $p = igk_getv($tab, 0);
    $params = igk_getv($tab, 1);
    $page = "/" . $p;
    $actionctrl = igk_getctrl(IGK_SYSACTION_CTRL);
    igk_set_env("sys://io_invoke_uri", 1);
    igk_set_env("sys://no_render", !$render);
    if ($c !== false) {
        $k = "sys://env/state/subdomain";
        igk_push_env($k, $c);
        $k = IGK_REG_ACTION_METH;
        $pattern = igk_sys_ac_getpattern($k);
        $e = new IGKSystemUriActionPatternInfo(array(
            "action" => $k,
            "value" => $c->getRegInvokeUri(),
            "pattern" => $pattern,
            "uri" => $page,
            "keys" => igk_str_get_pattern_keys($k),
            "ctrl" => $c,
            "requestparams" => $params
        ));
        if ($actionctrl && ($c !== $actionctrl)) {
            if (!$c->NoGlobalAction && ($ce = $actionctrl->matche_global($page))) {
                try {
                    $ce->ctrl = null;
                    $actionctrl->invokeUriPattern($ce);
                } catch (Exception $e) {
                    igk_show_exception($e);
                    igk_exit();
                }
            } else {
                $actionctrl->invokeCtrlUriPattern($c, $e, false);
            }
        }
        igk_pop_env($k);
    } else {
        $app->getControllerManager()->InvokeUri($uri);
        if (!$actionctrl->handle_redirection_uri($actionctrl, $page, $params, 1, $render)) {
            $defctrl = igk_get_defaultwebpagectrl();
            if ($defctrl && method_exists($defctrl, "handle_redirection_uri")) {
                $defctrl->handle_redirection_uri($page);
            }
        }
    }
    igk_set_env("sys://no_render", null);
    igk_set_env("sys://io_invoke_uri", 0);
    return 1;
}
///<summary>check if there is a controller that override uri pattern</summary>
/**
 * check if there is a controller that override uri pattern
 */
function igk_io_is_ctrl_uri($uri = null, $file = 1, &$data = null)
{
    $uri = $uri ?? igk_io_base_request_uri();
    if ($file && igk_io_is_file(igk_io_basedir() . $uri)) {
        return 1;
    }
    $actionctrl = igk_getctrl(IGK_SYSACTION_CTRL);
    return $actionctrl && (($data = $actionctrl->matche($uri)) !== null);
}
///<summary></summary>
///<param name="f"></param>
/**
 * 
 * @param mixed $f 
 */
function igk_io_is_file($f)
{
    return file_exists($f) && !is_dir($f);
}
///<summary> check if full path</summary>
///<remark>path mus exists</remark>
/**
 *  check if full path
 */
function igk_io_is_fullpath($d)
{
    $k = igk_dir($d);
    $c = igk_realpath($k);
    if ($c) {
        return $k == $c;
    }
    $droot = igk_uri(igk_io_rootdir());
    $d = igk_uri($d);
    if (strpos(strtolower($d), strtolower($droot)) === 0) {
        return true;
    }
    return false;
}
///<summary></summary>
///<param name="uri" default="null"></param>
/**
 * 
 * @param mixed $uri 
 */
function igk_io_is_subdomain_uri($uri = null)
{
    $s = igk_io_subdomain_uri_name($uri);
    return !empty($s);
}
///<summary>Represente igk_io_joinpath function</summary>
///<param name="args"></param>
/**
 * Represente igk_io_joinpath function
 * @param mixed $args 
 */
function igk_io_joinpath(...$args)
{
    return igk_dir(implode(DIRECTORY_SEPARATOR, $args));
}
///<summary></summary>
///<param name="file"></param>
///<param name="options" default="null"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $options 
 */
function igk_io_libdiruri($file, $options = null)
{
    $target = substr($file, strlen(IGK_LIB_DIR) + 1);
    return igk_html_get_system_uri("/" . IGK_RES_FOLDER . "/_lib_/{$target}", $options);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="$c" ref="true"></param>
///<param name="p" ref="true"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $$c 
 * @param mixed $p 
 */
function igk_io_locate_view_file($ctrl, &$c, &$p)
{
    $d = $ctrl->getViewDir() . "/{$c}";
    $h = $p;
    if (is_dir($d)) {
        if (is_string($h)) {
            if (!preg_match("/\." . IGK_VIEW_FILE_EXT_REGEX . "/i", $h)) {
                if (!is_dir($c_d = $d . "/" . $h)) {
                    $h .= "." . IGK_DEFAULT_VIEW_EXT;
                }
            }
            if (file_exists($d . "/" . $h)) {
                $c = $c . "/" . $p;
                $p = array();
            }
        } else if (is_array($h)) {
            $cdir = $d;
            $ftype = 0;
            $ctype = 0;
            foreach ($h as $d) {
                $tfile = $cdir . "/" . $d;
                if (is_dir($tfile)) {
                    $cdir .= $tfile;
                    $ftype = 1;
                } else {
                    if (!preg_match("/\." . IGK_VIEW_FILE_EXT_REGEX . "/i", $tfile)) {
                        $tfile .= "." . IGK_DEFAULT_VIEW_EXT;
                    }
                    if (file_exists($tfile)) {
                        $cdir .= "/" . $d;
                        $ftype = 1;
                        $ctype++;
                    }
                    break;
                }
                $ctype++;
            }
            if ($ctype > 0) {
                $c .= "/" . implode("/", array_slice($h, 0, $ctype));
                $p = array_slice($h, $ctype);
            }
        }
    }
}
///<summary>move uploaded file to destination</summary>
/**
 * move uploaded file to destination
 */
function igk_io_move_uploaded_file($file, $destination)
{
    if (!move_uploaded_file($file, $destination)) {
        return false;
    }
    return true;
}
///<summary></summary>
///<param name="name"></param>
///<param name="dir"></param>
///<param name="pattern" default="pics_%d%"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $dir 
 * @param mixed $pattern 
 */
function igk_io_moveuploadedfiletodatafolder($name, $dir, $pattern = "pics_%d%")
{
    $img = igk_getv($_FILES, $name);
    if ($img) {
        if (IO::CreateDir($dir)) {
            if (!file_exists($dir . "/.htaccess")) {
                IO::WriteToFile($dir . "/.htaccess", "allow from all");
            }
            for ($i = 0; $i < igk_count($img["name"]); $i++) {
                if (igk_getv($img["error"], $i) == 0) {
                    $r = str_replace("%d%", $i, $pattern);
                    $f = $img["tmp_name"][$i];
                    $o = $dir . $r . "." . igk_io_path_ext($img["name"][$i]);
                    igk_io_move_uploaded_file($f, $o);
                }
            }
        }
    }
}
///<summary>Represente igk_io_packagesdir function</summary>
/**
 * Represente igk_io_packagesdir function
 */
function igk_io_packagesdir()
{
    return igk_get_packages_dir();
}
///<summary>protect the full request uri</summary>
/**
 * protect the full request uri
 */
function igk_io_protect_request($uri)
{
    $uri = rtrim($uri, "/");
    $buri = rtrim(igk_io_fullrequesturi(), "/");
    if ($buri !== $uri) {
        igk_navto($uri);
        igk_exit();
    }
}
///<summary></summary>
///<param name="uri"></param>
/**
 * 
 * @param mixed $uri 
 */
function igk_io_protect_request_ajx($uri)
{
    if (igk_is_ajx_demand()) {
        igk_create_node("script")->setContent("window.location.href='{$uri}';")->renderAJX();
        igk_exit();
    }
}
///<summary></summary>
///<param name="d"></param>
/**
 * 
 * @param mixed $d 
 */
function igk_io_push_request_uri($d)
{
    igk_set_env("sys://io/globaluri", $d);
}
///<summary> return the global primary query information</summary>
/**
 *  return the global primary query information
 */
function igk_io_query_info()
{
    $k = "sys://io/query/info";
    $v = igk_get_env($k, function () {
        $obj = igk_createObjStorage();
        $obj->base_uri = igk_io_baseuri();
        $obj->base_dir = igk_io_basedir();
        $obj->referer = igk_server()->HTTP_REFERER;
        $obj->query = igk_server()->QUERY_STRING;
        $obj->fname = null;
        $obj->ctrl = null;
        $obj->entryuri = igk_io_request_uri_path();
        $obj->root_uri = igk_io_root_entryuri();
        $obj->fullentry = igk_io_baseuri() . $obj->entryuri;
        if ($q = $obj->query) {
            $obj->fullentry .= '?' . $q;
        }
        if ($path_info = igk_server()->PATH_INFO) {
            $obj->params = $g = array_slice(explode("/", ($path_info)), 1);
        }
        return $obj;
    });
    return $v;
}
///<summary> return the query uri without GET QUERY ARGS</summary>
/**
 *  return the query uri without GET QUERY ARGS
 */
function igk_io_query_request_uri()
{
    $buri = igk_getv(explode("?", igk_io_request_uri()), 0);
    return $buri;
}
///<summary></summary>
///<param name="f"></param>
///<param name="endpattern"></param>
/**
 * 
 * @param mixed $f 
 * @param mixed $endpattern 
 */
function igk_io_read_header($f, $endpattern)
{
    if (!file_exists($f))
        return false;
    $hf = fopen($f, "r");
    $s = IGK_STR_EMPTY;
    while (($c = fread($hf, 1)) !== null) {
        $s .= $c;
        if (preg_match($endpattern, $s))
            break;
    }
    fclose($hf);
    return $s;
}
///<summary>get real path without resolving like realpath does</summary>
///<param name="$dir" type="string" ></param>
/**
 * get real path without resolving like realpath does
 * @param string $$dir 
 */
function igk_io_realpath($dir)
{
    return Path::getInstance()->realpath($dir);
}
///<summary>Represente igk_io_relativepath function</summary>
///<param name="spath"></param>
///<param name="link"></param>
/**
 * helper: get relative link
 * @param string $spath source path
 * @param string $link target path
 */
function igk_io_relativepath(string $path, string $link)
{
    return Path::getInstance()->relativepath($path, $link);
}
///<summary></summary>
///<param name="txt"></param>
/**
 * 
 * @param mixed $txt 
 */
function igk_io_remove_bom($txt)
{
    if ((strlen($txt) > 3) && (ord($txt[0]) === 239) && (ord($txt[1]) === 187) && (ord($txt[2]) === 191)) {
        return substr($txt, 3);
    }
    return $txt;
}
///<summary> use to remove empty line from file</summary>
/**
 *  use to remove empty line from file
 */
function igk_io_removeemptyline($file)
{
    if (!file_exists($file))
        return;
    $f = IO::ReadAllText($file);
    $o = igk_str_remove_empty_line($f);
    igk_io_save_file_as_utf8($file, $o, true);
}
///<summary> render resources file </summary>
/**
 *  render resources file 
 */
function igk_io_render_res_file($dir, $query, $second = 3600)
{
    if (preg_match("/^res\.(?P<lang>[^\.]+)\.(?P<ext>(e?js(on)?|txt|xml|bin|dat))$/i", basename($query), $tab)) {
        $t = substr($tab["lang"], 0, 2);
        $ext = strtolower($tab["ext"]);
        $ext = igk_getv(["ejson" => "json"], $ext, $ext);
        $rwf = igk_getr("rwf");
        $c1 = $dir . "/res." . $t . "." . $ext;
        if (!file_exists($c1)) {
            $c1 = igk_io_basedir() . dirname($query) . "/res." . $t . "." . $ext;
        }
        if (file_exists($c1)) {
            igk_clear_header_list();
            igk_header_set_contenttype($ext);
            igk_header_cache_output($second);
            igk_zip_output(igk_io_read_allfile($c1));
            igk_exit();
        }
    }
    igk_die(__("Resource not found"));
}
///<summary>get request uri entry according to base dir</summary>
///<note>sample: request_uri /local.com/data/sample/param
///sample: script_name = /local.com/index.php
///output: /data/sample/</note>
/**
 * get request uri entry according to base dir
 */
function igk_io_request_entry()
{
    $b = igk_io_request_uri();
    $t = igk_uri(dirname((($g = igk_server()->SCRIPT_NAME) ? $g : igk_server()->PHP_SELF)));
    $s = $b;
    if (strstr($b, $t)) {
        $s = "/" . ltrim(substr($b, strlen($t)), "/");
    }
    return urldecode($s);
}
///<summary>request for Firefox thumbnails demand</summary>
///<remark>controller can adapt their view to match that requirement</remark>
/**
 * request for Firefox thumbnails demand
 */
function igk_io_request_for_firefox_thumbnails()
{
    $b = false;
    if (strstr(igk_server()->HTTP_USER_AGENT, "Firefox")) {
        $b = igk_server()->HTTP_CACHE_CONTROL === 'no-cache' && igk_server()->HTTP_PRAGMA == 'no-cache';
    }
    return $b;
}
///<summary>alias to system SERVER : REQUEST_URI</summary>
/**
 * alias to system SERVER : REQUEST_URI
 */
function igk_io_request_uri()
{
    return igk_server()->REQUEST_URI ?? "/";
}
///<summary>get request uri path</summary>
/**
 * get request uri path
 */
function igk_io_request_uri_path()
{
    return igk_getv(parse_url(igk_io_request_uri()), "path");
}
///<summary>reset the query information</summary>
/**
 * reset the query information
 */
function igk_io_reset_query_info()
{
    igk_set_env("sys://io/query/info", null);
    return igk_io_query_info();
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="uri"></param>
///<param name="view" default=""></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $uri 
 * @param mixed $view 
 */
function igk_io_resolv($ctrl, $uri, $view = '')
{
    $str = str_replace("%appuri%", $ctrl->getAppUri(), $uri);
    $str = str_replace("%view%", $view, $str);
    return $str;
}
///<summary>resolve path</summary>
/**
 * resolve path
 */
function igk_io_resolvpath($file, $sourcedir, &$resolved = false): string
{
    die("not implement: " . __FUNCTION__);

    if (empty(strstr($file, $sourcedir))) {
        $tsourcedir = [$sourcedir];
        while (!$resolved && ($sourcedir = array_pop($tsourcedir))) {
            if ($hdir = opendir($sourcedir)) {
                while ($c = readdir($hdir)) {
                    if (($c == ".") || ($c == "..")) {
                        continue;
                    }
                    $mdir = $sourcedir . DIRECTORY_SEPARATOR . $c;
                    if (is_link($mdir) && !(empty($rp = realpath($mdir))) && ($v = strstr($file, $rp))) {
                        $file = $mdir . DIRECTORY_SEPARATOR . substr($file, strlen($rp) + 1);
                        $resolved = true;
                        break;
                    }
                    if (is_dir($mdir)) {
                        array_push($tsourcedir, $mdir);
                    }
                }
                closedir($hdir);
            }
        }
    }
    return $file;
}
///<summary>get the global resource folder</summary>
/**
 * get the global resource folder
 */
function igk_io_resourcesdir()
{
    return igk_io_combine(igk_io_basedir(), IGK_RES_FOLDER);
}
///<summary>retrieve root entry uri</summary>
/**
 * retrieve root entry uri
 */
function igk_io_root_entryuri()
{
    $dir = "";
    if (!igk_io_basedir_is_root()) {
        $sbdir = igk_uri(igk_io_basedir());
        $srdir = igk_uri(igk_io_rootdir());
        if (strstr($sbdir, $srdir)) {
            $child = substr($sbdir, strlen($srdir));
            $dir = $child . DIRECTORY_SEPARATOR . $dir;
        } else {
            return null;
        }
    } else {
        $dir = "/";
    }
    $cdir = igk_uri($dir);
    return $cdir;
}
///<summary>convert dir to uri from document root</summary>
/**
 * convert dir to uri from document root
 */
function igk_io_root_pathrequest($dir)
{
    return igk_uri(igk_io_root_entryuri() . igk_io_basepath($dir));
}
///<summary></summary>
/**
 * 
 */
function igk_io_root_uri()
{
    return IO::GetRootUri();
}
///<summary>return the dir from document root</summary>
/**
 * return the dir from document root
 */
function igk_io_rootbasedir(?string $dir = null)
{
    return IO::GetRootBaseDir($dir);
}
///<summary>return the root base request uri. starting with </summary>
///<remark > old function [igk_io_root_base_uri] rename to [igk_io_rootBaseRequestUri]</remark >
/**
 * return the root base request uri. starting with 
 */
function igk_io_rootbaserequesturi()
{
    $v_ruri = igk_io_base_request_uri();
    $tab = explode('?', $v_ruri);
    $uri = igk_getv($tab, 0);
    return "/" . $uri;
}
///<summary>server root directory </summary>
/**
 * return server root directory 
 * @return string server root directory 
 */
function igk_io_rootdir()
{
    return Path::getInstance()->getRootDir();
}
///<summary>get the fully request root request uri base on DocumentRoot with IGK_APP_DIR</summary>
/**
 * get the fully request root request uri base on DocumentRoot with IGK_APP_DIR
 */
function igk_io_rootrequesturi()
{
    $o = "";
    if (igk_io_basedir_is_root()) {
        $o = igk_io_baseuri() . igk_io_request_uri();
    } else {
        $k = substr(igk_io_doc_root_request_uri(), strlen(igk_io_basedir()));
        $o = igk_io_baseuri() . $k;
    }
    return $o;
}
///<summary></summary>
///<param name="filename"></param>
///<param name="content"></param>
///<param name="override" default="true"></param>
///<param name="transform" default="true"></param>
/**
 * 
 * @param mixed $filename 
 * @param mixed $content 
 * @param mixed $override 
 * @param mixed $transform 
 */
function igk_io_save_file_as_utf8($filename, $content, $override = true, $transform = true)
{
    $r = $transform ? igk_ansi2utf8(igk_str_encode_to_utf8($content)) : $content;
    return IO::WriteToFile($filename, $r, $override);
}
///<summary>shortcut to igk_io_save_file_as_utf8</summary>
/**
 * shortcut to igk_io_save_file_as_utf8
 */
function igk_io_save_file_as_utf8_wbom($filename, $content, $overwrite = true, $chmod = IGK_DEFAULT_FILE_MASK, $type = "w+")
{
    return \IGK\System\IO\FileWriter::Save($filename, $content, $overwrite, $chmod, $type);
}
///<summary></summary>
///<param name="id"></param>
///<param name="folder"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $id 
 * @param mixed $folder 
 * @param mixed $callback 
 */
function igk_io_save_posted_file($id, $folder, $callback)
{
    $f = igk_getv($_FILES, $id);
    $n = igk_getr(IGK_FD_NAME);
    if (($f["error"] == 0) && igk_qr_confirm()) {
        if (IO::CreateDir($folder)) {
            $destination = igk_dir($folder . "/" . $f["name"]);
            if (!file_exists($destination)) {
                if (!igk_io_move_uploaded_file($f["tmp_name"], $destination)) {
                    igk_notifyctrl()->addError("move upload file failed");
                } else {
                    $d = igk_uri(igk_io_basepath($destination));
                    igk_notifyctrl()->addMsgr("msg.FileAdded_1", $d);
                    $callback($n, $d);
                }
            } else {
                igk_notifyctrl()->addWarningr("warn.FileAlreadyExists");
                $d = igk_io_basepath($destination);
                $callback($n, $d);
                return 0;
            }
        } else {
            igk_notifyctrl()->addError("can't create a directory");
            return 0;
        }
    } else {
        igk_notifyctrl()->addErrorr("err.NotFileToAdd_1" . $f["error"]);
        return 0;
    }
    return 1;
}
///<summary></summary>
///<param name="file"></param>
///<param name="content"></param>
///<param name="overwrite" default="true"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $content 
 * @param mixed $overwrite 
 */
function igk_io_savecontentfromtextarea($file, $content, $overwrite = true)
{
    if (ini_get("magic_quotes_gpc")) {
        $content = stripcslashes($content);
    }
    return igk_io_save_file_as_utf8($file, $content, $overwrite);
}
///<summary>Represente igk_io_scandir function</summary>
///<param name="dir"></param>
/**
 * Represente igk_io_scandir function
 * @param mixed $dir 
 */
function igk_io_scandir($dir)
{
    if (!is_dir($dir)) {
        return [];
    }
    $tab = array_filter(scandir($dir), function ($v) {
        return (($v != ".") && ($v != ".."));
    });
    if (!$tab)
        $tab = [];
    $tab = array_map(
        function ($d) use ($dir) {
            return $dir . DIRECTORY_SEPARATOR . $d;
        },
        $tab
    );
    return $tab;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_io_set_dir_level($v)
{
    igk_set_env("sys://io/relative_dir_level", $v);
}
///<summary>store ajx uploaded data to folder</summary>
/**
 * store ajx uploaded data to folder
 */
function igk_io_store_ajx_uploaded_data($folder, $fname = null)
{
    $tab = igk_get_allheaders();
    if ((igk_getv($tab, "IGK_UPLOADFILE", false) == false) || !IO::CreateDir($folder)) {
        return false;
    }
    $fsize = igk_getv($tab, "IGK_UP_FILE_SIZE");
    $type = igk_getv($tab, "IGK_UP_FILE_TYPE", "text/html");
    $fname = $fname === null ? igk_getv($tab, "IGK_FILE_NAME", "file.data") : $fname;
    $bfname = igk_io_basenamewithoutext($fname);
    $of = igk_dir($folder . "/" . $fname);
    $v_gh = fopen("php://input", "r");
    $v_wh = fopen($of, "w");
    if ($v_wh) {
        igk_io_copy_stream($v_gh, $v_wh, 8192, 1);
        if (filesize($of) == $fsize) {
            return $of;
        }
        unlink($of);
        return null;
    } else {
        @fclose($v_gh);
    }
    return null;
}
///<summary></summary>
///<param name="file"></param>
///<param name="cnf"></param>
///<param name="tagname" default="config"></param>
///<param name="callback" default="null"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $cnf 
 * @param mixed $tagname 
 * @param mixed $callback 
 */
function igk_io_store_conf($file, $cnf, $tagname = "config", $callback = null)
{
    $d = igk_create_node($tagname);
    foreach ($cnf as $k => $v) {
        igk_conf_store_value($d, $k, $v);
    }
    $s = $d->render();
    if (igk_io_save_file_as_utf8($file, $s)) {
        if ($callback) {
            $callback();
        }
        return true;
    }
    return false;
}
///<summary>store base64 encoding data to outfile</summary>
/**
 * store base64 encoding data to outfile
 */
function igk_io_store_uploaded_base64($outfile, $data)
{
    if (empty($data))
        return 0;
    list($d, $v)
        = explode(";", $data);
    $g = base64_decode(trim(explode(",", str_replace(" ", "+", $v))[1]));
    return igk_io_save_file_as_utf8_wbom($outfile, $g, true);
}
///<summary></summary>
///<param name="file"></param>
/**
 * 
 * @param mixed $file 
 */
function igk_io_store_uploaded_file($file)
{
    if (!IO::CreateDir(dirname($file)))
        return 0;
    $fin = fopen("php://input", "r");
    if (!$fin)
        return 0;
    $fo = fopen($file, "w+");
    $buffsize = 4096;
    $r = 0;
    while (($c = fread($fin, $buffsize))) {
        fwrite($fo, $c, strlen($c));
        $r = 1;
    }
    fclose($fo);
    fclose($fin);
    if ($r == 0) {
        unlink($file);
        return 0;
    }
    return 1;
}
///<summary>Represente igk_io_sys_classes_dir function</summary>
/**
 * Represente igk_io_sys_classes_dir function
 */
function igk_io_sys_classes_dir()
{
    return IGK_LIB_DIR . "/" . IGK_LIB_FOLDER . "/" . IGK_CLASSES_FOLDER;
}
///<summary></summary>
///<param name="prefix"></param>
/**
 * 
 * @param mixed $prefix 
 */
function igk_io_sys_tempnam($prefix)
{
    return tempnam(sys_get_temp_dir(), $prefix);
}
///<summary>Represente igk_io_sys_test_classes_dir function</summary>
/**
 * Represente igk_io_sys_test_classes_dir function
 */
function igk_io_sys_test_classes_dir()
{
    return IGK_LIB_DIR . "/" . IGK_LIB_FOLDER . "/" . IGK_TESTS_FOLDER;
}
///<summary>return the system full path according to BASEDIR.</summary>
///<code>exemple: in window will return c://wamp/website/[$relativepath]</code>
///<summary>convert file to uri offline presentation </summary>
/**
 * return the system full path according to BASEDIR.
 * convert file to uri offline presentation 
 */
function igk_io_to_uri($f, $exist = 1)
{
    if (file_exists($f) || !$exist) {
        return "file:////" . igk_uri(igk_realpath($f));
    }
    return 0;
}
///<summary>shortcut to save file</summary>
///<param name="filename">file to touch</param>
///<param name="defaultContent" default="">default content</param>
/**
 * shortcut to save file
 * @param mixed $filename file to touch
 * @param mixed $defaultContent default content
 */
function igk_io_touch($filename, $content = '')
{
    return igk_io_save_file_as_utf8_wbom($filename, $content, 1);
}
///<summary></summary>
///<param name="f"></param>
/**
 * 
 * @param mixed $f 
 */
function igk_io_treat_lnk_referer($f)
{
    if (igk_is_ajx_demand()) {
        $str = trim(igk_getv(explode('?', igk_server()->HTTP_REFERER), 0));
        if (!empty($str) && !IGKString::EndWith($str, '/')) {
            if (strpos($f, '../') === 0)
                $f = substr($f, 3);
        }
    }
    return $f;
}
///<summary>return a unix presentation path</summary>
/**
 * return a unix presentation path
 */
function igk_io_unix_path($p)
{
    return str_replace("\\", "/", $p);
}
///<summary>unlink file if exists</summary>
/**
 * unlink file if exists
 */
function igk_io_unlink($file)
{
    if (file_exists($file)) {
        return @unlink($file);
    }
    return 0;
}
///<summary>determine that a local uri target a directory</summary>
/**
 * determine that a local uri target a directory
 */
function igk_io_uri_is_dir($uri)
{
    $buri = igk_io_baseuri();
    $s = strstr($uri, $buri);
    if (empty($s))
        return 0;
    $bdir = igk_str_rm_last(igk_io_basedir(), DIRECTORY_SEPARATOR);
    $b = igk_dir(str_replace($buri, $bdir, $uri));
    return is_dir($b);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="fname" default=""></param>
/**
 * 
 * @param mixed $ctrl 
 * @param string $filename 
 */
function igk_io_view_entry_uri($ctrl, string $filename = "")
{
    $c = $filename;
    if (!empty($filename)) {
        if (!($c = dirname($filename)))
            $c = $filename;
        if ($c == '.')
            $c = '';
    }
    return $ctrl->getAppUri($c);
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="fname" default=""></param>
/**
 * 
 * @param BaseController $ctrl 
 * @param string $fname 
 */
function igk_io_view_root_entry_uri(BaseController $ctrl, string $fname = ""): string
{
    $buri = igk_io_baseuri() ?? '';
    return substr(igk_io_view_entry_uri($ctrl, $fname), strlen($buri));
}
///<summary>get if is ajx demand</summary>
/**
 * get if is ajx demand
 */
function igk_is_ajx_demand()
{
    return !(igk_get_env(IGK_ENV_NO_AJX_TEST) == 1) &&
        (
            (igk_getv($headers = igk_get_allheaders(), "IGK_X_REQUESTED_WITH") || (igk_getv($headers, "X_REQUESTED_WITH") == "XMLHttpRequest"))
            || igk_server()->HTTP_IGK_AJX
        );
}
///<summary>Represente igk_is_ajx_form_request function</summary>
/**
 * Represente igk_is_ajx_form_request function
 */
function igk_is_ajx_form_request()
{
    return igk_getr("igk-ajx-form") == 1;
}
///<summary></summary>
///<param name="o"></param>
///<param name="k"></param>
/**
 * 
 * @param mixed $o 
 * @param mixed $k 
 */
function igk_is_array_key_present($o, $k)
{
    $t = is_object($o) ? (array)$o : (is_array($o) ? $o : null);
    if ($t == null)
        return false;
    if (!is_array($k))
        return false;
    foreach ($k as $s) {
        if (!isset($t[$s])) {
            return false;
        }
    }
    return true;
}
///<summary>check if the defined class is included as script evaluation object</summary>
/**
 * check if the defined class is included as script evaluation object
 */
function igk_is_class_included($classname)
{
    $c = igk_get_reg_class_file($classname);
    if ($c) {
        return true;
    }
    return false;
}
///<summary>check if an object is instance or subclass of a class</summary>
/**
 * check if an object is instance or subclass of a class
 */
function igk_is_class_instance_of($n, $class)
{
    return (get_class($n) == $class) || is_subclass_of($n, $class);
}
///<summary>Represente igk_is_class_subclass_of function</summary>
///<param name="o"></param>
///<param name="cl"></param>
/**
 * Represente igk_is_class_subclass_of function
 * @param mixed $object object to check 
 * @param mixed $cl 
 */
function igk_is_class_subclass_of($object, $cl)
{
    return (get_class($object) == $cl) || is_subclass_of($object, $cl);
}

if (!function_exists('igk_is_class_assignable')) {
    /**
     * check wether a class can be assign to a parent class
     */
    function igk_is_class_assignable(string $class_name, string $parent_class_name)
    {
        return ($class_name == $parent_class_name) || is_subclass_of($class_name, $parent_class_name);
    }
}
///<summary>get if an object is typeof closure</summary>
/**
 * get if an object is typeof closure
 */
function igk_is_closure($f)
{
    return is_callable($f) && is_object($f) && strtolower(get_class($f)) == "closure";
}
///<summary></summary>
/**
 * 
 */
function igk_is_conf_connected()
{
    return (($c = igk_getconfigwebpagectrl()) && $c->getIsConnected()) ||
        igk_server()->IGK_IS_CONF_CONNECTED;
}
///<summary></summary>
/**
 * 
 */
function igk_is_confpagefolder()
{
    return ($c = igk_getconfigwebpagectrl()) && ($c->getCurrentPageFolder() == IGK_CONFIG_PAGEFOLDER);
}
///<summary>check if a constant is present on tubestr</summary>
/**
 * check if a constant is present on tubestr
 */
function igk_is_const_defined($tubestr)
{
    $r = explode("|", $tubestr);
    foreach ($r as $v) {
        if (defined(trim($v)))
            return 1;
    }
    return false;
}
///<summary></summary>
///<param name="obj"></param>
/**
 * 
 * @param mixed $obj 
 */
function igk_is_controller($obj)
{
    return is_object($obj) && igk_reflection_class_extends($obj, IGK_ROOT_CTRLBASECLASS);
}
///<summary> check if an object is a controller</summary>
/**
 *  check if an object is a controller
 */
function igk_is_ctrl($ctrl)
{
    if (is_object($ctrl) && igk_reflection_class_extends(get_class($ctrl), BaseController::class)) {
        return true;
    }
    return false;
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_is_defaultwebpagectrl($ctrl)
{
    return igk_get_defaultwebpagectrl() === $ctrl;
}
///<summary>check if this is presently on design mode</summary>
/**
 * check if this is presently on design mode
 */
function igk_is_design_mode()
{
    return igk_get_env("sys://designmode") == 1;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_is_domain_name($n)
{
    return (strtolower($n) == "localhost") || (igk_get_domain_name($n) === $n);
}
///<summary></summary>
///<param name="t"></param>
///<param name="default" default=""></param>
/**
 * 
 * @param mixed $t 
 * @param mixed $default 
 */
function igk_is_empty($t, $default = '')
{
    if (empty($t)) {
        return $default;
    }
    return $t;
}
///<summary></summary>
///<param name="func"></param>
/**
 * 
 * @param mixed $func 
 */
function igk_is_function_disable($func)
{
    return in_array($func, explode(",", ini_get("disable_functions")));
}
///<summary>check if the node is html item</summary>
/**
 * check if the node is html item
 */
function igk_is_html($n)
{
    return is_object($n) && ($n instanceof HtmlItemBase);
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_is_html_node_overriding_view($n)
{
    if ($n instanceof XmlNode)
        return false;
    if (method_exists($n, $method = 'render') && !$n->NoOverride) {
        $cl = get_class($n);
        $v_minfo = new ReflectionMethod($n, $method);
        return ($v_minfo->{'class'} == $cl);
    }
    return false;
}
///<summary> check if the name if a valid identifier</summary>
/**
 *  check if the name if a valid identifier
 */
function igk_is_identifier($name)
{
    return preg_match(IGK_ISIDENTIFIER_REGEX, $name);
}
///<summary></summary>
///<param name="file"></param>
/**
 * 
 * @param mixed $file 
 */
function igk_is_included_view($file)
{
    return ($g = igk_get_env(IGKEnvironment::VIEW_INC_VIEW)) && isset($g[igk_dir($file)]);
}
///<summary>check for module presence</summary>
/**
 * check for module presence
 */
function igk_is_module_present($modulename)
{
    $v_k = "sys://require_mods";
    $g = igk_get_env($v_k, array());
    if (isset($g[$modulename]))
        return 1;
    return 0;
}
///<summary>determine that the request is coming from this local server</summary>
/**
 * determine that the request is coming from this local server
 */
function igk_is_srv_request()
{
    $tab = igk_get_allheaders();
    $s = (igk_server()->HTTP_HOST == igk_server_name() || die("server not match")) && ((igk_getv($tab, "IGK_SERVER") == IGK_PLATEFORM_NAME) || die("server ")) && ((igk_getv($tab, "IGK_CREF") == igk_app()->session->getCref()) || die("cref"));
    return $s;
}
///<summary>get if the current request is a thumnail request</summary>
/**
 * get if the current request is a thumnail request
 */
function igk_is_thumbnail_request()
{
    return !isset($_SERVER["HTTP_COOKIE"]) && (igk_server()->HTTP_CACHE_CONTROL == "no-cache") && (igk_server()->HTTP_PRAGMA == "no-cache");
}
///<summary>check if $uri command came from an uri request</summary>
/**
 * check if $uri command came from an uri request
 */
function igk_is_uri_demand($uri)
{
    return igk_uri_is_match(igk_io_currenturi(), $uri);
}
///<summary>Represente igk_is_valid_module_info function</summary>
///<param name="obj"></param>
/**
 * Represente igk_is_valid_module_info function
 * @param mixed $obj 
 */
function igk_is_valid_module_info($obj)
{
    if (!$obj)
        return false;
    return property_exists($obj, "name") && property_exists($obj, "author") && property_exists($obj, "version");
}
///<summary></summary>
/**
 * 
 */
function igk_is_webapp()
{
    return igk_server()->IS_WEBAPP == 1;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_is_xmlnode($n)
{
    return is_object($n) && igk_reflection_class_extends($n, IGK_HTML_ITEMBASE_CLASS);
}
///<summary> get a javascript link uri contain in a form </summary>
/**
 *  get a javascript link uri contain in a form 
 */
function igk_js_a_postform($uri)
{
    return "javascript: return (function(q){var f = window.igk.getParentByTagName(q, 'form');  if (f){f.action ='" . $uri . "'; f.submit();return false;}})(this);";
}
///<summary>get a link javascript a posturi</summary>
/**
 * get a link javascript a posturi
 */
function igk_js_ajx_aposturi($uri, $targetNodeId)
{
    return "javascript: window.igk.ajx.aposturi('" . $uri . "', '" . $targetNodeId . "');";
}
///<summary></summary>
///<param name="uri"></param>
///<param name="method" default="null"></param>
/**
 * 
 * @param mixed $uri 
 * @param mixed $method 
 */
function igk_js_ajx_post_auri($uri, $method = null)
{
    return "javascript: window.igk.web.a_posturi(this,'" . $uri . "', " . (($method == null) ? 'null' : $method) . ");";
}
///<summary></summary>
///<param name="uri"></param>
/**
 * 
 * @param mixed $uri 
 */
function igk_js_ajx_post_body_uri($uri)
{
    $funcd = "function(xhr){ if (this.isReady()){ this.replaceBody();}} ";
    return "javascript: \$ns_igk.ajx.post('" . $uri . "', null,  " . $funcd . "); return false; ";
}
///<summary>post a link uri</summary>
/**
 * post a link uri
 */
function igk_js_ajx_post_luri($parentTag)
{
    return "javascript: return window.igk.ajx.a_postResponse(this, '" . $parentTag . "');";
}
///<summary></summary>
///<param name="uri"></param>
/**
 * 
 * @param mixed $uri 
 */
function igk_js_ajx_postform_frame($uri)
{
    return "javascript:  \$ns_igk.ajx.postform(\$igk(this).getForm(), '" . $uri . "' , function(xhr){ if (this.isReady()){ \$ns_igk.ctrl.frames.appendFrameResponseToBody(xhr.responseText);  }});  return false;";
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_js_ajx_view_ctrl($ctrl)
{
    $id = $ctrl->TargetNode["igk-type-id"];
    igk_js_render_script("ns_igk.ajx.fn.getfortarget('" . $ctrl->getUri("ViewAJX") . "', ns_igk.ctrl.getCtrlById('" . $id . "'));");
}
///<summary></summary>
///<param name="folder"></param>
/**
 * 
 * @param mixed $folder 
 */
function igk_js_bind_script_folder($folder)
{
    $folder = igk_uri($folder);
    if (!is_dir($folder))
        return;
    if (!($tab = igk_environment()->get("ScriptFolder"))) {
        $tab = [];
    }
    if (!isset($tab[$folder])) {
        $tab[$folder] = igk_dir($folder);
    }
    igk_environment()->set("ScriptFolder", $tab);
}
///<summary>used to bind script for wui component</summary>
/**
 * used to bind script for wui component
 */
function igk_js_bind_wuiscript($document, $ctrl, $file, $node = null)
{
    $f = igk_realpath($ctrl->getScriptsDir() . "/" . $file);
    $document = $document ?? igk_get_last_rendered_document();
    $node = $node ?? igk_get_rendering_node();
    if (empty($f) || ($document == null) || $document->ScriptManager->isLoaded($f)) {
        return;
    }
    $src = igk_create_node("script");
    $src->setContent(igk_io_read_allfile($f));
    if (igk_is_ajx_demand()) {
        $src->renderAJX();
    } else {
        if ($node) {
            $node->add(new HtmlSingleNodeViewerNode($src));
        }
    }
    $document->addScript($f, 'temp');
}
///<summary>used to post frame to uri. used in href of <a></a> element </summary>
///<remark>if frame need to be shown used ajax mecanism</remark>
///<param name="ctrl"> controller to where response must be send in ajax syntax</param>
///<param name="uri"> request uri</param>
/**
 * used to post frame to uri. used in href of 
 * @param mixed $ctrl  controller to where response must be send in ajax syntax
 * @param mixed $uri  request uri
 */
function igk_js_ctrl_posturi($ctrl, $uri)
{
    $q = IGK_STR_EMPTY;
    if ($ctrl != null) {
        $q = ",'" . $ctrl->TargetNode["id"] . "'";
    } else
        $q = ",null";
    return "javascript:window.igk.ctrl.frames.postframe(this, '" . $ctrl->getUri($uri) . "&ajx=1'" . $q . ");";
}
///<summary>distribute js files </summary>
/**
 * distribute js files 
 */
function igk_js_dist_scripts($files)
{
    $out = "";
    $mergescallback = function ($file, $n = '') {
        $src = trim(str_replace("\"use strict\";", "", igk_js_minify(file_get_contents($file))));
        if (!empty($src)) {
            $header = "/*file:" . $n . "*/" . IGK_LF;
            echo $header . $src . IGK_LF;
        }
    };
    foreach ($files as $file) {
        if (!file_exists($file))
            continue;
        ob_start();
        $c = $mergescallback($file, igk_io_basepath($file));
        $output = ob_get_contents();
        ob_clean();
        $out .= $output . IGK_LF;
    }
    return $out;
}
///<summary>used to enable tinymce on element with textarea</summary>
/**
 * used to enable tinymce on element with textarea
 */
function igk_js_enable_tinymce($target, $elements = null, $op = null, $doc = null)
{
    if (!($mce = igk_require_module(\tinymce::class,  ActionHelper::Nothing(), true, 0))) {
        return;
    }
    $mce->initDoc($doc ?? igk_app()->getDoc());
    $element_txt = 'null';
    if ($elements) {
        if (is_array($elements)) {
            $element_txt = "{elements: \"" . implode(',', $elements) . "\"}";
        } else
            $element_txt = "{elements: \"" . $elements . "\"}";
    }
    $opt = "";
    if ($op) {
        $opt .= ", " . json_encode($op);
    }
    $script = <<<EOF
if (ns_igk.tinyMCE){
	ns_igk.tinyMCE.runOn({$element_txt}{$opt});
}
EOF;

    $sc = $target->add("script");
    $sc->Content = $script;
    return $sc;
}
///<summary></summary>
///<param name="target"></param>
///<param name="uri"></param>
///<param name="func" default="null"></param>
///<param name="saveState" default="false"></param>
/**
 * 
 * @param mixed $target 
 * @param mixed $uri 
 * @param mixed $func 
 * @param mixed $saveState 
 */
function igk_js_get_posturi($target, $uri, $func = null, $saveState = false)
{
    $func = ($func == null) ? "function(xhr){ if (this.isReady()){ this.setResponseTo(self);}}" : $func;
    $out = "javascript: (function(a){ var self = a; var q = igk.getParentById(self, '" . $target["id"] . "'); igk.ajx.post('" . $uri . "', null, " . $func . ", " . igk_parsebool($saveState) . "); })(this); ";
    return $out;
}
///<summary></summary>
/**
 * 
 */
function igk_js_get_temp_script_files()
{
    $h = igk_js_get_temp_script_host();
    if ($h) {
        return $h->targetNode->getParam("files");
    }
    return null;
}
///<summary>get tempory script host</summary>
/**
 * get tempory script host
 */
function igk_js_get_temp_script_host()
{
    return igk_get_env("sys://temp/script");
}
///<summary>init document to load</summary>
/**
 * init document to load
 */
function igk_js_init()
{
    $c = new \IGK\System\Html\Dom\HtmlScriptNode();
    $s = "";
    if (!igk_io_basedir_is_root()) {
        $s = "{baseuri:'" . igk_io_baseuri() . "'}";
    }
    $c->Content = new \IGK\System\Html\Dom\HtmlTextNode("if(window.ns_igk)ns_igk.init_document({$s});");
    $c->renderAJX();
}
///<summary>Represente igk_js_inline_text function</summary>
///<param name="msg"></param>
///<param name="attrib" default="1"></param>
/**
 * Represente igk_js_inline_text function
 * @param mixed $msg 
 * @param mixed $attrib 
 */
function igk_js_inline_text($msg, $attrib = 1)
{
    $msg = str_replace("\\", "\\\\", $msg);
    $msg = str_replace("'", "\\'", $msg);
    if ($attrib) {
        $msg = str_replace('"', '\\&quot;', $msg);
    }
    return "['" . implode("','", explode("\n", $msg)) . "'].join(String.fromCharCode(10))";
}
///<summary></summary>
///<param name="text"></param>
/**
 * 
 * @param mixed $text 
 */
function igk_js_lnk_confirm($text)
{
    return "if (ns_igk) ns_igk.form.confirmLink(this, '$text'); return false;";
}
///<summary></summary>
///<param name="doc"></param>
///<param name="dirname"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $dirname 
 */
function igk_js_load_found_script($doc, $dirname)
{
    $dirname = igk_dir($dirname);
    $tab = igk_environment()->get("ScriptFolder");
    if (($doc == null) || !is_dir($dirname) || isset($tab[$dirname])) {
        return;
    }
    $tdir = [$dirname];
    foreach (IO::GetFiles($dirname, "/\.js/") as $g) {
        $doc->addScript($g);
    }
    // while (($dirname = array_pop($tdir)) && ($hdir = @opendir($dirname))) {
    //     if (!$hdir) {
    //         igk_debug_wln("scripts dirname can not be opened [" . $dirname . "]");
    //         return false;
    //     }
    //     while ($fd = readdir($hdir)) {
    //         if (($fd == ".") || ($fd == ".."))
    //             continue;
    //         $f = $dirname . "/" . $fd;
    //         if (is_dir($f)) {
    //             if (($fd == IGK_SCRIPT_FOLDER)) {
    //                 igk_js_load_script($doc, $f);
    //             } else {
    //                 array_push($tdir, $f);
    //             }
    //         }
    //     }
    //     closedir($hdir);
    // }
    return true;
}
///<summary>load javascript document from directory</summary>
///<param name="doc">document where to load</param>
///<param name="dirname">target directory</param>
///<param name="tag">registration tag</param>
///<param name="regtype">registration type. use for building regex searching expression. 0|1 1= for all script</param>
/**
 * load javascript document from directory
 * @param mixed $doc document where to load
 * @param mixed $dirname target directory
 * @param mixed $tag registration tag
 * @param mixed $regtype registration type. use for building regex searching expression. 0|1 1= for all script
 * @deprecated load script ... to document not allowed 
 */
function igk_js_load_script(IGKHtmlDoc $doc, $dirname, $tag = 'priv', $regtype = 0)
{
    igk_die(__FUNCTION__);
    // igk_trace();
    // igk_wln_e("load script ....");
    // return;

    $reskey = "sys://res_files";
    $dirname = igk_uri($dirname);
    $tab = igk_get_env($key = "sys://js/loaded_folder", array());
    if (isset($tab[$dirname])) {
        return;
    }
    if (!($res_files = igk_get_env($reskey)))
        $res_files = [];

    $resolver = IGKResourceUriResolver::getInstance();
    $callback = function ($f) use ($resolver) {
        if (!igk_environment()->isOPS()) {
            $h = $resolver->resolve($f);
        }
    };

    $cache_path = IGKCaches::js_filesystem()->getCacheFilePath($dirname);

    if (file_exists($cache_path)) {
        $data = include($cache_path);
        foreach ($data as $f => $tag) {
            $f = igk_io_expand_path($f);
            $res_files[$f] = $tag;
            if (igk_io_path_ext($f) == "js") {
                $doc->addScript($f, $tag);
            } else {
                $callback($f);
            }
        }
    } else {
        $tab[$dirname] = 1;
        igk_set_env($key, $tab);
        $reg = "(^[^\.](.)*\.js$)";
        if ($regtype == 1) {
            $reg = "\.js$";
        }
        $dirname = igk_uri($dirname);
        if (!($tab = igk_environment()->get("ScriptFolder"))) {
            $tab = [];
        }
        if (($doc == null) || !is_dir($dirname) || isset($tab[$dirname])) {
            return;
        }
        $bregex = "/(" . $reg . "|\.(json|xml|xsl|html)$)/";
        $idir = igk_sys_js_ignore();
        if (isset($idir[$dirname])) {
            return false;
        }
        $rdir = $dirname;
        igk_js_bind_script_folder($rdir);


        $scriptFolder = array($dirname);
        $stag = "\"{$tag}\"";

        $m = "";
        while ($dirname = array_pop($scriptFolder)) {
            $hdir = @opendir($dirname);
            if (!$hdir) {
                igk_debug_wln("scripts dirname can not be opened [" . $dirname . "]");
                return false;
            }
            $f = null;
            $fd = null;
            while ($fd = readdir($hdir)) {
                if (($fd == ".") || ($fd == ".."))
                    continue;
                $f = $dirname . "/" . $fd;
                if (is_dir($f)) {
                    if (isset($idir[$f])) {
                        continue;
                    }
                    $scriptFolder[] = $f;
                } else {
                    if (preg_match($bregex, $bf = basename($f))) {
                        $res_files[$f] = $stag;
                        if (igk_io_path_ext($bf) == "js") {
                            $doc->addScript($f, $tag);
                        } else {
                            $callback($f);
                        }
                        $m .= "\"" . igk_io_collapse_path($f) . "\"=>{$stag},\n";
                    }
                }
            }
            closedir($hdir);
        }
        $src = igk_cache_array_content($m, $cache_path);
        $src .= "// + | cache for " . igk_io_collapse_path($rdir);
        igk_io_w2file($cache_path, $src);
    }
    igk_set_env($reskey, $res_files);
    return true;
}
///<summary>min javascript source</summary>
///<param name="compact">No compact char</param>
///<param name="nocompactchar">No compact char</param>
/**
 * min javascript source
 * @param mixed $compact No compact char
 * @param mixed $nocompactchar No compact char
 */
function igk_js_minify($s, $compact = 1, $nocompactchar = ' ')
{
    $count = strlen($s);
    $i = 0;
    $o = "";
    $m = 0;
    $stop = 0;
    $symbolspace = "=;()\{\}+-*:<>[]|, ";
    $symbols = "+-<>=\{\}(),|[]*%";
    $mark = '*';
    while ($i < $count) {
        $ch = $s[$i];
        $i++;
        if ($m == 2) {
            if ($ch == $stop) {
                $m = 0;
            }
            continue;
        }
        if ($m == 1) {
            if ($ch == "/") {
                $m = 2;
                $stop = IGK_LF;
                continue;
            }
            if ($ch == $mark) {
                $e = 0;
                $cm = "";
                while ($i < $count) {
                    $ch = $s[$i];
                    if ($ch == "/") {
                        if ($s[$i - 1] == $mark) {
                            $e = 1;
                            $cm = substr($cm, 0, strlen($cm) - 1);
                            break;
                        }
                    }
                    $cm .= $ch;
                    $i++;
                }
                if ($e)
                    $i++;
            } else {
                if (!preg_match_all("#(\||&|!|:|\+|=|-|\()\s*$#i", $o, $ctab)) {
                    $m = 0;
                    $o .= "/" . $ch;
                    continue;
                }
                $i--;
                $rgx = "/" . igk_str_read_brank($s, $i, "/", "/", null, 1);
                $i++;
                while (($i < $count) && (preg_match("/(g|i|m|u|y)/", $s[$i]))) {
                    $rgx .= $s[$i];
                    $i++;
                }
                $o .= $rgx;
            }
            $ch = "";
        }
        switch ($ch) {
            case "/":
                if (($m % 5) == 0) {
                    $m = 1;
                } else if ($m == 1) {
                    $m = 2;
                    $stop = IGK_LF;
                }
                break;
            case "\"":
            case "'":
                if (($m % 5) == 0) {
                    $i--;
                    $rgx = igk_str_read_brank($s, $i, $ch, $ch, null, 1);
                    $o .= $rgx;
                    $m = 4;
                    $i++;
                    $ch = "";
                }
                break;
            case "\t":
            case IGK_LF:
            case "\r":
                if ($m != 5) {
                    $m = 5;
                }
                break;
            case " ":
                if (($m % 5) == 0) {
                    $lni = strlen($o);
                    $_last = $lni > 0 ? $o[$lni - 1] : '';

                    if ((($lni > 0) && !empty($_last)) && ((!$compact && !empty($nocompactchar) && (strpos($nocompactchar, $_last) === false))
                        || (!empty($symbolspace) && strpos($symbolspace, $_last) === false)))
                        $o .= $ch;
                    // else
                    //     igk_ilog_assert(igk_is_debug(), ":::ignore space '" . $_last . "'");
                    $m = 5;
                }
                break;
            default:
                if ($m == 5) {
                    if ((($lni = strlen($o)) > 0) && (strpos($symbolspace, $o[$lni - 1]) === false))
                        $o .= " ";
                    else if ($compact && ($lni > 0) && ($o[$lni - 1] == ' ') && (strpos($symbols, $ch) !== false)) {
                        $o = substr($o, 0, $lni - 1);
                    } else {
                        $ct = strlen($o);
                        if (($ct > 0) && (strpos("+-", $o[$ct - 1]) !== false))
                            $o .= " ";
                    }
                }
                $m = 0;
                break;
        }
        if ($m == 0)
            $o .= $ch;
        if ($m == 4)
            $m = 0;
    }
    if ($m == 1)
        $o .= "/";
    return $o;
}
///<summary>shortcut to no script loading shortcut</summary>
/**
 * shortcut to no script loading shortcut
 */
function igk_js_no_autoload($dir)
{
    return igk_sys_js_ignore($dir);
}
///<summary></summary>
///<param name="m"></param>
/**
 * 
 * @param mixed $m 
 */
function igk_js_notify_danger($m)
{
    $d = igk_create_node($m);
    $d->setClass("igk-notify igk-notify-danger");
    $d->Content = $m;
    $d->renderAJX();
}
///<summary>get a javascript expression to from parent form variable to uri expression</summary>
///<args name="uri" >uri where to post form</args>
///<args name="jsfunc" >javascript function expression to pass</args>
/**
 * get a javascript expression to from parent form variable to uri expression
 */
function igk_js_post_form_uri($uri, $jsfunc = null)
{
    $c = IGK_STR_EMPTY;
    if ($jsfunc)
        $c .= "," . $jsfunc;
    return "javascript: window.igk.ajx.postform(this.form, '" . $uri . "' " . $c . "); ";
}
///<summary>used to get javascript uri component to post frame on the javascript context</summary>
///<param name="uri">string uri or object(Listener)</param>
/**
 * used to get javascript uri component to post frame on the javascript context
 * @param mixed $uri string uri or object(Listener)
 */
function igk_js_post_frame($uri, $ctrlid = null, $global = true)
{
    if (is_string($uri))
        return "javascript:" . igk_js_post_frame_cmd($uri, $ctrlid, $global);
    else {
        return new IGKJSPostFrameCmd($uri, $ctrlid, $global);
    }
}
///<summary>get the post frame javascript command</summary>
/**
 * get the post frame javascript command
 */
function igk_js_post_frame_cmd($uri, $ctrl = null, $global = true)
{
    $q = IGK_STR_EMPTY;
    if ($ctrl != null) {
        if (is_string($ctrl)) {
            $q = ",'" . $ctrl . "'";
        } else
            $q = ",'" . $ctrl->TargetNode["id"] . "'";
    } else
        $q = ",null";
    if ($global)
        $q .= ",true";
    else
        $q .= ",false";
    return "ns_igk.ctrl.frames.postframe(this, '" . $uri . "&ajx=1'" . $q . ");";
}
///<summary> get a javascript src that will post uri to server</summary>
/**
 *  get a javascript src that will post uri to server
 */
function igk_js_post_uri($uri, $jsfunc = null)
{
    $jsfunc = $jsfunc ? $jsfunc : "null";
    return "javascript: window.igk.ajx.post('" . $uri . "', null, {$jsfunc});";
}
///<summary>render history</summary>
/**
 * render history
 */
function igk_js_push_history_ajx($uri, $data = null)
{
    $n = igk_create_node("script");
    $n->Content = "igk.winui.history.push('{$uri}',{uri:'{$data}','src':'balafonjs'}, null);";
    $n->renderAJX();
}
///<summary></summary>
///<param name="u"></param>
/**
 * 
 * @param mixed $u 
 */
function igk_js_reg_global_script($u)
{
    if (file_exists($u)) {
        $tab = igk_get_env(IGK_ENV_GLOBAL_SCRIPT_KEY, array());
        $tab[igk_realpath($u)] = 1;
        igk_set_env(IGK_ENV_GLOBAL_SCRIPT_KEY, $tab);
        return true;
    } else {
        igk_wln("file not exists [$u]");
    }
    return false;
}
///<summary> render script node content</summary>
/**
 *  render script node content
 */
function igk_js_render_script($script)
{
    $s = igk_create_node("script");
    $s->setContent($script);
    $s->renderAJX();
}
///<summary>init history</summary>
/**
 * init history
 */
function igk_js_winui_init_history($t, $cn, $page = IGK_HOME, $src = IGK_BALAFON_JS)
{
    $id = igk_get_component_id($cn);
    if ($id) {
        $t->script()->Content = "igk.winui.history.push(null,{id:'{$id}', src:'{$src}', page:'{$page}'});";
    }
}
///<summary>* json response helper</summary>
///<param name="msg"></param>
///<param name="exit"></param>
/**
 * json response helper
 * @param mixed $msg message to json
 * @param mixed $exit 
 */
function igk_json($msg, $code = 200)
{
    $rep = new JsonResponse($msg, $code);
    return igk_do_response($rep);
}
///<summary>parse expression. multi json object expression</summary>
///<param name="exp">param or semi column expression</param>
/**
 * parse expression. multi json object expression
 * @param mixed $exp param or semi column expression
 */
function igk_json_array_parse($exp, &$err = null)
{
    throw new IGKException("Obsolete: " . __FUNCTION__);
}
///<summary>transform object to json string presentation</summary>
/**
 * transform object to json string presentation
 */
function igk_json_encode($t, $options = null)
{
    return json_encode($t, $options);
}
///<summary></summary>
///<param name="exp"></param>
/**
 * 
 * @param mixed $exp 
 */
function igk_json_expression($exp, $strict = true)
{
    return IGKCoreJSon::GetExpression($exp, $strict);
}
///<summary>Represente igk_json_expression_error function</summary>
/**
 * Represente igk_json_expression_error function
 */
function igk_json_expression_error()
{
    return igk_get_env("error://igk_json_expression");
}
///<summary>convert json string expression to object</summary>
/**
 * convert json string expression to object
 */
function igk_json_parse($expression, $strict = true)
{
    $k = "sys://volatile/instance/" . __FUNCTION__;
    $r = igk_get_env($k);
    if (!$r) {
        $r = new IGKCoreJSon();
        igk_set_env($k, $r);
    }
    return $r->ToDictionary($expression, $strict);
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_json_readarray($v)
{
    throw new IGKException("Obsolete:" . __FUNCTION__);
}
///<summary>transform the reading string value to php entity data</summary>
/**
 * transform the reading string value to php entity data
 */
function igk_json_value($v)
{
    if (is_numeric($c = trim($v))) {
        if (is_scalar($v))
            return floatval($v);
        return intval($v);
    }
    if ($v == 'null') {
        return null;
    }
    return $v;
}
///<summary>default key sort callback</summary>
/**
 * default key sort callback
 */
function igk_key_sort($k, $v)
{
    return strcmp($k, $v);
}
///<summary>destroy all stored session</summary>
///<note>administrative function</note>
/**
 * destroy all stored session
 */
function igk_kill_all_sessions($exclude = null, &$outtab = null)
{
    $d = ini_get("session.save_path");
    $i = 0;
    if (!empty($d) && is_dir($d)) {
        $outtab = $outtab ?? array();
        igk_sess_write_close();
        $_SESSION = array();
        $_COOKIE = array();
        $f = IO::GetFiles($d, "/^(.)+$/i", false);
        if ($f) {
            foreach ($f as $v) {
                if ($exclude && (preg_match("#" . $exclude . "$#i", basename($v)))) {
                    continue;
                }
                $s = @filesize($v);
                if ($s) {
                    $outtab[$v] = $s;
                    unlink($v);
                }
                $i++;
            }
        }
    }
    return $i;
}
///<summary></summary>
/**
 * 
 */
function igk_kill_trace()
{
    $file = igk_io_basedir() . "/Data/.trace";
    if (file_exists($file))
        unlink($file);
}
///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab 
 */
function igk_last($tab)
{
    if (($i = igk_count($tab)) > 0)
        return $tab[$i - 1];
    return null;
}
///<summary> force loading class</summary>
/**
 *  force loading class
 */
function igk_load_class($classname)
{
    return igk_load_classes([$classname]);
}
///<summary>Represente igk_load_classes function</summary>
///<param name="tab" default="[[]"></param>
/**
 * Represente igk_load_classes function
 * @param mixed $tab 
 */
function igk_load_classes($tab = [])
{
    if (is_string($tab)) {
        $tab = [$tab];
    }
    list($major, $minor)
        = explode(".", PHP_VERSION);
    $resolv_class = ["." . $major . "." . $minor, "." . $major, ""];
    $cdir = igk_io_sys_classes_dir();
    while (($classname = array_shift($tab)) !== null) {
        if (!class_exists($classname, false)) {
            $n = $classname;
            $f = igk_uri($n);
            if (strpos($f, "IGK/") === 0) {
                $f = substr($f, 4);
            }
            foreach ($resolv_class as $version) {
                if (file_exists($cf = igk_uri($cdir . "/" . $f . "{$version}.php")) || (!empty($version) && file_exists($cf = igk_uri($cdir . "/{$version}/" . $f . "php")))) {
                    require_once($cf);
                    if (!class_exists($n, false) && !interface_exists($n, false) && !trait_exists($n, false)) {
                        igk_trace();
                        igk_die("file {$cf} loaded but not content class|interface|trait {$n} definition", 1, 500);
                    }
                    break;
                }
            }
        }
    }
    return true;
}
///<summary>helper: load controllers </summary>
///<param name="$dirname">root directory</param>
/**
 * helper: load controllers 
 * @param mixed $dirname root directory 
 */
function igk_loadcontroller(string $dirname)
{
    return igk_loadlib($dirname, ".php");
}
///<summary></summary>
///<param name="uri"></param>
/**
 * helper: load request from uri
 * @param mixed $uri 
 */
function igk_loadr(string $uri)
{
    if (count($uri) == 0)
        return;
    $tab = array();
    $t = parse_url($uri);
    $q = $t["query"];
    parse_str($q, $tab);
    $_REQUEST = $tab;
}
///<summary>shurtcut to igk_log_append</summary>
/**
 * shurtcut to igk_log_append
 */
function igk_log($msg, $file, $tag = null)
{
    igk_log_append($file, $msg, $tag);
}
///<summary>append log to file</summary>
/**
 * append log to file
 */
function igk_log_append($file, $msg, $tag = IGK_LOG_SYS)
{
    $s = date("d-m-Y H:i:s:");
    if ($tag) {
        $s .= "[{$tag}] - ";
    }
    if ($query = igk_server()->REQUEST_URI)
        $s .= " " . $query . " ";
    igk_set_env("igk_log_var_dump", "text");
    if ($msg && is_array($msg) || is_object($msg)) {
        $msg = (function () use ($msg) {
            $s = (IGK_LF);
            foreach ($msg as $k => $v) {
                $s .= $k . " : ";
                if (is_object($v) || is_array($v)) {
                    $s .= igk_ob_get_func("igk_log_var_dump", array($v));
                } else {
                    $s .= $v;
                }
                $s .= IGK_LF;
            }
            return $s;
        })();
    }
    igk_set_env("igk_log_var_dump", null);
    $s .= $msg . "\r" . IGK_LF;
    if (!file_exists($file)) {
        igk_io_save_file_as_utf8_wbom($file, $s, true, 0775);
    } else {
        if ($r = @fopen($file, "a+")) {
            fwrite($r, $s);
            fclose($r);
        }
    }
}
///<summary></summary>
/**
 * 
 */
function igk_log_enabled()
{
    return igk_const_defined("IGK_WRITE_LOG") || Server::IsLocal();
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_log_write($msg)
{
    if (!igk_log_enabled())
        return;
    $s = igk_getctrl(IGK_LOG_CTRL);
    if ($s) {
        $s->write($msg);
    }
}
///<summary></summary>
///<param name="tag"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $tag 
 * @param mixed $msg 
 */
function igk_log_write_i($tag, $msg)
{
    if (!igk_log_enabled())
        return;
    $s = igk_getctrl(IGK_LOG_CTRL);
    if ($s) {
        $s->write_i($tag, $msg);
    } else {
        IGKLog::getInstance()->write_i($tag, $msg);
    }
}
///<summary></summary>
///<param name="tag"></param>
///<param name="data"></param>
/**
 * 
 * @param mixed $tag 
 * @param mixed $data 
 */
function igk_log_write_i_data($tag, $data)
{
    if (!igk_log_enabled())
        return;
    $s = igk_getctrl(IGK_LOG_CTRL);
    if ($s) {
        $s->write_i_data($tag, $data);
    } else {
        IGKLog::getInstance()->write_i_data($tag, $data);
    }
}
///<summary></summary>
///<param name="msg"></param>
///<param name="title" default="admin mail"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $title 
 */
function igk_mail_admin_send($msg, $title = "admin mail")
{
    $app = igk_app();
    $mail = $app->Configs->mail_admin;
    $domain = $app->App->Configs->website_domain;
    igk_mail_sendmail($mail, "no-reply@" . $domain, $title, $msg, null);
}
///<summary>return the system's @from mail </summary>
///<param name="ctrl">controller to get system mail</param>
/**
 * return the system's @from mail 
 * @param mixed $ctrl controller to get system mail
 */
function igk_mail_from($ctrl = null)
{
    $m = null;
    if ($ctrl && (!$m = $ctrl->Configs->mailSystem)) {
    }
    if (!$m)
        $m = igk_mail_noreply_address();
    return $m;
}
///<summary>get mail no reply address</summary>
/**
 * get mail no reply address
 */
function igk_mail_noreply_address()
{
    return igk_sys_getconfig("mail_noreply", "no-reply@" . igk_sys_getconfig("website_domain"));
}
///<summary>Represente igk_mail_option function</summary>
/**
 * Represente igk_mail_option function
 */
function igk_mail_option()
{
    $opt = new \IGK\System\Net\Mail\MailRendererOptions();
    HtmlRenderer::InitRendererOption($opt);
    return $opt;
}
///<summary>send mail to from</summary>
///<return>false if mail controller not found or sending mail failed</return>
/**
 * send mail to from
 */
function igk_mail_sendmail($to, $from, $title, $message, $replyto = null, $attachement = null, $type = "text/html")
{
    $mail_ctrl = igk_getctrl(IGK_MAIL_CTRL);
    if ($mail_ctrl) {
        return $mail_ctrl->sendmail($from, $to, $title, $message, $replyto, $attachement, $type);
    }
    return false;
}
///<summary>utility function used to split string</summary>
/**
 * utility function used to split string
 */
function igk_mail_split_str($s, $ln = 75, $sep = IGK_LF)
{
    $n = "";
    while (!empty($s)) {
        if (!empty($n))
            $n .= $sep;
        $h = substr($s, 0, $ln);
        $s = substr($s, strlen($h));
        $n .= $h;
    }
    return $n;
}
///<summary>get mail style sheet</summary>
/**
 * get mail style sheet
 */
function igk_mail_stylesheet()
{
    $s = HtmlNode::CreateWebNode("style");
    $s["type"] = "text/css";
    $f = igk_io_currentrelativepath(igk_sys_getconfig("mail_style_sheet", igk_io_basepath(igk_dir(IGK_LIB_DIR . "/Default/" . IGK_STYLE_FOLDER . "/mail.css"))));
    if (file_exists($f)) {
        $v_s = igk_str_remove_lines(IO::ReadAllText($f));
        $v_s = igk_css_treat($v_s, true, igk_app()->getDoc()->getTheme());
        $s->setContent($v_s);
    } else {
        igk_debug_wln(getcwd());
        igk_debug_wln("file not exists " . $f);
    }
    return $s->render();
}
///<summary>Represente igk_map_array_to_str function</summary>
///<param name="tab"></param>
/**
 * Represente igk_map_array_to_str function
 * @param mixed $tab 
 */
function igk_map_array_to_str($tab, $usekey = true)
{
    $m = "";
    array_map(
        function ($v, $k) use (&$m, $usekey) {
            if ($usekey) {
                if (is_numeric($k)) {
                    $m .=  $k . '=>';
                } else {
                    $m .= '"' . $k . '"=>';
                }
            }
            if (is_numeric($v)) {
                $m .= $v;
            } else if (is_string($v)) {
                $m .= '"' . $v . '"';
            } else {
                if ($v === null) {
                    $m .= "null";
                } else {
                    if (is_array($v)) {
                        $m .= json_encode($v);
                    } else {
                        $m .= '"' . $v . '"';
                    }
                }
            }
            $m .= ",\n";
        },
        $tab,
        array_keys($tab)
    );
    return $m;
}
///retrive menu by name
/**
 */
function igk_menu_getmenu($name)
{
    return igk_getctrl(IGK_MENU_CTRL)->getMenu($name);
}
///get the root menu of this item
/**
 */
function igk_menu_getrootmenu($name)
{
    return igk_getctrl(IGK_MENU_CTRL)->getRootMenu($name);
}
///<summary></summary>
/**
 * 
 */
function igk_menu_getroots()
{
    return igk_getctrl(IGK_MENU_CTRL)->getRoots();
}
///<summary></summary>
///<param name="tab"></param>
///<param name="level" default="1"></param>
///<param name="auth"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $level 
 * @param mixed $auth 
 */
function igk_menu_option_i($tab, $level = 1, $auth = 0)
{
    $o = new StdClass();
    if (is_array($tab)) {
        $o->key = igk_getv($tab, "key");
        $o->level = igk_getv($tab, "level");
    } else {
        $o->key = $tab;
        $o->level = $level;
    }
    $o->auth = $auth;
    return $o;
}
///<summary>return font code</summary>
///<summary>navigate to session redirection parameter</summary>
/**
 * return font code
 * navigate to session redirection parameter
 */
function igk_nav_session()
{
    $s = ($rf = igk_server()->HTTP_REFERER) ? $rf : igk_get_session(IGKSession::IGK_REDIRECTION_SESS_PARAM);
    if ($s) {
        ob_clean();
        igk_navto($s);
    }
    igk_navtocurrent();
}
///<summary></summary>
///<param name="uri"></param>
///<param name="headerStatus" default="null"></param>
/**
 * 
 * @param mixed $uri 
 * @param ?int $headerStatus code
 */
function igk_navto($uri, ?int $headerStatus = null)
{
    //  if (igk_environment()->isDev()){
    // igk_trace();
    // igk_wln_e("nav to ".$uri);
    //}
    if (!igk_is_webapp()) {
        return;
    }
    if (($headerStatus !== null) && $headerStatus) {
        igk_set_header($headerStatus);
    }
    $buri = igk_io_baseuri();
    if (strpos($uri, $buri) === 0) {
        $uri = trim(substr($uri, strlen($buri)));
        // + | OVH ONLY Support
        if (empty($uri))
            $uri = $buri;
        else {
            $uri = "/" . ltrim($uri, '/');
        }
    }
    header("Location: " . $uri);
    igk_exit();
}
///<summary>navigate du current main controller view</summary>
/**
 * navigate du current main controller view
 */
function igk_navto_ctrl_view()
{
    $g = igk_get_view_args();
    $ctrl = igk_getv($g, "ctrl");
    $c = igk_getv($g, "fname");
    if ($ctrl && $c) {
        igk_navto($ctrl->getAppUri($c));
    }
}
///<summary></summary>
///<param name="ctrl" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_navto_home($ctrl = null)
{
    if ($ctrl) {
        $u = $ctrl->MainView == IGK_DEFAULT_VIEW ? null : $ctrl->MainView;
        igk_navto($ctrl->getAppUri($u));
    } else {
        igk_navto(igk_io_baseuri());
    }
}
///<summary></summary>
///<param name="failuri" default="null"></param>
/**
 * 
 * @param mixed $failuri 
 */
function igk_navto_referer($failuri = null)
{
    $u = igk_getv(explode("?", igk_server()->HTTP_REFERER ?? ""), 0);
    if (empty($u)) {
        if ($failuri) {
            $u = $failuri;
        } else {
            if (igk_sys_env_production()) {
                $u = igk_io_baseuri();
            } else
                igk_die("no referer");
        }
    }
    igk_navto($u);
}
///<summary></summary>
///<param name="path" default="null"></param>
/**
 * 
 * @param mixed $path 
 */
function igk_navtobase($path = null)
{
    if (defined('IGK_NO_WEB_REDIRECT'))
        return;
    $s = null;
    if ($path == null) {
        $s = igk_io_currentrelativepath(IGK_STR_EMPTY, "/");
    } else {
        $s = igk_io_currentrelativepath($path, "/");
    }
    if (empty($s)) {
        if (igk_server_is_redirecting()) {
            if (function_exists($fc = "igk_get_app_ctrl")) {
                $ctrl = $fc();
                if ($ctrl) {
                    igk_navto($ctrl->getAppUri());
                }
            }
            igk_navto(igk_io_baseuri());
        } else {
            if (!empty($path) && strpos($path, "#") !== false) {
                igk_navto("./#" . igk_getv(explode("#", $path), 1));
            } else {
                igk_navto("./");
            }
        }
    } else {
        igk_navto(igk_uri($s));
    }
    igk_exit();
}
///<summary>navigate to basic request uri</summary>
/**
 * navigate to basic request uri
 */
function igk_navtobaseuri()
{
    $uri = igk_io_rootrequesturi();
    $uri = igk_getv(explode("?", $uri), 0);
    igk_navto($uri);
}
///<summary>navigate to current uri</summary>
/**
 * navigate to current uri
 */
function igk_navtocurrent($extra = null)
{
    if (!($uri = igk_server()->HTTP_REFERER)) {
        $uri = igk_io_baseuri(igk_server()->REQUEST_URI);
    }
    $tg = parse_url($uri);
    if ($extra) {
        $extra = ltrim($extra, "/");
    }
    $path = "/" . ltrim(implode("/", array_filter([igk_getv($tg, "path"), $extra])), '/');
    //igk_wln_e("current path : ", $path);
    igk_navto($path);
}
///<summary>is network available</summary>
/**
 * is network available
 */
function igk_network_available()
{
    return 1;
}
///<summary> create and generate new id</summary>
/**
 *  create and generate new id
 */
function igk_new_id()
{
    return date('Hmi') . md5(uniqid(rand()));
}
///<summary></summary>
/**
 * 
 */
function igk_new_response()
{
    return new \IGK\System\Http\WebResponse("");
}
///<summary>get node component uri</summary>
///<param name="$c" > node to get uri</param>
///<param name="u" > the local uri of the component</param>
/**
 * get node component uri
 * @param mixed $$c  node to get uri
 * @param mixed $u  the local uri of the component
 */
function igk_node_get_uri($c, $u)
{
    if (!$c)
        return null;
    return "?!/" . $c->getParam("system://component/id") . "/" . $u;
}
///<summary>register node fonction to parameter list</summary>
/**
 * register node fonction to parameter list
 */
function igk_node_reg_function($node, $name, $callback)
{
    $key = IGK_COMPONENT_REG_FUNC_KEY;
    $d = $node->getParam($key);
    if ($d == null)
        $d = array();
    $d[$name] = $callback;
    $node->setParam($key, $d);
}
///<summary>get notification event</summary>
/**
 * get notification event
 */
function igk_notification_event($name)
{
    $ctrl = igk_getctrl(IGK_NOTIFICATION_CTRL, false) ?? igk_die("no notification ctrl registrated");
    return $ctrl->getNotificationEvent($name);
}
///<summary> return a notification id for a controller</summary>
/**
 *  return a notification id for a controller
 */
function igk_notification_id($ctrl, $n)
{
    return "sys://ctrl/" . strtolower($ctrl->getName()) . "/{$n}";
}
///<summary>raise a notification event</summary>
/**
 * raise a notification event
 */
function igk_notification_push_event($name, $o, $param = null)
{
    $args = array_slice(func_get_args(), 1);
    igk_hook($name, $args);
}
///<summary>registrate a notification event</summary>
/**
 * registrate a notification event
 */
function igk_notification_reg_event($name, $callable)
{
    igk_reg_hook($name, $callable);
    return;
}
///<summary>free all callable from notification event</summary>
/**
 * free all callable from notification event
 */
function igk_notification_reset($name)
{
    $ctrl = igk_getctrl(IGK_NOTIFICATION_CTRL, true);
    $ctrl->resetNotification($name);
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_notification_response($name)
{
    $v_notification = igk_notifyctrl()->getNotification($name, false);
    if ($v_notification) {
        $tb = array();
        $p = null;
        $s = igk_html_render_node($v_notification, $p, $tb);
        return $s;
    }
    return null;
}
///<summary>unregister notification event</summary>
/**
 * unregister notification event
 */
function igk_notification_unreg_event($name, $callable)
{
    $name = $name ?? IGK_GLOBAL_EVENT;
    $ctrl = igk_getctrl(IGK_NOTIFICATION_CTRL, true);
    return $ctrl->unregisterNotification($name, $callable);
}
///<summary></summary>
///<param name="cond"></param>
///<param name="name" default="null"></param>
///<param name="goodmsg" default="null"></param>
///<param name="failemsg" default="null"></param>
/**
 * 
 * @param mixed $cond 
 * @param mixed $name 
 * @param mixed $goodmsg 
 * @param mixed $failemsg 
 */
function igk_notify_assert($cond, $name = null, $goodmsg = null, $failemsg = null)
{
    $c = igk_notifyctrl($name);
    if ($cond) {
        $c->addSuccess($goodmsg ?? "success");
    } else {
        $c->addError($failemsg ?? "failed");
    }
}
///<summary></summary>
///<param name="msg"></param>
///<param name="notifytag" default="null"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $notifytag 
 */
function igk_notify_danger($msg, $notifytag = null)
{
    if (igk_is_ajx_demand()) {
        igk_ajx_toast($msg, "igk-danger");
    } else {
        igk_notifyctrl($notifytag)->addError($msg);
    }
}
///<summary>shortcut to notification error</summary>
/**
 * shortcut to notification error
 */
function igk_notify_error($msg, $target = null)
{
    if (igk_current_context() != IGKAppContext::running) {
        return;
    }
    $ctrl = igk_notifyctrl($target);
    if ($ctrl != null) {
        $ctrl->addError($msg);
    } else {
        igk_wl("<div class=\"igk-notify-box igk-notify-box-error\" >" . $msg . "</div>");
    }
}
///<summary></summary>
///<param name="msg"></param>
///<param name="nofitytag" default="null"></param>
///<param name="type" default="gk-default"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $nofitytag 
 * @param mixed $type 
 */
function igk_notify_post($msg, $notifytag = null, $type = "igk-default")
{
    if (igk_is_ajx_demand()) {
        igk_ajx_toast($msg, $type);
    } else {
        igk_notifyctrl($notifytag)->TargetNode->div()->setContent($msg)->setClass($type);
    }
}
///<summary> utility to notify response</summary>
/**
 *  utility to notify response
 */
function igk_notify_reponse($msg, $type = 'default', $name = null)
{
    if (igk_is_ajx_demand()) {
        igk_ajx_toast($msg, $type);
    } else {
        $name = $name ?? igk_getr("notifyhost");
        igk_notifyctrl($name)->add($msg, $type);
    }
}
///<summary>shortcut to set notify host</summary>
/**
 * shortcut to set notify host
 */
function igk_notify_sethost($node, $notificationName = "::global")
{
    igk_notifyctrl()->setNotifyHost($node, $notificationName);
}
///<summary></summary>
///<param name="msg"></param>
///<param name="notifytag" default="null"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $notifytag 
 */
function igk_notify_success($msg, $notifytag = null)
{
    igk_notify_post($msg, $notifytag, "igk-panel igk-success");
}
///<summary></summary>
/**
 * 
 */
function igk_notifybox()
{
    return igk_getctrl(IGK_SHARED_CONTENT_CTRL)->getEntity("notifybox");
}
///<summary>used to render directory an message for box notification </summary>
/**
 * used to render directory an message for box notification 
 */
function igk_notifybox_ajx($msg)
{
    igk_wln("<div class=\"igk-notify-box\" >" . $msg . "</div>");
}
///<summary>get notification controller</summary>
/**
 * get notification controller
 * @param string $name notification handler 
 */
function igk_notifyctrl($name = null)
{
    $ctrl = igk_getctrl(IGK_NOTIFICATION_CTRL, true);
    if ($name == null) {
        return $ctrl;
    }
    return $ctrl->getNotification($name, true);
}
///<summary>Represente igk_ns_name function</summary>
///<param name="ns"></param>
/**
 * Represente igk_ns_name function
 * @param mixed $ns 
 */
function igk_ns_name($ns)
{
    $txt = explode("-", $ns);
    $g = array_slice($txt, 1);
    $txt = array_splice($txt, 0, 1);
    $ns = implode("", $txt + array_map("ucfirst", $g));
    return str_replace("/", "\\", $ns);
}
///<summary>clean ob</summary>
/**
 * clean ob - clear buffer  levels
 * @param int level -1 clear all , to level index
 */
function igk_ob_clean(int $level = 0)
{
    if ($level == -1) {
        $level = ob_get_level() - 1;
    }
    while (ob_get_level() > $level) {
        ob_end_clean();
    }
}
///<summary>get content</summary>
/**
 * get content
 */
function igk_ob_get($d)
{
    IGKOb::Start();
    igk_wl($d);
    $o = IGKOb::Content();
    IGKOb::Clear();
    return $o;
}
///<summary>used to call a ajx function to render content offscreen</summary>
/**
 * used to call a ajx function to render content offscreen
 */
function igk_ob_get_func($callback, $args = [])
{
    if (!empty($args) && !is_array($args))
        $args = [$args];
    IGKOb::Start();
    call_user_func_array($callback, $args);
    $c = IGKOb::Content();
    IGKOb::Clear();
    return $c;
}
///<summary>append data to current object</summary>
/**
 * append data to current object
 */
function igk_obj_append(&$obj, $data)
{
    foreach ($data as $k => $v) {
        $obj->$k = $v;
    }
    return $obj;
}
///<summary>used to bind request data to object</summary>
/**
 * used to bind request data to object
 */
function igk_obj_binddata($obj, $data)
{
    if (is_object($data)) {
        foreach ($obj as $k => $v) {
            $obj->$k = igk_getv($data, $k, $v);
        }
    }
    return $obj;
}
///<summary>invoke a function of a StdClass</summary>
/**
 * invoke a function of a StdClass
 */
function igk_obj_call($obj, $callable, $params = null)
{
    $g = isset($obj->$callable) ? $obj->$callable : null;
    if (igk_is_callable($g)) {
        if ($params)
            return call_user_func_array($g, $params);
        return $g();
    }
    return null;
}
///<summary> shortcut to OwnViewCtrl::Contains </summary>
/**
 *  shortcut to OwnViewCtrl::Contains 
 */
function igk_own_view_ctrl($ctrl)
{
    return OwnViewCtrl::Contains($ctrl);
}
///<summary> return an array of controller that possessed this rendering</summary>
/**
 *  return an array of controller that possessed this rendering
 */
function igk_own_view_list()
{
    return OwnViewCtrl::GetList();
}
///<summary>transform page-method-view translations name</page>
/**
 * transform page-method-view translations name
 */
function igk_page($n)
{
    static $page_list = null;
    if ($page_list == null) {
        $page_list = array("films" => "movies");
    }
    if (!($c = igk_getv($page_list, $n, null))) {
        $c = $n;
    }
    return $c;
}
///<summary></summary>
///<param name="num"></param>
/**
 * 
 * @param mixed $num 
 */
function igk_parse_num($num)
{
    if (is_numeric($num)) {
        if ($num == 0)
            return "0";
        return trim($num) . IGK_STR_EMPTY;
    }
    return "0";
}
///<summary>convert to bool string</summary>
///<param name="bool"></param>
/**
 * convert to bool string
 * @param mixed $bool 
 */
function igk_parsebool($bool)
{
    if (!is_bool($bool)) {

        if (is_string($bool)) {
            $bool = preg_match("/(true|1)/i", $bool);
        } else
            $bool = boolval($bool);
    }
    return $bool ? "true" : "false";
}
///<summary> parse bool language expression</summary>
/**
 *  parse bool language expression
 */
function igk_parsebools($b)
{
    return __("V." . igk_parsebool($b));
}
/**
 * get bool value
 * @param mixed $v value to convert to boolean
 * @return bool
 */
function igk_bool_val($v): bool
{
    if ($v && (is_string($v) && in_array(strtolower($v), ['true', 'false', '1', '0']))) {
        $v = (bool)preg_match("/(true|1)/i", $v);
    }
    return (bool)boolval($v);
}
///<summary>used to parse string value to compatible xml value.</summary>
/**
 * used to parse string value to compatible xml value.
 */
function igk_parsexmlvalue($value, $isvalue = false, $isandroidres = false)
{
    $value = preg_replace_callback(
        "/(?<value>('|([&]((quot);)*)))/i",
        function ($tmatch) use ($isvalue, $isandroidres) {
            switch ($tmatch["value"]) {
                case "&":
                    return "&amp;";
                case "&quot;":
                    return "\"";
                case "'":
                    if ($isandroidres)
                        return "\\'";
                    break;
            }
            return $tmatch[0];
        },
        $value
    );;
    return $value;
}
///<summary>retrieve matches</summary>
///<param name="pattern"></param>
///<param name="uri">uri to check</param>
///<param name="keys">keys to check</param>
/**
 * retrieve matches
 * @param mixed $pattern 
 * @param mixed $uri uri to check
 * @param mixed|array $keys keys to check
 */
function igk_pattern_get_matches($pattern, $uri, $keys)
{
    $c = preg_match_all($pattern, $uri, $tab);
    $t = array();
    if (($c > 0) && ($keys)) {
        foreach ($keys as $v) {
            if ($v == "query") {
                $t[$v] = $tab[$v][0];
                continue;
            }
            $s = $tab[$v][0];
            if (strstr($s, "/"))
                $s = explode("/", $s);
            if (!isset($t[$v]))
                $t[$v] = $s;
            else {
                if (is_array($t[$v]))
                    $t[$v][] = $s;
                else {
                    $t[$v] = array($t[$v], $s);
                }
            }
        }
    }
    return $t;
}
///<summary>get pattern match from uri key regex get from action in</summary>
///<param name="KeyPattern">The pattern key </param>
///<param name="BaseUri">the base uri</param>
///<return>Return the pattern object</return>
/**
 * get pattern match from uri key regex get from action
 * @param mixed $KeyPattern The pattern key 
 * @param mixed $BaseUri the base uri
 * @deprecated not implement
 */
function igk_pattern_get_uri_from_key($k, $buri = null)
{
    igk_wln_e(__FILE__ . ":" . __LINE__, "invoke uri ... ", $k);

    $buri = igk_str_rm_last($buri ? $buri : igk_io_baseuri(), '/');
    while (preg_match("/^\^/i", $k)) {
        $k = substr($k, 1);
    }
    $e = IGK_REG_ACTION_METH;
    while (igk_str_endwith($k, $e)) {
        $k = substr($k, 0, strlen($k) - strlen($e));
    }
    while (igk_str_endwith($k, "$")) {
        $k = substr($k, 0, strlen($k) - 1);
        break;
    }
    $k = preg_replace_callback(
        "/\([^\)]+\)(\?)?/i",
        function ($m) {
            return "";
        },
        $k
    );
    return $buri . $k;
}
///<summary></summary>
///<param name="s"></param>
/**
 * 
 * @param mixed $s 
 */
function igk_pattern_matcher_get_pattern($s)
{
    $s = preg_replace_callback("#:(?P<name>([a-z0-9]+))\+?#i", "igk_pattern_matcher_matchcallback", $s);
    $s = preg_replace_callback(
        "/\\$\$/i",
        function () {
            return "";
        },
        $s
    );
    return "/" . str_replace("/", "\/", $s) . "$/i";
}
///<summary></summary>
///<param name="m"></param>
/**
 * helper: important helper to resolve uri
 * @param mixed $m 
 */
function igk_pattern_matcher_matchcallback($m)
{

    $n = $m["name"];
    switch (strtolower($n)) {
        case 'verbs':
            // + | add verbs specic support
            $tm = "(?P<" . $n . ">(get|post|options|head|delete))";
            break;
        case "options":
            // $tm = "(?P<" . $n . ">([^;]+=([^;]+;?)+)";
            // + | ----------------------------------------------
            // + | options matching 
            // $tm = "(?P<" . $n . ">([^;]+=([^;\?]+;?|;;)?)+)";
            $tm = "(?P<" . $n . ">([^;]+=([^;\?]+;?|;)?)+)";
            break;
        case "query":
            $tm = "(?P<" . $n . ">([^;]+;?)+)";
            break;
        case "path":
            if (substr($m[0], -1) == "+") {
                $tm = "(?P<" . $n . ">([^/;\?]+/?)+)";
            } else {
                $tm = "(?P<" . $n . ">[^/;\?]+)";
            }
            break;
        case "function":
            if (substr($m[0], -1) == "+") {
                $tm = "(?P<" . $n . ">([^/;]+/?)+)";
            } else {
                $tm = "(?P<" . $n . ">[^/;]+)";
            }
            break;
        case "lang":
            $lg = igk_get_env("sys://availlang") ??  R::GetSupportLangRegex();

            if (substr($m[0], -1) == "+") {
                $tm = "(?P<" . $n . ">(" . $lg . "/?))";
            } else {
                $tm = "(?P<" . $n . ">" . $lg . ")";
            }
            break;
        default:
            if (substr($m[0], -1) == "+") {
                $tm = "(?P<" . $n . ">([^/;]+/?)+)";
            } else {
                $tm = "(?P<" . $n . ">[^/;]+)";
            }
            break;
    }
    return $tm;
}
///<summary>Extract view argument from pattern</summary>
///<param name="ctrl"></param>
///<param name="p"></param>
///<param name="globalregister"></param>
/**
 * Extract view argument from pattern
 * @param mixed $ctrl 
 * @param mixed $p pattern data 
 * @param mixed $globalregister globaly register 
 */
function igk_pattern_view_extract($ctrl, $p, $globalregister = 0)
{
    if (!$p) {
        return array();
    }
    $c = igk_page(igk_getv($p, "function"));
    $param = igk_getv($p, "params");
    $query_options = igk_getv($p, "options");
    if (is_array($c)) {
        igk_die(__("Function is array list. Not Allowed"));
    }
    $handle_file = 0;
    if ($c && !method_exists($ctrl, $c)) {
        $viewdir = $ctrl->getViewDir();
        $dir = $viewdir . "/" . $c;
        $exts = explode("|", IGK_VIEW_FILE_EXT_REGEX);
        if (is_dir($dir)) {
            if (is_string($param)) {
                $param = !empty($param) ? array($param) : array();
            }
            $ext_regex = "/\." . IGK_VIEW_FILE_EXT_REGEX . "$/";
            $found = false;
            $cargs = [];
            while (!$found && (count($param) > 0)) {
                $path = implode("/", $param);
                $file = igk_dir($dir . "/" . $path);
                if (!is_dir($file)) {
                    if (preg_match($ext_regex, $file) && file_exists($file)) {
                        $found = true;
                        $c .= "/" . $path;
                        $param = [];
                    } else {
                        foreach ($exts as $ext) {
                            if (file_exists($cf = $file . "." . $ext)) {
                                $c .= "/" . $path;
                                $param = [];
                                $found = true;
                                $file = $cf;
                            }
                        }
                    }
                } else {
                    $file = rtrim($file, "/");
                    if (file_exists($cf = $file . "/" . IGK_DEFAULT_VIEW_FILE)) {
                        $c .= rtrim("/" . $path, "/");
                        $param = [];
                        $found = true;
                        $file = $cf;
                    }
                }
                if (!$found) {
                    array_unshift($cargs, array_pop($param));
                }
            }
            $handle_file = $found;
            $param = $cargs;
        }
    }
    if (is_string($query_options) && (strlen($query_options) > 0)) {
        $query_options = igk_get_query_options($query_options);
    }

    $t = array(
        "c" => $c,
        "param" => $param,
        "query_options" => $query_options,
        "handle_file" => $handle_file
    );
    if ($globalregister) {
        IGK\Helper\ViewHelper::RegisterArgs($t);
    }
    return $t;
}
///<summary>peek the data on environement variable</summary>
/**
 * peek the data on environement variable
 */
function igk_peek_env($n)
{
    return igk_environment()->peek($n);
}
///<summary></summary>
/**
 * 
 */
function igk_phar_available()
{
    if (in_array('phar', stream_get_wrappers()) && class_exists('Phar', false)) {
        return !empty(Phar::running());
    }
    return 0;
}
///<summary></summary>
/**
 * 
 */
function igk_phar_running()
{
    return igk_phar_available() && strstr(IGK_LIB_DIR, Phar::running());
}
///<summary></summary>
///<param name="file"></param>
///<param name="content"></param>
///<param name="error" ref="true"></param>
///<param name="code" ref="true"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $content 
 * @param mixed $error 
 * @param mixed $code 
 */
function igk_php_check_and_savescript($file, $content, &$error, &$code)
{
    $f = $file;
    $v_c = $content;
    $v_old = null;
    if (file_exists($f)) {
        $v_old = IO::ReadAllText($f);
    }
    igk_io_save_file_as_utf8($f, $v_c, true);
    $code = 10;
    $error = array();
    $uri = igk_html_resolv_img_uri(igk_dir(IGK_LIB_DIR . "/igk_checkfile.php"));
    $r = igk_curl_post_uri($uri, array("file" => $f));
    if ($r != 'ok') {
        igk_wln($error);
        unlink($f);
        igk_io_save_file_as_utf8($f, $v_old, true);
        return false;
    }
    return true;
}
///<summary></summary>
///<param name="txt"></param>
///<param name="exit" default="1"></param>
/**
 * 
 * @param mixed $txt 
 * @param mixed $exit 
 */
function igk_plain_text($txt, $exit = 1)
{
    header("Content-Type:text/plain");
    echo $txt;
    if ($exit)
        igk_exit();
}

///<summary>pop data on  environment variable. use to restore state</summary>
/**
 * pop data on environment variable. use to restore state
 */
function igk_pop_env($n)
{
    return igk_environment()->pop($n);
}
///<summary></summary>
///<param name="name"></param>
///<param name="tab" ref="true"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $tab 
 */
function igk_pop_tab($name, &$tab)
{
    $c = igk_pop_env("sys://pushtab/{$name}");
    $tab = $c;
    return $c;
}
///<summary>filter menu</summary>
/**
 * filter menu
 */
function igk_post_filter_menu($tab, $source, $tag = null)
{
    $itab = igk_get_env("sys://men/postfilter");
    $ctab = array_merge($tab);
    if ($itab) {
        if (igk_get_env("sys://men/postfilter/sort", 1)) {
            usort($itab, function ($a, $b) {
                $l1 = igk_getv($a, "level", 10);
                $l2 = igk_getv($b, "level", 10);
                $r = 0;
                if ($l1 != $l2) {
                    if ($l1 < $l1) {
                        $r = -1;
                    }
                    $r = 1;
                }
                return $r;
            });
            igk_set_env("sys://men/postfilter/sort", null);
        }
        $args = array_merge(array(&$ctab), array_slice(func_get_args(), 1));
        foreach ($itab as $v) {
            $callback = igk_getv($v, "callback");
            if ($callback) {
                call_user_func_array($callback, $args);
            }
        }
        $tab = $ctab;
    }
    return $tab;
}
///<summary></summary>
///<param name="extraheader"></param>
/**
 * 
 * @param mixed $extraheader 
 */
function igk_post_header($extraheader)
{
    igk_set_env("sys://igk_post_uri/header", $extraheader);
}
///<summary></summary>
///<param name="msg"></param>
///<param name="iMessageHandler"></param>
///<param name="args" default="null"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $iMessageHandler 
 * @param mixed $args 
 */
function igk_post_message($msg, $iMessageHandler, $args = null)
{
    $iMessageHandler->handleCmd($msg, $args);
}
///<summary>post data in uri script a return data. in balafon system</summary>
/**
 * post data in uri script a return data. in balafon system
 */
function igk_post_uri($uri, $args = null, $content = IGK_APP_FORM_CONTENT, $samesession = true)
{
    if (igk_server_is_local()) {
        igk_ilog("/!\\ obsolete : file_get_content not working well. you better invoke directly with a controller : igk_curl_post_uri instead: " . $uri);
        igk_die("failed to pos uri . " . $uri);
    }
    return "";
}
///<summary> get environment last error for last call to igk_post_uri</summary>
/**
 *  get environment last error for last call to igk_post_uri
 */
function igk_post_uri_last_error()
{
    return igk_get_env("igk_post_uri:/Error");
}
///<summary></summary>
///<param name="pattern"></param>
///<param name="value"></param>
///<param name="key"></param>
///<param name="index"></param>
/**
 * 
 * @param mixed $pattern 
 * @param mixed $value 
 * @param mixed $key 
 * @param mixed $index 
 */
function igk_preg_match($pattern, $value, $key, $index = 0)
{
    $tab = array();
    $c = preg_match_all($pattern, $value, $tab);
    if ($c > 0) {
        return igk_getv($tab[$key], $index);
    }
    return null;
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_print($msg)
{
    igk_wl($msg);
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_print_ln($msg)
{
    igk_wl($msg . IGK_LF);
}
///<summary></summary>
/**
 * 
 */
function igk_print_stack_depth()
{
    $callers = debug_backtrace();
    for ($i = 0; $i < count($callers); $i++) {
        $f = igk_getv($callers[$i], "function");
        $fl = igk_getv($callers[$i], "file");
        $c = igk_getv($callers[$i], "class");
        echo "<div>";
        echo "<span>" . $i . "</span>";
        echo "<span style='color:#fd3333; text-align:right; width: 90px; display:inline-block;'>" . igk_getv($callers[$i], "line") . ":</span><span>";
        echo $f . "</span><span>";
        echo $c . "</span><span>";
        echo $fl . "</span>";
        echo "</div>";
    }
}


///<summary>push data on  environment variable. use to save state</summary>
/**
 * push data on environment variable. use to save state
 */
function igk_push_env($n, $v)
{
    return igk_environment()->push($n, $v);
    // $IGK_ENV = igk_environment();
    // if ($v == null) {
    //     return;
    // }
    // $tab = igk_getv($IGK_ENV, $n, function () {
    //     return array();
    // });
    // if (!is_array($tab)) {
    //     igk_die("failed tab is not an array:" . $n, __FUNCTION__);
    // }
    // array_push($tab, $v);

    // $IGK_ENV[$n] = $tab;
}
///<summary></summary>
///<param name="name"></param>
///<param name="tab" ref="true"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $tab 
 */
function igk_push_tab($name, &$tab)
{
    igk_push_env("sys://pushtab/{$name}", $tab);
}
///<summary></summary>
/**
 * 
 */
function igk_qr_confirm()
{
    return (igk_getr("confirm", 0) == 1);
}
///<summary>used to restore request</summary>
/**
 * used to restore request
 */
function igk_qr_restore()
{
    $v = igk_get_env("sys://store/request");
    if ($v && (igk_count($v) > 0)) {
        igk_wln($v);
        igk_wln("op");
        $_REQUEST = array_pop($v);
    }
    return $_REQUEST;
}
///<summary>used to save request</summary>
/**
 * used to save request
 */
function igk_qr_save($tab)
{
    $v = igk_get_env("sys://store/request", array());
    array_push($v, $_REQUEST);
    igk_set_env("sys://store/request", $v);
    $_REQUEST = $tab;
    return $v;
}
///<summary>get registrated display key</summary>
/**
 * get registrated display key
 */
function igk_r_getdisplay($key, $param = null)
{
    return __($key, $param)->getValue();
}

///<summary>raise environment event. </summary>
/**
 * raise environment event. 
 * @param string $evtn event name
 * @param array $params evtn event parameter
 */
function igk_raise_event(string $evtn, $params = array())
{
    $g = igk_get_env("sys://environment/events", array());
    $b = igk_getv($g, $evtn);
    if ($b) {
        if ($b->sortrequire)
            sort($b->callbacks);
        foreach ($b->callbacks as $v) {
            if (call_user_func_array($v, $params)) {
                break;
            }
        }
    }
}
///<summary></summary>
///<param name="obj"></param>
///<param name="n"></param>
///<param name="args"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $n 
 * @param mixed $args 
 */
function igk_raise_globalcallback($obj, $n, $args)
{
    return $obj->onInvoke($n, igk_getv($args, 0), array_slice($args, 1));
}
///<summary></summary>
/**
 * 
 */
function igk_raise_initenv_callback()
{
    $c = igk_get_env("sys://init_env/callback");
    if ($c) {
        foreach ($c as $k) {
            $k();
        }
    }
    igk_set_env("sys://init_env/callback", null);
}
///<summary>register file that will be used as script</summary>
/**
 * register file that will be used as script
 */
function igk_reg_action_script($name, $callback)
{
    igk_set_env("sys://actions/scripts/{$name}", $callback);
}
///<summary></summary>
///<param name="filename"></param>
///<param name="tab"></param>
/**
 * 
 * @param mixed $filename 
 * @param mixed $tab 
 */
function igk_reg_class_file($filename, $tab)
{
    return igk_reg_file("sys://reflection/class", $filename, $tab);
}
///<summary></summary>
///<param name="classname"></param>
///<param name="v"></param>
/**
 * 
 * @param mixed $classname 
 * @param mixed $v 
 */
function igk_reg_class_instance_key($classname, $v)
{
    if (class_exists($classname, false)) {
        igk_set_env(strtolower("sys://instance/key/" . $classname), $v);
    } else
        igk_die(__("Failed to register class instance key"));
}
///<summary>register command line</summary>
/**
 * register command line
 */
function igk_reg_cmd_args($name, $desc, $callback)
{
    $t = igk_get_env("sys://cmd/args", array());
    $t[$name] = (object)["callback" => $callback, "desc" => $desc, "category" => "system"];
    igk_set_env("sys://cmd/args", $t);
}
///<summary></summary>
///<param name="name"></param>
///<param name="args"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $args 
 */
function igk_reg_cmd_command($name, $args)
{
    if (is_string($args)) {
        if (function_exists($args) && is_callable($args)) {
            igk_set_env_keys("sys://cmd/commands", $name, $args);
            return 1;
        } else if (class_exists($args, true)) {
            igk_set_env_keys("sys://cmd/commands", $name, new $args());
            return 1;
        }
    } else if (is_callable($args)) {
        igk_set_env_keys("sys://cmd/commands", $name, $args);
    }
    return 0;
}
///<summary>register component</summary>
///<param name="id">identifier of the component</param>
///<param name="s" type="object" >the component </param>
/**
 * register component
 * @param mixed $id identifier of the component
 * @param object $s the component 
 */
function igk_reg_component($id, $s)
{
    $ctrl = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL);
    $ctrl->setParam("sys://globalcomponent/{$id}", $s);
}
///<summary>Represente igk_reg_component_ajx function</summary>
///<param name="n"></param>
///<param name="attr"></param>
///<param name="callback"></param>
/**
 * Represente igk_reg_component_ajx function
 * @param mixed $n 
 * @param mixed $attr 
 * @param mixed $callback 
 */
function igk_reg_component_ajx($n, $attr, $callback)
{
    $n->setCallback($attr, $callback);
    $n["onclick"] = "javascript:ns_igk.stop_event(event); ns_igk.ajx.post('" . igk_get_component_uri($n, "$attr") . "');  return false;";
    return $n;
}
///<summary>only to register a autodection package</summary>
/**
 * only to register a autodection package
 */
function igk_reg_component_package($packagename = null, $callback = null)
{
    $key = "sys://components/packages";
    $t = igk_get_env($key) ?? array();
    if (empty($packagename))
        return $t;
    $k = strtolower($packagename);
    if (isset($t[$k]))
        igk_die("[{$packagename}]" . " component package already register.");
    $t[$k] = array("name" => $packagename, "callback" => $callback, "init" => 0);
    $m = &$t;
    igk_set_env($key, $m);
}
///<summary>shortcut to register global system controller</summary>
/**
 * shortcut to register global system controller
 * @deprecated do not register controller
 */
function igk_reg_ctrl($name, $ctrl)
{
    die(__FUNCTION__);
    // if (IGKApp::IsInit() && $name && $ctrl) {
    //     igk_app()->getControllerManager()->register($name, $ctrl);
    //     return 1;
    // }
    // return 0;
}
///<summary></summary>
///<param name="n"></param>
///<param name="closure"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $closure 
 */
function igk_reg_env_closure($n, $closure)
{
    if (!is_object($closure) || get_class($closure) != "Closure")
        return 0;
    $key = IGK_ENV_CALLBACK_KEYS;
    $tab = igk_get_env($key, array());
    $tab[$n] = $closure;
    igk_set_env($key, $tab);
    return 1;
}
// ///<summary>register environment event. will not be serialize because store on environment variable.</summary>
// /**
//  * register environment event. will not be serialize because store on environment variable.
//  */
// function igk_reg_event($evtn, $callback)
// {
//     $key = "sys://environment/events";
//     $g = igk_get_env($key, array());
//     $s = igk_getv($g, $evtn);
//     if ($s == null) {
//         $s = (object)array();
//         $s->callbacks = array();
//     }
//     $s->sortrequire = 1;
//     array_push($s->callbacks, $callback);
//     $g[$evtn] = $s;
//     igk_set_env($key, $g);
// }
///<summary></summary>
///<param name="key"></param>
///<param name="file"></param>
///<param name="tab"></param>
/**
 * 
 * @param mixed $key 
 * @param mixed $file 
 * @param mixed $tab 
 */
function igk_reg_file($key, $file, $tab)
{
    $fkey = "sys://files";
    $tkey = "sys://functable";
    $g = igk_get_env($key, function () {
        return array("sys://files" => null);
    });
    if (!isset($g[$fkey][$file])) {
        $idx = igk_count($g[$fkey]);
        $g[$fkey][] = $file;
        $gb = igk_getv($g, $tkey);
        if (is_array($gb))
            $g[$tkey] = array_merge($gb, igk_array_createkeyarray($tab, $idx));
        else
            $g[$tkey] = igk_array_createkeyarray($tab, $idx);
        igk_set_env($key, $g);
        return $idx;
    }
    return -1;
}
///<summary></summary>
///<param name="name"></param>
///<param name="class"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $class 
 */
function igk_reg_form_builder_engine($name, $class)
{
    $k = "sys://form/builderengines";
    $tab = igk_get_env($k) ?? [];
    $tab[$name] = $class;
    igk_set_env($k, $tab);
    return $tab;
}
///Reflexion utility function to trace eval error callback.
/**
 */
function igk_reg_func_files($file, $tab)
{
    return igk_reg_file("sys://reflection/funcs", $file, $tab);
}

///<summary></summary>
///<param name="s"></param>
/**
 * 
 * @param mixed $s 
 */
function igk_reg_handle_file_request($s)
{
    if (empty($s) || is_object($s))
        return;
    $t = igk_get_env("sys://handle/file_request") ?? array();
    $t[$s] = $s;
    igk_set_env("sys://handle/file_request", $t);
}
///<summary> use to register html custom component</summary>
/**
 *  use to register html custom component
 */
function igk_reg_html_component($name, $callback, $ns = "igk")
{
    $package = igk_reg_component_package();
    if (!isset($package[$ns]["components"])) {
        $package[$ns]["components"] = [];
    }
    $name = strtolower(str_replace("-", "_", trim($name)));
    if ($callback === null) {
        unset($package[$ns]["components"][$name]);
    } else
        $package[$ns]["components"][$name] = $callback;
    igk_set_env("sys://components/packages", $package);
}
///<summary></summary>
///<param name="callback"></param>
/**
 * 
 * @param mixed $callback 
 */
function igk_reg_initenv_callback($callback)
{
    $c = igk_get_env("sys://init_env/callback", array());
    $c[] = $callback;
    igk_set_env("sys://init_env/callback", $c);
}
///<summary></summary>
///<param name="n" default="null"></param>
///<param name="uri" default="null"></param>
///<return refout="true"></return>
/**
 * 
 * @param mixed $n 
 * @param mixed $uri 
 */
function &igk_reg_ns($n = null, $uri = null)
{
    static $nsmanager;
    if (!isset($nsmanager)) {
        $nsmanager = new StdClass;
        $nsmanager->ns = array();
    }
    if ($n != null)
        $nsmanager->ns[$n] = $uri;
    return $nsmanager;
}
///<summary>register package</summary>
/**
 * register package
 */
function igk_reg_package($name, $callback)
{
    igk_die('not implemenent ' . __FUNCTION__);
    $t = igk_get_env("sys://packages") ?? [];
    $t[$name] = $callback;
    igk_set_env("sys://packages", $t);
}
///<summary></summary>
///<param name="ext"></param>
///<param name="p"></param>
/**
 * 
 * @param mixed $ext 
 * @param mixed $p 
 */
function igk_reg_path_exec($ext, $p)
{
    $t = igk_get_env("sys://env//path_exec");
    if ($t == null)
        $t = array();
    $t[$ext] = $p;
    igk_set_env("sys://env//path_exec", $t);
}
///<summary>Register string pipe expression</summary>
/**
 * Register string pipe expression
 */
function igk_reg_pipe($mixed, $callback = null)
{
    $k = "sys://localizedpipe";
    $tab = igk_get_env($k, function () {
        // @igk_comment: init binding expression
        return \IGK\System\Html\Templates\BindingPipeExpressionInfo::CreateNewDefinition();
    });
    if ($mixed) {
        if (is_array($mixed))
            $tab = array_merge($tab, $mixed);
        else if (is_string($mixed) && func_num_args() > 1) {
            $tab = array_merge($tab, [$mixed => $callback]);
        }
        igk_set_env($k, $tab);
    }
    return $tab;
}
///<summary>register global tempary event. only one callback </summary>
/**
 * register global tempary event. only one callback 
 */
function igk_reg_session_event($name, $callback)
{
    // igk_debug_wln( __FILE__.':'.__LINE__,  __FUNCTION__ . " " . $name);

    $ctx = igk_current_context();
    $e = strpos("running|starting", $ctx) !== false;
    $key = "sys://global_events";
    $primary = igk_get_env($key, array());
    $t = null;
    switch ($ctx) {
        case IGKAppContext::initializing:
            $primary[$name][] = $callback;
            igk_set_env($key, $primary);
            return 1;
    }
    if ($e) {
        $t = igk_app()->session->Events ?? array();
    } else {
        igk_die("context not supported " . $ctx);
    }
    if (!isset($t[$name])) {
        $t[$name] = array();
    }
    $t[$name][] = $callback;
    igk_app()->session->Events = $t;
    return 3;
}
///<summary>shortcut to register subdomain</summary>
/**
 * shortcut to register subdomain
 */
function igk_reg_subdomain($n, $ctrl, $row = null)
{
    return IGKSubDomainManager::getInstance()->reg_domain($n, $ctrl, $row);
}

///<summary>register widget</summary>
/**
 * register widget
 */
function igk_reg_widget($name, $callback = null, $priority = 10)
{
    $g = igk_get_env(IGK_ENV_WIDGETS_KEY);
    if (!is_array($g)) {
        $g = array();
    }
    $g[$name] = (object)array("name" => strtolower($name), "callback" => $callback);
    igk_set_env(IGK_ENV_WIDGETS_KEY, $g);
}
///<summary>register a widget zone</summary>
/**
 * register a widget zone
 */
function igk_reg_widget_zone($name, $args)
{
}
///<summary>get regex from pattern</summary>
/**
 * get regex from pattern
 */
function igk_regex_get($pattern, $key, $value, $index = 0)
{
    $t = array();
    $c = preg_match_all($pattern, $value, $t);
    if ($c > 0) {
        if ($key == null) {
            return $t;
        }
        if ($c == 1) {
            return $t[$key][0];
        } else {
            return $t[$index][$key];
        }
    }
    return null;
}
///<summary>core auto register class</summary>
///<param name="func"></param>
///<param name="priority" default="10"></param>
/**
 * core auto register class
 * @param mixed $func 
 * @param mixed $priority 
 */
function igk_register_autoload_class(callable $func = null, $priority = 10)
{
    die(__FUNCTION__ . " obsolete");
}

///<summary>register or get class informations</summary>
/**
 * register or get class informations
 */
function igk_register_class_info($classname = null, $infos = null)
{
    static $info = null;
    class_exists($classname, false) || die("failed to register class information");
    if ($info == null) {
        $info = array();
    }
    if ($classname !== null) {
        if (!isset($info[$classname])) {
            $info[$classname] = (object)array();
        }
        $clkey = "@_info_";
        if (is_object($infos)) {
            $cl = get_class($infos);
            if ("StdClass" != $cl) {
                $ckey = $cl;
            }
        }
        $info[$classname]->{$clkey} = $infos;
    }
    return $info;
}
///<summary></summary>
///<param name="name"></param>
///<param name="class"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $class 
 */
function igk_register_dataadapter($name, $class)
{
    igk_set_env_keys("sys://dataadapter", strtoupper($name), $class);
}
///register for global integration menu
///<summary>register for post filter menu </summary>
/**
 * register for post filter menu 
 */
function igk_register_post_filter_menu($callback)
{
    $tab = null;
    if (is_callable($callback)) {
        $tab = ["callback" => $callback, "level" => 10];
    } else {
        $tab = $callback;
    }
    igk_push_env("sys://men/postfilter", $tab);
    igk_set_env("sys://men/postfilter/sort", 1);
}
///<summary></summary>
///<param name="name"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $callback 
 */
function igk_register_requirement($name, $callback)
{
    $t = igk_get_env(IGK_ENV_REQUIREMENT_KEY, array());
    $t[$name] = $callback;
    igk_set_env(IGK_ENV_REQUIREMENT_KEY, $t);
}
///<summary>register tempory route to class</summary>
/**
 * register tempory route to class
 * @param string controller class
 */
function igk_register_temp_uri($controllerClass, ?BaseController $loader = null)
{
    $routes = igk_app()->session->getRoutes();
    $cl = is_object($controllerClass) ? get_class($controllerClass) : $controllerClass;
    $is_module = \IGK\Controllers\ApplicationModuleController::IsModule($controllerClass);
    $rtname = $is_module ? "m:" . $controllerClass->getName() : $cl;

    if (!isset($routes[$rtname])) {
        $guid = igk_get_component_uri_key(igk_create_guid());
        $routes[$rtname] = $guid;
        igk_app()->settings->{IGK_SESS_ROUTES} = $routes;
        $g = array(
            "type" => "route",
            "class" => $cl
        );
        if ($loader) {
            $g["loader"] = get_class($loader);
        }
        if ($is_module)
            $g["module"] = $rtname;
        igk_app()->session->component($guid, $g);
        return $guid;
    }
    return $routes[$rtname];
}
///<summary>register library with namespace generation on eval</summary>
/**
 * register library with namespace generation on eval
 */
function igk_registerlib($dir = null, $ext = ".phlib", $callback = null, $ns = null)
{
    $dir = $dir ?? IGK_LIB_DIR;
    if (!is_dir($dir))
        return;
    $tab = array();
    array_push($tab, $dir);
    $src = igk_realpath($dir);
    $ln = strlen($src);
    ob_start();
    $s = "";
    $functions = get_defined_functions()["user"];
    $classes = get_declared_classes();
    $source = igk_count($functions);
    $clcount = igk_count($classes);
    while ($dir = array_pop($tab)) {
        $hdir = opendir($dir) ?? igk_die("failed to open dir");
        while ($s = readdir($hdir)) {
            if (($s == ".") || ($s == ".."))
                continue;
            $f = igk_realpath($dir . "/" . $s);
            if (is_dir($f))
                array_push($tab, $f);
            else {
                if (preg_match("/" . $ext . "$/i", $s)) {
                    if ($ns) {
                        $subdir = str_replace("/", "\\", substr(dirname($f), $ln + 1));
                        $script = igk_io_read_allfile($f);
                        $nspace = $ns . (!empty($subdir) ? "\\" . $subdir : "");
                        $g = 'namespace ' . $nspace . '; use ' . $ns . ' as __base_ns; $Gfile = \'' . $f . '\'; ?>' . $script;
                        try {
                            igk_set_env(IGK_LAST_EVAL_KEY, "/!\\ eval file: " . $f);
                            eval($g);
                            igk_set_env(IGK_LAST_EVAL_KEY, null);
                        } catch (Exception $e) {
                            continue;
                        }
                        $functions2 = get_defined_functions();
                        $classes2 = get_declared_classes();
                        if (count($functions2["user"]) > $source) {
                            $ktab = array_slice($functions2["user"], $source);
                            igk_reg_func_files($f, $ktab);
                            $source += igk_count($ktab);
                        }
                        if (count($classes2) > $clcount) {
                            $ktab = array_slice($classes2, $clcount);
                            igk_reg_class_file($f, $ktab);
                            $clcount += igk_count($ktab);
                        }
                    } else
                        include_once($f);
                    $h = ob_get_contents();
                    igk_assert_die(($ct = strlen($h)) > 0, "file : " . $f . " : content : " . $ct . " " . $h);
                    if ($callback) {
                        $callback($f, $src, $ns);
                    }
                }
            }
        }
        closedir($hdir);
    }
    ob_end_clean();
}
///<summary>used to registers files to library</summary>
/**
 * used to registers files to library
 */
function igk_reglib($files)
{
    if (($files == null) || (igk_count($files) == 0))
        return;
    if (IGKSysCache::$LibFiles == null)
        IGKSysCache::$LibFiles = array();
    foreach ($files as $v) {
        if (!isset(IGKSysCache::$LibFiles[$v]))
            IGKSysCache::$LibFiles[$v] = $v;
    }
}
///<summary>register lib file once</summary>
/**
 * register lib file once
 */
function igk_reglib_once($file)
{
    $f = is_file($file) ? $file : igk_realpath($file);
    if (!empty($f) && file_exists($f)) {
        require_once($f);
        igk_reglib(array(igk_uri($f) => igk_uri($f)));
    }
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_regtowebpage($ctrl)
{
    $c = igk_get_defaultwebpagectrl();
    if ($c)
        $c->regChildController($ctrl);
}
///<summary>Represente igk_relection_get_properties_keys function</summary>
///<param name="class"></param>
///<param name="filter" default="IS_PUBLIC"></param>
/**
 * Represente igk_relection_get_properties_keys function
 * @param mixed $class 
 * @param mixed $filter 
 */
function igk_relection_get_properties_keys($class, $filter = ReflectionProperty::IS_PUBLIC)
{
    $properties = [];
    array_map(
        function ($i) use (&$properties) {
            $properties[strtolower($i->getName())] = $i;
        },
        (igk_sys_reflect_class($class))->getProperties($filter)
    );
    return $properties;
}
///<summary>shortcut to render global document</summary>
/**
 * shortcut to render global document
 */
function igk_render_doc($doc = null, $refreshdefault = 0, $ctrl = null)
{
    HtmlRenderer::RenderDocument($doc, $refreshdefault, $ctrl);
}
///<summary>render dummy document</summary>
/**
 * render dummy document
 */
function igk_render_dummy_doc($n, $title, $t)
{
    $d = igk_get_document($n);
    $d->Title = $title;
    $d->body->getBodyBox()->clearChilds()->add($t);
    $d->renderAJX();
    $d->dispose();
}
///<summary>Bind node to document and render it utility function</summary>
/**
 * Bind node to document and render it utility function
 */
function igk_render_node($node, $doc, $render = 1)
{
    if ($doc === null) {
        igk_die("[" . __FUNCTION__ . "] document can't be null");
    }
    $bbox = $doc->Body->getBodyBox();
    if (!igk_get_env("sys://doc/no_clear")) {
        $bbox->clearChilds();
    }
    $bbox->add($node);
    if ($render)
        $doc->renderAJX();
}
///<summary>render resources utility </summary>
///<param name="file">file to render</summary>
///<param name="cache" default="1" >allow caching</param>
///<param name="exit" default="1" >force script end</param>
/**
 * render resources utility 
 * @param mixed $file file to render
 * @param mixed $cache allow caching
 * @param mixed $exit force script end
 */
function igk_render_resource($file, $cache = 1, $exit = 1)
{
    if (preg_match("/\.(ph(p|tml))$/", $file)) {
        include_once($file);
    } else {
        if ($cache)
            igk_header_cache_output();
        igk_header_content_file($file);
        igk_zip_output(file_get_contents($file));
    }
    if ($exit) {
        igk_exit();
    }
}
///<summary>render trace </summary>
/**
 * render trace 
 */
function igk_render_trace()
{
    igk_wln(igk_show_trace(1));
}
///<summary></summary>
///<param name="code"></param>
///<param name="message"></param>
///<param name="data" default="null"></param>
/**
 * 
 * @param mixed $code 
 * @param mixed $message 
 * @param mixed $data 
 */
function igk_render_xml_error($code, $message, $data = null)
{
    $rp = igk_create_xmlnode("response");
    $rp->add("status")->Content = $code;
    $rp->add("message")->Content = $message;
    if ($data)
        $rp->addObData($data);
    return $rp;
}
///<summary></summary>
///<param name="type"></param>
/**
 * 
 * @param mixed $type 
 */
function igk_request_is($type)
{
    return igk_server()->REQUEST_METHOD == $type;
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="resname"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $resname 
 */
function igk_res_img($ctrl, $resname)
{
    if (file_exists($f = igk_uri($ctrl->getResourcesDir() . "/Img/{$resname}"))) {
        return $f;
    }
    return null;
}
///<summary></summary>
/**
 * 
 */
function igk_reset_db_dataadapter()
{
    \IGK\Database\DataAdapterBase::ResetDataAdapter();
}
///<summary>reset all session global variable</summary>
/**
 * reset all session global variable
 */
function igk_reset_globalvars()
{
    igk_app()->session->setParam(IGKSession::GLOBALVARS, array());
}
///<summary></summary>
/**
 * 
 */
function igk_reset_include()
{
    igk_set_env("sys://include/init", null);
}
///<summary></summary>
/**
 * 
 */
function igk_resetr()
{
    $_REQUEST = array();
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="name"></param>
///<param name="alllanguage" default="false"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $name 
 * @param mixed $alllanguage 
 */
function igk_rm_article($ctrl, $name, $alllanguage = false)
{
    if ($alllanguage) {
        $d = dirname($name);
        $exp = "/(\\" . igk_str_expr(igk_dir(basename($name))) . ")/i";
        if (!empty($d) && ($d != ".") && is_dir($d)) {
            $f = igk_io_getfiles($d, $exp, false);
        } else {
            if (!file_exists($name)) {
                $f = igk_io_getfiles($ctrl->getArticlesDir(), $exp, false);
            }
        }
        if ($f) {
            $i = 0;
            foreach ($f as $v) {
                unlink($v);
                $i++;
            }
            return $i;
        }
    } else {
        $f = $name;
        $d = dirname($name);
        if (!empty($d) && ($d != ".") && is_dir($d)) {
            $f = $ctrl->getArticleInDir(basename($name), $d);
        } else {
            if (!file_exists($name))
                $f = $ctrl->getArticle($name);
        }
        if (file_exists($f)) {
            unlink($f);
            return 1;
        }
    }
    return false;
}
///<summary></summary>
///<param name="n"></param>
///<param name="doc"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $doc 
 */
function igk_rm_balafonscriptfile_callback($n, $doc)
{
    $g = $doc->getParam("sys://igk/tempbalafonjs");
    unset($g[$n]);
    $doc->setParam("sys://igk/tempbalafonjs", $g);
}
///<summary>run script on background</summary>
/**
 * run script on background
 */
function igk_run_bg_script($f)
{
    $v_rp = igk_get_run_script_path();
    if (file_exists($v_rp)) {
        $offscreen = "";
        if (igk_server_is_linux())
            $offscreen = " 2>/dev/null > /dev/null  & ";
        else {
            $offscreen = " >NUL 2>NUL";
            $v_rp = "start /B /Min {$v_rp} ";
            $cmd = $v_rp . " " . igk_dir(IGK_LIB_RUNFILE) . " " . $f . $offscreen;
            pclose(popen($cmd, "r"));
            return 1;
        }
        $cmd = $v_rp . " " . igk_dir(IGK_LIB_RUNFILE) . " " . $f . $offscreen;
        return shell_exec($cmd);
    }
    return false;
}
///<summary> arguments or files lists</summary>
/**
 *  arguments or files lists
 */
function igk_run_scripts($files)
{
    $files = is_array($files) ? implode(" ", $files) : $files;
    $v_rp = igk_get_run_script_path();
    $cmd = $v_rp . " " . igk_dir(IGK_LIB_RUNFILE) . " " . $files;
    $out = [];
    exec($cmd, $out, $ret);
    if ($ret == 0) {
    } else {
        igk_ilog("failed to run > " . $cmd);
    }
    return implode(IGK_LF, $out);
}
///<summary>shortcut to save config</summary>
/**
 * shortcut to save config
 */
function igk_save_config($force = false)
{
    return IGKAppConfig::getInstance()->saveConfig($force);
}
///<summary>save a module to path</summary>
///<param name="path"></param>
/**
 * save a module to path
 * @param mixed $path 
 */
function igk_save_module($path)
{
    throw new \IGK\System\Exceptions\NotImplementException(__FUNCTION__);
}
///<summary>get a secure uri. on ssl protocol</summary>
/**
 * get a secure uri. on ssl protocol
 */
function igk_secure_uri($s, $secured = false, $ssl_protocol = true)
{
    if (($secured && !$ssl_protocol) || ($ssl_protocol && igk_sys_srv_is_secure())) {
        $s = str_replace("http://", "https://", $s);
    }
    return $s;
}
///<summary></summary>
///<param name="uri"></param>
///<param name="method" default="POST"></param>
///<param name="args" default="null"></param>
///<param name="content" default="IGK_APP_FORM_CONTENT"></param>
///<param name="header" default="null"></param>
/**
 * 
 * @param mixed $uri 
 * @param mixed $method 
 * @param mixed $args 
 * @param mixed $content 
 * @param mixed $header 
 */
function igk_send_request($uri, $method = "POST", $args = null, $content = IGK_APP_FORM_CONTENT, $header = null)
{
    $postdata = "";
    if ($args != null)
        $postdata = http_build_query($args);
    if ($header == null) {
        $header = "";
        $header = 'Content-type: ' . $content . IGK_CLF . "Content-Length: " . strlen($postdata) . IGK_CLF;
    }
    $opts = array('http' => array(
        'method' => $method,
        'header' => $header,
        'content' => $postdata
    ));
    $context = stream_context_create($opts);
    $result = @file_get_contents($uri, false, $context);
    return $result;
}
///<summary></summary>
/**
 * 
 */
function igk_server_is_linux()
{
    return strtolower(PHP_OS) == "linux";
}
///<summary>shortcut to Server::IsLocal()</summary>
/**
 * shortcut to Server::IsLocal()
 */
function igk_server_is_local()
{
    return Server::IsLocal();
}
///<summary></summary>
/**
 * 
 */
function igk_server_is_redirecting()
{
    $f = igk_server()->SCRIPT_NAME;
    return (igk_server()->REDIRECT_URL != null) && (basename($f) == "igk_redirection.php");
}
///<summary></summary>
/**
 * 
 */
function igk_server_is_refreshing()
{
    return igk_getctrl("igkpagectrl", false)->isRefreshing;
}
///<summary></summary>
/**
 * 
 */
function igk_server_is_window()
{
    return preg_match("/(winnt)/i", strtolower(PHP_OS));
}
///<summary>grant access to any platform server</summary>
/**
 * grant access to any platform server
 */
function igk_server_request_from_balafon()
{
    return igk_getv(igk_get_allheaders(), "IGK_SERVER") == IGK_PLATEFORM_NAME;
}
///<summary>check if request in on local server</summary>
/**
 * check if request in on local server
 */
function igk_server_request_onlocal_server()
{
    return igk_server()->REMOTE_ADDR === igk_server()->SERVER_ADDR;
}
///<summary></summary>
/**
 * 
 */
function igk_session_block_exit_callback()
{
    $t = null;
    if (($c = igk_getctrl(IGK_SESSION_CTRL, false)) && isset($c->TargetNode) && ($t = $c->TargetNode)) {
        $t->onAppExit();
    }
}

///<summary>check if session file exists</summary>
/**
 * check if session file exists
 */
function igk_session_exists($id, &$filesize = null)
{
    $d = ini_get("session.save_path");
    $f = igk_dir($d . "/" . IGK_SESSION_FILE_PREFIX . $id);
    if (file_exists($f)) {
        $filesize = filesize($f);
        return true;
    }
    return false;
}
///<summary>determine if session is active or not</summary>
/**
 * determine if session is active or not
 */
function igk_session_is_active()
{
    return session_status() === PHP_SESSION_ACTIVE;
}
///<summary>force session handle to default behaviour.</summary>
/**
 * force session handle to default behaviour.
 */
function igk_session_reset_handler()
{
    ini_set("session.save_handler", "files");
    register_shutdown_function('session_write_close');
    session_reset();
}
///<summary>session function to unlink session file if exists</summary>
/**
 * session function to unlink session file if exists
 */
function igk_session_unlinkfile($id)
{
    if (igk_app()->getApplication()->lib("session")) {
        return igk_app()->getApplication()->getLibrary()->session->unlink($id);
    }
    return false;
}
///<summary>call it at end to update the session</summary>
/**
 * call it at end to update the session
 */
function igk_session_update($id, $callback, $close = 1)
{
    if (!igk_session_exists($id) || ((session_id() == $id)))
        return;
    $session_cookie_name = igk_environment()->session_cookie_name;
    igk_session_reset_handler();
    session_name($session_cookie_name);
    igk_bind_session_id($id);
    @session_start();
    $callback($id, igk_app());
    if ($close)
        igk_sess_write_close();
}
///<summary></summary>
///<param name="n"></param>
///<param name="data"></param>
///<param name="duration" default="600"></param>
/**
 * set session cache data
 * @param string $n name for cache data
 * @param mixed $data data to store
 * @param int $duration duration
 */
function igk_set_cached(string $n, $data, int $duration = 600)
{
    $igk = igk_app();
    $c = $igk->Session->getParam("sys://cache");
    if ($c == null) {
        $c = array();
    }
    $c[$n] = (object)array(
        "data" => $data,
        "date" => igk_date_now(),
        "duration" => $duration
    );
    $igk->Session->setParam("sys://cache", $c);
}
///<summary></summary>
///<param name="n"></param>
///<param name="v" default="null"></param>
///<param name="override" default="1"></param>
///<param name="tm" default="null"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $v 
 * @param mixed $override 
 * @param mixed $tm 
 */
function igk_set_cookie($n, $v = null, $override = 1, $tm = null)
{
    $n = igk_get_cookie_name(igk_sys_domain_name() . "/" . $n);
    igk_set_global_cookie($n, $v, $override, $tm);
}
///<summary>store environment data as array</summary>
/**
 * store environment data as array
 */
function igk_set_env_array($k, $v)
{
    $s = igk_get_env($k);
    if (!is_array($s)) {
        $s = array();
    }
    $s[] = $v;
    igk_set_env($k, $s);
    return $s;
}
///<summary>set environment data value as assoc $key => $value</summary>
/**
 * set environment data value as assoc $key => $value
 */
function igk_set_env_keys($n, $k, $v)
{
    $s = igk_get_env($n);
    if (!is_array($s)) {
        $s = array();
    }
    $s[$k] = $v;
    igk_set_env($n, $s);
    return $s;
}
///<summary>set error</summary>
/**
 * set error
 */
function igk_set_error($tag, $message, $info = null)
{
    igk_push_env("sys://" . __FUNCTION__, array("tag" => $tag, "message" => $message, "info" => $info));
}
///<summary>set error message</summary>
/**
 * set error message
 */
function igk_set_error_msg($tab)
{
    $t = igk_get_env("sys://error_msgs");
    if (is_array($t)) {
        $t = array_merge($t, $tab);
    }
    igk_set_env("sys://error_msgs", $t);
}
///<summary></summary>
///<param name="callback"></param>
/**
 * 
 * @param mixed $callback 
 */
function igk_set_export_callback($callback)
{
    igk_set_env("sys://export_callback", $callback);
}
///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab 
 */
function igk_set_form_args($tab)
{
    igk_set_env("sys://form_args", $tab);
}
///<summary></summary>
///<param name="id"></param>
///<param name="value" default="null"></param>
/**
 * 
 * @param mixed $id 
 * @param mixed $value 
 */
function igk_set_form_value($id, $value = null)
{
    $tab = igk_get_env("sys://form_args");
    if (!$tab) {
        $tab = array();
    }
    $tab[$id] = $value;
    igk_set_env("sys://form_args", $tab);
    return $tab;
}
///<summary></summary>
///<param name="n"></param>
///<param name="v" default="null"></param>
///<param name="override" default="1"></param>
///<param name="tm" default="null"></param>
///<param name="dom" default="null"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $v 
 * @param mixed $override 
 * @param mixed $tm 
 * @param mixed $dom 
 */
function igk_set_global_cookie($n, $v = null, $override = 1, $tm = null, $dom = null, $secure = false, $options = null)
{
    if (headers_sent()) {
        return false;
    }
    $rs = igk_getv($_COOKIE, $n);
    if (!isset($_COOKIE[$n]) || $override) {
        !$dom &&
            $dom = igk_get_cookie_domain();
        $tdom = ["expires" => $tm !== null ? 0 : time() + (86400 * 7), "path" => "", "domain" => "", "secure" => $secure, "httponly" => false];
        if (!empty($dom))
            $tdom["domain"] = $dom;
        $toption = array_merge($tdom, $options ? $options : []);
        if (version_compare(PHP_VERSION, IGK_PHP_MIN_VERSION, ">=")) {
            $tdom["samesite"] = "Strict";
            setcookie($n, $v ?? '', $toption);
        } else {
            $toption = array_values($toption);
            setcookie($n, $v, ...$toption);
        }
        $_COOKIE[$n] = $v;
        return $v;
    }
    return $rs;
}
///<summary>used to set session global variable</summary>
/**
 * used to set session global variable
 */
function igk_set_globalvars($n, $d)
{
    $s = igk_app()->session->getParam(IGKSession::GLOBALVARS);
    if ($s == null)
        $s = array();
    if ($d == null) {
        if (isset($s[$n]))
            unset($s[$n]);
    } else {
        $s[$n] = $d;
    }
    igk_app()->session->setParam(IGKSession::GLOBALVARS, $s);
}

///<summary> set rendering node</summary>
/**
 *  set rendering node
 */
function igk_set_rendering_node($n)
{
    igk_set_env("sys://igk_html_rendered_node/node", $n);
}
///<summary></summary>
///<param name="engine"></param>
/**
 * 
 * @param mixed $engine 
 */
function igk_set_selected_builder_engine($engine)
{
    igk_set_env("sys://form/selectedbuilderengine", $engine);
}
///<summary>shortcut to set session param value</summary>
/**
 * shortcut to set session param value
 */
function igk_set_session($name, $v)
{
    igk_app()->getSession()->$name = $v;
}
///<summary>set session redirection page</summary>
/**
 * set session redirection page
 */
function igk_set_session_redirection($uri = null, $reset = 1)
{
    $redirect_key = "sys://func/" . __FUNCTION__;
    $h = igk_get_env($redirect_key);
    if ($reset) {
        $h = 0;
    }
    if ($h)
        return;
    if ($uri === null) {
        $uri = igk_io_request_uri_path();
    }
    igk_set_session(IGKSession::IGK_REDIRECTION_SESS_PARAM, $uri);
    igk_set_env($redirect_key, 1);
}
///<summary></summary>
///<param name="t"></param>
/**
 * set execution timeout helper
 * @param int $t 
 */
function igk_set_timeout(int $t)
{
    ini_set("max_execution_time", $t);
}
///set a request key=>$value
/**
 * set request helper 
 */
function igk_setr($key, $value)
{
    $_REQUEST[$key] = $value;
}
///<summary></summary>
///<param name="obj"></param>
///<param name="k"></param>
///<param name="v"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $k 
 * @param mixed $v 
 */
function igk_setv($obj, $k, $v)
{
    if (is_object($obj))
        $obj->$k = $v;
    else
        $obj[$k] = $v;
}
///<summary></summary>
///<param name="obj"></param>
/**
 * 
 * @param mixed $obj 
 */
function igk_show_code($obj)
{
    igk_wl("<code>");
    igk_wl($obj);
    igk_wl("</code>");
}
///<summary></summary>
///<param name="doc" default="null"></param>
///<param name="code" default="404"></param>
///<param name="redirect" default="null"></param>
///<param name="message" default="null"></param>
/**
 * 
 * @param mixed $doc 
 * @param mixed $code 
 * @param mixed $redirect 
 * @param mixed $message 
 */
function igk_show_error_doc($doc = null, $code = 404, $redirect = null, $message = null)
{
    if ($redirect == null)
        $redirect = igk_server()->REDIRECT_URL;
    $g = igk_sys_getconfig("error_ctrl");
    $error_ctrl = $g ? igk_getctrl($g, false) : null;
    if ($error_ctrl && method_exists(get_class($error_ctrl), "ViewError")) {
        $error_ctrl->ViewError($code, $redirect);
        return;
    }
    $key = IGK_DOC_ERROR_ID;
    $doc = igk_create_node("div");
    $doc->setParam("id", $key);
    $doc->div()->setClass("igk-danger")->container()->addCol()->addSectionTitle()->setClass("alignl igk-font-title-1")->Content = __("/!\ ERROR");
    $container = $doc->div()->container();
    $t = $container->addRow()->addCol()->div();
    $t["class"] = "igk-notifybox igk-notifybox-danger";
    $b = $t->add("blockquote");
    $b["cite"] = igk_io_baseuri();
    $b->div()->Content = __("msg.error.requestedpagenotfount_1", $code . " ----: " . $redirect . "");
    if (!empty($message)) {
        $t = $container->addRow()->addCol()->div();
        $t->Content = "Message : " . $message;
    }
    $container->addRow()->addCol()->div()->addA(igk_io_baseuri())->Content = __("Go home");
    $opt = HtmlRenderer::CreateRenderOptions();
    $opt->Context = "mail";
    $doc->renderAJX($opt);
}
///<summary>get the global application folder</summary>
/**
 * get the global application folder
 */
function igk_show_exception($ex, $file = null, $line = null, $title = null)
{
    IGK\Helper\ExceptionUtils::ShowException($ex, $file, $line, $title);
}
///<summary>Represente igk_show_exception_trace function</summary>
///<param name="callers"></param>
///<param name="depth" default="0"></param>
/**
 * Represente igk_show_exception_trace function
 * @param mixed $callers 
 * @param mixed $depth 
 */
function igk_show_exception_trace($callers, $depth = 0)
{
    $o = "";
    for ($i = $depth; $i < count($callers); $i++) {
        //+ show file before line to cmd+click to be handle

        $f = igk_getv($callers[$i], "function");
        $c = igk_getv($callers[$i], "class", "__global");
        $o .= igk_getv($callers[$i], "file") . ":" . igk_getv($callers[$i], "line") . PHP_EOL;
    }
    echo $o;
}
///<summary></summary>
///<param name="array"></param>
/**
 * 
 * @param mixed $array 
 */
function igk_show_keytype($array)
{
    $out = "<pre>";
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            $out .= $k . " =&gt; " . $v . IGK_LF;
        }
    }
    $out .= "</pre>";
    igk_wl($out);
}
///<summary></summary>
///<param name="obj"></param>
/**
 * 
 * @param mixed $obj 
 */
function igk_show_prev($obj)
{
    igk_wl("<pre class=\"igk-prev igk-prev-sys\">");
    print_r($obj);
    igk_wl("</pre>");
}
///<summary></summary>
///<param name="target"></param>
///<param name="obj"></param>
/**
 * 
 * @param mixed $target 
 * @param mixed $obj 
 */
function igk_show_prevto($target, $obj)
{
    $s = IGK_STR_EMPTY;
    ob_start();
    igk_show_prev($obj);
    $s = ob_get_contents();
    ob_end_clean();
    $target->div()->Content = $s;
}
///<summary></summary>
///<param name="render" default="1"></param>
/**
 * 
 * @param mixed $render 
 */
function igk_show_serverinfo($render = 1)
{
    $t = "";
    $t .= "<div><h1>Server Info</h1></div><table class='igk-tab-serve-info'>";
    foreach ($_SERVER as $k => $v) {
        $t .= "<tr>";
        $t .= "<td>" . $k . "</td>";
        $t .= "<td>" . $v . "</td>";
        $t .= "</tr>";
    }
    $t .= "</table>";
    if ($render) {
        igk_wl($t);
    }
    return $t;
}
///<summary></summary>
///<param name="obj"></param>
/**
 * 
 * @param mixed $obj 
 */
function igk_show_textarea($obj)
{
    igk_wl("<textarea>");
    igk_wl($obj);
    igk_wl("</textarea>");
}
///<summary>return the tracing information info</summary>
/**
 * return the tracing information info
 */
function igk_show_trace($depth = 1)
{
    if (!defined("IGK_APP_DIR")) {
        define('IGK_APP_DIR', IGK_LIB_DIR . "/temp");
    }
    if (igk_get_env("sys://TRACING")) {
        return ".tracing.....";
    }
    $def = defined("IGK_TRACE_CLEAN");
    if ($def) {
        ob_clean();
    }
    $callers = debug_backtrace();
    igk_set_env("sys://TRACING", 1);
    $title = "Trace Info";
    $t_class = "Class";
    $t_line = "Line";
    $t_func = "Function";
    $t_from = " FROM ";
    if (IGKApp::IsInit()) {
        $title = __($title);
        $t_class = __($t_class);
        $t_func = __($t_func);
        $t_line = __($t_line);
    }
    $o = "";
    if (igk_is_cmd()) {
        $tc = 0;
        for ($i = $depth; $i < count($callers); $i++, $tc++) {
            //+ show file before line to cmd+click to be handle

            $f = igk_getv($callers[$i], "function");
            $c = igk_getv($callers[$i], "class", "__global");
            $o .= igk_getv($callers[$i], "file") . ":" . igk_getv($callers[$i], "line") . PHP_EOL;
        }
        echo $o;
    } else {
        $o = "<div class=\"traceinfo\">";
        $o .= "<div class=\"igk-title-4\">{$title}</div>";
        $o .= "<div>";
        $o .= "<span>" . igk_io_request_uri() . "</span> <span>" . $t_from . "</span>";
        $o .= "<ul>";
        foreach ($callers[0] as $k => $v) {
            $o .= "<li><span>" . $k . ":</span>";
            if (!is_array($v))
                $o .= "<span>" . $v . "</span>";
        }
        $o .= "</ul>";
        $o .= "</div>";
        $o .= "<div>";
        $o .= "<table>";
        for ($i = $depth; $i < count($callers); $i++) {
            $f = igk_getv($callers[$i], "function");
            $c = igk_getv($callers[$i], "class", "__global");
            $o .= "<tr>";
            $o .= "<td>" . igk_getv($callers[$i], "line") . "</td>";
            $o .= "<td>" . igk_getv($callers[$i], "file") . "</td>";
            $o .= "<td>" . $f . "</td>";
            $o .= "<td>" . $c . "</td>";
            $o .= "</tr>";
        }
        $o .= "</table>";
        $o .= "</div>";
        $o .= "</div>";
    }
    return $o;
}
///<summary></summary>
///<param name="a"></param>
///<param name="b"></param>
/**
 * 
 * @param mixed $a 
 * @param mixed $b 
 */
function igk_sort_bynodeindex($a, $b)
{
    if ($a->TargetNode && $b->TargetNode) {
        $i = $a->TargetNode->Index;
        $j = $b->TargetNode->Index;
        return ($i == $j) ? 0 : (($i < $j) ? -1 : 1);
    }
    return strcmp($a->Name, $b->Name);
}
///<summary>retrieve sql data from from balafon engine type</summary>
///<param name="type">system Query type</param>
/**
 * retrieve sql data from from balafon engine type
 * @param mixed $type system Query type
 */
function igk_sql_data_type($t)
{
    switch (strtolower($t)) {
        case "int":
        case "integer":
            return "Int";
        case "varchar":
            return "VARCHAR";
        case "text":
        case "string":
            return "TEXT";
    }
    return strtoupper($t);
}
///<summary>Represente igk_src_code function</summary>
///<param name="src"></param>
///<param name="start"></param>
///<param name="end"></param>
/**
 * Represente igk_src_code function
 * @param mixed $src 
 * @param mixed $start 
 * @param mixed $end 
 */
function igk_src_code($src, $start, $end)
{
    return implode("\n", array_filter(array_slice(explode("\n", $src), $start, $end)));
}

///<summary>store start time</summary>
/**
 * store start time
 */
function igk_start_time($name = null)
{
    $t = microtime(true);
    igk_set_env("sys://env/starttime" . ($name ? "/{$name}" : ""), $t);
    return $t;
}
///<summary>Represente igk_start_time_process function</summary>
///<param name="name"></param>
///<param name="callback" type="callable"></param>
/**
 * Represente igk_start_time_process function
 * @param mixed $name 
 * @param callable $callback 
 */
function igk_start_time_process($name, callable $callback)
{
    igk_start_time($name);
    $response = $callback();
    $execute_time = igk_execute_time($name);
    return compact("response", "execute_time");
}
///end package manager
/**
 */
function igk_stop_timeout()
{
    igk_set_timeout(0);
}
///<summary>used to add data to value</summary>
/**
 * used to add data to value
 */
function igk_str_append_to(&$s, $v, $sep = ',')
{
    if (empty($v)) {
        return;
    }
    if (empty($s))
        $s .= $v;
    else
        $s = $sep . $v;
}
///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab 
 */
function igk_str_array_rm_empty($tab)
{
    $o = array();
    foreach ($tab as $k) {
        if (empty($k))
            continue;
        $o[] = $k;
    }
    return $o;
}
///<summary>used to capitalize string</summary>
/**
 * used to capitalize string
 */
function igk_str_capitalize($s)
{
    return ucfirst($s);
}
///<summary>check if the length of string is equal to 0</summary>
/**
 * check if the length of string is equal to 0
 */
function igk_str_empty($t)
{
    return strlen($t) === 0;
}
if (!function_exists('igk_str_transform_linefeed')) {
    /**
     * tranform \n|\r|\t string to litteral expression. if \\n leave at is
     * @param string $v haystack
     * @param string $feed feed expression
     * @return string
     */
    function igk_str_transform_linefeed(string $v, ?array $feed = null):string
    {
        if (is_null($feed)) {
            $feed = ['\n', '\r', '\t'];
        }
        while (count($feed) > 0) {
            $q = array_shift($feed);
            $pos = 0;
            while (($pos = strpos($v, $q, $pos)) !== false) {
                if ($pos > 0) {
                    if ($v[$pos - 1] == "\\") {
                        $pos += 2;
                        continue;
                    }
                }
                $v = substr($v, 0, $pos) . "\n" . substr($v, $pos + 2);
                $pos++;
            }
        }
        return $v;
    }
}
///<summary></summary>
///<param name="chaine"></param>
///<param name="pattern"></param>
/**
 * 
 * @param mixed $chaine 
 * @param mixed $pattern 
 */
function igk_str_endwith($chaine, $pattern)
{
    return IGKString::EndWith($chaine, $pattern);
}
///<summary>detect all loadable tab in the string and replace with htmlentities</summary>
/**
 * detect all loadable tab in the string and replace with htmlentities
 */
function igk_str_escape_tag($s)
{
    if (empty($s))
        return $s;
    $offset = 0;
    $c = 0;
    while (preg_match("/<(\/)?(?P<tag>(script|img|object|iframe|style|link))(\s+(\/?>)?|>)/i", $s, $tab, PREG_OFFSET_CAPTURE, $offset)) {
        $_close_f = $tab[0][0][strlen($tab[0][0]) - 1] == ">";
        $m = str_replace("<", "&lt;", $tab[0][0]);
        $m = str_replace(">", "&gt;", $m);
        $s_ln = strlen($tab[0][0]);
        if (!$_close_f) {
            $pos = $offset + $s_ln;
            $ln = strlen($s);
            while ($pos < $ln) {
                $ch = $s[$pos];
                $s_ln++;
                if ($ch == ">") {
                    $m .= "&gt;";
                    break;
                }
                switch ($ch) {
                    case '"':
                    case "'":
                        $m .= htmlentities(igk_str_read_brank($s, $pos, $ch, $ch), ENT_NOQUOTES, "UTF-8");
                        $s_ln = $pos + 1;
                        break;
                    default:
                        $m .= $ch;
                        break;
                }
                $pos++;
            }
        }
        $s = substr($s, 0, $tab[0][1]) . $m . substr($s, $tab[0][1] + $s_ln);
        $offset = $tab[0][1] + strlen($m);
        $c++;
    }
    return $s;
}
///<summary>Represente igk_str_escape_tag_replace function</summary>
///<param name="s" ref="true"></param>
///<param name="offset" ref="true"></param>
///<param name="tab"></param>
///<param name="entityflag" default="ENT_NOQUOTES"></param>
///<param name="encoding" default="UTF-8"></param>
/**
 * Represente igk_str_escape_tag_replace function
 * @param mixed $s 
 * @param mixed $offset 
 * @param mixed $tab 
 * @param mixed $entityflag 
 * @param mixed $encoding 
 */
function igk_str_escape_tag_replace(&$s, &$offset, $tab, $entityflag = ENT_NOQUOTES, $encoding = "UTF-8")
{
    $_close_f = $tab[0][0][strlen($tab[0][0]) - 1] == ">";
    $m = str_replace("<", "&lt;", $tab[0][0]);
    $m = str_replace(">", "&gt;", $m);
    $s_ln = $tab[0][1] + strlen($tab[0][0]);
    if (!$_close_f) {
        $pos = $s_ln;
        $ln = strlen($s);
        while ($pos < $ln) {
            $ch = $s[$pos];
            $s_ln++;
            if ($ch == ">") {
                $m .= "&gt;";
                break;
            }
            switch ($ch) {
                case '"':
                case "'":
                    $m .= htmlentities(igk_str_read_brank($s, $pos, $ch, $ch), $entityflag, $encoding);
                    $s_ln = $pos + 1;
                    break;
                default:
                    $m .= $ch;
                    break;
            }
            $pos++;
        }
    }
    $b = substr($s, 0, $tab[0][1]) . $m;
    $s = $b . substr($s, $s_ln);
    $offset = strlen($b);
}
///<summary></summary>
///<param name="array"></param>
///<param name="str"></param>
/**
 * 
 * @param mixed $array 
 * @param mixed $str 
 */
function igk_str_explode($array, $str)
{
    $t = array();
    if (count($array) > 0) {
        $k = explode($array[0], $str);
        $array = array_slice($array, 1);
        foreach ($k as $kt) {
            $t = array_merge($t, igk_str_explode($array, $kt));
        }
    } else {
        $t[] = $str;
    }
    return $t;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_str_explode_uppercase($v)
{
    $o = array();
    $v = preg_replace_callback(
        "/([a-z0-9]*)([A-Z])/",
        function ($m) use (&$o) {
            $c = count($o);
            if ($c == 0) {
                $o[] = substr($m[0], 0, -1);
                $o[] = $m[2];
            } else {
                $o[$c - 1] .= substr($m[0], 0, -1);
                $o[] = $m[2];
            }
            return "";
        },
        $v
    );
    if (!empty($v)) {
        $o[count($o) - 1] .= $v;
    }
    return $o;
}
///<summary></summary>
///<param name="str"></param>
/**
 * 
 * @param mixed $str 
 */
function igk_str_expr($str)
{
    $str = str_replace(".", "\.", $str);
    return $str;
}
///<summary>bind data using format</summary>
///<param name="data">mixed. array of data or string</param>
/**
 * bind data using format
 * @param mixed $data mixed. array of data or string
 */
function igk_str_format_bind($format, $data)
{
    $o = "";
    if (is_array($data)) {
        foreach ($data as $k) {
            $o .= igk_str_format($format, $k);
        }
    } else {
        $o = igk_str_format($format, $data);
    }
    return $o;
}
///<summary>used to retrieve pattern keys from path pattern expression</summary>
///<code>used in IGKUri</code>
/**
 * used to retrieve pattern keys from path pattern expression
 */
function igk_str_get_pattern_keys($s)
{
    $tab = array();
    $t = array();
    $s = preg_match_all("#:(?P<name>([a-z0-9]+))\+?#i", $s, $t);
    for ($i = 0; $i < $s; $i++) {
        $tab[] = $t["name"][$i];
    }
    return $tab;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_str_get_version($v)
{
    $tb = explode(".", $v);
    if (igk_count($tb) < 4) {
        $c = igk_count($tb) - 4;
        while ($c > 0) {
            $tb[] = 0;
            $c--;
        }
    } else {
        $tb = array_shift($tb, 4);
    }
    return igk_str_join_tab($tb, '.', false);
}
///<summary>glue data.</summary>
/**
 * glue data.
 */
function igk_str_glue($mix = ",")
{
    $tab = array_slice(func_get_args(), 1);
    if (igk_count($tab) <= 0)
        return null;
    $s = "";
    $fc = 0;
    if (is_string($mix))
        $fc = function ($v, &$s = null) {
            return $v;
        };
    else if (is_callable($mix)) {
        $fc = $mix;
        $mix = "";
    } else
        return null;
    foreach ($tab as $v) {
        if (empty($v))
            continue;
        $h = $fc($v, $s);
        if (empty($s)) {
            $s = $h;
        } else
            $s .= $mix . $h;
    }
    return $s;
}
///<summary>shortcut to IGKString::IndexOf</summary>
/**
 * shortcut to IGKString::IndexOf
 */
function igk_str_index_of($str, $pattern, $offset = 0)
{
    return IGKString::IndexOf($str, $pattern, $offset);
}

///<summary></summary>
///<param name="str1"></param>
///<param name="str2"></param>
///<param name="pattern"></param>
/**
 * 
 * @param mixed $str1 
 * @param mixed $str2 
 * @param mixed $pattern 
 */
function igk_str_join($str1, $str2, $pattern)
{
    return $str1 . $pattern . $str2;
}
///<summary></summary>
///<param name="tab"></param>
///<param name="separator" default="','"></param>
///<param name="key" default="true"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $separator 
 * @param mixed $key 
 */
function igk_str_join_tab($tab, $separator = ',', $key = true)
{
    return IGKString::Join($tab, $separator, $key);
}
///<summary>convert string to php namespace name</summary>
/**
 * convert string to php namespace name
 */
function igk_str_ns($n)
{
    $n = str_replace(".", "\\", $n);
    $n = str_replace("/", "\\", $n);
    $n = preg_replace("/[^0-9a-z\\\\_]/i", "_", $n);
    return implode("_", array_filter(explode("_", str_replace(" ", "_", str_replace("/", "\\", $n)))));
}
///<summary>Represente igk_str_pipe_args function</summary>
///<param name="src"></param>
///<param name="c" ref="true"></param>
///<param name="removequote" default="0"></param>
/**
 * Represente igk_str_pipe_args function
 * @param mixed $src 
 * @param mixed $c 
 * @param mixed $removequote 
 */
function igk_str_pipe_args($src, &$c, $removequote = 0)
{
    $c = 0;
    $v = "";
    $ln = strlen($src);
    $end = false;
    while (!$end && ($c < $ln)) {
        $ch = $src[$c];
        switch ($ch) {
            case '|':
                $end = true;
                break;
            case "'":
                $v .= igk_str_read_brank($src, $c, $ch, $ch);
                break;
            default:
                $v .= $ch;
                break;
        }
        $c++;
    }
    $v = trim($v);
    if (($removequote) && (strlen($v = trim($v)) > 0) && ($v[0] == "'")) {
        $v = substr($v, 1, -1);
    }
    $pipe = substr($src, $c);
    return [$v, $pipe];
}
///<summary></summary>
///<param name="v"></param>
/**
 * helper: get piped data
 * @param mixed $v 
 */
function igk_str_pipe_data($src, $removequote = 0)
{
    list($v, $pipe) = igk_str_pipe_args($src, $c, $removequote);
    return igk_str_pipe_value($v, $pipe);
}
///<summary>pipe data</summary>
///<param name="v">data to evaluate</param>
///<param name="pipe">string | separated</param>
/**
 * pipe data
 * @param mixed $v data to evaluate
 * @param mixed $pipe string | separated
 */
function igk_str_pipe_value($v, $pipe)
{

    $tpipe = explode('|', $pipe);
    $loc_t = igk_reg_pipe(null);
    foreach ($tpipe as $s) {
        $s = trim($s);
        if (empty($s)) {
            continue;
        }
        $args = [$v];
        if (($pos = strpos($s, ";")) !== false) {
            $exp = substr($s, $pos + 1);
            $s = substr($s, 0, $pos);
            $tab = igk_get_query_options($exp);
            if ($tab) {
                $args[] = $tab;
            } else if (!empty($exp)) {
                $args = array_merge($args, StringUtility::ReadArgs($exp));
            }
        }
        $fc = igk_getv($loc_t, $s);
        if ($fc && igk_is_callable($fc)) {
            $v = igk_invoke_callback_obj(null, $fc, $args);
        }
    }
    return $v;
}
///<summary>remove magic cote from message</summary>
/**
 * remove magic cote from message
 * @param $content 
 * @return string stripped quote
 */
function igk_str_quotes(string $content)
{
    if (is_string($content) && ini_get("magic_quotes_gpc")) {
        $content = stripcslashes($content);
    }
    return $content;
}
///<summary></summary>
///<param name="s"></param>
/**
 * read argument
 * @param string $s 
 */
function igk_str_read_args(string $s)
{
    $count = strlen($s);
    $i = 0;
    $tab = array();
    $v = "";
    while ($i < $count) {
        $ch = $s[$i];
        $i++;
        switch ($ch) {
            case " ":
                if (!empty($v)) {
                    $tab[] = $v;
                    $v = null;
                }
                break;
            case '"':
                if (empty($v)) {
                    $i--;
                    $rgx = igk_str_read_brank($s, $i, $ch, $ch, null, 1);
                    $v .= $rgx;
                    $m = 4;
                    $i++;
                    $tab[] = $v;
                    $v = null;
                } else {
                    $v .= $ch;
                }
                break;
            default:
                $v .= $ch;
                break;
        }
    }
    if (!empty($v)) {
        $tab[] = $v;
    }
    return $tab;
}
///<summary></summary>
///<param name="treat"></param>
///<param name="goptions" default="null" ref="true"></param>
/**
 * 
 * @param mixed $treat 
 * @param mixed $goptions 
 */
function igk_str_read_bracket_source_code($treat, &$goptions = null)
{
    $goptions = $goptions ?? (function () {
        $s = igk_str_read_createoptions();
        $s->ignoreEmptyLine = 0;
        $s->removeComment = 0;
        $s->endOffset = 0;
        return $s;
    })();
    $m = igk_str_read_source_code_bracket(is_array($treat) ? $treat : [$treat], $goptions);
    return $m;
}
///<summary>used to read in brank</summary>
///<param name="exp">expression</param>
///<param name="$c">position offset </param>
///<param name="end">char end</param>
///<param name="start">char start </param>
///<param name="ln">ln: size to read</param>
///<param name="escaped">if end char must consider escape</param>
/**
 * used to read in brank
 * @param mixed $exp expression
 * @param mixed $$c position offset 
 * @param mixed $end char end
 * @param mixed $start char start 
 * @param mixed $ln ln: size to read
 * @param mixed $escaped if end char must consider escape
 * @param ?string $encased char to read
 */
function igk_str_read_brank($exp, int &$c, $end = "]", $start = "[", $ln = null, $escaped = 0, $autoclose = 1, $encapsechar = null)
{
    $iv = "";
    $ln = $ln ?? strlen($exp);
    $deep = -1;
    $litteral = $end == $start;
    if ($litteral) {
        if ($exp[$c] == $start) {
            $c++;
            $iv = $start;
        }
    } else {
        $escaped = 0;
    }
    $g = 0;
    $eh = 0;
    $encapsed_depth = 0;
    $ch = '';
    while ($ln > $c) {
        $ch = $exp[$c];
        // + | encapsed char " reading 
        if ($encapsechar && $litteral) {
            if ($ch == $encapsechar) {
                $encapsed_depth++;
            }
            if ($ch == $end) {
                if (($encapsed_depth % 2) == 0) {
                    break;
                }
                $iv .= $ch;
                $ch = '';
            }
        }


        if (!$eh && !(($ch != $end) || ($deep > 0))) {
            break;
        }
        if ($eh) {
            $eh = 0;
        } else
            $eh = ($escaped) && $ch == "\\";
        $iv .= $ch;
        $c++;
        if (!$litteral && !$g) {
            if ($ch == $start) {
                $deep++;
            } else if ($ch == $end)
                $deep--;
        }
        $g = 0;
    }
    if ((!empty($iv) && ($ch == $end)) || $autoclose)
        $iv .= $end;
    return $iv;
}
///<summary></summary>
/**
 * 
 */
function igk_str_read_callback_list()
{
    static $callbacklist;
    if ($callbacklist == null)
        $callbacklist = [
            "endmultilinestring" => function ($t, $start, &$offset, &$mode, $options, $match) {
                if ($match["name"][0] == $options->matchName) {
                    $mode = $options->{'@multlinestringmode'};
                    $options->datalf = $options->{'@datalf'};
                    $options->offsetBracket = $options->{'@offsetBracket'};
                    $options->ignoreEmptyLine = $options->{'@multistringEmptyLine'}
                        ?? 1;
                    $t = $options->data . IGK_LF . $match[0][0];
                    $offset = strlen($t);
                    unset($options->data);
                    unset($options->{'@multlinestringmode'});
                    unset($options->{'@multistringEmptyLine'});
                    unset($options->matchName);
                    $t .= IGK_LF;
                    $offset++;
                    if ($options->treatClassStatement) {
                        if ($mode == 0) {
                            if (strpos($match[0][0], ';', -1) !== false) {
                                $offset--;
                            }
                        }
                    }
                } else
                    $offset = strlen($t);
                return $t;
            }, "startmultilinestring" => function ($t, $start, &$offset, &$mode, $options, $match) {
                if ($mode == 3) {
                    $offset = $start + strlen($match[0][0]);
                    return $t;
                }
                $options->matchName = $match['name'][0];
                $ld = "";
                if ($options->FormatText) {
                    $ld = igk_str_read_get_intent($options);
                    $options->data .= $ld . trim($t);
                    $t = $options->data;
                    $options->data = "";
                }
                $offset = strlen($t);
                $options->{'@multistringEmptyLine'} = $options->ignoreEmptyLine;
                $options->{'@multlinestringmode'} = $mode;
                $options->{'@datalf'} = igk_getv($options, 'datalf');
                $options->{'@offsetBracket'} = igk_getv($options, 'offsetBracket');
                unset($options->datalf);
                $options->offsetBracket = 0;
                $options->ignoreEmptyLine = 0;
                $mode = 3;
                return $t;
            }, "uncollapsestringBracket" => function ($t, $start, &$offset, &$mode, $options) {
                $lis = $start;
                $s = igk_str_read_brank($t, $lis, $t[$start], $t[$start], null, 1);
                if (($mode == 10) && ($k = igk_getv($options, "saveString"))) {
                    if (!empty($d = trim($options->data)))
                        $options->outString[$k][] = $d;
                    $options->outString[$k][] = trim(substr($t, $offset, $start - $offset)) . trim($s);
                    $options->data = "";
                    $t = substr($t, $lis + 1);
                    $offset = 0;
                } else {
                    $offset = $lis + 1;
                }
                return $t;
            }, "ignoreComment" => function ($t, $start, &$offset, &$mode, $options) {
                $t = substr($t, 0, $start);
                $offset = strlen($t) + 1;
                return $t;
            }
        ];
    return $callbacklist;
}
///<summary>call this for cleaning code source</summary>
///<param name="lines">mixed. array of string </param>
///<param name="options"> options object with clean source parameter </param>
/**
 * call this for cleaning code source
 * @param mixed $lines mixed. array of string 
 * @param mixed $options  options object with clean source parameter 
 */
function igk_str_read_clean_source_code($lines, &$options = null)
{
    $tab = igk_str_read_create_cleanup_source();
    if ($options == null) {
        $options = igk_str_read_createoptions();
        $options->endLine = count($lines);
    } else {
        if (!isset($options->endLine) || ($options->endLine == -1))
            $options->endLine = count($lines);
    }
    $options->bracketDepth = -1;
    $options->stop = 0;
    if (!isset($options->endLine) || ($options->endLine == -1))
        $options->endLine = count($lines);
    $callbacklist = igk_str_read_callback_list();
    array_unshift($tab, (object)array(
        "name" => "uncollapsestring",
        "mode" => [0, 8, 9, 10],
        "pattern" => "/(\"|')/",
        "callback" => $callbacklist["uncollapsestringBracket"]
    ));
    array_unshift($tab, (object)array(
        "name" => "bracketIgnoreCollapseStart",
        "mode" => 8,
        "pattern" => "/\{/i",
        "callback" => function ($t, $start, &$offset, &$mode, $options, $data = null) {
            if (!isset($options->bracketDepth) || $options->bracketDepth == -1) {
                $options->bracketDepth = 0;
            } else {
                $options->bracketDepth++;
                $t = substr($t, $start + 1);
                $offset = 0;
                return $t;
            }
            $t = substr($t, 0, $start);
            $offset = strlen($t) + 1;
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "bracketIgnoreComment",
        "mode" => 8,
        "pattern" => "#//(.)+$#i",
        "callback" => $callbacklist["ignoreComment"]
    ));
    array_unshift($tab, (object)array(
        "name" => "defineConstant",
        "mode" => 0,
        "pattern" => "#\s*define\s*($|([\(])\s*)#",
        "callback" => function ($t, $start, &$offset, &$mode, $options, $data = null) {
            if (!isset($options->bracketDepth)) {
                igk_wln(igk_show_trace());
                igk_wln_e("backetNot define");
            }
            if ($options->bracketDepth > -1) {
                $offset = $start + strlen($data[0][0]);
                return $t;
            }
            if (igk_getv($options, "noHandleDefinitions")) {
                $offset = $start + strlen($data[0][0]);
                return $t;
            }
            $mode = 10;
            $offset = $start + strlen($data[0][0]);
            $options->saveString = "name";
            $options->outString = array("name" => array(), "value" => array());
            $t = substr($t, $offset);
            $offset = 0;
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "endReadDefineConstant",
        "mode" => 10,
        "pattern" => "#(\)|\)\s*)?;#i",
        "callback" => function ($t, $start, &$offset, &$mode, $options, $data = null) {
            $txt = trim($data[0][0]);
            if ($emode = igk_getv($options, 'endMode')) {
                if (!preg_match("/;$/", $txt)) {
                    igk_wln_e("content not end with ';': " . $mode . ": " . $options->lineNumber . "--- : ---" . $txt);
                }
                $t = substr($t, $start + strlen($txt) + 1);
                $offset = 0;
                $mode = 0;
                unset($options->endMode);
            } else {
                $rm = "";
                if (!empty($v_c = trim($options->data))) {
                    if ($txt == ";") {
                        if ($v_c[strlen($v_c) - 1] == ")") {
                            $v_c = substr($v_c, 0, -1);
                        }
                    }
                    $rm .= $v_c;
                }
                $options->data = "";
                $rm .= trim(substr($t, $offset, $start - $offset));
                $v = $options->outString["value"];
                if (!empty($v_c = trim($rm)))
                    $v[] = $v_c;
                $name = implode("", $options->outString["name"]);
                $value = implode("", $v);
                if (!isset($options->definitions)) {
                    $options->definitions = array();
                }
                $options->definitions["constants"][] = [$name, $value];
                $t = substr($t, $start + strlen($data[0][0]));
                $offset = 0;
                unset($options->saveString);
                unset($options->outString);
                if (preg_match("/;$/", $data[0][0])) {
                    $mode = 0;
                } else {
                    $options->endMode = 10;
                }
            }
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "endReadDefineConstantName",
        "mode" => 10,
        "pattern" => "#s*,\s*#i",
        "callback" => function ($t, $start, &$offset, &$mode, $options, $data = null) {
            if ($options->saveString == 'value') {
                $offset = $start + strlen($data[0][0]);
                $options->outString["value"][] = $data[0][0];
                return $t;
            }
            $options->saveString = 'value';
            $offset = 0;
            $t = substr($t, $start + 1);
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "bracketCollapseIngoreEnd",
        "mode" => 8,
        "pattern" => "/\}/i",
        "callback" => function ($t, $start, &$offset, &$mode, $options, $data = null) {
            $t = substr($t, $start + 1);
            $offset = 0;
            $options->bracketDepth--;
            if (empty(trim($t))) {
                $offset = strlen($t) + 1;
            }
            if ($options->bracketDepth < 0) {
                $options->stop = 0;
                $mode = 0;
                $options->bracketDepth = -1;
                $options->data = "";
                $offset = 0;
            }
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "startBracket",
        "mode" => 0,
        "pattern" => "/\{/i",
        "callback" => function ($t, $start, &$offset, &$mode, $options) {
            $offset = $start + 1;
            if (!isset($options->bracketDepth) || ($options->bracketDepth == -1)) {
                $options->bracketDepth = 0;
                $goptions = (object)array(
                    "removeComment" => $options->removeComment,
                    "ignoreEmptyLine" => $options->ignoreEmptyLine,
                    "startLine" => 0,
                    "endLine" => 1,
                    "bracketDepth" => $options->bracketDepth,
                    "offset" => 0,
                    "outOffset" => 0
                );
                $m = igk_str_read_bracket_source_code(substr($t, $start), $goptions);
                if ($goptions->stop) {
                    $lnend = substr($t, $start + $goptions->outOffset);
                    $t = substr($t, 0, $start) . $m;
                    $offset = strlen($t);
                    $t .= $lnend;
                    $options->bracketDepth = -1;
                } else {
                    $options->bracketDepth = $goptions->bracketDepth;
                    $offset = strlen($t) + 1;
                }
            } else {
                $options->bracketDepth++;
                $mode = 0;
            }
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "endNormalBracket",
        "mode" => 0,
        "pattern" => "/\}/i",
        "callback" => function ($t, $start, &$offset, &$mode, $options) {
            $offset = $start + 1;
            $options->bracketDepth--;
            if ($options->bracketDepth < -1) {
                igk_wln_e(__LINE__ . ": failed for normal bracket end: " . $options->bracketDepth . " = " . $options->lineNumber);
            }
            if ($options->bracketDepth == 0) {
                $options->bracketDepth = -1;
            }
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "markGroup",
        "mode" => 0,
        "pattern" => "/(?P<type>(trait|function|((abstract|final) \\s*)?class|interface)) \s*([^\(])?/m",
        "callback" => function ($t, $start, &$offset, &$mode, $options, $data = null) {
            if ($options->bracketDepth > -1) {
                $offset = $start + strlen($data[0][0]);
                return $t;
            }
            $lis = $start;
            $type = preg_match("/(function|class|trait|interface)/", $data["type"][0], $tab) ? $tab[0] : 0;
            if (!empty($options->data)) {
                $sdata = $options->data;
                $ln = strlen($sdata);
                $t = $t . $sdata;
                unset($sdata);
            }
            $options->data = "";
            $options->outOffset = $start;
            switch ($type) {
                case "class":
                case "function":
                case "interface":
                    $n = strpos($t, "{", $start + 1);
                    if ($n !== false) {
                        $treat = substr($t, $start);
                        $t = substr($t, 0, $start);
                        $goptions = (object)array(
                            "stop" => 0,
                            "ignoreEmptyLine" => 0,
                            "removeComment" => 0,
                            "offset" => $n - $start,
                            "startLine" => 0,
                            "endLine" => 1,
                            "bracketDepth" => -1,
                            "id" => "readMarkGroupSourceCodeBracket"
                        );
                        $m = igk_str_read_source_code_bracket([$treat], $goptions);
                        if (!$goptions->stop || !preg_match("/\}$/i", trim($m))) {
                            $options->bracketDepth = $goptions->bracketDepth;
                            $mode = 8;
                            $offset = strlen($t) + 1;
                        } else {
                            $mode = 0;
                            $options->outOffset += strlen($m);
                            $ss = substr($treat, 0, $start) . substr($treat, $n + strlen($m));
                            $t = $ss;
                            $offset = strlen($t);
                        }
                    } else {
                        $mode = 8;
                        $t = substr($t, 0, $start);
                        $offset = strlen($t) + 1;
                    }
                    break;
            }
            return $t;
        }
    ));
    return igk_str_read_source_code(
        $lines,
        function ($text) use ($options) {
            $s = "";
            return $text;
        },
        $options,
        $tab
    );
}
///<summary></summary>
/**
 * 
 */
function igk_str_read_create_cleanup_source()
{
    $c = igk_str_read_callback_list();
    return array(
        (object)array(
            "name" => "endmultilinestring",
            "pattern" => "/^(?P<name>[a-z]+)($|;)?/i",
            "mode" => 3,
            "callback" => $c["endmultilinestring"]
        ),
        (object)array(
            "name" => "startmultilinestring",
            "pattern" => "#\<\<\<(')?(?P<name>[a-z]+)(\\1)?\s*$#i",
            "mode" => 0,
            "callback" => $c["startmultilinestring"]
        ),
        (object)array(
            "name" => "endcomment",
            "pattern" => "#\*\/#i",
            "mode" => 1,
            "callback" => function ($t, $start, &$offset, &$mode, $options) {
                $offset = $start + 2;
                $mode = 0;
                $s = "";
                if (!$options->removeComment) {
                    $s = $options->data . IGK_LF . substr($t, 0, $start + 2);
                    igk_wln_e("End comment " . $offset, $options);
                }
                unset($options->data);
                $t = $s . substr($t, $offset);
                return $t;
            }
        ),
        (object)array(
            "name" => "startcomment",
            "pattern" => "#/\*#i",
            "mode" => 0,
            "callback" => function ($t, $start, &$offset, &$mode, $options) {
                if (($j = strpos($t, "*/", $start)) !== false) {
                    $offset = $j + 2;
                    if ($options->removeComment) {
                        $t = substr($t, 0, $start) . substr($t, $offset);
                    }
                } else {
                    $mode = 1;
                    $offset = strlen($t);
                }
                return $t;
            }
        ),
        (object)array(
            "pattern" => "#(\"|')#i",
            "mode" => 0,
            "name" => "stringReading",
            "callback" => function ($t, $start, &$offset, &$mode, $options) {
                if ($mode != 0) {
                    $offset = $start + 1;
                    return $t;
                }
                $ch = $t[$start];
                $value = igk_str_read_brank($t, $start, $ch, $ch, null, 1);
                $offset = $start + 1;
                return $t;
            }
        ),
        (object)array(
            "pattern" => "#//(.)*$#",
            "name" => "singleLineComment",
            "mode" => 0,
            "callback" => function ($t, $start, &$offset, &$mode, $options) {
                if ($options->removeComment) {
                    $s = trim(substr($t, 0, $start));
                    $a = "";
                    if (!empty($s)) {
                        if (preg_match("/\s$/", $t, $c) && ($c[0] == "\r")) {
                            $a = "\r";
                        }
                        $t = $s . $a;
                    } else
                        $t = "";
                }
                $offset = strlen($t) + 1;
                return $t;
            }
        )
    );
}
///<summary>represent basic options for reading source code </summary>
/**
 * represent basic options for reading source code 
 */
function igk_str_read_createoptions()
{
    return (object)array(
        "Depth" => 0,
        "startLine" => 0,
        "endLine" => -1,
        "removeComment" => 1,
        "ignoreEmptyLine" => 1,
        "FormatText" => 1,
        "@context" => "",
        "ignoreProcessor" => 1,
        "noHandleDefinitions" => 0,
        "noAutoParameter" => 0,
        "indentChar" => "\t",
        "endOffset" => 0,
        "stop" => 0,
        "data" => "",
        "isDebug" => 0,
        "mode" => 0,
        "treatClassStatement" => 0,
        "offsetBracket" => 0,
        "noPhpPreprocessor" => 0,
        "definitions" => null,
        "documentation" => null,
        "parameters" => null,
        "toread" => 0,
        "callback" => array()
    );
}
///<summary></summary>
///<param name="options"></param>
///<param name="mode"></param>
/**
 * 
 * @param mixed $options 
 * @param mixed $mode 
 */
function igk_str_read_get_intent($options, $mode = 0)
{
    if ($mode == 3)
        return "";
    return str_repeat($options->indentChar, igk_getv($options, 'offsetBracket', 0));
}
///<summary>read value in branket</summary>
///<param name="s">search string</param>
///<param name="start">start branket in expression</param>
///<param name="end">end branket in expression</param>
/**
 * read value in branket
 * @param mixed $s search string
 * @param mixed $start start branket in expression
 * @param mixed $end end branket in expression
 */
function igk_str_read_in_brancket($s, $start, $end, &$tc_c = null)
{
    if (empty($s))
        return null;
    $g = array();
    $xs = 0;
    $expr = array($s);
    while (count($expr) > 0) {
        $q = array_shift($expr);
        $i = strpos($q, $start);
        if (($i !== false) && ($i != -1)) {
            $r = IGKString::IndexOf($q, $start, $i + 1);
            $rg = IGKString::IndexOf($q, $end, $i + 1);
            if ($r == -1) {
                $ln = $rg - $i - 1;
                if ($ln > 0)
                    $g[] = substr($q, $i + 1, $ln);
            } else {
                $depth = 1;
                $kindex = $r;
                $mark = false;
                $offsets = array();
                $bsegment = 0;
                for ($xs = $i + 1; $xs < strlen($s); $xs++) {
                    if ($q[$xs] == $start) {
                        $depth++;
                        $offsets[] = (object)array("X" => $xs, "Y" => 0);
                        $bsegment = count($offsets);
                        continue;
                    }
                    if ($s[$xs] == $end) {
                        $depth--;
                        if ($depth == 0) {
                            $kindex = $xs;
                            $mark = true;
                            break;
                        }
                        $bsegment--;
                        $tg = $offsets[$bsegment];
                        $tg->Y = $xs;
                        $offsets[$bsegment] = $tg;
                    }
                }
                if ($mark) {
                    $g[] = substr($s, $i + 1, $kindex - $i - 1);
                    foreach ($offsets as $item) {
                        $expr[] = substr($s, $item->X, $item->Y - $item->X + 1);
                    }
                }
            }
        }
    }
    if ($tc_c !== null)
        $tc_c += $xs;
    return $g;
}
///<summary>read source code algorithm . entry</summary>
///<param name="lines" >array of string line entries</param>
///<param name="callback" >callback to call at end</param>
///<param name="options" >option paramater to pass</param>
///<param name="toclean" >clean up object</param>
/**
 * read source code algorithm . entry
 * @param mixed $lines array of string line entries
 * @param mixed $callback callback to call at end
 * @param mixed $options option paramater to pass
 * @param mixed $toclean clean up object
 */
function igk_str_read_source_code($lines, $callback, &$options, $toclean = null)
{
    $desc = "";
    $m = "";
    $options = $options ?? igk_str_read_createoptions();
    $count = igk_count($lines);
    if ($options->endLine == -1) {
        $options->endLine = $count - 1;
    }
    $mode = 0;
    $toclean = $toclean ?? igk_str_read_create_cleanup_source();
    $eline = $options->endLine;
    $sline = $options->startLine;
    foreach (["stop" => 0, "offset" => 0, "isDebug" => 0] as $k => $v) {
        if (!isset($options->$k))
            $options->$k = $v;
    }
    $hw = 0;
    $tq = array();
    $options->output = &$desc;
    $allMode = '*';
    $fc_treatLine = igk_getv($options->callback, "treatLine") ?? function ($t) {
        return $t;
    };
    $fc_inmode = function ($options, $mode) {
        return ($mode == 3) || (igk_getv($options, '@context') == 'html');
    };
    while (!$options->stop && ($sline <= $eline) && ($sline < $count)) {
        if ($hw) {
            $uself = !igk_getv($options, 'datalf');
            if ($uself)
                $desc .= IGK_LF;
            $hw = 0;
        }
        $t = rtrim($lines[$sline]);
        if ($mode != 3) {
            $t = ltrim($t);
        }
        $sline++;
        if ($options->ignoreEmptyLine && empty($t)) {
            continue;
        }
        $options->lineNumber = $sline - 1;
        $options->lineText = $t;
        $def = 0;
        $tq[] = $t;
        $nextoffset = null;
        $baseq = "";
        $baseoffset = 0;
        while (($q = array_pop($tq)) && !empty(trim($q))) {
            $lpos = null;
            if ($baseq == $q) {
                if ($options->offset == $baseoffset) {
                    igk_wln_e("[blf] -  offset failed: " . $sline . ":" . $q);
                }
            }
            $baseq = $q;
            $baseoffset = $options->offset;
            $nextoffset = $options->offset;
            foreach ($toclean as $mk) {
                $fcmode = $mk->mode;
                $modecond = (is_string($fcmode) && ($fcmode === $allMode)) || (is_callable($fcmode) && $fcmode($mode, $options)) || (is_array($fcmode) && in_array($mode, $fcmode)) || ($fcmode == $mode);
                if ($modecond && preg_match($mk->pattern, $t, $tab, PREG_OFFSET_CAPTURE, $options->offset)) {
                    if (!$lpos) {
                        $lpos = array($mk, $tab[0][1], $tab, $mk->pattern);
                        continue;
                    }
                    if ($tab[0][1] < $lpos[1]) {
                        $lpos[0] = $mk;
                        $lpos[1] = $tab[0][1];
                        $lpos[2] = $tab;
                    }
                }
            }
            if ($lpos) {
                $fc = "callback";
                $g = $lpos[0]->$fc;
                $t = $g($t, $lpos[1], $options->offset, $mode, $options, $lpos[2], $lpos[0]);
                if ($options->offset < strlen($t))
                    array_push($tq, $t);
            }
        }
        $options->offset = 0;
        $treat_i = ($mode != 0) || ($options->offsetBracket > 0);
        if ($treat_i || ($options->ignoreEmptyLine && empty(trim($t)))) {
            if ($treat_i && ((!$options->ignoreEmptyLine) || (($options->ignoreEmptyLine) && !empty($t)))) {
                $uself = !igk_getv($options, 'datalf');
                $lf = "";
                if ($uself) {
                    $lf = IGK_LF;
                    if (!$fc_inmode($options, $mode)) {
                        $lf .= igk_str_read_get_intent($options, $mode);
                        $t = ltrim($t);
                    }
                }
                if (!isset($options->data) || empty($options->data)) {
                    $options->data = $t;
                } else {
                    $options->data = rtrim($options->data) . $lf . $t;
                }
            }
            continue;
        }
        $desc .= $fc_treatLine($t);
        if (empty($t))
            $hw = 0;
        else
            $hw = 1;
    }
    return $callback($desc);
}
///<summary>read source code in bracket</summary>
/**
 * read source code in bracket
 */
function igk_str_read_source_code_bracket($lines, &$options = null)
{
    $tab = igk_str_read_create_cleanup_source();
    $options = $options ?? igk_str_read_createoptions();
    if (!isset($options->bracketDepth)) {
        $options->bracketDepth = -1;
        igk_wln(igk_show_trace());
        igk_wln_e(__LINE__ . ":not bracketDepth define");
    }
    $options->stop = 0;
    if (!isset($options->endLine) || ($options->endLine == -1))
        $options->endLine = count($lines);
    if (!isset($options->outOffset)) {
        $options->outOffset = 0;
    }
    array_unshift($tab, (object)array(
        "name" => "uncollapsestring",
        "mode" => 0,
        "pattern" => "/(\"|')/",
        "callback" => function ($t, $start, &$offset, &$mode, $options) {
            $lis = $start;
            $s = igk_str_read_brank($t, $lis, $t[$start], $t[$start], null, 1);
            $offset = $lis + 1;
            $options->outOffset += ($lis - $start);
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "endbracket",
        "mode" => 0,
        "pattern" => "/\}/i",
        "callback" => function ($t, $start, &$offset, &$mode, $options) {
            if ($options->bracketDepth <= 0) {
                $t = substr($t, 0, $start + 1);
                $offset = $start + strlen($t);
                $options->outOffset += ($offset - $start);
                $options->stop = 1;
                return $t;
            }
            $options->bracketDepth--;
            $offset = $start + 1;
            return $t;
        }
    ));
    array_unshift($tab, (object)array(
        "name" => "startBracket",
        "mode" => 0,
        "pattern" => "/\{/i",
        "callback" => function ($t, $start, &$offset, &$mode, $options) {
            $offset = $start + 1;
            $options->outOffset += 1;
            if (!isset($options->bracketDepth) || ($options->bracketDepth == -1)) {
                $options->bracketDepth = 0;
                $t = substr($t, $start);
                $offset = $offset - $start;
            } else
                $options->bracketDepth++;
            return $t;
        }
    ));
    return igk_str_read_source_code(
        $lines,
        function ($text) use ($options) {
            return $text;
        },
        $options,
        $tab
    );
}
///<summary>remove all line empty line</summary>
/**
 * remove all line empty line
 */
function igk_str_remove_empty_line($str)
{
    $t = preg_split("/(\r\n)|(\n)/i", $str);
    $o = IGK_STR_EMPTY;
    $i = 0;
    foreach ($t as $v) {
        if (($v == null) || empty($v) || (strlen(trim($v)) == 0))
            continue;
        if ($i == 1)
            $o .= IGK_CLF;
        $o .= $v;
        $i = 1;
    }
    return $o;
}
///<summary>remove line empty file </summary>
/**
 * remove empty line line
 * @param string $str string
 * @return string 
 */
function igk_str_remove_lines(string $str)
{
    return StringUtility::SanitizeLine($str);
}
///<summary>remove surrounding quote</summary>
///<param name="v"></param>
/**
 * remove surrounding quote
 * @param mixed $v 
 */
function igk_str_remove_quote($v)
{
    if ((strlen($v = trim($v)) > 0)) {
        if ($v[0] == "'") {
            $v = trim($v, "'");
        } else if ($v[0] == '"') {
            $v = trim($v, '"');
        }
    }
    return $v;
}
///<summary></summary>
///<param name="p"></param>
///<param name="$c"></param>
/**
 * 
 * @param mixed $p 
 * @param mixed $$c 
 */
function igk_str_repeat($p, $c)
{
    $o = "";
    while ($c > 0) {
        $o .= $p;
        $c--;
    }
    return $o;
}
///<summary>remove all parttern</summary>
/**
 * remove all parttern
 */
function igk_str_rm_last(string $str, string $pattern)
{
    $c = strlen($pattern);
    if ($c == 1) {
        return rtrim($str, $pattern);
    }
    while (IGKString::EndWith($str, $pattern)) {
        $str = substr($str, 0, strlen($str) - $c);
    }
    return $str;
}
///<summary></summary>
///<param name="str"></param>
///<param name="pattern"></param>
/**
 * 
 * @param mixed $str 
 * @param mixed $pattern 
 */
function igk_str_rm_start(string $str, string $pattern)
{
    if ($pattern != null) {
        $c = strlen($pattern);
        while (($c > 0) && (strpos($str, $pattern) === 0)) {
            $str = substr($str, $c);
        }
    }
    return $str;
}
///<summary>convert to snake version</summary>
/**
 * convert to snake version
 */
function igk_str_snake(string $str)
{
    $str = preg_replace("/[^a-z0-9]/i", "", $str);
    $str = str_replace("_", "", $str);
    $out = $str;
    if (count($g = preg_split("/[A-Z]/", $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE)) > 0) {
        $out = "";
        $offset = 0;
        for ($i = 0; $i < count($g); $i++) {
            if ($i > 0)
                $out .= '_';
            $out .= implode("_", str_split(substr($str, $offset, $g[$i][1] - $offset))) . strtolower($g[$i][0]);
            $offset = $g[$i][1] + strlen($g[$i][0]);
        }
        if ($offset < strlen($str)) {
            $out .= "_" . implode("_", str_split(substr($str, $offset)));
        }
    } else {
        $out = implode("_", str_split($str));
    }
    return strtolower($out);
}
///<summary></summary>
///<param name="str"></param>
/**
 * 
 * @param mixed $str 
 */
function igk_str_split_lines($str)
{
    return preg_split("/(\r\n)|(\n)/i", $str);
}
///<summary>split string data </summary>
/**
 * split string data 
 */
function igk_str_split_string($splitchr, $str)
{
    // igk_debug_wln("splitting", $str);
    $ln = strlen($str);
    $pos = 0;
    $tab = array();
    $v = "";
    $treat = function ($v) {
        $cv = trim($v);
        if (!empty($cv) && (($ln = strlen($cv)) > 1) && ($cv[0] == $cv[$ln - 1]) && (strpos('"\'', $cv[0]) !== false)) {
            return substr($cv, 1, $ln - 2);
        }
        return $cv;
    };
    while ($pos < $ln) {
        $ch = $str[$pos];
        switch ($ch) {
            case "\"":
            case "'":
                $v .= igk_str_read_brank($str, $pos, $ch, $ch);
                $pos++;
                break;
            case $splitchr:
                $tab[] = $treat($v);
                $v = "";
                break;
            default:
                $v .= $ch;
                break;
        }
        $pos++;
    }
    if (!empty($v)) {
        $tab[] = $treat($v);
    }
    return $tab;
}
///<summary>Represente igk_str_startwith function</summary>
///<param name="str"></param>
///<param name="pattern"></param>
/**
 * Represente igk_str_startwith function
 * @param mixed $str 
 * @param mixed $pattern 
 */
function igk_str_startwith($str, $pattern)
{
    return IGKString::StartWith($str, $pattern);
}
///<summary></summary>
///<param name="content"></param>
///<param name="length" default="150"></param>
/**
 * 
 * @param mixed $content 
 * @param mixed $length 
 */
function igk_str_summary($content, $length = 150)
{
    if (strlen($content) > $length) {
        $content = substr($content, 0, $length) . "...";
    }
    return $content;
}
///<summary></summary>
///<param name="v"></param>
/**
 * 
 * @param mixed $v 
 */
function igk_str_toupperentities($v)
{
    return preg_replace_callback(
        "/&([a-z]+);/i",
        function ($m) {
            $g = substr($m[0], 1, -1);
            switch ($g) {
                case "OELIG":
                    return "OE";
                default:
                    return $g[0];
            }
        },
        strtoupper(htmlentities($v, 0, 'UTF-8'))
    );
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_str_toupperinvariant($n, $map = null)
{
    $interval = function ($min, $max, &$map, $callback) {
        for ($i = $min; $i <= $max; $i++) {
            $callback($i, $map);
        }
    };
    if ($map == null) {
        $map = [];
        $interval(128, 131, $map, function ($x, &$map) {
            $map[$x] = "A";
        });
        $interval(135, 136, $map, function ($x, &$map) {
            $map[$x] = "C";
        });
        $interval(137, 140, $map, function ($x, &$map) {
            $map[$x] = "E";
        });
        $interval(146, 150, $map, function ($x, &$map) {
            $map[$x] = "O";
        });
        $interval(172, 175, $map, function ($x, &$map) {
            $map[$x] = "I";
        });
        $interval(182, 185, $map, function ($x, &$map) {
            $map[$x] = "U";
        });
        $map[160] = "A";
        $map[180] = "O";
    }
    $n = strtoupper($n);
    $ln = strlen($n);
    $hs = "";
    for ($i = 0; $i < $ln; $i++) {
        if (ord($n[$i]) == 195) {
            $i++;
            if (isset($map[$j = ord($n[$i])])) {
                $hs .= $map[$j];
            } else {
                $hs .= "<$j>";
            }
        } else {
            $hs .= $n[$i];
        }
    }
    return $hs;
}
///<summary>get uncollapse string</summary>
///<param name="v"></param>
/**
 * get uncollapse string
 * @param mixed $v 
 */
function igk_str_uncollapsestring($v)
{
    if (is_string($v)) {
        if (preg_match("/^(\"|')/", $v)) {
            $p = 1;
            $v = substr(igk_str_read_brank($v, $p, $v[0], $v[0], null, 1, 0), 0, -1);
        }
    }
    return $v;
}
///<summary>transform [n] to conform balafon uri specification </summary>
/**
 * transform [n] to conform balafon uri specification 
 */
function igk_str_view_uri($n)
{
    return str_replace("_", "/", $n);
}
///<summary></summary>
/**
 * 
 */
function igk_support_noextension_script()
{
    $o = <<<EOF
AddHandler server-parsed .php
SetHandler Application/x-httpd-php
AddHandler Application/x-httpd-php .php
EOF;

    $o .= <<<EOF
<FilesMatch "[^\.]+|(\.ph(p3?|tml)$)">
	SetHandler Application/x-httpd-php
</FilesMatch>
EOF;

    return $o;
}
///<summary></summary>
///<param name="n"></param>
///<param name="m"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $m 
 */
function igk_svg_bind_callable_list($n, $m)
{
    $list = $m->getTempFlag("svg-list");
    if ($list) {
        $o = '<igk:svg-list class="igk-svg-lst">';
        foreach ($list as $k => $v) {
            if (!file_exists($v))
                continue;
            $o .= "<" . $k . ">";
            $o .= igk_svg_content(igk_io_read_allfile($v));
            $o .= "</" . $k . ">";
        }
        $o .= "</igk:svg-list>";
        $n->Content = $o;
    }
    return 1;
}
///<summary>Represente igk_svg_bind_name function</summary>
///<param name="name"></param>
///<param name="context" default="null"></param>
/**
 * Represente igk_svg_bind_name function
 * @param mixed $name 
 * @param mixed $context 
 */
function igk_svg_bind_name($name, $context = null)
{
    $_key = "sys://svg/lists";
    $c = igk_environment()->{IGK_SVG_REGNODE_KEY};
    $obj = $c->getParam($_key, function () {
        return array();
    });
    $obj[$name] = $context;
    $c->setParam($_key, $obj);
}
///<summary>bind all svg document from the folder. All of them will be send to client. use igk_svg_register_icons if only send required is mandatory.</summary>
/**
 * bind all svg document from the folder. All of them will be send to client. use igk_svg_register_icons if only send required is mandatory.
 */
function igk_svg_bind_svgs($doc, $dir = IGK_LIB_DIR . "/Data/R/svg/icons")
{
    igk_trace();
    igk_exit();
    //  $files = $doc->getTempFlag(__FUNCTION__, array());
    $g = $doc->body->addNodeCallback("svg-bind-all", function ($t) {
        $n = $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT);
        return $n;
    });
    if (!$g->targetNode) {
        $g->targetNode = igk_html_node_notagnode();
    }
    $t = $g->targetNode->clearChilds();
    $t->addOnRenderCallback(igk_create_node_callback('igk_svg_bind_callable_list', array("n" => $doc)));
    if (!($list = $doc->getTempFlag("svg-list")))
        $list = array();
    foreach (IO::GetFiles($dir, "/\.svg$/i") as $v) {
        $list[strtolower(igk_io_basenamewithoutext($v))] = $v;
    }
    $doc->setTempFlag("svg-list", $list);
}
///<summary></summary>
///<param name="name"></param>
///<param name="file"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $file 
 */
function igk_svg_bindfile($name, $file)
{
    $_vkey = "sys://svg/file";
    $source = igk_environment()->{IGK_SVG_REGNODE_KEY};
    if ($source && file_exists($file)) {
        $f = $source->getParam($_vkey) ?? [];
        $f[$name] = $file;
        $source->setParam($_vkey, $f);
    }
}
///<summary>render svg list</summary>
///<param name="n">cibling node</param>
///<param name="mn">host cibling - generally is document</param>
/**
 * render svg list
 * @param mixed $n cibling node
 * @param mixed $mn host cibling - generally is document
 * @deprecated 
 */
function igk_svg_callable_list($n, $m)
{
    igk_dev_wln_e(
        __FILE__ . ":" . __LINE__,
        "loading ......." . __FUNCTION__ . " deprecated"
    );
    // $c = $m->getParam("sys://svg/lists");
    // $g = $m->getParam("sys://svg/file");
    // if ((!$c || igk_count($c) == 0) || !$g) {
    //     return 0;
    // }
    // $o = "";
    // if ($g) {
    //     foreach (array_keys($c) as $k) {
    //         $v = igk_getv($g, $k);
    //         if (($v === null) || empty($v)) {
    //             continue;
    //         }
    //         $f = igk_io_expand_path($v);
    //         if (!file_exists($f)) {
    //             $f = igk_realpath(igk_io_basedir() . "/{$v}");
    //         }
    //         if (empty($f)) {
    //             if (!igk_sys_env_production()) {
    //                 igk_wln_e("svg : {$k} not found, from {$f} in {$v}");
    //             }
    //             continue;
    //         }
    //         $o .= "<" . $k . ">";
    //         $o .= igk_svg_content(igk_io_read_allfile($f));
    //         $o .= "</" . $k . ">";
    //     }
    //     $n->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->targetNode->text($o);
    // }
    // $m->setParam("sys://svg/lists", null);
    return 1;
}
///<summary>trim all unnecessary content</summary>
///<param name="s"> content to treat</param>
///<param name="s"> content to treat</param>
/**
 * trim all unnecessary content
 * @param string $s content to treat
 * @return string output
 */
function igk_svg_content($s)
{
    $s = preg_replace("#\<\?(.)+\?\>\s*#i", "", $s);
    $s = preg_replace("#\<!--(.)+-->#i", "", $s);
    $s = preg_replace("#\<!DOCTYPE([^\>]+)\>\s*#i", "", $s);
    // explode empty tag 
    $s = preg_replace_callback(
        "#\<(?P<name>" . IGK_XML_IDENTIFIER_RX . ") (?P<attrib>([a-z0-9_\-\.\,\=\"\' :]*))/>#i",
        function ($t) {
            return "<" . $t["name"] . " " . $t["attrib"] . "></" . $t["name"] . ">";
        },
        $s
    );
    return $s;
}
///<summary></summary>
/**
 * 
 */
function igk_svg_get_regicons()
{
    $source = igk_environment()->{IGK_SVG_REGNODE_KEY};
    if ($source)
        return $source->getParam("file");
    return null;
}
///<summary>register svg item</summary>
/**
 * register svg item
 */
function igk_svg_register($doc, $name, $file)
{
    $v_svg_reg_key = "sys://node/svg_regnode";
    $b = $doc ? $doc->getBody() : null;
    $_newfc = function () {
        $n = new SvgListNode();
        // $n->addOnRenderCallback(igk_create_node_callback('igk_svg_callable_list', (object)array("node" => $n)));
        // if (igk_environment()->isDev()) {
        //     $n->comment()->Content = "SVG LIST:";
        // }
        return $n;
    };
    if (!$b) {
        if (igk_is_atomic()) {
            $b = igk_get_env($v_svg_reg_key);
            if ($b == null) {
                $b = $_newfc();
                igk_set_env($v_svg_reg_key, $b);
            }
            igk_environment()->{IGK_SVG_REGNODE_KEY} = $b;
            igk_svg_bindfile($name, $file);
            return $b;
        }
        return null;
    }
    $n = $doc->getBody()->addNodeCallback("svgregister", function () use ($_newfc) {
        $n = igk_environment()->{IGK_SVG_REGNODE_KEY}
            ?? $_newfc() ?? igk_die("svg node not created");
        igk_environment()->{IGK_SVG_REGNODE_KEY} = $n;
        return igk_html_node_clonenode($n);
    });
    igk_svg_bindfile($name, $file);
    return $n;
}
///<summary>register svg list document in directory. only required svg from the folder will be send to client</summary>
/**
 * register svg list document in directory. only required svg from the folder will be send to client
 */
function igk_svg_register_icons($doc, $name = null, $dir = IGK_LIB_DIR . "/Data/R/svg/icons")
{
    if (!empty($name)) {
        if (file_exists($f = $dir . "/" . $name . ".svg")) {
            igk_svg_register($doc, $name, $f);
            return;
        }
    }
    $r = [];
    IO::GetFiles($dir, "/\.svg$/i", true, $r, function ($file) use ($doc) {
        igk_svg_register($doc, igk_io_basenamewithoutext($file), $file);
    });
}
/**
 * use svg image
 */
function igk_svg_use($name, $context = null)
{
    return \IGK\System\Html\SVG\SvgRenderer::RegisterIcon($name, $context);
}
///<summary>create uri system pattern info</summary>
/**
 * create uri system pattern info
 * @param string $controller can be : 
 * @param string $uri can be : 
 * @param string $pattern can be : 
 */
function igk_sys_ac_create_pattern($ctrl, $uri, $methodpattern = IGK_REG_ACTION_METH)
{
    $k = $methodpattern;
    $pattern = igk_sys_ac_getpattern($k);
    $keys = igk_str_get_pattern_keys($k);
    $page = $uri;
    $e = new IGKSystemUriActionPatternInfo(array(
        "action" => $k,
        "value" => $uri,
        "pattern" => $pattern,
        "uri" => $page,
        "keys" => $keys,
        "ctrl" => $ctrl,
        "requestparams" => null
    ));
    return $e;
}
///<summary></summary>
///<param name="basePatternUri"></param>
/**
 * 
 * @param mixed $basePatternUri 
 */
function igk_sys_ac_getpattern($basePatternUri)
{
    return igk_pattern_matcher_get_pattern($basePatternUri);
}
///<summary>get uri action pattern info</summary>
/**
 * helper: get current uri pattern info
 */
function igk_sys_ac_getpatterninfo()
{
    return igk_getctrl(IGK_SYSACTION_CTRL)->getPatternInfo();
}
///<summary>use to register action</summary>
/**
 * helper: use to register action
 */
function igk_sys_ac_register($uriPattern, $uri)
{
    igk_getctrl(IGK_SYSACTION_CTRL)->sys_ac_register($uriPattern, $uri);
}
///<summary>use to unregister uri action</summary>
/**
 * use to unregister uri action
 */
function igk_sys_ac_unregister($uriPattern)
{
    igk_getctrl(IGK_SYSACTION_CTRL)->sys_ac_unregister($uriPattern);
}
///<summary>if muri is array ignore file</summary>
///<param name="muri">uri location</param>
///<param name="file" default="null">physical file path</param>
/**
 * if muri is array ignore file
 * @param mixed $muri uri location
 * @param mixed $file physical file path
 */
function igk_sys_add_cache_uri($muri, $file = null)
{
    $f = IGK_APP_DIR . "/Caches/uri.cache";
    $t = array();
    if (file_exists($f)) {
        include($f);
    }
    $d = igk_date_now();
    if (is_array($muri)) {
        foreach ($muri as $k => $v) {
            $t[$k] = $v . "|" . $d;
        }
    } else {
        $t[$muri] = $file . "|" . $d;
    }
    igk_sys_store_uri_cache($t);
}
///<summary>get the system author. alias function of constant IGK_AUTHOR</summary>
/**
 * get the system author. alias function of constant IGK_AUTHOR
 */
function igk_sys_author()
{
    return IGK_AUTHOR;
}
///<summary>system shortcut to current user authorisation</summary>
/**
 * system shortcut to current user authorisation
 */
function igk_sys_authorize($authname, $authCtrl = null)
{
    if (igk_is_conf_connected())
        return true;
    $u = igk_app()->session->User;
    if ($u) {
        if ($authCtrl) {
            $authname = $authCtrl::name($authname);
        }
        return $u->auth($authname);
    }
    return false;
}
///<summary>Represente igk_sys_balafon_js function</summary>
///<param name="doc"></param>
///<param name="debug" default="false"></param>
/**
 * Represente igk_sys_balafon_js function
 * @param mixed $controller 
 * @param mixed $debug 
 * @param mixed $minify minify on generation 
 */
function igk_sys_balafon_js(?BaseController $controller = null, $debug = false, bool $minify = true, $usecache = true)
{
    $r = igk_io_cacheddist_jsdir();
    $f = null;
    $encoding = igk_server()->HTTP_ACCEPT_ENCODING ?? '';
    $accept = array_map("trim", explode(",", $encoding));
    if (0 &&  $usecache  && file_exists($f = $r . "/balafon.js")) {
        $ctx = file_get_contents($f);
        if (!empty($ctx) && in_array("deflate", $accept)) {
            $src = @gzinflate($ctx);
            if ($src !== false)
                return $src;
        }
        $src = $ctx;
        if ($decode = (in_array("gzip", $accept) && !empty($src) ? gzdecode($src) : false)) {
            return $decode;
        }
        return $src;
    }
    $src = HtmlCoreJSScriptsNode::GetCoreScriptInlineContent(null);

    $const = "constant";
    $header = "//author: C.A.D. BONDJE DOUE" . IGK_LF;
    $header .= "//libname: balafon.js" . IGK_LF;
    $header .= "//version: {$const('IGK_BALAFON_JS_VERSION')}" . IGK_LF;
    $header .= "//copyright: igkdev @ 2013 - " . date('Y') . IGK_LF;
    $header .= "//license: //igkdev.com/balafon/balafonjs/license" . IGK_LF;
    $header .= "//generate: " . date("Ymd H:i:s") . IGK_LF;
    $header .= "\"use strict\";";
    // $s = empty($c->data) ? "/* core script failed to load cache not provide. */" : $c->data;
    $s = $src;
    $s = str_replace("\"use strict\";", "", $s);
    if (ob_get_level() > 0)
        ob_clean();
    ob_start();
    $src = "";
    if ($debug) {
        $src = $header . $s;
    } else {
        $src = $header . igk_js_minify($s);
    }
    ob_get_clean();
    ob_start();
    igk_zip_output($src, 0, 0, $type);
    $out = ob_get_clean();
    $f && igk_io_w2file($f, $out);
    return $src;
}
///<summary>build distribution </summary>
/**
 * build distribution 
 */
function igk_sys_build_dist($ctrl)
{
    $dir = $ctrl->getDeclaredDir();
    $distfolder = $dir . "/" . IGK_DIST_FOLDER;
    if (is_dir($distfolder)) {
        IO::RmDir($distfolder, true);
    }
    IO::CreateDir($distfolder);
    IO::CreateDir($distfolder . "/js");
    IO::CreateDir($distfolder . "/css");
    IO::CreateDir($distfolder . "/img");
    IO::CreateDir($distfolder . "/data");
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="id"></param>
///<param name="uri"></param>
///<param name="callback"></param>
///<param name="message"></param>
///<param name="hiddenEntries" default="null"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $id 
 * @param mixed $uri 
 * @param mixed $callback 
 * @param mixed $message 
 * @param mixed $hiddenEntries 
 */
function igk_sys_buildconfirm_ajx($ctrl, $id, $uri, $callback, $message, $hiddenEntries = null)
{
    if (igk_qr_confirm()) {
        $ctrl->call($callback);
    } else {
        $frame = igk_frame_add_confirm($ctrl, $id, $uri);
        $frame->Form->Div->Content = $message;
        if ($hiddenEntries) {
            foreach ($hiddenEntries as $k => $v) {
                $frame->Form->addInput($k, "hidden", $v);
            }
        }
        igk_wl($frame->render());
    }
}
///<summary>cache library file</summary>
/**
 * cache library file
 */
function igk_sys_cache_lib_files()
{
    igk_clear_cache();
    IGKSysCache::$LibFiles = array();
    $dirname = dirname(igk_io_projectdir());
    igk_io_basedir();
    $t_files = igk_load_env_files($dirname);
    igk_reglib($t_files);
    IGKSysCache::CacheLibFiles(true);
}
///<summary>get the cache request file</summary>
/**
 * get the cache request file
 */
function igk_sys_cache_request()
{
    $g = "";
    $f = IGK_APP_DIR . "/Caches/uri.cache";
    $dom = igk_getv($_SERVER, 'HTTP_HOST');
    if (!IGKValidator::IsIPAddress($dom) && preg_match('/^(([^\.])+\.)+(([^\.])+\.)([^\.]+)$/i', $dom)) {
        return null;
    } else {
        $g = igk_io_current_request_uri();
    }
    if ($g == "/")
        return $g . "index.php";
    if (strstr($g, "/?")) {
        $g = "/index.php" . substr($g, 1);
    }
    $g = igk_str_rm_last($g, "/");
    if (file_exists($f)) {
        $t = array();
        include($f);
        if (isset($t[$g])) {
            list($uri, $time)
                = explode("|", $t[$g]);
            return $uri;
        }
    }
    return $g;
}
///<summary>get if request require cache</summary>
/**
 * get if request require cache
 */
function igk_sys_cache_require()
{
    return defined("IGK_CACHE_REQUIRE") || igk_getv(igk_get_allheaders(), "IGK_CACHE_REQUIRE", 0);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_cache_uri()
{
    return igk_getv(igk_get_allheaders(), "IGK_CACHE_URI");
}
///return system cgi folder
/**
 */
function igk_sys_cgi_folder()
{
    return IGK_LIB_CGI_BIN_DIR;
}



///<summary>invoke configuration page settings</summary>
/**
 * invoke configuration page settings
 */
function igk_sys_config_view($file)
{
    if (defined("IGK_PHAR_CONTEXT")) {
        if (!igk_sys_env_production()) {
            igk_wln("appdir: " . igk_const('IGK_APP_DIR'));
            igk_wln((new IGKHtmlRelativeUriValueAttribute(IGK_BALAFON_JS_CORE_FILE))->getValue());
        }
        igk_exit();
    }
    if (!igk_get_env("sys://data/configsettings") && file_exists($f = igk_io_basedatadir("/configure"))) {
        $g = igk_json_parse(igk_io_read_allfile($f));
        igk_set_env("sys://data/configsettings", $g);
        if ($g && igk_getv($g, "noConfig")) {
            igk_navto(igk_io_baseuri());
        }
    }

    if (!igk_environment()->IsWebApp() && ($p = igk_sys_getconfig("configuration_port")) && ($p != $_SERVER["SERVER_PORT"])) {
        if ($p == 443) {
            $s = igk_secure_uri(igk_io_baseDomainUri(), true, false) . "/Configs";
        } else {
            $s = igk_io_baseDomainUri();
            igk_dev_wln_e("port : " . $p,  $_SERVER["SERVER_PORT"]);
        }
        igk_navto($s);
        igk_exit();
    }
    $igk = igk_app();
    if (!$igk) {
        igk_log_write_i("CONFIG", "No Instante found for configuration page");
        igk_exit();
    }
    try {
        if (igk_is_thumbnail_request()) {
            $d = igk_create_node("thumbNailDocument", null, array($file));
            if ($wb = igk_sys_getconfig("website_title")) {
                $wb = "- [ {$wb} ] ";
            }
            $d->Title = __("Configuration Page") . $wb;
            $d->renderAJX();
            igk_exit();
        }
        if ($igk->Session->getParam("igk_wizeinstall") || (igk_server_is_local() && igk_getr("wizeinstall") == 1)) {
            igk_header_no_cache();
            $c = igk_getctrl(IGK_CONF_CTRL);
            $t = array();
            if (isset($_POST["install"])) {
                $t[] = "install";
            }
            igk_ctrl_render_doc($c, "wizeinstall", $t);
            igk_exit();
        }
        $doc = $igk->getDoc();
        $cnf = igk_getconfigwebpagectrl();
        if ($cnf) {
            RequestHandler::getInstance()->handle_ctrl_request_uri();
            $cnf->View();
        }
        if ($doc) {
            $doc->setBaseUri(igk_io_baseuri() . "/Configs/");
            $doc->Favicon = new IGKHtmlRelativeUriValueAttribute(IGK_LIB_DIR . "/Default/R/Img/cfavicon.ico");
            igk_set_session_redirection(null);
            igk_header_no_cache();
            HtmlRenderer::RenderDocument($doc, false, $cnf);
        } else {
            igk_do_response(WebResponse::Create('Configs: Misconfiguration', 500));
            igk_set_header(500, "Configs Misconfiguration.");
            igk_error_page404("Configs Misconfiguration.");
        }
    } catch (Exception $Ex) {
        igk_show_exception($Ex);
    }
    igk_exit();
}
///<summary></summary>
/**
 * 
 */
function igk_sys_create_app_setting()
{
    $d = igk_createobj();
    $d->domainfileNotMaintain = 0;
    return $d;
}
///<summary>create session start info</summary>
/**
 * create session start info
 */
function igk_sys_create_session_start_info()
{
    igk_ilog("create start session info");
    $t = igk_date_now();
    return array(
        "server" => $_SERVER,
        "start-time" => $t,
        "user-addr" => igk_getv(
            $_SERVER,
            "REMOTE_ADDR"
        ),
        "user-agent" => igk_getv(
            $_SERVER,
            "HTTP_USER_AGENT"
        ),

    );
}
///<summary>create a new system user</summary>
/**
 * create a new system user
 */
function igk_sys_create_user($userdata, $usertable = IGK_TB_USERS, $authtable = IGK_TB_AUTHORISATIONS, $grouptable = IGK_TB_GROUPS, $groupauth = IGK_TB_GROUPAUTHS, $usergrouptable = IGK_TB_USERGROUPS)
{
    $b = new IGKUserInfo();
    $b->loadData($userdata);
    $bkey = IGKUserInfo::DB_INFO_KEY;
    $r = (object)array();
    $r->authtable = igk_db_get_table_name($authtable);
    $r->grouptable = igk_db_get_table_name($grouptable);
    $r->groupauthtable = igk_db_get_table_name($groupauth);
    $r->usertable = igk_db_get_table_name($usertable);
    $r->usergrouptable = igk_db_get_table_name($usergrouptable);
    $b->setProperty($bkey, $r);
    return $b;
}
///<summary></summary>
/**
 * 
 */
function igk_sys_ctrl()
{
    return igk_getctrl(IGK_SYS_CTRL);
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_sys_ctrl_type($ctrl)
{
    $s = get_class($ctrl);
    if ($ctrl instanceof ControllerTypeBase) {
        $t = class_parents($s);
        $ht = IGKControllerTypeManager::GetControllerTypes();
        $ht = igk_array_key_value_toggle($ht);
        foreach ($t as $v) {
            if (isset($ht[$v]))
                return $ht[$v];
        }
    }
    return "unknow";
}
///<summary>get current domain name according to configuration</summary>
/**
 * get current domain name according to configuration
 */
function igk_sys_current_domain_name()
{
    if (igk_sys_is_subdomain()) {
        return igk_sys_subdomain_name() . "." . igk_sys_domain_name();
    } else {
        return igk_sys_domain_name();
    }
}
///<summary>Represente igk_sys_current_user function</summary>
/**
 * Represente igk_sys_current_user function
 */
function igk_sys_current_user()
{
    ($u = igk_app()->session->User) || ($u = igk_get_system_user()) || igk_die("failed to get a current user");
    return $u;
}
///<summary>get the current user id</summary>
/**
 * get the current user id
 */
function igk_sys_current_user_id()
{
    $uid = ($u = igk_app()->session->User) || ($u = igk_get_system_user()) ? $u->clId : null;
    return $uid;
}
///<summary></summary>
/**
 * 
 */
function igk_sys_db_constant_cache()
{
    return igk_dir(igk_io_cachedir() . "/db/.db.constants.cache");
}
///<summary></summary>
/**
 * 
 */
function igk_sys_debug_components()
{
    return igk_get_env("sys://debug/components");
}
///<summary>get system debug zone controller</summary>
///<remark>only one at time</remark>
/**
 * get system debug zone controller
 */
function igk_sys_debugzone_ctrl()
{
    return igk_getctrl(igk_getv(igk_app()->getConfigs(), "debugHostCtrl", null), false);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_disable_html_caching()
{
    @unlink(igk_dir(igk_io_applicationdir() . "/Caches/" . IGK_CACHE_HTML));
}
///<summary>check if a controller is a domain controller and return the controller or false</summary>
/**
 * check if a controller is a domain controller and return the controller or false
 */
function igk_sys_domain_control($ctrl)
{
    $h = IGKSubDomainManager::GetSubDomainName();
    if (!empty($h)) {
        return IGKSubDomainManager::IsControl($h, $ctrl);
    }
    return false;
}
///<summary>get configured base domain</summary>
/**
 * get configured base domain
 */
function igk_sys_domain_name()
{
    return IGKSubDomainManager::GetBaseDomain();
}
///<summary></summary>
/**
 * 
 */
function igk_sys_enable_html_caching()
{
    $dir = igk_io_cachedir();
    $d = igk_date_now();
    igk_io_save_file_as_utf8_wbom(
        $dir . "/" . IGK_CACHE_HTML,
        <<<EOF
#active the html caching "{$d}
#delete me to deactivate the html cache
EOF,
        true
    );
}
///<summary>return the current environment</summary>
/**
 * return the current environment
 */
function igk_sys_env()
{
    return igk_server()->ENVIRONMENT;
}
///<summary>force production mode</summary>
/**
 * force production mode
 */
function igk_sys_env_enable_production_mode()
{
    igk_server()->ENVIRONMENT = "production";
    igk_app()->session->setParam("sys://env/production", 1);
}
///<summary></summary>
///<param name="error"></param>
/**
 * 
 * @param mixed $error 
 */
function igk_sys_error($error)
{
    igk_set_header(404);
    $r = new XmlNode("result");
    $r->add("error")->Content = $error;
    $r->add("msg")->Content = __(igk_get_error_key($error));
    $r->RenderXML();
}
///<summary></summary>
/**
 * 
 */
function igk_sys_errorzone_ctrl()
{
    return igk_getctrl(igk_getv(igk_app()->getConfigs(), "errorHostCtrl", null), false);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_force_view()
{
    igk_getctrl(IGK_SESSION_CTRL)->forceview();
}
///<summary>handle global error</summary>
/**
 * handle global error
 */
function igk_sys_g_handle_error($severity, $message, $filename, $lineno)
{
    $__handle_severity = function ($severity, $message, $filename, $lineno) {
        $fcs = igk_get_env("sys://severity_handle");
        if ($fcs) {
            foreach ($fcs as $v) {
                $o = call_user_func_array($v->fc, func_get_args());
                if ($o)
                    return 1;
            }
        }
        return false;
    };
    if ($__handle_severity($severity, $message, $filename, $lineno)) {
        return;
    }
    if (igk_sys_env_production()) {
        $logmsg = "severity: " . $severity . " " . $message . " " . $filename . ":" . $lineno;
        igk_ilog(igk_io_request_uri() . ":\n" . $logmsg, __FUNCTION__);
        return;
    }
    $s = "";
    $s .= igk_server()->REQUEST_URI . IGK_LF;
    $s .= ("severity :{$severity}") . IGK_LF;
    $s .= ("message :{$message}") . IGK_LF;
    $s .= ("filename :{$filename}") . IGK_LF;
    $s .= ("line :{$lineno}") . IGK_LF;
    $s .= ("query_string :" . igk_server()->QUERY_STRING . "") . IGK_LF;
    if ($severity != 2) {
        switch ($severity) {
            case 8:
                if (igk_server()->REQUEST_URI == "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/balafon.css.php")
                    return;
                if (strstr($message, "tempnam()"))
                    return;
                break;
            case 8192:
                return;
        }
        if (strstr($filename, "eval()'d")) {
            $s .= "EvaluatedCode::[{$lineno}]:" . igk_get_env(IGK_LAST_EVAL_KEY) . "";
            igk_get_env(IGK_LAST_EVAL_KEY);
        }
        if ($severity == 4096)
            $s .= ("EvalScript :" . igk_get_env("sys://eval/lastscript")) . IGK_LF;
        if (!igk_sys_env_production()) {
            igk_ilog($s, __FUNCTION__);
        }
    }
}
///<summary>return an array of server session id</summary>
/**
 * return an array of server session id
 */
function igk_sys_get_all_openedsessionid($checksize = true)
{
    $tab = array();
    $d = ini_get("session.save_path");
    if (empty($d)) {
        $d = defined('IGK_SESS_DIR') ? constant('IGK_SESS_DIR') : null;
    }
    if ($d && is_dir($d)) {
        $f = IO::GetFiles($d, "/^(.)+$/i", false);
        if ($f) {
            foreach ($f as $v) {
                if ($checksize && (filesize($v) == "0"))
                    continue;
                $n = igk_str_rm_start(basename($v), IGK_SESSION_FILE_PREFIX);
                $tab[$n] = ["id" => $n, "file" => $v];
            }
        }
    }
    return $tab;
}
///<summary> get prefix</summary>
/**
 *  get prefix
 */
function igk_sys_get_html_ns_prefix($ns)
{
    $nk = IGK_ENV_HTML_NS_PREFIX;
    $cp = igk_get_env($nk);
    if (!$cp) {
        return null;
    }
    return igk_getv($cp->ns, $ns);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_get_mtime_uid()
{
    $m = microtime();
    $l = igk_getv(explode(' ', $m), 1);
    return Number::ToBase((int)($l), 36);
}
///<summary> helper: get projects </summary>
/**
 * helper: get projects 
 */
function igk_sys_get_projects_controllers()
{
    return igk_app()->getControllerManager()->getUserControllers();
}
///<summary></summary>
///<param name="file"></param>
/**
 * 
 * @param mixed $file 
 */
function igk_sys_get_referencedir($file)
{
    return igk_getv(igk_get_env(IGK_ENV_COMPONENT_REFDIRS_KEY), igk_realpath($file));
}
///<summary>shortcut to get subdomain ctrl</summary>
/**
 * shortcut to get subdomain ctrl
 */
function igk_sys_get_subdomain_ctrl($uri)
{
    return IGKSubDomainManager::getInstance()->checkDomain($uri);
}
///<summary>shortcut to get user ctrl</summary>
/**
 * shortcut to get user ctrl
 */
function igk_sys_get_user_ctrl()
{
    return igk_getctrl(IGK_USER_CTRL);
}
///<summary>get all controllers shortcut</summary>
/**
 * get all controllers shortcut
 */
function igk_sys_getall_ctrl()
{
    return igk_app()->getControllerManager()->getControllers();
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_sys_getall_funclist($ctrl)
{
    $funcs = array();
    if (!igk_is_conf_connected())
        return $funcs;
    $f = $ctrl->getDataDir() . "/.funclist.xml";
    $cl = get_class($ctrl);
    if (file_exists($f) && ($s = HtmlReader::LoadFile($f))) {
        $r = igk_getv($s->getElementsByTagName("func-list"), 0);
        if ($r) {
            foreach ($r->Childs as $v) {
                $n = $v["name"];
                if (empty($n) || !method_exists($cl, $n))
                    continue;
                $o = array(IGK_FD_NAME => $n, "clAvailable" => 0);
                if ($v["available"]) {
                    $o["clAvailable"] = 1;
                }
                $funcs[$n] = (object)$o;
            }
        }
    }
    return $funcs;
}
///<summary>used to get only available functions list</summary>
/**
 * used to get only available functions list
 */
function igk_sys_getfunclist($ctrl, $news = false, $funcrequest = null)
{
    if ($ctrl == null || !igk_reflection_class_extends($ctrl, IGK_CTRL_BASE))
        return null;
    $func = array();
    $f = $ctrl->getDataDir() . "/.funclist.xml";
    $cl = get_class($ctrl);
    $rlist = array();
    if (file_exists($f) && ($s = HtmlReader::LoadFile($f))) {
        $r = igk_getv($s->getElementsByTagName("func-list"), 0);
        if ($r) {
            foreach ($r->getChilds() as $v) {
                $n = $v["name"];
                if (empty($n) || !method_exists($cl, $n))
                    continue;
                $rlist[$n] = $v;
                if ($v["available"]) {
                    $func[] = $n;
                }
            }
        }
        if ((($funcrequest != null) && !isset($rlist[$funcrequest])) || $news && igk_is_conf_connected()) {
            igk_sys_load_class_method($cl, $func, $r, $rlist, 1);
            igk_io_save_file_as_utf8($f, $s->render());
        }
    } else {
        $d = new XmlNode("func-list");
        igk_sys_load_class_method($cl, $func, $d, $rlist, 1);
        igk_io_save_file_as_utf8($f, $d->render());
    }
    return $func;
}
///<summary>handle global system single action</summary>
/**
 * handle global system single action
 */
function igk_sys_handle_action($name, $args)
{
    $b = igk_get_env("sys://env/actions");
    if (!$b)
        return false;
    $t = igk_getv($b, $name);
    if ($t) {
        call_user_func_array($t, array_slice(func_get_args(), 1));
        return 1;
    }
    return false;
}
///<summary></summary>
/**
 * 
 */
function igk_sys_handle_cache()
{
}
///<summary>handle base controller request uri</summary>
/**
 * helper: handle base controller request uri
 */
function igk_sys_handle_ctrl_request_uri($u = null, $defaultBehaviour = 1)
{
    return \IGK\System\Http\RequestHandler::getInstance()->handle_ctrl_request_uri($u, $defaultBehaviour);
}
///<summary>handle entry files</summary>
/**
 * handle entry files
 */
function igk_sys_handle_entry_file($dir)
{
    $tab = explode(",", IGK_ENTRY_FILES);
    $e = 0;
    $ext = '';
    $e = 0;
    foreach ($tab as $k) {
        if (file_exists($ext = $dir . "/" . trim($k))) {
            $e = 1;
            break;
        }
    }
    if ($e) {
        return $ext;
    }
    return null;
}
///<summary></summary>
///<param name="msg" default="null"></param>
///<param name="content" default="null"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $content 
 */
function igk_sys_handle_error($msg = null, $content = null)
{
    $c = error_get_last();
    if (igk_count($c) == 0) {
        return;
    }
    switch (igk_getv($c, 'type')) {
        case E_PARSE:
            igk_wln("<div class=\"igk-error\" style=\"" . igk_css_get_style(".igk-error") . "\">Parse Error </div>");
            igk_wl("<code>" . $msg . "</code>");
            igk_exit();
        case E_NOTICE:
            igk_wl("<div class=\"igk-notice\" style=\"" . igk_css_get_style(".igk-notice") . "\">Notice </div>\n");
            igk_wl("<code>" . $msg . "</code>");
            igk_wln($c);
            break;
        default:
            break;
    }
}
///<summary>use in phar  context to handle a request</summary>
/**
 * use in phar context to handle a request
 */
function igk_sys_handle_request($uri)
{
    if (empty($uri))
        return;
    $g = array('index.php', 'index.phtml', 'index.html', 'main.php');
    $f = "";
    $args = explode('?', $uri);
    $furi = $args[0];
    $query = igk_getv($args, 1);
    if (file_exists($f = IGK_APP_DIR . $furi) || (igk_phar_running() && file_exists($f = Phar::running() . $furi))) {
        if (is_dir($f)) {
            if ($uri[strlen($furi) - 1] != '/') {
                igk_navto($furi . "/" . (empty($query) ? '' : '?' . $query));
            }
            $ext = '';
            $e = 0;
            foreach ($g as $k) {
                if (file_exists($ext = $f . "/" . $k)) {
                    $e = 1;
                    break;
                }
            }
            if ($e) {
                $f = $ext;
            }
        }
        if (is_file($f)) {
            if (preg_match("/\.(ph(p|tml))$/", $f)) {
                include_once($f);
            } else {
                igk_header_cache_output();
                igk_header_content_file($f);
                igk_zip_output(file_get_contents($f));
            }
            igk_exit();
        }
    } else {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REDIRECT_URL'] = $uri;
        $_SERVER['REDIRECT_STATUS'] = 200;
        $_SERVER['REDIRECT_REQUEST_METHOD'] = igk_getv($_SERVER, 'REQUEST_METHOD', 'GET');
        $_SERVER['REDIRECT_QUERY_STRING'] = igk_getv($_SERVER, 'QUERY_STRING', 'GET');
        include(IGK_LIB_DIR . '/igk_redirection.php');
    }
    igk_set_header(404);
    igk_wln('Bad request : ' . $uri);
    igk_wln_e('File not found: ' . $f, __FILE__ . ":" . __LINE__);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_handle_request_method()
{
    if (defined("IGK_NO_REST_ACTION"))
        return;
    $m = igk_server()->REQUEST_METHOD;
    if (!preg_match("/(DELETE|PUT|STORE|SAVE)/i", $m))
        return;
    $upd = igk_json_parse(igk_io_get_uploaded_data());
    switch ($m) {
        case "DELETE":
            if (igk_getv($upd, "destroysession") == 1) {
                @session_start();
                $id = session_id();
                igk_session_destroy();
                igk_json(igk_json_encode(["sessiondestroy" => 1, 'sessid' => $id]));
            }
            igk_exit();
            break;
    }
}
///<summary></summary>
///<param name="query"></param>
/**
 * 
 * @param mixed $query 
 */
function igk_sys_handle_res($query)
{
    if (preg_match("/^res\.(?P<lang>[^\.]+)\.(?P<ext>(e?js(on)?|txt|xml|bin|dat))$/i", basename($query), $tab)) {
        $t = substr($tab["lang"], 0, 2);
        $ext = strtolower($tab["ext"]);
        $ext = igk_getv(["ejson" => "json"], $ext, $ext);
        $rwf = igk_getr("rwf");
        $c1 = dirname($rwf) . "/res." . $t . "." . $ext;
        if (!file_exists($c1)) {
            $c1 = igk_io_basedir() . dirname($query) . "/res." . $t . "." . $ext;
        }
        if (file_exists($c1)) {
            igk_clear_header_list();
            igk_header_set_contenttype($ext);
            igk_zip_output(igk_io_read_allfile($c1));
            igk_exit();
        }
        igk_set_header(404, __("Resource not found"));
        igk_exit();
    };
}
///<summary></summary>
/**
 * 
 */
function igk_sys_html_cache_dir()
{
    return igk_dir(igk_io_cachedir() . "/html");
}
///<summary>include file in system configuration</summary>
/**
 * include file in system configuration
 */
function igk_sys_include($name)
{
    return include(igk_io_currentrelativepath($name));
}
///<summary>used to include a file base on the current file directory</summary>
/**
 * used to include a file base on the current file directory
 */
function igk_sys_include_once($f)
{
    $d = igk_trace_function(2);
    $f = dirname($d->file) . "/" . basename($f);
    if (file_exists($f))
        include_once($f);
}
///<summary></summary>
///<param name="uri"></param>
///<param name="u"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $uri 
 * @param mixed $u 
 * @param mixed $callback 
 */
function igk_sys_invoke_reg_uri($uri, $u, $callback)
{
    return IGKRoutes::Invoke($uri, $u, $callback);
}
///<summary>shortcut to invoke uri</summary>
/**
 * shortcut to invoke uri
 */
function igk_sys_invoke_uri($uri = null)
{
    igk_app()->getControllerManager()->InvokeUri($uri);
}
///<summary>get if action registrated</summary>
/**
 * get if action registrated
 */
function igk_sys_is_action($name)
{
    $key = "sys://env/actions";
    return ($t = igk_get_env($key)) && igk_getv($t, $name) != null;
}
///<summary>Represente igk_sys_is_auth function</summary>
///<param name="authname"></param>
///<param name="user" default="null"></param>
/**
 * Represente igk_sys_is_auth function
 * @param mixed $authname 
 * @param mixed $user 
 */
function igk_sys_is_auth($authname, $user = null)
{
    if ($user === null) {
        $user = igk_sys_current_user();
    }
    return $user->auth($authname);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_is_htmlcaching()
{
    return file_exists(igk_io_cachedir() . "/" . IGK_CACHE_HTML);
}
///<summary></summary>
///<param name="page"></param>
/**
 * 
 * @param mixed $page 
 */
function igk_sys_is_page($page)
{
    $tb = igk_sys_pagelist();
    $p = strtolower($page);
    foreach ($tb as $k) {
        if ($k == $p)
            return true;
    }
    return false;
}
///<summary></summary>
///<param name="document"></param>
/**
 * 
 * @param mixed $document 
 */
function igk_sys_is_rootdocument($document)
{
    return $document && (igk_app()->getDoc() === $document);
}
///<summary> get if the current rendering system is on subdomain context.</summary>
/**
 *  get if the current rendering system is on subdomain context.
 */
function igk_sys_is_subdomain()
{
    return IGKSubDomainManager::IsSubDomain();
}
///<summary></summary>
///<param name="key"></param>
///<param name="state" ref="true"></param>
/**
 * 
 * @param mixed $key 
 * @param mixed $state 
 */
function igk_sys_ischanged($key, &$state)
{
    $c = igk_getctrl(IGK_CHANGE_MAN_CTRL, false);
    if ($c)
        return $c->isChanged($key, $state);
    return false;
}
///<summary></summary>
///<param name="key"></param>
/**
 * 
 * @param mixed $key 
 */
function igk_sys_islanguagesupported($key)
{
    die("obselete : " . __FUNCTION__);
}
///<summary></summary>
///<param name="key"></param>
/**
 * 
 * @param mixed $key 
 */
function igk_sys_ispagesupported($key)
{
    $m = igk_getctrl(IGK_MENU_CTRL);
    if ($m) {
        if (!empty($tab = $m->getPageList())) {
            $tab = array_combine($tab, $tab); // igk_array_tokeys($tab);
        }
        return isset($tab[$key]);
    }
    return false;
}
///<summary>get that the current request is redirecting</summary>
/**
 * get that the current request is redirecting
 */
function igk_sys_isredirecting()
{
    return ((basename(igk_server()->SCRIPT_NAME) == "igk_redirection.php") && (igk_server()->REDIRECT_STATUS == 200));
}
///<summary>get if present user have the right to do an "authname" </summary>
/**
 * get if present user have the right to do an "authname" 
 */
function igk_sys_isuser_authorize($u, $authname, $strict = false, $authCtrl = null, $adapter = IGK_MYSQL_DATAADAPTER)
{
    if (!$u) {
        return false;
    }
    if (is_object($u) && method_exists($u, "IsAuthorize")) {
        return $u->IsAuthorize($authname, $strict, $authCtrl, $adapter);
    }
    $v_uinfo = igk_sys_create_user($u);
    return IGKUserInfo::GetIsAuthorize($v_uinfo, $authname, $strict, $authCtrl, $adapter);
}
/**
 * get core exclude directory
 */
function igk_sys_js_exclude_dir(): array
{
    $exclude_dir = igk_sys_js_ignore();
    $exclude_dir["@--ignore_hidden--"] = 1;
    $exclude_dir["node_modules"] = 1;
    return $exclude_dir;
}
///<summary>ignore lib folder</summary>
/**
 * ignore lib folder
 * @param array|string $dir
 */
function igk_sys_lib_ignore($dir)
{

    $key = IGKEnvironment::IGNORE_LIB_DIR;
    $d = igk_get_env($key);
    if (!$d) {
        $d = array();
    }
    if (is_string($dir)) {
        $dir = [$dir];
    }
    foreach ($dir as $k) {
        $d[igk_uri($k)] = 1;
    }
    igk_set_env($key, $d);
}
///<summary>get class method that will be exposed</summary>
/**
 * get class method that will be exposed
 */
function igk_sys_load_class_method($classname, &$func, $listnode, $rlist, $publicOnly = 0)
{
    static $exposable = null;
    if ($exposable) {
        $explosableDisabled = ["login"];
    }
    $iexposable = array();
    if (method_exists($classname, "GetExposableUriMethod")) {
        $iexposable = $classname::GetExposableUriMethod();
    }
    $h = igk_sys_reflect_class($classname);
    $sf = $h->getFileName();
    $meth = array();
    $base = $h->getParentClass();
    foreach (get_class_methods($classname) as $v) {
        $refmethod = new ReflectionMethod($classname, $v);
        $n = $refmethod->getName();
        if (preg_match("/^__/", $n) || ($publicOnly && !$refmethod->isPublic()) || $refmethod->isStatic() || (($p_cl = $base) && method_exists($p_cl->getName(), $v) && !in_array($v, $iexposable))) {
            continue;
        }
        $meth[] = $v;
        switch ($n) {
            default:
                $fname = $refmethod->getFileName();
                if (isset($rlist[$n]))
                    break;
                if ($refmethod->isPublic() && !$refmethod->isStatic() && (($fname == $sf) || IGKString::EndWith($refmethod->getName(), "_contract"))) {
                    $func[] = $refmethod->getName();
                    $listnode->add("function")->setAttribute("name", $refmethod->getName())->setAttribute("available", 1);
                }
                break;
        }
    }
}
///<summary></summary>
///<param name="func"></param>
/**
 * 
 * @param mixed $func 
 */
function igk_sys_meth_info($func)
{
    $v = array();
    $tab = explode("/", $func);
    $v["Name"] = $tab[0];
    $v["Args"] = array_slice($tab, 1);
    return (object)$v;
}
///<summary></summary>
/**
 * helper check if rewrite module is available
 */
function igk_sys_mod_rewrite_available()
{
    return igk_apache_module("mod_rewrite") || isset($_SERVER["IGK_REWRITE_MOD"]);
}
///<summary></summary>
/**
 * 
 */
function igk_sys_pagelist()
{
    return igk_getctrl(IGK_MENU_CTRL)->getPageList();
}
///<summary> get a new powerd node block to add to body</summary>
/**
 *  get a new powerd node block to add to body
 */
function igk_sys_powered_node()
{
    $d = igk_create_node("div");
    $d["class"] = "igk-powered no-selection no-contextmenu google-Roboto";
    $d["igk-no-contextmenu"] = "1";
    $s = igk_app()->getConfigs()->powered_message;
    $d->Content = !empty($s) ? $s : __("Powered by") . " <a href=\"" . IGK_WEB_SITE . "\" >IGKDEV</a> ";
    $d->setCallback("getIsVisible", "igk_sys_powered_view_callback");
    return $d;
}
///<summary></summary>
/**
 * 
 */
function igk_sys_powered_view_callback()
{
    $i = igk_get_env("sys://nopowered");
    return $i ? false : true;
}
///<summary>register global system action</summary>
/**
 * register global system action
 */
function igk_sys_reg_action($name, $callback)
{
    if (empty($name) || !is_callable($callback))
        return 0;
    $key = "sys://env/actions";
    $d = igk_get_env($key, array());
    $d[$name] = $callback;
    igk_set_env($key, $d);
    return 1;
}
///<summary> register autolibrary directory</summary>
///<param dir="lib"> directory where library is installed</summary>
/**
 *  register autolibrary directory
 * @param mixed $  directory where library is installed
 */
function igk_sys_reg_autoloadlib($dir, $ns)
{
    die(__FUNCTION__ . " obselete");
}
///<summary>register component display name</summary>
/**
 * register component display name
 */
function igk_sys_reg_componentname($tab)
{
    $ttab = igk_get_env(IGK_ENV_COMPONENT_DISPLAY_NAMES_KEY, function () {
        return array();
    });
    if (igk_count($ttab) == 0) {
        if (file_exists(IGK_COMPONENT_NAMESFILE)) {
            $f = function (&$htab) {
                include(IGK_COMPONENT_NAMESFILE);
            };
            $f($ttab);
        }
    }
    foreach ($tab as $k => $v) {
        $ttab[strtolower($k)] = $v;
    }
    igk_set_env(IGK_ENV_COMPONENT_DISPLAY_NAMES_KEY, $ttab);
}
///<summary>registrer controller</summary>
/**
 * registrer controller
 */
function igk_sys_reg_controller($n, $classname)
{
    igk_set_env_keys("sys://app/controllers", $n, $classname);
}
///<summary></summary>
///<param name="n"></param>
///<param name="callable"></param>
/**
 * 
 * @param mixed $n 
 * @param mixed $callable 
 */
function igk_sys_reg_debugcomponents($n, $callable)
{
    $g = igk_get_env("sys://debug/components");
    if ($g == null)
        $g = array();
    $g[$n] = $callable;
    igk_get_env("sys://debug/components", $g);
}
///<summary>register a display setting</summary>
///<param name="expression">callback expression</param>
/**
 * register a display setting
 * @param mixed $expression callback expression
 */
function igk_sys_reg_display($keyTab, $expression)
{
    igk_set_env("sys://tabdisplay/" . $keyTab, $expression);
}
///<summary> register a html component</summary>
///<param name="ns">namespace for element to register</param>
///<param name="name">the component's name</param>
///<param name="callback">callable to create the component</param>
/**
 *  register a html component
 * @param mixed $ns namespace for element to register
 * @param mixed $name the component's name
 * @param mixed $callback callable to create the component
 */
function igk_sys_reg_html_component($ns, $name, $callback)
{
    $nk = IGK_ENV_HTML_COMPONENTS;
    $cp = igk_get_env($nk);
    if ($cp == null) {
        $cp = array();
    }
    $h = igk_getv($cp, $ns, array());
    $file = "";
    $h[strtolower($name)] = array("key" => $name, "callback" => $callback, "from" => $file);
    $cp[$ns] = $h;
    igk_set_env($nk, $cp);
}
///<summary>register namespace prefix</summary>
///<summary></summary>
///<param name="src"></param>
///<param name="dir"></param>
/**
 * register namespace prefix
 * @param mixed $src 
 * @param mixed $dir 
 */
function igk_sys_reg_referencedir($src, $dir)
{
    $ttab = igk_get_env(IGK_ENV_COMPONENT_REFDIRS_KEY, function () {
        return array();
    });
    if (file_exists($src))
        $ttab[igk_realpath($src)] = $dir;
    igk_set_env(IGK_ENV_COMPONENT_REFDIRS_KEY, $ttab);
}
///<summary></summary>
///<param name="callback"></param>
///<param name="priority" default="10"></param>
/**
 * 
 * @param mixed $callback 
 * @param mixed $priority 
 */
function igk_sys_reg_severity($callback, $priority = 10)
{
    $fcs = igk_get_env("sys://severity_handle", array());
    $fcs[] = (object)array("fc" => $callback, "priority" => $priority);
    usort($fcs, function ($i, $s) {
        if ($i->priority < $s->priority)
            return 1;
        else if ($i->priority > $s->priority)
            return -1;
        return 0;
    });
    igk_set_env("sys://severity_handle", $fcs);
}
///<summary>register uri callback</summary>
/**
 * register uri callback
 */
function igk_sys_reg_uri($u, $callback, $prehandle = 0)
{
    return IGKRoutes::Register($u, $callback, $prehandle);
}
///<summary></summary>
///<param name="key"></param>
///<param name="state" ref="true"></param>
/**
 * 
 * @param mixed $key 
 * @param mixed $state 
 */
function igk_sys_regchange($key, &$state)
{
    $c = igk_getctrl(IGK_CHANGE_MAN_CTRL, false);
    if ($c)
        return $c->registerChange($key, $state);
    return null;
}
///<summary></summary>
///<param name="u"></param>
///<param name="gooduri" default="null"></param>
///<param name="baduri" default="null"></param>
///<param name="listener" default="null"></param>
/**
 * 
 * @param mixed $u 
 * @param mixed $gooduri 
 * @param mixed $baduri 
 * @param mixed $listener 
 * @deprecated
 */
function igk_sys_register_user($u, $gooduri = null, $baduri = null, $listener = null)
{
    $uc = igk_getctrl(IGK_USER_CTRL);
    $tb = IGK\Models\Users::table();
    $r = igk_db_create_row($tb, $u);
    if (empty($r->clLogin)) {
        igk_set_error(__FUNCTION__, "login is empty");
        return 0;
    }
    if (!igk_user_pwd_required($u->clPwd, $u->clRePwd)) {
        igk_set_error(__FUNCTION__, "passwd not match balafon requirement");
        return 0;
    }
    $uc->clLogin = strtolower($r->clLogin);
    $i = igk_db_insert_if_not_exists($uc, $tb, $r);
    if ($i) {
        $usr = igk_get_user_bylogin($r->clLogin);
        $info = "u=" . $usr->clLogin . "&d=" . date("y-m-d") . "$gooduri=" . $gooduri;
        $info .= "&redirect=" . $gooduri . "&baduri=" . $baduri;
        if ($listener) {
            $listener($usr, igk_io_baseuri() . "/" . $uc->getUri("us_activate&q=" . base64_encode($info)));
            return 1;
        }
    }
    igk_ilog("failed to register");
    igk_set_error(__FUNCTION__, "failed to register user to database");
    igk_notifyctrl()->addErrorr("e.registrationnotpossible");
    return $i;
}
///register a method for view call back
/**
 */
function igk_sys_regview($ctrlname, $ctrl, $callback)
{
    $c = igk_getctrl($ctrlname, false);
    if (($c == null) || ($ctrl == null) && ($c !== $ctrl))
        return;
    $c->regView($ctrl, $callback);
}
///<summary>handle or renderging by default</summary>
/**
 * handle or renderging by default
 */
function igk_sys_render_default_uri($uri = null, $ctrl = null, $ownctrl = 1)
{
    $uri = $uri ?? igk_io_base_request_uri();
    $defctrl = $ctrl ?? igk_get_defaultwebpagectrl();
    if ($defctrl && ($ownctrl || igk_own_view_ctrl($defctrl)) && method_exists($defctrl, "is_handle_uri") && $defctrl->is_handle_uri($uri)) {
        $defctrl->handle_redirection_uri($uri);
        if (igk_environment()->isDev()) {
            igk_ilog("/!\\ default controller did not handle request.");
        }
    } else {
        if (igk_environment()->isDev()) {
            igk_ilog("/!\\ controller not found to handle the request.");
        }
    }
} 
///<summary>require file in syst1m configuration</summary>
/**
 * require file in syst1m configuration
 */
function igk_sys_require($name)
{
    require(igk_io_currentrelativepath($name));
}
///<summary></summary>
///<param name="u"></param>
/**
 * 
 * @param mixed $u 
 */
function igk_sys_root_user($u)
{
    return igk_sys_get_user_ctrl()->getRootUser($u);
}
///<summary>Represente igk_sys_setting function</summary>
///<param name="key"></param>
/**
 * Represente igk_sys_setting function
 * @param mixed $key 
 */
function igk_sys_setting($key)
{
    return igk_app()->getConfigs()->getLangSetting($key);
}
///<summary></summary>
///<param name="code"></param>
///<param name="defctrl" default="null"></param>
///<param name="callback" default="null"></param>
/**
 * 
 * @param mixed $code 
 * @param mixed $defctrl 
 * @param mixed $callback 
 */
function igk_sys_show_error_doc($code, $defctrl = null, $callback = null)
{
    $defctrl = $defctrl ? $defctrl : igk_get_defaultwebpagectrl();
    if ($defctrl) {
        if (file_exists($f = $defctrl::GetErrorView($code))) {
            $defctrl->setCurrentView($f, true, null, array("error" => $code, "uri" => igk_io_request_uri()));
            HtmlRenderer::RenderDocument();
        }
    }
    $doc = igk_get_document("error_" . $code, true);
    $doc->body["class"] = "igk-error-body";
    $codefile = $defctrl->getViewDir() . "/error/{$code}.phtml";
    $doc->Title = __("Error 404");
    $c_args = ["t" => $doc->body->getBodyBox()->add("div"), "fname" => "Error", "ctrl" => $defctrl];
    if (file_exists($codefile)) {
        extract($c_args);
        include($codefile);
    } else switch ($code) {
        case 403:
        case 503:
            igk_set_header(403);
            $doc->Title = "Forbiden - " . igk_getv($_SERVER, "REDIRECT_STATUS", igk_getr("code")) . " ";
            $bbox = $doc->body->getBodyBox()->clearChilds();
            $bbox->setClass("err-" . $code);
            get_class($bbox->addHeaderBar("Forbiden"));
            $bbox->div()->addSectionTitle()->setClass("igk-danger")->Content = "/!\\ Forbiden";
            $bbox->div()->addNode("i")->Content = __("Access denied:") . " " . igk_io_fullpath2fulluri(igk_io_rootdir() . igk_io_request_uri());
            break;
        case 404:
            $doc->Title = __("Error 404");
            break;
        case 5404:
            igk_set_env("sys://error", $code);
            $doc->Title = "/!\\ Domain error - 5404 ";
            $bbox = $doc->body->getBodyBox();
            $bbox->setClass("err-" . $code);
            $bbox->addHeaderBar("SubDomainError", igk_io_currentbasedomainuri());
            $bbox->div()->div()->setStyle("clear:both");
            $bbox->div()->addSectionTitle()->setClass("igk-danger")->Content = __("err.page.{$code}");
            $options[5404] = igk_io_subdomain_uri_name();
            $bbox->div()->addNode("i")->Content = __("msg.page.{$code}_1", $options[$code]);
            if (!igk_sys_env_production()) {
                $bbox->div()->addObData(function () {
                    $srv = "<div style='font-size:1.6em; padding:10px; background-color:#fefefe; border:1px solid :#ddd; color:#444;' >Server Info</div>";
                    $srv .= "<table>";
                    foreach ($_SERVER as $k => $v) {
                        $srv .= "<tr>";
                        $srv .= "<td>" . $k;
                        $srv .= "</td>";
                        $srv .= "<td>" . $v;
                        $srv .= "</td>";
                        $srv .= "</tr>";
                    }
                    $srv .= "</table>";
                    igk_wl($srv);
                });
            }
            $bbox->div()->addA("//" . igk_sys_domain_name())->Content = __("Go home");
            break;
        case 504:
            break;
        case 901:
            igk_set_header(403);
            $uri = $_SERVER["REQUEST_URI"];
            igk_elog("Failed to access a resources : {$uri}");
            if (igk_sys_env_production()) {
                igk_navto_home(null);
            }
            igk_exit();
            break;
    }
    if (is_callable($callback))
        $callback($doc);
    $opt = HtmlRenderer::CreateRenderOptions();
    $opt->Context = "html";
    $doc->renderAJX($opt);
}
///<summary>system is showdown</summary>
/**
 * system is showdown
 */
function igk_sys_shutdown_function($evt = null)
{
    if (!defined("IGK_APP_DIR")) {
        define("IGK_APP_DIR", dirname(__FILE__) . "/temp");
    }
    if ($tab = igk_environment()->get("register_shutdown_function")) {
        $args = func_get_args();
        foreach ($tab as $fc) {
            $fc(...$args);
        }
        igk_is_debug() && igk_ilog("shutdown : " . count($tab));
    }
    $s = "";
    $last = error_get_last();
    $ob_level = ob_get_level();
    if ($ob_level > 0) {
        $s = ob_get_contents();
        ob_end_flush();
    }
    if ($last) {
        igk_ilog($last, __FUNCTION__);
    }
    if (igk_get_env("sys://noshowlast_error") || !$last) {
        return;
    }
    if (ob_get_length() > 0)
        @ob_clean();
    $split_message = function ($a) use ($last) {
        $t = explode(IGK_LF, $a);
        $i = 0;
        $n = "<div>";
        foreach ($t as $r) {
            $n .= "<div>" . $r . "</div>";
        }
        $n .= "</div>";
        return $n;
    };
    $const = "constant";
    switch (igk_getv($last, 'type')) {
        case E_PARSE:
            if (igk_is_cmd()) {
                igk_wln("PARSE_ERROR-{$const('IGK_PLATEFORM_NAME')}");
                igk_wln("command line args");
                $f = igk_get_env("igk_include_script");
                if ($f) {
                    igk_wln($f . ":" . igk_getv($last, "line"));
                }
                return;
            }
            header("Content-Type:text/html");
            igk_header_no_cache();
            igk_set_header(404, 'error on page');
            $lastexp = igk_get_env(IGK_LAST_EVAL_KEY);
            if ($lastexp && igk_server_is_local()) {
                $lastexp = "eval function failed to evaluate : <div><code>" . htmlentities($lastexp) . "</code></div>";
            }
            $title = __("Parse Error EVAL");
            $lc = igk_get_env("sys://eval/lastscript");
            $min_css = igk_io_read_allfile(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/min.error.css");
            $ast = igk_ob_get($last);
            $s = <<<EOF
<html>
	<head>
		<title>{$title} - {$const('IGK_PLATEFORM_NAME')}</title>
		<style type="text/css">{$min_css}</style>
	</head>
	<body>
		<div style="color: #eee; background-color:#C8503B; padding:25px 8px;">[{$const('IGK_PLATEFORM_NAME')}] : PARSE ERROR</div>
		<div >script try to evaluate an expression that is not recognized</div>
		<div ><quote>{$lastexp}</quote></div>
		<div>
		{$ast}
		</div>
		<div>Script : </div>
		<code>
		{$lc}
		<code>
	</body>
</html>
EOF;

            echo $s;
            break;
        case E_ERROR:
            igk_set_env("sys://error/repport", 1);
            if (igk_is_cmd()) {
                igk_wln("[" . __FUNCTION__ . "] -- ERROR : " . igk_io_request_uri());
                igk_session_destroy();
                igk_wln($last);
            } else if (!ini_get("display_errors")) {
                header("Content-Type: text/html");
                $v_file = IGK_LIB_DIR . "/Views/error/exceptions.phtml";
                if (file_exists($v_file)) {
                    $fname = basename($v_file);
                    $source = __FUNCTION__;
                    $error_style = igk_io_baseuri() . "/Lib/igk/" . IGK_STYLE_FOLDER . "/errors.css";
                    $trace = igk_ob_get_func("igk_trace");
                    igk_set_header(500);
                    include($v_file);
                    return;
                } else {
                    $trace = igk_ob_get_func("igk_trace");
                    $dv = "<!DocType html ><html><body style='margin:0px;'><div>";
                    $dv .= "<div style='font-size:2.1em; color:#fe8e5e; background-color:#C8503B; padding:10px 5px;' >/!\\ Error /!\\</div>";
                    $dv .= "</div>";
                    $dv .= "<div>";
                    $dv .= $trace;
                    $dv .= "</div>";
                    $dv .= "<div>";
                    $cls = array("#ea9", "adf");
                    $cli = 0;
                    $cl = "";
                    foreach ($last as $k => $v) {
                        if ($cli == 0) {
                            $cli = 1;
                        } else
                            $cli = 0;
                        $cl = $cls[$cli];
                        $dv .= "<div style='font-size:0.8em'>";
                        $dv .= "<div style='display:inline-block; color: #445884; float:left; width:20%;'>{$k}</div>";
                        $dv .= "<div style='display:inline-block; float:left; width:70%; background-color:{$cl};' >";
                        $dv .= ($k == "message" ? $split_message($v) : $v);
                        $dv .= "</div>";
                        $dv .= "<div style='clear:both;' ></div>";
                        $dv .= "</div>";
                    }
                    $dv .= "</div>";
                    $dv .= "</body></html>";
                    if (igk_sys_env_production()) {
                        igk_mail_admin_send($dv);
                    } else
                        echo $dv;
                }
            } else {
                if (igk_environment()->isDev()) {
                    $dv = "<!DOCTUYPE html>";
                    $dv .= "<html><head></head><body><div>";
                    $dv .= "ERROR : " . $last["type"];
                    $dv .= "<ul>";
                    $dv .= "<li><label>Message: </label>" . implode("<br />", explode("\n", $last["message"])) . "</li>";
                    $dv .= "</ul>";
                    $dv .= "</div></body></html>";
                    igk_wln($dv);
                }
            }
            break;
    }
    if (!defined("IGK_NODESTROY_ON_FATAL")) {
        igk_session_destroy();
    }
}
///<summary></summary>
/**
 * 
 */
function igk_sys_srv_domain_name()
{
    $psrv = igk_server_name();
    $tab = null;
    if (preg_match_all("/(www\.)?(?<domain>(.)+)$/i", $psrv, $tab))
        return $tab["domain"][0];
    return null;
}
///<summary></summary>
/**
 * 
 */
function igk_sys_srv_is_ip()
{
    $host = igk_server()->HTTP_HOST;
    if (IGKValidator::IsIpAddress($host))
        return 1;
    return 0;
}
///<summary></summary>
/**
 * 
 */
function igk_sys_srv_is_secure()
{
    $srv = igk_server();
    return $srv->HTTPS || $srv->SSL_PROTOCOL;
}

///<summary>shortcut to referer</summary>
/**
 * shortcut to referer
 */
function igk_sys_srv_referer()
{
    return igk_server()->HTTP_REFERER;
}
///<summary></summary>
///<param name="scheme" default="'http'"></param>
/**
 * 
 * @param mixed $scheme 
 */
function igk_sys_srv_uri_scheme($scheme = 'http')
{
    $r = igk_server()->REQUEST_SCHEME;
    $uri = igk_server()->SCRIPT_URI;
    if ($r)
        return $r;
    if ($uri && preg_match_all("/^(?<scheme>(.)+):\/\//i", $uri, $tab))
        return $tab["scheme"][0];
    return $scheme;
}
///<summary></summary>
///<param name="file"></param>
///<param name="render"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $render 
 */
function igk_sys_start_engine($file, $render = true)
{
    try {
        igk_sys_handle_cache();
        IGKApplication::Boot('web')
            ->run($file, $render);
    } catch (\Exception $ex) {
        if (igk_environment()->isDev()) {
            igk_ilog(json_encode(["Exception" => $ex->getMessage(), "trace" => $ex->getTrace(), "location" => __FILE__ . ":" . __LINE__], JSON_PRETTY_PRINT));
        }
        igk_show_exception($ex);
    }
}
///<summary>store document as cache</summary>
///<return>the document rendering output</return>
/**
 * store document as cache
 */
function igk_sys_store_doc_cache($doc, $file, $uri = null)
{
    $f = igk_dir(IGK_APP_DIR . "/Caches/html" . $file);
    $opt = HtmlRenderer::CreateRenderOptions();
    $opt->Cache = 1;
    $o = $doc->render($opt);
    igk_io_save_file_as_utf8_wbom($f, $o, true);
    if ($uri) {
        igk_sys_add_cache_uri($uri);
    }
    return $o;
}
///<summary></summary>
///<param name="src"></param>
///<param name="name"></param>
///<param name="uri" default="null"></param>
/**
 * 
 * @param mixed $src 
 * @param mixed $name 
 * @param mixed $uri 
 */
function igk_sys_store_str_cache($src, $name, $uri = null)
{
    $fn = preg_replace("/(\+|=|\?)/i", "_", $name);
    $cdir = igk_sys_html_cache_dir();
    $f = igk_dir($cdir . $fn);
    $dir = dirname($f);
    $sub = strlen($cdir);
    if (IO::CreateDir($dir)) {
        if (igk_io_save_file_as_utf8_wbom($f, $src, true)) {
            if ($uri) {
                igk_sys_add_cache_uri($name, substr($f, $sub));
            }
            return $f;
        }
    }
    return null;
}
///<summary></summary>
///<param name="t"></param>
/**
 * 
 * @param mixed $t 
 */
function igk_sys_store_uri_cache($t)
{
    $f = IGK_APP_DIR . "/Caches/uri.cache";
    $s = "<?php" . IGK_LF;
    foreach ($t as $k => $v) {
        $s .= "\$t[\"{$k}\"]=\"{$v}\";" . IGK_LF;
    }
    $s .= "?>";
    igk_io_save_file_as_utf8_wbom($f, $s, true);
}
///<summary> get the subdomain name</summary>
/**
 *  get the subdomain name
 */
function igk_sys_subdomain_name()
{
    return IGKSubDomainManager::GetSubDomainName();
}

///<summary></summary>
///<param name="ctrlname"></param>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrlname 
 * @param mixed $ctrl 
 */
function igk_sys_unregview($ctrlname, $ctrl)
{
    $ctrl = igk_getctrl($ctrlname, false);
    if (($ctrl == null) || ($ctrl == null))
        return;
    $ctrl->unregView($ctrl);
}
///<summary>get the system version. alias function of constant IGK_VERSION</summary>
/**
 * get the system version. alias function of constant IGK_VERSION
 */
function igk_sys_version()
{
    return IGK_VERSION;
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_sys_viewctrl($name)
{
    $ctrl = igk_getctrl($name);
    if ($ctrl != null) {
        $ctrl->View();
    }
}
///<summary>zip core library</summary>
///<param name="$tfile" type="string" >path where to store</param>
///<return>bool</return>
/**
 * zip core library
 * @param string $tfile path where to store
 * @return bool true if succeed
 */
function igk_sys_zip_core(string $tfile, $incVersion = false)
{
    if (!class_exists(ZipArchive::class, false))
        return false;
    $zip = new ZipArchive();

    if ($zip->open($tfile, ZIPARCHIVE::CREATE)) {
        $files = igk_zip_dir(IGK_LIB_DIR, $zip, "Lib/igk", "/(Lib\/igk\/(temp|application|.Caches|Data\/(config.xml|domain.conf)))|(\.(vscode|git(ignore)?|gkds|DS_Store|bak)$)/");
        $manifest = igk_create_xmlnode("manifest");
        $manifest["xmlns"] = "https://www.igkdev.com/balafon/schemas/manifest";
        $manifest["appName"] = IGK_PLATEFORM_NAME;
        $manifest->add("version")->Content = IGK_VERSION;
        $manifest->add("author")->Content = IGK_AUTHOR;
        $manifest->add("date")->Content = date("Ymd His");
        $zip->addFromString("manifest.xml", $manifest->render((object)["Indent" => true]));
        $zip->addFromString("__lib.def", IGK_VERSION);
        if ($incVersion) {
            list($major, $minor, $x, $y) = explode(".", IGK_VERSION);
            $x = $incVersion;
            $zip->addFromString("Lib/igk/igk_version.php", implode(".", [$major, $minor, $x, $y]));
        }
        if ($files) {
            foreach ($files as $k) {
                $manifest->add("file")->setAttribute("entry", $k);
            }
        }
        return @$zip->close();
    }
    return false;
}
///<summary>Represente igk_sys_zip_project function</summary>
///<param name="controller"></param>
///<param name="path"></param>
///<param name="author" default="IGK_AUTHOR"></param>
/**
 * Represente igk_sys_zip_project function
 * @param string|BaseController $controller name
 * @param mixed $path where to store
 * @param mixed $author default author setting
 */
function igk_sys_zip_project($controller, $path, $exclude_regex = null, $author = IGK_AUTHOR, ?array $manifestOptions = [])
{
    if (!class_exists(ZipArchive::class)) {
        return false;
    }
    $pdir =  $g = is_string($controller) ? $controller : $controller->getDeclaredDir();
    $ref = "/(\/(temp|node_modules))|\.(vscode|git(ignore)?|gkds|DS_Store)$/";
    if (is_null($exclude_regex)) {
        SyncProjectSettings::InitProjectExcludeDir($pdir, $excludir);
        $rc = Replacement::RegexExpressionFromString(implode("|", array_keys($excludir)));
        $exclude_regex = "/" . trim($rc, "/") . '|' . trim($ref, '/') . "/";
    }

    $ignore = $exclude_regex; //  ?? "/(\/(temp|node_modules))|\.(vscode|git(ignore)?|gkds|DS_Store)$/";
    $zip = new ZipArchive();
    if (file_exists($path)) {
        @unlink($path);
    }
    IO::CreateDir(dirname($path));
    if ($zip->open($path, ZIPARCHIVE::CREATE)) {
        $g = is_string($controller) ? $controller : $controller->getDeclaredDir();
        $prjname = is_string($controller) ? basename($g) :
            igk_str_snake(basename(igk_dir(get_class($controller))));
        igk_zip_dir($g, $zip, $prjname, $ignore);
        $manifest = igk_create_xmlnode("manifest");
        $manifest["xmlns"] = "https://schema.igkdev.com/project";
        $manifest["appName"] = IGK_PLATEFORM_NAME . "/" . $prjname;
        $manifest->add("version")->Content = $controller->Configs->get("version", "1.0");
        $manifest->add("author")->Content = $author;
        $manifest->add("date")->Content = date("Ymd His");
        if ($manifestOptions) {
            $manifest->setAttributes($manifestOptions);
        }
        $zip->addFromString("manifest.xml", $manifest->render());
        $zip->addFromString("__project.def", "");
        igk_is_debug() && Logger::info("close zip");
        $zip->close();
        return true;
    }
    return false;
}
///<summary>activate the current template</summary>
/**
 * activate the current template
 */
function igk_template_activate($name)
{
}
///<summary>get the current active template</summary>
/**
 * get the current active template
 */
function igk_template_active()
{
}
///<summary></summary>
/**
 * 
 */
function igk_template_footer()
{
    $c = igk_get_env(IGKEnvironment::CURRENT_CTRL);
    $t = $t ?? $c->TargetNode;
}
///<summary></summary>
///<param name="t" default="null"></param>
/**
 * 
 * @param mixed $t 
 */
function igk_template_header($t = null)
{
    $c = igk_get_env(IGKEnvironment::CURRENT_CTRL);
    $t = $t ?? $c->TargetNode;
}
///<summary>get list of templates</summary>
/**
 * get list of templates
 */
function igk_template_list()
{
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_template_register($name)
{
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_template_unregister($name)
{
}
///<summary></summary>
///<param name="condition"></param>
///<param name="target"></param>
///<param name="message"></param>
/**
 * 
 * @param mixed $condition 
 * @param mixed $target 
 * @param mixed $message 
 */
function igk_test_assert($condition, $target, $message)
{
    if ($condition)
        $target->add("message")->Content = $message;
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_text($msg)
{
    if (func_num_args() > 1)
        $msg = implode("\n", func_get_args());
    igk_do_response(new \IGK\System\Http\WebResponse($msg, 200, [
        "Content-Type: text/plain"
    ]));
}
///<summary></summary>
///<param name="file"></param>
///<param name="line"></param>
/**
 * 
 * @param mixed $file 
 * @param mixed $line 
 */
function igk_throw_exception($file, $line)
{
    try {
        igk_die("custom_exception", $file, $line);
    } catch (Exception $Ex) {
        igk_show_exception($Ex);
        igk_exit();
    }
}
///<summary></summary>
///<param name="d"></param>
///<param name="from" default="null"></param>
/**
 * 
 * @param mixed $d 
 * @param mixed $from 
 */
function igk_time_max_info($d, $from = null)
{
    $c = $from ?? time();
    $d = $d - $c;
    if ($d <= 0)
        return array($d, 'n');
    if ($d < 60) {
        return array($d, 's');
    }
    $d = round($d / 60);
    if ($d < 60)
        return array($d, 'm');
    $d = round($d / 60);
    if ($d <= 24)
        return array($d, 'h');
    $d = round($d / 24);
    if ($d < 365)
        return array($d, 'd');
    $d = round($d / 365);
    return array($d, "y");
}
///<summary> get the time span</summary>
///<usage> igk_time_span('Ymd', '20150518')</usage>
/**
 *  get the time span
 */
function igk_time_span($format, $value)
{
    $b = date_parse_from_format($format, $value);
    $s = mktime($b["hour"], $b["minute"], $b["second"], $b["month"], $b["day"], $b["year"]);
    return $s;
}
///<summary></summary>
///<param name="obj"></param>
///<param name="emptyarray" default="1"></param>
/**
 * 
 * @param mixed $obj 
 * @param mixed $emptyarray 
 */
function igk_to_array($obj, $emptyarray = 1)
{
    if (is_array($obj))
        return $obj;
    if (is_object($obj)) {
        if (method_exists($obj, "to_array"))
            return $obj->to_array();
    }
    return $obj == null ? (($emptyarray) ? array() : null) : array($obj);
}
///<summary>call a tool action</summary>
/**
 * call a tool action
 */
function igk_tool_call($name, $args = null)
{
    if (empty($name))
        return false;
    $r = igk_get_env(IGK_KEY_TOOLS);
    if ($r == null) {
        igk_notifyctrl()->addWarningr("msg.noactionregister_1", $name);
        return false;
    }
    $p = (object)igk_getv($r, $name);
    if ($p && is_callable($p->Action)) {
        $f = $p->Action;
        if ($args == null)
            return $f();
        else
            return call_user_func_array($f, is_array($args) ? $args : array($args));
    }
    return false;
}
///<summary>register a tool to a system engine</summary>
/**
 * register a tool to a system engine
 */
function igk_tool_reg($name, $prop)
{
    $r = igk_get_env(IGK_KEY_TOOLS);
    if ($r == null)
        $r = array();
    if (isset($r[$name])) {
    }
    $r[$name] = $prop;
    igk_set_env(IGK_KEY_TOOLS, $r);
}
///<summary></summary>
///<param name="level" default="1"></param>
/**
 * 
 * @param mixed $level 
 */
function igk_trace_function($level = 1)
{
    $c = IGKException::GetCallingFunction($level);
    return $c;
}
///<summary>Represente igk_trace_log function</summary>
///<param name="message"></param>
///<param name="tag" default="IGK"></param>
///<param name="level" default="2"></param>
/**
 * Represente igk_trace_log function
 * @param mixed $message 
 * @param mixed $tag 
 * @param mixed $level 
 */
function igk_trace_log($message, $tag = "IGK", $level = 2)
{
    $s = igk_ob_get_func(function () use ($message, $level) {
        igk_wl($message . "\n");
        igk_trace($level);
    });
    igk_ilog($s, $tag);
}
///<summary></summary>
/**
 * 
 */
function igk_tracing()
{
    $f = igk_io_basedir() . "/Data/.trace";
    if (file_exists($f))
        return true;
    return false;
}
///<summary></summary>
///<param name="b"></param>
/**
 * 
 * @param mixed $b 
 */
function igk_typeof($b)
{
    if ($b === null)
        return "null";
    if (is_string($b))
        return "string";
    if (is_object($b)) {
        return get_class($b);
    }
    if (is_array($b))
        return "array";
    return "unknow";
}
///<summary></summary>
///<param name="path"></param>
/**
 * 
 * @param mixed $path 
 */
function igk_uninstall_module($path)
{
    throw new \IGK\System\Exceptions\NotImplementException(__FUNCTION__);
}
///<summary></summary>
///<param name="name"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $callback 
 * @param bool $all remove all occurence
 */
function igk_unreg_hook($name, $callback, $all = true)
{
    return IGKEvents::unreg_hook($name, $callback, $all);
}
///<summary>unregister html component </summary>
/**
 * unregister html component 
 */
function igk_unreg_html_component($name, $ns = "igk")
{
    igk_reg_html_component($name, null, $ns);
}
///<summary>unreg session event by name</summary>
///<param name="name">the key of the name session event</param>
///<param name="callback">callback to register</param>
/**
 * unreg session event by name
 * @param mixed $name the key of the name session event
 * @param mixed $callback callback to register
 */
function igk_unreg_session_event($name, $callback)
{
    $e = igk_get_session_event($name);
    $ctx = igk_current_context();
    $key = "sys://global_events";
    $rne = strpos("running|starting", $ctx) !== false;
    switch ($ctx) {
        case IGKAppContext::initializing:
            $e = igk_getv(igk_get_env($key, array()), $name);
            break;
        default:
            $e = igk_getv(igk_app()->session->Events, $name);
            break;
    }
    if ($e) {
        $t = array();
        $found = 0;
        foreach ($e as $v) {
            if (($v === $callback) || igk_cmp_array_value($v, $callback)) {
                $found = 1;
                continue;
            }
            $t[] = $v;
        }
        if ($found) {
            switch ($ctx) {
                case IGKAppContext::initializing:
                    $h = igk_get_env($key);
                    $h[$name] = $t;
                    igk_set_env($key, $h);
                    break;
                default:
                    $h = igk_app()->session->Events;
                    $h[$name] = $t;
                    igk_app()->session->Events = $h;
                    break;
            }
            return 1;
        }
    }
    if (!igk_sys_env_production())
        igk_die("unreg session event failed " . $name);
    return 0;
}
///<summary>unserialize internal data</summary>
/**
 * unserialize internal data
 */
function igk_unseri_data($data)
{
    $nobj = (object)array();
    $ln = strlen($data);
    $pos = 0;
    while ($pos < $ln) {
        $g = strpos($data, ':', $pos);
        if ($g === false)
            break;
        $v = "";
        $n = trim(substr($data, $pos, $g - $pos));
        $pos = $g + 1;
        while (($pos < $ln - 1) && ($data[$pos] == ' '))
            $pos++;
        if ($data[$pos] == '"') {
            $v = igk_str_read_brank($data, $pos, '"', '"');
        } else {
            $g = strpos($data, ';', $pos);
            if ($g === false) {
                $v = trim(substr($data, $pos));
            } else
                $v = trim(substr($data, $pos, $g - $pos));
        }
        $g = $pos + strlen($v);
        $nobj->$n = $v;
        $pos = $g + 1;
    }
    return $nobj;
}
///<summary>unset document</summary>
/**
 * unset document
 */
function igk_unset_document($doc)
{
    $tab = igk_app()->session->getParam(IGK_KEY_DOCUMENTS);
    $key = $doc->getParam(IGK_DOC_ID_PARAM);
    if (isset($tab[$key])) {
        unset($tab[$key]);
        igk_app()->session->setParam(IGK_KEY_DOCUMENTS, $tab);
    }
    $doc->Dispose();
}
///<summary></summary>
///<param name="f"></param>
/**
 * 
 * @param mixed $f 
 */
function igk_update_include($f)
{
    $f = igk_dir($f);
    $t = igk_get_env("sys://include/init");
    $source = $t["funcs"];
    $clcount = $t["classes"];
    $functions2 = get_defined_functions();
    $classes2 = get_declared_classes();
    if (count($functions2["user"]) > $source) {
        $ktab = array_slice($functions2["user"], $source);
        igk_reg_func_files($f, $ktab);
        $source += igk_count($ktab);
    }
    if (count($classes2) > $clcount) {
        $ktab = array_slice($classes2, $clcount);
        igk_reg_class_file($f, $ktab);
        $clcount += igk_count($ktab);
    }
}
///<summary></summary>
///<param name="u"></param>
///<param name="args" default="null"></param>
/**
 * 
 * @param mixed $u 
 * @param mixed $args 
 */
function igk_uri_add_args($u, $args = null)
{
    $u = parse_url($u);
    $q = $args ? http_build_query($args) : null;
    $o = $u["path"];
    if (isset($u["query"]))
        $o .= "?" . $u["query"] . ($q ? "&" . $q : null);
    else if (!empty($q)) {
        $o .= "?" . $q;
    }
    return $o;
}
///<summary></summary>
///<param name="qparam" default="null"></param>
/**
 * 
 * @param mixed $qparam 
 */
function igk_uri_invokecurrent($qparam = null)
{
    $curi = igk_io_baseuri(igk_io_get_relative_currenturi()) . $qparam;
    $f = igk_curl_post_uri($curi, null);
    return $f;
}
///<summary>check is uri match by ignoring the case</summary>
/**
 * check is uri match by ignoring the case
 */
function igk_uri_is_match($u1, $u2)
{
    return strtolower($u1) == strtolower($u2);
}
///<summary>get the current uri </summary>
/**
 * get the current uri 
 */
function igk_uri_rquery()
{
    return igk_getv($_SERVER, 'REQUEST_QUERY');
}
/**
 * uri sanitize value
 */
function igk_uri_sanitize($v)
{
    return urlencode(str_replace(';', '_%', $v));
}
/**
 * reverse uri sanitize
 */
function igk_uri_unsanitize($v)
{
    $v = urldecode($v);
    $v = str_replace('_%', ';', $v);
    return $v;
}
///<summary></summary>
///<param name="packagename"></param>
/**
 * 
 * @param mixed $packagename 
 */
function igk_use_component_package($packagename)
{
    $key = "sys://components/packages";
    $tab = igk_get_env($key);
    $n = strtolower($packagename);
    if (!$tab || !($t = igk_getv($tab, $n)))
        return false;
    igk_set_env("sys://components/currentpackage", $n);
    return 1;
}
///<summary> define usage of required package list</summary>
///<param name="packaglist"> mixed : semi colon separated list of string of array or string </param>
/**
 *  define usage of required package list
 * @param mixed $packaglist  mixed : semi colon separated list of string of array or string 
 */
function igk_use_web_package($packageList)
{
    $tab = $packageList;
    if (is_string($tab))
        $tab = explode(";", $tab);
    igk_set_env("sys://web/package", $tab);
}
///<summary>shortcut to get system user</summary>
/**
 * shortcut to get system user
 */
function igk_user()
{
    return igk_app()->session->User;
}
///<summary></summary>
///<param name="name"></param>
///<param name="datatype"></param>
///<param name="cardinality"></param>
///<param name="type"></param>
/**
 * 
 * @param mixed $name 
 * @param mixed $datatype 
 * @param mixed $cardinality 
 * @param mixed $type 
 * @deprecated
 */
function igk_user_add_info_type($name, $datatype, $cardinality, $type)
{
    $ctrl = igk_getctrl(IGK_SYSDB_CTRL);
    $v_ktt = array(
        IGK_FD_NAME => $name,
        "clDataType" => $datatype,
        "clCardinality" => $cardinality,
        "clType" => $type
    );
    return igk_db_insert_if_not_exists($ctrl, igk_db_get_table_name(IGK_TB_USER_INFO_TYPES), $v_ktt);
}
///<summary></summary>
///<param name="r"></param>
///<param name="inf"></param>
///<param name="v"></param>
///<param name="ctrl"></param>
/**
 * 
 * @param object $r 
 * @param mixed $inf 
 * @param mixed $v 
 * @param mixed $ctrl 
 */
function igk_user_build_info($r, $inf, $v, $ctrl)
{
    $d =  ($r->clType == 0) ? $ctrl->getDataAdapter()->selectAll(
        $r->clDataType,
        array(IGK_FD_ID => $v->clValue)
    )->getRowAtIndex(0) : $v->clValue;

    return (object)array(
        IGK_FD_NAME => $inf,
        "clDescription" => $v->clDescription,
        "clValue" => $d
    );
}
///<summary></summary>
///<param name="login"></param>
/**
 * 
 * @param mixed $login 
 */
function igk_user_connectas($login)
{
    $u = igk_get_user_bylogin($login);
    if ($u) {
        igk_getctrl(IGK_USER_CTRL)->setGlobalUser($u);
        return 1;
    }
    return 0;
}
///<summary>user fonctions . get fullname </summary>
/**
 * user fonctions . get fullname 
 */
function igk_user_fullname($u)
{
    if (!empty(trim($t = igk_getv($u, "clDisplay")))) {
        return $t;
    }
    return igk_getv($u, "clFirstName") . " " . igk_getv($u, "clLastName");
}
///<summary>generate password</summary>
/**
 * generate password
 */
function igk_user_genpwd($length = 8)
{
    static $chars = null;
    if (!$chars)
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#!()_";
    $r = "";
    $count = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
        $idx = rand(0, $count - 1);
        $r .= substr($chars, $idx, 1);
    }
    return $r;
}
///<summary></summary>
///<param name="u"></param>
/**
 * 
 * @param mixed $u 
 */
function igk_user_get_env_param($u)
{
    $k = IGK_ENV_PARAM_KEY;
    return igk_getv($u, $k);
}
///<summary></summary>
///<param name="inf"></param>
///<param name="uid" default="null"></param>
/**
 * 
 * @param string $inf 
 * @param mixed $uid 
 */
function igk_user_info(string $inf, $uid = null)
{
    $u = $uid == null ? igk_app()->session->getUser() : igk_get_user($uid, ['Columns' => 'clId']);
    if ($u == null)
        return null;

    $ctrl = igk_getctrl(IGK_SYSDB_CTRL);
    $v = \IGK\Models\UserInfoTypes::select_all([IGK_FD_NAME => $inf]);
    $r = null;
    if ($v !== null) {
        $r = igk_getv($v, 0); // ->getRowAtIndex(0);

        if ($r == null)
            return null;
        // $v_q = igk_db_table_select_where(igk_db_get_table_name(IGK_TB_USER_INFOS), array("clUserInfoType_Id" => $r->clId, "clUser_Id" => $u->clId), $ctrl);
        $v_q = \IGK\Models\UserInfos::select_all(["clUserInfoType_Id" => $r->clId, "clUser_Id" => $u->clId]);
        $v_n = $r ? $r->clCardinality : 'infinity';
        switch ($v_n) {
            case 0:
            case 'infinity':
                $tab = array();
                foreach ($v_q  as $v) {
                    $tab[] = igk_user_build_info($r, $inf, $v, $ctrl);
                }
                return $tab;
            case 1:
                if (igk_count($v_q) > 0) {
                    $v = igk_getv($v_q, 0);
                    return igk_user_build_info($r, $inf, $v, $ctrl);
                }
                return null;
            default:
                if ($v_n < 0) {
                    igk_die("error on data cardinality ");
                }
                $v_tk = min($v_n, igk_count($v_q));
                $tab = array();
                foreach ($v_q as $v) {
                    $tab[] = igk_user_build_info($r, $inf, $v, $ctrl);
                    $v_tk--;
                    if ($v_tk <= 0)
                        break;
                }
                return $tab;
        }
    }
    igk_trace();
    igk_exit();
    return "no";
}
///<summary>check if password require ok</summary>
/**
 * check if password require ok
 */
function igk_user_pwd_required($pwd, $repwd)
{
    if (!$pwd || !$repwd || ($pwd != $repwd)) {
        return false;
    }
    return IGKValidator::IsValidPwd($pwd);
}
///<summary>set environment param to users</summary>
/**
 * set environment param to users
 */
function igk_user_set_env_param($u, $pn, $obj)
{
    if ($u === null)
        return 0;
    $k = IGK_ENV_PARAM_KEY;
    $tab = igk_getv($u, $k);
    if ($tab == null)
        $tab = array();
    $tab[$pn] = $obj;
    $u->$k = $tab;
    return 1;
}
///<summary> get user information</summary>
///<param name="cardinality"> 0 for infinite value, or more than 0</param>
///<param name="type"> 1 for regex, 2 for data type</param>
///<param name="expression"> regex or data type</param>
///<return> single value or array of values if found</return>
///<remark> in case of new data to insert cardinality type and expression must be setup . system must init user setting info to register data</remark>
/**
 *  get user information
 * @param mixed $cardinality  0 for infinite value, or more than 0
 * @param mixed $type  1 for regex, 2 for data type
 * @param mixed $expression  regex or data type
 */
function igk_user_set_info($inf, $data, $uid = null, $cardinality = 0, $type = 1, $expression = "(.)+")
{
    $u = $uid == null ? igk_app()->session->User : igk_get_user($uid);
    $ei = igk_get_env("sys://db_init");
    if (!$ei && ($u == null)) {
        return null;
    }
    $cond = array(IGK_FD_NAME => $inf);
    $ctrl = igk_getctrl(IGK_SYSDB_CTRL);
    $v = \IGK\Models\UserInfoTypes::select_row($cond);

    // igk_db_table_select_where(igk_db_get_table_name(IGK_TB_USER_INFO_TYPES), array(IGK_FD_NAME => $inf), $ctrl);
    $r = null;
    if ($v == null) {
        if ($ei == 1) {
            $tab = $ctrl->getInfoDataEntry($inf, $cardinality, $type, $expression);
            $r = \IGK\Models\UserInfoTypes::select_row($cond);
        }
    } else {
        $r = $v;
    }
    if ($u == null)
        return null;
    $insert = true;
    if ($r == null)
        return null;
    $v_n = $r->clCardinality;
    $v_kkt = array("clUserInfoType_Id" => $r->clId, "clUser_Id" => $u->clId);
    $model = \IGK\Models\UserInfos::model();
    $v_q = $model::select_all($v_kkt); //
    $v_kkt["clValue"] = $data;
    switch ($r->clType) {
        case 1:
            if (!empty($r->clDataType)) {
                if ($r->clRegex && !preg_match($r->clRegex, $data)) {
                    igk_ilog([__FUNCTION__,  $r->clRegex, "Data not match " . $data]);
                    return false;
                }
            }
            break;
        case 0:
            if (!is_numeric($data) || empty($r->clDataType) || (
                ($model->prepare()
                    ->where(array(IGK_FD_ID => $data))->execute()->getRowCount() == 0)
            )) {
                igk_wln(__FUNCTION__ . ": Not valid data [{$r->clDataType}] : $data");
                igk_wln($r);
                return false;
            } else {
                igk_debug_wln("valid data");
            }
            break;
    }
    switch ($v_n) {
        case 0:
            break;
        case 1:
            if ($v_q->RowCount > 0) {
                if ($v_q->RowCount == 1) {
                    $r = $v_q->getRowAtIndex(0);
                    $r->clValue = $data;
                    igk_db_update($ctrl, igk_db_get_table_name(IGK_TB_USER_INFOS), $r);
                    return true;
                } else
                    igk_log_write_i(__FUNCTION__, "DataBaseStructure Error : data will not be inserted");
                return false;
            }
            break;
        default:
            if ($v_n < 0) {
                igk_die("error on data cardinality " . $v_n);
            }
            if (!($v_n < $v_q->RowCount)) {
                return false;
            }
            break;
    }
    if ($insert) {
        $g = \IGK\Models\UserInfos::insertIfNotExists($v_kkt);
        return $g;
    }
    return false;
}
///<summary></summary>
///<param name="u"></param>
/**
 * 
 * @param mixed $u 
 */
function igk_user_store_tokenid($u)
{
    if (igk_environment()->NO_SESSION)
        return;
    $id = igk_create_cref();
    setcookie(igk_get_cookie_name(igk_sys_domain_name() . "/" . Cookies::USER_ID), $u->clId . ":" . $id, time() + (86400 * 7), igk_get_cookie_path());
    igk_user_set_info("TOKENID", $id, 1, 1);
    $u->clTokenStored = 1;
}
///<summary></summary>
///<param name="item" ref="true"></param>
///<param name="params"></param>
/**
 * 
 * @param mixed $item 
 * @param mixed $params 
 */
function igk_usort(&$item, $params)
{
    if ($item === null) {
        igk_trace();
        igk_die(__FUNCTION__ . " : item is null");
        return;
    }
    if (is_array($item)) {
        if (!is_callable($params)) {
            igk_trace();
        }
        usort($item, $params);
    } else {
        $item->Sort($params);
    }
}
///<summary></summary>
///<param name="msg"></param>
///<param name="cibling" default="null"></param>
/**
 * 
 * @param mixed $msg 
 * @param mixed $cibling 
 */
function igk_val_add_error($msg, $cibling = null)
{
    $li = igk_val_node()->li();
    $li->Content = $msg;
    IGKValidator::AddCibling($cibling);
    $li->cibling = $cibling;
    return $li;
}
///<summary></summary>
///<param name="e"></param>
///<param name="name"></param>
/**
 * 
 * @param mixed $e 
 * @param mixed $name 
 */
function igk_val_cbcss($e, $name)
{
    if ($e && isset($e[$name]))
        return "err_c";
}
///<summary></summary>
///<param name="callback"></param>
///<param name="object"></param>
///<param name="name"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $callback 
 * @param mixed $object 
 * @param mixed $name 
 * @param mixed $msg 
 */
function igk_val_check($callback, $object, $name, $msg)
{
    if (is_bool($callback)) {
        if ($callback) {
            igk_val_add_error($msg, $name);
        }
        return;
    }
    $v = call_user_func_array(array("IGKValidator", $callback), array($object->$name));
    if ($v) {
        igk_val_add_error($msg, $name);
    }
}
///<summary></summary>
/**
 * 
 */
function igk_val_cibling()
{
    return IGKValidator::Cibling();
}
///<summary></summary>
/**
 * 
 */
function igk_val_haserror()
{
    return IGKValidator::Error()->HasChilds;
}
///<summary></summary>
/**
 * 
 */
function igk_val_init()
{
    IGKValidator::Init();
}
///<summary></summary>
///<param name="type"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $type 
 * @param mixed $msg 
 */
function igk_val_ispic($type, $msg)
{
    if (!igk_io_fileispicture($type)) {
        IGKValidator::Error()->li()->Content = $msg;
    }
}
///<summary></summary>
///<param name="tname"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $tname 
 * @param mixed $msg 
 */
function igk_val_isstrnullorempty($tname, $msg)
{
    if (IGKValidator::IsStringNullOrEmpty($tname)) {
        IGKValidator::Error()->li()->Content = $msg;
    }
}
///<summary></summary>
/**
 * 
 */
function igk_val_node()
{
    return IGKValidator::Error();
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="name"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $name 
 */
function igk_val_regparam($ctrl, $name)
{
    $ctrl->setParam($name . ":error", igk_val_node());
    $ctrl->setParam($name . ":errorcibling", igk_val_cibling());
}
///<summary></summary>
///<param name="ctrl"></param>
///<param name="name"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $name 
 */
function igk_val_unregparam($ctrl, $name)
{
    $ctrl->setParam($name . ":error", null);
    $ctrl->setParam($name . ":errorcibling", null);
}
///<summary></summary>
///<param name="regenerate"></param>
/**
 * 
 * @param mixed $regenerate 
 */
function igk_valid_cref($regenerate = 0, $throwex = 0)
{
    $sess = igk_app()->getSession();
    $cref = base64_encode($sess->getCRef());

    $result = (igk_getr($cref) == 1);
    if ($regenerate) {
        $sess->generateCref();
    }
    if (!$result && igk_environment()->isDev() && igk_getr('dev-valid-cref')) {
        $result = 1;
    }
    if (!$result && $throwex) {
        throw new \IGK\System\Exceptions\CrefNotValidException();
    }
    return $result;
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_verbose_wln($msg)
{
    if (defined("IGK_VERBOSE")) {
        igk_wln($msg);
        igk_flush_data();
    }
}
///<summary>Represente igk_view_action_path function</summary>
/**
 * Represente igk_view_action_path function
 */
function igk_view_action_path()
{
    if ($t = igk_view_env_actions()) {
        return implode("/", [$t["v"], igk_view_handle_name()]);
    }
    return null;
}
///<summary>shortcut to retrieve view argument</summary>
/**
 * shortcut to retrieve view argument
 */
function igk_view_args($n = null, $default = null)
{
    return \IGK\Helper\ViewHelper::GetViewArgs($n, $default);
}
///<summary> shortcut : view controller's article content</summary>
/**
 *  shortcut : view controller's article content
 */
function igk_view_article($ctrl, $name)
{
    $f = $name;
    if (!file_exists($f))
        $f = $ctrl->getArticle($f);
    $n = igk_html_node_notagnode();
    igk_html_article($ctrl, $f, $n);
    return $n->render();
}
///<summary></summary>
///<param name="f"></param>
///<param name="bindinginfo"></param>
///<param name="target"></param>
///<param name="exit" default="1"></param>
/**
 * 
 * @param mixed $f 
 * @param mixed $bindinginfo 
 * @param mixed $target 
 * @param mixed $exit 
 */
function igk_view_bindfile($f, $bindinginfo, $target, $exit = 1)
{
    if (file_exists($f)) {
        igk_include($f, $bindinginfo, $target);
        if ($exit) {
            $target->renderAJX();
            igk_exit();
        }
        return 1;
    } else {
        if ($exit) {
            if (is_callable($g = igk_getv($bindinginfo, "requestFailedCallback"))) {
                $g($f, $bindinginfo, 404);
            } else {
                $ac = igk_getv($bindinginfo, "action");
                igk_set_header(404);
                igk_wl("Oups!!!!! - Requested uri not found - " . $ac);
            }
            igk_exit();
        }
    }
    return 0;
}
///<summary>Represente igk_view_builder_extra function</summary>
///<param name="file"></param>
///<param name="option"></param>
/**
 * Represente igk_view_builder_extra function
 * @param mixed $file 
 * @param mixed $option 
 */
function igk_view_builder_extra($file, $option)
{

    $extra = "\n// + | view file: ";
    $extra .= (igk_environment()->isDev() ? $file : igk_io_collapse_path($file)) . "\n";

    if (!HtmlProcessInstructionNode::IsPhpCloseInstruct($option)) {
        $extra = "\n<?php " . $extra;
    }
    return $extra;
}
///<summary>trait parameter</summary>
///<param name="ctrl"></param>
///<param name="$c"></param>
///<param name="f"></param>
///<param name="p" ref="true"></param>
/**
 * trait parameter
 * @param mixed $ctrl 
 * @param mixed $$c 
 * @param mixed $f 
 * @param mixed $p 
 */
// function igk_view_dispatch_args($ctrl, $c, $f, &$p)
// {
//     $g = substr($f, strlen($ctrl->getViewDir()) + 1);
//     if (is_array($p) && !preg_match("#^" . $c . "#", $g) || ((($ext = igk_io_path_ext($c)) != $c) && !preg_match("/phtml$/", $ext))) {
//         array_unshift($p, ...explode("/", $c));
//         return 1;
//     }
//     return false;
// }
///<summary></summary>
/**
 * 
 */
function igk_view_env_actions()
{
    return igk_get_env(IGKEnvironment::VIEW_HANDLE_ACTIONS);
}
///<summary></summary>
/**
 * 
 */
function igk_view_get_action_params()
{
    return ($r = igk_view_env_actions()) ? igk_getv($r, "args") : null;
}
///<summary></summary>
///<param name="action"></param>
/**
 * 
 * @param mixed $action 
 */
function igk_view_handle($action)
{
    $fname = igk_get_env(IGKEnvironment::VIEW_HANDLE_ACTIONS);
    if (!$fname)
        return;
    $fs = "sys://view/actions/" . $fname["v"];
    $params = func_num_args() > 1 ? array_slice(func_get_args(), 1) : $fname["args"];
    $fc = igk_get_env($fs . "/" . $action);
    $fc_result = null;
    if ($fc) {
        $ht = array_slice($params, 1);
        igk_set_env(IGKEnvironment::VIEW_CURRENT_ACTION, $action);
        if (igk_count($ht) > 0) {
            $fc_result = call_user_func_array($fc, $ht);
        } else
            $fc_result = $fc();
        unset($ht);
    }
    return $fc_result;
}
///<summary>handle view command action</summary>
/**
 * handle view command action
 */
function igk_view_handle_action($fname, $params, $redirectfailed = 1)
{
    $action = igk_getv($params, 0);
    $fs = "sys://view/actions/" . $fname;
    $fc_result = null;
    $fc = null;
    $v_errkey = IGKViewActionsConstants::HANDLE_ERROR;
    if ($action) {
        if ($action != $v_errkey) {
            $fc = igk_get_env($fs . "/" . $action);
        }
        if (!$fc) {
            if (is_array($v_tab = igk_get_env($fs . "/{$v_errkey}"))) {
                $fc = igk_getv($v_tab, 404);
                $params = array(implode("/", $params));
                array_unshift($params, null);
            }
        }
    } else {
        $redirect = igk_server()->REDIRECT_STATUS;
        if (isset($redirect)) {
            if (is_array($v_tab = igk_get_env($fs . "/{$v_errkey}"))) {
                $fc = igk_getv($v_tab, $redirect);
            }
        }
    }
    // + | -------------------------------------------------------
    // + | in case function is null and implement - DEFAULT HANDLE
    // + |
    if (is_null($fc) && ($fc = igk_get_env($fs . "/" . IGKViewActionsConstants::HANDLE_DEFAULT))) {
        $action = IGKViewActionsConstants::HANDLE_DEFAULT;
        array_unshift($params, null);
    }


    if ($fc) {
        igk_set_env(IGKEnvironment::VIEW_CURRENT_ACTION, $action);
        $ht = array_slice($params, 1);
        $fc_result = Dispatcher::Dispatch($fc, ...$ht);
    }
    return $fc_result;
}
///<summary>handle view command actions. </summary>
///<param name="viewName">command view name</param>
///<param name="arrayList">mixed. string(classname)|assoc array of callback|. </param>
///<param name="params">paramflag to enable handle specification</param>
///<param name="exit">exit after execution</param>
///<param name="flag">extra flag</param>
/**
 * handle view command actions. 
 * @param mixed $viewName command view name
 * @param mixed $arrayList mixed. string(classname)|assoc array of callback|. 
 * @param mixed $params paramflag to enable handle specification
 * @param bool $exit exit if handle function return true.
 * @param mixed $flag extra flag
 */
function igk_view_handle_actions($viewname, $arrayList, $params, $exit = 1, $flag = 0)
{
    igk_set_env(IGKEnvironment::VIEW_HANDLE_ACTIONS, array("v" => $viewname, "list" => $arrayList, "args" => $params));
    $b = 0;
    if (is_string($arrayList)) {
        if (class_exists($arrayList)) {
            $arrayList = new $arrayList();
        } else {
            igk_die("not allowed view action handler");
        }
    }
    if (is_array($arrayList)) {
        foreach ($arrayList as $k => $v) {
            igk_view_reg_action($viewname, $k, $v);
        }
        $b = igk_view_handle_action($viewname, $params);
        if ($b && $exit) {
            igk_do_response($b);
        }
    } else if (is_object($arrayList)) {
        $b = igk_view_handle_obj_action($viewname, $arrayList, $params, $exit, $flag);
    }
    igk_set_env(IGKEnvironment::VIEW_HANDLE_ACTIONS, null);
    if ($b && $exit) {
        $c = igk_get_current_base_ctrl();
        if ($c)
            $c->regSystemVars(null);
        igk_exit();
    }
    return $b;
}

/**
 * unset action 
 */
function igk_view_unset_action($viewname, $action)
{
    igk_set_env("sys://view/actions/" . $viewname . "/" . $action, null);
}

///<summary>get view handle last action name</summary>
/**
 * get view handle last action name
 */
function igk_view_handle_name()
{
    return igk_get_env(IGKEnvironment::VIEW_CURRENT_ACTION);
}
function igk_is_request_type(\ReflectionType $ref)
{
    return IGKType::GetName($ref) == \IGK\System\Http\Request::class;
}
///<summary>handle object action.</summary>
///<param name="fname">action identifier.</param>
///<param name="object">object that will be used to handle actions.</param>
///<param name="params">parameters.</param>
///<param name="flags">extra flag.</param>
/**
 * handle object action.
 * @param mixed $fname action identifier.
 * @param mixed $object object that will be used to handle actions.
 * @param mixed $params parameters.
 * @param mixed $flags extra flag.
 */
function igk_view_handle_obj_action($fname, $object, array $params = [], $exit = 1, $flag = 0)
{
    return IGKActionBase::HandleObjAction($fname, $object, $params, $exit, $flag);
}
if (!function_exists('igk_view_navto')) {
    function igk_view_navto($path)
    {
        if ($fname = ViewHelper::GetViewArgs('fname')) {
            igk_navto(ViewHelper::CurrentCtrl()->getAppUri($fname . $path));
        }
        return false;
    }
}
///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl 
 */
function igk_view_handle_uri($ctrl)
{
    if ($s = igk_view_handle_name())
        $s = "/" . $s;
    return $ctrl->getAppUri($ctrl->getCurrentView() . $s);
}
///<summary>get initialize view binding array info</summary>
/**
 * get initialize view binding array info
 */
function igk_view_init_bindinginfo()
{
    $t = array(
        "bindto" => null,
        "ctrl" => null,
        "doc" => null,
        "t" => null,
        "fname" => null,
        "params" => null,
        "requestFailedCallback" => null
    );
    return $t;
}
///<summary>register view action</summary>
/**
 * register view action
 */
function igk_view_reg_action($fname, $action, $callback)
{
    igk_set_env("sys://view/actions/" . $fname . "/" . $action, $callback);
}
///<summary></summary>
///<param name="tab"></param>
///<param name="tag" default="li"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $tag 
 */
function igk_warray($tab, $tag = "li")
{
    $n = igk_html_node_notagnode();
    if (is_array($tab))
        $n->addArrayList($tag, $tab);
    else
        $n->div()->Content = $tab;
    $n->renderAJX();
}
///<summary></summary>
///<param name="m"></param>
/**
 * 
 * @param mixed $m 
 */
function igk_wcode($m)
{
    igk_wl("<code>");
    igk_wl($m);
    igk_wl("</code>");
}
///<summary></summary>
/**
 * 
 */
function igk_web_defaultpage()
{
    return igk_configs()->get("menu_defaultPage", IGK_DEFAULT);
}
///<summary>shortcut to platform config</summary>
/**
 * shortcut to platform config
 */
function igk_web_get_config($name, $default = null)
{
    return igk_configs()->get($name, $default);
}
///<summary></summary>
/**
 * 
 */
function igk_web_prefix()
{
    return igk_configs()->website_prefix;
}
///<summary>write to buffer and exit</summary>
/**
 * write to buffer and exit
 */
function igk_wl_e($msg)
{
    call_user_func_array("igk_wl", func_get_args());
    igk_exit();
}
///<summary>utility: write a message in a textarea</summary>
/**
 * utility: write a message in a textarea
 */
function igk_wln_area($msg)
{
    $t = igk_create_node("textarea");
    $t->Content = $msg;
    $t->renderAJX();
}
///<summary></summary>
///<param name="cond"></param>
///<param name="msg"></param>
/**
 * 
 * @param mixed $cond 
 * @param mixed $msg 
 */
function igk_wln_assert($cond, $msg)
{
    if ($cond) {
        igk_wln($msg);
    }
}
///<summary>write with html</summary>
/**
 * write with html
 */
function igk_wln_html($msg)
{
    header("Content-Type:text/html");
    igk_wln($msg);
}
///<summary></summary>
///<param name="cond"></param>
/**
 * 
 * @param mixed $cond 
 */
function igk_wln_if($cond)
{
    if ((!$cond) || (count($args = array_slice(func_get_args(), 1)) == 0))
        return;
    call_user_func_array("igk_wln", $args);
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg 
 */
function igk_wln_ob_flushdata($msg)
{
    igk_wln($msg);
    igk_flush_data();
}
///<summary></summary>
/**
 * 
 */
function igk_wln_ob_get($obj)
{
    IGKOb::Start();
    igk_wl($obj);
    $c = IGKOb::Content();
    IGKOb::Clear();
    return $c;
}
///<summary>write in buffer output with html secification tag</summary>
/**
 * write in buffer output with html secification tag
 */
function igk_wnode($msg, $tag = 'div')
{
    $n = igk_create_node($tag);
    $n->addObData(function () use ($msg) {
        igk_wln($msg);
    });
    $n->renderAJX();
}
///<summary></summary>
///<param name="msg"></param>
/**
 * xml response helper
 * @param mixed $msg 
 */
function igk_xml($msg)
{
    return igk_do_response(new \IGK\System\Http\XmlResponse($msg));
}
///<summary>file reading object</summary>
/**
 * file reading object
 */
function igk_xml_create_readinfo(&$inf)
{
    return igk_create_filterobject($inf, ["min" => 0, "max" => 10, "offset" => 0, "path" => "root", "pathinfo" => [], "current" => null, "start" => 0, "count" => 0, "objects" => [], "item" => 0, "bufferSize" => 4096]);
}
///<summary>helper - create render option</summary>
/**
 * helper: - create render option
 * @var IHtmlRenderOptions renderOptions render options
 */
function igk_xml_create_render_option()
{
    return HtmlRenderer::CreateRenderOptions();
}
///<summary></summary>
/**
 * 
 */
function igk_xml_create_to_node_settings()
{
    return (object)array(
        "storeAttribAsNode" => 0,
        "handleArray" => function ($k, $v, $n, $setting = null) {
            foreach ($v as $sk => $v) {
                $nv = $n->addXmlNode($k);
                igk_xml_to_node($v, $nv, $setting);
            }
            return 1;
        }
    );
}
///<summary></summary>
///<param name="v" default="1.0"></param>
///<param name="e" default="utf-8"></param>
/**
 * 
 * @param mixed $v 
 * @param mixed $e 
 */
function igk_xml_header($v = "1.0", $e = "utf-8", $standalone = "yes")
{
    $lf = "";
    if ($standalone != "yes")
        $lf = "standalone=\"{$standalone}\" ";
    return "<?xml version=\"$v\" encoding=\"$e\" {$lf}?>";
}
///<summary></summary>
///<param name="option"></param>
///<param name="tab"></param>
/**
 * 
 * @param mixed $option 
 * @param mixed $tab 
 */
function igk_xml_initialize($option, $tab)
{
    foreach ($tab as $k => $v) {
        if (!isset($option->$k)) {
            $option->$k = $v;
        }
    }
}
///<summary>determining if xml options is caching require</summary>
/**
 * determining if xml options is caching require
 */
function igk_xml_is_cachingrequired($options = null)
{
    return $options && ($options->Context ==  HtmlContext::Html) && $options->Cache;
}
///<summary>is mail option</summary>
///<param name="options"></param>
/**
 * is mail option
 * @param mixed $options 
 */
function igk_xml_is_mailoptions($options)
{
    if ($options && !isset($options->Context)) {
        igk_die("Try to send XML options without <b>Context</b> property");
    }
    return $options && ($options->Context == "mail");
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param mixed $n 
 */
function igk_xml_is_validname($n)
{
    return preg_match("/^[a-z_][a-z_0-9\-]*(:[a-z]+){0,1}$/i", $n);
}
///<summary>Represente igk_xml_obj_2_xml function</summary>
///<param name="node"></param>
///<param name="data"></param>
///<param name="attrib_style" default="false"></param>
/**
 * Represente igk_xml_obj_2_xml function
 * @param mixed $node 
 * @param mixed $data 
 * @param mixed $attrib_style 
 */
function igk_xml_obj_2_xml($node, $data, $attrib_style = false)
{
    $tbuild = [["t" => $node, "d" => $data]];
    while ($q = array_shift($tbuild)) {
        foreach ($q["d"] as $k => $v) {
            if ($attrib_style && !is_object($v) && !is_array($v)) {
                $q["t"][$k] = $v;
                continue;
            }
            $n = $q["t"]->add($k);
            if (is_string($v)) {
                $n->Content = $v;
            } else if (is_array($v)) {
                array_unshift($tbuild, ["t" => $n, "d" => $v]);
            }
        }
    }
}

///<summary></summary>
///<param name="s"></param>
///<param name="pos" ref="true"></param>
///<param name="outstring" default="null" ref="true"></param>
///<param name="securename" default="1"></param>
/**
 * 
 * @param mixed $s 
 * @param mixed $pos 
 * @param mixed $outstring 
 * @param mixed $securename 
 */
function igk_xml_read_attribute($s, &$pos = 0, &$outstring = null, $securename = 1)
{
    $ln = strlen($s);
    $m = 0;
    $n = "";
    $v = "";
    $tab = array();
    while ($pos < $ln) {
        $ch = $s[$pos];
        switch ($ch) {
            case "=":
                if ($m == 0) {
                    if (empty($n = trim($n))) {
                        throw new InvalidXmlReadException('Not a valid attribute', $pos);
                    }
                    $m = 1;
                    $v = $n;
                    $n = "";
                    $outstring .= " " . $v . "=";
                    if ($securename && !igk_xml_is_validname($v)) {
                        throw new InvalidXmlReadException("[" . $v . '] is not a valid xml\'s attribute name', $pos);
                    }
                }
                break;
            case "'":
            case "\"":
                if ($m != 1 || !empty($n))
                    throw new InvalidXmlReadException('Not a valid attribute value', $pos);
                $n = igk_str_read_brank($s, $pos, $ch, $ch);
                $outstring .= $n;
                $n = substr($n, 1, strlen($n) - 2);
                $tab[$v] = $n;
                $v =
                    $n = "";
                $m = 0;
                break;
            case " ":
                if ($m == 1) {
                    if (!empty($n = trim($n))) {
                        $tab[$v] = $n;
                        $outstring .= $n;
                        $v =
                            $n = "";
                        $m = 0;
                        continue 2;
                    }
                }
                $n .= $ch;
                break;
            case '>':
                if ($m == 0)
                    break 2;
                break;
            default:
                $n .= $ch;
                break;
        }
        $pos++;
    }
    if ($m == 1) {
        $tab[$v] = $n;
        $outstring .= $n;
    }
    return $tab;
}
///<summary></summary>
///<param name="s"></param>
///<param name="pos" ref="true"></param>
/**
 * 
 * @param mixed $s 
 * @param mixed $pos 
 */
function igk_xml_read_doctype($s, &$pos)
{
    $out = "";
    $brank = array();
    $depth = 0;
    $ln = strlen($s);
    while ($pos < $ln) {
        $ch = $s[$pos];
        switch ($ch) {
            case "[":
            case "(":
                $depth++;
                break;
            case "]":
            case ")":
                $depth--;
                break;
            case ">":
                if ($depth <= 0)
                    break 2;
                break;
        }
        $out .= $ch;
        $pos++;
    }
    return $out;
}


///<summary>used to read tagname</summary>
/**
 * used to read tagname
 */
function igk_xml_read_tagname($content, &$pos)
{
    $n = "";
    $ln = strlen($content);
    while (($pos < $ln) && ($ch = $content[$pos]) && ($ch != ">") && ($ch != " ")) {
        $n .= $ch;
        $pos++;
    }
    return $n;
}
///<summary>used to read xml</summary>
///<param name="path">xpath object</param>
///<param name="callback">the callback(index) used to read valid data at index</param>
/**
 * used to read xml
 * @param mixed $path xpath object
 * @param mixed $callback the callback(index) used to read valid data at index
 */
function igk_xml_read_xml($c, $path = null, &$inf = null, $callback = null)
{
    $inf = new StdClass();
    $inf->ln = strlen($c);
    $inf->content = $c;
    $inf->offset = 0;
    $inf->out = "";
    $path = ltrim($path, "/");
    $inf->path = $path;
    $g = $path != null ? explode("/", $path) : null;
    $o = "";
    $t = "0";
    $m = 0;
    $depth = 0;
    $skip_depth = 0;
    $item = 0;
    $start = 0;
    $pathlevel = count($g);
    while ($inf->offset < $inf->ln) {
        $ch = $inf->content[$inf->offset];
        switch ($ch) {
            case "<":
                $m = 1;
                break;
            case "?":
                if ($m == 1) {
                    $gp = strpos($inf->content, "?>", $inf->offset + 1);
                    if ($gp == false) {
                        throw new InvalidXmlReadException("?&gt; close not found");
                    }
                    $s = substr($inf->content, $inf->offset + 1, $gp - $inf->offset - 1);
                    if ($skip_depth == 0)
                        $o .= "<?" . $s . "?>";
                    $inf->offset += ($gp);
                    $m = 0;
                }
                break;
            case "/":
                if ($m == 1) {
                    if ($depth == 0) {
                        throw new InvalidXmlReadException("Invalid XmlReadException - No root found");
                    }
                    $inf->offset++;
                    $n = igk_xml_read_tagname($inf->content, $inf->offset);
                    if ($skip_depth > 0) {
                        $skip_depth--;
                    } else {
                        $o .= "</" . $n . ">";
                    }
                    $depth--;
                    $m = 0;
                }
                break;
            case "!":
                if ($m == 1) {
                    if (substr($inf->content, $inf->offset + 1, 2) == "--") {
                        $inf->offset += 3;
                        $gp = strpos($inf->content, "-->", $inf->offset);
                        if ($gp == false) {
                            $s = substr($inf->content, $inf->offset + 1);
                            $inf->offset = $inf->ln;
                        } else {
                            $s = substr($inf->content, $inf->offset, $gp - $inf->offset);
                            $inf->offset = $gp + 2;
                        }
                        if ($skip_depth == 0)
                            $o .= "<!--" . $s . "-->";
                        $m = 0;
                    } else if (substr($inf->content, $inf->offset + 1, 7) == "[CDATA[") {
                        $inf->offset += 8;
                        $gp = strpos($inf->content, "]]>", $inf->offset);
                        if ($gp == false) {
                            $s = substr($inf->content, $inf->offset);
                            $inf->offset = $inf->ln;
                        } else {
                            $s = substr($inf->content, $inf->offset, $gp - $inf->offset);
                            $inf->offset = $gp + 2;
                        }
                        if ($skip_depth == 0)
                            $o .= "<![CDATA[" . $s . "]]>";
                        $m = 0;
                    } else if (substr($inf->content, $inf->offset + 1, 8) == "DOCTYPE ") {
                        $inf->offset += 8;
                        igk_xml_read_doctype($inf->content, $inf->offset, null);
                    } else {
                        throw new InvalidXmlReadException("&lt;! not a valid specification");
                    }
                }
                break;
            default:
                if ($m == 1) {
                    $n = igk_xml_read_tagname($inf->content, $inf->offset);
                    igk_assert_die($n === null, "tagname is null");
                    if (($skip_depth > 0) || ($pathlevel > $depth) && ($g && ($g[$depth] != $n))) {
                        $skip_depth++;
                    }
                    if (($skip_depth == 0) && (($pathlevel - 1) == ($depth))) {
                        if ($callback) {
                            if (!$start) {
                                if ($callback($item)) {
                                    $start = 1;
                                } else
                                    $skip_depth = 1;
                            } else {
                                if (!$callback($item)) {
                                    for ($i = $depth - 1; $i >= 0; $i--) {
                                        $o .= "</" . $g[$i] . ">";
                                    }
                                    break 2;
                                }
                            }
                        }
                        $item++;
                    }
                    $empty = 0;
                    $attr = "";
                    igk_xml_read_attribute($inf->content, $inf->offset, $attr);
                    $empty = ($inf->content[$inf->offset - 1] == "/");
                    $gap = 0;
                    if ($skip_depth > 0) {
                        if ($empty) {
                            $skip_depth--;
                        } else {
                            $depth++;
                            $gap = 0;
                        }
                    } else {
                        $o .= "<" . $n;
                        $o .= $attr;
                        if ($empty) {
                            $o .= "/>";
                        } else {
                            $o .= ">";
                            $depth++;
                            $gap = 0;
                        }
                    }
                    $m = 0;
                } else {
                    $ggg = strpos($inf->content, "<", $inf->offset);
                    if ($ggg !== false) {
                        $s = trim(substr($inf->content, $inf->offset, $ggg - $inf->offset));
                        if ($skip_depth == 0) {
                            $o .= $s;
                        }
                        $inf->offset = $ggg - 1;
                    }
                }
                break;
        }
        $inf->offset++;
    }
    $inf->out = $o;
    return $inf->out;
}
///<summary>return a xml representation of the object</summary>
/**
 * return a xml representation of the object
 */
function igk_xml_render($name, $object, $setting = null)
{
    $tq = array((object)array("n" => $name, "t" => $object));
    $out = "";
    if (!$setting) {
        $setting = (object)["LF" => IGK_LF, "IndentChar" => "\t", "FormatText" => 0, "Depth" => 0, "xmlns" => null, "defaultKeyItem" => "item_", "contentText" => "Content"];
    }
    $tq[0]->indent = $setting->FormatText ? str_repeat($setting->IndentChar, $setting->Depth) : "";
    while ($q = array_pop($tq)) {
        $out .= $q->indent . "<" . $q->n;
        $child = 0;
        $tab = array();
        foreach ($q->t as $k => $v) {
            if ($k === $setting->contentText)
                continue;
            if (is_numeric($k)) {
                $k = $setting->defaultKeyItem . $k;
            }
            if (is_array($v) || is_object($v)) {
                $child = 1;
                $c = (object)array(
                    "n" => $k,
                    "t" => $v,
                    "p" => $q,
                    "indent" => $setting->FormatText ? $q->indent . $setting->IndentChar : ""
                );
                $q->last = $c;
                array_push($tab, $c);
            } else
                $out .= " " . $k . "=\"" . HtmlUtils::GetAttributeValue($v) . "\"";
        }
        $lf = $setting->FormatText ? $setting->LF : "";
        $c = !empty($setting->contentText) ? igk_getv($q->t, $setting->contentText) : "";
        if (!empty($c)) {
            $child = 1;
            $c = HtmlUtils::GetValue($c);
        }
        if (!$child)
            $out .= "/>" . $lf;
        else {
            if (count($tab) > 0)
                $tq = array_merge($tq, array_reverse($tab));
            $out .= ">" . $c . $lf;
            continue;
        }
        while ($q && isset($q->p)) {
            $l = igk_getv($q->p, "last");
            if ($l && ($l === $q)) {
                $out .= $q->p->indent . "</" . $q->p->n . ">" . $lf;
                $q = $q->p;
            } else
                break;
        }
    }
    return $out;
}
///<summary>Represente igk_xml_text function</summary>
///<param name="data"></param>
///<param name="version" default="1.0"></param>
///<param name="e" default="UTF-8"></param>
///<param name="standalone" default="yes"></param>
/**
 * Represente igk_xml_text function
 * @param mixed $data 
 * @param mixed $version 
 * @param mixed $e 
 * @param mixed $standalone 
 */
function igk_xml_text($data, $version = "1.0", $e = "UTF-8", $standalone = "yes")
{
    return igk_xml_header($version, $e, $standalone) . PHP_EOL . ltrim($data);
}
///<summary>convert a object stdClass object to core xml node object</summary>
/**
 * convert a object stdClass object to core xml node object
 */
function igk_xml_to_node($obj, $name = 'objResult', $setting = null)
{
    $dobj = function () {
        return (object)array("obj" => null, "node" => null);
    };
    $tn = igk_is_xmlnode($name) ? $name : igk_create_xmlnode($name);
    $cobj = $dobj();
    $cobj->node = $tn;
    $cobj->obj = $obj;
    $tab = array($cobj);
    $setting = $setting ?? igk_xml_create_to_node_settings();
    $handleArray = $setting->handleArray;
    while (igk_count($tab) > 0) {
        $tobj = array_shift($tab);
        $obj = $tobj->obj;
        $n = $tobj->node;
        foreach ($obj as $k => $v) {
            $a = is_array($v);
            if ($a || is_object($v)) {
                if ($a && $handleArray($k, $v, $n, $setting))
                    continue;
                $cobj = $dobj();
                $cobj->node = $n->addXmlNode($k);
                $cobj->obj = $v;
                array_push($tab, $cobj);
            } else {
                if ($setting->storeAttribAsNode)
                    $n->addNode($k)->Content = $v;
                else
                    $n[$k] = $v;
            }
        }
    }
    return $tn;
}
///<summary>convert an igk balafon xml node presentation to stdClass object</summary>
/**
 * convert an igk balafon xml node presentation to stdClass object
 */
function igk_xml_to_obj($n, $arraycallback = null)
{
    $obj = igk_createobj();
    $nobj = function () {
        return (object)array("c" => null, 'obj' => null);
    };
    if ($n && igk_reflection_class_extends($n, IGK_HTML_ITEMBASE_CLASS)) {
        $h = $nobj();
        $h->c = $n;
        $h->obj = $obj;
        $_vlist = array($h);
        $_clist = array();
        while (igk_count($_vlist) > 0) {
            $h = array_shift($_vlist);
            $n = $h->c;
            $cobj = $h->obj;
            foreach ($n->Attributes as $k => $v) {
                $cobj->$k = $v;
            }
            if ($n->HasChilds) {
                $_clist = array();
                foreach ($n->Childs as $k) {
                    $v_n = $k->TagName;
                    if (empty($v_n))
                        continue;
                    $j = igk_createobj();
                    if ($k->HasChilds) {
                        if (isset($cobj->$v_n)) {
                            $r = $cobj->$v_n;
                            if (!is_array($r))
                                $r = array($r);
                            $cobj->$v_n = $r;
                        } else
                            $cobj->$v_n = $j;
                        $g = $nobj();
                        $g->c = $k;
                        $g->obj = $j;
                        array_push($_vlist, $g);
                    } else {
                        $g = $nobj();
                        $g->c = $k;
                        $g->obj = $j;
                        array_push($_vlist, $g);
                        if (!isset($cobj->$v_n)) {
                            $cobj->$v_n = $j;
                        } else {
                            $vh = $cobj->$v_n;
                            if ($arraycallback) {
                                $vh = $arraycallback($v_n, $vh, $k->Content);
                            } else {
                                if (!is_array($vh)) {
                                    $vh = array($vh);
                                }
                                $vh[] = $j;
                            }
                            $cobj->$v_n = $vh;
                        }
                    }
                }
            } else {
            }
        }
    }
    return $obj;
}
///<summary></summary>
///<param name="t"></param>
/**
 * 
 * @param mixed $t 
 */
function igk_xml_type2str($t)
{
    $tab = array(
        XMLNodeType::NONE => "NONE",
        XMLNodeType::ELEMENT => "Element",
        XMLNodeType::PROCESSOR => "PROCESSOR",
        XMLNodeType::COMMENT => "COMMENT",
        XMLNodeType::ENDELEMENT => "ENDELEMENT",
        XMLNodeType::CDATA => "CDATA",
        XMLNodeType::TEXT => "TEXT",
        XMLNodeType::DOCTYPE => "DOCTYPE"
    );
    if (isset($tab[$t]))
        return $tab[$t];
    return "UNKNOW";
}
///<summary></summary>
///<param name="inf" ref="true"></param>
/**
 * 
 * @param mixed $inf 
 */
function igk_xml_unset_read_info(&$inf)
{
    if ($inf->count === null) {
        $inf->count = igk_count($inf->objects);
    }
    unset($inf->pathinfo);
    unset($inf->current);
}

///<summary>create xslt transform object</summary>
/**
 * create xslt transform object
 */
function igk_xml_xsl_transform($xml, $xslt, &$error = 0)
{
    if ($error !== 0) {
        libxml_use_internal_errors(true);
    }
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $xsl = new DOMDocument();
    $xsl->loadXML($xslt);
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl);
    $out = $proc->transformToXML($dom);
    $error = libxml_get_errors();
    return $out;
}
///<summary> create xsl transform node </summary>
/**
 *  create xsl transform node 
 */
function igk_xml_xsl_transformnode($root, $uri = null)
{
    $p = new \IGK\System\Html\XML\XmlProcessor("xml-stylesheet");
    $xml = new \IGK\System\Html\XML\XmlProcessor("xml");
    $xml["version"] = "1.0";
    $xml["encoding"] = "utf8";
    $h = igk_create_xmlnode($root);
    $b = igk_create_notagnode();
    $b->add($p);
    $b->add($h);
    $p["href"] = $uri;
    $p["type"] = "text/xsl";
    $b->root = $h;
    return $b;
}

if (!function_exists('igk_template_create_ctrl')) {

    /**
     * create and init application template controller
     * @return ?BaseController 
     */
    function igk_template_create_ctrl($n)
    {
        return null;
    }
}
