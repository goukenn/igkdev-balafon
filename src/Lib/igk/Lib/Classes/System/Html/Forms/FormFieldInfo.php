<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldInfo.php
// @date: 20231228 12:19:12
namespace IGK\System\Html\Forms;

use IGK\System\Html\Forms\Validations\IFormValidationInfo; 

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms
*/
class FormFieldInfo extends FieldInfo implements IFormValidationInfo{
    /**
     * 
     * @var ?string
     */
    var $id;

    // /**
    //  * 
    //  * @var ?string
    //  */
    // var $type;

    /**
     * field form validator
     * @var ?FormFieldValidatorBase
     */
    var $validator;

    // /**
    //  * get or set if the field is required
    //  * @var ?bool
    //  */
    // var $required;

    /**
     * 
     * @var ?string $error confiured message in case of error 
     */
    var $error;

    /**
     * 
     * @var mixed
     */
    var $default;

}