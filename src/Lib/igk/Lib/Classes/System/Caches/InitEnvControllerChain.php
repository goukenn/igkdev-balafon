<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitEnvControllerChain.php
// @date: 20220906 11:18:32
namespace IGK\System\Caches;
use IGK\Controllers\ApplicationModuleController; 
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKEvents;
use IGKException;
use ReflectionException;

/**
* auto generate doc.
* @package IGK\System\Caches
*/
class InitEnvControllerChain{
    /**
    * Property: chain.
    * @var mixed
    */
    private $m_chain = [];
    /**
    * Adds.
    * @param mixed $chain
    * @return InitEnvControllerChain
    */
    public function add($chain): InitEnvControllerChain{
        array_push($this->m_chain, $chain);
        return $this;
    }
    /**
    * Updates.
    * @param mixed $ctrl
    */
    public function update($ctrl){
        foreach($this->m_chain as $k){
            $k->update($ctrl);
        }
    }
    /**
    * Complete.
    */
    public function complete(){
        foreach($this->m_chain as $k){
            $k->complete();
        }
    }
    /**
     * init controller definition 
     * @param mixed $tab 
     * @param mixed $manager 
     * @param mixed $loader 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function load(array $tab, $manager, $loader){
        $no_def = [
            ApplicationModuleController::class
        ];
        $args = 
            [
                'ctrl'=>null,
                'source'=>$this
            ];
        foreach ($tab as $cl) {
            if (
            !in_array($cl, $no_def))
             {
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
                $args['ctrl'] = $o;
                igk_hook(IGKEvents::HOOK_CONTROLER_LOADED, $args);
            }
        }
        $this->complete();
    }
}