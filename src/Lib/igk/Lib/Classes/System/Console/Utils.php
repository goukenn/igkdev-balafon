<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Utils.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console;
/**
 * console helper
 * @package IGK\System\Console
 */
class Utils{
    public static function GenerateConfiguration($public_dir, $app_dir, $base_uri="//localhost"){
        $init_data = igk_create_xmlnode("balafon");
        $base_uri = igk_get_domain_name($base_uri) ?? '//localhost';
        $init_data->env()->setAttributes(["name" => "IGK_BASE_URI", "value" => $base_uri]);
        $init_data->env()->setAttributes(["name" => "IGK_DOCUMENT_ROOT", "value" => $public_dir]);
        $init_data->env()->setAttributes(["name" => "IGK_BASE_DIR", "value" => $public_dir]);
        $init_data->env()->setAttributes(["name" => "IGK_APP_DIR", "value" => $app_dir]);
        $sapp_dir = $app_dir == "./" ? "": $app_dir;
        $init_data->env()->setAttributes(["name" => "IGK_PROJECT_DIR", "value" => $sapp_dir."/Projects"]);
        $init_data->env()->setAttributes(["name" => "IGK_PACKAGE_DIR", "value" => $sapp_dir."/Packages"]);
        $init_data->env()->setAttributes(["name" => "IGK_MODULE_DIR", "value" => $sapp_dir."/Packages/Modules"]);
        $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" => $sapp_dir."/Packages/vendor"]);
        return $init_data;
    }
}