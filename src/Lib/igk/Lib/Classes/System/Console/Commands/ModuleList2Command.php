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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--module:list";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "module";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "List installed module(s)";

    /**
    * auto generate doc.
    * @param mixed $command
    */
    public function exec($command) { 
        return (new ModuleCommand())->exec($command, "ls");
    } 
}