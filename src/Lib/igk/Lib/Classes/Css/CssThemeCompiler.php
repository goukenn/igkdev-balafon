<?php
namespace IGK\Css;

use IGK\System\Html\Dom\HtmlDocTheme;
use IGKCaches;
use IGKCssDefaultStyle;

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

    public static function CompileAndRenderTheme(HtmlDocTheme $theme, string $docid, string $cacheid){
        $src_sys = "";
        $cf = IGKCaches::css_filesystem()->getCacheFilePath($cacheid, ".css.cache");
        $express_cf = IGKCaches::css_filesystem()->getCacheFilePath($cacheid."/expression", ".css.cache");
        $no_systheme = 0;
        if (!igk_setting()->no_css_cache && file_exists($cf)){
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
                    if (filemtime($f) > $mtime){
                        $must_recompile = true;  
                        break;
                    }
                } 
                if ($must_recompile){
                    $theme->getDef()->setFiles($cfile);
                }
            }
           // igk_wln_e(":---", $data, $array, $cfile, $must_recompile, $theme->to_array());
            if (!$must_recompile && file_exists($express_cf )){
                $src_sys = file_get_contents($express_cf);
            }
            else{
                igk_css_bind_sys_global_files($theme);
                $src_sys = $theme->get_css_def();         
                igk_io_w2file($express_cf, $src_sys, true);
                igk_io_w2file($cf, serialize($theme->to_array()));          
            }
            $no_systheme = 1;      
            echo igk_css_get_core_comment($docid);
            echo $src_sys;
        }else {
            igk_css_bind_sys_global_files($theme);
            igk_css_load_theme($theme);
            $src_sys = $theme->get_css_def();         
            igk_io_w2file($express_cf, $src_sys, true);
            igk_io_w2file($cf, serialize($theme->to_array()));
            $no_systheme = 1;
            echo igk_css_get_core_comment($docid);
            echo $src_sys;
        }
        return $no_systheme;
    }
}