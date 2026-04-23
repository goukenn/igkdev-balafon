<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigureLayout.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration\Controllers;
use function igk_resources_gets as __;

/**
* Configure layout.
* @package IGK\System\Configuration\Controllers
*/
class ConfigureLayout{
    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;
    /**
    * .ctr
    * @param mixed $controller
    */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }
    /**
     * configuration bar title 
     * @param mixed $n 
     * @return void 
     */
    public function configBar($n){
        $n->setClass("+dispflex +flex-space-between flex-alignc");
        $n->div()->setClass("logo svg-fit posab")->Content = igk_svg_use("balafon_logo");
        $n->h1()->setClass('flex-grow-1')->Content = __("BALAFON &gt; CPANEL");
        $n->div()->setClass('flex-grow-1')->setStyle("margin-right:10px")->Content= __("Welcome, {0}", igk_configs()->admin_login);
    }
    /**
    * Returns string representation.
    */
    public function __toString(){
        return 'layout';
    }
}