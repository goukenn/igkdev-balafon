<?php
// @author: C.A.D. BONDJE DOUE
// @file: formattersCommand.php
// @date: 20250808 12:14:55
namespace IGK\System\Console\Commands;

use Exception;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKServices;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class formattersCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--formatters';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='handle formatters';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		'action*'=>[
			'register'=>'register external formatter as default',
			'list'=>'list formatters',
		], 
	];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'formatters';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'action [options]';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $action
    */
    public function exec($command, ?string $action=null) { 
		$action || igk_die('required action');

		if (!method_exists($this, $fc = '_action_'.$action)){
			igk_die('missing action');
		}
		$args = array_slice(func_get_args(), 2);
		array_unshift($args, $command);
		call_user_func_array([$this, $fc], $args);
	}

    /**
    * Action register.
    * @param null|string $name
    * @param null|string $classname
    */
    protected function _action_register(?string $name=null, ?string $classname=null){
		if (empty($args = array_filter(func_get_args()))){
			igk_die('arguments required');
		}
	}
	/**
	 * action list 
	 * @return void 
	 * @throws Exception 
	 */

    protected function _action_list(){
		Logger::print('list formatters: ');
		$g = igk_app()->getService(IGKServices::FORMATTER_SERVICE); 
		print_r($g); 
	}
}