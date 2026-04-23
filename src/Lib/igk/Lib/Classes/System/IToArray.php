<?php
// @author: C.A.D. BONDJE DOUE
// @file: IToArray.php
// @date: 20230310 23:11:48
namespace IGK\System;

/**
* define to array 
* @package IGK\System\Array
*/
interface IToArray{
    /**
     * convert to array 
     * @return null|array 
     */
    public function to_array(): ?array;
}