<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDataMapper.php
// @date: 20230302 11:10:59
namespace IGK\Mapping;


///<summary>use to map key value to [new_key, value]</summary>
/**
* use to map key value to [new_key, value]
* @package IGK\Mapping
*/
interface IDataMapper{
    /**
     * call to map data to other value
     * @param string $key receive key
     * @param mixed $value value to map
     * @return null|array null if not mapped
     */
    function map(string $key, $value): ?array;
}