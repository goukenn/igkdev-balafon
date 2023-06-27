<?php
// @author: C.A.D. BONDJE DOUE
// @filename: _FormCallableValidator.php
// @date: 20220531 11:45:07
// @desc: 

namespace IGK\System\Html\Forms;

/**
 * 
 * @package IGK\System\Html\Forms
 */
class _FormCallableValidator extends FormFieldValidatorBase implements IFormValidator{
    private $m_callable;
    public function __construct(callable $call)
    {
        $this->m_callable = $call;
    }

    public function assertValidate($value): bool { 
        return false;
    }
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        $fc = $this->m_callable;       
        return $fc($value, $default, $fieldinfo, $error);
    }

}