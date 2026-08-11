<?php
// @author: C.A.D. BONDJE DOUE
// @file: IControllerClassResolver.php
// @date: 20260810 13:09:08
namespace IGK\System\Controllers;


/**
* 
* @package IGK\System\Controllers
* @author C.A.D. BONDJE DOUE
*/
interface IControllerClassResolver{
    /**
     * 
     * @param string $path 
     * @return string 
     */
    function resolveClass(string $path): string;
}