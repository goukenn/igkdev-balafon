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
     * @var  null|string|'text'
     */
    var $type;
    /**
     * 
     * @var null|string
     */
    var $pattern; 
    /**
     * get if required
     * @var ?bool
     */
    var $required = false;
    /**
     * error message
     * @var ?string
     */
    var $error;

    /**
     * place holder message
     * @var ?string
     */
    var $placeholder;

    /**
     * max length
     * @var ?int
     */
    var $maxLength;
    /**
     * min length
     * @var ?int
     */
    var $minLength;

    public function __construct()
    {
        $this->type = 'text';
    }
}