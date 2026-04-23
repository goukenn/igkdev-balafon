<?php
// @author: C.A.D. BONDJE DOUE
// @file: IArrayKeyExists.php
// @date: 20231016 01:11:49
namespace IGK\System;

/**
* auto generate doc.
* @package IGK\System\Array
*/
interface IArrayKeyExists{
    /**
    * Key exists.
    * @param string $name
    * @return bool
    */
    function keyExists(string $name):bool;
}