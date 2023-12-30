<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\IFormFieldOptions;

/**
 * represent a form field validator
 * @package IGK\System\Html\Forms
 */
interface IFormValidator{
    /**
     * use to assert validation
     * @param mixed $value 
     * @return bool 
     */
    function assertValidate($value):bool;
    /**
     * validate form data 
     * @param mixed|FormValidationParam $value value to check 
     * @param mixed $default default value in case of false re
     * @param null|IFormFieldOptions $fieldinfo field info definition 
     * @param array $error list of error message
     * @return mixed 
     */
    function validate($value, $default=null, & $error=[]);  
}