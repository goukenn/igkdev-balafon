<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectAssetHandlerTrait.php
// @date: 20221110 20:58:50
namespace IGK\System\Actions\Traits;

use IGK\System\Http\PageNotFoundException;
use IGK\System\Http\WebFileResponse;

///<summary></summary>
/**
* 
* @package IGK\System\Actions\Traits
*/
trait ProjectAssetHandlerTrait{
    /**
     * resolve internal asset 
     * @return mixed 
     */
    public function assets(){
        $f = implode("/", array_merge([$this->getController()->getDataDir(), __FUNCTION__],func_get_args()));       
        // igk_wln_e("domain:::");
        if (file_exists($f)){
            $mime = igk_getv(igk_header_mime(), igk_io_path_ext($f), "text/plain");  
            // igk_wln_e("data ", igk_io_path_ext($f), $mime)  ;        
            $response = new WebFileResponse($f, $mime);
            $response->zip = false;
            $response->cache_output(igk_configs()->assets_cache_output());
            $response->output(); 
        } 
        throw new PageNotFoundException();    
    }
}