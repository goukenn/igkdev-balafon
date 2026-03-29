<?php
//
// @file: ModuleList2Command.php
// @author: C.A.D. BONDJE DOUE
// @desc: list installed module 
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ModuleList2Command extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--module:list";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "module";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "List installed module(s)";
    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command) { 
        return (new ModuleCommand())->exec($command, "ls");
    } 
}