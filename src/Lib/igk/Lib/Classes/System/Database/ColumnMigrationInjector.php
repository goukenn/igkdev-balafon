<?php

namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;
use IGKEvents;

/**
 * 
 */
class ColumnMigrationInjector{
    /**
     * column info
     * @var DbColumnInfo
     */
    var $info;
    public function __construct(DbColumnInfo $info){
        $this->info = $info;
    }
    public function add(& $info){
        $t = & $info["ColumnInfo"];
        $t[$this->info->clName] = $this->info;  
    }
    public function remove(& $info){
        unset($info["ColumnInfo"][$this->info->clName]);
    }

    /**
     * inject column definitions
     * @param mixed $driver 
     * @param mixed $table 
     * @param mixed $callable 
     * @return void 
     */
    public static function Inject($driver, $table, $callable){
          // inject column info     
          igk_reg_hook(IGKEvents::FILTER_DB_SCHEMA_INFO, $fc = function($e)use($table, $callable){
              $tablename = $e->args["tablename"];
              if ($table != $tablename){
                  return;
                } 
            $v_info = & $e->args["info"]; 
            $callable($v_info); 
        }); 
        $driver->getDataTableDefinition($table); 
        igk_unreg_hook(IGKEvents::FILTER_DB_SCHEMA_INFO, $fc); 
    }
}