<?php

// @author: C.A.D. BONDJE DOUE
// @filename: EmbedResourceResolver.php
// @date: 20230821 19:31:10
// @desc: resource inline

namespace IGK\System\Html\Css;
use IGK\Css\ICssResourceResolver;

class EmbedResourceResolver implements ICssResourceResolver{

    public function resolve(string $path): ?string { 
        switch(strtolower(igk_io_path_ext($path))){
            case 'svg':
                return 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($path)); 
        }
        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
        
    }

    public function resolveColor(string $keyValue): ?string {
        return null;
     }
     
}