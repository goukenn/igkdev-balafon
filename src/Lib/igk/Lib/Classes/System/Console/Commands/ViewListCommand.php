<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewListCommand.php
// @date: 20230313 21:29:46
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\Helper\ViewHelper;
/**
* 
* @package IGK\System\Console\Commands
*/
class ViewListCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--view:list';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='list controller\'s view';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "controller";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[];

    /**
    * auto generate doc.
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