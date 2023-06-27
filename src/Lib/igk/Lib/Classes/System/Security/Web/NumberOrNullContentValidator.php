<?php
// @author: C.A.D. BONDJE DOUE
// @file: NumberOrNullContentValidator.php
// @date: 20230125 13:50:19
namespace IGK\System\Security\Web;


///<summary></summary>
/**
* number content validator
* @package IGK\System\Security\Web
*/
class NumberOrNullContentValidator extends MapContentValidatorBase
{

    protected function validate(&$value, $key): bool {
        return true;
     }
    public function map($value, $key, &$error, bool $missing=false, bool $required = true)
    {
        if (empty($value) || is_null($value) || is_numeric($value)) {
            if (empty($value)){
                $value = null;
            }
            return $value;
        }
        $error[$key] = 'not a valid number or null.';
    }
}