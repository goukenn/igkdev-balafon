<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlScriptLoader.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\Helper\IO;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
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
        $lf = $options->LF;
        $tabstop = HtmlRenderer::GetTabStop($options);        
        $production_file  = ""; 
        if (!$production) {
            $rq = count(array_filter(explode("/", $d))) . "/:";
            $resolverfc = function ($f) use ($resolver, &$s, &$tag, $lf, $tabstop, $defer) {               
                $g = basename($f); 
                if (strpos($g, ".") === 0){
                   return;
                }
                $ext = Path::GetExtension($f);
                $u = $resolver->resolve($f);
                switch (($ext)) {
                    case ".js";
                        $u .= "?v=" . IGK_VERSION;
                        $s .= $tabstop."<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                        if (($tag != "igk") || $defer) {
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
            $resolverfc = function ($f) use (&$s) {
                $ext = Path::GetExtension($f);
                $F = igk_io_collapse_path($f);
                switch (($ext)) {
                    case ".js";
                        $s.= IGK_START_COMMENT."F: ". $F."".IGK_END_COMMENT.IGK_LF;
                        $s .= file_get_contents($f);
                        break;
                    default:
                        //resolv to asset folder
                        break;
                }
            };
        } 
       
        while ($q = array_shift($tab)) {
            $dir = $q[0];
            $tag = $q[1];
            $cache_path = IGKCaches::js_filesystem()->getCacheFilePath($rq . $dir);

            if (!$no_page_cache && file_exists($cache_path)) {
                ob_start();
                include($cache_path);
                $out .= ob_get_contents();
                ob_end_clean();
            } else {
                $s = "";
                IO::GetFiles($dir, "/\.(js|json|xml|svg|shader|txt)$/", true, $exclude_dir, $resolverfc);        
                IO::WriteToFile($cache_path, $s);
                $out .= $s;
            }
        }
        if ($production && !empty($out)){
            $pif = [
                igk_js_minify($out),
                $firstEval ? igk_js_minify(file_get_contents(IGK_LIB_DIR."/Inc/js/eval.js")) : "igk.js.initEmbededScript()"
            ];
            $out = $tabstop."<script type=\"text/javascript\" language=\"javascript\" defer >\n//<![CDATA[".$pif[0]."]]>\n</script>".$lf;
            $out.= $tabstop."<script type=\"text/javascript\" language=\"javascript\" defer >\n".$pif[1]."\n</script>".$lf;
            if (!$no_page_cache){
                // IO::WriteToFile($production_file, $out);
                // $path = IGKCaches::js_filesystem()->getCacheFilePath("corejs-dist:/igk.js", ".js");
                IO::WriteToFile($production_file, $out);
            }
        } 
        return $out;
    }


 
}