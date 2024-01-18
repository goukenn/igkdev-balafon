<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormValidationFieldHost.php
// @date: 20240104 15:36:08
namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\Forms\IFormValidationField;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
interface IFormValidationFieldHost{
    public function setFieldInfo(?IFormValidationField $field);
    public function getFieldInfo():?IFormValidationField;
}