<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICacheChain.php
// @date: 20220906 11:48:56
namespace IGK\System\Caches;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
interface IControllerCacheChain{
    /**
     * update the controller chain data
     * @param BaseController $controller 
     * @return void 
     */
    function update(BaseController $controller):void;
    /**
     * called to store the cache
     * @return mixed 
     */
    function complete():void;
}