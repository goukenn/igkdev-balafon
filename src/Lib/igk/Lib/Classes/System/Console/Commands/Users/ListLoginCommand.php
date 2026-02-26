<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListLoginCommand.php
// @date: 20230703 09:47:08
namespace IGK\System\Console\Commands\Users;
use IGK\Controllers\SysDbController;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class ListLoginCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--users:list';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='list system\'s user';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = self::USER_CAT;

    /**
    * auto generate doc.
    * @param mixed $command
    */
    public function exec($command) {  
		$m = Users::select_all(null, ['OrderBy'=>['clLogin|ASC']]);
		$c = count($m);
		array_map(function($i)use(& $c){
			Logger::print($i->clLogin);		
			return $i->clLogin;}, $m);
		Logger::info("Count: ".$c);
		return 0;
	}
}