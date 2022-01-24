<?php

namespace IGK\System\Html\Forms;


/**
 * represent core field info
 * @package IGK\System\Html\Forms
 */
class FieldInfo{

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
     * @var mixed
     */
    var $maxlength;

    /**
     * required
     * @var bool
     */
    var $required;


    /**
     * error message
     */
    var $error;
}