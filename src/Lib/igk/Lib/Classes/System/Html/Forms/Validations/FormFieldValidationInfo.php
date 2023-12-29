<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidationInfo.php
// @date: 20231228 21:50:33
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
class FormFieldValidationInfo implements IFormValidationInfo{
    /**
     * message in case of error
     * @var ?string 
     */
    var $error;
    /**
     * the validator to use
     * @var mixed
     */
    var $validator;
    /**
     * is required
     * @var ?bool
     */
    var $required; 
}