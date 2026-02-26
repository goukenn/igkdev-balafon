<?php
// @author: C.A.D. BONDJE DOUE
// @file: IArrayKeyExists.php
// @date: 20231016 01:11:49
namespace IGK\System;
/**
* 
* @package IGK\System\Array
*/
interface IArrayKeyExists{

    /**
    * auto generate doc.
    * @param string $name
    * @return bool
    */
    function keyExists(string $name):bool;
}