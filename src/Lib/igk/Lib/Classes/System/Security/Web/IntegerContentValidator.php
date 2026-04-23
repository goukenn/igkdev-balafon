<?php
// @author: C.A.D. BONDJE DOUE
// @file: IntegerContentValidator.php
// @date: 20230303 20:55:22
namespace IGK\System\Security\Web;

/**
* auto generate doc.
* @package IGK\System\Security\Web
*/
class IntegerContentValidator extends NumberContentValidator{
    /**
    * Property: default value.
    * @var mixed
    */
    var $defaultValue = 0;
    /**
    * Property: missing default value.
    * @var mixed
    */
    var $missingDefaultValue = null;
    /**
    * Validates.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    public function validate(&$value, $key): bool
    {
        if ($r = (is_integer($value) || is_numeric($value))){
            $value = intval($value); 
        } 
        return $r;
    }
}