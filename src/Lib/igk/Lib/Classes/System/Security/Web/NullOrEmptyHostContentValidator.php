<?php

namespace IGK\System\Security\Web;

/**
 * null or empty host content validator
 * @package IGK\System\Security\Web
 */
class NullOrEmptyHostContentValidator extends MapContentValidatorBase{
    private $parent;
    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function map($value, $key, &$error) { 
        if (empty($value)){
            return null;
        }
        return $this->parent->map($value, $key, $error);        
    } 
}