<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigureLayout.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Configuration\Controllers;

use function igk_resources_gets as __; 

class ConfigureLayout{
    public function __construct($controller)
    {
        $this->controller = $controller;
    }
    public function configBar($n){
        $n->setClass("+dispflex alignc flex-space-between flex-alignc");
        $n->div()->setClass("logo svg-fit posab")->Content = igk_svg_use("balafon_logo");
        $n->h1()->Content = __("BALAFON &gt; configuration");
        $n->div()->setStyle("margin-right:10px")->Content= __("Welcome, {0}", igk_configs()->admin_login);
    }
}