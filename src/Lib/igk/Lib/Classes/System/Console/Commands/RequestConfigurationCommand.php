<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestConfigurationCommand.php
// @date: 20260509 17:37:47
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Http\ConfigurationPageHandler;

/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class RequestConfigurationCommand extends AppExecCommand{
	var $command='--request:config';
	var $desc='request configuration showpanel';
	/* var $options=[]; */
	/* var $category = ''; */
	var $usage = '[option]';
	public function exec($command, ?string $uri=null) { 
		// 
		$conf = new ConfigurationPageHandler(null, null, null);
		$uri = $uri ?? '/';
		$conf->handle_route($uri);


	}
}