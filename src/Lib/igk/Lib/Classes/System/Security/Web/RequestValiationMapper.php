<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestValiationMapper.php
// @date: 20230125 13:46:04
namespace IGK\System\Security\Web;

use IGK\Helper\MapHelper;
use IGK\System\Data\ObjectValidationMapper;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class RequestValiationMapper extends ObjectValidationMapper
{
    var $mapper;
    var $validating = 0;
    var $defaultContentValidator;
    private $_resolv_data;
    private $m_errors;
    private $m_not_required;
    private $m_defaultValues;

    protected function getDefaultContentValidator(){
        return $this->defaultContentValidator ??  new HtmlContentValidator;
    }
    public function __construct($map, ?array $defaultValues=null , ?array $not_required=null)
    {
        $this->mapper = $map;
        $this->m_defaultValues = $defaultValues;
        $this->m_not_required = $not_required;
    }
    public function isValidate()
    {
        return empty($this->m_errors) && !$this->validating;
    }
    public function validate($data)
    {
        $this->validating = 1;
        $this->_resolv_data = [];
        
        $v_mapper  = $this->mapper;
        $rf = &$this->_resolv_data;
        $keys = array_keys($v_mapper);
        $values = $data; // array_values($data);
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
            }
        } 
        else{
            $v_notquire = function($i){
                return !($this->m_not_required && key_exists($i, $this->m_not_required));
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
            $required =  $v_notquire ($q); 

            $missing = is_object($values) ? !property_exists($values, $q) : !key_exists($q, $values);
            $v = igk_getv($values, $q); 
            
            if (!(!$num && is_callable($fc = $v_mapper[$q]))) {                
                $fc = $defaultMapper;
            }
            if ($fc instanceof MapContentValidatorBase){
                if ($fc->canUpdateSetting()){
                    $fc->updateSetting(
                        igk_getv($this->m_defaultValues, $q),
                        $g? igk_getv($g, $q): null,
                        false
                    );
                }
            }
            $v = $fc($v, $q, $error, $missing, $required);
            if ($error) {
                $this->m_errors[$q] = $error;
            }
            $rf[$q] = $v;
        }
        $this->validating = 0;
        return $this;
    }
    /**
     * get map
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