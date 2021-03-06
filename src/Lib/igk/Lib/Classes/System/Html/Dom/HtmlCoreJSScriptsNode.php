<?php

namespace IGK\System\Html\Dom;

use IGK\Helper\IO;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGKCaches;
use IGKException;
use IGKResourceUriResolver;

/**
 * core script rendering
 * @package IGK\System\Html\Dom
 */
final class HtmlCoreJSScriptsNode extends HtmlNode
{
    private static $sm_instance;
    public static function getItem()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    private function __construct(){
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
        return $this->getIsVisible() && igk_getv($opt, "Document");        
    }
    protected function __getRenderingChildren($options = null)
    {
        return null;
    }
    public function render($options = null)
    { 
        $tabstop = "";
        $bck_def = false;
        $bck_def = $options->Depth;
        $options->Depth = max(0, $options->Depth - 1);
      
        $tabstop = HtmlRenderer::GetTabStop($options);
        $sb = new StringBuilder();
        $script = self::GetCoreScriptContent($options, igk_environment()->is("OPS"));
        if (igk_environment()->isDev()) {
            $sb->appendLine($tabstop."<!-- core scripts -->");
            $sb->appendLine($script);
            $sb->appendLine($tabstop."<!-- end:core scripts -->");  
        } else {
            // production script
            $sb->appendLine($script);
        }
        if($bck_def)
            $options->Depth = $bck_def;
        return $sb;
    }
    
    /**
     * get script content resolver
     * @param bool $production 
     * @return string|false 
     * @throws IGKException 
     */
    public static function GetCoreScriptContent($options, $production = false)
    {
        return HtmlScriptLoader::LoadScripts( [
            [IGK_LIB_DIR . "/" . IGK_SCRIPT_FOLDER, "igk"],
            [IGK_LIB_DIR . "/Ext", "sys"],
        ], $options, $production);       
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

        $regex = "/\.(js|json|xml|svg|shader|txt)$/";

        while ($q = array_shift($tab)) {
            $dir = $q;
            if ($cache_path = !$production ?  IGKCaches::js_filesystem()->getCacheFilePath($rq.$name.$dir): null){
                if ( file_exists($cache_path)) {
                    ob_start();
                    include($cache_path);
                    $out .= ob_get_contents();
                    ob_end_clean();
                } else {
                    $s = "";
                    IO::GetFiles($dir, $regex , true, $exclude_dir, $resolverfc);
                    IO::WriteToFile($cache_path, $s);                 
                    $out .= $s;
                }
            }
            else {
                $s = "";
                IO::GetFiles($dir, $regex, true, $exclude_dir, $resolverfc);
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
