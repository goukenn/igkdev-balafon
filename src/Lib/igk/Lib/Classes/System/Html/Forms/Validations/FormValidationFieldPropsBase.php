<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationFieldPropsBase.php
// @date: 20231229 16:01:23
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
abstract class FormValidationFieldPropsBase{
 /**
     * 
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
     * 
     * @var ?ObjectStorage of the field information
     */
    var $fieldInfo; 
}