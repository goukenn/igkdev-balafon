<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListLoginCommand.php
// @date: 20230703 09:47:08
namespace IGK\System\Console\Commands\Users;

use IGK\Controllers\SysDbController;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class ListLoginCommand extends AppExecCommand{
	var $command='--users:list';
	var $desc='list system\'s user';
	/* var $options=[]; */

	var $category = self::USER_CAT;

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