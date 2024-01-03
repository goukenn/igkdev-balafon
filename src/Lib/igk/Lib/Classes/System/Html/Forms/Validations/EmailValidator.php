<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EmailValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\Forms\FieldInfo;
use IGKValidator;

use function igk_resources_gets as __;


/**
 * validate pssword from fields
 * @package IGK\System\Html\Forms
 */
class EmailValidator extends FormFieldValidatorBase implements IFormValidator{

    protected function _validate($value, $default = null, array &$error = [], ?object $options = null) { 
        if (is_string($value) && ($s = trim($value))){
            if ($this->assertValidate(($s))){
                return $s;
            }
            if ($default && $this->assertValidate($default)){
                return $default;
            }

        }
        $error[] = 'email is not valid.';
    }

    public function assertValidate($value): bool { 
        return $value && IGKValidator::IsEmail($value);
    }
}