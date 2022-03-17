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
use IGK\Controllers\ControllerInitListener;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\Resources\R;
use \IGKControllerManagerObject;
use IGK\System\Console\App; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\SchemaBuilder;
use IGK\System\Http\Route;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;

use function igk_resources_gets as __;
use stdClass;
///<summary>Represente class: MakeProjectCommand</summary>
class MakeProjectCommand extends AppExecCommand{
    var $category="make", $command="--make:project", $desc="make new project.", $options=[
        "--type:[type]"=>"project type. default is ApplicationController::class", 
        "--entryNamespace:[namespace]"=>"define project entry NS", 
        "--desc:[desc]"=>"project description", 
        "--configs"=>"setup project config", 
        "--git"=>"flag to enabled git configuration", 
        "--conf-default"=>"flag to allow default configuration",
        "--force"=>"flag force create project",
        "--version"=>"application version"
    ];
    ///<summary>Represente exec function</summary>
    ///<param name="command"></param>
    ///<param name="name" default=""></param>
    public function exec($command, $name=""){
        if(empty($name)){
            return false;
        }
        if($command->app->getConfigs()->isTemp){
            Logger::danger("Create Project in temporary folder is not allowed. please setup your environment");
            return false;
        }
        $name = StringUtility::CamelClassName($name);
        $dir = igk_io_projectdir()."/".$name;
        Logger::info(__("Make project ... {0}",  $name));
      
        
        $author=$command->app->getConfigs()->get("author", IGK_AUTHOR);
        $type=igk_getv($command->options, "--type", \IGK\Controllers\ApplicationController::class);
        $e_ns=igk_getv($command->options, "--entryNamespace", null);
        $desc=igk_getv($command->options, "--desc", null);
        $configs=igk_getv($command->options, "--configs", null);
        $no_config=property_exists($command->options, "--noconfig");
        $use_git=property_exists($command->options, "--git");
        $force = property_exists($command->options, "--force"); 
        $ns=igk_str_ns($name);
        $pname=basename(igk_io_dir($ns));
        $clname=ucfirst($pname)."Controller";
        $dir=igk_io_projectdir()."/{$pname}";
        igk_init_controller(new ControllerInitListener($dir, 'appsystem'));
        $defs="";


        
        if(!empty($e_ns)){
            $e_ns=str_replace("\\\\", "\\", igk_str_ns($e_ns));
            $defs .= "protected function getEntryNamespace(){ return {$e_ns}::class; }";
        }
        IO::CreateDir(implode("/", [$dir,IGK_DATA_FOLDER,IGK_RES_FOLDER]));
        $bind=[];
        $fname=$clname.".php";
        $fullClassName = implode("\\", array_filter([$e_ns , $clname])); 


        $bind[$dir."/.global.php"]=function($file) use ($author){
            $builder=new PHPScriptBuilder();
            $builder->type("function")->author($author)->desc("global function");
            igk_io_w2file($file, $builder->render());
        };
        $bind[$dir."/$fname"] =function($file) use ($type, $author, $defs, $desc, $clname, $fname){
            $builder=new PHPScriptBuilder();
            $builder->type("class")->name($clname)
            ->author($author)->defs($defs)
            ->doc("Controller entry point")
            ->file($fname)
            ->desc($desc)
            ->extends($type);
            igk_io_w2file($file, $builder->render());
        };
        $this->_bind_articles($bind, $dir);


        $defaultsrc=<<<EOF
/**
 * @var object \$t
 * @var object \$doc
*/
\$doc->title = \$this->getName();
\$t->clearChilds();
\$t->div()->h1()->Content = "Hello !!! ".\$this->getName();
EOF;

        $config=new stdClass();
        if(!$no_config && class_exists($type)){
            if(method_exists($type, "GetAdditionalDefaultViewContent")){
                $defaultsrc=$type::GetAdditionalDefaultViewContent();
                if(strpos($defaultsrc, "<?php") === 0){
                    $defaultsrc=substr($defaultsrc, 5);
                }
            }
            Logger::info("Setup general configuration");
            $prop=(object)["name"=>$pname, "description"=>$desc];
            $tab=property_exists($command->options, "--conf-default") ? igk_sys_getdefaultctrlconf(): [];
            if(method_exists($type, "GetAdditionalConfigInfo")){
                if($tabInfo=$type::GetAdditionalConfigInfo()){
                    $tab=array_merge($tab, $tabInfo);
                }
            }
            ksort($tab);
            foreach($tab as $key=>$value){
                $def=null;
                if($def=(is_array($value) ? igk_getv($value, "default"): $value)){
                    if(igk_is_closure($def)){
                        $def=$def($prop);
                    }
                }
                $config->$key=igk_getsv(readline("configure : ".$key. " = "), $def);
            }
        }
        if($config){
            $bind[igk_io_joinpath($dir, IGK_DATA_FOLDER, IGK_CTRL_CONF_FILE)
            ]=function($file) use ($config){
                $d=igk_createxml_config_data($config);
                igk_io_w2file($file, $d->render((object)["Indent"=>true]));
            };
        }
        $bind[$dir."/".IGK_VIEW_FOLDER.
        "/default.phtml"]=function($file) use ($author, $dir, $defaultsrc){
            $builder=new PHPScriptBuilder();
            $builder->type("function")->file(basename($file))->desc("default balafon view")->author($author)->defs($defaultsrc);
            igk_io_w2file($file, $builder->render());
        };
        $bind[$dir."/".IGK_DATA_FOLDER.
        "/data.schema.xml"]=function($file) use ($author, $dir){
            $build=new SchemaBuilder();
            igk_io_w2file($file, $build->render((object)["Context"=>"XML", "Indent"=>true]));
        };

        // Lib/autoload.php
        $bind[$dir."/".IGK_LIB_FOLDER."/autoload.php"] = function($file){
            $builder = new PHPScriptBuilder();
            $builder->uses(Route::class)
            ->type("function")
            ->file(basename($file))
            ->defs(implode("\n", [
                "// on register_autoload initialize",
            ]));       
            igk_io_w2file($file, $builder->render());
        };

        // configuration 
        $bind[$dir."/".IGK_CONF_FOLDER."/routes.php"] = function($file){
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

        if($configs)
            $bind[$dir."/".IGK_DATA_FOLDER."/".IGK_CTRL_CONF_FILE]=function($file) use ($author, $dir, $configs){
            $parse=(object)[];
            array_map(function($m) use ($parse){
                list($k, $v)
                =explode("=", $m);
                igk_conf_set($parse, $v, $k);
                return;
            }
            , $configs);
            $c=igk_createxml_config_data($parse);
            igk_io_w2file($file, $c->render((object)["Context"=>"XML", "Indent"=>true]));
        };
        // + | init environment - data
        $bind[$dir."/phpunit.xml.dist"] = function($file)use($fullClassName){
            $c_app =  igk_io_expand_path("%lib%");
            // igk_wln_e(__FILE__.":".__LINE__, $c_app,igk_io_collapse_path($file), igk_io_expand_path(igk_io_collapse_path($file)));
            $n = igk_create_xmlnode("phpunit");
            $n["xmlns:xsi"] = "http://www.w3.org/2001/XMLSchema-instance"; 
            $n["xsi:noNamespaceSchemaLocation"]=igk_io_expand_path("%packages%")."/vendor/phpunit/phpunit/phpunit.xsd";
            $n["bootstrap"] = "./Lib/Tests/autoload.php";
            $n["colors"] = "true";
            $suites = $n->add("testsuites");
            $ts =  $suites->add("testsuite");
            $ts["name"] = "projects";
            $ts->add("directory")->Content = "./Lib/Tests";
            $env = $n->php();
            $env->add("env")->setAttributes(["name" => "IGK_BASE_DIR", "value" => IGK_BASE_DIR]);
            $env->add("env")->setAttributes(["name" => "IGK_APP_DIR", "value" => IGK_APP_DIR]);
            $env->add("env")->setAttributes(["name" => "IGK_TEST_CONTROLER", "value" => $fullClassName]);
            igk_io_w2file($file, $n->render((object)["Indent"=>true]));
        };
        

        $bind[$dir."/phpunit-watcher.yml"] = function($file){
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
        
        $bind[$dir."/".IGK_LIB_FOLDER."/Tests/autoload.php"] = function($file)use($name){
            $builder = new PHPScriptBuilder();
            $e_ns = str_replace("/", "\\", $name);
            $builder->type('function')->defs(
                implode("\n",
                    [
                        // '$_ENV["igk_test_module"] = \\'.$e_ns.'::class;',
                        'require_once $_ENV["IGK_APP_DIR"]."/Lib/igk/Lib/Tests/autoload.php";'
                    ]
            ));
            igk_io_w2file($file, $builder->render());
        };

           
        $bind[$dir."/".IGK_LIB_FOLDER."/Tests/".$clname."Test.php"] = function($file)use($name, $clname){
            $builder = new PHPScriptBuilder();
            $e_ns = str_replace("/", "\\", $name);
            $builder->type('class')
                ->namespace($e_ns."\\Tests")
                ->name($clname."Test")
                ->file(basename($file))
                ->desc($name." controller test ")
                ->extends(\IGK\Tests\Controllers\ControllerBaseTestCase::class)
                ->defs( implode("\n", [
                    "public function test_init(){",
                    "   \$this->assertTrue(true);",
                    "}"
                ]))
            ;
            igk_io_w2file($file, $builder->render());
        };

        if($use_git){
            ($force || !is_dir($dir."/.git")) && 
            GitHelper::Generate($bind, $dir, $name, $author, $desc, [
                "phpunit-watcher.yml",
                "phpunit.xml.dist",
                ".phpunit.*",
                ".phpunit.result.cache",
            ]);
        }
        foreach($bind as $n=>$c){
            if($force || !file_exists($n)){
                $c($n, $command);
            }
        }
    
        IGKControllerManagerObject::ClearCache(null, true);
        Logger::info("output: ".$dir);
        Logger::success("done\n");
    }
    protected function _store_article($f){
        $builder=new PHPScriptBuilder();
        $builder->type("function")->file(basename($f));
        igk_io_w2file($f, $builder->render());
    }
    protected function _bind_articles(array & $bind, $dir){
        $tab = explode("|", R::GetSupportLangRegex());
        $outdir = $dir."/".IGK_ARTICLES_FOLDER;
        $fc = Closure::fromCallable([$this,"_store_article"])->bindTo($this);
        foreach(explode("|", "about|presentation|confidentiality") as $l){
            foreach($tab as $b){
                $bind[$outdir."/".$l.".".$b.".phtml"] = $fc; 
            }
        }
    }
    ///<summary>Represente help function</summary>
    public function help(){
        Logger::print("-");
        Logger::info("Make new Balafon PROJECT");
        Logger::print("-\n");
        Logger::print("Usage : ");
        Logger::print(App::gets(App::GREEN, $this->command).Logger::TabSpace. " name [options]\n");
        Logger::info("Options");
        foreach($this->options as $k=>$v){
            Logger::print(App::gets(App::GREEN, $k). Logger::TabSpace. " $v \n");
        }
        Logger::print("");
    }
}
