<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormValidationField.php
// @date: 20240104 13:47:09
namespace IGK\System\Html\Forms;
/**
 * represent a validated field info
 * @package IGK\System\Html\Forms
 * @author C.A.D. BONDJE DOUE
 * @property ?bool $allowEmpty allow empty value 
 * @property ?bool $allowNull attribute allow Null value  
 * @property ?bool $required is required field
 * @property ?int $maxLength max length of the field
 * @property ?int $minLength minimum length of the field
 * @property ?string $error error message
 * @property ?string $pattern pattern used to validate data
 * @property string $type type of the data 
 */
interface IFormValidationField
{ 
}