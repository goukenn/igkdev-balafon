<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterCommand.php
// @date: 20260205 12:52:03
namespace IGK\System\Console\Commands\Modules;

use IGK\System\Console\AppExecCommand;

/**
* 
* @package IGK\System\Console\Commands\Modules
* @author C.A.D. BONDJE DOUE
*/
class RegisterCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--module:register';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='register module to online Balafon\'s module package repository';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'module';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = '[options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $module_name
    */
    public function exec($command, ?string $module_name=null) { 
		//TODO;
		$mod = ($module_name ? igk_get_module($module_name) ?? igk_die('missing module'): null) ?? igk_current_module() ?? igk_die('module not found');

		igk_wln_e($mod);
	}
}