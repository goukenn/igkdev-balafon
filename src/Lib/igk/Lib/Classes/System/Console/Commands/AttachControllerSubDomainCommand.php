<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttachControllerSubDomainCommand.php
// @date: 20260117 15:27:51
namespace IGK\System\Console\Commands;

use IGK\Models\Subdomains;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class AttachControllerSubDomainCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--subdomain:attach';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='attach controller subdomain';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'sys';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller name [entry_point] [options...]';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $name
    * @param null|string $entry_point
    */
    public function exec($command, ?string $controller=null, ?string $name=null, ?string $entry_point=null) { 
		$ctrl = self::GetController($controller) ?? igk_die('require controller');	
		$c = Subdomains::Add($name, $ctrl->getName(), $entry_point);
		if ($c)
			Logger::success('subdomain '.$name);
		else{
			Logger::danger('failed to add domain');
			return -1;
		}
	}
}