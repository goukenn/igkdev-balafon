<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataMappingBase.php
// @date: 20260731 12:29:08
namespace IGK\System\Data;


/**
* 
* @package IGK\System\Data
* @author C.A.D. BONDJE DOUE
*/
abstract class DataMappingBase{
    /**
     * map data 
     * @param mixed $data
     */
    public abstract function Map($data);
}