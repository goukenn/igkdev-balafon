<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_autoload_test.php
// @date: 20251119 09:23:17
// @desc: 
// init environment   
$_key_app_dir = $name;
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
if (!defined($_key_app_dir)) {
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
            if (igk_io_file_exists($configFile = $bdir . "/balafon.config.xml")) {
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
    !defined($name) && define($name, $bdir);
}
return constant($name);