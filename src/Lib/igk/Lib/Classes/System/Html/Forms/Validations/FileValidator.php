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

    public function assertValidate($value): bool {
        /// TODO: Expect file validation data 
        return false;
    }

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
   
        return $value;
    }

}