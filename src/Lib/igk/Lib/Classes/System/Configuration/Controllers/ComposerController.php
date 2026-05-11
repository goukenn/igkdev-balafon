<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ComposerController.php
// @date: 20220803 13:48:57
// @desc: 
namespace  IGK\System\Configuration\Controllers;
use IGK\Controllers\BaseController;
use function igk_resources_gets as __;

/**
* class used to register global user in system
*/
class ComposerController extends ConfigControllerBase {
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return IGK_COMPOSER_CTRL;
    }
    /**
    * Returns Config Page.
    */
    public function getConfigPage(){
        return "composer";
    }
    /**
    * Returns Config Group.
    */
    public function getConfigGroup(){
        return "administration";
    }
    /**
     * enable or not the use of this configuration
     * @return true 
     */
    public function getIsConfigPageAvailable()
    {
        return true;
    }
    /**
    * Returns Is Visible.
    * @return bool
    */
    public function getIsVisible():bool
    {
        return true;
    }
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController
	{
        $t = $this->getTargetNode();
		$t->clearChilds();
        $t->panelbox()->host([$this, "_composer_pan"], $this);
        return $this;
    }
    /**
    * Composer pan.
    * @param mixed $n
    * @param mixed $ctrl
    */
    protected function _composer_pan($n, $ctrl){
        $_json = igk_io_packagesdir()."/composer.json";
        $_available = file_exists($_json); 
        $n->h2()->Content = "Composer";
        $n->hr();
        $n->panelbox()->div()->Content = __("Is Available : {0}", __($_available ? "True" : "False")); 
        if ($_available){
            $_id = "resutlnode";
            $n->panelbox()->div()->Content = __("Version : {0}", $ctrl->getComposerVersion());
            $n->actionbar(function($a)use($ctrl, $_id){
            });
            $n->div()->setAttribute("id", $_id);
        }
    } 
    /**
     * initialize composer
     * @return void 
     */
    private function getComposerVersion(){
        return $this->_exec_command("--version");
    }
    /**
    * auto generate doc.
    * @param mixed $command
    * @return mixed
    */
    private function _exec_command($command){
        $cmd = igk_io_packagesdir()."/composer.phar";
        if (!file_exists($cmd)){
            return "undefine";
        }
        $cwd = getcwd();
        $c_cmd = implode(" ", [
            "./composer.phar",
            $command
        ]);
        chdir(dirname($cmd));
        $c = exec( $c_cmd );
        chdir($cwd); 
        return $c;
    }
}