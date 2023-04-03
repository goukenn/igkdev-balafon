<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssUtils.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Css;
use IGK\IGlobalFunction;
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
use IGK\System\Html\Css\CssEnvironment;
use IGK\System\Html\Css\CssMapTheme;
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
     * get class values 
     * @param string $haystack 
     * @return array 
     */
    public static function GetClassValues(string $haystack):array{
        $tq = array_filter(explode(' ', $haystack));
        $r = [];
        while((count($tq)>0)){
            $value = array_shift($tq);
            if (($op = ltrim($value, " +-")) && ($op != $value)){
                $cp = substr($value, 0, -strlen($op)+strlen($value));
                $value = $op;            
                $op= $cp;
            }else{
                $op='';
            }
            $r[] = [$value, $op];
        }
        return $r;

    }
    /**
     * get initialized class 
     * @param string $tagname 
     * @param null|string $default 
     * @return void 
     */
    public static function InitClass(string $tagname, ?string $default=null){
        return implode(" ", array_filter([CssEnvironment::GetInitClass($tagname), $default]));
    }
    public static function GetControllerSelectorClassNameFromRegisterURI(BaseController $controller, ?string $ruri=null):?string{
        if (!empty(
            $ruri
        )){
            $chain = '';
            $v_closure = function($a)use (& $chain){
                if (!empty($chain)){
                    $a ='/'.$a;
                }
                $chain = igk_css_str2class_name($chain.$a);                
                return $chain;
            };
            $ruri = implode (" ", array_map($v_closure, explode('/', rtrim($ruri,'/'))));
        }
        return $ruri;
    }
    /**
     * inject balafon style content
     * @param mixed $doc 
     * @param mixed $file 
     * @return true|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */
    public static function InjectStyleContent($doc, string $file){
        
        $style = $doc->getEnvParam($key = '://inject_style') ?? [];
        if (isset($style[$file])){
            return true;
        }
        $doc->getHead()->style()->Content 
			= CssUtils::GetInjectableStyleFromFileDefinition($file);
        $style[$file] = 1;
        $doc->setEnvParam($key, $style);
    }
 
    public static function InitSysTheme($vsystheme){
        $vsystheme->def->Clear();
        $d = $vsystheme->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
        $d = $vsystheme->getMedia(HtmlDocThemeMediaType::XSM_MEDIA);
        $d = $vsystheme->reg_media("(max-width:700px)");

        $v_cache_file = igk_dir(IGK_LIB_DIR . "/.Cache/.css.cache");
        if (file_exists($v_cache_file)) {
            igk_css_include_cache($v_cache_file, $lfile);
        } else {
            $lfile = array_filter(explode(";", $vsystheme->getDef()->getFiles() ?? ""));
            $options = null;
            if (IGlobalFunction::Exists("igk_global_init_material")) {
                $options = (object)["file" => &$lfile];
                IGlobalFunction::igk_global_init_material($options);
            }

            if (!$options || !igk_getv($options, "handle")) {
                igk_hook(IGKEvents::HOOK_INIT_GLOBAL_MATERIAL_FILTER, [&$lfile]);

                if (count($lfile) == 0) {
                    $lfile[] = igk_dir(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/global.pcss");
                    $lfile[] = igk_get_env("sys://css/file/global_color", igk_dir(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/igk_css_colors.phtml"));
                    $lfile[] = igk_get_env("sys://css/file/global_template", igk_dir(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/igk_css_template.phtml"));
                }
            }
        }
        $g = implode(";", array_unique($lfile));
        $g = str_replace(IGK_LIB_DIR, "%lib%", $g);
        $vsystheme->def->setFiles($g);
    }
    /**
     * Get Injectable Style from file definition
     * @param string $file pcss source file
     * @param null|BaseController $ctrl 
     * @param null|HtmlDocTheme $parent 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */
    public static function GetInjectableStyleFromFileDefinition(string $file, ?BaseController $ctrl =null ,
         ?HtmlDocTheme $parent = null,  & $css=null, $autoinit=true){
        $ctrl = $ctrl ?? ViewHelper::CurrentCtrl() ?? die('must provide a controller');
        $doc = IGKHtmlDoc::CreateDocument('temp');
        $th = new HtmlDocTheme($doc, 'temp-style');
        $th->parent = $parent ?? $doc->getSysTheme(); 
        $autoinit && $th->parent->initGlobalDefinition();
        $css = CssUtils::GetFileContent($file, $ctrl, $th );
        $src = $th->get_css_def(true,false);
        $autoinit && $th->parent->resetSysGlobal();
        return $src;
    } 
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
        $def = array_merge($def, self::AppendDataTheme($controller, $systheme, $primaryTheme));
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
    public static function AppendDataTheme(BaseController $controller, HtmlDocTheme $a_theme, 
    string $primaryTheme = CssThemeOptions::DEFAULT_THEME_NAME, bool $theme_export = false)
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

        // $def[] = "/* theme {$primaryTheme} */"; 
        
        foreach ($tdef as $theme_name) {
            $opt = new CssThemeOptions;
            $opt->theme_name = $theme_name;
            $is_primaryTheme = $primaryTheme == $theme_name;
            if ($is_primaryTheme){
                $def[] = "\n/* theme: primary */\n".$a_theme->get_css_def(true, $theme_export);
                //continue;
            }
            // load specific attached theme options... 
            $v_theme = new HtmlDocTheme(null, "temp", "temporary");
            // set options before bind style
            $v_theme->setRenderOptions($opt);
            // igk_css_load_theme($v_theme);
            $controller->bindCssStyle($v_theme, true);
            $g = $v_theme->getDef();
            $tab = $g->getAttributes();
            $lk = "html[data-theme='{$theme_name}'] ";
            if ($tab) {
                array_map(function ($v, $k) use (& $g, $lk) {
                    self::TreatCssDefinition($v, $k, $g, false, $lk);
                     
                }, $tab, array_keys($tab));
            }

            if (!$is_primaryTheme && ($medias = $v_theme->getMedias())){
                while(count($medias)>0){
                    if (!($m = array_shift($medias))){
                        continue;
                    }
                    $g = new CssMapTheme($m, $is_primaryTheme, $lk);
                    $g->map();
                }
                //igk_wln_e($medias);
            }

            // igk_wln_e("theme name ", $theme_name);
            $def[] = "\n/* theme: ".$theme_name." */\n".$v_theme->get_css_def(true, true);
        }
        return $def;
    }

    /**
     * treat css detection 
     */
    public static function TreatCssDefinition($v, $k, & $g, $is_primaryTheme, $lk){
        $v_ev = false;
        // + | ignore case 
        // + | value is empty or k alreay content lk theme or prefix value contain [litteral] to evaluate
        $is_empty = empty($v);
        $theme_def = strpos($k, 'html[data-theme=') !== false;
        $need_eval = !$is_empty && preg_match(IGK_CSS_TREAT_REGEX, $v);

        if ($is_empty || $theme_def || $need_eval) {
            if (!$theme_def && $need_eval){
                $g[$k] = null;     
                if (!$is_primaryTheme){
                    $key = self::_prependThemePreKeyToCssSelector($k, $lk);
                    $g[$key] = $v;        
                }
            }
            return null;
        }
        $key = self::_prependThemePreKeyToCssSelector($k, $lk);
        $g[$key] = null;//$v;//"background-color:red";//null;
        if (!$is_primaryTheme) {
            $g[$k] = null;
        }
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
     * include pcss binding files
     * @param string $file file to incluce
     * @param ?BaseController $ctrl controller
     * @param HtmlDocTheme $theme theme to use
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
        $prop = & $theme->getProperties();
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
        $args = get_defined_vars(); //  $file, $theme->getRenderOptions()->theme_name);
        self::BindThemeFile($file, $theme->getRenderOptions()->theme_name, $args);
      

        // igk_include_if_exists(
        //     dirname($file) . "/themes/" . $theme->getRenderOptions()->theme_name . ".theme.pcss",
        //     $args
        // ); 
        $root = [];
        $theme->setRootReference($root);
        include($file);

        

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
    private static function BindThemeFile(string $file, $theme_name, $args){
        $rf = igk_io_basenamewithoutext($file);
      
        foreach([$rf,""] as $tf ){
           $f = dirname($file) . "/themes/" .$rf. $theme_name .".theme.pcss";
           if (file_exists($f)){
            igk_include_if_exists(
                    $f,
                    $args
                );    
            }
            break;
        }
    }
}
