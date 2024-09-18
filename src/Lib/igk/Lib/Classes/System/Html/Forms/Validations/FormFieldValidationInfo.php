<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidationInfo.php
// @date: 20231228 21:50:33
namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\Forms\IFormValidationField;
use IGK\System\Html\Forms\Validations\Traits\FormFieldValidationInfoTrait;

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
    
    use FormFieldValidationInfoTrait; 
}