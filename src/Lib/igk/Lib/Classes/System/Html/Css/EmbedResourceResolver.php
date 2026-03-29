<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EmbedResourceResolver.php
// @date: 20230821 19:31:10
// @desc: resource inline
namespace IGK\System\Html\Css;
use IGK\Css\ICssResourceResolver;
/**
* Embed resource resolver.
* @package IGK\System\Html\Css
*/
class EmbedResourceResolver implements ICssResourceResolver{
    /**
     * Resolve a file path to a base64-encoded inline data URI.
     * @param string $path The absolute file path to the resource.
     * @return string|null
     */
    public function resolve(string $path): ?string {
        switch(strtolower(igk_io_path_ext($path))){
            case 'svg':
                return 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($path));
        }
        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
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