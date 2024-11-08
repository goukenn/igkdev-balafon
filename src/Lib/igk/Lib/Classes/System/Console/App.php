<?php
// @author: C.A.D. BONDJE DOUE
// @filename: App.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console;

use IGK\System\Configuration\XPathConfig;
use Closure;
use Exception;
use IGK\Helper\IO;
use IGK\System\Console\Commands\InitCommand;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGKAppType;
use IGKException;
use stdClass;
use Throwable;

///<summary>represent Balafon CLI console Application</summary>
class App
{
    const GREEN = "\e[1;32m";
    const GRAY = "\e[1;90m";
    const GRAY_I = "\e[3;90m";
    const YELLOW = "\e[0;33m";
    const YELLOW_B = "\e[1;33m";
    const YELLOW_I = "\e[3;33m";
    const RED = "\e[0;31m";
    const RED_B = "\e[1;31m";
    const BLUE = "\e[0;34m";
    const BLUE_B = "\e[1;34m";
    const BLUE_I = "\e[3;34m";
    const PURPLE = "\e[3;35m";
    const AQUA = "\e[3;36m";
    const SHA_INDIGO = "\e[38;2;153;35;89m";
    const END = "\e[0m";
    const TEMP_DIR_NAME = '.balafon-caches';

    const GroupIndex = 2;
    /*
     * application version
     * @var string
     */
    const version = "1.0.1";
    /**
     * available command
     * @var mixed
     */
    public $command = [];
    /**
     * setup the base command
     * @var mixed
     */
    protected $_basePath;

    /**
     * store application configuration
     */
    protected $_configs;

    public function getConfigs()
    {
        return $this->_configs;
    }
    /**
     * context
     * @return ?string 
     */
    public function getContext()
    {
        if ($cons = igk_environment()->console) {
            return $cons->type;
        }
        return null;
    }

    /**
     * return application level base path 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetAppBasePath(): string
    {
        if ($app = igk_app()) {
            $cons_app = $app->getApplication();
            if ($cons_app instanceof BalafonApplication) {
                return $cons_app->basePath;
            }
        }
        return getcwd();
    }
    /**
     * run application command line
     * @param array $command default commands
     * @param string $basePath base path
     * @param XPathConfig $configs loaded configuration
     * @return void 
     * @throws Exception 
     */
    public static function Run($command = [], string $basePath = null, XPathConfig $configs = null)
    {
        $app = (new static);
        if ($basePath === null) {
            $basePath = getcwd();
        }
        // + |init balafon temporary directory  
        $wdir = sys_get_temp_dir() . "/" . self::TEMP_DIR_NAME;

        !defined('IGK_LOG_FILE') && define('IGK_LOG_FILE', $wdir . "/logs/." . igk_environment()->getToday() . "/cons.log");

        if (!IO::CreateDir($wdir)) {
            Logger::danger("can't create tempory directory for command storage");
        }

        register_shutdown_function(function () use ($wdir) {
            if (!($error = error_get_last())) {
                IO::RmDir($wdir);
            } else {
                // igk_environment()->isDev() && print_r($error);
                error_clear_last();
            }
        });

        igk_environment()->NO_DB_LOG = 1;
        igk_environment()->NO_SESSION = 1;
        igk_environment()->set("workingDir", $wdir);
        $app->_basePath = $basePath;
        $app->_configs = $configs;
        Logger::SetLogger(new ConsoleLogger($app));
        $app->boot();

        if (!file_exists(AppCommandConstant::GetCacheFile())) {
            Logger::warn("missing cache files");
            $v_cmd = self::CreateCommand($app);
            Logger::info("init command");
            $cmd = new InitCommand();
            $cmd->exec($v_cmd);
            unset($v_cmd);
            Logger::success('init command.');
        }

        $command_args = AppCommand::GetCommands($app);

        if ($command_args) {
            foreach ($command_args as $c) {
                $callbable = null;
                if ($c instanceof AppCommand) {
                    $callbable = [$c, "run"];
                } else {
                    $callbable = $c->callable;
                }
                $command[$c->command] = [
                    $callbable,
                    $c->desc,
                    $c->category
                ];
            }
        }
        $handle = [];
        foreach ($command as $n => $b) {
            if (count($c = explode(',', $n)) > 1) {
                array_map(function ($i) use (&$handle, $b) {
                    $handle[trim($i)] = $b;
                }, $c);
            } else {
                $handle[trim($n)] = $b;
            }
        }
        ksort($command);
        $app->command = $command;
        $tab = array_slice(igk_server()->argv, 1);
        // + | before execute a command move the working directory to server PWD
        if (isset($_SERVER['IGK_COMMAND_PWD'])) {
            chdir($_SERVER['IGK_COMMAND_PWD']);
            unset($_SERVER['IGK_COMMAND_PWD']);
        }
        return self::Exec($app, $tab);
    }
    /**
     * expose start base path
     * @return mixed 
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }
    /**
     * execute argument
     * @param App $app 
     * @param array $args 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function Exec(App $app, array $args)
    {
        $command = $app->command;
        $v_cnf = $app->getConfigs();
        $v_basePath = $app->_basePath;

        $app = new static();
        // + pass new configuration .
        $app->_basePath = $v_basePath;
        $app->_configs = $v_cnf;

        if ($command_args = AppCommand::GetCommands($app)) {
            foreach ($command_args as $c) {
                $callbable = null;
                if ($c instanceof AppCommand) {
                    $callbable = [$c, "run"];
                } else {
                    $callbable = $c->callable;
                }
                $command[$c->command] = [
                    $callbable,
                    $c->desc,
                    $c->category
                ];
            }
        }

        $handle = [];
        foreach ($command as $n => $b) {
            if (count($c = explode(',', $n)) > 1) {
                array_map(function ($i) use (&$handle, $b) {
                    $handle[trim($i)] = $b;
                }, $c);
            } else {
                $handle[trim($n)] = $b;
            }
        }
        ksort($command);
        $app->command = $command;

        $tab = $args;
        $command = igk_createobj();
        $command->app = $app;
        $command->command = $tab;
        $command->exec = null;
        $command->storage = array();
        $command->waitForNextEntryFlag = false;
        $command->options = new stdClass();
        $action = null;
        $args = [];
        $show_help = true;
        $split = false;

        foreach ($tab as $id => $v) {
            if (!$split && $v == '--') {
                $split = true;
                continue;
            }
            if ($split) {
                $args[] = $v;
                continue;
            }
            if ($command->waitForNextEntryFlag) {
                $action($v, $command, []);
                $command->waitForNextEntryFlag = false;
            }
            if (isset($handle[$v])) {
                $action = is_callable($handle[$v]) ? $handle[$v] : $handle[$v][0];
                $action($v, $command, implode(":", array_slice($c, 1)));
            } else {
                $c = explode(":", $v);
                $v_ts =  implode(":", array_slice($c, 1));
                if (isset($handle[$c[0]])) {
                    if (isset($handle[$v])) {
                        $action = is_callable($handle[$v]) ? $handle[$v] : $handle[$v][0];
                        $action($v, $command, $v_ts);
                    } else {
                        $command->options->{$c[0]} = $v_ts;
                    }
                } else {

                    if ($c[0][0] == "-") {
                        if (!property_exists($command->options, $c[0])) {
                            $command->options->{$c[0]} = $v_ts;
                        } else {
                            if (!is_array($command->options->{$c[0]})) {
                                $command->options->{$c[0]} = [$command->options->{$c[0]}];
                            }
                            $command->options->{$c[0]}[] = $v_ts;
                        }
                        unset($command->command[$id]);
                    } else
                        $args[] = $v;
                }
            }
        }

        try {
            $action = $command->exec; //($v, $command, implode(":", array_slice($c,1)));
            if ($action) {
                if (property_exists($command->options, "--help")) {
                    $targs = [$command->command[0], $command];
                    foreach ($args as $v) {
                        $targs[] = $v;
                    }

                    return call_user_func_array([$app, 'showHelp'], $targs);
                }
                return $action($command, ...$args);
            } else {
                if ($tab) {
                    Logger::danger("blf-cli: no action found - " . json_encode($tab));
                }
            }
        } catch (Exception $ex) {
            $app->print('--');
            $app->print(self::Gets(self::RED, "BALAFON Command Error : ") . $ex->getMessage());
            $app->print('--');
            if (igk_is_debug() && !igk_environment()->NoConsoleLogger) {
                igk_show_exception_trace($ex->getTrace(), 0 );
            }
            igk_exit(1, -1000);
        } catch (Throwable $ex) {
            Logger::danger("error: throw: " . $ex->getMessage());
            Logger::info($ex->getFile() . ":" . $ex->getLine());
            if (igk_is_debug() && !igk_environment()->NoConsoleLogger) {
                igk_show_exception_trace($ex->getTrace(), 0);
            } else {
                igk_exit(1, -100);
            }
            $show_help = false;
        }
        if ($show_help)
            $app->showHelp();
    }
    protected function boot()
    {
        igk_hook("console::app_boot", $this);
    }
    public function print(...$text)
    {
        $cl = Logger::GetColorizer();
        foreach ($text as $s) {
            if ($cl) {
                $s = $cl($s);
            }
            echo $s . PHP_EOL;
        }
    }
    /**
     * print off - in STDERR
     * @param mixed $text 
     * @return void 
     */
    public function print_off(...$text)
    {
        foreach ($text as $s) {
            fwrite(STDERR, $s . PHP_EOL);
        }
    }
    public function print_debug(...$text)
    {
        if (igk_is_debug())
            $this->print_off(...$text);
    }
    public function showHelp($command = null)
    {
        if (!empty($command) && ($cmd = $this->command[$command])) {
            if (is_array($inf = igk_getv($cmd, 1))) {
                $cf = $inf["help"];
                if ($cf instanceof Closure) {
                    $fc = $cf->bindTo($this);
                    $args = array_slice(func_get_args(), 1);
                    call_user_func_array($fc, $args);
                } else {
                    igk_wln($cf);
                }
            } else {
                if ($cmd[0] instanceof Closure) {
                    if (is_array($m = igk_getv($cmd, 1)) && is_callable($fc = igk_getv($m, "help"))) {
                        $fc();
                    }
                } else {
                    if (isset($cmd[0][0])) {
                        $c = func_get_args();
                        $cmd[0][0]->help(...$c);
                    }
                }
            }
            return;
        }
        $this->print("BALAFON CLI-UTILITY");;
        $this->print("Author: C.A.D. BONDJE DOUE");
        $this->print(sprintf("Version:  %s", self::Gets(self::GREEN, self::version)));
        $this->print("");
        $this->print(self::Gets(self::YELLOW, "Usage:"));
        $this->print("\tbalafon [command] [options] [arguments]");
        $this->print("");
        $this->print("");

        $groups = [];
        array_walk($this->command, function ($c, $key) use (&$groups) {
            $cat = null;
            if (is_array($c)) {
                if (($l = igk_getv($c, 1)) && (is_array($l))) {
                    $cat = igk_getv($l, "category");
                }
            }
            $cat = $cat ?? igk_getv($c, self::GroupIndex, "");

            if (!isset($groups[$cat]))
                $groups[$cat] = [];
            $groups[$cat][$key] = $c;
        });

        ksort($groups,  SORT_FLAG_CASE | SORT_STRING);
        $key = key($groups);
        while ((count($groups) > 0) && ($g = array_shift($groups))) {
            if (!empty($key)) {
                Logger::print(App::Gets(App::YELLOW, "groups: " . $key));
                Logger::print("");
            }

            foreach ($g as $n => $c) {
                $s = " " . self::GREEN . $n . "\e[0m \r\t\t\t\t";

                if (is_array($c) && is_array($c[1])) {
                    $s .= (igk_getv($c[1], "desc"));
                } else  if (! ($c instanceof Closure)) {
                    $s .= (igk_getv($c, 1));
                }

                $this->print(implode("\r\n\t\t\t\t", explode("\n", $s)) . "\n");
            }
            $key = key($groups);
        }
        $this->print("");
    }
    public function getLogFolder()
    {
        if ($this->_configs) {
            return $this->_configs->get("logFolder");
        }
    }
    /**
     * get console command string . 
     * @param mixed $color 
     * @param mixed $s 
     * @return string 
     */
    public static function Gets($color, $s)
    {
        return $color . $s . "\e[0m";
    }

    private function __construct() {}

    /**
     * create app command from app
     * @param App $app 
     * @return object 
     */
    public static function CreateCommand(App $app)
    {
        return (object)[
            "app" => $app,
            "options" => (object)[]
        ];
    }

    /**
     * create a command options
     * @return object 
     */
    public static function CreateCommandOptions()
    {
        return (object)[
            "app" => new static,
            "options" => (object)[]
        ];
    }
    /**
     * create new command
     * @param mixed $source 
     * @return object 
     */
    public function createNewCommand($source)
    {
        $o = self::CreateCommand($this);
        $o->source = $source;
        return $o;
    }

    /**
     * get app author
     * @return mixed 
     * @throws IGKException 
     */
    public function getAuthor()
    {
        return $this->getConfigs()->author;
    }
}
