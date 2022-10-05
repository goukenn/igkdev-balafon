<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvControllerCacheRoute.php
// @date: 20220906 11:47:54
namespace IGK\System\Caches;

use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\SystemUriActionController;

///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
class EnvControllerCacheRoute implements IControllerCacheChain{
    public const FILE = SystemUriActionController::CACHE_FILE;
    public static function GetCacheFile(){
        return igk_io_cachedir()."/".self::FILE;
    }
    public function update(BaseController $controller):void{

    }
    public function complete():void
    {
        
    }
}