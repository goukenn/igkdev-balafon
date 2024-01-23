<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidatorContainerBase.php
// @date: 20231229 18:41:42
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
abstract class FormFieldValidatorContainerBase extends FormFieldValidatorBase{
    /**
     * get fields for form validation 
     * @return array 
     */
    public abstract function getFields(): array;
}