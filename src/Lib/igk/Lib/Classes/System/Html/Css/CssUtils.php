<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssUtils.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Css;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Css\IGKCssColorHost;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGKEnvironmentConstants;
use IGKEvents;
use IGKException;
use IGKHtmlDoc;
use IGKOb;
use ReflectionException;

require_once(IGK_LIB_CLASSES_DIR . "/Css/IGKCssColorHost.php");

/**
 * utility function 
 * @package IGK\System\Html\Css
 */
abstract class CssUtils
{
    public static function InitSysGlobal(\IGKHtmlDoc $doc){
        $clear = 0;
        $sys = $doc->getSysTheme();
        if (!$sys->getinitGlobal()){
            $sys->initSysGlobal(); 
            $clear = 1;
            if (!defined("IGK_FORCSS")){
                register_shutdown_function(function()use($sys){
                    $sys->resetSysGlobal();
                });
            }
        } 
        return $clear;
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param IGKHtmlDoc $document 
     * @param string $file 
     * @param bool $cssRendering direct redering 
     * @param bool $temp 
     * @param bool $raiseHook 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public static function InitBindingCssFile(BaseController $ctrl, \IGKHtmlDoc $document,  string $file, 
        bool $cssRendering,
        bool $temp = false,
        bool $raiseHook=true)
    {   
        if (is_file($file)) {
            // if (!defined("IGK_FORCSS")) { 
            if (!$cssRendering) { 
                // igk_wln("binding ...".$temp);
                // igk_css_reg_global_tempfile($file, $document->getTheme(), $ctrl, $temp);
                igk_css_reg_global_style_file($file, $document->getTheme(), $ctrl, $temp);// $document->getSysTheme(), $ctrl, $temp);
            } else { 
                igk_css_bind_file($document, $ctrl, $file);
            }
            if ($raiseHook){
                igk_hook(IGKEvents::HOOK_BIND_CTRL_CSS, ["sender" => $ctrl, "type" => "css"]);
            }
        }
    }

    /**
     * get inline style rendering
     * @param \IGKHtmlDoc $doc 
     * @return string|null 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws Exception 
     */
    public static function GetInlineStyleRendering($doc, bool $themeexport)
    {
        
        $bvtheme = new HtmlDocTheme($doc, "temp://files", false);
        $out = "";
        $g = $doc->getTheme()->getInlineStyle(true);          
        // igk_wln_e("inline style renderegin ??? ", $doc->getParam('change'), $g);
        // igk_wln("inline rendering", $g);
        // + | reset parameter        
        $sys = $doc->getSysTheme();
        $clear = 0;
        if ($g) {
            if (!$sys->getinitGlobal()){
                $sys->initSysGlobal();
                $clear = 1;
            } 
            foreach ($g as $v) {
                igkOb::Start();
                igk_css_bind_file($v->host, igk_io_expand_path($v->file), $bvtheme);
                $m = igk_css_treat(igkOb::Content(), $themeexport, $sys, $sys);
                igkOb::Clear();
                if (!empty($m)) {
                    $out .= $m;
                }
            }  
        }
        $o = "";
        if (!empty($out)) {
            $o .= $out;
        }
        $o .= $bvtheme->get_css_def(false, false, null, $doc);
        if ($clear)
            $sys->resetSysGlobal();
            
        if (!empty($o)) {
            $s = igk_create_node("style");
            $s["id"] = "tempsp";
            $s->Content = $o;
            return $s->render();
        }
        return null;
    }

    /**
     * helper append inline style
     * @param string #Parameter#cfa1602d 
     * @param IGK\System\Html\Css\file #Parameter#cea15e9a 
     * @return void 
     * @throws IGKException 
     */
    public static function AppendInlineStyle(string $file){
        $ctrl = igk_get_current_base_ctrl();
        if ($doc = ViewHelper::CurrentDocument()){ 
           $doc->getTheme()->addInlineStyle($ctrl, $file); 
        }
    }


    public static function GetFileContent(string $file, $ctrl, $theme){
        self::Include($file, $ctrl, $theme);
        return $theme->getDef();
    }
    public static function Include($file, $ctrl, $theme){
        $context = \IGK\Css\IGKCssContext::Init($ctrl, $theme);
        require_once __DIR__."/theme_functions.php";

        $xsm_screen = $theme->get_media(HtmlDocThemeMediaType::XSM_MEDIA);
        $sm_screen = $theme->get_media(HtmlDocThemeMediaType::SM_MEDIA);
        $lg_screen = $theme->get_media(HtmlDocThemeMediaType::LG_MEDIA);
        $xlg_screen = $theme->get_media(HtmlDocThemeMediaType::XLG_MEDIA);
        $xxlg_screen = $theme->get_media(HtmlDocThemeMediaType::XXLG_MEDIA);
        $PTR = $theme->getPrintMedia();
        $css_m = "";
        if ($ctrl) {
            $n = "";
            if (is_object($ctrl)) {
                $n = $ctrl->getName();
            } else
                $n = $ctrl;
            $css_m = $n ? "." . strtolower(igk_css_str2class_name($n)) : '';
            $ctrl::register_autoload();
            unset($n);
        }
        $def = $theme->def;
        $cltab = & $theme->getCl();
        $cl = IGKCssColorHost::Create($cltab); 
        $prop = &$theme->getProperties();
        $referer = igk_server()->get("HTTP_REFERER", "igk://system");
        igk_environment()->push(IGKEnvironmentConstants::CSS_UTIL_ARGS, get_defined_vars());
        include($file);
        igk_environment()->pop(IGKEnvironmentConstants::CSS_UTIL_ARGS);

        $cltab = & $theme->getCl();
        $cl = IGKCssColorHost::Create($cltab);
        if (isset($root) && is_array($root)) {
            $v_root = igk_getv($def, ":root" , "");
            foreach ($root as $k => $v) {
                if (empty($v))
                    continue;
                $v_root .= $k . ":" . $v . ";";
                igk_set_env_keys("sys://css/vars", $k, $v);
            }
            $def[":root"] = $v_root;
        }
    }
}
