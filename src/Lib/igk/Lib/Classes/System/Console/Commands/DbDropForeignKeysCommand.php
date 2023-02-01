<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbDropForeignKeysCommand.php
// @date: 20230118 15:51:18
namespace IGK\System\Console\Commands;

use IGK\Controllers\SysDbController;
use IGK\Helper\Database;
use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Command
*/
class DbDropForeignKeysCommand extends AppExecCommand{
    var $command = "--db:drop-foreign-keys";
    var $category = "db";
    var $desc = "remove all foreign keys for database";

    public function exec($command,?string $controller=null ) {
        if ($controller && !($ctrl = self::GetController($controller, false))){            
            igk_die("controller not found");
        }else{
            $ctrl = $ctrl ?? SysDbController::ctrl();
        }     
        DbCommandHelper::Init($command);  
        Database::DropForeignKeys($ctrl);
    }
}