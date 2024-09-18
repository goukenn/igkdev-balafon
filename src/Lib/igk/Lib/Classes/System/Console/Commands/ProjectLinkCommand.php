<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectLinkCommand.php
// @date: 20240805 20:20:17
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ProjectLinkCommand extends AppExecCommand{
	var $command='--project:link';
	var $desc='link project to folder'; 
	/* var $options=[]; */
	var $category="project";
	public function exec($command) { 
		throw new IGKException('not implemented');
	}
}