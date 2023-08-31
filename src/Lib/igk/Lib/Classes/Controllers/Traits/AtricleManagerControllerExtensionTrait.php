<?php
// @author: C.A.D. BONDJE DOUE
// @file: AtricleManagerControllerExtensionTrait.php
// @date: 20221120 09:52:24
namespace IGK\Controllers\Traits;

use IGK\Controllers\BaseController;
use IGK\Helper\ArticleContentBindingHelper;
use IGK\Helper\IO;
use IGK\System\IO\Path;
use IGKResourceUriResolver;

///<summary></summary>
/**
* 
* @package IGK\Controllers\Traits
*/
trait AtricleManagerControllerExtensionTrait{
    /**
     * get article content
     * @param BaseController $controller 
     * @param mixed $name 
     * @return null|string 
     */
    public static function article(BaseController $controller, $name, ?array $args= null) : ?string{
        if (file_exists($file = $controller->getArticle($name))){
            $src = file_get_contents($file);
            if ($src && $args){
                $src = ArticleContentBindingHelper::BindContent($src, $args);
            }
            return $src;
        }
        return null;
    }
    /**
     * get asset lists
     * @param BaseController $ctrl 
     * @param string $path 
     * @param mixed $ext 
     * @param bool $recursive 
     * @return null|array 
     */
    public static function assets_list(BaseController $ctrl, 
        string $path, 
        string $ext, bool $recursive = false, ?callable $callback=null){
        $dir = Path::Combine($ctrl->getDataDir(), IGK_RES_FOLDER); 
        $dir .= '/'.ltrim($path,'/'); 
        if (!is_dir($dir)){
            return null;
        }
        $exclude_dir = null;
        $rt = IO::GetFiles($dir, $ext, $recursive, $exclude_dir, $callback);
        return $rt;
    }
    public static function assets_list_uri(BaseController $ctrl, string $path, string $ext, bool $recursive = false){
        $g = [];
        self::assets_list($ctrl, $path, $ext, $recursive, function($f)use($ctrl, & $g){             
            $rf = IGKResourceUriResolver::getInstance()->resolve($f);
            $g[] = igk_io_append_query($rf, ['v'=>IGK_VERSION]);
            return false;
        });  
        rsort($g); 
        return $g;
    }
}