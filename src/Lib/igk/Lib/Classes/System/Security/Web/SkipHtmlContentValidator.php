<?php
// @author: C.A.D. BONDJE DOUE
// @file: SkipHtmlContentValidator.php
// @date: 20230125 13:47:37
namespace IGK\System\Security\Web;
use IGK\System\Html\Forms\HtmlValidator;

/**
* auto generate doc.
* @package IGK\System\Security\Web
*/
class SkipHtmlContentValidator extends MapContentValidatorBase
{

    /**
    * Property: validator.
    * @var mixed
    */
    private $m_validator;

    /**
    * Validates.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    protected function validate(&$value, $key): bool { 
        return true;
    }

    /**
    * Returns Validator.
    */
    protected function getValidator()
    {
        return new HtmlValidator;
    }

    /**
    * Map.
    * @param mixed $value
    * @param mixed $key
    * @param mixed & $error
    * @param bool $missing
    * @param bool $required
    */
    public function map($value, $key, &$error, bool $missing=false, bool $required = true)
    {
        if (!$this->m_validator) {
            $this->m_validator = $this->getValidator();
        }
        if (strpos($value,"\xF0") !== false){
            $value = igk_str_encode_to_utf8($value);
        }
        // $value = igk_str_encode_to_utf8($value);
        return $this->m_validator->treatValue($value);
    }
}