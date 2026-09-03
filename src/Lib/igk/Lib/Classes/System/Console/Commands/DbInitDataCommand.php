<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitDataCommand.php
// @date: 20230802 20:49:12
namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helper\Database;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\InitBase;
use IGK\System\EntryClassResolution;
use IGK\System\IO\Path;
use ReflectionMethod;

/**
 * auto generate doc.
 * @package IGK\System\Console\Commands
 */
class DbInitDataCommand extends AppExecCommand
{
    /**
     * Property: command.
     * @var mixed
     */
    var $command = '--db:initdata';
    /**
     * Property: desc.
     * @var mixed
     */
    var $desc = 'initialize data command'; 
	/* var $options=[]; */
    /**
     * Property: category.
     * @var mixed
     */
    var $category = 'db';
    /**
     * Property: usage.
     * @var mixed
     */
    var $usage = 'controller [action_name] [options]';

    var $options = [

    ];
    /**
     * Exec.
     * @param mixed $command
     * @param null|string $controller
     * @param null|string $action_name
     */
    public function exec($command, ?string $controller = null, ?string $action_name = null)
    {
        if (empty($action_name)) {
            self::AutoInjectController($command, $controller, $action_name);
        }

        is_null($controller) && igk_die('required controller');
        ($ctrl = self::GetController($controller)) ?? igk_die('missing controller');
        self::BindUserCommand($ctrl, $command);
        $cl = $ctrl->resolveClass(EntryClassResolution::DbInitData) ?? igk_die('init data class is missing');

        if ($action_name) {
            if (method_exists($cl, $fc = InitBase::ACTION_METHOD_PREFIX . ucfirst($action_name))) {
                $args = array_merge([$ctrl], array_slice(func_get_args(), 3));
                call_user_func_array([$cl, $fc], $args);
                Logger::success('done');
            } else {
                igk_die(sprintf('missing action name in %s', $cl));
            }
        } else {
            Logger::info('initailize db. with [./InitBase]');
            Database::InitData($ctrl);
            Logger::success('done');
        }
    }
    public function help($args = null, $controller = null)
    {
        $s = parent::help();
       
        Logger::print('actions_name* came from Database/InitData class');
        $controller = $controller ?? SysDbController::ctrl();
        if ($controller) {
            if ($ctrl = self::GetController($controller)) {
                if ($funcs = $this->_getReflectClassActions($ctrl)) {
                    Logger::print('Available actions: ' . "\n");
                    Logger::print(implode("\n", $funcs));
                    Logger::print('');
                }
            }
        }

        return $s;
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @return string[] 
     */
    private function _getReflectClassActions(BaseController $ctrl)
    {
        $cl = $ctrl->resolveClass(EntryClassResolution::DbInitData) ?? igk_die('init data class is missing');
        $g = igk_sys_reflect_class($cl);
        $r = [];
        $prefix = \IGK\System\Database\InitBase::ACTION_METHOD_PREFIX;
        foreach ($g->getMethods(ReflectionMethod::IS_PUBLIC) as $g) {
            if (!$g->isStatic()) continue;
            $n = $g->getName();
            if (igk_str_startwith($n, $prefix)) {
                $n = substr($n, strlen($prefix));
                $r[] = $n;
            }
        }
        sort($r);
        return $r;
    }
}
