<?php

namespace IGK\System\Configuration\Controllers;

use function igk_resources_gets as __; 

class ConfigureLayout{
    public function __construct($controller)
    {
        $this->controller = $controller;
    }
    public function configBar($n){
        $n->setClass("+dispflex")->setStyle("font-size: 8pt;");
        $n->h1()->setStyle("padding: 5px 20px")->Content = __("Configuration");
    }
}