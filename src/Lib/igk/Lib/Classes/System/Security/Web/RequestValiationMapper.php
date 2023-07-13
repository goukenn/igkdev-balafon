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
 

    protected function getDefaultContentValidator(){
        return $this->defaultContentValidator ??  new HtmlContentValidator;
    }
    // public function __construct($map, ?array $defaultValues=null , ?array $not_required=null, ?array $resolv_keys=null)
    // {
    //     $this->mapper = $map;
    //     $this->m_defaultValues = $defaultValues;
    //     $this->m_not_required = $not_required;
    //     $this->m_resolvKeys = $resolv_keys;
    // }
   
    public function validate($data)
    {
        $this->validating = 1;
        $this->_resolv_data = [];
        
        $v_resolv_key = $this->m_resolvKeys;
        $v_mapper  = $this->mapper;
        $rf = &$this->_resolv_data;
        $keys = array_keys($v_mapper);
        $values = $data; // array_values($data);
        $defaultMapper = $this->getDefaultContentValidator(); 
        $v_not_require = null;
        $g = $this->m_not_required;
        if ($g && (count($g)==1)){
            if (is_callable($g[0])){
                $v_not_require= $g[0]; 
                $g = null;
            }
            else if ($g[0]=="*"){
                $v_not_require = function(){return true;};
                $g = null;
            }
        } 
        else{
            $v_not_require = function($i){
                return !(!is_null($this->m_not_required) || !($this->m_not_required && key_exists($i, $this->m_not_required)));
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
            $missing = is_object($values) ? !property_exists($values, $q) : is_array($values) && !key_exists($q, $values);
            if ($v_resolv_key && isset($v_resolv_key[$q])){
                $q = $v_resolv_key[$q];
                $num = false;
            }
            
            $required = !$v_not_require($q); 
            
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
           
            $v = $fc($v, $skey, $error, $missing, $required);
            if ($error) {
                $this->m_errors[$skey] = $error;
            }
            $rf[$q] = $v;
        }
        $this->validating = 0;
        return $this;
    }
   
}