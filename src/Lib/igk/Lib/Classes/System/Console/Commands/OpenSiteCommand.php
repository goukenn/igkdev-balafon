<?php
// @author: C.A.D. BONDJE DOUE
// @file: OpenSiteCommand.php
// @date: 20251027 13:58:43
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;

/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class OpenSiteCommand extends AppExecCommand{
	var $command='--open';
	var $desc='desc';
	var $options=[];
	var $category = '';
	var $usage = '';
	public function exec($command, ?string $location=null) {
		$agent = 'firefox';
		$page = 'https://local.com:7300';
		`open -a {$agent} {$page}/{$location}`;
	}
}