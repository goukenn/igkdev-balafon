<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInstallCommand.php
// @date: 20230302 07:14:49
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
/**
* 
* @package IGK\System\Console\Commands
*/
class ProjectInstallCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = '--projet:install';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "install a site projects";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = "project_name [option]";

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $controller
    */
    public function exec($command, ?string $controller = null) {
        is_null($controller) && igk_die("controller required.");
     }
}