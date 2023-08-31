<?php
// @author: C.A.D. BONDJE DOUE
// @file: ResolveUriTrait.php
// @date: 20230718 12:15:08
namespace IGK\System\WinUI\Traits;

use IGK\Controllers\BaseController;
use IGK\System\IO\Path;
use IGKValidator;

///<summary></summary>
/**
* 
* @package IGK\System\WinUI\Traits
*/
trait ResolveUriTrait{
     /**
     * resolve url
     * @param mixed $uri 
     * @param mixed $ctrl 
     * @return mixed 
     */
    public function resolveUriMenu($uri, ?BaseController $ctrl = null){
        if (strpos($uri, './') === 0){
            return substr($uri,1);
        } else {
            if (!IGKValidator::IsUri($uri)){
                $uri= '/'.ltrim($uri, '/');
            } else {
                return $uri;
            }
        }
        if ($ctrl)
            return Path::FlattenPath($ctrl::ruri($uri));
        return $uri;
    }
}