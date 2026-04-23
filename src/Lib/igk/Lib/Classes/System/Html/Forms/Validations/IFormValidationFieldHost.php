<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormValidationFieldHost.php
// @date: 20240104 15:36:08
namespace IGK\System\Html\Forms\Validations;
use IGK\System\Html\Forms\IFormValidationField;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
interface IFormValidationFieldHost{
    /**
    * Sets Field Info.
    * @param null|IFormValidationField $field
    * @return ?IFormValidationField
    */
    public function setFieldInfo(?IFormValidationField $field);
    public function getFieldInfo():?IFormValidationField;
}