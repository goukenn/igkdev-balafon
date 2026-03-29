<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectLinkCommand.php
// @date: 20240805 20:20:17
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGKException;
/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ProjectLinkCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--project:link';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='link project to folder'; 
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category="project";
    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command) { 
		throw new IGKException('not implemented');
	}
}