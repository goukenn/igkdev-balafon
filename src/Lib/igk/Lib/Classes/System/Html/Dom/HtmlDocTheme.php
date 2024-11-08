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
use IGK\Css\ICssResourceResolver;
use IGK\Css\ICssStyleContainer;
use IGK\Helper\SysUtils;
use IGK\System\Html\Css\CssConstants;
use IGK\System\Html\Css\CssMinifier;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme as DomHtmlDocTheme;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGKCssDefaultStyle;
use IGKEnvironmentConstants;
use IGKMedia;
use IGKOb;
use IGKObjectGetProperties;
use IGKHtmlDoc;
use IGKException;

///<summary>represent a document themes</summary>
/**
 * represent a document themes
 * @method ?array getTempFile() get tempory loading files
 */
final class HtmlDocTheme extends IGKObjectGetProperties implements ArrayAccess, ICssStyleContainer
{   

    const MEDIA_KEY = "medias";
    const DOC_THEME_KEYSTORAGE = "theme-storage";
    private $m_document;
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
     * 
     * @var ?array <string, string[]> color definition 
     */
    private $m_bindThemeColor;
    /**
     * media definition
     * @var ?IGKCssDefaultStyle
     */
    private $m_def;
    private $m_id;
    private $m_medias;
    private $m_type;
    private $m_istemp;
    private $m_resolver;
    private $m_initGlobal;
    private $m_options;
    private static $MEDIA;
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

    private $m_includes;

    public function & setIncludeFileListListener(& $array){
        $g = & $this->m_includes;
        $this->m_includes = & $array;
        return $g;
    }
    public function & getIncludedFiles(){
        return $this->m_includes;
    }
    /**
     * 
     * @param string $file 
     * @return void 
     */
    public function include_once(string $file, $args=null){
        if (is_null($this->m_includes)){
            $this->m_includes = [];
        }
        if (($f = realpath($file)) && !key_exists($f, $this->m_includes)){
            $this->m_includes[$f] = 1;
            (function(){
                extract(func_get_arg(1));
                include(func_get_arg(0));
            })($f, $args ?? $this->get_include_args());
        }

    }
    protected function get_include_args(){
        return igk_environment()->get(IGKEnvironmentConstants::CSS_UTIL_ARGS) ?? [];   
    }

    /**
     * set theme colors
     * @param null|array $theme_colors 
     * @return void 
     */
    public function setThemeColors(?array $theme_colors){
        $this->m_themeColors = $theme_colors;
    }
    public function getImports(){
        return $this->m_imports;
    }
    /**
     * get theme color by name
     * @param string $theme_name 
     * @return ?array
     * @throws IGKException 
     */
    public function getThemeColorsByName(string $theme_name): ?array{
        if ($this->m_themeColors){
            return igk_getv($this->m_themeColors, $theme_name);
        }
        return null;
    }
    public function getCharset(){
        return $this->m_charset;
    }
    public function setCharset(?string $charset){
        $this->m_charset = $charset;
    }
    public function setNamespace(?string $namespace){
        $this->m_namespace = $namespace;
    }
    public function getNamespace(){
        return $this->m_namespace;
    }
    public function import($uri){
        if (null === $this->m_imports){
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
    public function supports(string $condition){
        return $this->getdef()->supports($condition);
    }
    /**
     * change the id of this doc theme
     * @param null|string $id 
     * @return void 
     */
    public function setId(?string $id){
        $this->m_id = $id;
        return $this;
    }

    /**
     * get the redering options
     */
    public function getRenderOptions(){
        return $this->m_options;
    }
    /**
     * set the rendering options
     * @param null|CssThemeOptions $options 
     * @return $this 
     */
    public function setRenderOptions(?CssThemeOptions $options=null){
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
    public function setThemeColor(string $color, string $value, $themeName='light', $def=null){
        $def = $def ?? $this->getdef();
        $root = & $this->getRootReference(); 
        $root['--'.$themeName.'-color-'.$color] = $value;
        
    }
    public function bindThemeColor(string $theme, ?array $colors){
        if (is_null($colors)){
            unset($this->m_bindThemeColor[$theme]);
        }else
        $this->m_bindThemeColor[$theme] = $colors;
    }
    /**
     * get :root reference
     */
    public function & getRootReference(){
        return $this->m_root_ref;
    }
    /**
     * set :root reference
     * @param mixed $ref 
     * @return void 
     */
    public function setRootReference(& $ref){
        $this->m_root_ref = & $ref;        
    }


    /**
     * get if is init global
     */
    public function getInitGlobal(){
        return $this->m_initGlobal;
    }
    /**
     * get global color definition used to render color on current context
     * @return void 
     */
    public function getGlobalColor(){
        return CssColorDef::getInstance();
    }
    /**
     * initialize global theme definition
     */
    public function initGlobalDefinition(bool $force=false){
        if ($force || !$this->getInitGlobal()){
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
    public function resetSysGlobal(){
        if (!defined("IGK_FORCSS")){
            $cl = & igk_app()->getDoc()->getTheme()->def->getCl();
            array_splice($cl, 0, count($cl)); 
        }
        $this->reset();
        if (($this->getDoc()->getSysTheme()=== $this) &&  $this->getInitGlobal()){
            CssUtils::InitSysTheme($this);
        }
        $this->m_initGlobal = false;
    }

    use  ArrayAccessSelfTrait;
    const INLINE_STYLE_KEY = "css://temp/rendering"; 
    /**
     * parent of this theme
     * @var mixed
     */
    var $parent;

    /**
     * get current theme resolver
     * @return mixed 
     */
    public function getResolver(){
        return $this->m_resolver;
    }
    /**
     * get resources list 
     * @return void 
     */
    public function &getRes(){
        $o = null;
        return $o;
    }
    ///<summary></summary>
    ///<param name="document"></param>
    ///<param name="id"></param>
    ///<param name="type" default="global"></param>
    /**
     * 
     * @param HtmlItemBase|null $document owner
     * @param string|id $id
     * @param string|false $type the default value is "global", if false the the is no need to initialize
     */
    public function __construct(?HtmlItemBase $document=null, ?string $id=null, $type = "global")
    {
        $this->m_id = $id ?? igk_create_guid();
        $this->m_document = $document;
        $this->m_type = $type;
        $this->m_istemp = $type === false;
        $this->_initialize();
    }
    public static function CreateTemporaryTheme($id)
    {
        $c = new HtmlDocTheme(null, $id);
        $c->m_istemp = true;
        return $c;
    }
    public function getIsTemp()
    {
        return $this->m_istemp;
    }
    /**
     * set color definition
     * @var array<string,string> $color <color name, color_value>
     * @exemple setColor(['indigo'=>'#323232'])  
     */
    public function setColors(array $color)
    {
        // if (is_string($color)) {
        //     $color = explode('|', $color);
        // }
        $cl = &$this->getCl();
        $cl = array_unique(array_filter(array_merge($cl, $color)));
    }
    public function resetColors()
    {
        $cl = &$this->getCl();
        $cl = [];
    }
    ///<summary>display value</summary>
    /**
     * display value
     */
    public function __toString()
    {
        return "HtmlDocTheme : [id:" . $this->m_id . ", type: {$this->m_type} ]";
    }
    public function bindFile($file)
    {
        igk_css_bind_file($this, null, $file, $this);
    }
    ///<summary>convert data to array</summary>
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

    ///<summary>load userialize data to to thme</summary>
    /**
     * load userialize data to to thme
     * @param array $data 
     * @return false 
     */
    public function load_data(array $data)
    {
        $this->m_def->load_data($data);
        if ($medias = igk_getv($data, self::MEDIA_KEY)) {
            //$this->m_medias = $medias;
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
    public function replaceMediaList(?array $list){
        $this->m_medias = $list;
        return $this;
    }
    ///<summary></summary>
    ///<param name="indent" default="true"></param>
    ///<param name="themeexport" default="false"></param>
    ///<param name="doc" default="null"></param>
    /**
     * get theme styling definition
     * @param \IGKHtmlDoc $doc document that host theme to export
     * @param bool $minfile the default value is true
     * @param bool $themeexport the default value is false 
     * @param ICssStyleContainer $systheme style container
     */
    private function _get_css_def(IGKHtmlDoc $doc, $minfile = false, $themeexport = false, ?ICssResourceResolver $resourceResolver=null,  ?ICssStyleContainer $systheme = null)
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

        if (!$this->noHeader  && $this->m_charset){ 
            $out .= sprintf('@charset %s;%s', $this->m_charset, "\n");
        }
        if ($this->m_namespace){ 
            $out .= sprintf('@namespace %s;%s', $this->m_namespace, "\n");
        }
        if (!$this->noHeader && $this->m_imports){
            $out.= CssUtils::RenderImport($this->m_imports);
        }

        $builder = new \IGK\Css\CssThemeResolver();
        $builder->theme = $this;
        $builder->parent = $systheme;
        $builder->resolver = $resourceResolver;    
        $this->m_resolver = $builder;
        $this->m_resolver->themeResolved = & $this->m_themingResolv;

        $v_opts = $this->getRenderOptions();
        $v_skips = ($v_opts ? $v_opts->skips: null) ?? [];

        // + | --------------------------------------------------------------------
        // + | render symbols
        // + |
        
        $s = $def->getSymbols();
        if (is_array($s)) {
            $v_cacherequire = igk_sys_cache_require();
            $tb = array();
            foreach ($s as $k => $v) {
                if (file_exists($k)) {
                    // $rk = igk_realpath($k);
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
            if (!empty($v_var_def))
                $out .= ":root{" . $v_var_def . "}";
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
            !$themeexport && $out .= "\n/* <!-- end:Rules --> */".$lineseparator;
        }

        // + | --------------------------------------------------------------------
        // + | render attributes
        // + | 
        $s = "";
        $tv = 0;
        $prefix = $this->prefix;
        $css_minifier = new CssMinifier;
        if ($attr = $def->getAttributes()) {
            foreach ($attr as $k => $v) {
                if (empty($v))
                    continue;  
                if (is_numeric($k) || empty($k)){

                    $s.= $css_minifier->minify($v);
                    continue;
                }
                $kv = trim($builder->treatThemeValue($v, $themeexport)); 
                if (!empty($kv)){
                    if ($prefix){
                        $k = str_replace('.','.'.$prefix, $k);
                    }
                    $s .= $k . "{" . $kv . "}" . $lineseparator;
                    $tv = 1;
                }
            }  
        }
        
        if ($tv) {
            !$themeexport && ($out .= "/* <!-- Attributes --> */" . $lineseparator);
            $out .= rtrim($s) . $lineseparator;;
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
     * map theme to definition
     * @param mixed $mapper 
     * @return void 
     */
    public function map($mapper, $systheme, $resourceResolver){
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
                    $mapper("def", $k , $kv);
                    $tv = 1;
                }
            }
        }
        if ($this->m_medias) {            
            foreach ($this->m_medias as $k => $v) {
                // map keys
                $m = $v->def;
                if (empty($m)) continue;
                $pm = [];
                foreach($m as $t=>$s){
                    $pm[$t] = $builder->treat($s, $themeexport);
                }
                $mapper("media", self::GetMediaName($k), $pm);               
            } 
        }
    }
    ///<summary></summary>
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
        // (($cl = get_class($this->m_document)) != IGKHtmlDoc::class)  && igk_die("class [" . $cl . "] not allowed\n ");
        $tab = null;
        $id = $this->m_document->getId();
        $app_info = igk_app()->settings->appInfo; 
               
        $docs = null;
        $themes = null;
        $uri = igk_io_request_uri();
        
        if (!$this->m_istemp && $app_info) {

            $docs = & $app_info->documents[$id];
            //$docs = igk_getv($app_info->documents, $id);

            if ($docs === null) {
                // attach array to document id 
                $docs = [];
                $app_info->getData()->documents[$id] = & $docs;
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
        // $this->_root_def= $this->def;
    }
    // private $_root_def;
    // public function check(){
        
    //     $r = $this->def === $this->_root_def;
    //     igk_wln("checking ..... ? ", $this->m_id, $r, $this->def, $this->_root_def);
    //     return $r;
    // }
    public function __debugInfo()
    {
        return [];
    }
    ///<summary></summary>
    ///<param name="id"></param>
    /**
     * 
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
    ///<summary></summary>
    ///<param name="style"></param>
    /**
     * 
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
    ///<summary></summary>
    ///<param name="cl"></param>
    ///<param name="value"></param>
    /**
     * 
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
    ///<summary>Add file to document theme</summary>
    /**
     * Add file to document theme
     */
    public function addFile(?BaseController $host, string $f, $temp = 1)
    {
        if ($host === null)
            igk_die("controller host must be defined");
        igk_css_reg_global_style_file($f, $this, $host, $temp);
    }
    ///<summary>add font package</summary>
    /**
     * add font package
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
    ///<summary>attach tempory css file</summary>
    /**
     * attach tempory css file
     * @return bool 
     */
    public function addTempFile($file)
    {
        if (!file_exists($file))
            return !1;
        $v_tfiles = & $this->m_def->getTempFiles();
        if (($g = igk_io_collapse_path($file)) && !in_array($g, $v_tfiles)) {
            $v_tfiles[] = $g; 
        }
        return !0;
    }
    ///<summary>add css file to render inline</summary>
    /**
     * add css file to render inline 
     * @param BaseController $host controller that host the file
     * @param string $f file path 
     */
    public function addInlineStyle($host, string $f)
    {
        if (!file_exists($f))
            return false;
        $ckey = self::INLINE_STYLE_KEY;
        $tab = $this->getParam($ckey);
        if ($tab === null)
            $tab = array();
        $f = igk_io_collapse_path($f);
        $m = $f.':'.$host;
        $hashContainer = new \IGK\System\HashContainer('sha256', function($a, $k, string $code){ 
            return $k == hash($code, $a->file.':'.$a->host);
        });
        if ($hashContainer->contains($m, $tab)){ 
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
    public function getInlineStyle($reset=false){

        $g = $this->getParam(self::INLINE_STYLE_KEY);
        if ($reset){
           $this->setParam(self::INLINE_STYLE_KEY, null);
        }
        return $g;
    }

    
    ///<summary></summary>
    /**
     * 
     */
    public function ClearChilds()
    {
        $this->m_def->clear();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function ClearFont()
    {
        $tab = $this->ft->Attributes;
        if (count($tab) > 0) {
            foreach ($tab as  $v) {
                if (is_object($v)) {
                    foreach ($v->Fonts as  $n) {
                        $f = igk_io_basedir($n->File);
                        if (file_exists($f))
                            @unlink($f);
                    }
                } else {
                    $f = igk_io_basedir($v);
                    if (file_exists($f))
                        @unlink($f);
                }
            }
            $this->ft->Attributes->Clear();
            $this->save();
        }
    }
    ///<summary></summary>
    ///<param name="minfile" default="false"></param>
    ///<param name="themeexport" default="false"></param>
    ///<param name="doc" default="null"></param>
    /**
     * get css definition
     * @param bool $minfile the default value is false
     * @param bool $themeexport the default value is false
     * @param ?ICssResourceResolver $resourceResolver
     * @param mixed $doc the default value is null
     */
    public function get_css_def(bool $minfile = false, bool $themeexport = false, 
        ?ICssResourceResolver $resourceResolver=null, $doc = null,
        ?DomHtmlDocTheme $parent = null)
    {
        $out = '';
        $el = $minfile ? IGK_STR_EMPTY : IGK_LF;
        $is_root = false;
        $doc = $doc ?? $this->m_document ?? igk_app()->getDoc();
        $v_parent = $parent ?? $this->parent;
        $this->m_themingResolv = false;

 
        $systheme = $doc->getSysTheme();
        $is_root = $this === $systheme;
        // $parent = $is_root ? null : (($v_parent instanceof self) && ($v_parent !== $this) ? $this->parent : $systheme);
        $parent = $is_root ? null : (($v_parent instanceof self) && ($v_parent !== $this) ? $v_parent : $systheme);
        \IGK\System\Diagnostics\Benchmark::mark("theme-export-def"); 
        $out = $this->_get_css_def($doc, $minfile, $themeexport, $resourceResolver, $parent);
        \IGK\System\Diagnostics\Benchmark::expect("theme-export-def", 0.100);
            
        if ($this->m_medias) {
            $out.= CssUtils::RenderMedia($this->m_medias, $this, $parent ?? $systheme, $minfile, $el, $is_root);
           
        } 
        $rtdef_root = array_merge(
            CssUtils::GetRootPropsArray($cl = $this->getCl() ?? []),
            CssUtils::GetRootPropsArray($props = $this->getProperties() ?? []), 
            $this->m_root_ref ?? []);
        
        if ($rtdef_root){
            ksort($rtdef_root);
            if ($this->m_options?->rootListener){
                $this->m_options->rootListener->store($rtdef_root);
            }else{
                $out.= sprintf(':root{%s}', igk_css_array_key_map_implode($rtdef_root));
            }
        }
   
        ///  TODO : theming definitions .
        if($this->m_bindThemeColor && $this->m_themingResolv){
            // resolv class 
            $out.= PHP_EOL.$this->_getThemingDefinition($systheme, $minfile, $el, $is_root);
            $this->m_themingResolv = false;
        }

        return rtrim($out);
    }
   
    /**
     * 
     * @return null|string 
     */
    public function getDefaultTheme(){
        return $this->m_default_theme;
    }
    /**
     * set default theme 
     * @param null|string $default_theme 
     * @return void 
     */
    public function setDefaultTheme(?string $default_theme){
        $this->m_default_theme = $default_theme;
    }
    private function _getThemingDefinition($systheme, $minfile, $el){
        $s = '';
        $v_default_theme = $this->getDefaultTheme() ;
        $bck = $this->getCl();
        $medias = $this->getMedias();
        $source_defs = [];
        $r = $this->getdef();
        $g = new HtmlDocTheme(null, "temp", "temporary");
        $v_source_media = ['medias'=>$medias,'initdef'=>null, 'init'=>false, 'source'=>$this];
        foreach($this->m_bindThemeColor as $theme_name=>$cl){
            $g->setColors($cl); 
            $g->m_medias = CssUtils::CloneMedia($medias);
            $s .= CssUtils::RenderMedia(
                $g->m_medias,
                $g, $systheme, $theme_name, $r->getAttributes(),
                $v_default_theme == $theme_name, $v_source_media, $minfile, $el);  
            $g->m_medias = null;
       
        }
        $this->setColors($bck);
        return $s;
    }
    ///<summary></summary>
    ///<param name="id"></param>
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
    ///<summary></summary>
    /**
     * 
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
    ///<summary></summary>
    /**
     * 
     */
    public function getAppend()
    {
        return "";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getAttributes()
    {
        igk_die(__METHOD__ . ". not avaiable for theme");
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * get color definitions
     * @return mixed|array colors
     */
    public function &getCl()
    {
        return $this->m_def->getCl();
    }
    ///<summary>return theme extra smalll media type</summary>
    public function getxsm_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::XSM_MEDIA);
    }
    ///<summary>return theme smal media type</summary>
    public function getsm_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
    }
    ///<summary>return theme large media type</summary>
    public function getlg_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::LG_MEDIA);
    }
    ///<summary>return theme extra large media type</summary>
    public function getxlg_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::XLG_MEDIA);
    }
    ///<summary>return theme extra extra large media type</summary>
    public function getxxlg_screen()
    {
        return $this->getMedia(HtmlDocThemeMediaType::XXLG_MEDIA);
    }
    /**
     * get printer media
     * @return mixed 
     */
    public function getptr(){
        return $this->getPrintMedia();
    }
    ///<summary></summary>
    ///<param name="key" default="null"></param>
    /**
     * 
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
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * get
     * @return mixed|array|object definition
     */
    public function &getdef()
    {
        return $this->m_def;
    }
    ///<summary> get the parent document</summary>
    /**
     *  get the parent document
     */
    public function getDoc()
    {
        return $this->m_document;
    }
    ///<summary>return registrated fontn</summary>
    /**
     * return registrated fontn
     */
    public function getFont()
    {
        return null;
    }
    ///<summary> return the id of the data</summary>
    /**
     *  return the id of the data
     */
    public function getId()
    {
        return $this->m_id;
    }
    ///<summary></summary>
    ///<param name="idk"></param>
    /**
     * 
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
    ///<summary></summary>
    ///<param name="idk"></param>
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
    ///<summary>get all registrated medias</summary>
    /**
     * get all registrated medias
     */
    public function getMedias()
    {
        return $this->m_medias;
    }
   
    ///<summary>get print media</summary>
    /**
     * get print media
     */
    public function getPrintMedia()
    {
        return $this->reg_media("print", null, 'print');
    }
   
    ///<summary></summary>
    /**
     * 
     */
    public function getRegChangedKey()
    {
        return __CLASS__ . "_" . $this->Name;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * get rules attached to theme definition
     * @return mixed|array rules
     */
    public function & getRules()
    {
        $sd = &$this->m_def->getRules();
        return $sd;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    /**
     * 
     * @param mixed $file
     */
    public function LoadThemeFromFile($file)
    {
        if (file_exists($file)) {
            include($file);
        }
    }

    ///<summary></summary>
    ///<param name="i"></param>
    /**
     * 
     * @param mixed $i
     */
    protected function _access_offsetExists($i): bool
    {
        if (isset($this->m_tc))
            return ($i >= 0) && ($i < count($this->m_tc));
        return !1;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
     * 
     * @param mixed $key
     */
    protected function _access_offsetGet($key)
    {
        return $this->def[$key];
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $key
     * @param mixed $value
     */
    protected function _access_offsetSet($key, $value)
    {
        if ($key == "file") {
            igk_die(__METHOD__ . " offset is file");
        }
        $def = & $this->getDef();
        if (is_null($key)){

            if (is_null($value)){
                unset($def[0]);
                return;
            }
            $l = $def[0];
            if ($l){
                $value = $l.$value;
                $def[''] = $value;
            }else 
                $def[] = $value;
            return;
        }
        $def[$key] = $value;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
     * 
     * @param mixed $i
     */
    protected function _access_offsetUnset($i)
    {
        if (isset($this->m_tc))
            unset($this->m_tc[$i]);
    }
    ///<summary></summary>
    /**
     * output 
     */
    public function output()
    {
        header("Content-Type:text/css");
        $s = $this->get_css_def();
        igk_wl($s);
    }
    ///<summary></summary>
    ///<param name="def"></param>
    ///<param name="name"></param>
    ///<param name="expression"></param>
    /**
     * 
     * @param mixed $def
     * @param mixed $name
     * @param mixed $expression
     */
    public function reg_keyFrames($def, $name, $expression)
    {
        $def->addRule("@-webkit-keyframes " . $name, $expression);
        $def->addRule("@-moz-keyframes " . $name, $expression);
        // $def->addRule("@-ms-keyframes " . $name, $expression);
        // $def->addRule("@-o-keyframes " . $name, $expression);
        $def->addRule("@keyframes " . $name, $expression);
    }
    ///<summary>register a media </summary>
    ///<param name="$name">name or condition</param>
    /**
     * register a media
     * @param mixed $name name or condition
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
    ///<summary></summary>
    ///<param name="cl"></param>
    /**
     * 
     * @param mixed $cl
     */
    public function removeColor($cl)
    {
        if (isset($this->cl[$cl])) {
            $this->cl[$cl] = null;
            $this->save();
        }
    }
    ///remove a specific font
    /**
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
                if (file_exists($f) && (unlink($f))) {
                    igk_notifyctrl()->addMsg(__("msg.fontfile.removed"));
                }
                $this->ft[$name] = null;
                $this->save();
                return true;
            }
        }
        return false;
    }
    
    ///<summary>reset all media</summary>
    /**
     * reset all media definition 
     */
    public function reset($save = false)
    { 
        $this->def->Clear();
        $cl = & $this->getCl();//->Clear();
        array_slice($cl, count($cl));
        if ($res = $this->res){            
            array_splice($res, 0, count($res));
        }  
        if ($rule = & $this->getRules()){
            array_splice($rule,0, count($rule));
        } 
        if ($this->m_medias)
        foreach ($this->m_medias as $v) {
            $v->Clear();
        }
        if ($save)
            $this->save();
    }
    ///<summary></summary>
    /**
     * clear all - 
     */
    public function resetAll()
    {
        $this->def->Clear(); 
        $this->m_medias = array();
        $this->_initMedia($this->m_id);
    }
    
    ///<summary></summary>
    ///<param name="file" default="null"></param>
    /**
     * 
     * @param mixed $file the default value is null
     */
    public function save($file = null)
    {
        if (($file == null) && empty($this->Name))
            return;
        $f = ($file == null) ? igk_io_syspath(IGK_RES_FOLDER . "/Themes/" . $this->Name . "." . IGK_DEFAULT_VIEW_EXT) : $file;
        $out = IGK_STR_EMPTY;
        $out .= "<?php" . IGK_LF;
        $out .= <<<EOF
// Theme Media creation
// Name : {$this->Name}
\$cl = get_class(\$this);
if (\$cl != 'HtmlDocTheme')
{
	igk_die("this file can be only included in HtmlDocTheme context");
}
EOF;
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
    ///protected the access to allow parent or child call via calluser func
    /**
     */
    protected function setdef(?IGKCssDefaultStyle $v)
    {
        if ($v === null) {
            igk_die("/!\\ bad ? " . ($v === null), __METHOD__);
        }
        $this->m_def = $v;
    }
    ///set properties
    /**
     * store document tempory property 
     */
    public function setProperty($name, $value)
    {
        $p = & $this->m_def->getParams();
        if (is_null($value)){ 
            unset($p[$name]);
        }else{
            $p[$name] = $value;
        } 
    }
 ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return mixed|array properties
     */
    public function & getProperties($k = null)
    {
        $g = & $this->m_def->getParams();
        if ($k) {
            if (isset($g[$k])){
                $g = & $g[$k];
                return $g;
            }
        } 
        return  $g;
    }
     ///<summary></summary>
    /**
     * 
     */
    public function getParam($key)
    {
        return $this->getProperties($key);
    }
    public function setParam($key, $value)
    {    
        $this->setProperty($key, $value);
        return $this;
    }
}
