<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssetsActionTrait.php
// @date: 20221212 11:31:26
namespace IGK\Actions\Traits;
use IGK\System\IO\Path;

/**
* auto generate doc.
* @package IGK\Actions\Traits
*/
trait AssetsActionTrait{
    /**
    * Assets.
    * @param null|mixed $f
    */
    public function assets($f=null){
		$f = implode("/", func_get_args());
		$dir = $this->getController()->getAssetsDir();
		if (!$f || !igk_io_file_exists($f = $dir."/".$f)){
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
}