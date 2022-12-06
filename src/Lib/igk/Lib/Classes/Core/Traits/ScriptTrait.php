<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScriptTrait.php
// @date: 20221202 15:22:19
namespace IGK\Core\Traits;

use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlScriptLoader;
use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\Core\Traits
*/
trait ScriptTrait{
     /**
     * 
     */
    public static function GetCoreScriptInlineContent($manager):?string{    
        return self::GetScriptInlineContent(self::GetCoreScriptDirs(), $manager);       
    }
    /**
     * get core script folders
     * @return string[][] 
     */
    public static function GetCoreScriptDirs(){
        return  [
            [IGK_LIB_DIR . "/" . IGK_SCRIPT_FOLDER, "igk"],
            [IGK_LIB_DIR . "/Ext", "sys"],
        ];
    }
     /**
     * get script content resolver
     * @param bool $production 
     * @return string|false 
     * @throws IGKException 
     */
    public static function GetCoreScriptContent($options, $production = false)
    { 
        return HtmlScriptLoader::LoadScripts(self::GetCoreScriptDirs(), $options, $production, igk_sys_js_exclude_dir());       
    }

     /**
     * get script content 
     * @param mixed $tab 
     * @return null|string 
     * @throws IGKException 
     */
    public static function GetScriptInlineContent($tab, $manager): ?string{
        $out = "";
        $s = ""; 
        $lf = PHP_EOL;
        $exclude_dir = igk_sys_js_exclude_dir();
        $allowHiddenFile = $manager ? $manager->allowHiddenFile : false;        
        $resolverfc = function ($f) use (&$s, &$tag, $lf, $manager, $allowHiddenFile) {         
            if (!$allowHiddenFile && (strpos(basename($f), ".") === 0)){
               return;
            }
            $ext = Path::GetExtension($f); 
            switch (($ext)) {
                case ".js";                    
                    $s .= "// ".igk_io_collapse_path($f).$lf;                     
                    $s .= file_get_contents($f).$lf;                   
                    break;
                default:
                    if ($manager){
                        $manager->addAssets($f);
                    }
                break;
            }
        };
    
        while ($q = array_shift($tab)) {
            $dir = $q[0];  
            if ($files = IO::GetFiles($dir, "/\.(js|json|xml|svg|shader|txt)$/", true, $exclude_dir)){
                array_map($resolverfc, $files);
                $out .= $s . "\n";  
            }  
            $s = "";
        }
        return $out;
    }
   
}