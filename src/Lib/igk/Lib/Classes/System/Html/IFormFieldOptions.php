<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormFieldOptions.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

use IGK\System\Html\Forms\IFormValidationField;
use IGK\System\Html\Forms\Validations\FormFieldValidationInfo;

/**
 * represent a form field options declaration
 * @package IGK\System\Html
 * @property ?string $t simple text field 
 * @property ?string $type input type. allowed value text|radio|password|fieldset|efieldset|button|checkbox|hidden|submit|reset|datalist;
 * @property ?string $value default value
 * @property ?string $attrs attribute definition 
 * @property ?string $attribs attribute definition 
 * @property ?string $attributes attribute definition 
 * @property ?string $legend legend for fieldset
 * @property ?string $field name
 * @property ?string $pattern
 * @property array $data entry data. $key=>$value, ['i'=>, 't'=>]
 */
interface IFormFieldOptions extends IFormValidationField{

}