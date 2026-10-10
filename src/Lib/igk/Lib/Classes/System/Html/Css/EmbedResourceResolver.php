<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EmbedResourceResolver.php
// @date: 20230821 19:31:10
// @desc: resource inline
namespace IGK\System\Html\Css;
use IGK\Css\ICssResourceResolver;
use IGK\Resources\R;
use IGK\System\IO\Path;

/**
* Embed resource resolver.
* @package IGK\System\Html\Css
*/
class EmbedResourceResolver implements ICssResourceResolver{
    /**
     * set the controller to link
     * @var mixed
     */
    var $controller; 

  

    /**
     * Resolve a file path to a base64-encoded inline data URI.
     * @param string $path The absolute file path to the resource.
     * @return string|null
     */
    public function resolve(string $path): ?string {
        if (empty($path) || !igk_io_file_exists($path, true)){
            return null;
        }

        switch(strtolower(igk_io_path_ext($path))){
            case 'svg':
                return 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($path));
        }
        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
    }
    /**
     * 
     * @param string $value 
     * @return null|string 
     */
    public function resolveResourcePath(string $value): ?string{
        $ctrl = $this->controller;
        $tf = null;
        if($ctrl && igk_str_startwith($value, './')){
            $rf = Path::CombineAndFlattenPath($ctrl->getAssetsDir(), $value);
            if (igk_io_file_exists($rf, true)){
                return $rf;
            }
        }
        if ($tf = R::GetImgResUri($value, $path)){
            $tf = $path;
        } else if (file_exists($value)){
            $tf = $value;
        }
        return $tf;
    }
    /**
     * Resolve a CSS color key value; returns null as embedding is not applicable.
     * @param string $keyValue The color key to resolve.
     * @return string|null
     */
    public function resolveColor(string $keyValue): ?string {
        return null;
     }
}