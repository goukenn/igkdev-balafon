<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FormValidation.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

use IGK\Helper\Activator;
use IGK\Helper\StringUtility;
use IGKException;
use IGKObjStorage;
use function igk_resources_gets  as __;

require_once __DIR__ . "/IFormValidator.php";
require_once __DIR__ . "/IFormPatternValidator.php";
/**
 * use to validate form data
 * @package IGK\System\Html\Forms\Validations
 */
class FormValidation
{

    /**
     * @
     */
    var $skipNullValue = true;
    /**
     * 
     * @var mixed
     */
    var $uri;

    /**
     * use storage data object in case of good data
     * @var true
     */
    var $storage = true;
    /**
     * fields to validate
     * @var mixed
     */
    private $_fields;

    /**
     * error message
     * @var mixed
     */
    private $m_errors;

    /**
     * register custom validator
     * @var array
     */
    private $m_validators = [];

    /**
     * return stored error
     * @return array
     */
    public function getErrors()
    {
        return $this->m_errors;
    }
    /**
     * set array of validation
     * @param array $fields 
     * @return self 
     */
    public function fields(array $fields)
    {
        $this->_fields = $fields;
        return $this;
    }


    /**
     * register validator
     * @param mixed $name 
     * @param null|callable|IFormValidator $callable 
     * @return void 
     */
    public function registerValidator($name, $callable)
    {
        if ($callable === null) {
            unset($this->m_validators[$name]);
        } else {
            if (is_callable($callable)) {
                $callable = new _FormCallableValidator($callable);
            } else if (is_object($callable) && !($callable instanceof IFormValidator)) {
                return;
            }
            $this->m_validators[$name] = $callable;
        }
    }
    /**
     * used to validate files
     */
    public function files(?array $filedata = null)
    {
        if ($filedata === null) {
            $filedata = $_FILES;
        }
        $this->m_errors = [];
        $result = false;
        $out_data = [];
        foreach ($this->_fields as $k => $data) {
            if (igk_getv($data, "type") == "file") { // filter files
                $validator = new FileValidator();
                $validator->fieldInfo = $data;
                $validator->name = $k;
                $storage = new IGKObjStorage($data);
                $storage->name = $k;
                $v = igk_getv($filedata, $k);
                $v = $validator->validate($v, $storage->default, $this->m_errors);
                $out_data[$k] = $v;
            }
        }
        if (count($this->m_errors) == 0) {
            return $out_data;
        } 
        return $result;
    }
    /**
     * 
     * @param array $request array that simulate the request
     * @return bool|array|IGKObjStorage if storage will return an object storage or array
     */
    public function validate(array $request)
    {
        // + | reset the error list
        $this->m_errors = [];
        $result = false;

        if (empty($request)) {
            $this->m_errors[] = __("validation: empty request not allowed");
        }
        if (empty($this->_fields)) {
            $this->m_errors[] = __("no validator setup");
        }
        if (count($this->m_errors) == 0) {

            // + | stats validation propcess 
            /**
             * @var ?IGKObjStorage $storage 
             */
            $storage = null;
            $out_data = [];
            $root_data = &$out_data;
            $v_rfd = [['o' => &$out_data, 'f' => $this->_fields, 'v'=>$request]];
            while (count($v_rfd) > 0) {
                $v_dof = array_shift($v_rfd);
                $out_data = &$v_dof['o']; 
                $request = $v_dof['v']; 

                foreach ($v_dof['f'] as $k => $data) {
                    if (is_numeric($k)) {
                        if (is_string($data)) {
                            $k = $data;
                            $data = [];
                        }
                        if (is_object($data)) {
                            if (!$data->validateRequest($out_data, $this->m_errors)) {
                            }
                            continue;
                        }
                    }

                    $v = igk_getv($request, $k);
                    $storage = new FormFieldObjStorage($data);
                    $storage->name = $k;
                    if ($storage->required && ($v === null)) {                        
                        if (!key_exists($k, $request)){ //  && !isset($request[$k])) {
                            $this->m_errors[$k] = "missing value.";
                            continue;
                        }
                    }
                    if(is_null($v) && $this->skipNullValue){
                        continue;
                    }

                    //+ object field validation 
                    //+ in order to disable recursion just use props data while
                    if (is_object($v) || is_array($v)) {
                        $validator = igk_getv($data,'validator');
                        $default = igk_getv($data,'default');
                        $error = [];
                        if ($validator && ($r = $validator->validate($v, $default, $error))){
                            $out_data[$k] = $r;
                            continue;
                        }
                        if ($error){
                            $this->m_errors[$k] = $error;
                            continue;
                        }
                        if ($validator instanceof FormFieldValidatorContainerBase){
                            $v_nobj = [];
                            $v_rfd[] = ['o' => & $v_nobj, 'f' => $validator->getFields(), 'v'=>$v]; 
                            $out_data[$k] = & $v_nobj; 
                        }
                        continue;
                    } 
                    // if ((empty($v) || (strlen($v) == 0)) && $storage->required) {
                    //     $this->m_errors[] = __("form validation {0} is required", $k);
                    //     continue;
                    // }


                    // validate field 
                    $_v = $this->getValidator($storage->type);
                    if ($_v instanceof IFormPatternValidator) {
                        $_v->setPattern($storage->pattern);
                    }
                    $v_e = [];
                    $v_value = Activator::CreateNewInstance(FormValidationParam::class, [
                        'input' => $v,
                        'default' => $storage->default,
                        'required' => $storage->required,
                        'allowNull' => $storage->allowNull,
                        //'error'=>& $v_e,
                        'name' => $k,
                        'fieldInfo' => $storage
                    ]);
                    $v_value->error = &$v_e;
                    // $v = $_v->validate($v, $storage->default, $v_e, $storage->isRequired, $storage->allowNull);                    
                    $v = $_v->validate($v_value); // , $storage->default, $v_e, $storage->isRequired, $storage->allowNull);                    

                    if (empty($v_e)) {
                        $out_data[$k] = $v;
                    } else {
                        $this->m_errors[$k][] = $v_e;
                    }
                }
            }
            unset($out_data);
            $out_data = $root_data;
            if (count($this->m_errors) == 0) {
                if ($this->storage)
                    $result = new IGKObjStorage($out_data);
                else
                    $result = $out_data;
            }
        }

        return $result;
    }

    /**
     * 
     * @param null|string $type 
     * @return IFormValidator
     */
    function getValidator(?string $type = null)
    {
        $cl = DefaultValidator::class;
        if ($type !== null) {
            $m = StringUtility::CamelClassName($type . "_Validator");
            if (file_exists($file = __DIR__ . "/{$m}.php")) {
                require_once $file;
            }
            $m = __NAMESPACE__ . "\\" . $m;
            if (class_exists($m, false)) {
                $cl = $m;
            } else {
                if (isset($this->m_validators[$type])) {
                    return $this->m_validators[$type];
                }
            }
        }
        return new $cl();
    }
    /**
     * utility validate form fields
     * @param mixed $fields 
     * @param array $request 
     * @param string $method 
     * @return bool|array|IGKObjStorage 
     * @throws IGKException 
     */
    public static function ValidateFormFields($fields, ?array $request = null, string $method = "POST")
    {
        if ($request === null) {
            $request = $_REQUEST;
        }
        if (igk_server()->method($method)) {
            return (new self())->fields($fields)->validate($request);
        }
        return false;
    }

    /**
     * validation has error
     * @return bool 
     */
    public function hasError():bool{
        return count($this->m_errors)>0;
    }
}
