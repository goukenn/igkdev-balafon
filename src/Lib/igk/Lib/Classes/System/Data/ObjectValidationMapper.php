<?php
// @author: C.A.D. BONDJE DOUE
// @file: ObjectValidationMapper.php
// @date: 20230309 22:09:30
namespace IGK\System\Data;
use IGK\Helper\MapHelper;
use IGK\System\Security\Web\MapContentValidatorBase;
use IGK\System\Security\Web\ObjectContentValidator;

/**
* 
* @package IGK\System\Data
*/
/**
* auto generate doc.
* @package IGK\System\Data
*/
class ObjectValidationMapper{
    /**
    * Property: resolv data.
    * @var mixed
    */
    protected $_resolv_data;
    /**
    * Property: errors.
    * @var mixed
    */
    protected $m_errors;
    /**
    * Property: not required.
    * @var mixed
    */
    protected $m_not_required;
    /**
    * Property: default values.
    * @var mixed
    */
    protected $m_defaultValues;
    /**
    * Property: resolv keys.
    * @var mixed
    */
    protected $m_resolvKeys;
    /**
    * Map of mapper.
    * @var mixed
    */
    var $mapper;
    /**
    * Property: validating.
    * @var mixed
    */
    var $validating = 0;
    /**
    * Property: default content validator.
    * @var mixed
    */
    var $defaultContentValidator;
    /**
    * Returns Default Content Validator.
    */
    protected function getDefaultContentValidator(){
        return $this->defaultContentValidator ??  new ObjectContentValidator;
    }
    /**
    * .ctr
    * @param mixed $map
    * @param null|array $defaultValues
    * @param null|array $not_required
    * @param null|array $resolv_keys
    */
    public function __construct($map, ?array $defaultValues=null , ?array $not_required=null, ?array $resolv_keys=null)
    {
        $this->mapper = $map;
        $this->m_defaultValues = $defaultValues;
        $this->m_not_required = $not_required;
        $this->m_resolvKeys = $resolv_keys;
    }
    /**
    * auto generate doc.
    * @return bool
    */
    public function isValidate():bool
    {
        return empty($this->m_errors) && !$this->validating;
    }
    /**
    * Validates.
    * @param mixed $data
    */
    public function validate($data)
    {
        $this->validating = 1;
        $this->_resolv_data = [];
        $v_resolv_key = $this->m_resolvKeys;
        $v_mapper  = $this->mapper;
        $rf = &$this->_resolv_data;
        $keys = array_keys($v_mapper);
        $values = $data; 
        $defaultMapper = $this->getDefaultContentValidator(); 
        $v_notquire = null;
        $g = $this->m_not_required;
        if ($g && (count($g)==1)){
            if (is_callable($g[0])){
                $v_notquire= $g[0]; 
                $g = null;
            }
            else if ($g[0]=="*"){
                $v_notquire = function(){return true;};
                $g = null;
            } else {
                $v_notquire = function($tg)use($g){
                    return in_array($tg, $g);
                };
            }
        } 
        else{
            $v_notquire = function($i){
                return ($this->m_not_required && (key_exists($i, $this->m_not_required) || in_array($i,$this->m_not_required) ));
            };
        }
        while (count($keys) > 0) {
            $error = null;
            $num = false;
            $q = array_shift($keys);
            if (is_numeric($q)) {
                $q = $v_mapper[$q];
                $num = true;
            }
            $skey = $q;
            $v = igk_getv($values, $q); 
            $missing = is_object($values) ? !property_exists($values, $q) : !key_exists($q, $values);
            if ($v_resolv_key && isset($v_resolv_key[$q])){
                $q = $v_resolv_key[$q];
                $num = false;
            }
            $required = !$v_notquire ($q); 
            if (!(!$num && is_callable($fc = $v_mapper[$q]))) {                
                $fc = $defaultMapper;
            }
            if ($fc instanceof MapContentValidatorBase){
                if ($fc->canUpdateSetting()){
                    $fc->updateSetting(
                        igk_getv($this->m_defaultValues, $q),
                        $g? igk_getv($g, $q): null,
                        $fc->getAllowNullValue()
                    );
                }
            }
            $v = $fc($v, $skey, $error, $missing, $required);
            if ($error) {
                $this->m_errors[$skey] = $error;
            }
            $rf[$q] = $v;
        }
        $this->validating = 0;
        return $this;
    }
    /**
    * get map
    * @param mixed $outMap
    * @return mixed
    */
    public function map($outMap = null)
    {
        if ($this->isValidate()) {
            if ($outMap) {
                return MapHelper::MapDataToObject($outMap, $this->_resolv_data);
            }
            return $this->_resolv_data;
        }
        return [
            '__validatation_error__' => $this->m_errors
        ];
    }
}