<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeClassCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use Exception;
use IGK\Constants;
use IGK\Helper\IO;
use IGK\Helper\JSon;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\Traits\ClassBuilderTrait;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\IO\Path;
use IGK\System\Regex\Replacement;
use IGK\Tests\BaseTestCase;
use IGKException;

/**
* Make class command.
* @package IGK\System\Console\Commands
*/
class MakeClassCommand extends AppExecCommand
{
    use ClassBuilderTrait;
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--make:class";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "make";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "make a new class. This is contextual command.";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        "--controller:[ctrl]" => "controller that will own the class",
        "--desc:[text]" => "description of the class",
        "--force" => "force creation",
        "--ns:[namespace]" => "namespace",
        "--path:[dir]" => "output directory",
        "--type:[typename]" => "type name. Allowed value : class|trait|interface|[scaffold model]",
        "--test" => "test flag",
        "--defs" => "code definition",
        "--file:[file_to_create]" => "generate a file",
        '--scaffold'=>'list of scaffold model to use with type'
    ];
    /**
    * Constant: test class.
    * @var mixed
    */
    const TEST_CLASS = 'IGK\Tests';
    /**
    * Constant: core ns.
    * @var mixed
    */
    const CORE_NS = "IGK";
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = "";
    /**
    * auto generate doc.
    * @param mixed $command
    * @return array
    */
    private function _initCommand($command)
    {
        $ctrl = igk_getv_nil($command->options, "--controller");
        $extends = igk_getv($command->options, "--extends");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
        $path = igk_getv($command->options, "--path");
        $ns = igk_str_ns(igk_getv($command->options, "--ns", $test ? self::TEST_CLASS : self::CORE_NS));
        $type = igk_getv($command->options, "--type", "class");
        $defs = igk_getv($command->options, "--defs");
        return get_defined_vars();
    }
    /**
    * auto generate doc.
    * @param mixed $file
    * @return void
    */
    public function generateFileFromCommand($command, $file)
    {
        extract($this->_initCommand($command));
        $author = $this->getAuthor($command);
        if (!is_array($file)) {
            $file = [$file];
        }
        $ns = igk_str_ns(igk_getv($command->options, "--ns", "IGK"));
        while (count($file) > 0) {
            $q = array_shift($file);
            if (!$q) continue;
            if (file_exists($q) && !$force){
                Logger::danger('file exists');
                continue;
            }
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
            if (igk_io_path_ext($q) != 'php') {
                $q .= '.php';
            }
            igk_io_w2file($q, $builder->render());
            Logger::info("generate : " . $q);
        }
    }
    /**
     * retrieve make:class - scaffold service 
     * @return mixed 
     * @throws Exception 
     */
    public function getScaffoldClassService()
    {
        return igk_app()->getService('make:class-scaffold');
    }
    /**
    * auto generate doc.
    * @param mixed $extensions
    * @return string|null
    */
    static function ScaffoldResolveClass(string $path, $extensions)
    {
        while (count($extensions) > 0) {
            $q = array_shift($extensions);
            if (igk_io_file_exists($cf = $path . $q)) {
                return $cf;
            }
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param null|{name:string, defs: template}
    * @return mixed
    */
    protected function resolveTypeDefinition($type)
    {
        if ($scaffold = $this->getScaffoldClassService()) {
            $type = $scaffold->classModel($type);
        } else if ($d = igk_environment()->make_class_scaffold) {
            $type = igk_getv($d, $type);
        } else if ($file = self::ScaffoldResolveClass(IGK_LIB_DIR . "/Data/Scaffold/" . $type, ['.mphp', '.template'])) {
            $type = igk_createobj(['name' => $type, 'defs' => file_get_contents($file)]);
        }
        return $type;
    }
    /**
    * Showlist of scaffold.
    */
    public function showlistOfScaffold(){
        $list = [];
        $docs = [
            'dbconstant'=>'make class for initial table column value'
        ];
        if ($srv = $this->getScaffoldClassService()){
            $srv->scaffoleList($list, $docs);
        }
        foreach(IO::GetFiles(IGK_LIB_DIR.'/Data/scaffold', '/\.(php|template)/', false) as $file){
            $list[] = igk_io_basenamewithoutext($file);
        }
        sort($list);
        Logger::info("scaffold list:\n");
        Logger::print(implode("\n", array_map(function($s)use($docs){
            return App::Gets(App::GREEN ,$s)."\r\t\t".igk_getv($docs, $s);
        }, $list)));
        Logger::print('');
    }
    /**
     * exec command
     */
    public function exec($command, $class_path = null, ?string $definition = null)
    { 
        if ($scaffold_help = igk_getv($command->options, '--scaffold')){
            $this->showlistOfScaffold();
            return;
        }
        if (empty($class_path)) {
            $f = igk_getv($command->options, '--file');
            if ($f) {
                return $this->generateFileFromCommand($command, $f);
            }
            Logger::danger("classPath can't be empty");
            return -1;
        }
        $context = $command->app->getContext();
        if ($context == 'module') {
            $c = new ModuleMakeClassCommand;
            $module = igk_getv($command->options, "--module");
            return $c->exec($command, $module, $class_path);
        }
        $ctrl = igk_getv_nil($command->options, "--controller");
        $extends = igk_getv($command->options, "--extends");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
        $path = igk_getv($command->options, "--path");
        $ns = igk_getv($command->options, "--ns", $test ? self::TEST_CLASS : self::CORE_NS);
        $type = igk_getv($command->options, "--type", "class");
        $defs = igk_getv($command->options, "--defs");
        $definition = $definition ? igk_json_parse($definition) : null;
        if (strpos($class_path, '.')) {
            igk_die('not allowed class path name');
        }
        if ($test && !igk_str_endwith($class_path, 'Test')) {
            $class_path .= 'Test';
        }
        if (!empty($path) && !property_exists($command->options, '--ns')) {
            $ns = "";
        }
        if (!in_array($type, ["class", "interface", "trait"])) {
            $g = $type;
            $type = $this->resolveTypeDefinition($type) ?? 'class';
        }
        $dir = "";
        if (!empty($path)) {
            $dir = rtrim(igk_uri($path), '/');
        } else {
            if (!empty($ctrl)) {
                $ctrl_name = $ctrl;
                if (!($ctrl = self::GetController($ctrl, false))) {
                    igk_die("controller not found." . $ctrl_name);
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
                        if (strpos($class_path, igk_dir(self::TEST_CLASS)) !== 0) {
                            $class_path =  "IGK/Tests/" . $class_path;
                        }
                    }
                }
            }
        }
        $g = igk_dir($class_path);
        if (strpos($g, $gs = igk_dir($ns) . "/") === 0) {
            $g = ltrim(substr($g, strlen($gs)), "/");
        }
        if (($_ir = dirname($g)) != '.') {
            $ns = Path::Combine($ns, $_ir);
        }
        $ns = ltrim(str_replace("/", "\\", $ns), "\\");
        $fname = igk_dir($g);
        if (!preg_match('/\.php$/', $fname)) {
            $fname .= ".php";
        }
        $file = Path::Combine($dir, $fname);
        if (!file_exists($file) || $force) {
            $name = igk_str_ns(igk_io_basenamewithoutext($file));
            $author = $this->getAuthor($command);
            $builder = new PHPScriptBuilder();
            $src = ''; 
            if (!is_string($type)) {
                $extends = null;
                $model = igk_getv($command->options, '--model', 'Model');
                $uses = [];
                if ($ctrl && $model){
                    $tcl = $ctrl->resolveClass('/Models/'.$model);
                    if ($tcl){
                        $uses[] = $tcl;
                    }
                }
                $rp = new Replacement;
                $rp->add('/%__class_name__%/', $name);
                $rp->add('/%__namespace__%/', $ns);
                $rp->add('/%__class_phpdoc__%/', implode ("\n * ", array_filter([
                    "@author ".$author,
                    "@package ".$ctrl->getName()
                ])));
                $rp->add('/%__author__%/', PHPScriptBuilder::GenScriptFileHeader(
                        array_merge(compact('author'), [
                            'file'=> basename($file),
                            'date'=> date('Ymd His')
                            ]
                        )));
                $rp->add('/%__uses__%(;)?/', $uses ? 'use '.implode(";\nuse ", $uses).';':'');
                $rp->add('/%__model__%/', $model);
                $rp->add('/%__fieldname__%/', igk_getv($command->options, '--fieldname', Constants::DB_MODEL_FIELD_PREFIX. 'ID'));
                $rp->add("/%_[^%]+_%(;)?/", '');
                $defs = $rp->replace($type->defs);
                $src = $defs;
            } else {
                if ($definition){
                    $s = PHPScriptBuilderUtility::ExtractClassDefinition($definition,'', (object)[
                        'noHeader'=>true
                    ]);
                    $s = ltrim(igk_str_rm_start($s, '<?php'));
                    if ($defs)
                        $defs .= $s;
                    else  
                        $defs = $s; 
                }
                $builder->type($type)
                    ->namespace($ns)
                    ->author($author)
                    ->file(basename($file))
                    ->extends($extends)
                    ->name($name)
                    ->desc($desc)
                    ->defs($defs);
                $src = $builder->render();
            }
            igk_io_w2file($file, $src);
            Logger::success("output: " . $file);
            Logger::success("duration : " . igk_sys_request_time());
            return 0;
        } else {
            Logger::danger("file already exists : " . $file);
        }
        return 400;
    }
    /**
    * Help.
    */
    public function help()
    {
        parent::help();
    }
    /**
    * Shows Usage.
    */
    protected function showUsage()
    {
        Logger::print("Usage : balafon --make:class [options] classname");
    }
}