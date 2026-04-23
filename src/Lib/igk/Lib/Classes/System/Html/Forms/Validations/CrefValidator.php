<?php
// @author: C.A.D. BONDJE DOUE
// @file: CrefValidator.php
// @date: 20240104 16:29:32
namespace IGK\System\Html\Forms\Validations;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class CrefValidator extends HiddenValidator{
    /**
    * Validate.
    * @param mixed $value
    * @param null|mixed $default
    * @param null|array & $errors
    * @param null|mixed $options
    */
    public function _validate($value, $default=null, ?array & $errors = null, $options=null ){
        if (igk_valid_cref(1)){
            return $value;
        }
        $error[] = 'not a valid cref';
        return false;
    }
}