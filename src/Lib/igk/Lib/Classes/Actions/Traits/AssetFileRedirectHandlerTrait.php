<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssetFileRedirectHandlerTrait.php
// @date: 20230201 12:39:33
namespace IGK\Actions\Traits;

use IGK\System\Exceptions\ResourceNotFoundException;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait AssetFileRedirectHandlerTrait{
    public function assets(){
        // missing assets request 
        $ctrl = $this->getController();
        $ctrl->resolveAssets(["/"]);
        $fc = implode("/", func_get_args()); 
        if ($dir = $ctrl::asset($fc)){
            $dir = '/'.igk_str_rm_start($dir, "../"); 
            igk_navto($dir);
        } 
        throw new ResourceNotFoundException("missing asset : ".$fc, $fc);        
    }
}