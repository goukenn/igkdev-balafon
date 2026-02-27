<?php
// @author: C.A.D. BONDJE DOUE
// @file: RunDumpDbPropertyCommand.php
// @date: 20221118 09:15:39
namespace IGK\System\Console\Commands;
use IGK\Controllers\SysDbController;
use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\EntryClassResolution;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class RunDumpDbPropertyCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:dump-property';

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';

    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        '--create-query'=>'',
    ];

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'dump model property in controller';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'model controller';

    /**
    * Shows Usage.
    */
    protected function showUsage()
    {
        Logger::print("Usage:\n");
        Logger::info(sprintf("%s model controller",$this->command));
    }

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $model
    * @param null|string $controller
    */
    public function exec($command, ?string $model=null, ?string $controller = null) { 
        if (igk_is_null_or_empty($model)){
            igk_die('require model');
        }
        if ($controller){
            $ctrl = SysUtils::GetControllerByName($controller, false);
        }
        $ctrl = $ctrl ?? SysDbController::ctrl(); 
        $ctrl->register_autoload();
        if ($cl = $ctrl->resolveClass(EntryClassResolution::Models."\\".$model)){
            $a = 0;
            $row = $cl::createRow();
            if (property_exists($command->options, '--create-query')){
                $ad = $ctrl->getDataAdapter();
                $query = $ad->getGrammar()->createTableQuery($cl::table(), 
                    (array) $cl::model()->getTableInfo());
                Logger::print($query);
                Logger::print("# - ");
                $a = 1;
            }
            if (property_exists($command->options, '--insert-query')){
                $ad = $ctrl->getDataAdapter();
                $query = $ad->getGrammar()->createInsertQuery($cl::table(), (array) $row);
                Logger::print($query);
                Logger::print("# - ");
                $a = 1;
            }
            if ($a == 0)
            Logger::print($cl::dump_export());
        }
        else {
            Logger::danger('model class not found');
            return -1;
        }
    }
}