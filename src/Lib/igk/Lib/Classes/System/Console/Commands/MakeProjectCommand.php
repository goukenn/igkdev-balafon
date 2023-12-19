<?php
// @file: MakeProjectCommand.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

///<summary>Represente namespace: IGK\System\Console\Commands</summary>
namespace IGK\System\Console\Commands;

use Closure;
use IGK\ApplicationLoader;
use IGK\Controllers\ControllerInitListener;
use IGK\Database\SchemaBuilder\IDiagramSchemaBuilder;
use IGK\Helper\IO;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\Helper\StringUtility;
use IGK\Helper\SysUtils;
use IGK\Resources\R;
use IGK\System\Configuration\CoreGeneration;
use \IGKControllerManagerObject;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonConfiguration;
use IGK\System\Console\CommandEnvironmentArgLoader;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger;
use IGK\System\Database\SchemaBuilder;
use IGK\System\Http\Route;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;
use IGK\System\Project\Configurations\ConfigurationPropertyInfo;
use IGKConstants;
use IGKEvents;

use function igk_resources_gets as __;
use stdClass;
use Symfony\Component\Serializer\Encoder\JsonEncode;

///<summary>Represente class: MakeProjectCommand</summary>
class MakeProjectCommand extends AppExecCommand
{
    var $category = "make", $command = "--make:project", $desc = "make new project.", $options = [
        "--type:[type]" => "project type. default is ApplicationController::class",
        "--entryNamespace:[namespace]" => "define project entry NS",
        "--desc:[desc]" => "project description",
        "--configs" => "setup project config",
        "--git" => "flag: to enabled git configuration",
        "--conf-default" => "flag: to allow default configuration",
        "--force" => "flag force create project",
        "--no-config" => "flag disable auto configuration",
        "--conf:[name=value]" => "set configuration",
        "--version" => "application version"
    ];

    var $entryNamespace;

    /**
     * define author
     * @var mixed
     */
    protected $author;

    ///<summary>Represente exec function</summary>
    ///<param name="command"></param>
    ///<param name="name" default=""></param>
    public function exec($command, $controller = "")
    {


        if (empty($controller)) {
            return false;
        }
        if ($command->app->getConfigs()->isTemp) {
            Logger::danger(__("Create Project in temporary folder is not allowed. please setup your environment"));
            return false;
        }
        $controller = StringUtility::CamelClassName($controller);
        $dir = igk_io_projectdir() . "/" . $controller;
        Logger::info(__("Make project ... {0}",  $controller));



        $author = $this->getAuthor($command);
        $type = igk_getv($command->options, "--type", \IGK\Controllers\ApplicationController::class);
        $e_ns = igk_getv($command->options, "--entryNamespace", null);
        $desc = igk_getv($command->options, "--desc", null);
        $configs = igk_getv($command->options, "--configs", null);
        $no_config = property_exists($command->options, "--noconfig");
        $use_git = property_exists($command->options, "--git");
        $force = property_exists($command->options, "--force");
        $ns = igk_str_ns($controller);
        $pname = basename(igk_dir($ns));
        $clname = ucfirst($pname) . "Controller";
        $dir = igk_io_projectdir() . "/{$pname}";
        igk_init_controller(new ControllerInitListener($dir, 'appsystem'));
        $defs = "";
        if (!defined('IGK_TEST_INIT')) {
            spl_autoload_register(ApplicationLoader::TestClassesLoaderCallback(), true, true);
        }
        $this->author = $author;

        if (!empty($e_ns)) {
            $e_ns = str_replace("\\\\", "\\", igk_str_ns($e_ns));
            $defs .= "protected function getEntryNamespace(){ return \\{$e_ns}::class; }";
            $this->entryNamespace = $e_ns;
        } else {
            $this->entryNamespace = $e_ns =  SysUtils::GetProjectEntryNamespace($dir);
        }
        IO::CreateDir(implode("/", [$dir, IGK_DATA_FOLDER, IGK_RES_FOLDER]));
        $bind = [];
        $fname = $clname . ".php";
        $fullClassName = implode("\\", array_filter([$e_ns, $clname]));


        $bind[$dir . "/.global.php"] = function ($file) use ($author) {
            $builder = new PHPScriptBuilder();
            $builder->type("function")->author($author)->desc("global function");
            igk_io_w2file($file, $builder->render());
        };

      

        $bind[$dir . "/$fname"] = function ($file) use ($type, $author, $defs, $desc, $clname, $fname) {
            $builder = new PHPScriptBuilder();
            $builder->type("class")->name($clname)
                ->author($author)->defs($defs)
                ->doc("Controller entry point")
                ->file($fname)
                ->desc($desc)
                ->extends($type);
            igk_io_w2file($file, $builder->render());
        };

        $this->_bind_articles($bind, $dir);
        $this->_bind_langs($bind, $dir);
        $this->_bind_layout($bind, $dir);

        $defaultsrc = <<<EOF
/**
 * @var object \$t
 * @var object \$doc
*/
\$doc->title = \$this->getName();
\$t->clearChilds();
\$t->div()->h1()->Content = "Hello !!! ".\$this->getName();
EOF;

        // + | --------------------------------------------------------------------
        // + | Auto configuration 
        // + |

        $obj_conf = new stdClass();
        if ($p = igk_getv($command->options, "--conf")) {
            if (!is_array($p)) {
                $p = array($p);
            }
            $loader = new CommandEnvironmentArgLoader();
            foreach ($p as $l) {
                $loader->load($obj_conf, $l);
            }
        }

        $config = new stdClass();
        if (!$no_config && class_exists($type)) {
            if (method_exists($type, "GetAdditionalDefaultViewContent")) {
                $defaultsrc = $type::GetAdditionalDefaultViewContent();
                if (strpos($defaultsrc, "<?php") === 0) {
                    $defaultsrc = substr($defaultsrc, 5);
                }
            }
            Logger::info(__("Setup general configuration"));
            $prop = (object)["name" => $pname, "description" => $desc];
            $tab = property_exists($command->options, "--conf-default") ? igk_sys_getdefaultctrlconf() : [];
            if (method_exists($type, 'GetAdditionalConfigInfo')) {
                if ($tabInfo = $type::GetAdditionalConfigInfo()) {
                    $tab = array_merge($tab, $tabInfo);
                }
            }
            ksort($tab);
            if (function_exists('readline')) {
                Logger::info(__("Configure")); // definite keys string name
                $names = [
                    "clAppName" => __("Name"),
                    "clTitle" => __("Title"),
                    "clBasicUriPattern" => __("Entry URI"),
                    "clDataSchema"=>__('Use Database Schema'),
                    "clDataTablePrefix" => __("Table's Prefix"),
                    "clAppNotActive"=>__("Is project not active ?"),
                ];
                foreach ($tab as $key => $value) {
                    $def = null;
                    if ($value instanceof ConfigurationPropertyInfo){
                        if ($key == 'clAppName'){
                            $value->clDefaultValue = $controller;
                        }
                        if ($value->clType== 'bool'){
                            $names[$key] .= App::Gets(App::GRAY, ' (1|0)');
                        }
                        if (!is_null($value->clDefaultValue)){
                            $def = $value->clDefaultValue;
                            $names[$key] .= App::Gets(App::GRAY_I, sprintf(' (%s)', $value->clDefaultValue));
                        }
                    }
                    if (property_exists($obj_conf, $key)) {
                        $config->$key = $obj_conf->$key;
                    } else {
                        
                        if (is_null($def) && ($def = (is_array($value) ? igk_getv($value, "default") : $value)) ){
                        if (igk_is_closure($def)) {
                                $def = $def($prop);
                            }
                        }
                        $config->$key = igk_getsv(readline(igk_getv($names, $key, $key) . " = "), $def);
                    }
                }
            }
        } else {
            $config = (object)$obj_conf;
        }
        if ($config) {
            $bind[igk_io_joinpath($dir, IGK_DATA_FOLDER, IGK_CTRL_CONF_FILE)] = function ($file) use ($config) {
                $d = igk_createxml_config_data($config);
                igk_io_w2file($file, $d->render((object)["Indent" => true]));
            };
        } 

        $bind[$dir . "/" . IGK_VIEW_FOLDER .
            "/default.phtml"] = function ($file) use ($author, $defaultsrc) {
            $builder = new PHPScriptBuilder();
            $builder->type("function")->file(basename($file))
                ->desc("default balafon view")
                ->author($author)
                ->defs($defaultsrc);
            igk_io_w2file($file, $builder->render());
        };
        $no_access_callback = function ($f) {
            igk_io_w2file($f, 'deny from all');
        };
        $grant_access_callback = function ($f) {
            igk_io_w2file($f, 'allow from all');
        };
        $bind[$dir . "/" . IGK_DATA_FOLDER . '/.htaccess'] = $no_access_callback;
        $bind[$dir . "/" . IGK_DATA_FOLDER . '/' . IGK_RES_FOLDER . '/.htaccess'] = $grant_access_callback;

        $bind[$dir . "/" . IGK_DATA_FOLDER .
            "/data.schema.xml"] = function ($file) use ($author, $dir) {
            $build = new SchemaBuilder();
            $build["version"] = "1.0";
            $build["author"] = $author;
            $build["createAt"] = date('Ymd H:i:s');
            $build->comment("data schema");
            $build->comment(implode("\n", [
                "@entries -->",
                '<!--',
                '<Entries>',
                '   <Rows For="%prefix%user_info_types">',
                '       <Row clName="PhoneNumber" clDataType="tel" clRegex="[^0][0-9]+" clCardinality="0" clType="1" />',
                '       <Row clName="GSMNumber" clDataType="tel" clRegex="[^0][0-9]+" clCardinality="0" clType="1" />',
                '       <Row clName="Email" clDataType="email" clRegex="" clCardinality="0" clType="1" />',
                '   </Rows>',
                '...',
                '</Entries>',
            ]));
            $build->comment(implode("\n", [
                "@definitions -->",
                '<!--',
                '<DataDefinition TableName="%prefix%tablename">',
                '   <Column clName="clId" clType="Int" clAutoIncrement="True" clNotNull="True" clIsPrimary="True" />',
                '   <Column clName="clName" clType="VARCHAR" clTypeLength="140" clNotNull="True" clIsUnique="True" />',
                '</DataDefinition>',
            ]));
            $build->comment(implode("\n", [
                "@migrations-->",
                '<!--',
                '<Migrations>',
                '<Migration>',
                '<changeColumn table="%prefix%users" column="clLogin">',
                '    <Column clName="clLogin" clType="VARCHAR" clTypeLength="60" clIsUnique="True" clRequire="true" clNotNull="true" clNotAllowEmptyString="true" />',
                '</changeColumn>',
                '</Migration> ',
                '...',
                '</Migrations>',
            ]));
            igk_io_w2file($file, $build->render((object)["Context" => "XML", "Indent" => true]));
        };

        // Lib/autoload.php
        $bind[$dir . "/" . IGK_LIB_FOLDER . "/autoload.php"] = function ($file) {
            $builder = new PHPScriptBuilder();
            $builder->uses(Route::class)
                ->type("function")
                ->file(basename($file))
                ->defs(implode("\n", [
                    "// on initialize ::register_autoload",
                ]));
            igk_io_w2file($file, $builder->render());
        };

        MakeUtility::BindDefaultLangSupport($command, $dir, $bind);

        $this->_initConfigurationFile($bind, $dir, get_defined_vars());
        // configuration 
        $bind[$dir . "/" . IGK_CONF_FOLDER . "/routes.php"] = function ($file) {
            $builder = new PHPScriptBuilder();
            $builder->uses(Route::class)
                ->type("function")
                ->file(basename($file))
                ->defs(implode("\n", [
                    "// store the RouteActionHandler for this base controller",
                    "// Route::get( \$actionClass, string \$uriPattern) ",
                    "// - \$actionClass: middle ware action class",
                    "// - \$uriPattern : path {name}  ",
                    "// - \$uriPattern : path/{name} with required parameter ",
                    "// - \$uriPattern : path/{name} with optional parameter ",
                ]));
            igk_io_w2file($file, $builder->render());
        };
        $bind[$dir . "/" . IGK_CONF_FOLDER . "/views.php"] = function ($file) {
            $builder = new PHPScriptBuilder();
            $builder->uses(Route::class)
                ->type("function")
                ->file(basename($file))
                ->defs(implode("\n", [
                    "// store the controller configuration - ",
                    "return [",
                    '// "default_dir_entry"=>"default",',
                    '// "is_dir_entry"=>[],',
                    "];",
                ]));
            igk_io_w2file($file, $builder->render());
        };
        if ($configs)
            $bind[$dir . "/" . IGK_DATA_FOLDER . "/" . IGK_CTRL_CONF_FILE] = function ($file) use ($author, $dir, $configs) {
                $parse = (object)[];
                array_map(
                    function ($m) use ($parse) {
                        list($k, $v)
                            = explode("=", $m);
                        igk_conf_set($parse, $v, $k);
                        return;
                    },
                    $configs
                );
                $c = igk_createxml_config_data($parse);
                igk_io_w2file($file, $c->render((object)["Context" => "XML", "Indent" => true]));
            };
        // + | init environment - data
        $bind[$dir . "/phpunit.xml.dist"] = function ($file) use ($fullClassName) {
            // $c_app =  igk_io_expand_path("%lib%");
            // igk_wln_e(__FILE__.":".__LINE__, $c_app,igk_io_collapse_path($file), igk_io_expand_path(igk_io_collapse_path($file)));
            $n = igk_create_xmlnode("phpunit");
            $n["xmlns:xsi"] = "http://www.w3.org/2001/XMLSchema-instance";
            $n["xsi:noNamespaceSchemaLocation"] = igk_io_expand_path("%packages%") . "/vendor/phpunit/phpunit/phpunit.xsd";
            $n["bootstrap"] = "./Lib/Tests/autoload.php";
            $n["colors"] = "true";
            $suites = $n->add("testsuites");
            $ts =  $suites->add("testsuite");
            $ts["name"] = "projects";
            $ts->add("directory")->Content = "./Lib/Tests";
            $env = $n->php();
            // INSTALL PROJECT BASE PATH SETTING
            $env->add("env")->setAttributes(["name" => "IGK_BASE_DIR", "value" => IGK_BASE_DIR]);
            $env->add("env")->setAttributes(["name" => "IGK_APP_DIR", "value" => IGK_APP_DIR]);
            // PROJECT CONTROLLER
            $env->add("env")->setAttributes(["name" => "IGK_TEST_CONTROLER", "value" => $fullClassName]);
            igk_io_w2file($file, $n->render((object)["Indent" => true]));
        };


        $bind[$dir . "/phpunit-watcher.yml"] = function ($file) {
            $c_app =  igk_io_expand_path("%packages%");
            $n = new StringBuilder();
            $n->appendLine("hideManual: true");
            $n->appendLine("watch:");
            $n->appendLine("  directories:");
            $n->appendLine("    - ./");
            $n->appendLine("notifications:");
            $n->appendLine("  passingTests: false");
            $n->appendLine("  failingTests: false");
            $n->appendLine("phpunit:");
            $n->appendLine("  binaryPath: {$c_app}/vendor/bin/phpunit");
            $n->appendLine("  arguments: --stop-on-failure --colors=always --testdox --bootstrap Lib/Tests/autoload.php Lib/Tests");
            igk_io_w2file($file, $n);
        };

        $bind[$dir . "/" . IGK_LIB_FOLDER . "/Tests/autoload.php"] = function ($file) use ($controller) {
            $builder = new PHPScriptBuilder();
            $gen = new CoreGeneration();
            $builder->type('function')->defs(
                implode(
                    "\n",
                    [
                        $gen->GetTestRequireAutoload()
                    ]
                )
            );
            igk_io_w2file($file, $builder->render());
        };

        if (class_exists(\PHPUnit\Framework\TestCase::class))
            $bind[$dir . "/" . IGK_LIB_FOLDER . "/Tests/" . $clname . "Test.php"] = function ($file) use ($controller, $clname) {
                $builder = new PHPScriptBuilder();
                $e_ns = $this->entryNamespace;
                $builder->type('class')
                    ->namespace($e_ns . "\\Tests")
                    ->name($clname . "Test")
                    ->file(basename($file))
                    ->desc($controller . " controller test ")
                    ->extends(\IGK\Tests\Controllers\ControllerBaseTestCase::class)
                    ->defs(implode("\n", [
                        "public function test_init(){",
                        "   \$this->assertTrue(true);",
                        "}"
                    ]));
                igk_io_w2file($file, $builder->render());
            };

        $this->_bind_database($bind, $dir, $controller);

        if ($use_git) {
            ($force || !is_dir($dir . "/.git")) &&
                GitHelper::Generate($bind, $dir, $controller, $author, $desc, [
                    "phpunit**",
                    ".phpunit.result.cache",
                ]);
        }


        Utility::MakeBindFiles($command, $bind, $force);
        // + invoke hook - command
        igk_hook(IGKEvents::HOOK_COMMAND, ['cmd' => $this, 'dir' => $dir, 'name' => $controller, 'args' => func_get_args()]);

        \IGK\Helper\SysUtils::ClearCache(null, true);
        Logger::info("output: " . $dir);
        Logger::success("done\n");
    }
    protected function _store_article($f)
    {
        $builder = new PHPScriptBuilder();
        $builder->type("function")->file(basename($f));
        igk_io_w2file($f, $builder->render());
    }
    protected function _bind_articles(array &$bind, $dir)
    {
        $tab = R::GetSupportedLangs();
        $outdir = $dir . "/" . IGK_ARTICLES_FOLDER;
        $fc = Closure::fromCallable([$this, "_store_article"])->bindTo($this);
        foreach (explode("|", "about|presentation|confidentiality") as $l) {
            foreach ($tab as $b) {
                $bind[$outdir . "/" . $l . "." . $b . IGK_VIEW_FILE_EXT] = $fc;
            }
        }
    }
    protected function _bind_langs(array &$bind, $dir)
    {

        $touch = function ($file) {
            $sb = new StringBuilder();
            $sb->appendLine('$l["title.default"] = "Home";');
            $builder = new PHPScriptBuilder();
            $builder->type("function")
                ->desc("application current lang")
                ->defs($sb)
                ->author($this->author);
            igk_io_w2file($file, $builder->render());
        };
        foreach (R::GetSupportedLangs() as $l) {
            $bind[$dir . "/Configs/Lang/lang." . $l . ".presx"] = $touch;
        }
    }

    protected function _bind_layout(array &$bind, $dir)
    {
        $view_dir = implode("/", [$dir, IGK_VIEW_FOLDER]);
        $bind[$view_dir . "/.header.pinc"] = function ($f) {
            // primary layout header
            $sb = new StringBuilder();
            $sb->appendLine('igk_google_addfont($doc, "Roboto"); ');
            $sb->appendLine('$t->setClass("+google-Roboto"); ');

            $builder = new PHPScriptBuilder();
            $builder->type("function")->file(basename($f))
                ->defs($sb);
            igk_io_w2file($f, $builder->render());
        };
        $bind[$view_dir . "/.menu.pinc"] = function ($f) {
            $builder = new PHPScriptBuilder();
            $builder->type("function")->file(basename($f))->defs("return [];");
            igk_io_w2file($f, $builder->render());
        };
        $bind[$view_dir . "/.footer.pinc"] = function ($f) {
            // footer definition
            $sb = new StringBuilder();
            $sb->appendLine('$t->div()->container()->igkcopyright(); ');
            $builder = new PHPScriptBuilder();
            $builder->type("function")->file(basename($f))
                ->defs($sb);
            igk_io_w2file($f, $builder->render());
        };
    }

    protected function _bind_database(array &$bind, $dir, $controller)
    {
        $bind[$dir . "/" . IGK_LIB_FOLDER . "/" . IGK_CLASSES_FOLDER . "/Database/InitMacros.php"] = function ($file) use ($controller) {

            $e_ns = $this->entryNamespace;
            $builder = new PHPScriptBuilder;
            $extends = null;
            $desc = null;
            $content = implode("\n", [
                'public function run(AppBuilder $builder){',
                '}',
            ]);
            $builder->type("class")
                ->namespace($e_ns . "\\Database")
                ->author($this->author)
                ->file(basename($file))
                ->uses([
                    \IGK\System\AppBuilder::class
                ])
                ->extends($extends)
                ->name("InitMacros")
                ->desc($desc)
                ->defs($content);
            igk_io_w2file($file, $builder->render());
        };

        $bind[$dir . "/" . IGK_LIB_FOLDER . "/" . IGK_CLASSES_FOLDER . "/Database/InitDbSchemaBuilder.php"] = function ($file) use ($controller) {

            $e_ns = $this->entryNamespace;
            $builder = new PHPScriptBuilder;
            $extends = \IGK\Database\SchemaBuilder\IDiagramBuilder::class;
            $desc = null;
            $content = implode("\n", [
                'public function up(IDiagramSchemaBuilder $builder){',
                '}',
                'public function down(IDiagramSchemaBuilder $builder){',
                '}',
            ]);
            $builder->type("class")
                ->namespace($e_ns . "\\Database")
                ->author($this->author)
                ->file(basename($file))
                ->uses([
                    \IGK\Database\SchemaBuilder\IDiagramSchemaBuilder::class
                ])
                ->extends($extends)
                ->name(igk_io_basenamewithoutext(basename($file)))
                ->desc($desc)
                ->defs($content);
            igk_io_w2file($file, $builder->render());
        };
    }
    private function _initConfigurationFile(&$bind, $dir, $options)
    {
        $v_conf_dir = $dir . "/" . IGK_CONF_FOLDER;
        $bind[$v_conf_dir . "/profiles.php"] = function ($file) use ($options) {
            $cl = $options['fullClassName'];
            $sb = new StringBuilder;
            $builder = new PHPScriptBuilder;
            $sb->appendLine('return [];');
            $builder->type('function')
                ->defs($sb . '')
                ->uses([
                    $cl => 'ctrl'
                ])
                ->desc(implode("\n", [
                    'profile list usage',
                    'array of profiles=>[auth-group]'
                ]));
            igk_io_w2file($file, $builder->render());
        };

        $bind[$dir . "/".IGKConstants::PROJECT_CONF_FILE] = function ($file) use ($options) {
            $config = new BalafonConfiguration;
            $config->name = ($options ? igk_conf_get($options, 'config/clAppName') ?? igk_conf_get($options, 'controller'): null)
            ?? igk_create_guid();
            $config->version = ($options ? igk_conf_get($options, 'version') :null) ?? '1.0';
            $config->author =( $options ?igk_conf_get($options, 'author') : null) ?? IGK_AUTHOR;
            $options = JSonEncodeOption::IgnoreEmpty();
            igk_io_w2file($file, JSon::Encode($config, $options));
        };
    }

    ///<summary>Represente help function</summary>
    public function help()
    {
        Logger::print("-");
        Logger::info("Make new Balafon PROJECT");
        Logger::print("-\n");
        Logger::print("Usage : ");
        Logger::print(App::Gets(App::GREEN, $this->command) . Logger::TabSpace . " name [options]\n");
        Logger::info("Options");
        foreach ($this->options as $k => $v) {
            Logger::print(App::Gets(App::GREEN, $k) . Logger::TabSpace . " $v \n");
        }
        Logger::print("");
    }
}
