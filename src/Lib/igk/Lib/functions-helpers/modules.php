<?php

use Google\Service\Spanner\Instance;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\ViewHelper;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;

if (!function_exists('igk_current_module')) {
    /**
     * retrive the current module according to context execution
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    function igk_current_module()
    {
        $v_skey = 'module_resolution';
        $v_env = igk_environment();

        list($file) = igk_sys_get_caller_file(1);
        if ($file) {
            $path = igk_io_collapse_path($file);
            $key = "%modules%";

            if (strpos($path, $key) === 0) {
                $n = substr($path, strlen($key) + 1);
                $modules = $v_env->get($v_skey) ?? [];
                if (isset($modules[$n])) {
                    return $modules[$n];
                }
                $tab = igk_get_modules();
                ksort($tab, SORT_FLAG_CASE | SORT_STRING);
                foreach (array_keys($tab) as $k) {
                    if (strpos($n, $k) !== false) {
                        $mod = igk_get_module($k);
                        $modules[$n] = $mod;
                        $v_env->set($v_skey, $modules);
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

if (!function_exists('igk_get_loaded_modules')) {

    /**
     * get loaded required modules 
     * @return mixed 
     */
    function igk_get_loaded_modules()
    {
        $modules = igk_environment()->require_modules();
        return $modules;
    }
}


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
function igk_require_module(string $modulename, ?callable $init = null, $loadall = 1, $die = 1, $name = null)
{ 
    // + | PREPARE MODULE DEFINITION 
    $modulename = str_replace('.','\\', $modulename);

    $v_mod_key = IGKEnvironmentConstants::MODULES;
    $IGK_ENV = igk_environment();
    $g = &igk_environment()->require_modules();
    $mkey = igk_uri(strtolower($modulename));
    $v_init_on_view = ViewHelper::CurrentCtrl() !== null;
    $v_init_doc_method = \IGK\Controllers\ApplicationModuleController::INIT_DOC_METHOD;
    
    if (isset($g[$mkey])) {
        $mod = $g[$mkey];
        igk_bind_module($mod, $name);
        if ($init) {
            $init($mod, igk_ctrl_current_doc());
        } else if ($v_init_on_view) {
            $gdoc = $mod->getEnvParam($v_init_doc_method);
            if (is_null($gdoc)) {
                $doc = ViewHelper::CurrentDocument();
                igk_module_init_doc($mod, $doc);
            }
        }
        return $mod;
    }
    $dir = igk_dir(igk_get_module_dir() . "/{$modulename}");
    if (!igk_io_file_exists($dir, true)) {
        if ($die) {
            igk_trace();
            igk_dev_wln_e(
                __FILE__ . ":" . __LINE__,
                "module missing : ",
                $modulename
            );
            throw new \IGKException(__FUNCTION__ . "::module <b>{$modulename}</b> missing " . igk_io_collapse_path($dir), 500);
        }
        igk_die('missing location ' . $dir);
        return null;
    }

    // $expected_time = 0.05;
    // Benchmark::mark("loading::" . $modulename);
    igk_push_env($v_mod_key, $modulename);
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
                    if (igk_io_file_exists($fdir . DIRECTORY_SEPARATOR . ApplicationModuleController::CONF_MODULE, true) || ($excludedir && isset($excludedir[$dir]))) {
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
    igk_pop_env($v_mod_key); 
  
    $mod = igk_init_module($modulename, $init);
    $g[$mkey] = $mod;
    // + | --------------------------------------
    // + | file: used to store list of file to 
    // + |
    if (igk_count($f) > 0) {
        $g["::files"][$mkey] = $f;
    }
    if (!$mod) {
        igk_die('missing location [' . $dir . '] ? ' . file_exists($dir) . ' -- ' . $modulename);
    } else {
        igk_bind_module($mod, $name);
    }
    return $mod;
}
/**
 * get if module loaded
 * @param string $id 
 * @return bool 
 */
function igk_is_module_loaded(string $id): bool{
    $mod = igk_environment()->require_modules();
    $g = strtolower(igk_uri($id));
    return igk_getv($mod, $g) instanceof ApplicationModuleController;
}

/**
* auto generate doc.
* @return mixed
*/
function igk_loaded_modules(){
    $mod= igk_environment()->require_modules();
    foreach($mod as $k => $v){
        if (preg_match("/^::/", $k)){
            unset($mod[$k]);
        }
    }
    return $mod;
}
/**
 * enable all module 
 * @return array 
 */
function igk_module_inject_all()
{
    $mod = igk_get_modules();
    $count = 0;
    $ts = [];
    $failed = [];
    foreach (array_keys($mod) as $c) {
        if ($mod_c = igk_require_module($c, null, false)) {
            $count++;
            $ts[] = $c;
        } else {
            $failed[] = $c;
        }
    }
    return compact('ts', 'failed');
}

/**
* auto generate doc.
* @param null|string $name
* @return void
*/
function igk_bind_module($mod, ?string $name = null, ?BaseController $controller = null)
{
    $v_key = IGKEnvironmentConstants::CtrlEnvParamModules;
    if ($ctrl = $controller ?? \IGK\Helper\ViewHelper::CurrentCtrl()) {
        $g = $ctrl->getEnvParam($v_key) ?? [];
        if (!is_null($name))
            $g[$name] = $mod;
        $ctrl->setEnvParam($v_key, $g);
    }
}

/**
* auto generate doc.
* @param bool $initialize
* @return mixed
*/
function igk_init_module(string $path,  ?callable $init = null, $initialize = true)
{
    $v_meth = \IGK\Controllers\ApplicationModuleController::INIT_DOC_METHOD;
    $v_init = igk_environment()->getModulesManager()->init();
    if ($mod = $v_init->get($path)) {
        return $mod;
    }
    $v_mod_dir = igk_get_module_dir();
    $dir = null;

    if (($s = IO::ResolveDirRealPath($v_mod_dir, igk_dir($path)))) {
        $dir = $s;
    } else {
        return false;
    }
    $path = substr($dir, strlen($v_mod_dir) + 1);
    // + | require to protect to case sensitive path
    // $sdir = IO::GetUnixPath($dir, true);
    // if (igk_environment()->isOPS()) {
    //     if (empty($sdir)) {
    //         $sdir = $dir; //realpath($dir);
    //     }
    // }
    // if (empty($dir)) {
    //     return null;
    // }
    $ob = new \IGK\Controllers\ApplicationModuleController($dir, $path);
    if ($initialize) {
        $dc = igk_ctrl_current_doc();
        if (!$init && (method_exists($ob, $v_meth) || $ob->supportMethod($v_meth)) && $dc) {
            igk_module_init_doc($ob, $dc);
        } else if ($init) {
            $init($ob, $dc);
        }
        $v_init->register($path, $ob);
    }
    igk_hook(IGKEvents::HOOK_MODULE_DID_INIT_MODULE, ['module'=>$ob]);    
    return $ob;
}


// because initDoc only need to be call on view loading only once to initialize the document

/**
* Igk module init doc.
* @param ApplicationModuleController $module
* @param mixed $doc
*/
function igk_module_init_doc(ApplicationModuleController $module, $doc)
{
    $mod = &igk_environment()->require_modules();
    $v_k = '::initDoc';
    if (!isset($mod[$v_k])) {
        $mod[$v_k] = [];
    }
    $nk = strtolower($module->getName());
    if (!isset($mod[$v_k][$nk])) {
        call_user_func_array([$module, $fc = 'initDoc'], [$doc]);
        $mod[$v_k][$nk] = 1;
        $module->setEnvParam($fc, $doc);
    }
}
