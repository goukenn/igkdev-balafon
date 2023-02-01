<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestValiationMapper.php
// @date: 20230125 13:46:04
namespace IGK\System\Security\Web;

use IGK\Helper\MapHelper;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class RequestValiationMapper
{
    var $mapper;
    var $validating = 0;
    private $_resolv_data;
    private $m_errors;
    public function __construct($map)
    {
        $this->mapper = $map;
    }
    public function isValidate()
    {
        return empty($this->m_errors) && !$this->validating;
    }
    public function validate($data)
    {
        $this->validating = 1;
        $this->_resolv_data = [];
        
        $mapper  = $this->mapper;
        $rf = &$this->_resolv_data;
        $keys = array_keys($mapper);
        $values = $data; // array_values($data);
       
        while (count($keys) > 0) {
            $error = null;
            $num = false;
            $q = array_shift($keys);
            if (is_numeric($q)) {
                $q = $mapper[$q];
                $num = true;
            }
            $v = igk_getv($values, $q); 
            
            if (!$num && is_callable($fc = $mapper[$q])) {
                $v = $fc($v, $q, $error);
            }
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