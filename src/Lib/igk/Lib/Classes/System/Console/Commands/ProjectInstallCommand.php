<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInstallCommand.php
// @date: 20230302 07:14:49
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ProjectInstallCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--project:install';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "install a site projects";
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = "project_name [option]";
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    */
    public function exec($command, ?string $controller = null) {
        /// TODO: get project packages 
        is_null($controller) && igk_die("controller required.");
        // + | go no network and get project manifest
        $manifest_url = igk_configs()->get('packagist://project', IGK_WEB_SITE.'/packagist/'.$command);
     }
}