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
use IGK\System\IO\Path;
use IGK\Tests\BaseTestCase;

class MakeClassCommand extends AppExecCommand
{
    use ClassBuilderTrait;
    var $command = "--make:class";

    var $category = "make";

    var $desc = "make a new class. This is contextual command.";

    var $options = [
        "--controller:[ctrl]" => "controller that will own the class",
        "--desc:[text]" => "description of the class",
        "--force" => "force creation",
        "--ns:[namespace]" => "namespace",
        "--path:[dir]" => "output directory",
        "--type:[typename]" => "type name. Allowed value : class|trait|interface",
        "--test" => "test flag",
        "--defs" => "code definition",
        "--file:[file_to_create]"=>"generate a file"
    ];
    const TEST_CLASS = 'IGK\Tests';
    const CORE_NS="IGK";
    var $usage="";
    private function _initCommand($command){

        $ctrl = igk_getv_nil($command->options, "--controller");
        $extends = igk_getv($command->options, "--extends");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
        $path = igk_getv($command->options, "--path");
        $ns = igk_str_ns(igk_getv($command->options, "--ns", $test ? self::TEST_CLASS: self::CORE_NS));
        $type = igk_getv($command->options, "--type", "class");
        $defs = igk_getv($command->options, "--defs"); 
        return get_defined_vars();
    }
    public function generateFileFromCommand($command, $file){
        extract($this->_initCommand($command));
        $author = $this->getAuthor($command);
        if (!is_array($file)){
            $file = [$file];
        }
        $ns = igk_str_ns(igk_getv($command->options, "--ns", "IGK"));
        while(count($file)>0){
            $q = array_shift($file);
            if (!$q)continue;
            $name = igk_str_ns(igk_io_basenamewithoutext($q));
            $builder = new PHPScriptBuilder();
            $builder->type($type)
                ->namespace($ns)
                ->author($author)
                ->file(basename($q))
                ->extends($extends)
                ->name($name)
                ->desc($desc)
                ->defs($defs);
            if (igk_io_path_ext($q) != 'php'){
                $q.='.php';
            }
            igk_io_w2file($q, $builder->render());
            Logger::info("generate : ".$q);
        }

    }
    /**
     * exec command
     */
    public function exec($command, $class_path = null)
    {
        if (empty($class_path)) {
            $f = igk_getv($command->options, '--file');
            if ($f){
                return $this->generateFileFromCommand($command, $f);
            } 
            Logger::danger("classPath can't be empty");
            return -1;
        }

        $context = $command->app->getContext(); 
        if ($context == 'module'){
            //passing to module - 
            $c = new ModuleMakeClassCommand;
            $module = igk_getv($command->options, "--module");
            return $c->exec($command, $module, $class_path );
        }

        $ctrl = igk_getv_nil($command->options, "--controller");
        $extends = igk_getv($command->options, "--extends");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
        $path = igk_getv($command->options, "--path");
        $ns = igk_getv($command->options, "--ns", $test ? self::TEST_CLASS: self::CORE_NS);
        $type = igk_getv($command->options, "--type", "class");
        $defs = igk_getv($command->options, "--defs"); 

        if (strpos($class_path, '.')){
            igk_die('not allowed class path name');
        }
        if ($test && !igk_str_endwith($class_path, 'Test')){
            $class_path.= 'Test';
        }
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
                        if (strpos($class_path, igk_dir( self::TEST_CLASS)) !== 0) {
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
            $ns = Path::Combine($ns, $_ir);
        }
        $ns = ltrim(str_replace("/", "\\", $ns), "\\");
        $fname = igk_dir($g);
        if (!preg_match('/\.php$/', $fname)) {
            $fname .= ".php";
        }
        $file = Path::Combine( $dir , $fname);
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
