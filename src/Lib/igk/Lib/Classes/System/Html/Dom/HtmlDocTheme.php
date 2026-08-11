<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocTheme.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use ArrayAccess;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Css\CssColorDef;
use IGK\Css\CssThemeOptions;
use IGK\Css\ICssAddRule;
use IGK\Css\ICssAnimation;
use IGK\Css\ICssResourceResolver;
use IGK\Css\ICssStyleContainer;
use IGK\Helper\SysUtils;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssConstants;
use IGK\System\Html\Css\CssMinifier;
use IGK\System\Html\Css\CssRootPropertyStorageListener;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme as DomHtmlDocTheme;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGK\System\IO\FileHandler;
use IGK\System\IO\StringBuilder;
use IGKCssDefaultStyle;
use IGKEnvironmentConstants;
use IGKMedia;
use IGKOb;
use IGKObjectGetProperties;
use IGKHtmlDoc;
use IGKException;

/**
 * represent a document themes
 * @method ?array getTempFile() get tempory loading files
 */
final class HtmlDocTheme extends IGKObjectGetProperties implements
    ArrayAccess,
    ICssStyleContainer,
    ICssAddRule,
    ICssAnimation
{
    /**
    * Constant: media key.
    * @var mixed
    */
    const MEDIA_KEY = "medias";
    /**
    * Constant: doc theme keystorage.
    * @var mixed
    */
    const DOC_THEME_KEYSTORAGE = "theme-storage";
    /**
    * Constant: inline style key.
    * @var mixed
    */
    const INLINE_STYLE_KEY = "css://temp/rendering";
    /**
    * Constant: global type.
    * @var mixed
    */
    const GLOBAL_TYPE = 'global';
    /**
    * Constant: temp type.
    * @var mixed
    */
    const TEMP_TYPE = 'temporary';
    /**
    * Property: document.
    * @var mixed
    */
    private $m_document;
    /**
    * Property: root ref.
    * @var mixed
    */
    private $m_root_ref;
    /**
     * disable write of css header 
     * @var ?bool
     */
    var $noHeader;
    /**
     * default theme
     * @var ?string
     */
    private $m_default_theme;
    /**
    * auto generate doc.
    * @var ?array <string
    */
    private $m_bindThemeColor;
    /**
     * media definition
     * @var ?IGKCssDefaultStyle
     */
    private $m_def;
    /**
    * Identifier: id.
    * @var mixed
    */
    private $m_id;
    /**
    * Property: medias.
    * @var mixed
    */
    private $m_medias;
    /**
    * Type of type.
    * @var mixed
    */
    private $m_type;
    /**
    * Flag: istemp.
    * @var mixed
    */
    private $m_istemp;
    /**
    * Property: resolver.
    * @var mixed
    */
    private $m_resolver;
    /**
    * Property: init global.
    * @var mixed
    */
    private $m_initGlobal;
    /**
    * Property: options.
    * @var mixed
    */
    private $m_options;
    /**
    * Property: media.
    * @var mixed
    */
    private static $MEDIA;
    /**
    * Property: sm mediakey.
    * @var mixed
    */
    private static $SM_MEDIAKEY;
    /**
     * theme colors
     * @var ?array array<theme_name:string, array<color_name:string,color_value:string>
     */
    private $m_themeColors;
    /**
     * prefix used to bind css definition 
     * @var ?string
     */
    var $prefix;
    /**
     * define charset
     * @var ?string
     */
    private $m_charset;
    /**
    * Name of namespace.
    * @var mixed
    */
    private $m_namespace;
    /**
     * store the entries importations
     * @var mixed
     */
    private $m_imports;
    /**
     * inline theme resolution
     * @var ?bool
     */
    private $m_themingResolv;
    /**
    * Property: includes.
    * @var mixed
    */
    private $m_includes;
    /**
     * register frame to current theme
     * @param string $name 
     * @param string $expression 
     * @return void 
     */
    public function frame(string $name, string $expression)
    {
        return $this->registerKeyFrame($this, $name, $expression);
    }
    /**
    * auto generate doc.
    * @param string $filename
    * @param string $ext extension to handle bcss files
    * @return $this
    */
    public function load(string $filename, ?string $ext = null)
    {
        $ext = trim($ext ?? igk_io_path_ext($filename), ' .');
        $content = file_get_contents($filename);
        $def = $this;
        if ($handler = FileHandler::GetFileHandlerFromExtension(sprintf('.%s', $ext))) {
            $r = $handler->transform($content);
            $def[] = $r;
        } else {
            $def[] = $content;
        }
        return $this;
    }
    /**
     * append rule
     * @param string $name 
     * @param string $expression 
     * @return mixed 
     */
    public function addRule(string $name, string $expression)
    {
        $v_def = $this->getdef();
        return $v_def->addRule($name, $expression);
    }
    /**
    * Sets Include File List Listener.
    * @param mixed & $array
    */
    public function &setIncludeFileListListener(&$array)
    {
        $g = &$this->m_includes;
        $this->m_includes = &$array;
        return $g;
    }
    /**
    * Returns Included Files.
    */
    public function &getIncludedFiles()
    {
        return $this->m_includes;
    }
    /**
    * auto generate doc.
    * @param string $file
    * @param mixed $args
    * @return void
    */
    public function include_once(string $file, $args = null)
    {
        if (is_null($this->m_includes)) {
            $this->m_includes = [];
        }
        if (($f = realpath($file)) && !key_exists($f, $this->m_includes)) {
            $this->m_includes[$f] = 1;
            (function () {
                extract(func_get_arg(1));
                include(func_get_arg(0));
            })($f, $args ?? $this->get_include_args());
        }
    }
    /**
    * Returns include args.
    */
    protected function get_include_args()
    {
        return igk_environment()->get(IGKEnvironmentConstants::CSS_UTIL_ARGS) ?? [];
    }
    /**
     * set theme colors
     * @param null|array $theme_colors 
     * @return void 
     */
    public function setThemeColors(?array $theme_colors)
    {
        $this->m_themeColors = $theme_colors;
    }
    /**
    * Returns Imports.
    */
    public function getImports()
    {
        return $this->m_imports;
    }
    /**
     * get theme color by name
     * @param string $theme_name 
     * @return ?array
     * @throws IGKException 
     */
    public function getThemeColorsByName(string $theme_name): ?array
    {
        if ($this->m_themeColors) {
            return igk_getv($this->m_themeColors, $theme_name);
        }
        return null;
    }
    /**
    * Returns Charset.
    */
    public function getCharset()
    {
        return $this->m_charset;
    }
    /**
    * Sets Charset.
    * @param null|string $charset
    */
    public function setCharset(?string $charset)
    {
        $this->m_charset = $charset;
    }
    /**
    * Sets Namespace.
    * @param null|string $namespace
    */
    public function setNamespace(?string $namespace)
    {
        $this->m_namespace = $namespace;
    }
    /**
    * Returns Namespace.
    */
    public function getNamespace()
    {
        return $this->m_namespace;
    }
    /**
    * Imports.
    * @param mixed $uri
    */
    public function import($uri)
    {
        if (null === $this->m_imports) {
            $this->m_imports = [];
        }
        $this->m_imports[$uri] = $uri;
        return $this;
    }
    /**
     * get support on definition
     * @param string $condition 
     * @return mixed 
     */
    public function supports(string $condition)
    {
        return $this->getdef()->supports($condition);
    }
    /**
     * change the id of this doc theme
     * @param null|string $id 
     * @return void 
     */
    public function setId(?string $id)
    {
        $this->m_id = $id;
        return $this;
    }
    /**
     * get the redering options
     */
    public function getRenderOptions()
    {
        return $this->m_options;
    }
    /**
     * set the rendering options
     * @param null|CssThemeOptions $options 
     * @return $this 
     */
    public function setRenderOptions(?CssThemeOptions $options = null)
    {
        $this->m_options = $options;
        return $this;
    }
    /**
     * set theme color to root
     * @param mixed $color 
     * @param mixed $value 
     * @param string $themeName 
     * @param mixed $def 
     * @return void 
     */
    public function setThemeColor(string $color, string $value, $themeName = 'light', $def = null)
    {
        $def = $def ?? $this->getdef();
        $root = &$this->getRootReference();
        $root['--' . $themeName . '-color-' . $color] = $value;
    }
    /**
     * clear root 
     */
    public function clearRoot()
    {
        $root = &$this->getRootReference();
        $root = [];
        return $this;
    }
    /**
    * Binds Theme Color.
    * @param string $theme
    * @param null|array $colors
    */
    public function bindThemeColor(string $theme, ?array $colors)
    {
        if (is_null($colors)) {
            unset($this->m_bindThemeColor[$theme]);
        } else
            $this->m_bindThemeColor[$theme] = $colors;
    }
    /**
     * get :root reference
     */
    public function &getRootReference()
    {
        return $this->m_root_ref;
    }
    /**
    * set :root reference
    * @param mixed & $ref
    * @return void
    */
    public function setRootReference(&$ref)
    {
        $this->m_root_ref = &$ref;
    }
    /**
     * get if is init global
     */
    public function getInitGlobal()
    {
        return $this->m_initGlobal;
    }
    /**
     * get global color definition used to render color on current context
     * @return void 
     */
    public function getGlobalColor()
    {
        return CssColorDef::getInstance();
    }
    /**
    * initialize global theme definition
    * @param bool $force
    */
    public function initGlobalDefinition(bool $force = false)
    {
        if ($force || !$this->getInitGlobal()) {
            igk_css_bind_sys_global_files($this);
            igk_css_load_theme($this);
            $this->m_initGlobal = true;
        }
    }
    /**
     * reset sys global theme
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     */
    public function resetSysGlobal()
    {
        if (!defined("IGK_FORCSS")) {
            $cl = &igk_app()->getDoc()->getTheme()->def->getCl();
            array_splice($cl, 0, count($cl));
        }
        $this->reset();
        if (($this->getDoc()->getSysTheme() === $this) &&  $this->getInitGlobal()) {
            CssUtils::InitSysTheme($this);
        }
        $this->m_initGlobal = false;
    }
    use  ArrayAccessSelfTrait;
    /**
     * parent of this theme
     * @var mixed
     */
    var $parent;
    /**
     * get current theme resolver
     * @return mixed 
     */
    public function getResolver()
    {
        return $this->m_resolver;
    }
    /**
     * get resources list 
     * @return void 
     */
    public function &getRes()
    {
        $o = null;
        return $o;
    }
    /**
     * referer to global type 
     * @param HtmlItemBase|null $document owner
     * @param string|id $id
     * @param string|false $type the default value is "global", if false the the is no need to initialize
     */
    public function __construct(?HtmlItemBase $document = null, ?string $id = null, $type = self::GLOBAL_TYPE)
    {
        $this->m_id = $id ?? igk_create_guid();
        $this->m_document = $document;
        $this->m_type = $type;
        $this->m_istemp = $type === false;
        $this->_initialize();
    }
    /**
    * create tempory theme - no save in session
    * @param string $id
    **/
    public static function CreateTemporaryTheme(string $id): HtmlDocTheme
    {
        $c = new HtmlDocTheme(null, $id, false);
        $c->m_istemp = true;
        return $c;
    }
    /**
    * auto generate doc.
    */
    public function getIsTemp(): bool
    {
        return $this->m_istemp;
    }
    /**
    * set color definition
    * @param array $color
    * @var array<string,string> $color <color name, color_value>
    */
    public function setColors(array $color)
    {
        $cl = &$this->getCl();
        $cl = array_unique(array_filter(array_merge($cl, $color)));
    }
    /**
    * Resets Colors.
    */
    public function resetColors()
    {
        $cl = &$this->getCl();
        $cl = [];
    }
    /**
     * display value
     */
    public function __toString()
    {
        return "HtmlDocTheme : [id:" . $this->m_id . ", type: {$this->m_type} ]";
    }
    /**
    * Binds File.
    * @param mixed $file
    */
    public function bindFile($file)
    {
        igk_css_bind_file($this, null, $file, $this);
    }
    /**
     * convert data to array
     * @return mixed 
     */
    public function to_array()
    {
        $out = $this->m_def->to_array();
        $medias =  [];
        foreach ($this->m_medias as $id => $m) {
            $def = $m->to_array();
            if (count($def) == 0)
                continue;
            $medias[$id] = $def;
        }
        if (0 != count($medias)) {
            $out[self::MEDIA_KEY] = $medias;
        }
        return $out;
    }
    /**
     * load userialize data to to thme
     * @param array $data 
     * @return false 
     */
    public function load_data(array $data)
    {
        $this->m_def->load_data($data);
        if ($medias = igk_getv($data, self::MEDIA_KEY)) {
            foreach ($medias as $id => $m) {
                $v_m = igk_getv($this->m_medias, $id);
                if ($v_m) {
                    $v_m->load_data($m);
                }
            }
        }
        return false;
    }
    /**
     * replace media list
     * @param null|array $list 
     * @return $this 
     */
    public function replaceMediaList(?array $list)
    {
        $this->m_medias = $list;
        return $this;
    }
    /**
    * get theme styling definition
    * @param \IGKHtmlDoc $doc document that host theme to export
    * @param bool $minfile the default value is true
    * @param bool $themeexport the default value is false
    * @param ?ICssResourceResolver $resourceResolver
    * @param ICssStyleContainer $systheme style container
    */
    private function _get_css_def(IGKHtmlDoc $doc, $minfile = false, $themeexport = false, ?ICssResourceResolver $resourceResolver = null,  ?ICssStyleContainer $systheme = null)
    {
        $lineseparator = $minfile ? IGK_STR_EMPTY  : IGK_LF;
        $out = IGK_STR_EMPTY;
        $def = $this->def;
        $colors = $this->cl;
        $fonts = $def->getFont();
        $res = $this->res;
        $ft_def = "";
        $tv = 0;
        $s = "";
        if ($systheme === null) {
            $systheme = $doc->getSysTheme() ??  igk_app()->getDoc()->getSysTheme();
        }
        if (!$this->noHeader  && $this->m_charset) {
            $out .= sprintf('@charset %s;%s', $this->m_charset, "\n");
        }
        if ($this->m_namespace) {
            $out .= sprintf('@namespace %s;%s', $this->m_namespace, "\n");
        }
        if (!$this->noHeader && $this->m_imports) {
            $out .= CssUtils::RenderImport($this->m_imports);
        }
        $builder = new \IGK\Css\CssThemeResolver();
        $builder->theme = $this;
        $builder->parent = $systheme;
        $builder->resolver = $resourceResolver;
        $this->m_resolver = $builder;
        $this->m_resolver->themeResolved = &$this->m_themingResolv;
        $v_opts = $this->getRenderOptions();
        $v_skips = ($v_opts ? $v_opts->skips : null) ?? [];
        // + | --------------------------------------------------------------------
        // + | render symbols
        // + |
        $s = $def->getSymbols();
        if (is_array($s)) {
            $v_cacherequire = igk_sys_cache_require();
            $tb = array();
            foreach ($s as $k => $v) {
                if (igk_io_file_exists($k)) {
                    if ($v_cacherequire) {
                        $tb[] = "./" . igk_uri(igk_io_basepath($k));
                    } else
                        $tb[] = igk_io_fullpath2fulluri($k);
                }
            }
            $ks = igk_str_join_tab($tb, ',', false);
            $out .= ".igk-svg-symbol-lists:before{content:'$ks'} " . $lineseparator;
        }
        // + | --------------------------------------------------------------------
        // + | for design mode 
        // + |
        if (igk_css_design_mode()) {
            $v_var_def = "";
            foreach ($colors as $k => $v) {
                if (empty($v)) {
                    $v = "initial";
                } else if (preg_match("/\{(?P<name>(.)+)\}/i", $v, $tab)) {
                    $v = "var(--igk-cl-" . $tab["name"] . ")";
                }
                $v_var_def .= "--igk-cl-" . $k . ":" . $v . ";" . $lineseparator;
            }
            $tc = $this->properties;
            foreach ($tc as $k => $v) {
                if (empty($v)) {
                    $v = "initial";
                }
                $v_var_def .= "--igk-prop-" . $k . ":" . $v . ";" . $lineseparator;
            }
            if (!empty($v_var_def)){
                $out .= sprintf(":root{%s}", $v_var_def);
            }
        }
        // + | --------------------------------------------------------------------
        // + | render font definition 
        // + |
        if (!in_array('fonts', $v_skips) && $fonts) {
            $ft_def = "";
            foreach ($fonts as $k => $v) {
                if (!$v)
                    continue;
                $tv = 1;
                $s .= igk_css_get_fontdef($k, $v, $lineseparator);
                $v_def = null;
                if (isset($v->Def)) {
                    $v_def = ", " . $v->Def;
                }
                $ft_def .= ".ft-" . $k . " { font-family: \"$k\"{$v_def}; }" . $lineseparator;
            }
            if ($tv)
                $out .= "/* <!-- Fonts --> */" . $lineseparator . $s . $ft_def;
        }
        // + | --------------------------------------------------------------------
        // + | render rule definition 
        // + |
        if (!in_array('rules', $v_skips) && $def->getHasRules()) {
            !$themeexport && $out .= "/* <!-- Rules --> */\n" . $lineseparator;
            $out .= $def->getRulesString($lineseparator, $themeexport, $systheme);
            !$themeexport && $out .= "\n/* <!-- end:Rules --> */" . $lineseparator;
        }
        // + | --------------------------------------------------------------------
        // + | render attributes
        // + | 
        $s = "";
        $tv = 0;
        $prefix = $this->prefix;        
        $v_trim_chars = " \n\r\t\v\0;";
        if ($attr = $def->getAttributes()) {
            foreach ($attr as $k => $v) {
                if (empty($v))
                    continue;
                if (is_numeric($k) || empty($k)) {                    
                    $s .= $v;
                    $tv = $tv || !empty(trim($s));
                    continue;
                }
                if (is_array($v)){
                    $v = self::GlueDef($v);
                }
                $kv = $builder->treatThemeValue(trim($v, $v_trim_chars), $themeexport);
                if (!empty($kv)) {
                    if ($prefix) {
                        $k = str_replace('.', '.' . $prefix, $k);
                    }                     
                    $s .= $k . "{" . $kv . "}" . $lineseparator;
                    $tv = 1;
                }
            }
        } 
        if ($tv) {
            !$themeexport && ($out .= "/* <!-- Attributes --> */" . $lineseparator);
            $out .= rtrim($s) . $lineseparator;
            !$themeexport && ($out .= "/* <!-- end:Attributes --> */" . $lineseparator);
        }
        $res = $this->res;
        if (!$themeexport) {
            if ($res && ($attr = $res->Attributes)) foreach ($attr as $k => $v) {
                $out .= "." . $k . "{background-image: url('../Img/" . $v . "');}" . $lineseparator;
            }
        }
        $tab = $this->Append;
        if ($tab && igk_count($tab) > 0) {
            $keys = array_keys($tab);
            $out .= IGK_START_COMMENT . " APPEND THEME " . IGK_END_COMMENT . IGK_LF;
            igk_usort($keys, "igk_key_sort");
            foreach ($keys as $k) {
                $v = $tab[$k];
                $kv = trim($builder->treat($v, $themeexport));
                if (!empty($kv)) {
                    if (strpos($k, "#") === 0)
                        $out .= $k . "{" . $kv . "}" . $lineseparator;
                    else
                        $out .= "." . $k . "{" . $kv . "}" . $lineseparator;
                }
            }
        }
        $ktemp = IGK_CSS_TEMP_FILES_KEY;
        $v_csstmpfiles = $def->getTempFiles();
        if (count($v_csstmpfiles) > 0) {
            if (!igk_get_env($ktemp) && $v_csstmpfiles) {
                igk_set_env($ktemp, 1);
                $vtemp = HtmlDocTheme::CreateTemporaryTheme("theme://inline/tempfiles");
                foreach ($v_csstmpfiles as $k) {
                    $k = igk_io_expand_path($k);
                    IGKOb::Start();
                    igk_css_bind_file($vtemp, null, $k);
                    $m = IGKOb::Content();
                    IGKOb::Clear();
                    $h = $vtemp->get_css_def($minfile, $themeexport);
                    if (igk_is_debug()) {
                        $out .= "\n/TempFileLoading: *" . igk_io_basepath($k) . "*/\n";
                    }
                    $out .= $h;
                    if (!empty($m)) {
                        $out .= $m;
                    }
                    $vtemp->resetAll();
                }
                igk_set_env($ktemp, null);
            }
        }
        $this->m_resolver = null;
        return $out;
    }
    /**
    * auto generate doc.
    * @param array $v
    * @return string
    */
    static function GlueDef(array $v):string{
        return CssUtils::GlueArrayDefinition($v);
    }
    /**
    * map theme to definition
    * @param mixed $mapper
    * @param mixed $systheme
    * @param mixed $resourceResolver
    * @return void
    */
    public function map($mapper, $systheme, $resourceResolver)
    {
        $def = $this->def;
        $builder = new \IGK\Css\CssThemeResolver();
        $builder->theme = $this;
        $builder->parent = $systheme;
        $builder->resolver = $resourceResolver;
        $themeexport = false;
        if ($attr = $def->getAttributes()) {
            foreach ($attr as $k => $v) {
                if (empty($v))
                    continue;
                $kv = trim($builder->treat($v, $themeexport));
                if (!empty($kv)) {
                    $mapper("def", $k, $kv);
                    $tv = 1;
                }
            }
        }
        if ($this->m_medias) {
            foreach ($this->m_medias as $k => $v) {
                $m = $v->def;
                if (empty($m)) continue;
                $pm = [];
                foreach ($m as $t => $s) {
                    $pm[$t] = $builder->treat($s, $themeexport);
                }
                $mapper("media", self::GetMediaName($k), $pm);
            }
        }
    }
    /**
     * int theme
     */
    private function _initialize()
    {
        if ($this->m_document === null) {
            $tab = [];
            $this->def = new IGKCssDefaultStyle($tab);
            $this->_initMedia($this->m_id);
            return;
        }
        /**
         * @var IGKAppInfoStorage $app_info
         */
        $tab = null;
        $id = $this->m_document->getId();
        $app_info = igk_app()->settings->appInfo;
        $docs = null;
        $themes = null;
        if (!$this->m_istemp && $app_info) {
            $docs = &$app_info->documents[$id];
            if ($docs === null) {
                $docs = [];
                $app_info->getData()->documents[$id] = &$docs;
            }
            // + | register theme property  
            $v_key = self::DOC_THEME_KEYSTORAGE;
            if (!isset($docs[$v_key])) {
                $tab = [];
                $docs[$v_key] = &$tab;
                $tab[$this->m_id] = [];
                $tab = &$tab[$this->m_id];
                $themes = &$docs[$v_key];
            } else {
                $themes = &$docs[$v_key];
                if (!isset($themes[$this->m_id])) {
                    $themes[$this->m_id] = [];
                    $docs[$v_key] = &$themes;
                }
                $tab = &$themes[$this->m_id];
            }
        }
        $this->def = new IGKCssDefaultStyle($tab);
        $this->m_files = array();
        $this->m_medias = array();
        $this->m_mediasid = array();
        $this->Append = $this->add("AppendCss");
        $this->_initMedia($this->m_id);
    }
    /**
    * Used by var_dump() to customize debug output.
    * @return mixed
    */
    public function __debugInfo()
    {
        return [];
    }
    /**
    * auto generate doc.
    * @param mixed $id
    */
    private function _initMedia($id)
    {
        if (!(strpos($id, "media:") === 0)) {
            $this->reg_media("(max-width:" . (IGK_CSS_XSM_SCREEN) . "px)", HtmlDocThemeMediaType::XSM_MEDIA, "xsm");
            $this->reg_media("(min-width:" . (IGK_CSS_XSM_SCREEN + 1) . "px) and (max-width:" . IGK_CSS_SM_SCREEN . "px)", HtmlDocThemeMediaType::SM_MEDIA, "sm");
            $this->reg_media("(min-width:" . (IGK_CSS_SM_SCREEN + 1) . "px) and (max-width:" . IGK_CSS_LG_SCREEN . "px)", HtmlDocThemeMediaType::LG_MEDIA, "lg");
            $this->reg_media("(min-width:" . (IGK_CSS_LG_SCREEN + 1) . "px) and (max-width:" . IGK_CSS_XLG_SCREEN . "px)", HtmlDocThemeMediaType::XLG_MEDIA, "xlg");
            $this->reg_media("(min-width:" . (IGK_CSS_XLG_SCREEN + 1) . "px)", HtmlDocThemeMediaType::XXLG_MEDIA, "xxlg");
            $this->reg_media("(min-width:" . (IGK_CSS_XSM_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_XSM_MEDIA);
            $this->reg_media("(min-width:" . (IGK_CSS_SM_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_SM_MEDIA);
            $this->reg_media("(min-width:" . (IGK_CSS_LG_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_LG_MEDIA);
            $this->reg_media("(min-width:" . (IGK_CSS_XLG_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_XLG_MEDIA);
            $this->reg_media("(min-width:855px)", HtmlDocThemeMediaType::CTN_LG_MEDIA);
            $this->reg_media("(min-width:1300px)", HtmlDocThemeMediaType::CTN_XLG_MEDIA);
            $this->reg_media("(min-width:1820px)", HtmlDocThemeMediaType::CTN_XXLG_MEDIA);
            $this->reg_media();
        }
    }
    /**
    * auto generate doc.
    * @param mixed $style
    */
    private function add($style)
    {
        $tc = $this->m_tc ?? array();
        if (is_string($style) && !empty($style)) {
            $n = igk_create_xmlnode($style);
            array_push($tc, $n);
            return $n;
        }
        array_push($tc, $style);
        return $style;
    }
    /**
    * auto generate doc.
    * @param mixed $cl
    * @param mixed $value
    */
    public function addColor($cl, $value)
    {
        $changed = false;
        if (isset($this->cl[$cl])) {
            if ($this->cl[$cl] != $value) {
                $this->cl[$cl] = $value;
                $changed = true;
            }
        } else {
            $this->cl[$cl] = $value;
            $changed = true;
        }
        if ($changed) {
            $this->save();
        }
    }
    /**
    * Add file to document theme
    * @param ?BaseController $host
    * @param string $f
    * @param mixed $temp
    */
    public function addFile(?BaseController $host, string $f, $temp = 1)
    {
        if ($host === null)
            igk_die("controller host must be defined");
        igk_css_reg_global_style_file($f, $this, $host, $temp);
    }
    /**
    * add font package
    * @param mixed $name
    * @param mixed $path
    */
    public function addFont($name, $path)
    {
        $changed = false;
        $ft = &$this->def->getFont();
        if (isset($ft[$name])) {
            $changed = $ft[$name] != $path;
            $ft[$name] = $path;
        } else {
            $ft[$name] = $path;
            $changed = true;
        }
    }
    /**
    * attach tempory css file
    * @param mixed $file
    * @return bool
    */
    public function addTempFile($file)
    {
        if (!igk_io_cache_file_exists($file))
            return !1;
        $v_tfiles = &$this->m_def->getTempFiles();
        if (($g = igk_io_collapse_path($file)) && !in_array($g, $v_tfiles)) {
            $v_tfiles[] = $g;
        }
        return !0;
    }
    /**
     * add css file to render inline 
     * @param BaseController $host controller that host the file
     * @param string $f file path 
     */
    public function addInlineStyle($host, string $f)
    {
        if (!igk_io_file_exists($f))
            return false;
        $ckey = self::INLINE_STYLE_KEY;
        $tab = $this->getParam($ckey);
        if ($tab === null)
            $tab = array();
        $f = igk_io_collapse_path($f);
        $m = $f . ':' . $host;
        $hashContainer = new \IGK\System\HashContainer('sha256', function ($a, $k, string $code) {
            return $k == hash($code, $a->file . ':' . $a->host);
        });
        if ($hashContainer->contains($m, $tab)) {
            return false;
        }
        $tab[] = (object)array('file' => $f, 'host' => $host);
        $this->setParam($ckey, $tab);
        return true;
    }
    /**
     * retrieve stored inline style
     * @param bool $reset 
     * @return mixed 
     */
    public function getInlineStyle($reset = false)
    {
        $g = $this->getParam(self::INLINE_STYLE_KEY);
        if ($reset) {
            $this->setParam(self::INLINE_STYLE_KEY, null);
        }
        return $g;
    }
    /**
    * auto generate doc.
    */
    public function ClearChilds()
    {
        $this->m_def->clear();
    }
    /**
    * auto generate doc.
    */
    public function ClearFont()
    {
        $tab = $this->ft->Attributes;
        if (count($tab) > 0) {
            foreach ($tab as  $v) {
                if (is_object($v)) {
                    foreach ($v->Fonts as  $n) {
                        $f = igk_io_basedir($n->File);
                        if (igk_io_file_exists($f))
                            @unlink($f);
                    }
                } else {
                    $f = igk_io_basedir($v);
                    if (igk_io_file_exists($f))
                        @unlink($f);
                }
            }
            $this->ft->Attributes->Clear();
            $this->save();
        }
    }
    /**
    * get css definition
    * @param bool $minfile the default value is false
    * @param bool $themeexport the default value is false
    * @param ?ICssResourceResolver $resourceResolver
    * @param mixed $doc the default value is null
    * @param ?DomHtmlDocTheme $parent
    */
    public function get_css_def(
        bool $minfile = false,
        bool $themeexport = false,
        ?ICssResourceResolver $resourceResolver = null,
        $doc = null,
        ?DomHtmlDocTheme $parent = null
    ) {
        $out = '';
        $el = $minfile ? IGK_STR_EMPTY : IGK_LF;
        $is_root = false;
        $doc = $doc ?? $this->m_document ?? igk_app()->getDoc();
        $v_parent = $parent ?? $this->parent;
        $this->m_themingResolv = false;
        $v_opts = $this->m_options;
        $systheme = $doc->getSysTheme();
        $is_root = $this === $systheme;
        $parent = $is_root ? null : (($v_parent instanceof self) && ($v_parent !== $this) ? $v_parent : $systheme);
        \IGK\System\Diagnostics\Benchmark::mark($bmark = "theme-export-def");
        $out = $this->_get_css_def($doc, $minfile, $themeexport, $resourceResolver, $parent);
        \IGK\System\Diagnostics\Benchmark::expect("theme-export-def", 0.100);
        if ($this->m_medias) {
            $out .= CssUtils::RenderMedia($this->m_medias, $this, $parent ?? $systheme, $minfile, $el, $is_root);
        }
        $rtdef_root = array_merge(
            CssUtils::GetRootPropsArray($cl = $this->getCl() ?? []),
            CssUtils::GetRootPropsArray($props = $this->getProperties() ?? []),
            $this->m_root_ref ?? []
        );
        if ($rtdef_root) {
            ksort($rtdef_root);
            if ($v_opts && $v_opts->rootListener) {
                $v_opts->rootListener->store($rtdef_root);
            } else {
                $tr = new CssRootPropertyStorageListener;
                $tr->store($rtdef_root);
                $out .= $tr->render(); //  sprintf(':root{%s}', igk_css_array_key_map_implode($rtdef_root));
            }
        }
        if ($this->m_bindThemeColor && $this->m_themingResolv) {
            $out .= PHP_EOL . $this->_getThemingDefinition($systheme, $minfile, $el, $is_root);
            $this->m_themingResolv = false;
        }
        return rtrim($out);
    }
    /**
    * auto generate doc.
    * @return null|string
    */
    public function getDefaultTheme()
    {
        return $this->m_default_theme;
    }
    /**
     * set default theme 
     * @param null|string $default_theme 
     * @return void 
     */
    public function setDefaultTheme(?string $default_theme)
    {
        $this->m_default_theme = $default_theme;
    }
    /**
    * auto generate doc.
    * @param mixed $systheme
    * @param mixed $minfile
    * @param mixed $el
    * @return mixed
    */
    private function _getThemingDefinition($systheme, $minfile, $el)
    {
        $s = '';
        $v_default_theme = $this->getDefaultTheme();
        $bck = $this->getCl();
        $medias = $this->getMedias();
        $source_defs = [];
        $r = $this->getdef();
        $g = new HtmlDocTheme(null, "temp", "temporary");
        $v_source_media = ['medias' => $medias, 'initdef' => null, 'init' => false, 'source' => $this];
        foreach ($this->m_bindThemeColor as $theme_name => $cl) {
            $g->setColors($cl);
            $g->m_medias = CssUtils::CloneMedia($medias);
            $s .= CssUtils::RenderMedia(
                $g->m_medias,
                $g,
                $systheme,
                $theme_name,
                $r->getAttributes(),
                $v_default_theme == $theme_name,
                $v_source_media,
                $minfile,
                $el
            );
            $g->m_medias = null;
        }
        $this->setColors($bck);
        return $s;
    }
    /**
     * get register media
     * @param mixed $id
     */
    public function getMedia($id)
    {
        $g = null;
        if (isset($this->m_medias[$id])) {
            $g = &$this->m_medias[$id];
        } else {
            igk_ilog("Media not found {$id}");
            header("Content-Type:text/html");
            igk_dev_wln_e("media not found");
        }
        return $g;
    }
    /**
    * auto generate doc.
    */
    public function getAllClassExpression()
    {
        $out = IGK_STR_EMPTY;
        $def = $this->def;
        $tab = igk_create_node("table");
        foreach ($def->Attributes as $k => $v) {
            $r = $tab->addRow();
            $r->addTd()->Content = $k;
            $r->addTd()->Content = $v;
        }
        $out .= $tab->render();
        return $out;
    }
    /**
    * auto generate doc.
    */
    public function getAppend()
    {
        return "";
    }
    /**
    * auto generate doc.
    */
    public function getAttributes()
    {
        igk_die(__METHOD__ . ". not avaiable for theme");
    }
    /**
     * get color definitions
     * @return mixed|array colors
     */
    public function &getCl()
    {
        return $this->m_def->getCl();
    }
    /**
    * Getxsm screen.
    */
    public function getxsm_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::XSM_MEDIA);
    }
    /**
    * Getsm screen.
    */
    public function getsm_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
    }
    /**
    * Getlg screen.
    */
    public function getLgScreen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::LG_MEDIA);
    }
    /**
    * Getxlg screen.
    */
    public function getXLgScreen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::XLG_MEDIA);
    }
    /**
    * Getxxlg screen.
    */
    public function getXXLgScreen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::XXLG_MEDIA);
    }
    /**
     * get printer media
     * @return mixed 
     */
    public function getptr()
    {
        return $this->getPrintMedia();
    }
    /**
    * auto generate doc.
    * @param mixed $key the default value is null
    */
    public function getDeclaration($key = null)
    {
        /**
         * @var object $this
         */
        $out = IGK_STR_EMPTY;
        $key = $key == null ? "\$this" : $key;
        foreach ($this->def->Attributes as $k => $v) {
            $out .= $key . "[\"$k\"]=\"" . $v . "\";" . IGK_LF;
        }
        foreach ($this->getChilds() as $k) {
            $t = strtolower($k->TagName);
            $c = false;
            switch ($t) {
                case "default":
                case "igk:text":
                case "":
                    $c = true;
                    break;
                default:
                    $c = !preg_match(IGK_ISIDENTIFIER_REGEX, $t);
                    break;
            }
            if ($c)
                continue;
            $out .= "\$$k->TagName = igk_getv({$key}->getElementsByTagName(\"$k->TagName\"), 0);" . IGK_LF;
            $tab = $k->Attributes;
            if ($tab) {
                foreach ($tab as $r => $s) {
                    if (is_object($s)) {
                        switch ($k->TagName) {
                            case "Fonts":
                                $out .= "\$$k->TagName[\"$r\"]=\"" . str_replace("\\", "\\", str_replace("\"", "'", igk_css_get_fontdef($s->Name, $s))) . "\";" . IGK_LF;
                                break;
                        }
                        continue;
                    }
                    $out .= "\$$k->TagName[\"$r\"]=\"" . str_replace("\\", "\\", str_replace("\"", "'", $s)) . "\";" . IGK_LF;
                }
            }
        }
        return $out;
    }
    /**
     * retrieve reference to definition
     * @return mixed|array|object definition
     */
    public function &getdef()
    {
        return $this->m_def;
    }
    /**
     *  get the parent document
     */
    public function getDoc()
    {
        return $this->m_document;
    }
    /**
     * return registrated fontn
     */
    public function getFont()
    {
        return null;
    }
    /**
     *  return the id of the data
     */
    public function getId()
    {
        return $this->m_id;
    }
    /**
    * auto generate doc.
    * @param mixed $idk
    */
    public static function GetMediaClassInfo($idk)
    {
        if (self::$SM_MEDIAKEY == null) {
            self::$SM_MEDIAKEY = [HtmlDocThemeMediaType::XSM_MEDIA => "xsm", HtmlDocThemeMediaType::SM_MEDIA => "sm", HtmlDocThemeMediaType::LG_MEDIA => "lg", HtmlDocThemeMediaType::XLG_MEDIA => "xlg", HtmlDocThemeMediaType::XXLG_MEDIA => "xxlg",];
        }
        $s = null;
        if (isset(self::$SM_MEDIAKEY[$idk])) {
            $g = trim(self::$SM_MEDIAKEY[$idk]);
            $s = IGK_CSS_MEDIA_TYPE_CLASS . "{z-index:{$idk}; content:'{$g}'}";
        }
        return $s;
    }
    /**
     * get registrated media name
     * @param mixed $idk
     */
    public static function GetMediaName($idk)
    {
        if (!isset(self::$MEDIA))
            self::$MEDIA = [HtmlDocThemeMediaType::XSM_MEDIA => "(max-width:" . (IGK_CSS_XSM_SCREEN) . "px)", HtmlDocThemeMediaType::SM_MEDIA => "(min-width:" . (IGK_CSS_XSM_SCREEN + 1) . "px) and (max-width:" . IGK_CSS_SM_SCREEN . "px)", HtmlDocThemeMediaType::LG_MEDIA => "(min-width:" . (IGK_CSS_SM_SCREEN + 1) . "px) and (max-width:" . IGK_CSS_LG_SCREEN . "px)", HtmlDocThemeMediaType::XLG_MEDIA => "(min-width:" . (IGK_CSS_LG_SCREEN + 1) . "px) and (max-width:" . IGK_CSS_XLG_SCREEN . "px)", HtmlDocThemeMediaType::XXLG_MEDIA => "(min-width:" . (IGK_CSS_XLG_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_XSM_MEDIA => "(min-width:" . (IGK_CSS_XSM_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_SM_MEDIA => "(min-width:" . (IGK_CSS_SM_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_LG_MEDIA => "(min-width:" . (IGK_CSS_LG_SCREEN + 1) . "px)", HtmlDocThemeMediaType::GT_XLG_MEDIA => "(min-width:" . (IGK_CSS_XLG_SCREEN + 1) . "px)", HtmlDocThemeMediaType::CTN_LG_MEDIA => "(min-width:855px)", HtmlDocThemeMediaType::CTN_XLG_MEDIA => "(min-width:1300px)", HtmlDocThemeMediaType::CTN_XXLG_MEDIA => "(min-width:1820px)"];
        return igk_getv(self::$MEDIA, $idk, $idk);
    }
    /**
     * get all registrated medias
     */
    public function getMedias()
    {
        return $this->m_medias;
    }
    /**
     * get print media
     */
    public function getPrintMedia()
    {
        return $this->reg_media("print", null, 'print');
    }
    /**
    * auto generate doc.
    */
    public function getRegChangedKey()
    {
        return __CLASS__ . "_" . $this->Name;
    }
    /**
     * get rules attached to theme definition
     * @return mixed|array rules
     */
    public function &getRules()
    {
        $sd = &$this->m_def->getRules();
        return $sd;
    }
    /**
    * auto generate doc.
    * @param mixed $file
    */
    public function LoadThemeFromFile($file)
    {
        if (igk_io_file_exists($file)) {
            include($file);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $i
    */
    protected function _access_offsetExists($i): bool
    {
        if (isset($this->m_tc))
            return ($i >= 0) && ($i < count($this->m_tc));
        return !1;
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    protected function _access_offsetGet($key)
    {
        return $this->def[$key];
    }
    /**
    * auto generate doc.
    * @param mixed $key
    * @param mixed $value
    */
    protected function _access_offsetSet($key, $value)
    {
        $def = &$this->getDef();
        if (is_array($value) && !is_null($key)) {
            $value = CssUtils::GlueArrayDefinition($value);
        }
        if (is_null($key)) {
            if (is_null($value)) {
                unset($def[0]);
                return;
            }
            $l = $def[0];
            if ($l) {
                $value = $l . $value;
                $def[''] = $value;
            } else{
                $def[] = $value;
            }
            return;
        }
        $def[$key] = $value;
    }
    /**
    * auto generate doc.
    * @param mixed $i
    */
    protected function _access_offsetUnset($i)
    {
        if (isset($this->m_tc))
            unset($this->m_tc[$i]);
    }
    /**
     * output 
     */
    public function output()
    {
        header("Content-Type:text/css");
        $s = $this->get_css_def();
        igk_wl($s);
    }
    /**
     * register key frame 
     * @param ICssAddRule $def
     * @param string $name
     * @param string $expression
     */
    public function registerKeyFrame(ICssAddRule $def, string $name, string $expression)
    {
        $def->addRule("@-webkit-keyframes " . $name, $expression);
        $def->addRule("@-moz-keyframes " . $name, $expression);
        $def->addRule("@keyframes " . $name, $expression);
    }
    /**
     * add manimation 
     * @param string $name frame name
     * @param mixed|array|string $definition defintion 
     * @return void 
     */
    public function animation(string $name, $definition)
    {
        $definition = CssUtils::BlockDefinition($definition);
        $this->registerKeyFrame($this, $name, $definition);
    }
    /**
    * register a media
    * @param mixed $name name or condition
    * @param mixed $id
    * @param mixed $display
    */
    public function reg_media($name = "print", $id = null, $display = null)
    {
        $s = "";
        $n = null;
        $doc = $this->m_document;
        $is_root = strpos($this->m_id, "sys:");
        $display = ($name == 'print') && ($display == null) ? 'ptrdevice' : $display;
        if (!isset($this->m_medias[$name])) {
            $n = new IGKMedia("media:" . $name, $display);
            $idkey = $id ?? $name;
            $this->m_medias[$idkey] = $n;
        } else {
            $n = $this->m_medias[$name];
        }
        return $n;
    }
    /**
    * auto generate doc.
    * @param mixed $cl
    */
    public function removeColor($cl)
    {
        if (isset($this->cl[$cl])) {
            $this->cl[$cl] = null;
            $this->save();
        }
    }
    /**
    * auto generate doc.
    * @param mixed $name
    */
    public function removeFont($name)
    {
        $f = $this->ft[$name];
        if ($f) {
            if (is_object($f)) {
                $this->ft[$name] = null;
                unset($this->ft[$name]);
                $this->save();
                return true;
            }
            if (is_string($f)) {
                $f = igk_io_currentrelativepath($f);
                if (igk_io_file_exists($f) && (unlink($f))) {
                    igk_notifyctrl()->addMsg(__("msg.fontfile.removed"));
                }
                $this->ft[$name] = null;
                $this->save();
                return true;
            }
        }
        return false;
    }
    /**
    * reset all media definition
    * @param mixed $save
    */
    public function reset($save = false)
    {
        $this->def->Clear();
        $cl = &$this->getCl(); 
        array_slice($cl, count($cl));
        if ($res = $this->res) {
            array_splice($res, 0, count($res));
        }
        if ($rule = &$this->getRules()) {
            array_splice($rule, 0, count($rule));
        }
        if ($this->m_medias)
            foreach ($this->m_medias as $v) {
                $v->Clear();
            }
        if ($save)
            $this->save();
    }
    /**
     * clear all - 
     */
    public function resetAll()
    {
        $this->def->Clear();
        $this->m_medias = array();
        $this->_initMedia($this->m_id);
    }
    /**
    * auto generate doc.
    * @param mixed $file the default value is null
    */
    public function save($file = null)
    {
        if (($file == null) && empty($this->Name))
            return;
        $f = ($file == null) ? igk_io_syspath(IGK_RES_FOLDER . "/Themes/" . $this->Name . "." . IGK_DEFAULT_VIEW_EXT) : $file;
        $out = IGK_STR_EMPTY;
        $out .= "<?php" . IGK_LF;
        $out .= implode("\n", [
            '// Theme Media creation',
            "// Name : {$this->Name}",
            '$cl = get_class(\$this);',
            'if ($cl != \'HtmlDocTheme\')',
            '{',
            'igk_die("this file can be only included in HtmlDocTheme context");',
            '}'
        ]);
        $out .= $this->getDeclaration();
        $out .= IGK_START_COMMENT . "media properties " . IGK_END_COMMENT . IGK_LF;
        foreach ($this->m_medias as $k => $v) {
            $out .= "\$media = igk_getv(\$this->m_medias, '$k');" . IGK_LF;
            $out .= "if (\$media){ " . IGK_LF;
            $out .= $v->getDeclaration("\$media");
            $out .= "}" . IGK_LF;
        }
        $result = igk_io_save_file_as_utf8($f, $out, true);
        return $result;
    }
    /**
    * auto generate doc.
    * @param ?IGKCssDefaultStyle $v
    */
    protected function setdef(?IGKCssDefaultStyle $v)
    {
        if ($v === null) {
            igk_die("/!\\ bad ? " . ($v === null), __METHOD__);
        }
        $this->m_def = $v;
    }
    /**
    * store document tempory property
    * @param mixed $name
    * @param mixed $value
    */
    public function setProperty($name, $value)
    {
        $p = &$this->m_def->getParams();
        if (is_null($value)) {
            unset($p[$name]);
        } else {
            $p[$name] = $value;
        }
    }
    /**
    * auto generate doc.
    * @param mixed $k
    * @return mixed|array properties
    */
    public function &getProperties($k = null)
    {
        $g = &$this->m_def->getParams();
        if ($k) {
            if (isset($g[$k])) {
                $g = &$g[$k];
                return $g;
            }
        }
        return  $g;
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function getParam($key)
    {
        return $this->getProperties($key);
    }
    /**
    * Sets Param.
    * @param mixed $key
    * @param mixed $value
    */
    public function setParam($key, $value)
    {
        $this->setProperty($key, $value);
        return $this;
    }
    /**
     * check for app is system theme
     * @return bool
     * @throws IGKException not initialize
     */
    public function isSystemTheme(): bool
    {
        return $this === igk_app()->getDoc()->getSysTheme();
    }
}