<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssetsActionTrait.php
// @date: 20221212 11:31:26
namespace IGK\Actions\Traits;

use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait AssetsActionTrait{
    public function assets($f=null){
		$f = implode("/", func_get_args());
		$dir = $this->getController()->getAssetsDir();
    
		if (!$f || !file_exists($f = $dir."/".$f)){
			igk_set_header(RequestResponseCode::NotFound); 
			igk_exit();
		}
		igk_header_content_file($f);
        igk_header_cache_output(3600 * 24 * 365);
		$size = filesize($f); 
		header("Content-Length:". $size);
		echo file_get_contents($f); 
		igk_exit();
	}
    // public function assets(){
    //     $f = implode("/", array_merge([$this->getController()->getDataDir(), __FUNCTION__],
    //     func_get_args()));       
    //     // igk_wln_e("domain:::", $f, file_exists($f));
    //     if (file_exists($f)){
    //         $mime = igk_getv(igk_header_mime(), igk_io_path_ext($f), "text/plain");  
    //         // igk_wln_e("data ", igk_io_path_ext($f), $mime)  ;        
    //         $response = new WebFileResponse($f, $mime);
    //         $response->zip = false;
    //         $response->cache_output(igk_configs()->assets_cache_output());
    //         $response->output(); 
    //     } 
    //     throw new ResourceNotFoundException('resource not found', '');
    //     // throw new PageNotFoundException();    //
    // }
}