<?php

namespace IGK\System\IO;

use IGK\Controllers\BaseController;
use IGKSystemController;

final class Helper{
    public static function GenerateModel(BaseController $ctrl, callable $callback, $force=false){
        $manifest = [];
        $file = $ctrl->getDBConfigFile();
        if (!file_exists($file)){
            die("schema file not found.");
        }
        
        $schema = igk_db_load_data_schemas($file, $ctrl, true);
        $tables = igk_getv($schema, "tables"); 
        foreach($tables as $t=>$info){
            $callback($ctrl, $t, $info, $manifest, $force);
        }
        // something to do with the manifest
        return $manifest;
    }
    public static function GenerateModelFromFile($file, callable $callback, $force=false){
        $manifest = [];
        $ctrl = igk_getctrl(IGKSystemController::class);
        $schema = igk_db_load_data_schemas($file, null, true);
        $tables = igk_getv($schema, "tables"); 
        foreach($tables as $t=>$info){
            $callback($ctrl, $t, $info, $manifest, $force);
        }
        // something to do with the manifest
        return $manifest;
    }
}