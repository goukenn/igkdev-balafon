<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InitDataSchemaSQLCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Helper\Utility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKEvents;
use IGKNonVisibleControllerBase;



/**
 * initialize data schema
 * @package IGK\System\Console\Commands
 */
class InitDataSchemaSQLCommand extends AppExecCommand{
    var $command = "--db:schema";
    var $desc = "get db schemas"; 
    var $category = "db";

    var $options = [
        "controller"=>"controller to target",
        "file"=>"file to export",
        "-option:[xml|json]"=>"export type xml|json"
    ];

    public function showUsage(){
        Logger::print("--db:schema Controller [file]");
    }
    public function exec($command,  $ctrl=null, $file=null)
    {    
        require_once(__DIR__."/.InitDataSchemaController.pinc");
      
        if (!$ctrl  || !($ctrl = igk_getctrl($ctrl, false))){
            $ctrl = new InitDataSchemaController();
        }
        if ($file===null){ 
            $file = $ctrl::getDataSchemaFile();
        }
        if (!$file || !file_exists($file)){
            Logger::danger("data schema file not found");
            return -1;
        }
        $options = igk_getv($command->options, "-option");
        $resolvname = $options != "json";       
        $schema = igk_db_load_data_schemas($file, $ctrl, $resolvname);
        if (!$schema){
            Logger::danger("schema not valid");
            return -2;
        }
        igk_set_env(IGK_ENV_DB_INIT_CTRL, $ctrl); 
        $tables = igk_getv($schema, "tables"); 
        switch( $options )
        {
            case 'json':
                echo Utility::To_JSON($tables, [
                    "ignore_empty"=>1,
                ], JSON_PRETTY_PRINT);
                igk_exit();
        } 
        igk_hook(IGKEvents::HOOK_DB_INIT_ENTRIES, array($ctrl));
        igk_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, ["controller"=>$ctrl]);
        Logger::success("Schema complete");
        return 0;
    }
    public function help(){
        parent::help();
        Logger::print("file [-option:[json]]");
    }
}

