<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssUtils.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Css;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Css\CssThemeOptions;
use IGK\Css\CssThemeRenderer;
use IGK\Css\IGKCssColorHost;
use IGK\Helper\ArrayUtils;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGK\System\Http\CookieManager;
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
    /**
     * generate single theme value
     * @param BaseController $controller 
     * @param string $theme 
     * @return string|false 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */
    public static function GenCss(BaseController $controller, string $theme = CssThemeOptions::DEFAULT_THEME_NAME)
    {
        $opt = new CssThemeOptions;
        $opt->theme_name = $theme;
        $theme = new HtmlDocTheme(null, "temp", "temporary");
        $systheme = igk_app()->getDoc()->getSysTheme();
        // set options before bind style
        $theme->setRenderOptions($opt);
        igk_css_bind_sys_global_files($systheme);
        igk_css_load_theme($theme);
        $controller->bindCssStyle($theme, true);
        ob_start();
        echo "/* CSS theme */";
        echo implode("\n", [
            $systheme->get_css_def(true, true),
            $theme->get_css_def(true, true)
        ]);
        $r = ob_get_contents();
        ob_clean();
        $theme->setRenderOptions(null);
        return $r;
    }

    /**
     * get theme by selecting primary theme
     * @param BaseController $controller 
     * @param string $primaryTheme 
     * @return string|false 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */
    public static function GenCssWithThemeSupport(BaseController $controller, string $primaryTheme = CssThemeOptions::DEFAULT_THEME_NAME)
    {
        $systheme = igk_app()->getDoc()->getSysTheme();
        igk_css_bind_sys_global_files($systheme);
        $def = [];
        $def = array_merge($def, self::AppendDataTheme($controller, $primaryTheme));
        ob_start();
        echo "/* CSS theme */";
        echo implode(
            "\n",
            array_merge([
                $systheme->get_css_def(true, true),
            ], $def)
        );
        $r = ob_get_contents();
        ob_clean();
        // $theme->setRenderOptions(null);
        return $r;
    }
    /**
     * 
     * @param mixed $controller 
     * @param mixed $v_theme 
     * @param mixed $primaryTheme 
     * @return array 
     * @throws IGKException 
     */
    public static function AppendDataTheme(BaseController $controller, HtmlDocTheme $a_theme, string $primaryTheme = CssThemeOptions::DEFAULT_THEME_NAME)
    {
        if ($controller->getConfig('no_theme_support'))
            return;
        $tdef = ['light', 'dark'];
        if ($list = $controller->getConfig('theme_lists')) {
            if (is_string($list)) {
                $tdef = explode(',', $list);
            } else if (is_array($list)) {
                $tdef = $tdef;
            } else {
                return;
            }
        }
        $def = [];
        // $def[] = $theme->get_css_def(true, true);
        ArrayUtils::PrependAfterSearch($tdef, $primaryTheme);
        
        foreach ($tdef as $theme_name) {
            $opt = new CssThemeOptions;
            $opt->theme_name = $theme_name;
            $is_primaryTheme = $primaryTheme == $theme_name;
            if ($is_primaryTheme){
                $def[] = "\n/*theme: primary */\n".$a_theme->get_css_def(true, true);
            }
            // load specific attached theme options... 
            $v_theme = new HtmlDocTheme(null, "temp", "temporary");
            // set options before bind style
            $v_theme->setRenderOptions($opt);
            // igk_css_load_theme($v_theme);
            $controller->bindCssStyle($v_theme, true);
            $g = $v_theme->getDef();
            $tab = $g->getAttributes();
            $lk = "html[data-theme=$theme_name] ";
            if ($tab) {
                array_map(function ($v, $k) use (& $g, $is_primaryTheme, $lk) {
                    $v_ev = false;
                    // + | ignore case 
                    // + | value is empty or k alreay content lk theme or prefix value contain [litteral] to evaluate
                    $is_empty = empty($v);
                    $theme_def = strpos($k, 'html[data-theme=') !== false;
                    $need_eval = !$is_empty && preg_match(IGK_CSS_TREAT_REGEX, $v);

                    if ($is_empty || $theme_def || $need_eval) {
                        if (!$theme_def && $need_eval){
                            $key = self::_prependThemePreKeyToCssSelector($k, $lk);
                            $g[$k] = null;        
                            $g[$key] = $v;        
                        }
                        return null;
                    }
                    $key = self::_prependThemePreKeyToCssSelector($k, $lk);
                    $g[$key] = $v;
                    //if (!$is_primaryTheme) {
                        $g[$k] = null;
                    //}
                }, $tab, array_keys($tab));
            }
            // igk_wln_e("theme name ", $theme_name);
            $def[] = "\n/*theme:".$theme_name."*/\n".$v_theme->get_css_def(true, true);
        }
        return $def;
    }
    /**
     * prefix each selector with theme pre keys. 
     * @param mixed $tab 
     * @param mixed $keys 
     * @return string 
     */
    private static function _prependThemePreKeyToCssSelector($tab, $keys)
    {
        $lk = explode(",", $tab);
        $lk = implode(",", array_map(function ($a) use ($keys) {
            $a = ltrim($a);
            // for :root trim space
            if (strlen($a) > 0 && ($a[0] == ':')) {
                $keys = trim($keys);
            }
            return $keys . $a;
        }, $lk));
        return $lk;
    }
    public static function InitSysGlobal(\IGKHtmlDoc $doc)
    {
        $clear = 0;
        $sys = $doc->getSysTheme();
        if (!$sys->getinitGlobal()) {
            $sys->initGlobalDefinition();
            $clear = 1;
            if (!defined("IGK_FORCSS")) {
                register_shutdown_function(function () use ($sys) {
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
    public static function InitBindingCssFile(
        BaseController $ctrl,
        //  \IGKHtmlDoc $document,  
        HtmlDocTheme $theme,
        string $file,
        bool $cssRendering,
        bool $temp = false,
        bool $raiseHook = true
    ) {
        if (is_file($file)) {
            if (!$cssRendering) {
                igk_css_reg_global_style_file($file, $theme, $ctrl, $temp);
            } else {
                igk_css_bind_file($theme, $ctrl, $file);
            }
            if ($raiseHook) {
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
            if (!$sys->getinitGlobal()) {
                $sys->initGlobalDefinition();
                $clear = 1;
            }
            foreach ($g as $v) {
                igkOb::Start();
                igk_css_bind_file($bvtheme, null, igk_io_expand_path($v->file));
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
    public static function AppendInlineStyle(string $file)
    {
        $ctrl = igk_get_current_base_ctrl();
        if ($doc = ViewHelper::CurrentDocument()) {
            $doc->getTheme()->addInlineStyle($ctrl, $file);
        }
    }

    /**
     * 
     * @param string $file 
     * @param mixed $ctrl 
     * @param mixed $theme 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function GetFileContent(string $file, $ctrl, $theme)
    {
        self::Include($file, $ctrl, $theme);
        return $theme->getDef();
    }
    /**
     * include binding files
     * @param string $file 
     * @param ?BaseController $ctrl 
     * @param HtmlDocTheme $theme 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function Include(
        string $file,
        ?BaseController $ctrl = null,
        HtmlDocTheme  $theme = null,
        ?string $theme_name = null
    ) {
        $context = \IGK\Css\CSSContext::Init($ctrl, $theme);
        require_once __DIR__ . "/theme_functions.php";

        $xsm_screen = $theme->getMedia(HtmlDocThemeMediaType::XSM_MEDIA);
        $sm_screen = $theme->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
        $lg_screen = $theme->getMedia(HtmlDocThemeMediaType::LG_MEDIA);
        $xlg_screen = $theme->getMedia(HtmlDocThemeMediaType::XLG_MEDIA);
        $xxlg_screen = $theme->getMedia(HtmlDocThemeMediaType::XXLG_MEDIA);
        $PTR = $theme->getPrintMedia();
        $css_m = "";
        if ($ctrl) {
            $n = "";
            if (is_object($ctrl)) {
                $n = $ctrl->getName();
            } else
                $n = $ctrl;
            $css_m = $n ? "." . strtolower(igk_css_str2class_name($n)) : '';
            // in case need to  register component auto load component
            $ctrl::register_autoload();
            unset($n);
        }
        $def = $theme->def;
        $cltab = &$theme->getCl();
        $cl = IGKCssColorHost::Create($cltab);
        $prop = &$theme->getProperties();
        $referer = igk_server()->get("HTTP_REFERER", "igk://system");
        igk_environment()->push(IGKEnvironmentConstants::CSS_UTIL_ARGS, get_defined_vars());
        $render_options = $theme->getRenderOptions();
        if (is_null($render_options)) {
            $render_options = new CssThemeOptions;
            $render_options->theme_name = igk_getr(
                "theme_name",
                CssSession::getInstance()->theme_name ??
                    CookieManager::getInstance()->get('theme_name')
                    ?? CssThemeOptions::DEFAULT_THEME_NAME
            );
            $theme->setRenderOptions($render_options);
        } else if (is_null($theme_name)){
            $theme_name = $render_options->theme_name;
        } 
        igk_include_if_exists(
            dirname($file) . "/themes/" . $theme->getRenderOptions()->theme_name . ".theme.pcss",
            get_defined_vars()
        ); 
        $root = [];
        $theme->setRootReference($root);
        include($file);

        // if (strstr($file, 'config.pcss')){
        //     igk_debug(1);
        // igk_wln_e(__FILE__.":".__LINE__, 
        // $file,
        // $theme === igk_app()->getDoc()->getSysTheme(), $cl,   $cl['configBackgroundColor'], 
        // $theme->get_css_def(), $cltab, $theme->getCl());
        // }

        igk_environment()->pop(IGKEnvironmentConstants::CSS_UTIL_ARGS);
        // $cltab = &$theme->getCl();
        // $cl = IGKCssColorHost::Create($cltab);
        if (isset($root) && is_array($root)) {
            $v_root = igk_getv($def, ":root", "");
            $v_root = implode(";", array_map(
                function ($a, $b) {
                    igk_set_env_keys("sys://css/vars", $b, $a);
                    return $b . ":" . $a;
                },
                $root,
                array_keys($root)
            ));
            $def[":root"] = $v_root;
            unset($v_root);
        }
    }
}
