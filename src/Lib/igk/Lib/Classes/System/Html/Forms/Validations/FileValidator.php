<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileValidator.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Forms\Validations;
use function igk_resources_gets as __;


/**
 * file fields validator
 * @package IGK\System\Html\Forms
 */
class FileValidator extends FormFieldValidatorBase implements IFormValidator{
    var $name;
    var $fieldInfo;
    
    public function assertValidate($value): bool {
        /// TODO: Expect file validation data 
        return false;
    }

    protected function _validate($value, $default=null, & $error=[], ?object $options=null){    
        return $value;
    }

}