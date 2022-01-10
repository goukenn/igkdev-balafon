<?php

namespace IGK\System\Console\Commands;
use IGK\System\Console\AppCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Helper\IO as IGKIO;
use IGK\Helper\IO;

use function igk_resources_gets as __; 
 
class MakeModuleCommand extends AppCommand{
    var $command = "--make:module"; 
    var $category = "make";
    var $desc  = "make new module.";
    var $options = [ 
        "--desc"=>"small description",
        "--git"=>"enabled git configuration",
    ]; 

   
    public function run($args, $command)
    {
        $command->exec = function($command, $name){
            Logger::print("generate module : " . $command->app::gets($command->app::GREEN, $name));

            $dir = igk_html_uri(igk_get_module_dir()."/".$name);

            if (is_dir($dir)){
                Logger::danger(__("Module already exist"));
                return -1;
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
                ->file("$file.php")
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
            $bind[$dir."/global.php"] = function($file, $command, $name){              
                $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
                $e_ns = str_replace("/", "\\", $name);

                $definition = self::EntryModuleDefinition($author, $e_ns);
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
                igk_io_w2file($file, json_encode($o, JSON_PRETTY_PRINT));
            };

            // $bind[$dir."/README.md"] = function($file, $command, $name){
            //     $s = implode("\n",[
            //         "# ".$name,
            //         igk_getv($command->options, "--desc")
            //     ]);
            //     igk_io_w2file($file, $s);
            // };

            // $bind[$dir."/.gitignore"] = function($file, $command, $name){
            //     $s = implode("\n", [
            //         ".gitignore",
            //         "**/.vscode",
            //         ".config",
            //     ]);
            //     igk_io_w2file($file, $s);
            // };
            if ($use_git){
                GitHelper::Generate($bind, $dir,$name, $author, igk_getv($command->options, "--desc"));           
            }

            foreach($bind as $path=>$callback){
                if (!file_exists($path)){
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
"entry_NS"=>"$e_ns",

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