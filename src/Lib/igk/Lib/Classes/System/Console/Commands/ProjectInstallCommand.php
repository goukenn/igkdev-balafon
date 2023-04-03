<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInstallCommand.php
// @date: 20230302 07:14:49
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class ProjectInstallCommand extends AppExecCommand{
    var $command = '--projet:install';
    public function exec($command, ?string $controller = null) {
        is_null($controller) && igk_die("controller required.");
     }

}