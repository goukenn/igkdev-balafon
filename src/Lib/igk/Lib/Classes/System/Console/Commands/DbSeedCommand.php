<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSeedCommand.php
// @date: 20230509 08:40:53
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
/**
* 
* @package IGK\System\Console\Commands
*/
class DbSeedCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--db:seed';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='seed controller\'s database';

    /**
    * Property: category.
    * @var mixed
    */
    var $category='db';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage  = "[controller] [class] [options]";
	/* var $options=[]; */

    /**
    * Exec.
    * @param mixed $command
    * @param null|mixed $ctrl
    * @param null|mixed $class
    */
    public function exec($command, $ctrl = null, $class = null) {
		DbCommandHelper::Init($command);
		if (is_null($class)){
			if (is_null($ctrl)){
				$ctrl = self::ResolveController($command, null, false);
			}else{
				$bctrl = self::GetController($ctrl, false);
				if (!$bctrl){
					$class = $ctrl;
					$ctrl = self::ResolveController($command, null, false);
				}
			}
		}
		// Transform to namespace class
		DbCommandHelper::Seed($ctrl, $class); 
		return 0; 
	}
}