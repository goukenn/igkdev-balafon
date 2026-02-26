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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--open';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='desc';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'dev';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'location';

    /**
    * auto generate doc.
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