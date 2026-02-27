<?php
// @author: C.A.D. BONDJE DOUE
// @file: OpenSiteCommand.php
// @date: 20251027 13:58:43
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class OpenSiteCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--open';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='desc';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'dev';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'location';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $location
    */
    public function exec($command, ?string $location=null) {
		$agent = 'firefox';
		if ($page = getenv('IGK_WEB_URL')){
			`open -a {$agent} {$page}/{$location}`;
		}
	}
}