<?php
// @author: C.A.D. BONDJE DOUE
// @file: IntContentValidator.php
// @date: 20230125 13:48:58
namespace IGK\System\Security\Web;


///<summary>string int content validator</summary>
/**
* string int content validator
* @package IGK\System\Security\Web
*/
class IntContentValidator extends MapContentValidatorBase
{
    public function map($value, $key, &$error)
    {
        if (is_numeric($value)) {
            return intval($value);
        }
        $error[$key] = 'not a valid integer.';
    }
}