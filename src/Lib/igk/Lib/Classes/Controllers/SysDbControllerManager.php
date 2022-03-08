<?php
namespace IGK\Controllers;

use IGKException;

class SysDbControllerManager{
    public static function getInstance(){
        static $sm ;
        if ($sm === null){
            $sm = new self();
        }
        return $sm;
    }
    private function __construct()
    {
        
    }
    /**
     * return tabldata table definition 
     * @param string $table tablename
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetDataTableDefinition($table){
        $ctrl = igk_getctrl(SysDbController::class);
        $cnf = null;
        if ($driver = $ctrl->getDataAdapter()){ 
            $cnf = $driver->getDataTableDefinition($table);         
        }
        return $cnf;
    }
    /**
     * get data table definition . 
     * @param mixed $ctrl 
     * @param mixed $tablename 
     * @return array [\
     *      "ColumnInfo"=> []\
     * ]
     * @throws IGKException 
     */
    public static function GetDataTableDefinitionFormController($ctrl, $tablename){
        if (($ctrl==null) || ($ctrl instanceof SysDbController)){
            $g = self::GetDataTableDefinition($tablename);
        }else 
            $g = $ctrl->getDataTableDefinition($tablename);
        return $g;
    }
}