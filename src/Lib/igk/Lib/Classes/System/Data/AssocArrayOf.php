<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssocArrayOf.php
// @date: 20260731 12:39:15
namespace IGK\System\Data;

use IGK\Helper\Activator;

/**
* associate array of class object 
* @package IGK\System\Data
* @author C.A.D. BONDJE DOUE
*/

class AssocArrayOf extends DataMappingBase{
    /**
     * 
     * @var mixed
     */
    var $className;
    /**
     * 
     * @var string
     */
    var $keyProperty='id';
    /**
     * 
     * @param mixed $data 
     * @return mixed 
     */
    public function Map($data){
        $c = (array)$data;
        if (empty($c)){
            return null;
        }
        $m = [];
        foreach($data as $k=>$v){
            $d = array_merge([$this->keyProperty=>$k], (array)$v);
            $m[$k] = Activator::CreateNewInstance($this->className, $d);
        }
        return $m;
    }
}