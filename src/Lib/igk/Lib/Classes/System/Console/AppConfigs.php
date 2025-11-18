<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppConfigs.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console;
use Exception;
use IGK\Helper\IO;
use IGK\System\Configuration\XPathConfig;
use IGKException;
use function readline;
class AppConfigs
{
    var $author;
    /**
     * load configuration file
     */
    const ConfigurationFileName = IGK_BALAFON_CONFIG;
    public function init($init_data)
    {
        if (!function_exists('readdline')){
            return;
        }
        if (empty($this->author)) {
            $init_data->add("author")->Content  = $this->read_author();
        }
        foreach ([
            "IGK_DOCUMENT_ROOT",
            'IGK_BASE_DIR',
            "IGK_PROJECT_DIR",
            "IGK_APP_DIR",
            "IGK_PACKAGE_DIR",
            "IGK_MODULE_DIR",
            "IGK_VENDOR_DIR",
            "IGK_BASE_URI",
        ] as $envprop) {
            $key = "env_" . strtolower($envprop);
            if ($n = $this->$key("{$envprop} :")) {
                $init_data->add("env")->setAttribute("name", $envprop)
                    ->setAttribute("value", $n);
            }
        }
    }
    public function __call($name, $args)
    {
        if (method_exists($this, $n = strtolower("read_" . $name))) {
            return $this->$n(...$args);
        }
        return readline(...$args);
    }
    private function read_author()
    {
        if (empty(trim($s = readline("author : ")))) {
            $s = IGK_AUTHOR;
        }
        return $s;
    }
    private function read_env_igk_base_uri($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            $s = "http://localhost";
        }
        return $s;
    }
    private function read_env_igk_document_root($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/public")) {
                $s = "src/public";
            }
        }
        return $s;
    }
    private function read_env_igk_base_dir($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/public")) {
                $s = "src/public";
            }
        }
        return $s;
    }
    private function read_env_igk_app_dir($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/application")) {
                $s = "src/application";
            }
        }
        return $s;
    }
    private function read_env_igk_project_dir($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/application/Projects")) {
                $s = "src/application/Projects";
            }
        }
        return $s;
    }
    private function read_env_igk_vendor_dir($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/application/Packages/vendor")) {
                $s = "src/application/Packages/vendor";
            }
        }
        return $s;
    }
    private function read_env_igk_module_dir($prompt)
    {
        if (empty(trim($s = readline($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/application/Packages/Modules")) {
                $s = "src/application/Packages/Modules";
            }
        }
        return $s;
    }
    /**
     * load configuration file
     * @param string $configFile 
     * @return XPathConfig 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function LoadConfigurationFile(string $configFile){
        $wd = dirname($configFile);
        $c = igk_conf_load_file($configFile, "balafon");
        $configs = new XPathConfig($c);
        $c = $configs->get("env");
        if ($c) {
            if (!is_array($c))
                $c = [$c];
            foreach ($c as $env) {
                defined($env->name) || define(
                    $env->name,
                    preg_match("/_DIR$/", $env->name) ? IO::ResolvPathConstant($wd, $env->value) :
                        $env->value
                );
            }
        }
        return $configs;
    }
    /**
     * replace environment config;
     * @param mixed $config 
     * @return void 
     */
    public static function InitEnvironment($config){
        foreach(['IGK_MYSQL_DB_SERVER'=>'db_server'] as $k=>$v){
            if ($env = getenv($k)){
                $config->{$v} = $env;
            }
        }
    }
}