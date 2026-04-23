<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewListCommand.php
// @date: 20230313 21:29:46
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\Helper\ViewHelper;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ViewListCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--view:list';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='list controller\'s view';
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "controller";
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[];
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    */
    public function exec($command, ?string $controller = null) {
		$ctrl = ($controller ? self::GetController($controller) : null)?? die("missing controller");
		array_map(function($f){
			igk_wln($f);
		},
		ViewHelper::GetViews(true, null, $ctrl));
	}
}