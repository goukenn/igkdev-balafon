<?php
// @author: C.A.D. BONDJE DOUE
// @file: SkipHtmlContentValidator.php
// @date: 20230125 13:47:37
namespace IGK\System\Security\Web;

use IGK\System\Html\Forms\HtmlValidator;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class SkipHtmlContentValidator extends MapContentValidatorBase
{
    private $m_validator;

    protected function validate(&$value, $key): bool { 
        return true;
    }
    protected function getValidator()
    {
        return new HtmlValidator;
    }
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