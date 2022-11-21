<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbUtils.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IGK\Controllers\BaseController;
use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;

class DbUtils{

    /**
     * order controller callback
     */
    const OrderController = self::class."::OrderController";

    public static function OrderController($a, $b){ 
        if (get_class($a) == \IGK\Controllers\SysDbController::class){                    
            return -1;
        }
        if (get_class($b) == \IGK\Controllers\SysDbController::class){                    
            return -1;
        }
        if (RootControllerBase::IsSystemController($a)){
            if (RootControllerBase::IsSystemController($b)){
                return strcmp(get_class($a), get_class($b));
            }
        }
        return 1; 
            
    }
    public static function ResolvDefTableTypeName(string $table, BaseController $controller){
        $sys_prefix = igk_configs()->db_prefix ;
        $prefix = 
        ($controller instanceof SysDbController) ? $sys_prefix:
        $controller->getConfig('clDataTablePrefix', $sys_prefix );
        if ($prefix && (strpos($table, $prefix)===0)){
            $table = '%prefix%'.substr($table, strlen($prefix));
        }
        return $table;
    }
}