<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbDropForeignKeysCommand.php
// @date: 20230118 15:51:18
namespace IGK\System\Console\Commands;

use IGK\Controllers\SysDbController;
use IGK\Helper\Database;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* drop all database foreign keys
* @package IGK\System\Console\Command
*/
class DbDropForeignKeysCommand extends AppExecCommand{
    var $command = "--db:drop-foreign-keys";
    var $category = "db";
    var $desc = "remove all foreign keys for database";
    var $usage = 'controller [model] [options]';
    public function exec($command,?string $controller=null, ?string $model=null ) {
        if ($controller && !($ctrl = self::GetController($controller, false))){            
            igk_die("controller not found");
        }else{
            $ctrl = $ctrl ?? SysDbController::ctrl();
        }     
        if ($model){
            $model = $ctrl->model($model) ?? igk_die("missing model");
            Logger::offscreen()->warn('drop model forein keys');
            $ctrl->getDataAdapter()->dropForeignKeys([$model::table()]);
            Logger::offscreen()->success('done');
            return 0;
        }
        DbCommandHelper::Init($command);  
        Database::DropForeignKeys($ctrl);
    }
}