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

class CssThemeCompiler{
    /**
     * design mode
     * @var bool
     */
    var $designmode;

    /**
     * global color pointer
     * @var mixed
     */
    var $glc;

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

     public function __construct($colors, $designmode=false)
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
    public function treatValue(string $value, ICssStyleContainer $theme, ?ICssStyleContainer $parentTheme=null){
        $result = "";
        return $result;
    }

    /**
     * return compiler data
     * @param string $value 
     * @return bool 
     */
    public static function CanCompile(string $value){
       return (strpos($value, "[")!==false) || (strpos($value, "{")!==false);
    }

    /**
     * compile and render css 
     * @param HtmlDocTheme $theme 
     * @param string $docid 
     * @param string $cacheid cache item
     * @param bool $css_cache use css cache
     * @param bool $minfile use css cache
     * @param bool $theme_export use css cache
     * @return int 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     */
    public static function CompileAndRenderTheme(HtmlDocTheme $theme, 
        string $docid, 
        string $cacheid, 
        bool $css_cache, 
        bool $minfile, 
        bool $theme_export,
        ?ICssResourceResolver $resolver = null
        ){
        $src_sys = "";
        $cf = IGKCaches::css_filesystem()->getCacheFilePath($cacheid, ".css.cache");
        $express_cf = IGKCaches::css_filesystem()->getCacheFilePath($cacheid."/expression", ".css.cache");
        $no_systheme = 0;
        if ($css_cache && file_exists($cf)){
            // ob_start();
            // echo "/* systheme from cache */\n";
            $array = $theme->to_array();
            $data = unserialize(file_get_contents($cf)); // include($cf);
            $theme->load_data($data);
            $mtime = filemtime($cf);           
         
            $must_recompile = 0;
            if ($cfile = igk_getv($array, IGKCssDefaultStyle::FILES_RULE)){
                $cfile = igk_io_expand_path($cfile);
                $files = explode(";", $cfile);
                foreach($files as $f){
                    if (!file_exists($f))
                        continue;
                    if (filemtime($f) > $mtime){
                        $must_recompile = true;  
                        break;
                    }
                } 
                if ($must_recompile){
                    $theme->getDef()->setFiles($cfile);
                }
            }
            if ( !$must_recompile && file_exists($express_cf )){
                $src_sys = file_get_contents($express_cf);
            }
            else{ 
                igk_css_bind_sys_global_files($theme);
                $src_sys = $theme->get_css_def($minfile, $theme_export, $resolver);         
                igk_io_w2file($express_cf, $src_sys, true);
                igk_io_w2file($cf, serialize($theme->to_array()));          
            }
            $no_systheme = 1;      
            echo igk_css_get_core_comment($docid);
            echo $src_sys;
        }else {
            igk_css_bind_sys_global_files($theme);
            igk_css_load_theme($theme); 
            $src_sys = $theme->get_css_def($minfile, $theme_export, $resolver);    
            if (!$theme_export){
                igk_io_w2file($express_cf, $src_sys, true);
                igk_io_w2file($cf, serialize($theme->to_array()));
            }
            $no_systheme = 1;
            echo igk_css_get_core_comment($docid);
            echo $src_sys;
        }
        return $no_systheme;
    }
}