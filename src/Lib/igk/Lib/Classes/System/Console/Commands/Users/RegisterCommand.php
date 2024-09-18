<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterCommand.php
// @date: 20230703 13:28:58
namespace IGK\System\Console\Commands\Users;

use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class RegisterCommand extends AppExecCommand{
	var $command='--users:register';
	var $desc='register command user'; 
	/* var $options=[]; */

	var $category = self::USER_CAT;
	var $usage = 'login [firstname] [lastname] [options]';
	public function exec($command, string $login = null , ?string $firtname=null, ?string $lastname=null) { 
		Logger::print(Users::Register(['clLogin'=>$login, 'clFirstName'=>$firtname, 'clLastName'=>$lastname]));
	}
}