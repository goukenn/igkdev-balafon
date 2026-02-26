<?php
// @author: C.A.D. BONDJE DOUE
// @file: Globalization.php
// @date: 20230517 10:46:38
namespace IGK\System\Culture;
use IGK\Resources\R;
/**
* 
* @package IGK\System\Culture
*/
abstract class Globalization{

    /**
    * Property: decimal separator.
    * @var mixed
    */
    var $decimalSeparator = '.';

    /**
    * Name of currency name.
    * @var mixed
    */
    var $currencyName = 'EUR';

    /**
    * Property: currency symbol.
    * @var mixed
    */
    var $currencySymbol = '€';

    /**
    * Property: format.
    * @var mixed
    */
    var $format = '%.2f';

    /**
    * Property: symbol post fix.
    * @var mixed
    */
    var $symbolPostFix = true;

    /**
    * Property: reg globals.
    * @var mixed
    */
    static $sm_regGlobals;

    /**
    * .ctr
    */
    protected function __construct(){        
    }
    /**
     * get litteral value
     * @param string $v 
     * @return null|string 
     */

    public function getLitteralValue(string $v):?string{
        if (is_numeric($v)){
            $fm = $this->format;
            $fm = $this->symbolPostFix ? $fm.' '.$this->currencySymbol : $this->currencySymbol.' '.$fm;
            return sprintf($fm, $v);
        }
        return null;
    }

    /**
    * From currency format.
    * @param string $format
    * @param null|string $lang
    */
    public static function FromCurrencyFormat(string $format, ?string $lang = null){
        if (is_null(self::$sm_regGlobals)){
            self::$sm_regGlobals = [];
        }
        $lang = $lang ?? R::GetCurrentLang();
        $key = $lang."-".$format;
        if (isset(self::$sm_regGlobals[$key])){
            return self::$sm_regGlobals[$key];
        }
        $cl = __NAMESPACE__."\\".$format."Culture";
        if (class_exists($cl)){
            $r = new $cl();
        } else {
            $r = new DefaultCulture();
        }
        self::$sm_regGlobals[$key] = $r;
        return $r;
    }
}