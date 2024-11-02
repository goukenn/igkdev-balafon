<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InitCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use Exception;
use Error;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppCommandConstant;
use IGK\System\Console\AppConstant;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

use IGK\Helper\IO as IGKIO;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKConstants;
use IGKException;
use ReflectionClass;
use ReflectionException;

use function igk_resources_gets as __;

class InitCommand extends AppExecCommand
{
    var $command = "--command:init";

    var $desc  = "initialize balafon command cache";

    const BASECLASS_COMMAND = IGKConstants::BASECLASS_COMMAND;

    /**
     * 
     * @param mixed $command 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Error 
     */
    public function exec($command)
    {
        igk_set_timeout(0);
        $t = [
            igk_io_projectdir()
        ];
        \IGK\Helper\SysUtils::ClearCache();
        $v_bserie = ltrim(EntryClassResolution::CommandEntryNS,'\\');
        $commands = [];
        $commands_list = [];
        $ctrls = igk_app()->getControllerManager()->getControllers();
        $entry_cl = igk_uri($v_bserie);
        foreach ($t as $dir) {
            igk_is_debug() && Logger::info("init : ".$dir);
            foreach ($ctrls as $c) {
                if (!strstr(realpath($c->getDeclaredDir()), realpath($dir))){
                    continue;
                }
                //if (strstr($c->getDeclaredDir(), $dir)) {
                    $cldir = $c::classdir();
                    if (!isset($commands[$cldir])) {
                        $classname = get_class($c);
                        $c::register_autoload();
                        $cl_dir = $cldir . "/".$entry_cl;
                        $tab = igk_io_getfiles($cl_dir, "/\.php$/");
                        if (!$tab)
                            continue;
                        $ln = strlen($cl_dir);
                        foreach ($tab as $file) {
                            $mt = substr($file, $ln);
                            // remove extension 
                            $mt = ltrim(igk_io_remove_ext($mt), '/');
                            if ($clpath = $c->resolveClass($entry_cl."/" . $mt)) {
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
                //}
            }
        }

        igk_is_debug() && Logger::info("init - module > ");
        $mod = igk_get_modules();
        if ($mod  && (count($mod) > 0)) {
            $base_cl =  igk_uri(self::BASECLASS_COMMAND)."/";
            $system_cl_command = igk_uri($v_bserie);
            foreach ($mod as $k => $v) {
                $cmod = igk_get_module($k);
                $ns = $cmod->config("entry_NS");
                $dir = $cmod->getDeclaredDir() . "/.commands.php";
                if (file_exists($dir)) {
                    igk_is_debug() && Logger::info("try include - ".$dir);
                    if (is_array($td = include($dir))) {
                        $commands_list = array_merge($commands_list, $td);
                    }
                } else {
                
                    if (($f = $cmod->getClassesDir()) && is_dir($f)) {

                        // get all php file that match the patter 
                        // $tns = [];
                        // if (!empty($ns)) {
                        //     $tns = [$ns];
                        // }
                        $lc_dir = $f . '/'.$system_cl_command;
                        $files = igk_io_getfiles($lc_dir, "/Command\.php$/");
                        if (!$files) {
                            // Logger::info("command not found : ".$f);
                            continue;
                        } 
                        $len = strlen($lc_dir);
                        foreach ($files as $tf) {
                            $mf = substr($tf, $len);
                            $v = igk_regex_get("/\/(?P<name>(.+))Command\.php$/", "name", $mf);
                            if (empty($v)) 
                            
                            continue;

                            $classname = str_replace("/", "\\", ($ns ? $ns : "") . $base_cl . $v) . "Command";
                            if (isset($commands_list[$classname])){
                                igk_debug_wln_e("[Module] - classname already set", $classname, $tf);
                                continue;
                            }
                            require_once($tf); 
                            if (!class_exists($classname, false) || (igk_sys_reflect_class($classname))->isAbstract()) {
                                 igk_is_debug() && Logger::warn("skip command : > ". $classname);
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
        igk_is_debug() && Logger::info("load command > ");
        foreach ($commands_list as $ctrl => $lsts) {
            igk_is_debug() && Logger::info("command - ");
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
                igk_is_debug() && Logger::info("try collapse - ".$lsts);
                $defs .= "$ctrl::class=>\"" . igk_io_collapse_path($lsts) . "\"";
            }
            $i = 1;
        }
        $defs .= PHP_EOL . "];";
        igk_is_debug() && Logger::info("load command > ");
        $author = $this->getAuthor($command);
        $builder = new PHPScriptBuilder();
        $builder->type("function")
            ->author($author)
            ->defs($defs)
            ->desc("command list cache");
        igk_is_debug() && Logger::info("try write to file: > ".AppCommandConstant::GetCacheFile());
        igk_io_w2file(AppCommandConstant::GetCacheFile(), $builder->render());
        Logger::success(__("init command complete"));
    }
}
