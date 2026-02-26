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
class FormCallableValidatorInternal extends FormFieldValidatorBase implements IFormValidator{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_callable;

    /**
    * .ctr
    * @param callable $call
    */
    public function __construct(callable $call)
    {
        $this->m_callable = $call;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool { 
        return false;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    * @param null|mixed $default
    * @param array & $error
    * @param null|object $options
    */
    protected function _validate($value, $default=null, array & $error=[], ?object $options=null){ 
        $fc = $this->m_callable;       
        return $fc($value, $default, $error);
    }
}