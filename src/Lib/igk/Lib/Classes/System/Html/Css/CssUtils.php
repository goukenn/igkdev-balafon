<?php

namespace IGK\System\Html\Css;

use Exception;
use IGK\Controllers\BaseController;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGKEvents;
use IGKException;
use IGKOb;

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
    public static function InitBindingCssFile(BaseController $ctrl, string $file,  bool $temp = false)
    {  
        if (file_exists($file) && !igk_is_ajx_demand()) {
            if (!defined("IGK_FORCSS")) {
                $doc = $ctrl->getCurrentDoc() ?? igk_app()->getDoc();
                igk_css_reg_global_tempfile($file, $doc->getTheme(), $ctrl, $temp);
            } else {
                igk_css_bind_file($ctrl, $file);
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
        $rendering_key = HtmlDocTheme::INLINE_STYLE_KEY;
        $bvtheme = new HtmlDocTheme($doc, "temp://files", false);
        $out = "";
        $g = $doc->getTheme()->getParam($rendering_key);

        // + | igk_wln("inline rendering", $g);
        // + | reset parameter       
        $doc->getTheme()->setParam($rendering_key, null);
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
}
