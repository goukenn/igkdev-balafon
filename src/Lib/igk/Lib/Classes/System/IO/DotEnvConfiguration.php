<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvConfiguration.php
// @date: 20260108 10:35:09
namespace IGK\System\IO;

use IGK\Controllers\BaseController;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;

/**
 * internal dot env configurations reader 
 * @package IGK\System\IO
 * @author C.A.D. BONDJE DOUE
 */
class DotEnvConfiguration
{
    /**
     * Property: key.
     * @var mixed
     */
    var $key;
    /**
     * Property: refkey.
     * @var mixed
     */
    private $m_refkey;
    /**
     * Property: dot env.
     * @var mixed
     */
    private static $sm_dotEnv;
    /**
     * Property: sys dot env.
     * @var mixed
     */
    private static $sm_sysDotEnv;
    /**
     * Constant: app dot env config.
     * @var mixed
     */
    const APP_DOT_ENV_CONFIG = 'dotenv_config_location_dir';
    /**
     * auto generate doc.
     * @param mixed & $refkey
     * @param mixed $ctrl
     * @return void
     */
    private static function _loadingEnvDev(&$refkey, $ctrl = null)
    {
        $ctrl = $ctrl ?? igk_environment()->controller_config_loading;
        if (is_null(self::$sm_dotEnv)) {
            self::$sm_dotEnv = [];
        }
        if ($ctrl) {
            $k = self::LoadDotEnv($ctrl);
            $refkey = $k;
        }
    }
    /**
     * load dot environement configuration 
     * @param BaseController $ctrl 
     * @return ?string
     */
    public static function LoadDotEnv(BaseController $ctrl)
    {
        $k = $ctrl->name();
        if (!isset(self::$sm_dotEnv[$k])) {
            $dir = $ctrl->getDeclaredDir();
            $config = [];
            $tc = self::_GetRefNames();
            foreach ($tc as $c) {
                $f = Path::Combine($dir, '.env' . $c);
                if (file_exists($f)) {
                    $content = file_get_contents($f);
                    self::LoadConfiguration($config, $content);
                }
            }
            self::$sm_dotEnv[$k] = $config;
        }
        return $k;
    }
    /**
     * auto generate doc.
     * @return mixed
     */
    private static function _GetRefNames()
    {
        $tc = [''];
        if (igk_environment()->isDev()) {
            $tc[] = '.dev';
        } else {
            $tc[] = '.production';
        }
        return $tc;
    }
    /**
     * load system environment definition 
     * @return void
     */
    private static function _LoadSysDotEnv()
    {
        $config = [];
        $k = self::APP_DOT_ENV_CONFIG;
        if (($lc = igk_app()->getConfigs()->{$k})
            && is_dir($lc)
        ) {
            $dir = $lc;
        } else {
            $dir = Path::getInstance()->getApplicationDir();
        } 
        $dp = [$dir];
        $tc = self::_GetRefNames();
        $load = false;
        while (count($dp) > 0) {
            $dir = array_shift($dp); 
            foreach ($tc as $c) {
                $f = Path::Combine($dir, '.env' . $c);
                if (file_exists($f)) {
                    $content = file_get_contents($f);
                    self::LoadConfiguration($config, $content);
                    $load = true;
                    igk_environment()->dotenv_location = $dir;
                }
            }
            if (!$load) {
                $g = dirname($dir);
                if (($g != $dir) && ($g != '.')) {
                    array_unshift($dp, $g);
                }
            }
        }
        self::$sm_sysDotEnv = $config;
    }
    /**
     * .ctr
     * @param string $value
     * @param null|mixed $ctrl
     * @return mixed
     */
    public function __construct(string $value, $ctrl = null)
    {
        self::_loadingEnvDev($this->m_refkey, $ctrl);
        $this->m_refkey || igk_die('loading environment failed');
        $this->key = $value;
    }
    /**
     * local dot env 
     * @return mixed 
     */
    private static function _GetSysDotEnv()
    {
        if (is_null(self::$sm_sysDotEnv)) {
            self::_LoadSysDotEnv();
        }
        return self::$sm_sysDotEnv;
    }
    /**
     * load configurations
     * @param array &$config 
     * @param string $content 
     * @return void 
     */
    public static function LoadConfiguration(array &$config, string $content)
    {
        $regex = new RegexMatcherContainer;
        $regex->match('^#.*$', 'comment');
        $regex->appendStringDetection('string', true);
        $regex->match('(?<==).+$', 'value');
        $regex->match('(?i)[a-z_][a-z0-9_]*', 'litteral');
        $regex->match('(?i)\\$[a-z_][a-z0-9_]*', 'var');
        $pos = 0;
        $src = $content;
        /**
         * @var ?string
         */
        $key = null;
        $v = null;
        $fcs = [
            'litteral' => function ($e) use (&$key) {
                $key = trim($e->value);
            },
            'value' => function ($e, &$config) use (&$key) {
                $v = trim($e->value);
                $cv = null;
                if (is_numeric($v)){
                    $cv = false !== strpos($v, '.') ?floatval($v):intval($v);
                }
                else if (in_array($v, ['true','false'])){
                    $cv = $v=='true'; 
                }
                else if (strpos($v, '$') !== false) {
                    $cv = new DotEnvVarConfiguration($v, $key, $config);
                } else {

                    if (is_numeric($v)) {
                        $v = floatval($v);
                    } else if (in_array($cl = strtolower($v), ['true', 'false'])) {
                        $v = $cl == true;
                    }
                    $cv = trim($e->value);
                }
                $config[$key] =$cv;
                $key = null;
            },
            'comment' => function () {},
            'string' => function ($e, &$config) use (&$key) {
                if (empty($key)) igk_die('missing key expression');
                $ts = igk_str_remove_quote($e->value);
                $s = [];
                $ts = preg_replace_callback('/\\$\\{\\s*(?P<name>[a-z_][a-z0-9_]*)\\s*\\}/i', function($g)use(& $s){
                    $s[$g[1]] = 1;
                    return '$'.$g[1];
                }, $ts);

                $config[$key] = $s ? 
                   (function($a, $s){
                    $a->replaceKeys = array_keys($s);
                    return $a;
                   })(new DotEnvVarConfiguration($ts, $key, $config), $s)
                : $ts;
                $key = null;
            },
            'expression' => function () {
                igk_wln_e('handle expression //// ');
            }
        ];
        $is_debug = igk_is_debug('dotenv-loading');
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($fc = igk_getv($fcs, $e->tokenID)) {
                    $is_debug && Logger::info('[dotenv-loading] - '.$e->tokenID. ' ['.$e->value.']');
                    $fc($e, $config);
                }
            }
        }
        if ($key) {
            $config[$key] = null;
        }
    }
    /**
     * to string
     * @return mixed 
     */
    public function __toString(): string
    {
        return $this->getValue($this->key, null);
    }
    /**
     * 
     * @param null|string $key 
     * @param mixed $default 
     * @return mixed 
     */
    public function getValue(?string $key=null, $default =null ){
        $key = $key ?? $this->key; 
        return ($this->m_refkey? self::_RegSysDotEnvValue(self::$sm_dotEnv[$this->m_refkey], $key) : null) ??
        self::_RegSysDotEnvValue(self::_GetSysDotEnv(), $key, true) ?? $default;
    }
    /**
     * auto generate doc.
     * @param mixed $tab
     * @param mixed $key
     * @param bool $sys
     * @return mixed
     */
    private static function _RegSysDotEnvValue($tab, $key, bool $sys = false)
    {
        $g = igk_getv($tab, $key);
        if ($g instanceof DotEnvVarConfiguration) {
            $expression = $g->getExpression();
            return self::_TreatExpression($tab, $expression, $g->getKey(), $sys);
        }
        return $g;
    }
    /**
     * auto generate doc.
     * @param mixed $tab
     * @param mixed $expression
     * @param mixed $key
     * @param bool $sys
     * @return string|string[]|null
     */
    private static function _TreatExpression($tab, $expression, $key, bool $sys)
    {
        $ckey = [$key => 1];
        $failed = true;
        $v = preg_replace_callback('/\\$[a-zA-Z_][a-zA-Z_0-9]*/', function ($match) use ($tab, &$ckey, &$failed) {
            $n = substr($match[0], 1);
            if (!isset($ckey[$n])) {
                $ckey[$n] = 1;
            } else {
                throw new \IGKException('loop detected');
            }
            $l = igk_getv($tab, $n);
            $failed = $failed && !is_null($l);
            return $l;
        }, $expression);


        return !$failed && !$sys ? null : $v;
    }
    /**
     * retrieve .env configuration
     * @param string $key the key name 
     * @param mixed $default default value 
     * @return mixed 
     */
    public static function Get(string $key, $default = null)
    {
        if (self::$sm_dotEnv && isset(self::$sm_dotEnv[$key])) {
            return self::$sm_dotEnv[$key];
        }
        return igk_getv(self::_GetSysDotEnv(), $key, $default);
    }

    /**
     * 
     * @param string $file 
     * @return mixed|DotEnvFileConfiguration 
     */
    public static function LoadFromFile(string $file){
        $configs = [];
        self::LoadConfiguration($configs, file_get_contents($file));
        if ($configs){
            return new DotEnvFileConfiguration($configs);
        }
        return $configs;
    }
    /**
     * 
     * @param string $key 
     * @param string $name 
     * @param mixed $default 
     * @return mixed|null 
     */
    public static function GetFromControllerKey(string $key, string $name, $default=null){
        
        if ($gp = igk_getv(self::$sm_dotEnv, $key)){
            if (is_array($gp) && key_exists($name, $gp)){
                return igk_getv($gp, $name);
            }
            return self::Get($name, $default); 
        }
        igk_die('missing configuration name');
    }
}
