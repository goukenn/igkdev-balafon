<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMakeProjectServiceProvider.php
// @date: 20241005 08:18:47
namespace IGK\System\Services;


///<summary></summary>
/**
* 
* @package IGK\System\Services
* @author C.A.D. BONDJE DOUE
*/
interface IMakeProjectServiceProvider{
    /**
     * 
     * @param array &$bind 
     * @return mixed 
     */
    function makeProjectDefinition(array & $bind);
}