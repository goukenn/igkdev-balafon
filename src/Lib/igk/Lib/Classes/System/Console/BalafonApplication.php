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
use IGK\Models\Users;
use IGK\System\Configuration\XPathConfig;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Commands\ServerCommandHelper;
use IGK\System\Database\DbUtils;
use IGK\System\Database\MySQL\Controllers\DbConfigController;
use IGK\System\Html\Dom\HtmlCtrlNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\SystemUserProfile;
use IGK\System\ViewEnvironmentArgs;
use IGKApp;
use IGKApplicationBase;
use IGKConstants;
use IGKEnvironment;
use IGKException;
use IGKModuleListMigration;
use stdClass;
use Throwable;
use function igk_resources_gets as __;

// + | --------------------------------------------------------------------
// + | global options
// + |  --set-env: set environment definition
// + |  --set-server: set server Global value



/** @package  */
class BalafonApplication extends IGKApplicationBase
{
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

    public $environment;

    /**
     * filter arguments list 
     * @param mixed $a 
     * @return mixed 
     * @throws IGKException 
     */
    public static function FilterArgs($a)
    {
        if (strpos($a, "--wdir:") === 0) {
            $g = explode(":", $a);
            if (is_dir($g[1]) || igk_io_createdir($g[1]))
                chdir($g[1]);
            return null;
        }

        if (strpos($a, "--env:") === 0) {
            // + | set environment mode
            $g = strtolower(trim(implode('', array_slice(explode(":", $a),1))));
            if (in_array($g, ["production","development","test"])){
                // + | mark environment mode - priority to custom variable - fix environment mode 
                defined('IGK_ENVIRONMENT' ) || define('IGK_ENVIRONMENT', $g);
                // + |  set property mode 
                $_SERVER['IGK_ENVIRONMENT'] = $g;
                igk_server()->prepareServerInfo();
                igk_server()->ENVIRONMENT = $g; 
                $nev = igk_server()->ENVIRONMENT;
                $ops = igk_environment()->isOPS();
            }            
            return null;
        }
        if (strpos($a, "--set-server:") === 0) {
            // + | set environment variables
            $g = trim(implode('', array_slice(explode(":", $a),1)));
            $l = array_filter(explode("=", $g));
            $v = true;
            if (count($l)>1){
                $v = $l[1];
            }
            $_SERVER[$l[0]] = $v;
            return null;
        }
        if (strpos($a, "--set-env:") === 0) {
            // + | set environment variables
            $g = trim(implode('', array_slice(explode(":", $a),1)));
            $l = array_filter(explode("=", $g));
            $v = true;
            if (count($l)>1){
                $v = $l[1];
                
            }
            if ($v &&  in_array($tv = strtolower($v), ['true', 'false'])){
                $v = $tv == 'true' ? true : false;
            }
            $m = $l[0];
            igk_environment()->set($m, $v);            
            return null;
        }
        return $a;
    }
    /**
     * resolv path constan helper
     * @param mixed $dir 
     * @param mixed $value 
     * @return null|string 
     */
    private static function ResolvPathConstant($dir, $value)
    {
        return IO::ResolvPathConstant($dir, $value);
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
            if (file_exists($configFile = $bdir . "/" . AppConfigs::ConfigurationFileName)) {
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

        $argv = array_filter(array_map(get_class($this) . "::FilterArgs", $argv));
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
        igk_server()->REMOTE_ADDR = $_SERVER["REMOTE_ADDR"] = "0.0.0.0";

        $configFile = self::GetTopLevelConfigFile($bdir);
 
        try {
            if (!empty($configFile) && file_exists($configFile)) {
                $wd = dirname($configFile);
                $c = igk_conf_load_file($configFile, "balafon");
                $this->configs = new XPathConfig($c);
                $c = $this->configs->get("env");
                if ($c) {
                    if (!is_array($c))
                        $c = [$c];
                    foreach ($c as $env) {
                        defined($env->name) || define(
                            $env->name,
                            preg_match("/_DIR$/", $env->name) ? self::ResolvPathConstant($wd, $env->value) :
                                $env->value
                        );
                    }
                }
            } else {
                $this->configs = new XPathConfig((object)[]);
                $this->configs->isTemp = true;
                $wd = igk_environment()->get("workingDir", getcwd());
                register_shutdown_function(function () use ($wd){
                    if (strstr($wd, sys_get_temp_dir())) {
                        // in system temp directory 
                        error_log("remove working directory from .".$wd);
                        IO::RmDir($wd);
                    }
                });
                defined('IGK_NO_LIB_CACHE') || define('IGK_NO_LIB_CACHE', 1);
            }
        } catch (Exception $ex) {
            igk_wln_e("boostrap-application error : .... " . $ex->getMessage());
        }
        defined('IGK_APP_DIR') || define("IGK_APP_DIR", $wd);
        defined('IGK_BASE_DIR') || define("IGK_BASE_DIR", $wd);
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
        date_default_timezone_set(IGKConstants::DEFAULT_TIME_ZONE);
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
        igk_hook("console::app_cli_bootstrap", $this);
    }

    public function run(string $entryfile, $render = 1)
    {
        // + | --------------------------------------------------------------------------
        // + | configure engine to start
        // + |
        $this->no_init_environment = $this->configs->isTemp;
        igk_environment()->no_lib_cache = 1;
        igk_environment()->no_db_route = 1;
        igk_configs()->no_db_route = 1;
        igk_register_service('balafon', 'cli', new BalafonCLIService);
        IGKApp::StartEngine($this);
        return \IGK\System\Console\App::Run($this->command, $this->basePath, $this->configs);
    }

    /**
     * return primary command array
     * @return array
     */
    public function getPrimaryCommand(): array
    {
        // + |--------------------------------------------------------
        // + | balafon primary command
        // + |        
        $command = [
            "--wdir" => [null, __("set startup working directory") . "\n--wdir:path_to_working_dir"],
            "--debug" => [
                function ($v, $command) {
                    if (is_array($command))
                        $command["debug"] = true;
                    igk_debug(1);
                    igk_environment()->querydebug = 1;
                }, ['desc' => __("flag: enable debug"), 'category' => "flag"]
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
                }, [
                    "desc" => "Lock site. put it in maintenance mode."
                ]
            ],
            "--site:unlock" => [
                function ($v, $command) {
                    $command->exec = function ($command, $dir = null) {
                        $dir = $dir ?? IGK_BASE_DIR;
                        \IGK\Helper\MaintenanceHelper::UnlockSite($dir);
                    };
                }, [
                    "desc" => "Unlock site."
                ]
            ],
            "--set:maintenance" => [
                function () {
                    $dir = igk_io_basedir();
                    Logger::info("maintenance site " . $dir);
                    if (file_exists($file = $dir . "/" . \IGK\Helper\MaintenanceHelper::lockFile)) {
                        Logger::info("unlock site ...");
                        // in maintenace mode
                        \IGK\Helper\MaintenanceHelper::UnlockSite($dir);
                        // @unlink($dir."/.htaccess");
                        // @unlink($dir."/index.php");
                        // @rename($dir."/.lock.index.php", $dir."/index.php");
                        // @rename($dir."/.lock.htaccess", $dir."/.htaccess");
                        file_exists($file) && @unlink($file);
                    } else {
                        // put in maintence mode
                        Logger::info("lock site ...");
                        \IGK\Helper\MaintenanceHelper::LockSite($dir);
                    }
                    Logger::success("maintenance");
                    igk_exit();
                }, [
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

            "--make:page" => [
                function ($v, $command) {
                    //igk_wl("v ", $v);
                    $command->exec = function ($command, $ctrl = null, $page = null) use ($v) {
                        if (empty($ctrl)) {
                            $command->app->showHelp($v);
                            return -1;
                        }

                        $ctrl = str_replace("/", "\\", $ctrl);
                        Logger::info("make page:" . $ctrl);
                        if (($c = igk_getctrl($ctrl, false)) || ($c = $ctrl::ctrl())) {
                            $path = "Pages/" . ucfirst($page) . "Page";

                            if (!($t = $c->resolveClass($path))) {
                                $name = ucfirst($page);
                                if (strrpos($name, "Page", 4) === false) {
                                    $name .= "Page";
                                }
                                $builder = new PHPScriptBuilder();
                                $builder
                                    ->author($command->app->getConfigs()->get("author", IGK_AUTHOR))
                                    ->type("class")
                                    ->file("$path.php")
                                    ->name($name)
                                    ->extends(ControllerTask::class)
                                    ->implements()
                                    ->desc(igk_getv($command->options, "--desc"))
                                    ->defs("public function index(){\n}")
                                    ->namespace($c::ns("Pages"));
                                $file = $c::classdir() . "/{$path}.php";
                                igk_io_w2file($file, $builder->render());
                                Logger::success("complete page: " . $path);
                                Logger::info("file: " . $file);
                            }
                            return 200;
                        } else {
                            Logger::danger("failed : controller not found");
                        }
                    };
                }, [
                    "desc" => __("make a new page. controller name [options]"),
                    "help" => function () {
                        Logger::danger("\n--make:pag [options] ctrl pagename\n");
                        Logger::print("make a controller's page\n");
                    }
                ],
                "make"
            ],
            "--run" => [
                function ($v, $command = null) {
                    $command->exec = function ($command, ?string $file = null) {
                        if (empty($file)) {
                            Logger::danger(__("args: require file"));                            
                            return -1;
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


                        if ($id = intval(igk_getv($command->options, '--user'))) {
                            if ($user = \IGK\Models\Users::Get('clId', $id)) {
                                $ctrl::login($user, null, false);
                            }
                        }
                        $args = ViewEnvironmentArgs::CreateContextViewArgument($ctrl, __FILE__, 'balafon');
                        $params = array_slice(func_get_args(), 2);
                        $args->params = &$params;
                     
                        try {
                            if (file_exists($file)) { 
                                $result = SysUtils::Include($file, array_merge([
                                    "ctrl" => $ctrl,
                                    "user" => $user,
                                    "command"=>$command
                                ], (array)$args));

                                if ($result) {
                                    Logger::print("--- response ---- ");
                                    if (is_string($result)) {
                                        Logger::print($result);
                                    } else {
                                        var_dump($result);
                                    }
                                }
                            } else {
                                Logger::danger(__("[ run file ] file not found"));
                            }
                        } catch (Throwable $ex) {
                            $trace = $ex->getTrace()[0];
                            $TAG = ($ex instanceof \IGKException) ? '[BLF]' : '[EXTERNAL]';
                            Logger::danger(
                                sprintf("%s - ", $TAG) .
                                    implode(':', [
                                        $ex->getMessage() . " \nAt: " .
                                        igk_getv($trace,  'file'),
                                        igk_getv($trace, 'line'),
                                    ])
                            );
                            return false;
                        }
                        return 0;
                    };
                },
                [
                    "desc" => __("run script by loading"),
                    "help" => function () {
                        Logger::info(implode(
                            "\n",
                            [
                                "\n--run [options] [dbcommand] scriptfile\n--controller:[targetController]",
                                "--user:id\r\t\t\tglobal user to use", 
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

                if (!$command->exec) {
                    $command->exec = function () {
                        Logger::info("Core Version");
                        echo IGK_VERSION . "\n";
                        Logger::info("CLI - Version");
                        Logger::print(App::version);
                        echo "\n";
                        return 200;
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
                        $command->app->showHelp();
                        return 200;
                    };
                }
                return 200;
            }, ["desc" => "show help or activate help option for a command"], "info"],
        ];
        return $command;
    }

    public static function BindCommandController(BaseController $ctrl, ?Users $user= null)
    {
        igk_environment()->set(IGKEnvironment::CURRENT_CTRL, $ctrl);
        igk_environment()->set(IGKEnvironment::CURRENT_USER, $user);
        $ctrl->register_autoload();
    }
    /**
     * get working dirctory
     * @return ?string
     */
    public function getWorkingDir(){
        return $this->basePath;
    }
}
