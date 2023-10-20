<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelMappingData.php
// @date: 20231015 15:33:34
namespace IGK\Database\Mapping\Traits;

use IGK\Models\ModelBase;
use IGKSysUtil as sysutil;
///<summary></summary>
/**
* 
* @package IGK\Database\Mapping
*/
trait ModelMappingDataTrait{
    protected function getModelMappingData(ModelBase $data, $mapping){
        $info = $data->getTableInfo();
        $tn = $info ? sysutil::GetModelTypeNameFromInfo($info): null; 
        if ($mapping && $tn){ 
            $map_data = $mapping($tn); 
            if ($map_data){
                $b = $data->map($map_data);
                return $b;
            }
        } 
        return $data->to_array();
    }
}