<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InitCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\AppCommand;
use IGK\System\Console\AppCommandConstant;
use IGK\System\Console\AppConstant;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

use IGK\Helper\IO as IGKIO;
use ReflectionClass;
use function igk_resources_gets as __;

class InitCommand extends AppExecCommand
{
    var $command = "--command:init";

    var $desc  = "initialize balafon command cache";

    public function exec($command)
    {
        $t = [
            igk_io_projectdir()
        ];
        \IGK\Helper\SysUtils::ClearCache();
        $commands = [];
        $commands_list = [];
        $ctrls = igk_app()->getControllerManager()->getControllers();
        foreach ($t as $dir) {
            foreach ($ctrls as $c) {
                if (strstr($c->getDeclaredDir(), $dir)) {
                    $cldir = $c::classdir();
                    if (!isset($commands[$cldir])) {
                        $classname = get_class($c);
                        $c::register_autoload();
                        $tab = igk_io_getfiles($cldir . "/Commands", "/\.php$/");
                        if (!$tab)
                            continue;
                        foreach ($tab as $file) {
                            if ($clpath = $c::resolvClass("Commands/" . igk_io_basenamewithoutext($file))) {
                                if ((igk_sys_reflect_class($clpath))->isAbstract() || !is_subclass_of($clpath, AppCommand::class)) {
                                    continue;
                                }
                                if (!isset($commands_list[$classname])) {
                                    $commands_list[$classname] = [];
                                }
                                $commands_list[$classname][] = $clpath;
                                Logger::success("register: " . $clpath);
                            }
                        }
                        $commands[$cldir] = 1;
                    }
                }
            }
        }
        $mod = igk_get_modules();
        if ($mod  && (count($mod) > 0)) {

            foreach ($mod as $k => $v) {
                $mod = igk_get_module($k);
                $ns = $mod->config("entry_NS");
                $dir = $mod->getDeclaredDir() . "/.commands.php";
                if (file_exists($dir)) {
                    if (is_array($td = include($dir))) {
                        $commands_list = array_merge($commands_list, $td);
                    }
                } else {
                    if (($f = $mod->getLibDir()) && is_dir($f)) {

                        // get all php file that match the patter 
                        $tns = [];
                        if (!empty($ns)) {
                            $tns = [$ns];
                        }
                        $files = igk_io_getfiles($f . "/Classes/System/Console/Commands", "/Command\.php$/");
                        if (!$files) {
                            // Logger::info("command not found : ".$f);
                            continue;
                        }
                        foreach ($files as $tf) {
                            $v = igk_regex_get("/\/(?P<name>([^\/]+))Command\.php$/", "name", $tf);
                            if (empty($v)) continue;

                            $classname = str_replace("/", "\\", ($ns ? $ns . "/" : "") . "System/Console/Commands/" . $v) . "Command";

                            require_once($tf);
                            if (!class_exists($classname) || (igk_sys_reflect_class($classname))->isAbstract()) {
                                continue;
                            }
                            $commands_list[$classname] = $tf;
                        }
                    }
                }
            }
        }
        $defs = "return [\n";
        $i = 0;
        foreach ($commands_list as $ctrl => $lsts) {
            if ($i) {
                $defs .= ",\n";
            }
            if (is_array($lsts)) {
                $defs .= " \"$ctrl\"=>[\n";
                $y = 0;
                foreach ($lsts as $t) {
                    if ($y) {
                        $defs .= ",\n";
                    }
                    $defs .= "$t::class";
                    $y = 1;
                }
                $defs .= "\n]";
            } else {
                $defs .= "$ctrl::class=>\"" . igk_io_collapse_path($lsts) . "\"";
            }
            $i = 1;
        }
        $defs .= PHP_EOL . "];";
        $author = $this->getAuthor($command);
        $builder = new PHPScriptBuilder();
        $builder->type("function")
            ->author($author)
            ->defs($defs)
            ->desc("command list cache");

        igk_io_w2file(AppCommandConstant::GetCacheFile(), $builder->render());
        Logger::success(__("init command complete"));
    }
}
