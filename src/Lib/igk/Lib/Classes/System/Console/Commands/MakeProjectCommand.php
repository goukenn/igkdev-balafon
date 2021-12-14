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

use \ApplicationController;
use \IGKControllerManagerObject;
use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\SchemaBuilder;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKCtrlInitListener;
use IGK\Helper\IO as IGKIO;
use stdClass;
///<summary>Represente class: MakeProjectCommand</summary>
class MakeProjectCommand extends AppExecCommand{
    var $category="make", $command="--make:project", $desc="make new project.", $options=[
        "--type:[type]"=>"project type. default is ApplicationController::class", 
        "--entryNamespace:[namespace]"=>"define project entry NS", 
        "--desc:[desc]"=>"project description", 
        "--configs"=>"setup project config", 
        "--git"=>"flag to enabled git configuration", 
        "--conf-default"=>"flag to allow default configuration"
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
        Logger::info("make project ...".$name);
        $author=$command->app->getConfigs()->get("author", IGK_AUTHOR);
        $type=igk_getv($command->options, "--type", ApplicationController::class);
        $e_ns=igk_getv($command->options, "--entryNamespace", null);
        $desc=igk_getv($command->options, "--desc", null);
        $configs=igk_getv($command->options, "--configs", null);
        $use_git=property_exists($command->options, "--git");
        $ns=igk_str_ns($name);
        $pname=basename(igk_io_dir($ns));
        $clname=ucfirst($pname)."Controller";
        $dir=igk_io_projectdir()."/{$pname}";
        igk_init_controller(new IGKCtrlInitListener($dir, 'appsystem'));
        $defs="";
        if(!empty($e_ns)){
            $e_ns=str_replace("\\\\", "\\", igk_str_ns($e_ns));
            $defs .= "protected function getEntryNamespace(){ return {$e_ns}::class; }";
        }
        $bind=[];
        $fname=$clname.".php";
        $bind[$dir.
        "/.global.php"]=function($file) use ($author){
            $builder=new PHPScriptBuilder();
            $builder->type("function")->author($author)->desc("global function");
            igk_io_w2file($file, $builder->render());
        };
        $bind[$dir.
        "/$fname"]=function($file) use ($type, $author, $defs, $desc, $clname, $fname){
            $builder=new PHPScriptBuilder();
            $builder->type("class")->name($clname)->author($author)->defs($defs)->doc("Controller entry point")->file($fname)->desc($desc)->extends($type);
            igk_io_w2file($file, $builder->render());
        };
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
        if(class_exists($type)){
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
        if($configs)
            $bind[$dir."/".IGK_DATA_FOLDER."/".
        IGK_CTRL_CONF_FILE]=function($file) use ($author, $dir, $configs){
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
        if($use_git){
            GitHelper::Generate($bind, $dir, $name, $author, $desc);
        }
        foreach($bind as $n=>$c){
            if(!file_exists($n)){
                $c($n, $command);
            }
        }
        register_shutdown_function(function(){
            IGKControllerManagerObject::ClearCache(null, true);
        });
        Logger::info("output: ".$dir);
        Logger::success("done\n");
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
