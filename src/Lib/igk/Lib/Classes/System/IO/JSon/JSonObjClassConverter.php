<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonObjClassConverter.php
// @date: 20250128 13:22:20
namespace IGK\System\IO\JSon;
use IGK\Helper\Activator;
use IGKException;
/**
* 
* @package IGK\System\IO\JSon
* @author C.A.D. BONDJE DOUE
*/
class JSonObjClassConverter extends JSonBindToConverterBase{
    private $type;
    public function __construct($type){
        $this->type = $type;
    }
    /**
     * binding object class 
     * @param mixed $value 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     */
    public function convert($value, $options=null) {  
        if (is_null($value)){
            return null;
        }      
        $v_t = $this->type;
        $is_null = is_null($options);
        $g = Activator::CreateNewInstance($v_t, !$is_null?$value:[]);
        if (!$is_null){
            $options->handle = true;
            $options->unshiftData($g, $value);
        }
        return $g;
    }
}