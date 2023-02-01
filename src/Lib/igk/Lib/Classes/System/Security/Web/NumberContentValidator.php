<?php
// @author: C.A.D. BONDJE DOUE
// @file: NumberContentValidator.php
// @date: 20230125 13:49:42
namespace IGK\System\Security\Web;


///<summary></summary>
/**
* number content validator 
* @package IGK\System\Security\Web
*/
class NumberContentValidator extends MapContentValidatorBase
{

    public function map($value, $key, &$error)
    {
        if (is_numeric($value)) {
            return $value;
        }
        $error[$key] = 'not a valid number.';
    }
}