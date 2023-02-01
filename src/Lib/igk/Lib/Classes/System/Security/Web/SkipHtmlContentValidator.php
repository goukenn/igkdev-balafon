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
    protected function getValidator()
    {
        return new HtmlValidator;
    }
    public function map($value, $key, &$error)
    {
        if (!$this->m_validator) {
            $this->m_validator = $this->getValidator();
        }
        if (strpos($value,"\xF0") !== false){
            $value = utf8_encode($value);
        }
        // $value = utf8_encode($value);
        return $this->m_validator->treatValue($value);
    }
}