<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssetFileRedirectHandlerTrait.php
// @date: 20230201 12:39:33
namespace IGK\Actions\Traits;
use IGK\System\Exceptions\ResourceNotFoundException;
use IGK\System\IO\Path;

/**
* auto generate doc.
* @package IGK\Actions\Traits
*/
trait AssetFileRedirectHandlerTrait{
    /**
    * Assets.
    */
    public function assets(){
        $ctrl = $this->getController();
        $ctrl->resolveAssets(["/"]);
        $fc = implode("/", func_get_args()); 
        if ($dir = $ctrl::asset($fc)){
            $dir = '/'.igk_str_rm_start($dir, "../"); 
            igk_navto($dir);
        } 
        $cpath = $fc;
        $tpath = Path::Combine(IGK_LIB_DIR.'/Default/assets', $cpath);
        if (file_exists($tpath)){
            copy($tpath, $out = Path::Combine(igk_io_basedir(), 'assets/'.$cpath));
            echo file_get_contents($out);
            igk_exit();
        }
        throw new ResourceNotFoundException(sprintf("Missing asset : [%s]",$fc), $fc);        
    }
}