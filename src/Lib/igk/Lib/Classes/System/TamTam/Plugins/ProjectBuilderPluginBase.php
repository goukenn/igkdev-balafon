<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderPluginBase.php
// @date: 20231016 08:31:47
namespace IGK\System\TamTam\Plugins;

use IGK\Controllers\BaseController;
use IGK\System\Console\BalafonCLIService;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\TamTam\Plugins
*/
abstract class ProjectBuilderPluginBase{
    /**
     * builder controller logic
     * @param BaseController $ctrl 
     * @return void 
     */
    abstract function build(BaseController $ctrl);

    /**
     * get cli service
     * @return mixed 
     * @throws IGKException 
     */
    protected function getCLIService(): ?BalafonCLIService{
        return igk_get_service('balafon', 'cli'); 
    }
    protected function genAction(BaseController $baseController){
        // generate action command
    }
}