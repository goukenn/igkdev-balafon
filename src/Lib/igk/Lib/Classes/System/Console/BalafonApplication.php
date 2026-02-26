<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonApplication.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerTask;
use IGK\Controllers\SysDbController;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\Helper\Traits\IOPathCheckerTrait;
use IGK\Constants;
use IGK\Models\Users;
use IGK\System\Configuration\XPathConfig;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Commands\ServerCommandHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\DotEnvConfiguration;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\FileHandler;
use IGK\System\IO\Markdown\MarkdownFileHandler;
use IGK\System\IO\Path;
use IGK\System\IO\TextFileHandler;
use IGK\System\ViewEnvironmentArgs;
use IGKApp;
use IGKApplicationBase;
use IGKAppType;
use IGKEnvironment;
use IGKEvents;
use IGKException;
use IGKModuleListMigration;
use IGKServices;
use ReflectionException;
use stdClass;
use Throwable;
use function igk_resources_gets as __;
// + | --------------------------------------------------------------------
// + | global options
// + |  --set-env: set environment definition
// + |  --set-server: set server Global value
require_once IGK_LIB_CLASSES_DIR . "/Helper/Traits/IOPathCheckerTrait.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Console/ICLICommandApp.php";
/** @package  */
class BalafonApplication extends IGKApplicationBase implements ICLICommandApp
{
    use IOPathCheckerTrait;
    /**
     * store command
     * @var mixed
     */
    public $command;
    /**
     * application base path
     */
    public $basePath;
    /**
     * store config
     * @var mixed
     */
    public $configs;

    /**
    * Property: environment.
    * @var mixed
    */
    public $environment;

    /**
     * 
     * @return mixed 
     */

    public function getInitEnvironmentFileStructure()
    {
        return igk_environment()->NoAppInitFileStruct;
    }
    /**
     * initialize application modules 
     * */

    public static function InitModule($v_pdir, $conf, &$argv)
    {
        if (!preg_match("/--module:/", implode(' ', $argv))) {
            $gd = igk_get_packages_dir();
            $module = igk_conf_get($conf, 'name') ?? $gd;
            if ($module)
                $argv[] = "--module:" . $module;
        }
        igk_environment()->console = (object)['type' => 'module'];
    }
    /**
     * init command project modules
     * @param mixed $v_pdir 
     * @param mixed $conf 
     * @param mixed &$argv 
     * @return void 
     * @throws Exception 
     */

    public static function InitProject($v_pdir, $conf, &$argv)
    {
        if (!preg_match("/--controller:/", implode(' ', $argv))) {
            $controller = igk_conf_get($conf, 'controller') ?? igk_sys_detect_project_controller($v_pdir);
            if ($controller)
                $argv[] = "--controller:" . $controller;
        }
        igk_environment()->console = json_decode('{"type":"project"}');
    }
    /**
     * filter arguments list 
     * @param mixed $a 
     * @return mixed 
     * @throws IGKException 
     */

    public static function FilterArgs($a)
    {
        if (strpos($a, "--wdir:") === 0) {
            $g = explode(":", $a, 2);
            if (is_dir($g[1]) || igk_io_createdir($g[1]))
                chdir($g[1]);
            return null;
        }
        if (strpos($a, "--env:") === 0) {
            // + | set environment mode
            $v_envkey = 'IGK_ENVIRONMENT';
            $g = strtolower(trim(implode('', array_slice(explode(":", $a), 1))));
            if (in_array($g, ["production", "development", "test"])) {
                // + | mark environment mode - priority to custom variable - fix environment mode 
                defined($v_envkey) || define($v_envkey, $g);
                // + |  set property mode 
                $_SERVER[$v_envkey] = $g;
                igk_server()->prepareServerInfo();
                igk_server()->ENVIRONMENT = $g;
                // $nev = igk_server()->ENVIRONMENT;
                // $ops = igk_environment()->isOPS();
            }
            return null;
        }
        if (strpos($a, "--set-server:") === 0) {
            // + | set environment variables
            $g = trim(implode('', array_slice(explode(":", $a), 1)));
            $l = array_filter(explode("=", $g));
            $v = true;
            if (count($l) > 1) {
                $v = $l[1];
            }
            $_SERVER[$l[0]] = $v;
            return null;
        }
        if (strpos($a, "--set-env:") === 0) {
            // + | set environment variables
            $g = trim(implode('', array_slice(explode(":", $a), 1)));
            $l = array_filter(explode("=", $g));
            $v = true;
            if (count($l) > 1) {
                $v = $l[1];
            }
            if ($v &&  in_array($tv = strtolower($v), ['true', 'false'])) {
                $v = $tv == 'true' ? true : false;
            }
            $m = $l[0];
            igk_environment()->set($m, $v);
            return null;
        }
        return $a;
    }
    /**
     * get top level configuration files
     * @param string $bdir 
     * @return string|null|void 
     */
    private static function GetTopLevelConfigFile(string $bdir)
    {
        /// TASK : GET TOP LEVEL CONFIG FILE
        while (!empty($bdir)) {
            if (igk_io_file_exists($configFile = $bdir . "/" . AppConfigs::ConfigurationFileName)) {
                return $configFile;
            }
            $b = $bdir;
            $bdir = dirname($bdir);
            if ($b == $bdir) {
                return null;
            }
        }
    }

    public function bootstrap()
    {
        // + | because prefilter command line args
        global $argv, $argc;
        $argc = count($argv);
        $_SERVER["argv"] = $argv;
        $_SERVER["argc"] = $argc;
        if ($this->basePath === null) {
            $this->basePath = getcwd() ?? die("can't get current working directory");
        }
        defined('IGK_FRAMEWORK_ATOMIC') || define('IGK_FRAMEWORK_ATOMIC', 1);
        $wd = $bdir = $this->basePath;
        // + | --------------------------------------------------------------------
        // + | INIT SERVER INFO 
        // + |        
        igk_server()->SERVER_NAME = $_SERVER["SERVER_NAME"] = igk_getv($_ENV, 'IGK_SERVER_NAME', "BalafonCLI");
        igk_server()->REMOTE_ADDR = $_SERVER["REMOTE_ADDR"] = '0.0.0.0';
        $configFile = self::GetTopLevelConfigFile($bdir);
        try {
            if (!empty($configFile) && igk_io_file_exists($configFile)) {
                $this->configs = AppConfigs::LoadConfigurationFile($configFile);
            } else {
                $this->configs = new XPathConfig((object)[]);
                // + | tempory environment loading 
                $this->configs->isTemp = true;
                $this->configs->initController = true;
                $wd = igk_environment()->get("workingDir", getcwd());
                register_shutdown_function(function () use ($wd) {
                    if (strstr($wd, sys_get_temp_dir())) {
                        // in system temp directory 
                        error_log("remove working directory from ." . $wd);
                        IO::RmDir($wd);
                    }
                });
                defined('IGK_NO_LIB_CACHE') || define('IGK_NO_LIB_CACHE', 1);
            }
        } catch (Exception $ex) {
            igk_wln_e("boostrap-application error : .... " . $ex->getMessage());
        }
        defined('IGK_APP_DIR') || define("IGK_APP_DIR", igk_getv($_SERVER, 'IGK_APP_DIR', $wd));
        defined('IGK_BASE_DIR') || define('IGK_BASE_DIR', $wd);
        // setup the log folder
        if (!defined('IGK_LOG_FILE') && ($logFolder = $this->configs->logFolder)) {
            if (is_dir($logFolder)) {
                $logFolder = realpath($logFolder);
            } else {
                $logFolder = $wd . "/" . ltrim($logFolder, '/');
            }
            define('IGK_LOG_FILE', $logFolder . "/." . igk_environment()->getToday() . ".cons.log");
        }
        // + | load balafon commands ... 
        igk_loadlib(dirname(__FILE__) . "/Commands");
        date_default_timezone_set(Constants::DEFAULT_TIME_ZONE);
        // IGKApp::InitSingle(); 
        if (defined('IGK_DOCUMENT_ROOT'))
            igk_server()->IGK_DOCUMENT_ROOT = realpath(constant('IGK_DOCUMENT_ROOT'));
        // default library 
        $this->library("zip");
        $this->library("mysql");
        $this->library("curl");
        if (extension_loaded("gd")) {
            $this->library("gd");
        }
       
        $this->InitCoreSystemComponent();
        // init sys components -  

        
        igk_hook("console::app_cli_bootstrap", $this);
        // + | force register base formatter service as a Formatter service container
        IGKServices::Register(IGKServices::FORMATTER_SERVICE, \IGK\System\Text\Formatters\FormatterServiceContainer::class);
    }
    /**
     * 
     * @param string $entryfile 
     * @param int $render 
     * @return string|int 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public function run(string $entryfile, $render = 1)
    {
        // + | --------------------------------------------------------------------------
        // + | configure engine to start
        // + |
        $this->no_init_environment = $this->no_init_environment ?? igk_server()->IGK_NO_INIT_ENVIRONMENT;
        $_env = igk_environment();
        $_env->set("app_type", IGKAppType::balafon);
        $_env->no_lib_cache = 1;
        $_env->no_db_route = 1;
        igk_configs()->no_db_route = 1;
        igk_register_service('balafon', 'cli', new BalafonCLIService);
        $argv = &$_SERVER['argv'];

        // preprocess command line arguments 
        $hook_options = [];
        if ($e = igk_hook(IGKEvents::HOOK_PREPROCESS_COMMAND_LINE, ['argv' => &$argv, 'app' => $this], $hook_options)) {
            return 0;
        }

        IGKApp::StartEngine($this);
        return \IGK\System\Console\App::Run($this->command, $this->basePath, $this->configs);
    }
    /**
     * return primary command array
     * @return array
     */

    public function getPrimaryCommand(array $argv): array
    {
        // + |--------------------------------------------------------
        // + | balafon primary command
        // + |        
        $command = [
            "--wdir" => [null, __("set startup working directory") . "\n--wdir:path_to_working_dir"],
            "--debug" => [
                function ($v, $command, $debugList=[]) {
                    if (is_array($command))
                        $command["debug"] = true;
                    igk_debug(1);
                    if ($debugList){
                        array_map(function($n){
                            igk_environment()->set('debug_'.$n, true);
                        }, $debugList);                        
                    }
                    igk_environment()->querydebug = 1;
                },
                ['desc' => __("flag: enable debug"), 'category' => "flag"]
            ],
            "--set:default_controller" => [function ($v, $command) {
                $command->exec = function ($command, $name = "") {
                    if (!empty($name) && class_exists($name)) {
                        igk_configs()->default_controller = $name;
                        igk_save_config(true);
                        Logger::success(__("controller changed to {0}", $name));
                    }
                };
            }, __("set default controller")],
            "--get:sysconfigs" => [
                function ($v, $command) {
                    $command->exec = function ($command, $pattern = null) {
                        $tab = igk_configs()->getEntries();
                        ksort($tab);
                        foreach ($tab as $k => $v) {
                            if (!$pattern ||  preg_match("/$pattern/i", $k)) {
                                if (is_array($v)) {
                                    $v = json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                }
                                Logger::print($command->app::gets(App::BLUE, $k) . "=" . $v);
                            }
                        }
                        Logger::print("\n");
                    };
                },
                __("get system configuration")
            ],
            "--get:configs" => [
                function ($v, $command) {
                    $command->exec = function ($command, $classname = null, $pattern = null) {
                        if ($c = igk_getctrl($classname, false)) {
                            $ct = $c->getConfigs();
                            $is_xml = property_exists($command->options, "-xml");
                            if ($is_xml) {
                                $opt = igk_createobj();
                                $opt->Indent = true;
                                $opt->Context = "xml";
                                igk_createxml_config_data($ct->to_array())->renderAJX($opt);
                            } else {
                                $cl = $ct->to_array();
                                Logger::print(json_encode($cl, JSON_PRETTY_PRINT));
                            }
                        }
                        Logger::print("\n");
                    };
                },
                __("get controller class configuration")
            ],
            "--set:configs" => [
                function ($v, $command) {
                    $command->exec = function ($command, $classname = null, $path = null, $value = null) {
                        if (($c = igk_getctrl($classname, false)) && !empty($path)) {
                            $c->Configs->$path = $value;
                            $c->Configs->storeConfig();
                            Logger::success("Change configs");
                        }
                        Logger::print("\n");
                    };
                },
                __("set controller's configuration")
            ],
            "--set:libconfigs" => [
                function ($v, $command) {
                    $command->exec = function ($command, $path = null, $value = null) {
                        if ($cnf = igk_lib_configs()) {
                            $cnf->$path = $value;
                            $cnf->storeConfig();
                            Logger::success("Change configs");
                        }
                        Logger::print("");
                    };
                },
                __("set system configuration")
            ],
            "--site:lock" => [
                function ($v, $command) {
                    $command->exec = function ($command, $dir = null) {
                        $dir = $dir ?? IGK_BASE_DIR;
                        \IGK\Helper\MaintenanceHelper::LockSite($dir);
                    };
                },
                [
                    "desc" => "Lock site. put it in maintenance mode."
                ]
            ],
            "--site:unlock" => [
                function ($v, $command) {
                    $command->exec = function ($command, $dir = null) {
                        $dir = $dir ?? IGK_BASE_DIR;
                        \IGK\Helper\MaintenanceHelper::UnlockSite($dir);
                    };
                },
                [
                    "desc" => "Unlock site."
                ]
            ],
            "--set:maintenance" => [
                function () {
                    $dir = igk_io_basedir();
                    Logger::info("maintenance site " . $dir);
                    if (igk_io_file_exists($file = $dir . "/" . \IGK\Helper\MaintenanceHelper::lockFile)) {
                        Logger::info("unlock site ...");
                        // in maintenace mode
                        \IGK\Helper\MaintenanceHelper::UnlockSite($dir);
                        // @unlink($dir."/.htaccess");
                        // @unlink($dir."/index.php");
                        // @rename($dir."/.lock.index.php", $dir."/index.php");
                        // @rename($dir."/.lock.htaccess", $dir."/.htaccess");
                        igk_io_file_exists($file) && @unlink($file);
                    } else {
                        // put in maintence mode
                        Logger::info("lock site ...");
                        \IGK\Helper\MaintenanceHelper::LockSite($dir);
                    }
                    Logger::success("maintenance");
                },
                [
                    "desc" => "toggle maintenance mode"
                ]
            ],
            "--set:sysconfigs" => [
                function ($v, $command) {
                    DbCommandHelper::Init($command);
                    $command->exec = function ($command, $name = null, $value = null) {
                        Logger::print($name, $value);
                        if (!empty($name)) {
                            if (strpos($name, "=") !== false) {
                                $g = array_map('trim', explode("=", $name));
                                $name = $g[0];
                                $value = $g[1];
                            }
                            igk_configs()->$name = $value;
                            igk_save_config(true);
                            Logger::success("configuration changed: " . $name);
                        }
                    };
                },
                [
                    "desc" => __("set configuration. name value"),
                    "help" => function () {
                        Logger::print("\nusage : --set:sysconfig (property value|[...property=value])\n");
                    }
                ]
            ],
            "--controller:list" => [function ($v, $command) {
                $command->exec = function ($command, $pattern = ".+") {
                    Logger::print("");
                    $c = igk_app()->getControllerManager()->getControllers();
                    $t = [];
                    foreach ($c as $m) {
                        if (preg_match("#" . $pattern . "#", $cl = get_class($m))) {
                            $t[] = $command->app::gets(App::YELLOW, $cl) . "\r\n\t\t\t" . $m->getDeclaredDir();
                        }
                    }
                    sort($t, SORT_FLAG_CASE | SORT_STRING);
                    Logger::print(implode("\n", $t));
                    return 1;
                };
            }, __("list all controller"), "controller"],
            '--run' => [
                function ($v, $command = null) {
                    $command->exec = function ($command, ?string $file = null) {

                        if (property_exists($command->options, '--command:ls')) {

                            $def = EnvironmentCommandScripts::GetCacheDefinition();
                            Logger::print('commands');
                            foreach ($def as $k => $v) {
                                Logger::info($k);
                            }

                            return -1;
                        }
                        if (empty($file)) {
                            Logger::danger(__("args: require file"));
                            return -1;
                        }
                        if (!file_exists($file)) {
                            $dir = igk_getv($command->options, '--commands_dir');
                            $file = EnvironmentCommandScripts::GetCommandFile($file, $dir) ?? igk_die('missing arg command');
                        }
                        DbCommandHelper::Init($command);
                        ServerCommandHelper::Init($command);
                        if ($ctrl = igk_getv_nil($command->options, '--controller')) {
                            $ctrl = SysUtils::GetControllerByName($ctrl, true);
                        }
                        $user = null;
                        $ctrl = $ctrl ?? SysDbController::ctrl();
                        // - bind controller 
                        self::BindCommandController($ctrl);
                        self::BindCommandUser($command, $ctrl, $user);
                        $args = ViewEnvironmentArgs::CreateContextViewArgument($ctrl, __FILE__, 'balafon');
                        $params = array_slice(func_get_args(), 2);
                        $args->params = &$params;
                        $args->user = $user;
                        $file = realpath($file) === false ? Path::ResolvePath($file) : $file;
                        try {
                            if ($file && file_exists($file)) {
                                $tab = array_merge([
                                    "ctrl" => $ctrl,
                                    "user" => $user,
                                    "command" => $command
                                ], (array)$args);
                                $result = SysUtils::Include($file, $tab);
                                if ($result) {
                                    Logger::print('--- response ---');
                                    if (is_string($result)) {
                                        Logger::print($result);
                                    } else {
                                        var_dump($result);
                                    }
                                }
                            } else {
                                Logger::danger(sprintf('%s %s', __('[ run file ]'), App::Gets(
                                    App::BLUE,
                                    __('file not found')
                                )));
                            }
                        } catch (Throwable $ex) {
                            $trace = $ex->getTrace()[0];
                            $TAG = ($ex instanceof \IGKException) ? '[BLF]' : '[EXTERNAL]';
                            Logger::danger(
                                sprintf("%s - ", $TAG) .
                                    implode(':', [
                                        $ex->getMessage() . " \nAt: " .
                                            sprintf(
                                                '%s:%s',
                                                $ex->getFile(),
                                                $ex->getLine()
                                            )
                                    ])
                            );
                            return false;
                        }
                        return 0;
                    };
                },
                [
                    "desc" => __("run script by loading"),
                    "help" => function ($command, ?string $filename = null) {
                        if ($filename && ($file = Path::ResolvePath($filename))) {
                            // initialize command 
                            $fc = $command->app->command['--run'][0];
                            $targs = func_get_args();
                            // $margs = array_merge([null], func_get_args());
                            call_user_func_array($fc, array_merge([null], func_get_args()));
                            // invoke the running
                            return call_user_func_array($command->exec, $targs);
                        }
                        $sp = "\r\n\t\t\t\t";
                        Logger::info(implode(
                            "\n\n",
                            [
                                App::Gets(App::BLUE_B, "--run usage") . $sp. " [options*] [dbcommand*] scriptfile",
                                App::Gets(App::GREEN, "--controller") . ":[targetController]\r\n\t\t\t\tset base project controller",
                                App::Gets(App::GREEN, "--command:ls") . $sp."list all registrated command",
                                App::Gets(App::GREEN, "--user") . ":id\r\n\t\t\t\tglobal user to use",
                                App::Gets(App::GREEN, "--commands_dir") . ":dir\r\n\t\t\t\tglobal directory that contains scripts to run",
                            ]
                        ));
                    }
                ]
            ],
            "--run:cron" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = null) {
                    DbCommandHelper::Init($command);
                    $job = new \IGK\System\CronJob();
                    $job->provider = igk_getv($command->options, "--provider");
                    $job->ctrl = $ctrl;
                    return $job->execute();
                };
            }, __("run cron's script"), 'administration'],
            "-v, --version" => [function ($arg, $command) {
                // + | balafon : exec version 
                if (!$command->exec) {
                    $command->exec = function () {
                        Logger::info("Core Version:");
                        echo IGK_VERSION . "\n";
                        Logger::info("CLI - Version:");
                        Logger::print(App::version);
                        Logger::info("Author:");
                        echo IGK_AUTHOR . "\n";
                        Logger::info("PHP_VERSION:");
                        echo PHP_VERSION . "\n";
                        try {
                            if (defined('IGK_WORKING_DIR'))
                                echo 'working-dir: ' . constant('IGK_WORKING_DIR'), PHP_EOL;
                            $info = [
                                'lib_dir' => IGK_LIB_DIR,
                                'app_dir' => igk_io_applicationdir(),
                                'project_dir' => igk_io_projectdir(),
                                'package_dir' => Path::getInstance()->getPackagesDir(),
                                'module_dir' => Path::getInstance()->getModuleDir(),
                                'server_info' => $_SERVER,
                                'env' => $_ENV
                            ];
                        } catch (\Exception $ex) {
                            echo "failed : " . $ex->getMessage();
                        }
                        Logger::warn('application info:');
                        Logger::print(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        return 0;
                    };
                } else {
                    $command->options->{'--version'} = 1;
                }
            }, "show the current version"],
            "--help" => [function ($arg, $command) {
                if ($command->exec) {
                    $command->options->{"--help"} = "1";
                } else {
                    $command->exec = function ($command) {
                        $command->app->showHelp(null, igk_getv($command->options, '--filter'));
                        return 0;
                    };
                }
            }, ["desc" => "show help or activate help option for a command"], "sys"],
        ];
        $this->initCommand($command, $argv);
        return $command;
    }
    /**
     * initialize command
     * @param array $command 
     * @param array $argv 
     * @return void 
     */

    protected function initCommand(array $command, array $argv)
    {
        igk_environment()->NoAppInitFileStruct = true;
        if (in_array("--debug", $argv)) {
            $fc = $command["--debug"][0];
            $fc([], $command);
        }else {
            $tc = array_slice($argv,1);
            $tdebug = [];
            foreach($tc as $c){
                if (preg_match('/^--debug:/', $c)){
                    $tdebug[] = explode(':', $c,2)[1];
                }
            }
            if ($tdebug){
                    $fc = $command["--debug"][0];
                    $fc([], $command, $tdebug);
            }
        }
        if (in_array('--report-error', $argv)) {
            // activate error reporting
            ini_set('display_errors', 1);
            error_reporting(-1);
        }
        if (1 == array_search(Constants::INIT_COMMAND, $argv)) {
            \IGK\System\Console\Commands\BalafonInitCommand::Handle($this->no_init_environment, $argv);
        }
    }
    /**
     * 
     * @param mixed $command 
     * @param null|BaseController $ctrl 
     * @return void 
     * @throws Exception 
     */

    public static function BindCommandUser($command, ?BaseController $ctrl = null, &$user = null)
    {
        $user = null;
        if ($id = intval($login = igk_getv($command->options, '--user'))) {
            $user = \IGK\Models\Users::Get('clId', $id);
        } else if (!empty($login)) {
            $user = igk_get_user_bylogin($login);
        }
        if ($user && $ctrl) {
            $ctrl::login($user, null, false);
        }
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param null|Users $user 
     * @return void 
     */

    public static function BindCommandController(BaseController $ctrl, ?Users $user = null)
    {
        igk_environment()->set(IGKEnvironment::CURRENT_CTRL, $ctrl);
        igk_environment()->set(IGKEnvironment::CURRENT_USER, $user);
        $ctrl->register_autoload();
    }
    /**
     * get working dirctory
     * @return ?string
     */

    public function getWorkingDir()
    {
        return $this->basePath;
    }
    /**
     * initialize and treat argument
     * @param mixed &$argv 
     * @return void 
     */

    public static function InitAndTreatArgument(&$argv)
    {
        (function (&$argv) {
            require_once IGK_LIB_DIR . '/Lib/' . IGK_CLASSES_FOLDER . '/Constants.php';
            // start by filtering
            if (!isset($_SERVER['PWD'])) {
                $cwd = getcwd();
                $rf = igk_getv($_SERVER, 'SCRIPT_FILENAME');
                if (!self::IsRootPath($rf)) {
                    $rf = Path::CombineAndFlattenPath($cwd, $rf);
                }
                $fc_local = false;
                while (!$fc_local && $rf) {
                    $tf = $rf;
                    if ($tf == ($rf = dirname($rf))) {
                        // for both UNIX and WINDOW
                        break;
                    }
                    $v_conf = $rf . DIRECTORY_SEPARATOR . AppConfigs::ConfigurationFileName;
                    if (igk_io_file_exists($v_conf)) {
                        $fc_local = true;
                        $cwd = $rf;
                        break;
                    }
                }
                // resolv util sites found . 
                $_SERVER['PWD'] = $cwd;
                chdir($cwd);
            }
            $cwd = $_SERVER['PWD'];
            $r = array_map(BalafonApplication::class . "::FilterArgs", $argv);
            $argv = array_filter($r, function ($i) {
                return !is_null($i);
            });
            $proj_conf = '/' . Constants::PROJECT_CONF_FILE;
            $mod_conf = '/' . Constants::MODULE_CONF_FILE;
            $_filter = false;
            if ($cwd != ($rcwd = getcwd())) {
                $_SERVER['PWD'] = $rcwd;
                $_filter = true;
            }
            foreach (
                [
                    $proj_conf => [BalafonApplication::class, 'InitProject'],
                    $mod_conf => [BalafonApplication::class, 'InitModule']
                ] as $k => $callable
            ) {
                if (igk_io_file_exists($cf = $_SERVER['PWD'] . $k)) {
                    $v_pdir = dirname($cf);
                    if ($conf = json_decode(file_get_contents($cf))) {
                        // + | change workbench current working directory 
                        $wb = igk_conf_get($conf, "workbench/cwd") ?? getenv('IGK_WORKING_DIR');
                        if ($wb && is_dir($wb)) {
                            chdir($wb);
                        } else if ($wb) {
                            fwrite(STDERR, "missing configured worked directory");
                        }
                        $callable($v_pdir, $conf, $argv);
                        set_include_path($v_pdir . ':' . get_include_path());
                        return;
                    }
                }
            }
            // + | --------------------------------------------------------------------
            // + | so working dir fallback
            // + | 
            if (Constants::INIT_COMMAND != igk_getv($argv, 1)){ 

                if (!$_filter && ($wdir = getenv('IGK_WORKING_DIR')) && ($wdir != $_SERVER['PWD'])) {
                    if (is_dir($wdir)) {
                        //  + | add include path to path separator
                        set_include_path(get_include_path() . PATH_SEPARATOR . $cwd);
                        $_SERVER['PWD'] = $wdir;
                        chdir($wdir);
                        /**
                         * save the command working directory so that it can
                         */
                        $_SERVER['IGK_COMMAND_PWD'] = $cwd;
                    }
                }
            }
            // + | no reach fallback
            if (file_exists($cf = $_SERVER['PWD'] . '/' . Constants::PROJECT_CONF_FILE)) {
                $v_pdir = dirname($cf);
                if ($conf = json_decode(file_get_contents($cf))) {
                    // + | change workbench current working directory 
                    $wb = igk_conf_get($conf, "workbench/cwd") ?? getenv('IGK_WORKING_DIR');
                    if ($wb && is_dir($wb)) {
                        chdir($wb);
                    } else if ($wb) {
                        fwrite(STDERR, "missing configured worked directory");
                    }
                    if (!preg_match("/--controller:/", implode(' ', $argv))) {
                        $controller = igk_conf_get($conf, 'controller') ?? igk_sys_detect_project_controller($v_pdir);
                        if ($controller)
                            $argv[] = "--controller:" . $controller;
                    }
                    set_include_path($v_pdir . PATH_SEPARATOR . get_include_path());
                }
            }
        })($argv);
    }
}
