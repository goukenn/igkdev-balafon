<?php
// @author: C.A.D. BONDJE DOUE
// @file: AddTableToSchemaCommand.php
// @date: 20240910 19:36:40
namespace IGK\System\Console\Commands\Database;

use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\Exceptions\CommandException;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class AddTableToSchemaCommand extends AppExecCommand{
	var $command ='--db:schema-add-table';
	var $desc	 ='add table to db schema file';
	/* var $options=[]; */
	var $category = 'db';
	var $usage = "table_list [controller]";
	public function exec($command, ?string $table_list=null, ?string $controller=null){
		if (igk_is_null_or_empty($table_list)){
			throw new CommandException('table\"s required');
		}
		$ctrl = self::ResolveController($command, $controller) ?? igk_die('missing controller'); 
		igk_db_command_table($ctrl, $table_list);
		igk_exit(0);
	}
}