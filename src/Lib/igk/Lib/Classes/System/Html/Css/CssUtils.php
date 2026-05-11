<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssUtils.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Css;
use IGK\IGlobalFunction;
use Exception;
use IGK\Constants;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\Css\CssThemeOptions;
use IGK\Css\CssThemeRenderer;
use IGK\Css\IGKCssColorHost;
use IGK\Helper\ArrayUtils;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Css\CssEnvironment;
use IGK\System\Html\Css\CssMapTheme;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGK\System\Http\CookieManager;
use IGK\System\IO\FileHandler;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGK\System\Text\RegexMatcherContainer;
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
    /**
    * Property: treated colors.
    * @var mixed
    */
    private static $sm_treated_colors = [];
    /**
    * Constant: css desc title.
    * @var mixed
    */
    const CSS_DESC_TITLE = 'Balafon Css Theme';
    /**
     * get code block definition 
     * @param mixed $definition 
     * @return mixed 
     */
    public static function BlockDefinition($definition){
        if (is_array($definition)){
            $sb = new StringBuilder;
            foreach($definition as $k=>$v){
                $sb->append(sprintf('%s{%s}', $k, $v));
            }
            $definition = $sb.'';
        }
        return $definition;
    }
    /**
    * merge styles definition
    * @param mixed ...$args
    * @return string
    */
    public static function MergeStyleDefinition(...$args)
    {
        if (!is_array($args)) {
            $args  = [$args];
        }
        $m = StringUtility::DEFAULT_TRIM_CHAR . ';';
        $l =  implode(';', array_map(function ($c) use ($m) {
            if (is_array($c)){
                $c = self::GlueArrayDefinition($c); 
            }
            return rtrim($c ?? '', $m);
        }, array_filter($args)));
        return $l;
    }
    /**
    * Returns Treated Colors.
    */
    public static function &GetTreatedColors()
    {
        if (is_null(self::$sm_treated_colors)) {
            self::$sm_treated_colors = [];
        }
        return self::$sm_treated_colors;
    }
    /**
    * Clears Treat Colors.
    */
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
    /**
    * auto generate doc.
    * @param BaseController $controller
    * @param null|string $ruri
    * @return null|string
    */
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
    /**
    * auto generate doc.
    * @param mixed $vsystheme
    * @return void
    */
    public static function InitSysTheme($vsystheme)
    {
        $vsystheme->def->Clear();
        $d = $vsystheme->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
        $d = $vsystheme->getMedia(HtmlDocThemeMediaType::XSM_MEDIA);
        $d = $vsystheme->reg_media("(max-width:700px)");
        $v_cache_file = igk_dir(IGK_LIB_DIR . "/.Cache/.css.cache");
        if (igk_io_file_exists($v_cache_file, true)) {
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
        $g = implode(';', array_unique($lfile));
        $g = str_replace(IGK_LIB_DIR, "%lib%", $g);
        $vsystheme->def->setFiles($g);
    }
    /**
    * Get Injectable Style from file definition
    * @param string $file pcss source file
    * @param null|BaseController $ctrl
    * @param null|HtmlDocTheme $parent
    * @param mixed & $css
    * @param mixed $autoinit
    * @throws IGKException
    * @throws ArgumentTypeNotValidException
    * @throws ReflectionException
    * @throws EnvironmentArrayException
    * @throws CssParserException
    * @return string
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
     * @param string $theme 'dark' | 'light' | 'both'
     * @param bool $embedresource 
     * @param string $prefix prefix all current theme 
     * @return string|false 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */
    public static function GenCss(BaseController $controller, string $theme = CssThemeOptions::DEFAULT_THEME_NAME, bool $embedresource = false, ?string $prefix = '')
    {
        if ($theme == CssThemeOptions::BOTH_THEME_NAME) {
            $theme = igk_environment()->get('default_theme', CssThemeOptions::DEFAULT_THEME_NAME);
            return self::GenCssWithThemeSupport($controller, $theme);
        }
        $opt = new CssThemeOptions;
        $opt->theme_name = '';
        $opt->rootListener = new CssRootPropertyStorageListener;
        $theme = new HtmlDocTheme(null, "temp", "temporary");
        $systheme = igk_app()->getDoc()->getSysTheme();
        $theme->setRenderOptions($opt);
        $theme->prefix = $prefix;
        ob_start();
        igk_css_bind_sys_global_files($systheme);
        igk_css_load_theme($theme);
        $controller->bindCssStyle($theme, true);
        echo "/* @description: Balafon CSS theme */";
        $resourceResolver = null;
        if ($embedresource) {
            $resourceResolver = new EmbedResourceResolver();
        }
        $list = [$systheme, $theme];
        $imports = [];
        $sb = '';
        $ch = '';
        while (count($list) > 0) {
            $q = array_shift($list);
            $imports = array_merge($q->getImports() ?? [], $imports);
            $q->noHeader = true;
            $sb .= $ch . $q->get_css_def(true, true, $resourceResolver);
            $q->noHeader = false;
            $ch = "\n";
        }
        if ($imports) {
            echo self::RenderImport($imports);
        }
        echo $sb;
        echo "\n/* root definition */", $opt->rootListener->render();
        $r = ob_get_contents();
        ob_clean();
        $theme->setRenderOptions(null);
        return $r;
    }
    /**
    * auto generate doc.
    * @param array $imports
    * @return string
    */
    static function RenderImport(array $imports)
    {
        return implode(";\n", array_map(function ($s) {
            return sprintf('@import "%s"', $s);
        }, $imports)) . ";";
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
        // + | bind global theme to start 
        $systheme = igk_app()->getDoc()->getSysTheme();
        igk_css_bind_sys_global_files($systheme);
        $rootListener = new CssRootPropertyStorageListener;
        $def = self::AppendDataTheme($controller, $systheme, $primaryTheme, false, $rootListener);
        ob_start();
        self::ObHeader();
        echo implode(
            "\n",
            array_merge([], $def)
        );
        $r = ob_get_contents();
        ob_clean();
        return $r;
    }
    /**
     * write ob header 
     */
    private static function ObHeader()
    {
        echo sprintf("/* @description: %s */\n", self::CSS_DESC_TITLE);
        echo sprintf("/* @date: %s */\n", date('Ymd H:i:s'));
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
    public static function RenderPrimaryTheme(HtmlDocTheme $a_theme, ?array $colors = null, $minfile = false, $theme_export = false)
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
    * auto generate doc.
    * @param BaseController $controller
    * @param HtmlDocTheme $a_theme
    * @param mixed $primaryTheme
    * @param bool $theme_export
    * @param mixed $rootListener
    * @return array
    */
    public static function AppendDataTheme(
        BaseController $controller,
        HtmlDocTheme $a_theme,
        string $primaryTheme = CssThemeOptions::DEFAULT_THEME_NAME,
        bool $theme_export = false,
        $rootListener = null
    ) {
        if ($controller->getConfig('no_theme_support'))
            return;
        $tdef = explode('|', CssConstants::SUPPORT_THEME);
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
        ArrayUtils::PrependAfterSearch($tdef, $primaryTheme);
        $v_render_primary = false;
        $tab = $a_theme->getdef()->getAttributes() ?? [];
        $medias = $a_theme->getMedias();
        $opt = new CssThemeOptions;
        $opt->skips = ['rules', 'fonts'];
        $opt->rootListener = $rootListener;
        if ($v_opts = $a_theme->getRenderOptions()) {
            // + | TODO: Missing root listener 
            $v_opts->rootListener = $rootListener;
        }
        $v_systheme = $a_theme->isSystemTheme(); 
        $v_theme = null;
        $v_first = $v_systheme;
        $root_defs = [];
        $sroot_defs = [];  
        $v_copy = null;  
        foreach ($tdef as $theme_name) {
            $opt->theme_name = $theme_name;
            $opt->is_primary = $primaryTheme == $theme_name;
            $colors = $a_theme->getThemeColorsByName($theme_name);
            if ($v_systheme){
                $inc_files = $v_theme ? $v_theme->getIncludedFiles() : null;
                // + | load specific attached theme options... 
                $v_theme = new HtmlDocTheme(null, "temp", HtmlDocTheme::TEMP_TYPE);
                // + | $v_theme->replaceMediaList($s_medias);
                $inc_files && $v_theme->setIncludeFileListListener($inc_files);
                // + | store theming color before binding so no need to override color in target definition 
                if ($colors) {
                    $v_theme->setColors($colors);
                } 
            }
            else {
                $v_copy = $v_copy ?? $a_theme->to_array();
                $v_theme = new HtmlDocTheme(null, "temp", HtmlDocTheme::TEMP_TYPE);
                $v_theme->load_data($v_copy);
            } 
                // + | set options before bind style
                $v_theme->setRenderOptions($opt); 
                 // + | load bind style with theme 
                 $controller->bindCssStyle($v_theme, true);
            if ($v_first) {
                $core = $a_theme->get_css_def(true, true);
                array_unshift($def, implode("\n", [
                    "/* begin: core-theme: */",
                    $core,
                    "/* end: core-theme:*/",
                    ''
                ]));
                $v_render_primary = true; 
            }  
            if ($opt->is_primary  && ($s = $v_theme->get_css_def(true, true))){
                $def[] = $theme_export ? $s : implode("\n", ["/* begin: primary-theme */", $s, "/*end: primary-theme*/"]);
            }
            self::MapMediaCssTheme(
                $v_theme,
                $theme_name,
                $v_theme->def->getAttributes(),
                null,
                $opt->is_primary
            );   
            self::BindRootDataThemeDefinition($root_defs,$v_theme, $theme_name);
            if ( $s = $v_theme->get_css_def()){   
                $def[] = ($theme_export ? "\n/* theme: " . $theme_name . " */\n" : ''). $s; 
            }  
            $v_first = false;
        } 
        if ($rootListener && ($gv = $rootListener->render()))
            $def[] = $gv;
            // + | INJECT ROOT THEME PROPERTIES DEFINITION .
            // + | PROPERTIES THAT startt with -- (two hyphen must be consider as property )
        if (count($root_defs)>0){
            foreach($root_defs as $k=>$v){
                $gv = [];
                foreach($v as $rk=>$rv){
                    if (preg_match("/^--/", $rk)){
                        $gv[$rk] = $rv;
                    }
                }
                if (count($gv)>0){
                    $def[]= $k.sprintf('{%s}', self::GlueArrayDefinition($gv));
                }
            }
        }
        return $def;
    }
    /**
    * Binds Root Data Theme Definition.
    * @param array & $rootdef
    * @param mixed $theme
    * @param string $theme_name
    */
    public static function BindRootDataThemeDefinition(array & $rootdef, $theme, string $theme_name){
        $colors = $theme->getDef()->getCl();
        $keys = sprintf('html[data-theme="%s"]', $theme_name);
        if (!isset($rootdef[$keys])){
            $rootdef[$keys] = [];
        }
        $rootdef[$keys] = array_merge($rootdef[$keys], $colors );
    }
    /**
    * Exports Color And Properties.
    * @param BaseController $controller
    * @param mixed $theme
    */
    public static function ExportColorAndProperties(BaseController $controller, $theme)
    {
        $tdef = explode('|', CssConstants::SUPPORT_THEME);
        $v_theme = null;
        $colors = [];
        $props = [];
        foreach ($tdef as $theme_name) {
            $v_theme = new HtmlDocTheme(null, "temp", "temporary");
            // + | load bind style with theme 
            $controller->bindCssStyle($v_theme, true);
            $colors = array_merge($colors, $v_theme->getCl());
            $props = array_merge($props, $v_theme->getProperties());
        }
        return compact('colors', 'props');
    }
    /**
    * auto generate doc.
    * @param string $lk
    * @param array $tab
    * @param mixed & $g
    * @param mixed & $source_defs
    * @return void
    */
    public static function MapThemeDefinition(string $lk, array $tab, &$g, &$source_defs = null)
    {
        array_map(function ($v, $k) use (&$g, $lk, &$source_defs) {
            $v = !empty($v) ? CssUtils::RemoveNoTransformPropertyStyle($v) : $v;
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
     * @param HtmlDocTheme $theme
     * @param string|'dark'|'light' $theme_name 
     * @param ?array $tab definition to map 
     * @param ?array $medias array of medias list to map
     * @param bool $is_primary_theme 
     */
    public static function MapMediaCssTheme(
        HtmlDocTheme $theme,
        string $theme_name,
        ?array $tab,
        ?array $medias = null,
        bool $is_primary_theme = false
    ) {
        $lk = sprintf(CssConstants::THEME_SELECTOR_FORMAT, $theme_name);
        $theme_medias = $theme->getMedias();
        $medias = $medias ?? $theme_medias;
        if ($tab) {
            self::MapThemeDefinition($lk, $tab, $theme);
        }
        if ($medias)
        {
            self::MapTheme($medias, $is_primary_theme, $lk, true);
            if ($theme_medias !== $medias){
                foreach($medias as $k=>$v){
                    if ($v instanceof IGKMedia){
                        $m = $v->getDef() ?? [];
                        $g = igk_getv($theme_medias, $k);
                        if ($g instanceof IGKMedia){
                            $t = array_merge($g->getDef() ?? [], $m);
                            $g->clear();
                            $g->loadDef($t);
                        }
                    }
                }
            }
        }
    }
    /**
    * render medias
    * @param mixed $medias
    * @param mixed $theme
    * @param mixed $systheme
    * @param mixed $minfile
    * @param mixed $el
    * @param mixed $is_root
    * @param ?array & $source_media
    * @throws IGKException
    * @return string
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
                    $out .= $ns; 
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
    /**
    * update array media properties
    * @param array $medias
    * @param bool $is_primary_theme
    * @param string $lk
    * @param bool $skip
    */
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
     * glue array definition 
     * @param array<string,array> $tab key
     * @return string|void 
     */
    public static function GlueArrayDefinition(array $tab)
    {
        if (count($tab) > 0)
            return implode(";", array_map(function ($v, $k) {
                if (is_numeric($k))
                    return $v;
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
        // + | --------------------------------------------------------------------
        // + | remove system transform and litteral 
        // + | remove : {sys:...}
        // + | 
        // + |
        $container = new RegexMatcherContainer;
        $container->begin('{', '}(\\s*;\\s*)?', 'litteral'); 
        $container->begin("(\"|')", "\\1", 'string');        
        $container->begin("\\(", "\\)", 'parenthese');       
        $container->match("\\s*(;|:)\\s*", 'operator');
        $container->match("\\s+", 'white-space');
        $lpos = 0;
        $n = '';
        $join = '';
        $container->treat($v, function ($g, $pos, $v) use (&$n, &$lpos, &$join) {
            switch ($g->tokenID) {
                case 'operator':
                    $n = trim($n) . trim(substr($v, $lpos, $g->from - $lpos)) . trim($g->value);
                    $lpos = $pos;
                    break;
                case 'litteral':
                case 'white-space':
                    $n = trim($n . substr($v, $lpos, $g->from - $lpos));
                    if ($g->tokenID == 'white-space') {
                        $n .= ' ';
                    } else {
                        $join = '';
                    }
                    $lpos = $pos;
                    break;
            }
        });
        $n .= substr($v, $lpos);
        return trim($n);
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
        $detector = new CssThemeValueDetector;
        return $detector->treat($v);
    }
    /**
     * get only branket definition symbold
     * @param string $style 
     * @return string 
     */
    public static function GetBranketOnlyStyle(string $style)
    {
        $container = new RegexMatcherContainer;
        $container->match('\s*[\w\-]+\s*:\s*[^\[\];]+;\s*', 'exclude');
        $container->begin('\[', '\](\\s*;\\s*)?', 'definition');
        $container->begin("(\"|')", "\\1", 'string');
        $ch = '';
        $lpos = 0;
        $container->treat($style, function ($g, $pos, $data) use (&$lpos, &$ch) {
            switch ($g->tokenID) {
                case 'exclude':
                    $ch .= substr($data, $lpos, $g->from - $lpos);
                    $lpos = $pos;
                    break;
                case 'definition':
                    $end = igk_str_endwith(rtrim($g->value), ';');
                    $ch .= substr($data, $lpos, $g->from - $lpos) . trim($g->value, '; ');
                    if ($end) {
                        $ch .= ';';
                    }
                    $lpos = $pos;
                    break;
            }
        });
        return $ch;
    }
    /**
    * treat css detection
    * @param mixed $v
    * @param mixed $k
    * @param mixed & $g
    * @param bool $is_primaryTheme
    * @param string $lk
    * @param ?array & $source_defs
    */
    public static function TreatCssDefinition($v, $k, &$g, bool $is_primaryTheme, string $lk, ?array &$source_defs = null)
    {
        $v_ev = false;
        // + | ignore case 
        // + | value is empty or k alreay content lk theme or prefix value contain [litteral] to evaluate
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
                if (!$is_primaryTheme || $need_eval) {
                    $key = self::_prependThemePreKeyToCssSelector($k, $lk);
                    $g[$key] = $v;
                    $source_defs[$key] = [$k, $v_o];
                }
            }
            return null;
        }
        $key = self::_prependThemePreKeyToCssSelector($k, $lk);
        $g[$key] = null; 
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
            if (strlen($a) > 0 && ($a[0] == ':')) {
                $keys = trim($keys);
            }
            return $keys . $a;
        }, $lk));
        return $lk;
    }
    /**
    * init sys global document
    * @param \IGKHtmlDoc $doc
    */
    public static function InitSysGlobal(\IGKHtmlDoc $doc)
    {
        $clear = 0;
        $sys = $doc->getSysTheme();
        if (!$sys->getinitGlobal()) {
            $sys->initGlobalDefinition();
            $clear = 1;
            if (!defined("IGK_FORCSS")) {
                  igk_reg_hook(IGKEvents::HOOK_APP_SHUTDOWN, 
                    function () use ($sys) {
                    $sys->resetSysGlobal();
                });
            }
        }
        return $clear;
    }
    /**
    * auto generate doc.
    * @param BaseController $ctrl
    * @param HtmlDocTheme $theme
    * @param string $file
    * @param bool $cssRendering
    * @param bool $temp
    * @param bool $raiseHook
    * @return void
    */
    public static function InitBindingCssFile(
        BaseController $ctrl,
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
    * @param bool $themeexport
    * @throws IGKException
    * @throws CssParserException
    * @throws Exception
    * @return string|null
    */
    public static function GetInlineStyleRendering($doc, bool $themeexport)
    {
        $bvtheme = new HtmlDocTheme($doc, "temp://files", false);
        $out = "";
        $g = $doc->getTheme()->getInlineStyle(true);
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
    * @param string $file
    * @throws IGKException
    * @return void
    */
    public static function AppendInlineStyle(string $file)
    {
        $ctrl = igk_get_current_base_ctrl();
        if ($doc = ViewHelper::CurrentDocument()) {
            $doc->getTheme()->addInlineStyle($ctrl, $file);
        }
    }
    /**
    * auto generate doc.
    * @param string $file
    * @param mixed $ctrl
    * @param mixed $theme
    * @return mixed
    */
    public static function GetFileContent(string $file, $ctrl, $theme)
    {
        self::Include($file, $ctrl, $theme);
        return $theme->getDef();
    }
    /**
     * helper: get css class name 
     * @param BaseController $ctrl 
     * @return string 
     */
    public static function GetCssClassName(BaseController $ctrl){
        return strtolower(igk_css_str2class_name($ctrl->getName()));
    }
    /**
    * include pcss binding files
    * @param string $file file to incluce
    * @param ?BaseController $ctrl controller
    * @param HtmlDocTheme $theme theme to use
    * @param ?string $theme_name
    * @throws IGKException
    * @throws EnvironmentArrayException
    * @return void
    */
    public static function Include(
        string $file,
        ?BaseController $ctrl = null,
        ?HtmlDocTheme  $theme = null,
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
        ob_start();
        include($file);
        $src = ob_get_contents();
        ob_end_clean();
        if ($src) {
            if ($bcss_handler = FileHandler::GetFileHandlerFromExtension( Constants::BCSS_EXTENSION )){
                $src = $bcss_handler->transform($src);
            }            
            $theme[] = $src;
        }
        igk_environment()->pop(IGKEnvironmentConstants::CSS_UTIL_ARGS);
        // + remove binding properties args
    }
    /**
    * Property: old theme.
    * @var mixed
    */
    static $old_theme;
    /**
     * priority to file that match the current theme style in theme folder 
     * @param string $file style files
     * @param string $theme_name 'light'|'dark'
     * @param mixed $args 
     * @return void 
     */
    private static function BindThemeFile(string $file, string $theme_name, $args)
    {
        $rf = igk_io_basenamewithoutext($file);
        $v_dir = Path::Combine(dirname($file), IGK_THEMES_FOLDER);
        // + | fix list of theme file accorging to cibling styles file
        foreach (['', $rf . '.'] as $tf) {
            $f = $v_dir . "/" . $tf . $theme_name . CssConstants::THEME_FILE_EXT;;
            if (igk_io_file_exists($f, true)) {
                igk_include_if_exists(
                    $f,
                    $args
                );
            }
        }
    }
    /**
    * auto generate doc.
    * @param string $content
    * @param mixed $explode
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
    /**
    * Returns Root Props Array.
    * @param array $list
    */
    public static function GetRootPropsArray(array $list)
    {
        $mk = [];
        foreach (array_keys($list) as $k) {
            if (preg_match('/^--/', $k)) {
                $mk[$k] = $list[$k];
            }
        }
        return $mk;
    }
}