<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectLinkCommand.php
// @date: 20240805 20:20:17
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGKException;
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ProjectLinkCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--project:link';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='link project to folder'; 
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category="project";

    /**
    * auto generate doc.
    * @param mixed $command
    */
    public function exec($command) { 
		throw new IGKException('not implemented');
	}
}