<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitEnvironment.php
// @date: 20231019 12:57:36
namespace IGK\System\Console;
use IGK\Helper\IO;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
use IGKAppSystem;
use IGKEvents;
use stdClass;
/**
 * init environment balafon environment configuration
 * @package IGK\System\Console
 */
class BalafonInitEnvironment
{
    /**
    * Constant: app lib core.
    * @var mixed
    */
    const AppLibCore = '/Lib/igk';
    /**
     * retrieve vendor directory 
     * @param string $dir 
     * @return string|null 
     */
    public static function GetVendorDir(string $dir): ?string
    {
        while ($dir && ($dir != '.')) {
            $n = basename($dir);
            if ($n == 'vendor') {
                if (file_exists($dir . '/autoload.php')) {
                    return $dir;
                }
            }
            $pdir = $dir;
            if ($pdir === ($dir = dirname($dir))) {
                break;
            }
        }
        return null;
    }
    /**
     * retrieve sub relative directory
     * @param string $dir 
     * @param string $cwd 
     * @return string 
     */
    private static function _CurrentSubRelativeDir(string $dir, string $cwd): string
    {
        if (strstr($dir, $cwd . DIRECTORY_SEPARATOR) == $dir) {
            $dir = substr($dir, strlen($cwd) + 1);
        }
        return $dir;
    }
    /**
    * auto generate doc.
    * @param mixed $appLibCore
    * @return void
    */
    public function run($command, string $install_dir = 'src', string $appLibCore = self::AppLibCore)
    {
        igk_environment()->isDev() && Logger::info("--[init]--");
        $cwd = getcwd();
        if (property_exists($command->options, '--clean'))
            IO::CleanDir($cwd);
        $file = $cwd . "/" . AppConfigs::ConfigurationFileName;
        $options = igk_getv($command, "options") ?? new stdClass();
        if (file_exists($file) && !property_exists($options, "--force")) {
            Logger::danger("Balafon already initialized configuration. dddd - " . $file);
            return;
        }
        if ($install_dir == './') {
            $install_dir = '.';
        } else {
            $g = Path::ToLocalPath($install_dir, $cwd);
            $install_dir = \IGK\System\IO\Path::GetRelativePath($cwd, $g); // Path::ToLocalPath($install_dir, $cwd), $cwd);
        }
        $v_reset = property_exists($options, '--reset');
        $v_no_config = property_exists($options, "--no-config");
        $v_primary = property_exists($options, "--primary");
        $v_in_vendor = igk_getv($options, '--vendor-dir') ?? self::GetVendorDir(IGK_LIB_DIR);
        $init_data = igk_create_xmlnode("balafon");
        $config = new \IGK\System\Console\AppConfigs();
        $config->author = igk_environment()->balafon_author;
        $app_dir = '';
        $public_dir = '';
        $lib = null;
        if ($v_no_config) {
            $_git_contents = [
                '.balafon',
                '.vscode',
                '.gitignore',
                '*/node_modules/**',
            ];
            /**
             * disable configuration
             */
            $v_primary = $v_primary;
            $app_dir = Path::SubLocalPath(igk_getv($options, '--app-dir') ?? ($v_primary ? "./" :  $install_dir . "/application"), $cwd);
            $public_dir = $v_primary ? "./" : $install_dir . "/public";
            $sess_dir = $v_primary ? null : $install_dir . "/sesstemp";
            $app_dir = self::_CurrentSubRelativeDir($app_dir, $cwd);
            $v_in_vendor = $v_in_vendor ? self::_CurrentSubRelativeDir($v_in_vendor, $cwd) : null;
            if ($v_reset) {
                // + | --------------------------------------------------------------------
                // + | reset configuration 
                // + |                
                if (is_file($f = $app_dir . '/Data/configure')) {
                    @unlink($f);
                }
            }
            $init_data->env()->setAttributes(["name" => "IGK_BASE_URI", "value" => "//localhost"]);
            $init_data->env()->setAttributes(["name" => "IGK_DOCUMENT_ROOT", "value" => $public_dir]);
            $init_data->env()->setAttributes(["name" => 'IGK_BASE_DIR', "value" => $public_dir]);
            $init_data->env()->setAttributes(["name" => "IGK_APP_DIR", "value" => $app_dir]);
            $sapp_dir = $app_dir == "./" ? "." : $app_dir;
            $init_data->env()->setAttributes(["name" => "IGK_PROJECT_DIR", "value" => $sapp_dir . "/Projects"]);
            $init_data->env()->setAttributes(["name" => "IGK_PACKAGE_DIR", "value" => $sapp_dir . "/Packages"]);
            $init_data->env()->setAttributes(["name" => "IGK_MODULE_DIR", "value" => $sapp_dir . "/Packages/Modules"]);
            $init_data->env()->setAttributes(["name" => "IGK_NODE_MODULES_DIR", "value" => $sapp_dir . "/Packages/node_modules"]);
            $v_tvendor = $sapp_dir . "/Packages/vendor";
            $links = [];
            if ($_vendor = $v_in_vendor ?? $v_tvendor) {
                $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" =>
                $_vendor]);
                $_git_contents[] = $_vendor;
                if ($v_tvendor != $_vendor) {
                    $links[$v_tvendor] = $_vendor;
                }
            }
            if ($sess_dir) {
                $init_data->env()->setAttributes(["name" => "IGK_SESS_DIR", "value" => $sess_dir]);
                $_git_contents[] = $sess_dir;
            }
            igk_io_createdir($app_dir);
            igk_io_createdir($public_dir);
            $lib = ($app_dir == './' ? getcwd() : $app_dir) . $appLibCore;
            if (!file_exists($lib)) {
                igk_io_createdir($dir = dirname($lib));
                $core_lib = self::_CurrentSubRelativeDir(IGK_LIB_DIR, $cwd);
                $v_link = null;
                $v_lk = $lib;
                if ($core_lib == IGK_LIB_DIR) {
                    // not a sub folder 
                    $v_lk = Path::Combine(realpath($dir), basename($lib));
                }
                $v_link = \IGK\System\IO\Path::GetRelativePath($v_lk, $core_lib);
                @symlink($v_link, $lib);
            }
            $_git_contents[] = Path::GetRelativePath($cwd, $lib);
            // + | create links target => location
            foreach ($links as $c => $d) {
                if (file_exists($c)) {
                    IO::CreateDir(dirname($c));
                    $v_link = \IGK\System\IO\Path::GetRelativePath($c, $d);
                    @symlink($v_link, $c);
                }
            }
            if (!file_exists('.gitignore'))
                igk_io_w2file('.gitignore', implode("\n", $_git_contents));
        } else {
            Logger::info('auto init data');
            $config->init($init_data);
            $lib = \IGK\System\IO\Path::GetRelativePath( $cwd, IGK_LIB_DIR);  
        }
        $opts = HtmlRenderer::CreateRenderOptions();
        $opts->Indent = true;
        Logger::info('store-global-config-data : ' . $file);
        $init_data['init'] = date('Y-m-d');
        igk_io_w2file($file, $init_data->render($opts));
        // + | create a symlink to balafon command line interface 
        if (!file_exists('balafon')){
            @symlink(Path::Combine($lib, 'bin', 'balafon'), 'balafon');
        }
        igk_hook(IGKEvents::HOOK_SYS_INIT_CONFIG, ['reset' => $v_reset]);
        if ($v_reset) {
            IGKEvents::ClearHooks();
            // - reset environment  
            $argv = igk_getv($_SERVER, 'argv');
            $argv[0] = Path::Combine($cwd ,'/balafon');
            register_shutdown_function(function () use ($argv, $app_dir, $command, $public_dir, $cwd) {
                $cli = $argv[0];
                $cmd = "$cli --init --env-only --wdir:'{$cwd}' '{$app_dir}'";
                echo "launch: " . $cmd, PHP_EOL;
                echo shell_exec("$cmd");
                self::_AuthFiles($command, [$app_dir, $public_dir]);
            });
        } else {
            self::_AuthFiles($command, [$app_dir, $public_dir]);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $command
    * @param mixed $dirs
    * @return
    */
    private static function _AuthFiles($command, $dirs)
    {
        foreach ($dirs as $d)
            self::_InitIOFileAuth($command, $d);
    }
    /**
    * auto generate doc.
    * @param mixed $app_dir
    * @return void
    */
    static function _InitIOFileAuth($command, string $app_dir)
    {
        // + | fix mod and owner
        igk_environment()->isUnix() && ('darwin' != strtolower(PHP_OS)) && (function ($d, $command) {
            shell_exec("chmod -R 755 {$d}");
            $o = igk_getv($command->options, '--file-usergroup', self::_DefaultUserGroup());
            if (false === strpos($o, ':')) {
                $o = $o . ':' . $o;
            }
            shell_exec("chown -R {$o} {$d}");
        })(realpath(dirname($app_dir)), $command);
    }
    /**
    * Default user group.
    */
    static function _DefaultUserGroup(){
        if (strtolower(PHP_OS)=='darwin'){
            return '_www:_www';
        }
        return 'www-data:www-data';
    }
}