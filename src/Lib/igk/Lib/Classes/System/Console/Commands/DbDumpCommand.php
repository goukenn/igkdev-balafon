<?php

namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Helper\Utility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Helper;
use IGKEvents;
use IGKNonVisibleControllerBase;



/**
 * initialize data schema
 * @package IGK\System\Console\Commands
 */
class DbDumpCommand extends AppExecCommand{
    var $command = "--db:dump";
    var $desc = "dump db from schema"; 
    var $category = "db";

    var $options = [
        "controller"=>"controller to target",
        "file"=>"file to export",
        "-option"=>"export type xml|json"
    ];

    private $_entries;

    public function exec($command,  $ctrl=null, $file=null)
    {    
         
        if (!$ctrl  || !($ctrl = igk_getctrl($ctrl))){
            Logger::danger("controller required");
            return -1;
        
        }
        $type = igk_getv($command->options, "--type", "json");

        $ctrl::register_autoload();

        $this->_entries = [];
        $gen = $this->getGenerator($type);

        $man  = \IGK\System\IO\Helper::GenerateModel($ctrl, function()use($gen){
            if ($gen === $this){
                $gen->_generate(...func_get_args());
            }
            else {
                $gen->generate(...func_get_args());
            }
        }
        );

        igk_wl(json_encode($this->_entries, JSON_PRETTY_PRINT).PHP_EOL); 
        // Logger::success("Schema complete");
        return 0;
    }
    public function getGenerator($type){ 
        return $this; 
    }
    public function _generate($ctrl, $table, $info, & $manifest = []){

        /**
         * @var \IGK\System\Database\MySQL\DataAdapter $ad data adapter
         * @var \IGK\System\Database\MySQL\IGKMySQLQueryResult $g query result
         */
        $ad = $ctrl::getDataAdapter(); 
        $tb = $ctrl::resolv_table_name($table);
        $g = $ad->selectAll($table);
        $rest = []; 
        if ($g->RowCount){
            foreach($g->getRows() as $r){
                $rest[] = $r->to_array();
            }
        } 
        $this->_entries[$tb] = $rest;
    }
    public function help(){
        parent::help();
        Logger::print("file [-option:[json]]");
    }
}

