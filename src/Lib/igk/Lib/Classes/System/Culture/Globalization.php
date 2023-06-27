<?php
// @author: C.A.D. BONDJE DOUE
// @file: Globalization.php
// @date: 20230517 10:46:38
namespace IGK\System\Culture;

use IGK\Resources\R;

///<summary></summary>
/**
* 
* @package IGK\System\Culture
*/
abstract class Globalization{
    var $decimalSeparator = '.';
    var $currencyName = 'EUR';
    var $currencySymbol = '€';
    var $format = '%.2f';
    var $symbolPostFix = true;

    static $sm_regGlobals;


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