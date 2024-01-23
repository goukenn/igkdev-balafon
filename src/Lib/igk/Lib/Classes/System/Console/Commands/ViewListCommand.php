<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewListCommand.php
// @date: 20230313 21:29:46
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\Helper\ViewHelper;
///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class ViewListCommand extends AppExecCommand{
	var $command='--view:list';
	var $desc='list controller\'s'; 
	var $category = "view";
	/* var $options=[]; */
	/* var \$category; */
	public function exec($command, string $controller = null) {
		
		$ctrl = ($controller ? self::GetController($controller) : null)?? die("missing controller");
		array_map(function($f){
			igk_wln($f);
		},
		ViewHelper::GetViews(true, null, $ctrl));
	}
}