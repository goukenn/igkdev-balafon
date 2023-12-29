<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormFieldOptions.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

/**
 * 
 * @package IGK\System\Html
 * @property ?string $t text;
 * @property ?string $type input type. allowed value text|radio|password|fieldset|efieldset|button|checkbox|hidden|submit|reset|datalist;
 * @property ?string $value default value
 * @property ?string $attrs attribute definition 
 * @property ?string $attribs attribute definition 
 * @property ?string $attributes attribute definition 
 * @property ?string $allow_empty attribute definition 
 * @property ?string $empty_value attribute definition 
 * @property ?string $legend legend for fieldset
 * @property ?bool $required is required field
 * @property ?int $maxlength max length of the field
 * @property ?int $minlength minimum length of the field
 * @property ?string $error error message
 * @property ?string $field name
 * @property ?string $pattern
 * @property array $data entry data. $key=>$value, ['i'=>, 't'=>]
 */
interface IFormFieldOptions{

}