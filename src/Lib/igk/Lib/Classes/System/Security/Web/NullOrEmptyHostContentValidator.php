<?php
namespace IGK\System\Security\Web;
/**
 * null or empty host content validator
 * @package IGK\System\Security\Web
 */
class NullOrEmptyHostContentValidator extends MapContentValidatorBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $parent;

    /**
    * .ctr
    * @param mixed $parent
    */
    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    * @param mixed $key
    * @param mixed & $error
    * @param bool $missing
    * @param bool $required
    */
    public function map($value, $key, &$error, bool $missing=false, bool $required = true) { 
        if (empty($value)){
            return null;
        }
        return $this->parent->map($value, $key, $error);        
    } 
}