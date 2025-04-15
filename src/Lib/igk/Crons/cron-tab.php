#!/usr/bin/env php
<?php

use IGK\ApplicationFactory;
use IGK\ApplicationLoader;
use IGK\Helper\JSon;
use IGK\Models\Crons;
use IGK\System\Console\App;
use IGK\System\Console\AppConfigs;
use IGK\System\Console\ConsoleLogger;
use IGK\System\Console\IConsoleLogger;
use IGK\System\Console\Logger;
use IGK\System\Cron\CronExecutionStatus;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\Path;
use IGK\System\Process\CronJobProcess;
use Spatie\PhpUnitWatcher\ConsoleApplication;

define('CROS_DIR', __DIR__);
define('IGK_APP_CRON', 1);
error_reporting(-1);
ini_set('display_errors', 1);
require_once __DIR__ . "/../igk.environment.loading.php";





class cronApp extends IGKApplicationBase implements IConsoleLogger
{
    private $m_configs;

    /**
     * disable cron configuration 
     */
    const CNF_NO_CRON_LOGGER = 'cron.no-logger';

    public function print()
    {
        print_r(...func_get_args());
        echo PHP_EOL;
    }
    public function offscreen()
    {
        return $this;
    }
    public function getConfigs()
    {
        return $this->m_configs;
    }
    public function log($msg) {}

    public function info($msg)
    {
        $this->print(App::Gets(App::YELLOW, $msg));
    }

    public function warn($msg)
    {
        $this->print(App::Gets(App::BLUE, $msg));
    }

    public function success($msg)
    {
        $this->print(App::Gets(App::GREEN, $msg));
    }

    public function danger($msg)
    {
        echo App::Gets(App::RED, $msg);
    }

    public function bootstrap()
    {
        if (file_exists($v_conffile = Path::Combine(getcwd(), AppConfigs::ConfigurationFileName))) {
            $this->m_configs = AppConfigs::LoadConfigurationFile($v_conffile);
        }
        $this->library("zip");
        $this->library("mysql");
        $this->library("curl");


        
    }
    /**
     * just start application engine
     * @param string $entryfile 
     * @param int $render 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function run(string $entryfile, $render = 0)
    {
        IGKApp::StartEngine($this);
        AppConfigs::InitEnvironment(igk_configs());
        if (!igk_configs()->get(self::CNF_NO_CRON_LOGGER)) {
            Logger::SetLogger($this);
        } 
        igk_configs()->LogFile = igk_io_cachedir().'/crons/cron-tab'.IGK_LOG_FILE_EXT; 
    }
}


unset($_SERVER['PWD']);
ignore_user_abort(false);
$_SERVER["HTTP_USER_AGENT"] = "balafon - cron";
$_SERVER["SERVER_NAME"] = "balafon cron-server";
// $_SERVER['ENVIRONMENT'] = 'production';

ApplicationFactory::Register("crontab", cronApp::class);

\IGK\System\Console\BalafonApplication::InitAndTreatArgument($argv);

$app = ApplicationLoader::Boot("crontab");
$status = $app->run(__FILE__, false); 
  
$projects = igk_sys_get_projects_controllers();

// run_run cron setting
igk_hook(IGKEvents::HOOK_CRUNJOB, ['app' => $app, 'time' => time()]);

igk_environment()->querydebug = 1;
class_alias(\IGK\System\Cron\CommandHelper::class, 'CommandHelper');
// get cron job request 
$crons = Crons::select_all();
if ($crons) {
    while (count($crons) > 0) {
        $row = array_shift($crons);
        if ($row->process == 0) {
            $status = -1;
            $scr = $row->script;
            if (preg_match('/^\\w+@\\w+$/', $scr)) {
                list($cl, $fc) = igk_extract(explode('@', $scr, 2), '0|1');
                if (class_exists($cl) && method_exists($cl, $fc)) {
                    $arg = $d = json_decode($row->options ?? '[]', true) ?? [];
                    //$start = $start[0] + ($start[1] /1000);
                    unset($d['(@error)']);
                    if (isset($d['@params'])) {
                        $arg = $d['@params'];
                    }
                    try {
                        $start = igk_start_time('cron_exec');
                        $status = call_user_func_array([$cl, $fc], [(object)[
                            'args' => $arg,
                            'last-execution' => igk_getv($d, '@last-execution')
                        ]]);
                        $l = igk_execute_time('cron_exec', $start);

                        if ($status == CronExecutionStatus::SKIP){
                            continue;
                        }
                        $response = ['@params' => $arg, 'duration' => $l . 's'];
                        if ($status == CronExecutionStatus::RESTART) {
                            $response['@last-execution'] = igk_date_now();
                        }
                        $row->options = json_encode($response);
                        $row->process = $status;
                    } catch (\Exception $ex) {
                        $d['(@error)'] = $ex->getMessage();
                        $status = -1;
                        $row->options = json_encode($d);
                    }
                }
            }
            $row->status = $status;
            $row->update_at = null;
            $row->save();
        }
    }
}
igk_wln_e("cronjob complete : " . date('Y-m-d H:i:s'));
igk_exit(1, 0);
