<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitSystemDatabaseCommand.php
// @date: 20221118 21:36:59
namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Database\DbSchemasConstants;
use IGK\Helper\Database;
use IGK\Helpers\DbUtilityHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
 * auto generate doc.
 * @package IGK\System\Console\Commands
 */
class InitSystemDatabaseCommand extends AppExecCommand
{
    /**
     * Property: command.
     * @var mixed
     */
    var $command = "--dbsys:initdb";
    /**
     * Property: desc.
     * @var mixed
     */
    var $desc = 'init core system db';
    /**
     * Property: category.
     * @var mixed
     */
    var $category = "sys:db";
    /**
     * Property: options.
     * @var mixed
     */
    var $options = [
        '--force' => 'flag: force database initialisation model',
        '--drop' => 'flag: drop database before initialize',
    ];
    /**
     * Exec.
     * @param mixed $command
     */
    public function exec($command)
    {
        DbCommandHelper::Init($command);
        $ad = SysDbController::ctrl()->getDataAdapter();
        if (!$ad->connect()) {
            Logger::danger('can not connect to db');
            return -1;
        }
        $dbname = igk_configs()->db_name;
        if (property_exists($command->options, '--drop') && $dbname) {
            if ($ad->getName() == IGK_MYSQL_DATAADAPTER) {
                $ad->sendQuery('DROP DATABASE IF EXISTS `' . $dbname . '`;');
                $ad->sendQuery('CREATE DATABASE IF NOT EXISTS `' . $dbname . '`;');
                $ad->sendQuery('USE `' . $dbname . '`;');
            } else {
                $ad->dropDatabase($dbname);
                $ad->createDataBase($dbname);
            }
        }
        $force = property_exists($command->options, '--force');
        Database::InitSystemDb($force);
    }
}
