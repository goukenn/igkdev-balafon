<?php
// @author: C.A.D. BONDJE DOUE
// @file: IInjector.php
// @date: 20230921 12:06:12
namespace IGK\System;


///<summary></summary>
/**
* 
* @package IGK\System
*/
interface IInjector{
    /**
     * 
     * @param mixed $value 
     * @return mixed 
     */
    function resolve($value, ?string $type=null);
}