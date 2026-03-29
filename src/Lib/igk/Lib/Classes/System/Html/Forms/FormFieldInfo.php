<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldInfo.php
// @date: 20231228 12:19:12
namespace IGK\System\Html\Forms;
use IGK\System\Html\Forms\Validations\IFormValidationInfo;
use IGK\System\Html\Forms\Validations\Traits\FormFieldValidationInfoTrait;
/**
* 
* @package IGK\System\Html\Forms
*/
/**
* auto generate doc.
* @package IGK\System\Html\Forms
*/
class FormFieldInfo extends FieldInfo implements IFormValidationInfo{
    use FormFieldValidationInfoTrait; 
    /**
    * auto generate doc.
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
    * auto generate doc.
    * @var ?string $error confiured message in case of error
    */
    var $error;
    /**
    * auto generate doc.
    * @var mixed
    */
    var $default; 
    /**
     * max size
     * @var mixed
     */
    var $maxSize;
    /**
    * auto generate doc.
    */    var $multiple;
    /**
     * mime type
     */
    var $accept;
}