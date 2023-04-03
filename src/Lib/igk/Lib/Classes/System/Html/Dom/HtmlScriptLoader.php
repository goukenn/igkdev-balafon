<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlScriptLoader.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\Helper\IO;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGKCaches;
use IGKException;
use IGKResourceUriResolver;

/**
 * script loader 
 * @package IGK\System\Html\Dom
 */
class HtmlScriptLoader{

    var $options;

    /**
     * directory to load
     * @var mixed
     */
    var $dirs;

    /**
     * production mode
     * @var bool
     */
    var $production;

    /**
     * 
     * @var ?array excluded directory options
     */
    var $excludir;

    public function getExcludeDir() : array{
        return $this->excludir ? $this->excludir : igk_sys_js_exclude_dir(); 
    }


    public function getscript($options = null){
        return self::LoadScripts($this->dirs, $options, $this->production, $this->getExcludeDir());
    }

    /**
     * load script 
     * @param array $tab array of directory 
     * @param mixed $options render option
     * @param bool $production production mode 
     * @param array $exclude_dir list of excluded directory
     * @param string $cachePath cache path
     * @return string|false result
     * @throws IGKException 
     */
    public static function LoadScripts($tab, $options=null, $production=false, $exclude_dir=[], $cachePath="corejs:/igk.js", $defer=0){  
  

        $no_page_cache = igk_setting()->no_page_cache();
        $out = ""; 
        $uri = igk_server()->REQUEST_URI ?? "";
        $resolver = IGKResourceUriResolver::getInstance();
        $firstEval = $options ? igk_getv($options, "jsOpsFirstEval", true) : true;

        if($options && $firstEval)
            $options->jsOpsFirstEval = false;
        // 
        // default library directory             
        // append script to ignore
        //       
        $d = rtrim(explode("?", $uri)[0], "/");
        $rq = null;
        $resolverfc = null;
        $tag = null;
        $s = "";
        $lf = $options ? $options->LF : "";
        $tabstop = HtmlRenderer::GetTabStop($options);        
        $production_file  = ""; 
        if (!$production) {
            $rq = count(array_filter(explode("/", $d))) . "/:";
            $resolverfc = function ($f) use ($resolver, &$s, &$tag, $lf, $tabstop, $defer) {               
                $g = basename($f); 
                if (strpos($g, ".") === 0){
                    // + | ignore hidden file
                   return;
                } 
                $ext = Path::GetExtension($f);
                $u = $resolver->resolve($f);
                // if (!file_exists($u)){
                //     igk_wln_e("missing resolve file .... ".$u);
                // }
               
                switch (($ext)) {
                    case ".js";
                        $u .= "?v=" . IGK_VERSION;
                        $s .= $tabstop."<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                        $is_core = (($tag=="igk" ) && (basename($f) == "igk.js"));
                        $defer = $defer || !$is_core; // (($tag=="igk" ) && (basename($f) != "igk.js"));
                        if ($defer) { 
                            $s .= " defer";
                        }  
                        $s .= " ></script>".$lf;
                        break;
                }
            };
        } else {
            $production_file = IGKCaches::js_filesystem()->getCacheFilePath($cachePath, ".js"); 
            if (!$no_page_cache  && file_exists($production_file)){                
                return file_get_contents($production_file);
            }
            $assets = [];
            $resolverfc = function ($f) use (&$s, & $assets) {
                if (strpos(basename($f), '.')===0){ 
                    return;
                } 
                $ext = Path::GetExtension($f);
                $F = igk_io_collapse_path($f);
                switch (($ext)) {
                    case ".js";
                        $s .= IGK_START_COMMENT."F: ". $F."".IGK_END_COMMENT.IGK_LF;
                        $s .= file_get_contents($f);
                        break;
                    default:
                        //resolv to asset folder
                        $assets[] = $f;
                        break;
                }
            };
        } 
       
        while ($q = array_shift($tab)) {
            $dir = $q[0];
            $tag = $q[1];
            if ($dir && key_exists($dir, $exclude_dir)){
                continue;
            }

            $cache_path = IGKCaches::js_filesystem()->getCacheFilePath($rq . $dir);

            if (!$no_page_cache && file_exists($cache_path)) {
                ob_start();
                include($cache_path);
                $out .= ob_get_contents();
                ob_end_clean();
            } else {
                $s = ""; 
                $dirs = [];
                $dirs[] = $dir."/igk.js";
                $dirs[] = $dir."/polyfill.js";
                $dirs[] = $dir."/system/ctrl/ctrl.js";
                $exclude_dir += array_fill_keys($dirs,1); 
                IO::GetFiles($dir, self::GetLoadingAssetRegex() , true, $exclude_dir, $resolverfc);        
                IO::WriteToFile($cache_path, $s);
                $out .= $s; 
            }
        }
        if ($production && !empty($out)){
            $pif = [
                igk_js_minify($out),
                $firstEval ? igk_js_minify(file_get_contents(IGK_LIB_DIR."/Inc/js/eval.js")) : "igk.js.initEmbededScript()"
            ];
            $out = $tabstop."<script type=\"text/javascript\" language=\"javascript\" >\n//<![CDATA[".$pif[0]."]]>\n</script>".$lf;
            $out.= $tabstop."<script type=\"text/javascript\" language=\"javascript\" >\n".$pif[1]."\n</script>".$lf;
            if (!$no_page_cache){
                IO::WriteToFile($production_file, $out);
            }
        } 
        return $out;
    }
    /**
     * system loading accept regex 
     * @return string 
     */
    public static function GetLoadingAssetRegex(){

        return "/\.(js|json|xml|svg|shader|txt)$/";
    }

    /**
     * 
     * @param string $file 
     * @param string $uri 
     * @return string 
     * @throws IGKException 
     */
    public static function GetModuleInlineScriptContent(string $file, $uri = "/"){
        $sb = new StringBuilder; 
        $sb->appendLine("(function(){");        
        $mod_info = [
            "path"=>igk_io_collapse_path($file),
            "uri"=>$uri
        ];
        $sb->appendLine("const __MODULE__ = ".json_encode((object)$mod_info, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . ";");
        $sb->appendLine(file_get_contents($file));
        $sb->appendLine("})();");
        return "".$sb;
    }

    /**
     * get core script exception
     * @return never 
     * @throws NotImplementException 
     */
    public static function GetCoreScriptInlineContent(){
        throw new NotImplementException(__METHOD__);
    }
}