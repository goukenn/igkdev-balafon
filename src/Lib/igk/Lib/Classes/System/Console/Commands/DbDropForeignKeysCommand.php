<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbDropForeignKeysCommand.php
// @date: 20230118 15:51:18
namespace IGK\System\Console\Commands;
use IGK\Controllers\SysDbController;
use IGK\Helper\Database;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* drop all database foreign keys
* @package IGK\System\Console\Command
*/
class DbDropForeignKeysCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--db:drop-foreign-keys";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "db";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "remove all foreign keys for database";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'controller [model] [options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $model
    */
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