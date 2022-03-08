<?php

namespace IGK\System\Database;

use IGK\Controllers\RootControllerBase;

class DbUtils{

    /**
     * order controller callback
     */
    const OrderController = self::class."::OrderController";

    public static function OrderController($a, $b){ 
        if (get_class($a) == \IGK\Controllers\SysDbController::class){                    
            return -1;
        }
        if (RootControllerBase::IsSystemController($a)){
            if (RootControllerBase::IsSystemController($b)){
                return strcmp(get_class($a), get_class($b));
            }
        }
        return 1; 
            
    }
}