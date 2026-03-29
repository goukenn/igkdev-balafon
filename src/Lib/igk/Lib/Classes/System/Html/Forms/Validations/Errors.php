<?php
// @author: C.A.D. BONDJE DOUE
// @file: Errors.php
// @date: 20231230 09:37:50
namespace IGK\System\Html\Forms\Validations;
use function igk_resources_gets as __;
/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class Errors{
    /**
    * Constant: disable array.
    * @var mixed
    */
    const DISABLE_ARRAY = 1200;
    /**
    * Returns Errors.
    * @param mixed $code
    */
    public static function GetErrors($code){
        return [
            self::DISABLE_ARRAY => 'Converter disable array'
        ];
    }
}