<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvConfiguration.php
// @date: 20260108 10:35:09
namespace IGK\System\IO;

use AIOWPS\Firewall\File_Prefix_Trait;
use IGK\System\Regex\Replacement;
use IGK\System\Text\RegexMatcherContainer;

/**
 * use to load and 
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
            $refkey = $k;
        }
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
        $pos = 0;
        $src = $content;
        $key = null;
        $v = null;
        $fcs = [
            'litteral' => function ($e) use (&$key) {
                $key = trim($e->value);
            },
            'value' => function ($e, &$config) use (&$key) {
                $v = trim($e->value);
                // igk_dev_wln('value:' . $v);
                if (strpos($v, '$') !== false) {
                    $config[$key] = new DotEnvVarConfiguration($v, $key);
                } else {

                    if (is_numeric($v)) {
                        $v = floatval($v);
                    } else if (in_array($cl = strtolower($v), ['true', 'false'])) {
                        $v = $cl == true;
                    }
                    $config[$key] = trim($e->value);
                }
                $key = null;
            },
            'comment' => function () {},
            'string' => function ($e, &$config) use (&$key) {
                if (empty($key)) igk_die('missing key expression');
                $config[$key] = igk_str_remove_quote($e->value);
                $key = null;
            },
            'expression' => function () {
                igk_wln_e('handle expression //// ');
            }
        ];
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($fc = igk_getv($fcs, $e->tokenID)) {
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
        return self::_RegSysDotEnvValue(self::$sm_dotEnv[$this->m_refkey], $this->key) ??
           self::_RegSysDotEnvValue(self::_GetSysDotEnv(), $this->key, true) ?? '';
    }
    /**
     * 
     * @param mixed $tab 
     * @param mixed $key 
     * @return mixed 
     */
    private static function _RegSysDotEnvValue($tab, $key, bool $sys=false){
        $g = igk_getv($tab, $key);
        if ($g instanceof DotEnvVarConfiguration){
            $expression = $g->getExpression();
            return self::_TreatExpression($tab, $expression, $g->getKey(), $sys);
        }
        return $g;
    }
    /**
     * 
     * @param mixed $tab 
     * @param mixed $expression 
     * @param mixed $key 
     * @return string|string[]|null 
     */
    private static function _TreatExpression($tab, $expression, $key, bool $sys){
        $ckey = [$key=>1];
        $failed = true;
        $v = preg_replace_callback('/\\$[a-zA-Z_][a-zA-Z_0-9]*/', function($match)use($tab, & $ckey, & $failed){
            $n = substr($match[0],1);
            if (!isset($ckey[$n])){
                $ckey[$n] = 1;

            } else{
                throw new \IGKException('loop detected');
            } 
            $l = igk_getv($tab, $n);
            $failed = $failed && !is_null($l);
            return $l;
        }, $expression);


        return !$failed && !$sys ? null:$v;
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
}
