<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterCommand.php
// @date: 20230703 13:28:58
namespace IGK\System\Console\Commands\Users;
use IGK\Helper\JSon;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Colorize;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class RegisterCommand extends AppExecCommand{
	var $command='--users:register';
	var $desc='register command user'; 
	var $options=[
		'--activate'=>'flag: active the user'
	];
	var $category = self::USER_CAT;
	var $usage = 'login [firstname] [lastname] [options]';
	public function exec($command, ?string $login = null , ?string $firtname=null, ?string $lastname=null) { 
		!$login && igk_die('login is an empty string');
		// $ctrl = self::ResolveController($command);
		$activate = property_exists($command->options, '--activate');
		$r = false;
		try{
			$data = ['clLogin'=>$login, 'clFirstName'=>$firtname, 'clLastName'=>$lastname];
			if ($activate){
				$data[Users::FD_CL_STATUS] = 1;
			}
			$r = Users::Register($data);
			Logger::SetColorizer(new Colorize);
			$r && Logger::print(json_encode($r, JSON_PRETTY_PRINT));
		} catch(\Exception $ex){
			Logger::danger($ex->getMessage());
			return -1;
		}	
		Logger::success('done');
		
	}
}