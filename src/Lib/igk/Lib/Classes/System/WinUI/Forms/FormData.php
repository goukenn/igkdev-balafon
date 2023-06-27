<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormData.php
// @date: 20230205 21:40:41
namespace IGK\System\WinUI\Forms;

use Closure;
use IGK\Helper\Activator;
use IGK\System\Data\IDataValidator;
use IGK\System\Data\ObjectDataValidator;
use IGK\System\Http\Request;
use IGK\System\Traits\ActivableTrait;
use IGK\System\WinUI\Forms\FormValidationData;

///<summary></summary>
/**
 * 
 * @package IGK\System\WinUI\Forms
 */
abstract class FormData
{
    use ActivableTrait;

    // + | refer to [name]ContentValidator class 
    const SC_EMAIL = 'Email';
    const SC_PASSWORD = 'Password';
    const SC_INTEGER = 'Integer';
    const SC_NUMBER = 'Number';
    const SC_TEXT = 'Text';

    /**
     * get validation mapper
     * @param Request $request 
     * @return ?array 
     */
    public function getValidationMapperFromRequest(Request $request): FormValidationData
    {
        $ls = array_keys(get_class_vars(static::class));
        $tab = $this->getContentSecureFormRequest($request) ?? [];
        // Remove all  
        // copy only definition
        $tab = $this->mergeSecure($ls, $tab);
        return $this->getDataValidatorMapper($tab);
    }
    protected function mergeSecure($var_tab, $tab)
    {
        $rtab = [];
        foreach ($var_tab as $t) {
            if (!isset($tab[$t])) {
                $rtab[] = $t;
            } else {
                $rtab[$t] = $tab[$t];
            }
        }
        return $rtab;
    }
    protected function getValidationClassReference()
    {
        return static::class;
    }
    /**
     * 
     * @param null|array $tab 
     * @return FormValidationData 
     */
    protected function getDataValidatorMapper(?array $tab = null)
    {
        $ls = array_keys(get_class_vars($this->getValidationClassReference()));
        if (is_null($tab)) {
            $tab = $ls;
        }
        $_o = [];
        if ($v_ = $this->getNotRequired()) {
            $this->_ExpandValue($_o, $v_, $ls, true);
        }
        $v_not_required = $_o;
        $_o = [];
        if ($v_ = $this->getDefaultValues()) {
            $this->_ExpandValue($_o, $v_, $ls);
        }
        $v_defaults = $_o;
        $frm = new FormValidationData;
        $frm->mapper = $tab;
        $frm->defaultValues = $v_defaults;
        $frm->not_required = $v_not_required;
        return $frm;
    }
    /**
     * expand value 
     * @param mixed $_o 
     * @param mixed $v_ 
     * @param mixed $ls 
     * @return void 
     */
    private static function _ExpandValue(&$_o, $v_, $ls, $not_required = false)
    {
        foreach ($v_ as $k => $b) {
            if ($not_required && ($b instanceof Closure)) {
                $_o = [$b];
                return;
            }
            if (is_numeric($k) && in_array($b, $ls)) {
                $_o[$b] = null;
            } else {
                if (in_array($k, $ls))
                    $_o[$k] = $b;
            }
        }
    }
    /**
     * assoc of default custom value
     * @return null|array 
     */
    public function getDefaultValues(): ?array
    {
        return null;
    }

    /**
     * get not required fields [ key => missing default value, $key]
     * @return null|array 
     */
    protected function getNotRequired(): ?array
    {
        return null;
    }
    /**
     * get content secure field
     * @return null|array 
     */
    protected function getContentSecureFormRequest(Request $request): ?array
    {
        return null;
    }

    /**
     * validate from json request
     * @param Request $request 
     * @return static|false|\IGK\System\DataArgs data argument that implement static definition
     */
    public static function ValidateJSon(Request $request, $validator, ?array &$errors = null)
    {
        if ($g = $validator->validateJSon($request, static::class, $errors)) {
            return $g;
        }
        return false;
    }
    /**
     * validate data
     * @param mixed $data 
     * @param null $validator 
     * @return false|mixed validated data or false 
     */
    public static function ValidateData($data, ?object $validator = null, ?array &$errors = null)
    {
        $validator = $validator ??
            (method_exists(static::class, \CreateValidatorInstance::class) ?
                call_user_func_array([static::class, \CreateValidatorInstance::class], []) : null) ??
            new ObjectDataValidator();

        $e = new static;
        $validation_mapper = $e->getDataValidatorMapper();
        $requestData = [];        
        if ($validator->validate(
            $data,
            $validation_mapper->mapper,
            $validation_mapper->defaultValues,
            $validation_mapper->not_required,
            $requestData,
            $errors,
            $validation_mapper->resolvKeys
        )) {
            return $requestData;
        }
        return false;
    }

    /**
     * use to retrieve the fields to use in a form
     * @return array 
     */
    public static function Fields()
    {
        $c = new static;
        $tab = get_class_vars(static::class);
        return [$tab];
    }
}
