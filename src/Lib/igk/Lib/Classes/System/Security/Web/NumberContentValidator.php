<?php
// @author: C.A.D. BONDJE DOUE
// @file: NumberContentValidator.php
// @date: 20230125 13:49:42
namespace IGK\System\Security\Web;

/**
 * number content validator 
 * @package IGK\System\Security\Web
 */
class NumberContentValidator extends MapContentValidatorBase
{
    /**
    * Property: notvalid msg.
    * @var mixed
    */
    protected $notvalid_msg = 'not a valid number.';
    /**
    * Property: missing default value.
    * @var mixed
    */
    var $missingDefaultValue = null;
    /**
    * Property: default value.
    * @var mixed
    */
    var $defaultValue = 0;
    /**
    * Validates.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    protected function validate(& $value, $key):bool{ 
        if ($r = is_numeric($value)){
            $value =  floatval($value);
        }
        return $r;
    }
}