<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SysDbControllerManager.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Controllers;

use IGKException;

/**
 * get system's database info
 * @package IGK\Controllers
 */
class SysDbControllerManager{
    public static function getInstance(){
        static $sm ;
        if ($sm === null){
            $sm = new self();
        }
        return $sm;
    }
    private function __construct(){        
    }
    /**
     * return tabldata table definition 
     * @param string $table tablename
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetDataTableDefinition(?string $table=null){
        $ctrl = igk_getctrl(SysDbController::class);
        $cnf = null;
        if ($driver = $ctrl->getDataAdapter()){ 
            $cnf = $driver->getDataTableDefinition($table);       
        }
        return $cnf;
    }
    /**
     * get data table definition . 
     * @param ?BaseController $ctrl 
     * @param string $tablename 
     * @return null|object|array|\IGK\System\Database\SchemaMigrationInfo [\
     *      "columnInfo"=> []\
     *      "tableRowReference"=>[]\
     * ]
     * @throws IGKException 
     */
    public static function GetDataTableDefinitionFormController(?BaseController $ctrl, string $tablename){
        $g = null;
        if (($ctrl===null) || ($ctrl instanceof SysDbController)){ 
            $g = self::GetDataTableDefinition($tablename);
        }else 
            $g = $ctrl->getDataTableDefinition($tablename);
        return $g;
       
    }
}