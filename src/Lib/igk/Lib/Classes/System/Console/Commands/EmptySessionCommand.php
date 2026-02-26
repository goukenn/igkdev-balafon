<?php
// @author: C.A.D. BONDJE DOUE
// @file: EmptySessionCommand.php
// @date: 20250422 14:20:48
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Library\session;
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class EmptySessionCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--empty-session';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='reset sess to force it reloads'; 
	/* var $options=[]; */
	/* var $category = ''; */
	/* var $usage = ''; */

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $sessionid
    */
    public function exec($command, ?string $sessionid =null) { 
		$sessionid || igk_die('required session id');
		if (file_exists( $file = session::SessionPath($sessionid))){
			igk_io_w2file($file, serialize(['igk'=>'']));
			Logger::success('done');
		}
	}
}