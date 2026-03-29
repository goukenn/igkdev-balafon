<?php
// @author: C.A.D. BONDJE DOUE
// @file: AddColumnDataSchemaCommand.php
// @date: 20240910 20:16:22
// @exemple: balafon --db:schema-add-column commandlist "id;id" AppTestProject
namespace IGK\System\Console\Commands\Database;
use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\Exceptions\CommandException;
use IGK\System\Console\Logger;
/**
* add column to table schemas
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class AddColumnDataSchemaCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--db:schema-add-column';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='dbschema: add new column to controller\'s project tables';
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category="db";
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'table_name column_definition [controller]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $table_name
    * @param null|string $column_definition
    * @param null|string $controller
    */
    public function exec($command, ?string $table_name=null, ?string $column_definition=null, ?string $controller=null) { 
		if (igk_is_null_or_empty($table_name)){
			throw new CommandException('table\"s  name required');
		}
		if (igk_is_null_or_empty($column_definition)){
			throw new CommandException('column_definition required');
		}
		$ctrl = self::ResolveController($command, $controller); 
		if (igk_db_command_column($ctrl, $table_name, $column_definition)){
			Logger::success(sprintf('%s: schema modified', $ctrl->getName()));
		}else{
			Logger::danger('failed.');
			return -1;
		} 
	}
} 