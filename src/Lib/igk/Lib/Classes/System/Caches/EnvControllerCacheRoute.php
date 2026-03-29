<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvControllerCacheRoute.php
// @date: 20220906 11:47:54
namespace IGK\System\Caches;
use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\SystemUriActionController;
/**
* auto generate doc.
* @package IGK\System\Caches
*/
class EnvControllerCacheRoute implements IControllerCacheChain{
    public
    /**
    * Constant: file.
    * @var mixed
    */
    const FILE = SystemUriActionController::CACHE_FILE;
    /**
    * Returns Cache File.
    */
    public static function GetCacheFile(){
        return igk_io_cachedir()."/".self::FILE;
    }
    /**
    * Updates.
    * @param BaseController $controller
    * @return void
    */
    public function update(BaseController $controller):void{
    }
    /**
    * Complete.
    * @return void
    */
    public function complete():void
    {
    }
}