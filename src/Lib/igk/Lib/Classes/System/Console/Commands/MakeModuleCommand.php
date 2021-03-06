<?php

namespace IGK\System\Console\Commands;
use IGK\System\Console\AppCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Helper\IO as IGKIO;
use IGK\Helper\IO;
use IGK\System\Configuration\CoreGeneration;
use IGK\System\IO\StringBuilder;

use function igk_resources_gets as __; 
 
class MakeModuleCommand extends AppCommand{
    var $command = "--make:module"; 
    var $category = "make";
    var $desc  = "make new module.";
    var $options = [ 
        "--desc"=>"small description",
        "--git"=>"enabled git configuration",
        "--force"=>"force creation if directory exists",
        "--version"=>"setup current version"
    ]; 

   
    public function run($args, $command)
    {
        $command->exec = function($command, $name){
            Logger::print("generate module : " . $command->app::gets($command->app::GREEN, $name));

            $dir = igk_html_uri(igk_get_module_dir()."/".$name);
            $force = property_exists($command->options, "--force"); 
            if (is_dir($dir)){
                if ($force){
                    // IO::RmDir($dir);
                }else{
                    Logger::danger(__("Module already exist"));
                    return -1;
                }
            }
            IO::CreateDir($dir."/".IGK_VIEW_FOLDER);
            IO::CreateDir($dir."/".IGK_STYLE_FOLDER);
            IO::CreateDir($dir."/".IGK_LIB_FOLDER."/Classes");
            IO::CreateDir($dir."/".IGK_LIB_FOLDER."/Tests");
            IO::CreateDir($dir."/".IGK_CONF_FOLDER);
            IO::CreateDir($dir."/".IGK_DATA_FOLDER);
            IO::CreateDir($dir."/".IGK_SCRIPT_FOLDER);
            if (file_exists($dir."/.global.php")){
                igk_io_w2file($dir."/.global.php", "<?php\n");
            }
            $use_git = property_exists($command->options, "--git");
            $bind = [];
            $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
            $bind[$dir."/.module.pinc"] = function($file, $command, $name){
                $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
                $e_ns = str_replace("/", "\\", $name);

                $definition = self::EntryModuleDefinition($author, $e_ns);
                $builder = new PHPScriptBuilder();
                $builder
                ->author($author)
                ->type("function")
                ->file(igk_io_collapse_path("{$file}.php"))
                ->name($name)  
                ->desc(igk_getv($command->options, "--desc"))
                ->defs("// + module definition\nreturn [\n$definition\n];")
                ->namespace($e_ns);
                igk_io_w2file($file, $builder->render());
            };
            $bind[$dir."/".IGK_STYLE_FOLDER."/default.pcss"] = Utility::TouchFileCallback("<?php \n"); 
            $bind[$dir."/".IGK_SCRIPT_FOLDER."/default.js"] = Utility::TouchFileCallback("// default entry script \n"); 
            $bind[$dir."/".IGK_SCRIPT_FOLDER."/default.bjs"] = Utility::TouchFileCallback("// default entry to be merge script \n"); 

            $bind[$dir."/".IGK_DATA_FOLDER."/".IGK_CTRL_CONF_FILE] = Utility::TouchFileCallback(igk_create_xmlnode(IGK_CNF_TAG)->render()); 
            $bind[$dir."/".IGK_DATA_FOLDER."/".IGK_SCHEMA_FILENAME] = Utility::TouchFileCallback(igk_create_xmlnode(IGK_SCHEMA_TAGNAME)->render()); 
            $bind[$dir."/.global.php"] = function($file, $command, $name){              
                $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
                $e_ns = str_replace("/", "\\", $name);
 
                $builder = new PHPScriptBuilder();
                $builder
                ->author($author)
                ->type("function")
                ->file(igk_io_collapse_path($file))
                ->name($name)  
                ->desc(igk_getv($command->options, "--desc"))
                ->defs("// + module entry file ");
                igk_io_w2file($file, $builder->render());
            };

            $bind[$dir."/module.json"] = function($file, $command, $name){
                $o = igk_createobj();
                $o->name = $name;
                $o->author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
                $o->version = igk_getv($command->options, "--version", "1.0");
                $o->require = igk_getv($command->options, "--require");
                igk_io_w2file($file, json_encode($o, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            };

            $bind[$dir."/Lib/Tests/autoload.php"] = function($file)use($name){
                $builder = new PHPScriptBuilder();
                $e_ns = str_replace("/", "\\", $name);
                $gen = new CoreGeneration();
                $builder->type('function')->defs(
                    implode("\n",
                        [ 
                            $gen->GetTestRequireAutoload()
                        ]
                ));
                igk_io_w2file($file, $builder->render());
            };

            $bind[$dir."/phpunit.xml.dist"] = function($file)use($name){
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
                $env->add("env")->setAttributes(["name" => "IGK_TEST_MODULE", "value" => $name]);
                igk_io_w2file($file, $n->render((object)["Indent"=>true]));
            };
            

            $bind[$dir."/phpunit-watcher.yml"] = function($file)use($name){
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
            

            if ($use_git){
                ($force || !is_dir($dir."/.git")) && 
                GitHelper::Generate($bind, $dir,$name, $author, igk_getv($command->options, "--desc"),
                [
                    "phpunit-watcher.yml",
                    "phpunit.xml.dist",
                    ".phpunit.*",
                    ".phpunit.result.cache",
                ]
                );           
            }

            foreach($bind as $path=>$callback){
                if ($force || !file_exists($path)){
                    $callback($path, $command, $name); 
                }
            }
            Logger::info("Location: ".$dir);  
            Logger::success(__("done"));
        };
    
    }
    static function EntryModuleDefinition($author=null, $e_ns=null, $version="1.0" ){
        
        return <<<EOF
//------------------------------------------------
// define entry name space
//
"entry_NS"=>\\{$e_ns}::class,

//------------------------------------------------
// version
//
"version"=>"{$version}",

//-------------------------------------------------
// author
//
"author"=>"{$author}"
EOF;
    }
}