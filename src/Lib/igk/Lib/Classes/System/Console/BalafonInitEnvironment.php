<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitEnvironment.php
// @date: 20231019 12:57:36
namespace IGK\System\Console;
use IGK\System\Html\HtmlRenderer;
use IGKAppSystem;
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
            $dir = dirname($dir);
        }
        return null;
    }
    /**
     * 
     * @param mixed $command 
     * @return void 
     */
    public function run($command, string $install_dir='src'){
        igk_environment()->isDev() && Logger::info("--init");
        $file = getcwd() . "/" . AppConfigs::ConfigurationFileName;
        $options = igk_getv($command, "options") ?? new stdClass();
        if (igk_io_file_exists($file) && !property_exists($options, "--force")) {
            Logger::danger("Balafon already initialized configuration.");
            return;
        }
        $v_reset = igk_getv($options, '--reset');        
        $v_no_config = property_exists($options, "--noconfig");
        $v_primary = property_exists($options, "--primary");

        $v_in_vendor = preg_match('/\/vendor\//', IGK_LIB_DIR) ? 
            self::GetVendorDir(IGK_LIB_DIR)
        : null;
     
        $init_data = igk_create_xmlnode("balafon");
        $config = new \IGK\System\Console\AppConfigs();
        $config->author = igk_environment()->balafon_author;
        if ($v_no_config) {
            /**
             * disable configuration
             */
            $primary = $v_primary;
            $app_dir = $primary ? "./" :  $install_dir."/application";
            $public_dir = $primary ? "./" : $install_dir."/public";
            $sess_dir = $primary ? null : $install_dir."/sesstemp";

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
            $sapp_dir = $app_dir == "./" ? "" : $app_dir;
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

        IGKAppSystem::InitEnv($app_dir, igk_app());
        igk_environment()->isUnix() && (function($d, $command){            
            `chmod -R 755 {$d}`;
            $o = igk_getv($command->options, '--owner', 'www-data');
            `chown -R {$o}:{$o} {$d}`;
            igk_wln("load-".$d);
        })(realpath(dirname($app_dir)), $command);

    }
}