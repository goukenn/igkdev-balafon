<?php
// @author: C.A.D. BONDJE DOUE
// @filename: _FormCallableValidator.php
// @date: 20220531 11:45:07
// @desc: 

namespace IGK\System\Html\Forms\Validations;

/**
 * internal use of callable validation
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
    protected function _validate($value, $default=null, array & $error=[], ?object $options=null){ 
        $fc = $this->m_callable;       
        return $fc($value, $default, $error);
    }

}