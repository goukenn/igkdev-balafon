<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbSchemaDefinitions.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Database;

use IGK\Helper\Activator;

abstract class DbSchemaDefinitions{
    /**
     * store schema definition [DRIVER][CTRL][TABLE INFO]
     * @var mixed
     */
    private static $sm_def;
    const CACHE_FILE = ".data-schema.definition.cache";

    //schema definition ADAPTER/CONTROLLER TABLE INFO -- 
    /**
     * get table definition 
     * @param string $ad_name 
     * @param string $table 
     * @return mixed 
     */
    public static function & GetDataTableDefinition(string $ad_name, string $table){
        $i = 0;
        if (is_null(self::$sm_def)){
            if (is_file($file = igk_io_cachedir()."/".self::CACHE_FILE)){
                $s = file_get_contents($file);
                if (!empty($s)){
                    if (($g = unserialize($s)) === false){
                        @unlink($s);
                    }else{
                        self::$sm_def = $g;
                        $i=1;
                    }
                }
            }
            !$i && (self::$sm_def = []);
            self::$sm_def["resolved"] = [];
        }        
        $key = sha1($ad_name.":".$table);
        if ($info = igk_getv(self::$sm_def["resolved"], $key)){
            return $info[0];
        }        
        $result = null;         
        if (isset(self::$sm_def[$ad_name])){
            $d = self::$sm_def[$ad_name];
            foreach($d as $ctrl=>$info){ 
                if ($inftable = igk_getv($info->tables, $table)){
                    // init resolved table definition - 
                    if (!isset($inftable->tableRowReference)){
                        $inftable->columnInfo = array_map([self::class, 'ToColumnInfoDefinition'], (array) $inftable->columnInfo);
                        $inftable->tableRowReference = igk_array_object_refkey($inftable->columnInfo, IGK_FD_NAME);
                    } 
                    self::$sm_def["resolved"][$key] = [
                        $inftable, $ctrl
                    ];
                    $result = $inftable;
                    // if ($table == 'tbigk_users'){
                    //     igk_debug_wln(__FILE__.":".__LINE__, $result);
                    // }
                    // igk_debug_wln("get definition d ".$table. " init ".$i);
                    break;
                }
            } 
        }
        return $result;
    }
    static function ToColumnInfoDefinition($m){        
        return new DbColumnInfo((array)$m);
    }
    /**
     * register schema definition 
     * @param mixed $ad_name 
     * @param mixed $table 
     * @param mixed $info 
     * @return void 
     */
    public static function RegisterDataTableDefinition($ad_name, $table, & $info){
        if (!isset(self::$sm_def[$ad_name])){
            self::$sm_def[$ad_name] = [];
        }
        self::$sm_def[$ad_name][$table] = & $info; 
    }
}