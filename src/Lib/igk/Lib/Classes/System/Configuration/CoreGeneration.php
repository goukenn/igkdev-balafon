<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CoreGeneration.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Configuration;

class CoreGeneration
{
    public function GetTestRequireAutoload()
    {
        // 'require_once $_ENV["IGK_APP_DIR"]."/Lib/igk/Lib/Tests/autoload.php";'
        $doc = <<<'EOF'
require_once (function ($name) {
    // init environment    
    foreach (['IGK_BASE_DIR', 'IGK_TEST_CONTROLLER', 'IGK_APP_DIR'] as $m) {
        if (defined($m))
            continue;
        foreach ([$_SERVER, $_ENV] as $tab) {
            if (isset($tab[$m])) {
                define($m, $tab[$m]);
                break;
            }
        }
    }
    if (!defined('IGK_APP_DIR')) {
        $resolv_path = function ($dir, $value) {
            $p = realpath($value);
            if (empty($p)) {
                return str_replace("\\", "/", $dir . "/" . $value);
            }
            return $p;
        };
        // loading environment
        $bdir = isset($_SERVER["PWD"]) ? $_SERVER["PWD"] : getcwd();
        if (function_exists('simplexml_load_file')) {
            $tconfigFile = null;
            while (!empty($bdir)) {
                if (file_exists($configFile = $bdir . "/%%balafon_config_file%%")) {
                    $tconfigFile = $configFile;
                    break;
                }
                $b = $bdir;
                $bdir = dirname($bdir);
                if ($b == $bdir) {
                    break;
                }
            }
            if (!is_null($tconfigFile)) {
                $wd = dirname($tconfigFile);
                $g = (array)simplexml_load_file($tconfigFile);
                if (key_exists('env', $g)) {
                    foreach ($g['env'] as $k) {
                        $n = "" . $k['name'];
                        $v = "" . $k['value'];
                        defined($n) || define(
                            $n,
                            preg_match("/_DIR$/", $n) ? $resolv_path($wd, $v) :
                                $v
                        );
                    }
                }
            }
        }
        !defined('IGK_APP_DIR') && define('IGK_APP_DIR', $bdir);
    }
    return constant($name);
})('IGK_APP_DIR') . "/Lib/igk/Lib/Tests/autoload.php";
EOF;
        
        foreach(["%%balafon_config_file%%"=>IGK_BALAFON_CONFIG] as $k=>$v){
            $doc = str_replace($k, $v, $doc);
        }
        return $doc;
    }
}
