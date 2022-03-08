<?php

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
    public static function _composer_pan($n, $ctrl){
        $_json = igk_io_packagesdir()."/composer.json";
        $_available = file_exists($_json); 
        $n->h2()->Content = "Composer";
        $n->hr();
        $n->div()->Content = "Is Available : ". $_available; 

        if ($_available){

            $n->div()->Content = __("Version : {0}", $ctrl->getComposerVersion());

        }

        $n->actionbar(function($a)use($ctrl){
            $a->abtn($ctrl->getUri("init"))->Content = "init";
        });
    } 
    /**
     * initialize composer
     * @return void 
     */
    public function init(){
        $cmd = igk_io_packagesdir()."/composer.phar";
        $cwd = getcwd();
        $c_cmd = implode(" ", [
            "composer",
            "--version"
        ]);
        chdir(dirname($cmd));
        $c = exec( $c_cmd );

        igk_text("c - ", $c_cmd,  $c);

    }
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