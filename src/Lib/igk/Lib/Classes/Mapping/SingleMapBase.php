<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayModelMap.php
// @date: 20220712 10:19:04
// @desc: 
namespace IGK\Mapping;

/**
* Single map base.
* @package IGK\Mapping
*/
abstract class SingleMapBase{

    /**
    * Map.
    * @param mixed $data
    */
    abstract function map($data);
}