<?php

namespace IGK\System\Html\Dom;

use IGK\Helper\IO;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGKCaches;
use IGKException;
use IGKResourceUriResolver;

class HtmlCoreJSScriptsNode extends HtmlNode
{
    private static $sm_instance;
    public static function getItem()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    public function __construct()
    {
        parent::__construct("igk:js-core-script");
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    protected function __AcceptRender($opt = null)
    {
        $document = igk_getv($opt, "Document");
        return true;
    }
    protected function __getRenderingChildren($options = null)
    {
        return null;
    }
    public function render($options = null)
    {
        $sb = new StringBuilder();
        if (igk_environment()->is("DEV")) {
            $sb->appendLine("<!-- core scripts -->");
            $sb->appendLine(self::GetCoreScriptContent());
            $sb->appendLine("<!-- :core scripts -->");
        } else {
            // production script
            $sb->appendLine(self::GetCoreScriptContent(igk_environment()->is("OPS")));
        }
        return $sb;
    }
    
    /**
     * get script content resolver
     * @param bool $production 
     * @return string|false 
     * @throws IGKException 
     */
    public static function GetCoreScriptContent($production = false)
    {
        $out = "";
        $exclude_dir = [];
        $resolver = IGKResourceUriResolver::getInstance();
        $tab = [
            [IGK_LIB_DIR . "/" . IGK_SCRIPT_FOLDER, "igk"],
            [IGK_LIB_DIR . "/Ext", "sys"],
        ];
        $d = rtrim(explode("?", igk_server()->REQUEST_URI)[0], "/");
        $rq = null;
        $resolverfc = null;
        $tag = null;
        $s = "";
        // $production = true;
        $production_file  = ""; 
        if (!$production) {
            $rq = count(array_filter(explode("/", $d))) . "/:";
            $resolverfc = function ($f) use ($resolver, &$s, &$tag) {
                $ext = Path::GetExtension($f);
                $u = $resolver->resolve($f);
                switch (($ext)) {
                    case ".js";
                        $u .= "?v=" . IGK_VERSION;
                        $s .= "<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                        if ($tag != "igk") {
                            $s .= " defer";
                        }
                        $s .= " ></script>";
                        break;
                }
            };
        } else {
            $production_file = IGKCaches::js_filesystem()->getCacheFilePath("corejs:/igk.js");
            if (file_exists($production_file)){
                return file_get_contents($production_file);
            }
            $resolverfc = function ($f) use ($resolver, &$s) {
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

            if (file_exists($cache_path)) {
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
                igk_js_minify(file_get_contents(IGK_LIB_DIR."/Inc/js/eval.js"))
            ];
            $out = "<script type=\"text/javascript\" language=\"javascript\" >\n//<![CDATA[".$pif[0]."]]>\n</script>\n";
            $out.= "<script type=\"text/javascript\" language=\"javascript\" >\n".$pif[1]."\n</script>";
            igk_io_w2file($production_file, $out);
            $path = IGKCaches::js_filesystem()->getCacheFilePath("corejs-dist:/igk.js", ".js");
            igk_io_w2file($path, implode("\n", $pif));
        }
        return $out;
    }

    /**
     * 
     * @param mixed $data list of include folder
     * @param mixed $name 
     * @param bool $production 
     * @return string|false 
     * @throws IGKException 
     */
    public static function GetScriptContent($data, $uri, $name, $production = false){
 

        $out = "";
        $exclude_dir = [];
        $resolver = IGKResourceUriResolver::getInstance();
        $tab = $data;
        $d = rtrim(explode("?", igk_server()->REQUEST_URI)[0], "/");
        $rq = count(array_filter(explode("/", $d))) . "/:";

        $resolverfc = null;
        $tag = null;
        $s = "";
        // $production = true;
        $production_file  = "";

   
      
        if (!$production) {
            $resolverfc = function ($f) use ($resolver, &$s, &$tag) {
                $ext = Path::GetExtension($f);
                $u = $resolver->resolve($f);
                switch (($ext)) {
                    case ".js";
                        $u .= "?v=" . IGK_VERSION;
                        $s .= "<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                        if ($tag != "igk") {
                            $s .= " defer";
                        }
                        $s .= " ></script>";
                        break;
                }
            };
        } else {

            $production_file = IGKCaches::js_filesystem()->getCacheFilePath($name.":/data.js");
            if (0 && file_exists($production_file)){
                return file_get_contents($production_file);
            }
            $resolverfc = function ($f) use ($resolver, &$s) {
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
            $dir = $q;
            $cache_path = !$production ?
                IGKCaches::js_filesystem()->getCacheFilePath($rq.$name.$dir):
                null;

            if ( $cache_path && file_exists($cache_path)) {
                ob_start();
                include($cache_path);
                $out .= ob_get_contents();
                ob_end_clean();
            } 
            else {
                $s = "";
                IO::GetFiles($dir, "/\.(js|json|xml|svg|shader|txt)$/", true, $exclude_dir, $resolverfc);
                IO::WriteToFile($production_file, $s);
                $out .= $s;
            }
        }
        if ($production && !empty($out)){
            $pif = [
                igk_js_minify($out),
                igk_js_minify(file_get_contents(IGK_LIB_DIR."/Inc/js/eval.js"))
            ];
            $out = "<script type=\"text/javascript\" language=\"javascript\" >\n//<![CDATA[".$pif[0]."]]>\n</script>\n";
            $out.= "<script type=\"text/javascript\" language=\"javascript\" >\n".$pif[1]."\n</script>";
            igk_io_w2file($production_file, $out);
            $path = IGKCaches::js_filesystem()->getCacheFilePath($name."-dist:/script.js", ".js");
            igk_io_w2file($path, implode("\n", $pif));
        }
        return $out;
    }
}
