<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationParam.php
// @date: 20231229 14:58:36
namespace IGK\System\Html\Forms\Validations;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
*/
class FormValidationParam extends FormValidationFieldPropsBase{
    /**
    * Property: default.
    * @var mixed
    */
    var $default;
    /**
    * Property: input.
    * @var mixed
    */
    var $input;
    /**
    * Property: output.
    * @var mixed
    */
    var $output;
    /**
     * error definition
     * @var ?array
     */
    var $error = [];
    /**
    * Callback handler for callback.
    * @var mixed
    */
    var $callback;
}