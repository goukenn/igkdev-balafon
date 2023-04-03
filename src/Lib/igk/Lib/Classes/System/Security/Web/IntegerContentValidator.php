<?php
// @author: C.A.D. BONDJE DOUE
// @file: IntegerContentValidator.php
// @date: 20230303 20:55:22
namespace IGK\System\Security\Web;


///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class IntegerContentValidator extends NumberContentValidator{
 
    var $defaultValue = 0;
    var $missingDefaultValue = null;

    public function validate(&$value, $key): bool
    {
        if ($r = (is_integer($value) || is_numeric($value))){
            $value = intval($value); 
        } 
        return $r;
    }
}