<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IService.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK;

use IGK\Services\IAppService;

/**
 * service implement init method
 * @package IGK
 */
interface IService extends IAppService{
    function init():bool;
}