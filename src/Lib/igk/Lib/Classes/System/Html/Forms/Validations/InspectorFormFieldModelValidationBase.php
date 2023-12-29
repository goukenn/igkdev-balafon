<?php
// @author: C.A.D. BONDJE DOUE
// @file: InspectorFormFieldModelValidationBase.php
// @date: 20231229 09:51:52
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* use to inspect model base data 
* @package IGK\System\Html\Forms\Validations
*/
abstract class InspectorFormFieldModelValidationBase extends InspectorFormFieldValidationBase{
    abstract function modelMap():array;
}