<?php
// @author: C.A.D. BONDJE DOUE
// @file: LocaleSetting.php
// @date: 20221121 09:54:10
namespace IGK\System\Configuration;
use IGK\Helper\StringUtility;
use IGK\Resources\R;
use function igk_resources_gets as __;
/**
* 
* @package IGK\System\Configuration
*/
class LocaleSetting{

    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;

    /**
    * Property: setting.
    * @var mixed
    */
    private $setting;

    /**
    * Returns Instance.
    */
    public static function getInstance(){
        return self::$sm_instance ?? self::$sm_instance = new self;
    }

    /**
    * .ctr
    */
    protected function __construct(){        
    }
    /**
     * get Locale configuration properties
     * @param string $format 
     * @return mixed 
     */

    public static function Get(string $format){
        $format = StringUtility::CamelClassName($format);
        $i = self::getInstance();
        if (method_exists($i, $fc = 'get'.$format)){
            return call_user_func_array([$i, $fc], []);
        }
    }

    /**
    * Returns Date Format.
    */
    public function getDateFormat(){
        if (__($k = "@date_format") == $k){
            switch(strtolower(R::GetCurrentLang() ?? 'fr')){
                case 'fr':
                    return 'd-m-Y';
                default:
                    return 'Y-m-d';                
            }
        }
        return $k;
    }
}