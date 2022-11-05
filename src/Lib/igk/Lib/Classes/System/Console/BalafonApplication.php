<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonApplication.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerTask;
use IGK\Helper\IO; 
use IGK\System\Configuration\XPathConfig;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Database\DbUtils;
use IGK\System\Html\Dom\HtmlCtrlNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\File\PHPScriptBuilder; 
use IGKApp;
use IGKApplicationBase;
use IGKConstants;
use IGKModuleListMigration;
use stdClass;
use Throwable;
use function igk_resources_gets as __;

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

    public static function FilterArgs($a)
    {
        if (strpos($a, "--wdir:") === 0) {
            $g = explode(":", $a);
            if (is_dir($g[1]) || igk_io_createdir($g[1]))
                chdir($g[1]);
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
    private static function ResolvPathConstant($dir, $value){
        return IO::ResolvPathConstant($dir, $value);
    }
    /**
     * get top level configuration files
     * @param string $bdir 
     * @return string|null|void 
     */
    private static function GetTopLevelConfigFile(string $bdir){
        /// TASK : GET TOP LEVEL CONFIG FILE
        while(!empty($bdir)){
            if (file_exists($configFile = $bdir . "/".AppConfigs::ConfigurationFileName)){
                return $configFile;
            }
            $b = $bdir;
            $bdir = dirname($bdir);
            if ($b==$bdir){
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
            $this->basePath = igk_getv($_SERVER, "PWD", getcwd()) ?? die("can't get current working directory");
        }
        defined('IGK_FRAMEWORK_ATOMIC') || define('IGK_FRAMEWORK_ATOMIC', 1);
        $wd = $bdir = $this->basePath;        
        igk_server()->SERVER_NAME = $_SERVER["SERVER_NAME"]  = "BalafonCLI";
 
  
        $configFile = self::GetTopLevelConfigFile($bdir);

        // igk_wln_e("loading config file : ", $configFile, $bdir, $_SERVER, getcwd());
        try {
            if (!empty($configFile) && file_exists($configFile)) {                
                $wd = dirname($configFile);
                $c = igk_conf_load_file($configFile, "balafon");   
                $this->configs = new XPathConfig($c);
                $c = $this->configs->get("env");

                // TODO: PRELOAD ENTRY DOCUMENT

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
                $wd = igk_environment()->get("workingdir", getcwd());
                register_shutdown_function(function () use ($wd) {
                    if (strstr($wd, sys_get_temp_dir())) {
                        IO::RmDir($wd);
                    }
                });
            }
        } catch (Exception $ex) {
            igk_wln_e("boostrap-application error : .... " . $ex->getMessage());
        }        
        defined('IGK_APP_DIR') || define("IGK_APP_DIR", $wd);
        defined('IGK_BASE_DIR') || define("IGK_BASE_DIR", $wd); 
        // setup the log folder
        if (!defined('IGK_LOG_FILE') && ($logFolder  = $this->configs->logFolder)) {
            define('IGK_LOG_FILE', $logFolder . "/." . igk_environment()->getToday(). ".cons.log");
        }
        igk_loadlib(dirname(__FILE__) . "/Commands");
        date_default_timezone_set(IGKConstants::DEFAULT_TIME_ZONE);
        // IGKApp::InitSingle(); 
        if (defined('IGK_DOCUMENT_ROOT'))
            igk_server()->IGK_DOCUMENT_ROOT = realpath(constant('IGK_DOCUMENT_ROOT'));

            
        $this->library("zip");
        $this->library("mysql");
        $this->library("curl");
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
        IGKApp::StartEngine($this);
        return \IGK\System\Console\App::Run($this->command, $this->basePath, $this->configs);
    }

    public function getPrimaryCommand()
    {

        //--------------------------------------------------------
        // + define basics balafon command
        //
        $command = [
            "--wdir" => [null, __("set startup working directory") . "\n--wdir:path_to_working_dir"],
            "--debug" => [
                function ($v, $command) {
                    if (is_array($command))
                        $command["debug"] = true;
                    igk_debug(1);
                    igk_environment()->querydebug = 1;
                }, __("enable debug")
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
                        if ($cnf = igk_lib_configs()){ 
                            $cnf->$path = $value;
                            $cnf->storeConfig(); 
                            Logger::success("Change configs");
                        }
                        Logger::print("");
                    };
                },
                __("set system configuration")
            ],
            "--site:lock" =>[
                function($v, $command){
                    $command->exec = function($command, $dir=null){
                        $dir = $dir ?? IGK_BASE_DIR;
                        \IGK\Helper\MaintenanceHelper::LockSite($dir);
                    };
                }, [
                    "desc"=>"Lock site. put it in maintenance mode."
                ]
            ],
            "--site:unlock"=>[
                function($v, $command){
                    $command->exec = function($command, $dir=null){
                        $dir = $dir ?? IGK_BASE_DIR;
                        \IGK\Helper\MaintenanceHelper::UnlockSite($dir);
                    };
                },[
                    "desc"=>"Unlock site."
                ]
            ],
            "--set:maintenance"=>[function(){
                $dir = igk_io_basedir();
                Logger::info("maintenance site ".$dir);
                if (file_exists($file = $dir."/".\IGK\Helper\MaintenanceHelper::lockFile)){
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
            },[
                "desc"=>"toggle maintenance mode"
            ]
            ],
            "--set:sysconfigs" => [
                function ($v, $command) {
                    DbCommandHelper::Init($command);
                    $command->exec = function ($command, $name = null, $value = null) {
                        Logger::print($name, $value);
                        if (!empty($name)) {
                            if (strpos($name, "=")!== false){
                                $g = array_map('trim', explode("=", $name));
                                $name = $g[0];
                                $value = $g[1];
                            }
                            igk_configs()->$name = $value;
                            igk_save_config(true);                            
                            Logger::success("configuration changed: ".$name);
                            
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

            "--db:seed" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = null, $class = null) {
                    DbCommandHelper::Init($command);
                    // Transformo to namespace class
                    if ($ctrl) {
                        $ctrl = str_replace("/", "\\", $ctrl);
                    }


                    if ($ctrl) {
                        if ($c = igk_getctrl($ctrl, false)) {
                            $inf = get_class($c);
                            if (!empty($class))
                                $inf .= "::" . $class;


                            Logger::print("seed... " . $inf . " query debug: " . igk_environment()->querydebug);
                            $c::register_autoload();

                            // igk_wln(
                            //     __FILE__.":".__LINE__, 
                            //     class_exists(\com\igkdev\app\llvGStock\MappingService::class));

                            $c::seed($class);
                            Logger::success("seed complete");
                            return 1;
                        } else {
                            Logger::danger("controller [$ctrl] not found");
                        }
                    } else {
                        $c = igk_sys_getall_ctrl();
                        foreach ($c as $t) {
                            $t::register_autoload();
                            Logger::info("seed:" . get_class($t));
                            if ($t::seed()) {
                                Logger::success("seed:" . get_class($t));
                            }
                        }
                    }
                    return -1;
                };
            }, __("seed your database with data"), "db"],
            "--db:migrate" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = null) {
                    DbCommandHelper::Init($command);
                    if (!is_null($ctrl ) && ($c = igk_getctrl($ctrl, false))) {
                        $c = [$c];
                    } else {
                        $c = igk_sys_getall_ctrl();

                        if (($ctrl === null) && ($modules = igk_get_modules())) {
                            $list = array_filter(array_map(function ($c, $k) {
                                if ($mod = igk_get_module($k)) {
                                    return $mod;
                                }
                            }, $modules, array_keys($modules)));

                            $c = array_merge($c, [IGKModuleListMigration::Create($list)]);
                        }
                    }
                    foreach ($c as $t) {
                        $cl = get_class($t);
                        Logger::info("migrate..." . $cl);
                        if ($t::migrate()) {
                            Logger::success("migrate:" . $cl);
                        }else {
                            Logger::danger("failed to migrate : ". $cl);
                        }
                    }
                    // migrate module 

                };
            }, __("migrate your database"),
                 "db"],
            "--db:initdb" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = "") {
                    $c = null;
                    DbCommandHelper::Init($command); 
                    if (!empty($ctrl)) {
                        if (!($c = igk_getctrl($ctrl, false))) {
                            Logger::danger("no controller found: " . $ctrl);
                            return -1;
                        }
                        $c = [$c];
                    } else {
                        $c = igk_app()->getControllerManager()->getControllers();
                        usort($c, DbUtils::OrderController);
                        if ($b = IGKModuleListMigration::CreateModulesMigration()){
                            $c = array_merge($c, [$b]); 
                        } 
                    }
                    if ($c) {
                        foreach ($c as $m) {
                            if ($m->getCanInitDb()) {
                                Logger::info("init: " . get_class($m));
                                $m::initDb();
                                Logger::success("complete: " . get_class($m));
                            }
                        }
                        return 1;
                    }
                    return -1;
                };
                return 0;
            }, [
                "desc" => __("init databases"),
                "help" => function () {
                    Logger::print("Init Database");
                    Logger::print("--db:initdb [options] controller");
                }
            ], "db"],
            "--dbsys:initdb" => [function ($v, $command) {
                $command->exec = function ($command) {
                    DbCommandHelper::Init($command);
                    if ($c = igk_getctrl(IGK_SYSDB_CTRL, false)) {
                        Logger::info("init system's database");
                        $c::Invoke($c, 'initDb');
                        Logger::success("done"); 
                    }
                    return 0;
                };
            }, __("initialize system database"), "db"],
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

                            if (!($t = $c::resolvClass($path))) {
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
            "--run" => [function ($v, $command=null) {
                $command->exec = function ($command, ?string $file=null) {
                    if (empty($file)){
                        Logger::danger(__("args: require file"));
                        return false;
                    }
                    try {
                        if (file_exists($file)) {
                            include($file);
                        } else {
                            Logger::danger(__("[ run file ] file not found"));
                        }
                    } catch (Throwable $ex) {
                        Logger::danger(":" . $ex->getMessage());
                        return false;
                    }
                    return 0;
                };
            },            
            [
                "desc"=>__("run script by loading"),
                "help"=>function(){
                    Logger::info("\n--run [options] scriptfile\n");
                }
            ]
            ],
            // "--compile" => [function ($v, $command=null) {
            //     $command->exec = function ($command, ?string $file=null, ?string $controller=null) {
            //         if (empty($file)){
            //             Logger::danger(__("args: require file"));
            //             return false;
            //         }
            //         try {
            //             if (file_exists($file)) {
            //                 $options = [
            //                     "engine"=>"balafonCompiler",
            //                     "context"=>"xml"
            //                 ];
            //                 if ($controller){
            //                     $controller = igk_getctrl($controller);
            //                 }
            //                 if ($controller){
            //                     $t = new HtmlCtrlNode($controller, "div");
            //                 }else{
            //                     $t = new HtmlNode("div");
            //                 }
            //                 // include($file);
            //                 $s = $t->render();
            //                 echo "<?php";
            //                 echo $s;
            //             } else {
            //                 Logger::danger(__("[ compile file ] file not found"));
            //             }
            //         } catch (Throwable $ex) {
            //             Logger::danger(":" . $ex->getMessage());
            //             // igk_show_exception_trace($ex);
            //             array_map(function($t){
            //                 echo $t["file"].":".$t["line"]."\n";
            //             },$ex->getTrace());
            //             return false;
            //         }
            //         return 0;
            //     };
            // }, [
            //     "desc"=>"compile view file",
            //     "help"=>function(){
            //         Logger::info('usage : balafon --compile file [controller]');
            //     }
            // ]],
            "--run:tac"=>[function(){
                // terminal action command
                Logger::success("terminal action command \n");
                $c = new TerminalActionCommand;
                return $c->run();   
            }, [
                "desc"=>__("terminal action command"),
                "help"=>function(){
                    Logger::info('terminal action command');
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
            }, __("run cron's script")],
            "--db:query" => [function ($v, $command) {
                $command->exec = function ($command, $model = null) {
                    $model = igk_ns_name($model);
                    if (!class_exists($model)){
                        Logger::danger("class not exists");
                        return 0;
                    }   
                    foreach($model::select_fetch() as $o){
                        igk_wln($o->to_json());
                    }                
                };
            }, __("run cron's script")],

            "-v, --version" => [function ($arg, $command) {
            
                if (!$command->exec){
                    $command->exec = function () {
                        echo IGK_VERSION . "\n";
                        return 200;
                    };
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
            "--init" => [
                // + | --------------------------------------------------------------
                // + | initialize environment configuration
                function ($arg, $command) {
                    $file = getcwd() . "/" .AppConfigs::ConfigurationFileName;
                    $options = igk_getv($command, "options") ?? new stdClass();
                    if (file_exists($file) && !property_exists($options, "--force")) {
                        Logger::print("already initialized");
                        return;
                    }
                    $init_data = igk_create_xmlnode("balafon");
                    $config = new \IGK\System\Console\AppConfigs();
                    $config->author = igk_environment()->balafon_author;


                    if (property_exists($options, "--noconfig")) {
                        $primary = property_exists($options, "--primary");
                        $app_dir = $primary ? "./" :  "src/application";
                        $public_dir = $primary ? "./" : "src/public";
                   

                        $init_data->env()->setAttributes(["name" => "IGK_BASE_URI", "value" => "//localhost"]);
                        $init_data->env()->setAttributes(["name" => "IGK_DOCUMENT_ROOT", "value" => $public_dir]);
                        $init_data->env()->setAttributes(["name" => "IGK_BASE_DIR", "value" => $public_dir]);
                        $init_data->env()->setAttributes(["name" => "IGK_APP_DIR", "value" => $app_dir]);
                        $sapp_dir = $app_dir == "./" ? "": $app_dir;
                        $init_data->env()->setAttributes(["name" => "IGK_PROJECT_DIR", "value" => $sapp_dir."/Projects"]);
                        $init_data->env()->setAttributes(["name" => "IGK_PACKAGE_DIR", "value" => $sapp_dir."/Packages"]);
                        $init_data->env()->setAttributes(["name" => "IGK_MODULE_DIR", "value" => $sapp_dir."/Packages/Modules"]);
                        $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" => $sapp_dir."/Packages/vendor"]);
                        igk_io_createdir($app_dir);
                        igk_io_createdir($public_dir);
                        if (!file_exists($lib = $app_dir."/Lib/igk")){
                            igk_io_createdir(dirname($lib)); 
                            symlink(IGK_LIB_DIR, $lib);
                        }
                    } else {
                        $config->init($init_data);
                    }
                    $opts = HtmlRenderer::CreateRenderOptions();
                    $opts->Indent = true;
                    igk_io_w2file($file, $init_data->render($opts));
                },
                ["desc" => "init configuration"], ""
            ]
        ];
        return $command;
    }
}
