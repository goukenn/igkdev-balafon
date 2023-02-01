<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeMigrationCommand.php
// @date: 20221111 22:58:34
namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\MigrationBase;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class MakeMigrationCommand extends AppExecCommand{
    var $command = '--migrate';

    var $category = "db";
    var $desc = "migrate utility";


    public function showUsage()
    {
        Logger::print('--migrate action [controller] [options]');
        Logger::info('');
        Logger::info('some action command: ');
        foreach(get_class_methods(self::class) as $n){
            if (strpos($n,'migrate_') === 0){
                Logger::print("\t".substr($n, 8));
            }
        }
    }
    /**
     * migrate exec command
     * @param mixed $command 
     * @param null|string $controller 
     * @param null|string $action 
     * @return void 
     * @throws IGKException 
     */
    public function exec($command, ?string $action = null, ?string $controller = null ) { 
   
        $offset = 2;
        if ($mcontroller = igk_getv_nil($command->options, '--controller')){
            // second parameter is action 
            // $action = $controller;
            $ctrl = self::getController($mcontroller);
            $offset = 2;
        } else {
            $ctrl = ($controller ? self::getController($controller, false): null) ?? SysDbController::ctrl(); 
            $offset = 3;             
        }
        if ($action)
        {
            if (method_exists($this, $fc = 'migrate_'.$action)){
                $args = array_slice(func_get_args(), $offset);
                array_unshift($args, $ctrl);
                array_unshift($args, $command);
                if ($this->$fc(...$args)==0){
                Logger::success('execute:'.igk_sys_request_time());
                }
            }
        } else {
            $this->showUsage();            
        }
    }

    public function migrate_new($command, ?BaseController $ctrl, ?string $name = ''){
        Logger::print('make new migration');
        if (is_null($ctrl)){
            Logger::danger("missing controller");
            return -1;
        }
        if (empty($name)){
            Logger::danger("missing name");
            return -1;
        }
        $clname = StringUtility::CamelClassName($name);
        $name = strtolower($clname);
        $file = "migration_".date('YmdHis').'_'.$name.'.php';
        $file = $ctrl->getClassesDir()."/Database/Migrations/".$file;
        $sb = new StringBuilder;

        $sb->appendLine(implode(' \n', [
            file_get_contents(implode('/', [IGK_LIB_DIR, IGK_INC_FOLDER, 'core/migration.pinc']))
        ]));
        $ns = $ctrl::ns('Database/Migrations');
        $builder = new PHPScriptBuilder;
        $builder->type('class')
        ->extends(\IGK\Database\MigrationBase::class)
        ->namespace($ns)
        ->defs($sb.'')
        ->uses([\IGK\System\DataBase\SchemaMigrationBuilder::class])
        ->name($clname);


        igk_io_w2file($file, $builder->render());
        Logger::success('gen file: '.$file);
    }
     
    public function migrate_up($command,  ?BaseController $ctrl ){
        $migHandle = new MigrationHandler($ctrl);
        return $migHandle->up(); 
    }
    public function migrate_down($command,  ?BaseController $ctrl ){
        $migHandle = new MigrationHandler($ctrl);
        return $migHandle->down(); 
    }
}