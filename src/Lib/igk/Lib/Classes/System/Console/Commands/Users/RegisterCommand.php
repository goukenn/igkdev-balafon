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
* auto generate doc.
* @package IGK\System\Console\Commands\Users
*/
class RegisterCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--users:register';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='register command user';
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		'--activate'=>'flag: active the user'
	];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = self::USER_CAT;
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'login [firstname] [lastname] [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $login
    * @param null|string $firtname
    * @param null|string $lastname
    */
    public function exec($command, ?string $login = null , ?string $firtname=null, ?string $lastname=null) { 
		!$login && igk_die('login is an empty string');
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