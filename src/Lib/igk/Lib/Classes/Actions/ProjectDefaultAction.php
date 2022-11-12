<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ProjectDefaultAction.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Actions;

use IGK\System\Actions\Traits\ProjectAssetHandlerTrait;
use IGK\System\Http\PageNotFoundException;
use IGK\System\Http\RequestException;
use IGK\System\Http\ResponseHtmlRenderer;
use IGK\System\Http\WebFileResponse;
use IGK\System\Http\WebResponse;
use IGK\System\IO\MimeType;

abstract class ProjectDefaultAction extends \IGKActionBase{
    use ProjectAssetHandlerTrait;
    /**
     * manifest cache output
     * @var mixed
     */
    protected $manifest_cache;

    public function logout(){
        $this->ctrl->logout(1);
    }
   
    protected function manifest_json(){
        $dir = $this->ctrl->getDeclaredDir();
        if (file_exists($fname = ($dir."/manifest.json"))){ 
            $response = new WebResponse($str=file_get_contents($fname)); 
            $response->headers = [
                "Content-Type:".MimeType::FromExtension("json"),
                "Content-Length:".strlen($str)
            ]; 
            if ($this->manifest_cache>0){
                $response->cache_output($this->manifest_cache);
            }
            igk_do_response($response);
        } else {  
            throw new RequestException(404);
       }
    }
    public function __call($name, $args){
        if (preg_match("/^manifest(\.json)?$/", $name)){
            return $this->manifest_json(...$args);
        } 
        return parent::__call($name, $args);
    }
}