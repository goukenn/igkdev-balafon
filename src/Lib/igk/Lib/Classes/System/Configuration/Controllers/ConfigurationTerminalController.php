<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationTerminalController.php
// @date: 20250419 12:19:04
namespace IGK\System\Configuration\Controllers;

/**
* terminal command interface 
* @package IGK\System\Configuration\Controllers
* @author C.A.D. BONDJE DOUE
*/
final class ConfigurationTerminalController extends ConfigControllerBase{
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return '{CFBCE372-3778-469B-7C41-D888CC220998}';
    }
    /**
    * Returns Config Page.
    */
    public function getConfigPage(){
        return "cli-terminal";
    }
    /**
    * Returns Config Group.
    */
    public function getConfigGroup(){
        return "administration";
    }
}