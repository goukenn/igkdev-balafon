<?php

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
        $n->h1()->setStyle("margin-left: 32px; padding: 5px 2px")->Content = __("Configuration");
        $n->div()->setStyle("margin-right:10px")->Content= __("Welcome, {0}", igk_app()->Configs->admin_login);
    }
}