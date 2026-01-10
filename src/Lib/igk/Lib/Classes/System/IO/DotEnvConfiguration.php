<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvConfiguration.php
// @date: 20260108 10:35:09
namespace IGK\System\IO;

use IGK\System\Text\RegexMatcherContainer;

/**
* use to load and 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
class DotEnvConfiguration{
    var $key;    
    private $m_refkey;
    private static $sm_dotEnv;
    private static function loadingEnvDev(& $refkey, $ctrl=null){
        $ctrl = $ctrl ?? igk_environment()->controller_config_loading;
        if (is_null(self::$sm_dotEnv)){
            self::$sm_dotEnv = [];
        }
        if ($ctrl){
            $k = $ctrl->name();
            if (!isset(self::$sm_dotEnv[$k])){
                $dir = $ctrl->getDeclaredDir();
                $config = [];
                $tc = [''];
                if (igk_environment()->isDev()){
                    $tc[] = '.dev';
                }else {
                    $tc[] = '.production';
                }
                foreach($tc as $c){
                    $f = Path::Combine($dir, '.env'.$c);
                    if (file_exists($f)){
                        $content = file_get_contents($f);
                        self::LoadConfiguration($config, $content);
                    }
                }
                self::$sm_dotEnv[$k] = $config;
            }           
            $refkey = $k;
        }

    }
    public function __construct(string $value, $ctrl=null)
    {
        self::loadingEnvDev($this->m_refkey, $ctrl);
        $this->m_refkey || igk_die('loading environment failed');
        $this->key = $value; 
    } 
    /**
     * load configurations
     * @param array &$config 
     * @param string $content 
     * @return void 
     */
    public static function LoadConfiguration(array & $config, string $content){
        $regex = new RegexMatcherContainer;
        $regex->match('^#.*$', 'comment');
        $regex->appendStringDetection('string', true);
        $regex->match('=', 'separator');
        $regex->match('(?<=).+$', 'value');
        $regex->match('(?i)[a-z_][a-z0-9_]*', 'litteral');
        $pos=0;
        // define
        $src = $content;
        $key = null;
        $v = null;
        $fcs = ['litteral'=>function($e)use(& $key){
            $key = trim($e->value); 
        }, 'value'=>function($e, & $config)use(& $key){
            $v = trim($e->value);

            if (is_numeric($v)){
                $v = floatval($v);
            }else if (in_array($cl = strtolower($v), ['true', 'false'])){
                $v = $cl == true;
            }
            $config[$key] = trim($e->value);
            $key = null;
        }, 'comment'=>function(){

        }, 'string'=>function($e, & $config)use (& $key){
            if (empty($key)) igk_die('missing key expression');
            $config[$key] = igk_str_remove_quote($e->value);
            $key = null;
        }];
        
        while($g = $regex->detect($src, $pos)){
            if ($e = $regex->end($g, $src, $pos)){
                if ($fc = igk_getv($fcs, $e->tokenID)){ 
                    igk_debug_wln('tokenID:'.$e->tokenID);
                    $fc($e, $config);
                }
            }
        }
        if ($key){
            $config[$key] = null;
        } 
    }
    public function __toString()
    {
       return igk_getv(self::$sm_dotEnv[$this->m_refkey], $this->key);
    }
}