<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormData.php
// @date: 20230205 21:40:41
namespace IGK\System\WinUI\Forms;
use Closure;
use Exception;
use IGK\Helper\Activator;
use IGK\System\Data\IDataValidator;
use IGK\System\Data\ObjectDataValidator;
use IGK\System\EntryClassResolution;
use IGK\System\Http\Request;
use IGK\System\Traits\ActivableTrait;
use IGK\System\WinUI\Forms\FormValidationData;
use IGKException;
/**
 * used to setup data for html's form
 * @package IGK\System\WinUI\Forms
 */
abstract class FormData
{
    use ActivableTrait;
    // + | refer to [name]ContentValidator class

    /**
    * Constant: sc email.
    * @var mixed
    */
    const SC_EMAIL = 'Email';

    /**
    * Constant: sc password.
    * @var mixed
    */
    const SC_PASSWORD = 'Password';

    /**
    * Constant: sc integer.
    * @var mixed
    */
    const SC_INTEGER = 'Integer';

    /**
    * Constant: sc number.
    * @var mixed
    */
    const SC_NUMBER = 'Number';

    /**
    * Constant: sc text.
    * @var mixed
    */
    const SC_TEXT = 'Text';
     /**
     * extract fields 
     * @param null|array $fields 
     * @return array 
     */

    public function to_array(?array $fields = null): array{
       return (array)$this;
    }
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
    /**
     * 
     * @param mixed $var_tab 
     * @param mixed $tab 
     * @return array 
     */

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
    /**
     * get class reference used to get properties 
     * @return string
     */

    protected function getValidationClassReference()
    {
        return static::class;
    }
    /**
     * array of mapper fields
     * @return (string|int)[] 
     */

    protected function getMapperFields(){
        $ls = array_keys(get_class_vars($this->getValidationClassReference()));
        return $ls;
    }
    /**
     * 
     * @param null|array $tab 
     * @return FormValidationData 
     */

    protected function getDataValidatorMapper(?array $tab = null)
    {
        $ls = $this->getMapperFields();
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
     * associative array of default custom value
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
     * @return false|static|object validated data or false - static::class's properties only 
     */

    public static function ValidateData($data, ?object $validator = null, ?array &$errors = null)
    {
        if (!$data){
            return false;
        }
        $validata_class = EntryClassResolution::CreateValidatorInstance;
        $validator = $validator ??
            (method_exists(static::class, $validata_class) ?
                call_user_func_array([static::class, $validata_class], []) : null) ??
            new ObjectDataValidator();
        /**
         * @var {validate():null}|null $validator
         */
        $e = new static;
        $validation_mapper = $e->getDataValidatorMapper();
        $requestData = [];
        if ($validator instanceof IDataValidator)
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
     * validate data if ok create a form information 
     * @param mixed $data 
     * @param null|object $validator 
     * @param null|array &$error 
     * @return ?static 
     * @throws IGKException 
     * @throws Exception 
     */

    public static function ValidateDataAndCreateInstance($data, ?object $validator = null, ?array & $error = null){
        if ($r = self::ValidateData($data, $validator, $error)){
            return Activator::CreateNewInstance(static::class, $r->getData());
        }
        return $r;
    }
    /**
     * use to retrieve the fields to use in a form
     * @return array 
     */

    public static function Fields():array
    { 
        return array_keys(get_class_vars(static::class));
    }
}