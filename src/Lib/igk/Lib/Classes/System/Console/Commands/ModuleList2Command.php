<?php
//
// @file: ModuleList2Command.php
// @author: C.A.D. BONDJE DOUE
// @desc: list installed module 
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Console\Commands
 */
class ModuleList2Command extends AppExecCommand{

    var $command = "--module:list";
    var $category = "module";
    var $desc = "List installed module(s)";

    public function exec($command) { 
        return (new ModuleCommand())->exec($command, "ls");
    } 
}