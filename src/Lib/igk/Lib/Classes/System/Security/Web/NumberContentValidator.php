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
    * auto generate doc.
    * @var mixed
    */
    protected $notvalid_msg = 'not a valid number.';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $missingDefaultValue = null;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $defaultValue = 0;

    /**
    * auto generate doc.
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