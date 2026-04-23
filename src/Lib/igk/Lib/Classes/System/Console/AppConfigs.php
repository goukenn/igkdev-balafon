<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppConfigs.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console;
use Exception;
use IGK\Helper\IO;
use IGK\System\Configuration\XPathConfig;
use IGK\System\Core\Configuration\DirectoriesInstallsConstants;
use IGKException;

/**
 * configuration builder 
 * @package IGK\System\Console
 */
class AppConfigs
{
    /**
    * Property: author.
    * @var mixed
    */
    var $author;
    /**
     * load configuration file
     */
    const ConfigurationFileName = IGK_BALAFON_CONFIG;
    /**
    * Initializes.
    * @param mixed $init_data
    */
    public function init($init_data)
    {
        if (!function_exists('readline')) {
            // + | missing readline
            return;
        }
        if (empty($this->author)) {
            $init_data->add("author")->Content  = $this->read_author();
        }
        $tc = array_merge([
            "IGK_DOCUMENT_ROOT"=>'document root',
            "IGK_BASE_URI"=>'base uri',
        ], DirectoriesInstallsConstants::GetInstallableDirConstants());
        foreach ($tc as $envprop => $title
        ) {
            if (is_numeric($envprop)){
                $envprop = $title;
            }
            $key = "env_" . strtolower($envprop);
            if ($n = $this->$key("{$title} :")) {
                $init_data->add("env")->setAttribute("name", $envprop)
                    ->setAttribute("value", $n);
            }
        }
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $args
    */
    public function __call($name, $args)
    {
        if (method_exists($this, $n = strtolower("read_" . $name))) {
            return $this->$n(...$args);
        }
        return igk_read_line(...$args);
    }
    /**
    * auto generate doc.
    * @return string|false
    */
    private function read_author()
    {
        if (empty(trim($s = igk_read_line("author : ")))) {
            $s = IGK_AUTHOR;
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_base_uri($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            $s = "http://localhost";
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_document_root($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            if (is_dir($dir = getcwd() . "/src/public")) {
                $s = "src/public";
            }
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $d
    * @return string
    */
    private static function _GetLocationDir($cwd, $l, $d): string
    {
        if (is_dir($cwd . $l)) {
            $s = "src/public";
        } else {
            $s = $d;
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_base_dir($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            $l = '/src/public';
            $s = self::_GetLocationDir(getcwd(), '/src/public', './');
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_app_dir($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            $s = self::_GetLocationDir(getcwd(), "/src/application", "./");
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_project_dir($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            $s = self::_GetLocationDir(getcwd(), "/src/application/Projects", "./Projects");
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_vendor_dir($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            $s = self::_GetLocationDir(getcwd(), "/src/application/Packages/vendor", "./Packages/vendor");
        }
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $prompt
    * @return
    */
    private function read_env_igk_module_dir($prompt)
    {
        if (empty(trim($s = igk_read_line($prompt)))) {
            $s = self::_GetLocationDir(getcwd(), "/src/application/Packages/Modules", "./Packages/Modules");
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
    public static function LoadConfigurationFile(string $configFile)
    {
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
                    preg_match("/(\\bIGK_DOCUMENT_ROOT\\b|_DIR$)/", $env->name) ? IO::ResolvPathConstant($wd, $env->value) :
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
    public static function InitEnvironment($config)
    {
        foreach (['IGK_MYSQL_DB_SERVER' => 'db_server'] as $k => $v) {
            if ($env = getenv($k)) {
                $config->{$v} = $env;
            }
        }
    }
}