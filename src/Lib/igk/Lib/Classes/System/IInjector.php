<?php
// @author: C.A.D. BONDJE DOUE
// @file: IInjector.php
// @date: 20230921 12:06:12
namespace IGK\System;
/**
* 
* @package IGK\System
*/

/**
* auto generate doc.
* @package IGK\System
*/
interface IInjector{

    /**
    * auto generate doc.
    * @param mixed $value
    * @return mixed
    */
    function resolve($value, ?string $type=null);
}