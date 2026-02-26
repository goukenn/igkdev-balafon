<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvControllerCacheRoute.php
// @date: 20220906 11:47:54
namespace IGK\System\Caches;
use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\SystemUriActionController;
/**
* 
* @package IGK\System\Caches
*/
class EnvControllerCacheRoute implements IControllerCacheChain{
    public

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILE = SystemUriActionController::CACHE_FILE;

    /**
    * auto generate doc.
    */
    public static function GetCacheFile(){
        return igk_io_cachedir()."/".self::FILE;
    }

    /**
    * auto generate doc.
    * @param BaseController $controller
    * @return void
    */
    public function update(BaseController $controller):void{
    }

    /**
    * auto generate doc.
    * @return void
    */
    public function complete():void
    {
    }
}