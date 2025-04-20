<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationTerminalController.php
// @date: 20250419 12:19:04
namespace IGK\System\Configuration\Controllers;


///<summary></summary>
/**
* terminal command interface 
* @package IGK\System\Configuration\Controllers
* @author C.A.D. BONDJE DOUE
*/
final class ConfigurationTerminalController extends ConfigControllerBase{
    public function getName(){
        return '{CFBCE372-3778-469B-7C41-D888CC220998}';
    }
    public function getConfigPage(){
        return "cli-terminal";
    }
    public function getConfigGroup(){
        return "administration";
    }
}