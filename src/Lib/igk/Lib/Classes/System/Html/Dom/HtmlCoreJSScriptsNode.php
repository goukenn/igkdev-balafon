<?php
namespace IGK\System\Html\Dom;

use IGK\Helper\IO;
use IGK\System\IO\StringBuilder;
use IGKCaches;
use IGKResourceUriResolver;

class HtmlCoreJSScriptsNode extends HtmlNode{
    private static $sm_instance;
    public static function getItem(){
        if (self::$sm_instance == null){
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
    public function render($options = null){
        $sb = new StringBuilder();
        if (igk_environment()->is("DEV"))
        {
            $sb->appendLine("<!-- core scripts -->");        
            $sb->appendLine(self::GetCoreScriptContent());
            $sb->appendLine("<!-- :core scripts -->");    
        }
        else {
            // production script
            $sb->appendLine(self::GetCoreScriptContent());
        }
        return $sb;
    }
    public static function GetCoreScriptContent(){
        $out = "";
        $exclude_dir = [];
        $resolver = IGKResourceUriResolver::getInstance();
        $tab = [
            [IGK_LIB_DIR."/".IGK_SCRIPT_FOLDER, "igk"],
            [IGK_LIB_DIR."/Ext", "sys"],
        ];
        $d =rtrim(explode("?", igk_server()->REQUEST_URI)[0], "/");
        $rq = count(array_filter(explode("/", $d)))."/:"; 
        // echo "log : ".$rq . " bbbb:".$d;

        while($q = array_shift($tab)){
            $dir = $q[0];
            $tag = $q[1];
        $cache_path = IGKCaches::js_filesystem()->getCacheFilePath($rq.$dir);

        if (file_exists($cache_path)){
            ob_start();
            include($cache_path);
            $out .= ob_get_contents();
            ob_end_clean();
        }
        else {
            $s = "";
            IO::GetFiles($dir, "/\.js$/", true, $exclude_dir, function($f) use ($resolver, & $s, $tag){            
                $u = $resolver->resolve($f)."?v=".IGK_VERSION;
                $s.="<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                if ($tag != "igk"){
                    $s.= " defer";
                }
                $s.=" ></script>";
            });
            IO::WriteToFile($cache_path, $s);
            $out .= $s;
        }
    }
        return $out;
    }
}