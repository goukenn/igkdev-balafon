<?php
// @author: C.A.D. BONDJE DOUE
// @file: Errors.php
// @date: 20231230 09:37:50
namespace IGK\System\Html\Forms\Validations;

use function igk_resources_gets as __;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class Errors{
    const DISABLE_ARRAY = 1200;


    public static function GetErrors($code){
        return [
            self::DISABLE_ARRAY => 'Converter disable array'
        ];
    }
}