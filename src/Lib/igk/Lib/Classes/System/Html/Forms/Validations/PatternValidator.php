<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PatternValidator.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Forms\Validations;
/**
 * pattern validator
 * @package IGK\System\Html\Forms
 */
class PatternValidator  extends FormFieldValidatorBase implements IFormValidator, IFormPatternValidator{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_pattern;

    /**
    * auto generate doc.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool { 
        return $this->matchPattern($value);
    }

    /**
    * auto generate doc.
    * @param string $pattern
    */
    public function setPattern(string $pattern) {
        $this->m_pattern = $pattern;
     }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function matchPattern($value) { 
        return preg_match($this->m_pattern, $value);
    }

    /**
    * auto generate doc.
    * @param mixed $value
    * @param null|mixed $default
    * @param mixed & $error
    * @param null|object $options
    */
    protected function _validate($value, $default=null, & $error=[], ?object $options=null){    
        if (!$this->matchPattern($value)){
            return $default;
        }
        return $value;
    }
}