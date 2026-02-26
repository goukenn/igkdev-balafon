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
    * Property: pattern.
    * @var mixed
    */
    private $m_pattern;

    /**
    * Asserts Validate.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool { 
        return $this->matchPattern($value);
    }

    /**
    * Sets Pattern.
    * @param string $pattern
    */
    public function setPattern(string $pattern) {
        $this->m_pattern = $pattern;
     }

    /**
    * Matches Pattern.
    * @param mixed $value
    */
    public function matchPattern($value) { 
        return preg_match($this->m_pattern, $value);
    }

    /**
    * Validate.
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