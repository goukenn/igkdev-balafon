<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeClassCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\Traits\ClassBuilderTrait;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Tests\BaseTestCase;

class MakeClassCommand extends AppExecCommand
{
    use ClassBuilderTrait;
    var $command = "--make:class";

    var $category = "make";

    var $desc = "make a new class";

    var $options = [
        "--controller:[ctrl]" => "controller that will own the class",
        "--desc:[text]" => "description of the class",
        "--force" => "force creation",
        "--ns:[namespace]" => "namespace",
        "--path:[dir]" => "output directory",
        "--type:[typename]" => "type name. Allowed value : class|trait|interface",
        "--test" => "test flag",
        "--defs" => "code definition"
    ];

    public function exec($command, $class_path = null)
    {
        if (empty($class_path)) {
            Logger::danger("classPath can't be empty");
            return -1;
        }
        $ctrl = igk_getv_nil($command->options, "--controller");
        $extends = igk_getv($command->options, "--extends");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
        $path = igk_getv($command->options, "--path");
        $ns = igk_getv($command->options, "--ns", $test ? \IGK\Tests::class: \IGK::class);
        $type = igk_getv($command->options, "--type", "class");
        $defs = igk_getv($command->options, "--defs"); 
        if (!empty($path) && !property_exists($command->options, '--ns')){
            // reset namespace
            $ns = "";
        }
        if (!in_array($type, ["class", "interface", "trait"])) {
            $type = "class";
        }
        $dir = "";
        if (!empty($path)) {
            $dir = rtrim(igk_uri($path), '/');
        } else {
            if (!empty($ctrl)) {
                $ctrl_name = $ctrl;
                if (!($ctrl = igk_getctrl($ctrl, false))){
                    igk_die("controller not found.".$ctrl_name);
                }
                $dir = $ctrl::classdir();
                $ns = $ctrl->getEntryNamespace();
                if ($test) {
                    $dir = dirname($dir) . "/tests";
                    if ($ns && (strpos($class_path, $ns) === false)) {
                        $class_path =  $ns . "/Tests/" . $class_path;
                    }
                }
            } else {
                $dir = igk_io_sys_classes_dir();
                if ($test) {

                    $dir = igk_io_sys_test_classes_dir();
                    if (empty($extends)) {
                        $extends = BaseTestCase::class;
                        if (strpos($class_path, igk_dir(\IGK\Tests::class)) !== 0) {
                            $class_path =  "IGK/Tests/" . $class_path;
                        } 
                    }
                }
            }
        }
        //igk_wln("classPath:", $classPath);
        $g = igk_dir($class_path);
        if (strpos($g, $gs = igk_dir($ns) . "/") === 0) {
            $g = ltrim(substr($g, strlen($gs)), "/");
        }
        //if ($ctrl){
        if (($_ir = dirname($g)) != '.') {
            $ns .= "/" . $_ir;
        }
        $ns = ltrim(str_replace("/", "\\", $ns), "\\");
        $fname = igk_dir($g);
        if (!preg_match('/\.php$/', $fname)) {
            $fname .= ".php";
        }
        $file = $dir . "/" . $fname;
        // Logger::success("output: " . igk_io_basedir());
        // Logger::success("output: " . $file);
        // Logger::success("output: " . getcwd());

        if (!file_exists($file) || $force) {
            $name = igk_str_ns(igk_io_basenamewithoutext($file));
            $author = $this->getAuthor($command);
            $builder = new PHPScriptBuilder();
            $builder->type($type)
                ->namespace($ns)
                ->author($author)
                ->file(basename($file))
                ->extends($extends)
                ->name($name)
                ->desc($desc)
                ->defs($defs);
            igk_io_w2file($file, $builder->render());
            Logger::success("output: " . $file); 
            Logger::success("duration : " . igk_sys_request_time());
            return 0;
        } else {
            Logger::danger("file already exists : " . $file);
        }
        return 400;
    }
    public function help()
    {
        parent::help();
    }
    protected function showUsage()
    {
        Logger::print("Usage : balafon --make:class [options] classname");
    }
}
