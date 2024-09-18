<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ColumnMigrationInjector.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;
use IGK\Helper\JSon;
use IGKEvents;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * migration injector used to inject defenition on column migration 
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
        $t = & $info->columnInfo;
        $nk = $this->info->clName;
        if (in_array(strtolower($nk) , explode('|', strtolower(implode('|', array_keys($t))))))
        {
            $pts = ['ignore_empty'=>true];
            igk_is_debug() && 
               ((igk_ilog('already contain column info ['.$nk.']') && 0) ||
               (igk_ilog(JSon::Encode($this->info, $pts, JSON_PRETTY_PRINT)) && 0)); 
            return;
        } 
        $t[$nk] = $this->info;  
    }
    public function remove(& $info){
        unset($info->columnInfo[$this->info->clName]);
    }

    /**
     * inject column definitions
     * @param mixed $driver 
     * @param string $table 
     * @param callable $callable 
     * @return void 
     */
    public static function Inject($driver, string $table, $callable){
          // + | ---------------------------------------------
          // + | inject column info     
          // + | 
          igk_reg_hook(IGKEvents::FILTER_DB_SCHEMA_INFO, $fc = function($e)use($table, $callable){
              $tablename = $e->args["tablename"];
              if ($table != $tablename){
                  return;
                } 
            $v_info = & $e->args["info"]; 
            
            $callable($v_info); 
        }); 
        // load tatble definition an dive into injection table 
        $driver->getDataTableDefinition($table); 
        igk_unreg_hook(IGKEvents::FILTER_DB_SCHEMA_INFO, $fc); 
    }
}