<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeClassCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Tests\BaseTestCase;

class MakeClassCommand extends AppExecCommand
{
    var $command = "--make:class";

    var $category = "make";

    var $desc = "make a new class";

    var $options = [
        "--controller:[ctrl]" => "controller that will own the class",
        "--desc:[text]" => "description of the class",
        "--force" => "force creation",
        "--ns:[namespace]" => "namespace",
        "--path:[dir]" => "install directory",
        "--type:[typename]" => "type name. Allowed value : class|trait|interface",
        "--test" => "test flag"
    ];

    public function exec($command, $classPath = null)
    {
        if (empty($classPath)) {
            Logger::danger("classPath can't be empty");
            return -1;
        }
        $ctrl = igk_getv($command->options, "--controller");
        $extends = igk_getv($command->options, "--extends");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
        $path = igk_getv($command->options, "--path");
        $ns = igk_getv($command->options, "--ns", $test ? \IGK\Tests::class: \IGK::class);
        $type = igk_getv($command->options, "--type", "class");

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

                    if ($ns && (strpos($classPath, $ns) === false)) {
                        $classPath =  $ns . "/Tests/" . $classPath;
                    }
                }
            } else {
                $dir = igk_io_sys_classes_dir();
                if ($test) {

                    $dir = igk_io_sys_test_classes_dir();
                    if (empty($extends)) {
                        $extends = BaseTestCase::class;
                        if (strpos($classPath, igk_dir(\IGK\Tests::class)) !== 0) {
                            $classPath =  "IGK/Tests/" . $classPath;
                        } 
                    }
                }
            }
        }
        //igk_wln("classPath:", $classPath);
        $g = igk_dir($classPath);
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
        if (!file_exists($file = $dir . "/" . $fname) || $force) {
            $name = igk_str_ns(igk_io_basenamewithoutext($file));
            $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
            $builder = new PHPScriptBuilder();
            $builder->type($type)
                ->namespace($ns)
                ->author($author)
                ->file(basename($file))
                ->extends($extends)
                ->name($name)
                ->desc($desc);
            igk_io_w2file($file, $builder->render());
            Logger::success("output: " . $file);
            return 200;
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
