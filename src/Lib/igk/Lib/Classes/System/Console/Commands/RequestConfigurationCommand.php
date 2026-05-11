<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestConfigurationCommand.php
// @date: 20260509 17:37:47
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Http\ConfigurationPageHandler;
/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class RequestConfigurationCommand extends AppExecCommand{
	var $command='--request:config';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $desc='request configuration showpanel';
	/* var $options=[]; */
	/* var $category = ''; */
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $usage = '[option]';
    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $uri
    * @return void
    */
    public function exec($command, ?string $uri=null) { 
		// 
		$conf = new ConfigurationPageHandler(null, null, null);
		$uri = $uri ?? '/';
		$conf->handle_route($uri);


	}
}