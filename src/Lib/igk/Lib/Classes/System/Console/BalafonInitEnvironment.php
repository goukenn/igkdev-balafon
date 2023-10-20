<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitEnvironment.php
// @date: 20231019 12:57:36
namespace IGK\System\Console;

use IGK\System\Html\HtmlRenderer;
use stdClass;

///<summary></summary>
/**
* init environment balafon environment configuration
* @package IGK\System\Console
*/
class BalafonInitEnvironment{
    public function run($command){
        Logger::info("--init");
        $file = getcwd() . "/" . AppConfigs::ConfigurationFileName;
        $options = igk_getv($command, "options") ?? new stdClass();
        if (file_exists($file) && !property_exists($options, "--force")) {
            Logger::danger("Balafon already initialized configuration.");
            return;
        }
        $init_data = igk_create_xmlnode("balafon");
        $config = new \IGK\System\Console\AppConfigs();
        $config->author = igk_environment()->balafon_author;


        if (property_exists($options, "--noconfig")) {
            $primary = property_exists($options, "--primary");
            $app_dir = $primary ? "./" :  "src/application";
            $public_dir = $primary ? "./" : "src/public";
            $sess_dir = $primary ? null : 'src/sesstemp';


            $init_data->env()->setAttributes(["name" => "IGK_BASE_URI", "value" => "//localhost"]);
            $init_data->env()->setAttributes(["name" => "IGK_DOCUMENT_ROOT", "value" => $public_dir]);
            $init_data->env()->setAttributes(["name" => "IGK_BASE_DIR", "value" => $public_dir]);
            $init_data->env()->setAttributes(["name" => "IGK_APP_DIR", "value" => $app_dir]);
            $sapp_dir = $app_dir == "./" ? "" : $app_dir;
            $init_data->env()->setAttributes(["name" => "IGK_PROJECT_DIR", "value" => $sapp_dir . "/Projects"]);
            $init_data->env()->setAttributes(["name" => "IGK_PACKAGE_DIR", "value" => $sapp_dir . "/Packages"]);
            $init_data->env()->setAttributes(["name" => "IGK_MODULE_DIR", "value" => $sapp_dir . "/Packages/Modules"]);
            $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" => $sapp_dir . "/Packages/vendor"]);
            if ($sess_dir)
                $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" => $sapp_dir . "/Packages/vendor"]);

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
        igk_io_w2file($file, $init_data->render($opts));
    }
}