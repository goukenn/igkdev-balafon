<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssThemeCompiler.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Css;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use Exception;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGKCaches;
use IGKCssDefaultStyle;
use IGKException;
use ReflectionException;

/**
* auto generate doc.
* @package IGK\Css
*/
class CssThemeCompiler
{
    /**
     * design mode
     * @var bool
     */
    var $designmode;
    /**
     * global color pointer
     * @var mixed
     */
    var $gcl;
    /**
     * 
     * @var mixed
     */
    var $start;
    /**
     * last time 
     * @var double
     */
    var $last;
    /**
     * resolv
     * @var array
     */
    var $resolv = [];

    /**
    * .ctr
    * @param mixed $colors
    * @param mixed $designmode
    */
    public function __construct($colors, $designmode = false)
    {
        $this->designmode = $designmode;
        $this->gcl = $colors;
    }
    /**
     * 
     * @param string $value 
     * @param ICssStyleContainer $theme 
     * @param null|ICssStyleContainer $parentTheme 
     * @return string 
     */

    public function treatValue(string $value, ICssStyleContainer $theme, ?ICssStyleContainer $parentTheme = null)
    {
        $result = "";
        return $result;
    }
    /**
     * return compiler data
     * @param string $value 
     * @return bool 
     */

    public static function CanCompile(string $value)
    {
        return (strpos($value, "[") !== false) || (strpos($value, "{") !== false);
    }
    /**
     * compile and render css 
     * @param HtmlDocTheme $theme 
     * @param string $docid 
     * @param string $cacheid cache item
     * @param bool $css_cache use css cache
     * @param bool $minfile use css cache
     * @param bool $theme_export use css cache
     * @return int 1 = no_systheme, 0 = systheme
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */

    public static function CompileAndRenderTheme(
        HtmlDocTheme $theme,
        string $docid,
        string $cacheid,
        bool $css_cache,
        bool $minfile,
        bool $theme_export,
        ?ICssResourceResolver $resolver = null
    ) {
        $src_sys = "";
        $cf = IGKCaches::css_filesystem()->getCacheFilePath($cacheid, ".css.cache");
        $express_cf = IGKCaches::css_filesystem()->getCacheFilePath($cacheid . "/expression", ".css.cache");
        $no_systheme = 0;
        $render_f = false;
        $lf = $minfile ? IGK_LF: "";
        
        if ($css_cache && igk_io_file_exists($cf, true)) {
            // + | check if one of included file changed
            $array = $theme->to_array();
            if (($data = unserialize(file_get_contents($cf)))!==false)
                $theme->load_data($data);
            $mtime = filemtime($cf); 
            $must_recompile = 0;
            if ($cfile = igk_getv($array, IGKCssDefaultStyle::FILES_RULE)) {
                $cfile = igk_io_expand_path($cfile);
                $files = array_map(CssHelper::MapToFileCallback(), 
                    explode(";", $cfile)
                );
                if ($must_recompile = IGKCaches::CheckCaches($files, $mtime)){
                    $theme->getDef()->clear();
                    $theme->getDef()->setFiles($cfile);
                } 
            } 
            if (!$must_recompile && igk_io_file_exists($express_cf, true)) {
                $src_sys = file_get_contents($express_cf); 
            } else {
                // igk_css_bind_sys_global_files($theme);
                // igk_css_load_theme($theme);
                // $src_sys = $theme->get_css_def($minfile, $theme_export, $resolver);
                // // + | cache expression
                // igk_io_w2file($express_cf, $src_sys, true);
                // // + | cache core 
                // igk_io_w2file($cf, serialize($theme->to_array()));
                $src_sys = self::CacheCssStoreAndExport($express_cf, $cf, $theme, $minfile, $theme_export, $resolver);  
            } 
            $render_f = true;
        } else {
            $src_sys = self::CacheCssStoreAndExport($express_cf, $cf, $theme, $minfile, $theme_export, $resolver); 
            //igk_wln_e(__FILE__.":".__LINE__ ,'3', $src_sys);          
            $render_f = true;
        }
        if ($render_f){
            $no_systheme = 1;
            echo igk_css_get_core_comment($docid).$lf;
            echo $src_sys;
        }
        return $no_systheme;
    }

    /**
    * auto generate doc.
    * @param mixed $express_cf
    * @param mixed $cf
    * @param mixed $theme
    * @param mixed $minfile
    * @param mixed $theme_export
    * @param mixed $resolver
    */

    static function CacheCssStoreAndExport($express_cf, $cf, $theme, $minfile, $theme_export, $resolver){
        igk_css_bind_sys_global_files($theme);
        igk_css_load_theme($theme);
        $src_sys = $theme->get_css_def($minfile, $theme_export, $resolver);
        if (!$theme_export) {
            igk_io_w2file($express_cf, $src_sys, true);
            igk_io_w2file($cf, serialize($theme->to_array()));
        }
        return $src_sys;
    }
}