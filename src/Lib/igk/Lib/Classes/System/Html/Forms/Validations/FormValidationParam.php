<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationParam.php
// @date: 20231229 14:58:36
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
class FormValidationParam extends FormValidationFieldPropsBase{
    
    
    var $default;
    var $input;
    var $output;
    /**
     * error definition
     * @var ?array
     */
    var $error = [];
 
    var $callback;
}