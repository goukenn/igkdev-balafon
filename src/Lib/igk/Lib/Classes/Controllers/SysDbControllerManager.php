<?php
namespace IGK\Controllers;


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
    public static function getDataTableDefinition($table){
        $ctrl = igk_getctrl(SysDbController::class);
        $cnf = null;
        if ($driver = $ctrl->getDataAdapter()){ 
            $cnf = $driver->getDataTableDefinition($table);         
        }
        return $cnf;
    }
}