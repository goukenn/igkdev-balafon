<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitEnvControllerChain.php
// @date: 20220906 11:18:32
namespace IGK\System\Caches;

use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\System\Controllers\ApplicationModules;

///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
class InitEnvControllerChain{
    private $m_chain = [];
    public function add($chain){
        array_push($this->m_chain, $chain);
        return $this;
    }
    public function update($ctrl){
        foreach($this->m_chain as $k){
            $k->update($ctrl);
        }
    }
    public function complete(){
        foreach($this->m_chain as $k){
            $k->complete();
        }
    }
    public function load($tab, $manager, $loader){
        $no_def = [
            ApplicationModuleController::class
        ];
        foreach ($tab as $cl) {
            if (is_subclass_of($cl, BaseController::class) &&
            !in_array($cl, $no_def))
             {
                // register controller
                $g = igk_sys_reflect_class($cl);
                if ($g->isAbstract() || !$g->getConstructor()->isPublic()) {
                    continue;
                }
                $o = new $cl();
                $manager->register($o);
                $rfile = $g->getFileName();
                $loader->registerClass(
                    $rfile,
                    $cl,
                    ""
                );
                $this->update($o);
            }
        }
        $this->complete();
    }
}