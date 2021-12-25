<?php

namespace IGK\System\Console;

use Exception;
use IGK\Controllers\ControllerTask;
use IGK\Helper\IO;
use IGK\Models\Crons;
use IGK\System\Configuration\XPathConfig;
use IGK\System\Console\Commands\DbCommand;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\Process\CronJobProcess;
use IGKApp;
use IGKApplicationBase;
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

    public function bootstrap()
    {
        if ($this->basePath === null) {
            $this->basePath = getcwd();
        }
        $bdir = $this->basePath;

        defined('IGK_FRAMEWORK_ATOMIC') || define('IGK_FRAMEWORK_ATOMIC', 1);
        $_SERVER["SERVER_NAME"] = "BalafonCLI";
        igk_server()->SERVER_NAME = $_SERVER["SERVER_NAME"];
        
        try{
        if (file_exists($configFile = $bdir . "/balafon.config.xml")) {
            $c = igk_conf_load_file($configFile, "balafon");
            $this->configs = new XPathConfig($c);
            $c = $this->configs->get("env");

            if ($c) {
                if (!is_array($c))
                    $c = [$c];
                foreach ($c as $env) {
                    defined($env->name) || define(
                        $env->name,
                        preg_match("/_DIR$/", $env->name) ? realpath($env->value) :
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

    } catch(Exception $ex){
        igk_wln_e("try to load .... ");
    }
        defined('IGK_APP_DIR') || define("IGK_APP_DIR", $wd);
        defined('IGK_BASE_DIR') || define("IGK_BASE_DIR", $wd);
        // setup the log folder
        if (!defined('IGK_LOG_FILE') && ($logFolder  = $this->configs->logFolder)) {
            define('IGK_LOG_FILE', $logFolder . "/." . IGK_TODAY . ".cons.log");
        }
        igk_loadlib(dirname(__FILE__) . "/Commands");
        date_default_timezone_set('Europe/Brussels');
        // IGKApp::InitSingle(); 
        if (defined('IGK_DOCUMENT_ROOT'))
            igk_server()->IGK_DOCUMENT_ROOT = realpath(constant('IGK_DOCUMENT_ROOT'));


        $this->library("zip");
        //igk_hook("console::app_boot", $this);
    }

    public function run(string $entryfile, $render = 1)
    {
        IGKApp::StartEngine($this);
        return \IGK\System\Console\App::Run($this->command, $this->basePath, $this->configs);
    }

    public function getPrimaryCommand()
    {

        //--------------------------------------------------------
        // + define basics balafon command
        //
        $command = [
            "--debug" => [
                function ($v, $command) {
                    $command->debug = true;
                    igk_debug(1);
                }, __("enable debug")
            ],
            "--set:default_controller" => [function ($v, $command) {
                $command->exec = function ($command, $name = "") {
                    if (!empty($name) && class_exists($name)) {
                        igk_app()->Configs->default_controller = $name;
                        igk_save_config(true);
                        Logger::success(__("controller changed to {0}", $name));
                    }
                };
            }, __("set default controller")],
            "--get:sysconfigs" => [
                function ($v, $command) {
                    $command->exec = function ($command, $pattern = null) {
                        $tab = igk_app()->Configs->getEntries();
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

            "--set:sysconfigs" => [
                function ($v, $command) {
                    DbCommand::Init($command);
                    $command->exec = function ($command, $name = null, $value = null) {

                        if (!empty($name)) {
                            igk_app()->Configs->$name = $value;
                            igk_save_config(true);
                            Logger::print("");
                            Logger::success("configuration changed");
                            Logger::print("");
                        }
                    };
                },
                [
                    "desc" => __("set configuration. name value"),
                    "help" => function () {
                        Logger::print("\nusage : --set:sysconfig property value\n");
                    }
                ]
            ],

            "--db:seed" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = null, $class = null) {
                    DbCommand::Init($command);

                    if ($ctrl) {
                        if ($c = igk_getctrl($ctrl, false)) {

                            $inf = get_class($c);
                            if (!empty($class))
                                $inf .= "::" . $class;
                            Logger::print("seed..." . $inf . " " . igk_environment()->querydebug);
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
                    DbCommand::Init($command);
                    if ($c = igk_getctrl($ctrl, false)) {
                        $c = [$c];
                    } else {
                        $c = igk_sys_getall_ctrl();
                    }
                    foreach ($c as $t) {
                        Logger::info("migrate..." . get_class($t));
                        if ($t::migrate()) {
                            Logger::success("migrate:" . get_class($t));
                        }
                    }
                };
            }, __("migrate your database"), "db"],
            "--db:initdb" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = "") {
                    $c = null;
                    DbCommand::Init($command);

                    if (!empty($ctrl)) {
                        if (!($ctrl = igk_getctrl($ctrl, false))) {
                            Logger::danger("no countroller found");
                            return -1;
                        }
                        $c = [$ctrl];
                    } else {
                        $c = igk_app()->getControllerManager()->getControllers();
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
                "desc" => __("initialize database"),
                "help" => function () {
                    Logger::print("Init Database");
                    Logger::print("--db:initdb [options] controller");
                }
            ], "db"],
            "--dbsys:initdb" => [function ($v, $command) {
                $command->exec = function ($command) {
                    DbCommand::Init($command);
                    if ($c = igk_getctrl(IGK_SYSDB_CTRL, false)) {
                        $c::Invoke($c, 'initDb');
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
                                if (strrpos($name, "Page", 4)===false){
                                    $name .="Page";
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
                                Logger::info("file: ".$file);
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
            "--run" => [function ($v, $command) {
                $command->exec = function ($command, $file) {
                    try {
                        if (file_exists($file)) {
                            include($file);
                        } else {
                            igk_wln("file not present");
                        }
                    } catch (Throwable $ex) {
                        Logger::danger(":" . $ex->getMessage());
                        return false;
                    }
                    return 0;
                };
            }, __("run script by loading")],
            "--run:cron" => [function ($v, $command) {
                $command->exec = function ($command, $ctrl = null) {
                    try {
                        $ctrl = igk_getctrl($ctrl, false);
                        $condition = ["crons_process" => 0];
                        if ($ctrl) {
                            $condition["crons_class"] = get_class($ctrl);
                        }
                        $rows = Crons::select_all($condition); // 
                        foreach ($rows as $r) {

                            if ($provider = CronJobProcess::GetJobProcessProvider($r->crons_script)) {
                                if ($provider->exec($r->crons_name, json_decode($r->crons_options), $ctrl)) {
                                    $r->crons_process = 1;
                                    Logger::success("success :" . $r->crons_name);
                                } else {
                                    $r->crons_process = -1;
                                    Logger::danger("failed :" . $r->crons_name);
                                }
                            }
                            $r->update();
                        }
                    } catch (Throwable $ex) {
                        Logger::danger(":" . $ex->getMessage());
                        return false;
                    }
                    return 0;
                };
            }, __("run cron's script")],

            "-v, --version" => [function ($arg, $command) {
                $command->exec = function () {
                    echo IGK_VERSION . "\n";
                    return 200;
                };
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
                function ($arg, $command) {
                    $file = getcwd() . "/balafon.config.xml";
                    if (file_exists($file)) {
                        Logger::print("already initialized");
                        return;
                    }
                    $init_data = igk_create_xmlnode("balafon");
                    $config = new \IGK\System\Console\AppConfigs();
                    $config->author = igk_environment()->balafon_author;


                    if (property_exists($command["options"], "--noconfig")) {
                        //
                        // "IGK_DOCUMENT_ROOT",
                        // "IGK_BASE_DIR",
                        // "IGK_PROJECT_DIR",
                        // "IGK_APP_DIR",
                        // "IGK_PACKAGE_DIR",
                        // "IGK_MODULE_DIR",
                        // "IGK_VENDOR_DIR",
                        // "IGK_BASE_URI",

                        $init_data->env()->setAttributes(["name" => "IGK_BASE_URI", "value" => "//localhost"]);
                        $init_data->env()->setAttributes(["name" => "IGK_DOCUMENT_ROOT", "value" => "src/public"]);
                        $init_data->env()->setAttributes(["name" => "IGK_BASE_DIR", "value" => "src/public"]);
                        $init_data->env()->setAttributes(["name" => "IGK_APP_DIR", "value" => "src/application"]);
                        $init_data->env()->setAttributes(["name" => "IGK_PROJECT_DIR", "value" => "src/application/Projects"]);
                        $init_data->env()->setAttributes(["name" => "IGK_PACKAGE_DIR", "value" => "src/application/Packages"]);
                        $init_data->env()->setAttributes(["name" => "IGK_MODULE_DIR", "value" => "src/application/Packages/Modules"]);
                        $init_data->env()->setAttributes(["name" => "IGK_VENDOR_DIR", "value" => "src/application/Packages/vendor"]);
                    } else {
                        $config->init($init_data);
                    }
                    $opts = igk_xml_create_render_option();
                    $opts->Indent = true;
                    igk_io_w2file($file, $init_data->render($opts));
                },
                ["desc" => "init configuration"], ""
            ]
        ];


        return $command;
    }
}
