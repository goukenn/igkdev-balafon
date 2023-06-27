<?php
// @author: C.A.D. BONDJE DOUE
// @file: ObjectDataValidator.php
// @date: 20230309 22:06:52
namespace IGK\System\Data;

use IGK\System\Data\IDataValidator;
use IGK\System\DataArgs;
use IGK\System\Security\Web\RequestValiationMapper;
use IGKException;

///<summary></summary>
/**
* default object validator
* @package IGK\System\Data
*/
class ObjectDataValidator implements IDataValidator{
    
    /**
     * just validate data
     * @param mixed $data 
     * @param array $mapper 
     * @param null|array $defaultValues 
     * @param null|array $not_required 
     * @param mixed $requestData 
     * @param null|array $error 
     * @return bool 
     * @throws IGKException 
     */
    public function validate($data, array $mapper, ?array $defaultValues=null, ?array $not_required=null, &$requestData = null,  ?array &$error = null, ?array $resolvKeys=null): bool
    { 
        $r = (new ObjectValidationMapper(
            $mapper,
            $defaultValues,
            $not_required,
            $resolvKeys
        ))->validate($data)->map();
        if (isset($r['__validatation_error__'])) {
            $error = $r['__validatation_error__'];
            return false;
        }
        // update action request data 
        $requestData = new DataArgs($r);
        return true;
    } 
    public function getDataValidatorMapper(){
        return [];
    }
}