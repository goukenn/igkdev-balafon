<?php

namespace IGK\System\Html\Dom;

use IGK\Helper\IO;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
use IGKCaches;
use IGKResourceUriResolver;

/**
 * script loader 
 * @package IGK\System\Html\Dom
 */
class HtmlScriptLoader{

    var $options;

    var $dirs;

    var $production;


    public function getscript($options = null){
        return $this->LoadScripts($this->dirs, $options, $this->production);
    }

    public static function LoadScripts($tab, $options=null, $production=false, $cachePath="corejs:/igk.js"){
 
        $no_page_cache = igk_setting()->no_page_cache();
        $out = ""; 
        $uri = igk_server()->REQUEST_URI ?? "";
        $resolver = IGKResourceUriResolver::getInstance();
        $firstEval = $options ? igk_getv($options, "jsOpsFirstEval", true) : true;

        if($options && $firstEval)
            $options->jsOpsFirstEval = false;
        

        // default library directory     
        // 
        // append script to ignore
        // 
        $exclude_dir = igk_sys_js_ignore();
       
        $d = rtrim(explode("?", $uri)[0], "/");
        $rq = null;
        $resolverfc = null;
        $tag = null;
        $s = "";
        $lf = $options->LF;
        $tabstop = HtmlRenderer::GetTabStop($options);

        // igk_wln_e("no cache page : ", $no_page_cache);
        // $production = true;
        $production_file  = ""; 
        if (!$production) {
            $rq = count(array_filter(explode("/", $d))) . "/:";
            $resolverfc = function ($f) use ($resolver, &$s, &$tag, $lf, $tabstop) {
                $g = basename($f);
                // ignore all file that start with . 
                if (strpos($g, ".") === 0){
                   return;
                }
                $ext = Path::GetExtension($f);
                $u = $resolver->resolve($f);
                switch (($ext)) {
                    case ".js";
                        $u .= "?v=" . IGK_VERSION;
                        $s .= $tabstop."<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                        if ($tag != "igk") {
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
            $resolverfc = function ($f) use ($resolver, &$s, $lf, $tabstop) {
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
            $out = $tabstop."<script type=\"text/javascript\" language=\"javascript\" >\n//<![CDATA[".$pif[0]."]]>\n</script>".$lf;
            $out.= $tabstop."<script type=\"text/javascript\" language=\"javascript\" >\n".$pif[1]."\n</script>".$lf;
            if (!$no_page_cache){
                // IO::WriteToFile($production_file, $out);
                // $path = IGKCaches::js_filesystem()->getCacheFilePath("corejs-dist:/igk.js", ".js");
                IO::WriteToFile($production_file, $out);
            }
        } 
        return $out;
    }
}