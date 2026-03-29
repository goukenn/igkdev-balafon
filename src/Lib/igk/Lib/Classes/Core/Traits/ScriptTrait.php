<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScriptTrait.php
// @date: 20221202 15:22:19
namespace IGK\Core\Traits;
use Exception;
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlScriptLoader;
use IGK\System\IO\IAssetManager;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherPattern;
use IGKException;
use IGKValidator;
/**
 * 
 * @package IGK\Core\Traits
 */
/**
* auto generate doc.
* @package IGK\Core\Traits
*/
trait ScriptTrait
{
    /**
    * auto generate doc.
    */    public static function GetCoreScriptInlineContent($manager): ?string
    {
        return self::GetScriptInlineContent(self::GetCoreScriptDirs(), $manager);
    }
    /**
     * get core script folders
     * @return string[][] 
     */
    public static function GetCoreScriptDirs()
    {
        return  [
            [IGK_LIB_DIR . "/" . IGK_SCRIPT_FOLDER, 'igk'],
            [IGK_LIB_DIR . "/Ext", 'sys'],
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
     * @param IAssetManager $manager IAssetManager
     * @return null|string 
     * @throws IGKException 
     */
    public static function GetScriptInlineContent($tab, ?IAssetManager $manager = null): ?string
    {
        $out = "";
        $s = "";
        $lf = PHP_EOL;
        $exclude_dir = igk_sys_js_exclude_dir();
        $allowHiddenFile = $manager ? $manager->allowHiddenFile : false;
        $references = [];
        $sources = []; //sources to load  
        $_autoloads_dir = [];
        $resolverfc = function ($f) use (&$s, &$tag, &$references, $lf, $manager, $allowHiddenFile, & $sources, & $_autoloads_dir) {
            if (!$allowHiddenFile && (strpos(basename($f), ".") === 0)) {
                return;
            }
            $ext = Path::GetExtension($f);
            switch (($ext)) {
                case ".js":
                    if (isset($sources[$f])){
                        return;
                    }
                    if (!isset($_autoloads_dir[$dir = dirname($f)])){
                        $_autoloads_dir[$dir] = 1;
                        if (file_exists($fjson = $dir.'/__autoload.json')){
                            $r = json_decode(file_get_contents($fjson), true);
                            $rg = igk_extract_obj($r, 'required|ignore');
                            $_autoloads_dir[$dir] = $rg; 
                        }
                    }
                    $rg = $_autoloads_dir[$dir];
                    $b_name = basename($f);
                    if (is_object($rg) && in_array($b_name, $rg->ignore ?? [])){
                        return;
                    }
                    if (is_object($rg) && ($required = igk_getv($rg->required, $b_name))){
                        foreach($required as $tf){
                            $tff = Path::CombineAndFlattenPath($dir, $tf);
                            // $sources[] = 'console.log("loading '.$tff.' ");';
                            if (($f != $tff) && !isset($sources[$tff])){
                                $sources[$tff] = self::TreatJSSource($tff, file_get_contents($tff), $references);
                            }
                        }
                    } 
                    $sources[$f] = 
                    // $s .= "// " . igk_io_collapse_path($f) . $lf;
                    // $ts = file_get_contents($f);
                    self::TreatJSSource($f, file_get_contents($f), $references);
                    // //treat source file             
                    // $s .= $ts . $lf;
                    break;
                default:
                    if ($manager instanceof IAssetManager) {
                        $manager->addAssets($f);
                    }
                    break;
            }
        };
        while ($q = array_shift($tab)) {
            $dir = $q[0];
            if ($files = IO::GetFiles($dir, "/\.(js|json|xml|svg|shader|txt)$/", true, $exclude_dir)) {
                array_map($resolverfc, $files);
                $out .= $s . "\n";
            }
            $s = "";
        }
        // - merge source 
        if (is_null($manager) && ($references)) {
            $sb = new StringBuilder;
            $r = 0;
            $sb->appendLine('(function(){');
            foreach ($references as $id => $s) {
                $sb->append(sprintf('__module_refs[' . $r . ']=%s;', HtmlScriptLoader::ImportContentAsModule(
                    file_get_contents($id),
                    $references
                )));
                unset($sources[$id]);
                $r++;
            }
            $out .= implode("\n", $sources);
            $sb->appendLine('})();');
            $out = $sb . '' . $out;
        } else {
             $out .= implode("\n", $sources);
        }
        // . bundler treatment
        $out = self::TreatBundlerSource($out);
        return $out;
    }
    /**
     * just treat bundle
     * @param string $src 
     * @return string 
     */
    public static function TreatBundlerSource(string $src){
        return $src;   
    }
    /**
     * treat js source
     * @param string $file 
     * @param mixed $src 
     * @param array &$reference 
     * @return mixed 
     * @throws IGKException 
     * @throws Exception 
     */
    static function TreatJSSource(string $file, $src, &$reference = [])
    {
        $dir = dirname($file);
        $jscontainer = new RegexMatcherContainer;
        $offset = 0;
        $l = $jscontainer->begin('(await\\b\s*)?import\s*\(', '\)(\s*;)?', 'import-resolution')->last();
        $lvalue  = $l->begin("('|\")", "\\1", "import.url");
        $l->patterns = [
            $lvalue
        ];
        $l = $jscontainer->begin('\/\*', '\*\/', 'multi-comment');
        $l = $jscontainer->match('\/\/.*$', 'comment');
        $l = $jscontainer->appendStringDetection('litteral');
        $l = $jscontainer->begin('(’)', '\\1', 'litteral-import');
        $url = null;
        while ($g = $jscontainer->detect($src, $offset)) {
            if ($e = $jscontainer->end($g, $src, $offset)) {
                switch ($e->tokenID) {
                    case 'import.url':
                        $l = trim($e->value, '"\'');
                        if (!IGKValidator::IsUri($l)) {
                            $cf = Path::CombineAndFlattenPath($dir,  $l);
                            if (file_exists($cf)) {
                                $url = $cf;
                            }
                        }
                        break;
                    case 'import-resolution':
                        if ($url) {
                            // replace with file content  
                            if (!isset($reference[$url])) {
                                $reference[$url] = 1;
                            }
                            $inx = array_search($url, array_keys($reference));
                            // return imports module reference for production . 
                            $gm = rtrim(substr($src, 0, $e->from));
                            // if (igk_str_endwith($gm,'=')){
                            // }
                            $ts = sprintf('__module_refs[%s].apply(window);', $inx);
                            $src = $gm . $ts . substr($src, $e->to);
                            $offset = $e->from + strlen($ts) + 1;
                            $url = null;
                        }
                        break;
                }
            }
        }
        return $src;
    }
}