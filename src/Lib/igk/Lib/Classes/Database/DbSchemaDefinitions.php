<?php
namespace IGK\Database;

abstract class DbSchemaDefinitions{
    static $sm_def = [];

    public static function & GetDataTableDefinition($ad_name, $table){
        $g = null;
        if (isset(self::$sm_def[$ad_name])){
            $d = self::$sm_def[$ad_name];
            if (isset($d[$table])){
                $g = & $d[$table];
            }
        }
        return $g;
    }
    public static function RegisterDataTableDefinition($ad_name, $table, & $info){
        if (!isset(self::$sm_def[$ad_name])){
            self::$sm_def[$ad_name] = [];
        }
        self::$sm_def[$ad_name][$table] = & $info; 
    }
}