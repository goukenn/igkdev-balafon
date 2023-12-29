<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FieldInfo.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Forms;

use IGK\System\Html\IFormFieldOptions;

/**
 * represent core field info
 * @package IGK\System\Html\Forms
 */
class FieldInfo implements IFormFieldOptions{

    /**
     * type of the fields
     * @var  null|string
     */
    var $type;

    /**
     * 
     * @var null|string
     */
    var $pattern;

    /**
     * max length
     * @var ?int
     */
    var $maxlength;

    /**
     * required
     * @var ?bool
     */
    var $required;


    /**
     * error message
     */
    var $error;

    /**
     * place holder message
     * @var mixed
     */
    var $placeholder;


    /**
     * min length
     * @var ?int
     */
    var $minLength;
}