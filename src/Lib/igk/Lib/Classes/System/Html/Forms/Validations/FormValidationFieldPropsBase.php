<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationFieldPropsBase.php
// @date: 20231229 16:01:23
namespace IGK\System\Html\Forms\Validations;
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
*/
abstract class FormValidationFieldPropsBase{
    /**
    * auto generate doc.
    * @var ?name of the validation parameter
    */
    var $name;
    /**
     * field is required
     * @var mixed
     */
    var $required;
    /**
     * field allow null response
     * @var mixed
     */
    var $allowNull;
    /**
     * allow empty value
     * @var ?bool
     */
    var $allowEmpty;
    /**
    * auto generate doc.
    * @var ?ObjectStorage of the field information
    */
    var $fieldInfo; 
}