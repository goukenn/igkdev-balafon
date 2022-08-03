<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PatternValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;

/**
 * pattern validator
 * @package IGK\System\Html\Forms
 */
class PatternValidator implements IFormValidator, IFormPatternValidator{
    private $m_pattern;

    public function setPattern(string $pattern) {
        $this->m_pattern = $pattern;
     }

    public function matchPattern($value) { 
        return preg_match($this->m_pattern, $value);
    }
    
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (!$this->matchPattern($value)){
            return $default;
        }
        return $value;
    }

}