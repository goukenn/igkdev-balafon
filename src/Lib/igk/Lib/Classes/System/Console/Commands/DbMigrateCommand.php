<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbMigrateCommand.php
// @date: 20221111 22:30:40
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Helper\SysUtils;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use IGKModuleListMigration;
!defined('IGK_CONSOLE_HTRAIT') &&  define('IGK_CONSOLE_HTRAIT', str_repeat('-', 60));

/**
* Db migrate command.
* @package IGK\System\Console\Commands
*/
class DbMigrateCommand extends AppExecCommand
{

    /**
    * Constant: h trait.
    * @var mixed
    */
    const H_TRAIT = IGK_CONSOLE_HTRAIT;

    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:migrate';

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'migration command';

    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        '--no-clear-db-cache'=>'flag: do not clear db cache',
        '--force'=>"flag: force module class creation"
    ];

    /**
    * auto generate doc.
    * @return void
    */

    public function showUsage(){
        parent::showCommandUsage('controller [options]');
    }

    /**
    * Exec.
    * @param mixed $command
    * @param null|mixed $ctrl
    */

    public function exec($command, $ctrl = null)
    {
        DbCommandHelper::Init($command);
        if (!is_null($ctrl)) {
            if (($c = self::GetController($ctrl, false))) {
                $c = [$c];
            } else {
                igk_die("missing controller : " . $ctrl);
            }
        } else {
            $c = igk_sys_getall_ctrl();
            if (($ctrl === null) && ($modules = igk_get_modules())) {
                $list = array_filter(array_map(function ($c, $k) {
                    if ($mod = igk_get_module($k)) {
                        return $mod;
                    }
                }, $modules, array_keys($modules)));
                SysUtils::PrependSysDb($c);
                $c = array_merge($c, [IGKModuleListMigration::Create($list)]);
            }
        }
        if (!$c){
            Logger::danger('no controller found to migrate');
            return -1;
        }
        $HTrait = str_repeat('-', 20);
        Logger::print(self::H_TRAIT);
        Logger::info("Do migration ");
        Logger::print(self::H_TRAIT."\n");
        if (!property_exists($command->options, '--no-clear-db-cache')){
            igk_ilog('clear db caches');
            DBCaches::Clear();
        }  
        foreach ($c as $t) {
            $cl = get_class($t);
            Logger::info("Migrate ... " . $cl);
            if ($t->getCanInitDb()) {
                // call core migration - update  
                if ($t::migrate(true)) {
                    Logger::success("Migrate: " . $cl);
                    if (!($t instanceof IGKModuleListMigration)){
                        $migHandle = new MigrationHandler($t);
                        $migHandle->up();
                    }
                } else {
                    Logger::danger("failed to migrate : " . $cl);
                }
            }
        } 
    }
}