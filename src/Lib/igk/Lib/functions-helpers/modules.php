<?php

use IGK\Controllers\ApplicationModuleController;
use IGK\Helper\IO;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;

if (!function_exists('igk_current_module')) {
    /**
     * retrive the current module according to execution
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    function igk_current_module()
    {
        list($file) = igk_sys_get_caller_file(1);
        if ($file) {
            $path = igk_io_collapse_path($file);
            $key = "%modules%";

            if (strpos($path, $key) === 0) {
                $n = substr($path, strlen($key) + 1);
                $modules = igk_environment()->get("module_resolution") ?? [];
                if (isset($modules[$n])) {
                    return $modules[$n];
                }
                $tab = igk_get_modules();
                ksort($tab, SORT_FLAG_CASE | SORT_STRING); 
                foreach (array_keys($tab) as $k) {
                    if (strpos($n, $k) !== false) {
                        $mod = igk_get_module($k);
                        $modules[$n] = $mod;
                        igk_environment()->set('module_resolution', $modules);
                        return $mod;
                    }
                }
            }
        }
    }
}
/**
 * include module helper
 */
function igk_include_module($modulename, ?callable $init = null, $loadall = 0)
{
    return igk_require_module($modulename, $init, $loadall, 0);
}
///<summary>represent require module</summary>
/**
 * /**
 *  
 * @param mixed $modulename 
 * @param callable|null $init 
 * @param int $loadall load all chain form 
 * @param int $die die if module not found
 * @param mixed $name exposed name
 * @return null|\IGK\Controllers\ApplicationModuleController 
 * @throws IGKException 
 * @throws ArgumentTypeNotValidException 
 * @throws ReflectionException 
 * @throws EnvironmentArrayException 
 */
function igk_require_module(string $modulename, callable $init = null, $loadall = 1, $die = 1, $name = null)
{
    $IGK_ENV = igk_environment();
    $g = &igk_environment()->require_modules();
    $mkey = igk_uri(strtolower($modulename));
    if (isset($g[$mkey])) {
        $mod = $g[$mkey];
        igk_bind_module($mod, $name);
        if ($init){
            $init($mod, igk_ctrl_current_doc());
        }
        return $mod; 
    }
    // igk_environment()->write_debug("load module : " . $modulename);
    $dir = igk_dir(igk_get_module_dir() . "/{$modulename}");
    if (!file_exists($dir)) {
        if ($die) { 
            igk_wln_e(
                __FILE__ . ":" . __LINE__,
                "module missing : ",
                $modulename
            ); 
            throw new \IGKException(__FUNCTION__ . "::module <b>{$modulename}</b> missing " . igk_io_collapse_path($dir), 500);
        }
        return null;
    }

    // $expected_time = 0.05;
    // Benchmark::mark("loading::" . $modulename);
    igk_push_env(IGKEnvironmentConstants::MODULES, $modulename);
    $f = 0;
    $ext_regex = "/(.)*\.php$/";
    $excluded_key = IGKEnvironment::IGNORE_LIB_DIR;
    $excludedir = igk_default_ignore_lib($dir);
    $excludedir = array_merge(igk_environment()->{$excluded_key} ?? [], $excludedir ?? []);
    $IGK_ENV->set($excluded_key,  $excludedir);
    $exclude_files = [igk_uri($dir . "/index.php")];
    if ($loadall) {
        $key = sprintf("module:%s", $modulename);
        $modfile = [];
        $files = \IGK\System\Configuration\CacheConfigs::GetCachedSetting(
            $key,
            "module_files"
        );
        if ($files) {
            $files = array_map(function ($f) {
                require_once igk_io_expand_path(trim($f));
            }, $files);
        } else {
            // $expected_time = 0.200;
            $f = igk_io_getfiles(
                $dir,
                function ($c, &$excludedir = null) use ($ext_regex, $exclude_files, &$modfile) {
                    if (in_array($c, $exclude_files))
                        return 0;
                    $hdir = dirname($c);
                    $basename = basename($hdir);
                    if ($excludedir && (isset($excludedir[$basename]) || isset($excludedir[$hdir]))) {
                        return -1;
                    }
                    if (preg_match($ext_regex, $c)) {
                        require_once($c);
                        // $modfile[$c] = 1;
                        $modfile[igk_io_collapse_path($c)] = 1;
                        return 1;
                    }
                    return 0;
                },
                true,
                $excludedir
            );
            /// TODO: Remove Cache setting
            // \IGK\System\Configuration\CacheConfigs::RegisterCacheSetting($key,  "module_files", array_keys($modfile));
        }
    } else {

        $f = igk_io_getfiles(
            $dir,
            function ($c, &$excludedir = null) use ($ext_regex, $dir) {
                $fdir = dirname($c);
                if ($dir != $dir) {
                    if (file_exists($fdir . DIRECTORY_SEPARATOR . ApplicationModuleController::CONF_MODULE) || ($excludedir && isset($excludedir[$dir]))) {
                        return -1;
                    }
                }
                if (preg_match($ext_regex, $c)) {
                    include_once($c);
                    return 1;
                }
                return 0;
            },
            true,
            $excludedir
        );
    }
    igk_pop_env(IGKEnvironmentConstants::MODULES);
    $mod = igk_init_module($modulename, $init); 
    $g[$mkey] = $mod;
    if (igk_count($f) > 0) {
        $g["::files"][$mkey] = $f;
    }
    // :: Benchmark::expect("loading::" . $modulename, $expected_time);
    igk_bind_module($mod, $name);
    return $mod;
}
function igk_bind_module($mod, ?string $name = null)
{
    if ($ctrl = \IGK\Helper\ViewHelper::CurrentCtrl()) {
        $g = $ctrl->getEnvParam("modules") ?? [];
        $g[$name] = $mod;
        $ctrl->setEnvParam("modules", $g);
    }
}

/**
 * 
 * @param mixed $path module path
 * @param null|callable $init (module, current_document)=>{}
 * @param bool $initialize 
 * @return mixed 
 * @throws IGKException 
 * @throws ArgumentTypeNotValidException 
 * @throws ReflectionException 
 */
function igk_init_module(string $path,  ?callable $init = null, $initialize = true)
{
    $v_meth = \IGK\Controllers\ApplicationModuleController::INIT_METHOD;
    $v_init = igk_environment()->getModulesManager()->init();
    if ($mod = $v_init->get($path)) {
        return $mod;
    }
    $dir = igk_dir(igk_get_module_dir() . "/{$path}");
    if (!file_exists($dir))
        return null;
    //require to protect to case sensitive path
    $sdir = IO::GetUnixPath($dir, true);
    if (igk_environment()->isOPS()) {
        if (empty($sdir)) {
            $sdir = $dir; //realpath($dir);
        }
    }
    if (empty($dir)) {
        return null;
    }
    $ob = new \IGK\Controllers\ApplicationModuleController($dir, $path);
    if ($initialize) {
        $dc = igk_ctrl_current_doc();
        if (!$init && (method_exists($ob, $v_meth) || $ob->supportMethod($v_meth)) && $dc) {
            $ob->initDoc($dc);
            $ob->setEnvParam($v_meth, $dc); 
        } else if ($init) {
            $init($ob, $dc);
        }
        $v_init->register($path, $ob);
    }
    return $ob;
}

