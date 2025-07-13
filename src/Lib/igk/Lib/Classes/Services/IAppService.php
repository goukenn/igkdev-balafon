<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAppService.php
// @date: 20230516 10:06:01
namespace IGK\Services;

// + | --------------------------------------------------------------------
// + | sevice is a one time class instance only initialize when required
// + | add parametered with /services.php configuration 
// + |

/**
* balafon service that can be register
* @package IGK\Services
*/
interface IAppService{
    /**
     * initialize the service - with configuration 
     * @param mixed $configs 
     * @return bool 
     */
    function init($configs=null):bool;
}