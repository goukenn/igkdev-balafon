<?php

namespace IGK\System\Html\Css;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Css\IGKCssColorHost;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDocThemeMediaType; 
use IGKEvents;
use IGKException;
use IGKOb;

require_once(IGK_LIB_CLASSES_DIR . "/Css/IGKCssColorHost.php");

/**
 * utility function 
 * @package IGK\System\Html\Css
 */
abstract class CssUtils
{

    /**
     * 
     * @param null|BaseController $ctrl 
     * @return void 
     * @throws IGKException 
     */
    public static function InitBindingCssFile(BaseController $ctrl, \IGKHtmlDoc $document,  string $file,  bool $temp = false)
    {   
        if (file_exists($file) && !igk_is_ajx_demand()) {
            if (!defined("IGK_FORCSS")) { 
                igk_css_reg_global_tempfile($file, $document->getTheme(), $ctrl, $temp);
            } else {
                igk_css_bind_file($document, $ctrl, $file);
            }
            igk_hook(IGKEvents::HOOK_BIND_CTRL_CSS, ["sender" => $ctrl, "type" => "css"]);
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
    public static function GetInlineStyleRendering($doc)
    {
        
        $bvtheme = new HtmlDocTheme($doc, "temp://files", false);
        $out = "";
        $g = $doc->getTheme()->getInlineStyle(true);          
        // igk_wln_e("inline style renderegin ??? ", $doc->getParam('change'), $g);
        // + | igk_wln("inline rendering", $g);
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
                $m = igk_css_treat(igkOb::Content(), $sys, $sys);
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
        $o .= $bvtheme->get_css_def(false, false, $doc);
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
            unset($n);
        }
        $def = $theme->def;
        $cltab = & $theme->getCl();
        $cl = IGKCssColorHost::Create($cltab); 
        $prop = &$theme->getProperties();
        $referer = igk_server()->get("HTTP_REFERER", "igk://system");
        include($file);
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
