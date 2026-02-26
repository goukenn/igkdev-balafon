<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationParam.php
// @date: 20231229 14:58:36
namespace IGK\System\Html\Forms\Validations;
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
class FormValidationParam extends FormValidationFieldPropsBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $default;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $input;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $output;
    /**
     * error definition
     * @var ?array
     */
    var $error = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $callback;
}