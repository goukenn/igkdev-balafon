<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ComposerController.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Configuration\Controllers;

use function igk_resources_gets as __;

///<summary>class used to register global user in system</summary>
/**
* class used to register global user in system
*/
class ComposerController extends ConfigControllerBase {
    public function getName(){
        return IGK_COMPOSER_CTRL;
    }
    public function getConfigPage(){
        return "composer";
    }
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
    public function getIsVisible()
    {
        return true;
    }
    public function View()
	{
        $t = $this->getTargetNode();
		$t->clearChilds();
        $t->panelbox()->host([$this, "_composer_pan"], $this);
    }
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
                // $a->ajxa($ctrl->getUri("init"), "#".$_id)->setClass("igk-btn")->Content = "init";
            });
            $n->div()->setAttribute("id", $_id);
        }
    } 
    /**
     * initialize composer
     * @return void 
     */
    // public function init(){        
    //     if ($c = $this->_exec_command("init")){
    //         igk_text("return - \n", $c);
    //     }
    // }
    private function getComposerVersion(){
        return $this->_exec_command("--version");
    }
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