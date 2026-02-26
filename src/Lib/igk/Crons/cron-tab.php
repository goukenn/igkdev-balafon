#!/usr/bin/env php
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: cron-tab.php
// @date: 20260222 16:10:14
// @desc: cront application 


namespace IGK\System\Console\Application;

use IGK\ApplicationFactory;
use IGK\ApplicationLoader;
use IGK\Models\Crons;
use IGK\System\Console\App;
use IGK\System\Console\AppConfigs;
use IGK\System\Console\IConsoleLogger;
use IGK\System\Console\Logger;
use IGK\System\Cron\CronScriptHandler;
use IGK\System\CronJob;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\Path; 

define('IGK_CRON_START_DIR', __DIR__);
define('IGK_APP_CRON', 1);
error_reporting(-1);
ini_set('display_errors', 1);
require_once __DIR__ . "/../igk.environment.loading.php";

require_once IGK_LIB_CLASSES_DIR.'/System/Cron/CronScriptHandler.php';


/**
 * 
 * @package 
 */
class cronApp extends IGKApplicationBase implements IConsoleLogger
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_configs;

    /**
     * disable cron configuration 
     */
    const CNF_NO_CRON_LOGGER = 'cron.no-logger';

    /**
    * auto generate doc.
    */
    public function print()
    {
        print_r(...func_get_args());
        echo PHP_EOL;
    }

    /**
    * auto generate doc.
    */
    public function offscreen()
    {
        return $this;
    }

    /**
    * auto generate doc.
    */
    public function getConfigs()
    {
        return $this->m_configs;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    public function log($msg) {}

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    public function info($msg)
    {
        $this->print(App::Gets(App::YELLOW, $msg));
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    public function warn($msg)
    {
        $this->print(App::Gets(App::BLUE, $msg));
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    public function success($msg)
    {
        $this->print(App::Gets(App::GREEN, $msg));
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    public function danger($msg)
    {
        echo App::Gets(App::RED, $msg);
    }

    /**
    * auto generate doc.
    */
    public function bootstrap()
    {
        if (igk_io_file_exists($v_conffile = Path::Combine(getcwd(), AppConfigs::ConfigurationFileName))) {
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
        $cnf = igk_configs();
        $cnf->LogFile = igk_io_cachedir() . '/crons/cron-tab' . IGK_LOG_FILE_EXT;
        if ($libs = $cnf->cronLibraries) {
            $s = ['mysql', 'zip', 'curl'];
            foreach ($libs as $k) {
                if (!in_array($k, $s)) {
                    try {
                        $this->library($s);
                    } catch (\Exception $ex) {
                    }
                }
            }
        }
    }
}


/**
 * handle cron script
 * @return void 
 */
function igk_handle_cron_script()
{
    $s = new CronScriptHandler;
    return call_user_func_array([$s, 'handle'], func_get_args());
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
if (in_array('--querydebug', $argv))
    igk_environment()->querydebug = 1;
if (in_array('--debug', $argv))
    igk_debug(true);

if (!class_exists('CommandHelper', false))
    class_alias(\IGK\System\Cron\CommandHelper::class, 'CommandHelper');
 
// get cron job request 
$crons = Crons::select_all();
CronJob::ExecuteCronList($crons, 'igk_handle_cron_script', __FILE__); 
igk_wln_e("cronjob complete : " . date('Y-m-d H:i:s'));
igk_exit(1, 0);
