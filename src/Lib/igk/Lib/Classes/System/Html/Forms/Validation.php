<?php
namespace IGK\System\Html\Forms;

use IGK\Helper\StringUtility;
use IGKObjStorage;
use function igk_resources_gets  as __;

require_once __DIR__."/IFormValidator.php";
require_once __DIR__."/IFormPatternValidator.php";

class Validation{
    /**
     * validation list
     * @var mixed
     */
    private $m_validator;

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
    public function getErrors(){
        return $this->m_errors;
    }
    /**
     * set array of validation
     * @param array $validation 
     * @return self 
     */
    public function validator(array $validation){
        $this->m_validator = $validation;
        return $this;
    }

    /**
     * register validator
     * @param mixed $name 
     * @param null|callable|IFormValidator $callable 
     * @return void 
     */
    public function registerValidator($name, $callable){
        if ($callable === null){
            unset($this->m_validators[$name]);
        }else {
            if (is_callable($callable))
            {
                $callable = new _FormCallableValidator($callable);
            } else if (is_object($callable) && !($callable instanceof IFormValidator )){
                return;
            }
            $this->m_validators[$name] = $callable;
        }
    }

    /**
     * 
     * @param array $request 
     * @return bool|array false 
     */
    public function validate(array $request){
        // + | reset the error list
        $this->m_errors = [];
        $result = false;

        if (empty($request)){
            $this->m_errors[] = __("validation: empty request not allowed");
        } 
        if (empty($this->m_validator)){
            $this->m_errors[] = __("no validator setup");
        }
        if (count($this->m_errors) == 0 ){

                // + | stats validation propcess 
                /**
                 * @var ?IGKObjStorage $storage 
                 */
                $storage = null;
                $out_data = [];
                foreach($this->m_validator as $k => $data){
                    $v = igk_getv($request, $k);
                    $storage = new IGKObjStorage($data);
                    $storage->name = $k;
                    if (empty($v) && $storage->required){
                        $this->m_errors[] = __("form validation {0} is required", $k);
                        continue;
                    }

                    // validate field 
                    $_v = $this->getValidator($storage->type);
                    if ($_v instanceof IFormPatternValidator){
                        $_v->setPattern($storage->pattern);
                    }
                    $v = $_v->validate($v, $storage->default, $storage, $this->m_errors); 
                    $out_data[$k] = $v;
                }
                if (count($this->m_errors) == 0 ){
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
    function getValidator(?string $type = null){
        $cl = DefaultValidator::class;
        if ($type!==null){
            $m = StringUtility::CamelClassName($type."_Validator");
            if (file_exists($file = __DIR__."/{$m}.php")){
                require_once $file;
            }
            $m = __NAMESPACE__."\\".$m;
            if (class_exists($m, false)){
                $cl = $m;
            } else {
                if (isset($this->m_validators[$type])){
                    return $this->m_validators[$type];
                }
            }             
        }
        return new $cl();
    }
}