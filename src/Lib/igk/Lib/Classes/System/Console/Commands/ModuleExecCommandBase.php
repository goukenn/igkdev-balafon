<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleExecCommandBaseCommand.php
// @date: 20230308 03:11:16
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
/**
* 
* @package IGK\System\Console\Commands
*/
abstract class ModuleExecCommandBase extends AppExecCommand{

    /**
    * Property: module.
    * @var mixed
    */
    protected $module;
}