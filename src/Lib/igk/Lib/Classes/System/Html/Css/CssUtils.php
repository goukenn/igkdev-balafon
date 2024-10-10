<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssUtils.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Css;

use IGK\IGlobalFunction;
use Exception;
use IGK\Controllers\ApplicationModuleController;
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
use IGK\System\IO\Path;
use IGKEnvironmentConstants;
use IGKEvents;
use IGKException;
use IGKHtmlDoc;
use IGKMedia;
use IGKOb;
use ReflectionException;

require_once(IGK_LIB_CLASSES_DIR . "/Css/IGKCssColorHost.php");

/**
 * utility function 
 * @package IGK\System\Html\Css
 */
abstract class CssUtils
{
    private static $sm_treated_colors = [];
    public static function &GetTreatedColors()
    {
        if (is_null(self::$sm_treated_colors)) {
            self::$sm_treated_colors = [];
        }
        return self::$sm_treated_colors;
    }
    public function ClearTreatColors()
    {
        self::$sm_treated_colors = [];
    }
    /**
     * treat media condition 
     * @param string $k 
     * @return string 
     */
    public static function TreatMediaCondition(string $k)
    {
        if (!preg_match("/\(.*\)/", $k)) {
            $rg = trim(preg_replace('/\b(not|only|print|screen|speech|and)\b/', '', $k));
            if (!empty($rg))
                $k = "(" . $k . ")";
        }
        return $k;
    }
    /**
     * bind core files
     * @param HtmlDocTheme $theme 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function BindCoreFile(HtmlDocTheme $theme)
    {
        $theme->bindFile(
            IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/global.pcss"
        );
        $theme->bindFile(
            IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/igk_css_template.phtml"
        );
    }
    /**
     * get class values 
     * @param string $haystack 
     * @return array 
     */
    public static function GetClassValues(string $haystack): array
    {
        $tq = array_filter(explode(' ', $haystack));
        $r = [];
        while ((count($tq) > 0)) {
            $value = array_shift($tq);
            if (($op = ltrim($value, " +-")) && ($op != $value)) {
                $cp = substr($value, 0, -strlen($op) + strlen($value));
                $value = $op;
                $op = $cp;
            } else {
                $op = '';
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
    public static function InitClass(string $tagname, ?string $default = null)
    {
        return implode(" ", array_filter([CssEnvironment::GetInitClass($tagname), $default]));
    }
    public static function GetControllerSelectorClassNameFromRegisterURI(BaseController $controller, ?string $ruri = null): ?string
    {
        if (!empty($ruri)) {
            $chain = '';
            $v_closure = function ($a) use (&$chain) {
                if (!empty($chain)) {
                    $a = '/' . $a;
                }
                $chain = igk_css_str2class_name($chain . $a);
                return $chain;
            };
            $ruri = implode(" ", array_map($v_closure, explode('/', rtrim($ruri, '/'))));
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
    public static function InjectStyleContent($doc, string $file)
    {

        $style = $doc->getEnvParam($key = '://inject_style') ?? [];
        if (isset($style[$file])) {
            return true;
        }
        $doc->getHead()->style()->Content
            = CssUtils::GetInjectableStyleFromFileDefinition($file);
        $style[$file] = 1;
        $doc->setEnvParam($key, $style);
    }

    public static function InitSysTheme($vsystheme)
    {
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
    public static function GetInjectableStyleFromFileDefinition(
        string $file,
        ?BaseController $ctrl = null,
        ?HtmlDocTheme $parent = null,
        &$css = null,
        $autoinit = true
    ) {
        $ctrl = $ctrl ?? ViewHelper::CurrentCtrl() ?? die('must provide a controller');
        $doc = IGKHtmlDoc::CreateDocument('temp');
        $th = new HtmlDocTheme($doc, 'temp-style');
        $th->parent = $parent ?? $doc->getSysTheme();
        $autoinit && $th->parent->initGlobalDefinition();
        $css = CssUtils::GetFileContent($file, $ctrl, $th);
        $src = $th->get_css_def(true, false);
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
    public static function GenCss(BaseController $controller, string $theme = CssThemeOptions::DEFAULT_THEME_NAME, bool $embedresource = false, ?string $prefix = '')
    {
        $opt = new CssThemeOptions;
        $opt->theme_name = $theme;
        $theme = new HtmlDocTheme(null, "temp", "temporary");
        $systheme = igk_app()->getDoc()->getSysTheme();
        // set options before bind style
        $theme->setRenderOptions($opt);
        $theme->prefix = $prefix;
        ob_start();
        igk_css_bind_sys_global_files($systheme);
        igk_css_load_theme($theme);
        $controller->bindCssStyle($theme, true);
        echo "/* CSS theme */";
        $resourceResolver = null;
        if ($embedresource) {
            $resourceResolver = new EmbedResourceResolver();
        }
        $list = [$systheme, $theme];
        $imports = [];
        $sb= '';
        $ch = '';
        while(count($list)>0){
            $q = array_shift($list);
            $imports = array_merge($q->getImports() ?? [], $imports);
            $q->noHeader = true; 
            $sb .= $ch.$q->get_css_def(true, true, $resourceResolver);
            $q->noHeader = false;
            $ch="\n";
        }

        // echo implode("\n", [
        //     $systheme->get_css_def(true, true, $resourceResolver),
        //     $theme->get_css_def(true, true, $resourceResolver)
        // ]);
        if ($imports){
            echo self::RenderImport($imports); 
        }
        echo $sb;
        $r = ob_get_contents();
        ob_clean();
        $theme->setRenderOptions(null);
        return $r;
    }
    /**
     * 
     * @param array $imports 
     * @return string 
     */
    static function RenderImport(array $imports){
        return implode(";\n", array_map(function($s){
                return sprintf('@import "%s"', $s);
            }, $imports)).";"; 
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
        return $r;
    }

    /**
     * helper use to clone media
     * @param array $medias 
     * @return array<array-key, \IGKMedia> 
     */
    public static function CloneMedia(array $medias)
    {
        return array_map(function ($i) {
            return IGKMedia::Clone($i);
        }, $medias);
    }

    /**
     * render primary theme with definied colors
     * @param mixed $a_theme 
     * @param array|null $colors 
     * @param bool $minfile 
     * @param bool $theme_export 
     * @return mixed 
     */
    public static function RenderPrimaryTheme(HtmlDocTheme $a_theme, array $colors = null, $minfile = false, $theme_export = false)
    {
        $s = '';
        $bck = $a_theme->getCl();
        if ($colors) {
            $a_theme->setColors(array_merge($colors ?? [], $bck));
        }
        $s = $a_theme->get_css_def($minfile, $theme_export);
        $a_theme->setColors($bck);

        return $s;
    }
    /**
     * 
     * @param mixed $controller 
     * @param mixed $v_theme 
     * @param mixed $primaryTheme 
     * @return array 
     * @throws IGKException 
     */
    public static function AppendDataTheme(
        BaseController $controller,
        HtmlDocTheme $a_theme,
        string $primaryTheme = CssThemeOptions::DEFAULT_THEME_NAME,
        bool $theme_export = false
    ) {
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
        $systheme = igk_app()->getDoc()->getSysTheme();
        $minfile = true;
        $tab = $a_theme->getdef()->getAttributes();
        $medias = $a_theme->getMedias();
        //  ob_end_clean();
        $v_render_primary = false;

        $opt = new CssThemeOptions;
        $opt->skips = ['rules', 'fonts'];

        foreach ($tdef as $theme_name) {
            $s_medias = self::CloneMedia($medias);
            $opt->theme_name = $theme_name;
            $is_primaryTheme =  $primaryTheme == $theme_name;
            $colors = $a_theme->getThemeColorsByName($theme_name);

            if (!$v_render_primary && $is_primaryTheme) {
                $s = self::RenderPrimaryTheme($a_theme, $colors, true, $theme_export);

                // bind primary color  

                array_unshift($def, implode("\n", [
                    "\n/* theme: primary */",
                    $s, // $a_theme->get_css_def(true, $theme_export)
                ]));

                $v_render_primary = true;
                //continue;
            }

            // load specific attached theme options... 
            $v_theme = new HtmlDocTheme(null, "temp", "temporary");
            $v_theme->replaceMediaList($s_medias);
            // + | store theming color before binding so no need to override color in target definition 
            if ($colors) {
                $v_theme->setColors($colors);
            }
            // + | set options before bind style
            $v_theme->setRenderOptions($opt);
            // + | load bind style with theme 
            $controller->bindCssStyle($v_theme, true);
            // } 
            // render and bind media
            self::MapMediaCssTheme(
                $v_theme,
                $theme_name,
                $tab,
                $s_medias,
                false
            );
            // + | --------------------------------------------------------------------
            // + | replace media 
            // + |

            //$v_theme->replaceMediaList($t_medias);
            $s = $v_theme->get_css_def(true, true);
            if (!empty($s)) {
                $def[] = "\n/* theme: " . $theme_name . " */\n" . $s;
            }
        }
        return $def;
    }
    /**
     * 
     * @param string $lk 
     * @param array $tab 
     * @param mixed $g 
     * @param mixed $source_defs 
     * @return void 
     */
    public static function MapThemeDefinition(string $lk, array $tab, &$g, &$source_defs = null)
    {
        array_map(function ($v, $k) use (&$g, $lk, &$source_defs) {
            //remove brank definitions  
            $v = CssUtils::RemoveNoTransformPropertyStyle($v);
            if (empty($v)) {
                // + | --------------------------------------------------------------------
                // + | no property found remove from global list
                // + |

                $g[$k] = null;
                return;
            }
            CssUtils::TreatCssDefinition($v, $k, $g, false, $lk, $source_defs);
        }, $tab, array_keys($tab));
    }
    /**
     * map media theme 
     */
    public static function MapMediaCssTheme(
        $g,
        string $theme_name,
        $tab,
        $medias,
        bool $is_primary_theme
    ) {
        $lk = sprintf(CssConstants::THEME_SELECTOR_FORMAT, $theme_name);
        if ($tab) {
            self::MapThemeDefinition($lk, $tab, $g);
        }
        self::MapTheme($medias, $is_primary_theme, $lk, true);
    }

    /**
     * render medias
     * @param mixed $medias 
     * @param mixed $theme 
     * @param mixed $systheme 
     * @param mixed $minfile 
     * @param mixed $el 
     * @param mixed $is_root 
     * @param null|array $source_media 
     * @return string 
     * @throws IGKException 
     */
    public static function RenderMedia(array $medias, $theme, $systheme, $minfile, $el, $is_root, ?array &$source_media = null)
    {
        $g = "";
        $out = '';
        $v_setup = false;
        $v_dummy = null;
        $v_skip_non_resolved = !is_null($source_media);
        foreach ($medias as $k => $v) {
            $g = trim($v->getCssDef($theme, $systheme, $minfile, $v_skip_non_resolved));
            if (!empty($g)) {
                if ($source_media) {
                    if (!($source_media['init'])) {
                        $v_dummy = $v_dummy ?? igk_getv($source_media, 'source') ?? new static(null, 'dummy');
                        $v_setup = true;
                        $g_source = $source_media['medias'][$k]->getCssDef($v_dummy, $systheme, $minfile);
                        $source_media['initdef'][$k] = $g_source;
                    }
                    if ($source_media['initdef'][$k] == $g) {
                        continue;
                    }
                }

                $ns = HtmlDocTheme::GetMediaName($k);
                if (igk_str_startwith($ns, '@')) {
                    $out .= $ns; //'@media'.
                } else
                    $out .= "@media " . $ns;
                $out .= "{" . $el;
                if ($is_root) {
                    $inf = HtmlDocTheme::GetMediaClassInfo($k);
                    if (!empty($inf)) {
                        $out .= $inf . $el;
                    }
                }
                $out .= $g . $el;
                $out .= "}" . $el;
            }
        }
        if ($v_setup) {
            $source_media['init'] = true;
        }
        return $out;
    }
    /**
     * treat value helper 
     * @param string $v 
     * @return string|void 
     * @throws IGKException 
     */
    public static function TreatValue(string $v)
    {

        $g = CssParser::Parse($v);
        $gp = CssUtils::GlueArrayDefinition($g->to_array());
        $s = $gp;
        return $s;
    }
    public static function MapTheme(array $medias, bool $is_primary_theme, string $lk, bool $skip = false)
    {
        while (count($medias) > 0) {
            if (!($m = array_shift($medias))) {
                continue;
            }
            $g = new CssMapTheme($m, $is_primary_theme, $lk);
            $g->skipProperty = $skip;
            $g->map();
        }
    }
    /**
     * 
     * @param array $tab 
     * @return string|void 
     */
    public static function GlueArrayDefinition(array $tab)
    {
        if (count($tab) > 0)
            return implode(";", array_map(function ($v, $k) {
                return sprintf('%s:%s', $k, $v);
            }, $tab, array_keys($tab))) . ";";
    }

    /**
     * remove {sys:...} expression form css source value
     * @param string $v 
     * @return string 
     */
    public static function RemoveTransformLitteralFrom(string $v)
    {
        $nv = '';
        $min = false;
        $offset = 0;

        // + | --------------------------------------------------------------------
        // + | remove system transform and litteral 
        // + |

        while ($min === false) {
            foreach (["'", '"', '{'] as $c) {
                if (false !== ($c = strpos($v, $c, $offset))) {
                    $min = ($min === false) ? $c : min($min, $c);
                }
            }
            if ($min !== false) {
                $nv = substr($v, 0, $min);
                $ch = $v[$min];
                $pos = $min;
                switch ($ch) {
                    case '{':
                        while ($min > 0) {
                            if ($nv[$min - 1] == ' ') {
                                $min--;
                                continue;
                            }
                            break;
                        }
                        $nv = rtrim($nv);
                        igk_str_read_brank($v, $pos, '}', '{');
                        $pos++;
                        $nv .= ltrim(substr($v, $pos));

                        $offset = $min;
                        break;
                    case '\'':
                    case '"':
                        # code...
                        $nv .= igk_str_read_brank($v, $pos, $ch, $ch) .
                            ltrim(substr($v, $pos + 1));
                        $offset = $pos + 1;
                        break;
                }
                $v = $nv;
                $min = false;
            } else {
                break;
            }
        }
        return trim($v);
    }
    /**
     * remove properties that not need transform for value  
     * @param string $v color:red; background-color:[bgcl]
     * @return string 
     */
    public static function RemoveNoTransformPropertyStyle(string $v)
    {
        if (empty($v)) {
            return $v;
        }

        $v = self::RemoveTransformLitteralFrom($v);
        $len = strlen($v);
        $offset = 0;
        $regex = '/\s*[\w\-]+\s*:\s*[^\[\];]+;\s*/';

        while (($offset < $len) &&  ($pos = strpos($v, '[', $offset)) !== false) {
            $g = substr($v, $offset, $pos - $offset);
            $inner = igk_str_read_brank($v, $pos, ']', '[');

            if (!empty($g) && (false !== strpos($g, ';'))) {
                $g = preg_replace($regex, '', $g);
            }
            $v = substr($v, 0, $offset) . $g . $inner . substr($v, $pos + 1);
            $offset = $offset + strlen($g . $inner) + 1;
            $len = strlen($v);
        }
        if ($offset  < $len) {
            // append extra ; if not end 
            $g = substr($v, $offset);
            if (false === strpos($g, ';')) {
                $g .= ';';
            }
            $g = preg_replace($regex, '', $g);
            $v = trim(substr($v, 0, $offset) . $g);
        }
        return trim($v);
    }
    /**
     * treat css detection 
     */
    public static function TreatCssDefinition($v, $k, &$g, bool $is_primaryTheme, string $lk, array &$source_defs = null)
    {
        $v_ev = false;
        // + | ignore case 
        // + | value is empty or k alreay content lk theme or prefix value contain [litteral] to evaluate
        // DECTECT ONLY STYLE WITH that have request transform 
        $is_empty = empty($v);
        $theme_def = strpos($k, CssConstants::THEME_SELECTOR_PREFIX) !== false;
        $need_eval = !$is_empty && preg_match(IGK_CSS_TREAT_REGEX, $v);

        if ($theme_def) {
            $g[$k] = null;
            return null;
        }


        if ($is_empty || $theme_def || $need_eval) {
            if (!$theme_def && $need_eval) {
                $v_o = igk_getv($g, $k);
                $g[$k] = null;
                if (!$is_primaryTheme) {
                    $key = self::_prependThemePreKeyToCssSelector($k, $lk);
                    $g[$key] = $v;
                    $source_defs[$key] = [$k, $v_o];
                }
            }
            return null;
        }
        $key = self::_prependThemePreKeyToCssSelector($k, $lk);
        $g[$key] = null; //$v;//"background-color:red";//null;
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
        $lk = explode(',', $tab);
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
            if (!($ctrl instanceof ApplicationModuleController)) {
                $ctrl::register_autoload();
            }
            unset($n);
        }
        $def = $theme->def;
        $cltab = &$theme->getCl();
        $cl = IGKCssColorHost::Create($cltab);


        $prop = &$theme->getProperties();
        $referrer = igk_server()->get("HTTP_REFERER", "igk://system");
        igk_environment()->push(IGKEnvironmentConstants::CSS_UTIL_ARGS, get_defined_vars());
        $render_options = $theme->getRenderOptions();
        if (is_null($render_options)) {
            $render_options = new CssThemeOptions;
            $th = igk_getr(
                "theme_name",
                CssSession::getInstance()->theme_name ??
                    CookieManager::getInstance()->get(CssSession::APP_THEME_NAME)
                    ?? CssThemeOptions::DEFAULT_THEME_NAME
            );
            if (!is_string($th)) {
                $render_options->theme_name = CssThemeOptions::DEFAULT_THEME_NAME;
            } else {
                $render_options->theme_name = $th;
            }
            $theme->setRenderOptions($render_options);
        } else if (is_null($theme_name)) {
            $theme_name = $render_options->theme_name;
        }
        $args = get_defined_vars();
        self::BindThemeFile($file, $render_options->theme_name, $args);
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
    /**
     * priority to file that match the current theme style in theme folder 
     * @param string $file 
     * @param string $theme_name 
     * @param mixed $args 
     * @return void 
     */
    private static function BindThemeFile(string $file, string $theme_name, $args)
    {
        $rf = igk_io_basenamewithoutext($file);
        $v_dir = Path::Combine(dirname($file), IGK_THEMES_FOLDER);

        foreach (['', $rf] as $tf) {
            $f = $v_dir . "/" . $tf . $theme_name . CssConstants::THEME_FILE_EXT;;
            if (file_exists($f)) {
                igk_include_if_exists(
                    $f,
                    $args
                );
            }
        }
    }


    /**
     * 
     * @param string $content 
     * @return array 
     */
    public static function GetCssSelectorKeys(string $content, $explode = true): array
    {
        $parse = CssParser::Parse($content);
        $keys = [];
        foreach ($parse->to_array() as $k => $m) {
            if (is_numeric($k)) {
                if ($m instanceof CssMedia) {
                    $c = array_keys($m->def);
                    if ($explode) {
                        $tc = [];
                        foreach ($c as $tk) {
                            $gt = explode(',', $tk);
                            while (count($gt) > 0) {
                                $tc[] = array_shift($gt);
                            }
                        }
                        $c = $tc;
                    }
                    $keys = array_merge(array_fill_keys(
                        $c,
                        1
                    ), $keys);
                }
            } else {
                if ($explode) {
                    $gt = explode(',', $k);
                    while (count($gt) > 0) {
                        $p = array_shift($gt);
                        $keys[$p] = 1;
                    }
                } else {
                    $keys[$k] = 1;
                }
            }
        }
        $tkeys = array_keys($keys);
        sort($tkeys);
        return $tkeys;
    }
}
