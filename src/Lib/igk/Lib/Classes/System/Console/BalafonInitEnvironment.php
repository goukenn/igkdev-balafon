<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitEnvironment.php
// @date: 20231019 12:57:36
namespace IGK\System\Console;
use IGK\System\Html\HtmlRenderer;
use IGKAppSystem;
use IGKEvents;
use stdClass;
/**
* init environment balafon environment configuration
* @package IGK\System\Console
*/
class BalafonInitEnvironment{
    /**
     * retrieve vendor directory 
     * @param string $dir 
     * @return string|null 
     */
    public static function GetVendorDir(string $dir): ?string{
        while($dir && ($dir!='.')){
            $n = basename($dir);
            if ($n=='vendor'){
                if (file_exists($dir.'/autoload.php')){
                    return $dir;
                }
            }
            $pdir = $dir;
            if ($pdir === ($dir = dirname($dir))){
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
    private static function _CurrentRelativeDir(string $dir, string $cwd): string{
        if (strstr($dir, $cwd.DIRECTORY_SEPARATOR) == $dir){
            $dir=substr($dir, strlen($cwd)+1);
        }
        return $dir;
    }
    /**
     * 
     * @param mixed $command 
     * @return void 
     */
    public function run($command, string $install_dir='src'){
        igk_environment()->isDev() && Logger::info("--[init]--");
        $cwd =  getcwd();
        $file = $cwd. "/" . AppConfigs::ConfigurationFileName;
        $options = igk_getv($command, "options") ?? new stdClass();
        if (file_exists($file) && !property_exists($options, "--force")) {
            Logger::danger("Balafon already initialized configuration. dddd - ".$file);
            return; 
        }
        if ($install_dir == './'){
            $install_dir = '.'; // getcwd();
        }
        $v_reset = property_exists($options, '--reset');        
        $v_no_config = property_exists($options, "--noconfig");
        $v_primary = property_exists($options, "--primary");
        $v_in_vendor = igk_getv($options, '--vendor-dir') ?? self::GetVendorDir(IGK_LIB_DIR);     
        $init_data = igk_create_xmlnode("balafon");
        $config = new \IGK\System\Console\AppConfigs();
        $config->author = igk_environment()->balafon_author;
        if ($v_no_config) {
            /**
             * disable configuration
             */
            $primary = $v_primary;
            $app_dir = igk_getv($options, '--app-dir') ?? ( $primary ? "./" :  $install_dir."/application");
            $public_dir = $primary ? "./" : $install_dir."/public";
            $sess_dir = $primary ? null : $install_dir."/sesstemp";
            $app_dir = self::_CurrentRelativeDir($app_dir, $cwd);
            $v_in_vendor = $v_in_vendor ? self::_CurrentRelativeDir($v_in_vendor, $cwd) : null;
            if ($v_reset){
                // + | --------------------------------------------------------------------
                // + | reset configuration 
                // + |                
                if (is_file($f = $app_dir.'/Data/configure')){
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
            $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" => 
            $v_in_vendor ?? $sapp_dir . "/Packages/vendor"]);
            if ($sess_dir)
                $init_data->env()->setAttributes(["name" => "IGK_SESS_DIR", "value" => $sess_dir]);
            igk_io_createdir($app_dir);
            igk_io_createdir($public_dir);
            if (!file_exists($lib = $app_dir . "/Lib/igk")) {
                igk_io_createdir(dirname($lib));
                symlink(IGK_LIB_DIR, $lib);
            }
        } else {
            $config->init($init_data);
        }
        $opts = HtmlRenderer::CreateRenderOptions();
        $opts->Indent = true;
        Logger::info('store : '.$file);
        igk_io_w2file($file, $init_data->render($opts));
        igk_hook(IGKEvents::HOOK_SYS_INIT_CONFIG, ['reset'=>$v_reset]);
        // + | fix mod and owner
        igk_environment()->isUnix() && (function($d, $command){            
            `chmod -R 755 {$d}`;
            $o = igk_getv($command->options, '--owner', 'www-data');
            `chown -R {$o}:{$o} {$d}`; 
        })(realpath(dirname($app_dir)), $command);
        if ($v_reset){
            IGKEvents::ClearHooks();
            // - init environment 
            // reset environement 
            $argv = igk_getv($_SERVER, 'argv');
            register_shutdown_function(function()use($argv, $app_dir){
                $cli = $argv[0];
                $cmd ="$cli --init --env-only '{$app_dir}'";
                echo "launch: ".$cmd, PHP_EOL;
                echo `$cmd`;
            });  
        }
    }
}