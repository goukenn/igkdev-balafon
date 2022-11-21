<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbUtilityHelper.php
// @date: 20221116 13:08:10
namespace IGK\Helper;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\Helpers
*/
class DbUtilityHelper{
    public static function InvokeOnStartDropTable(BaseController $controller, $autoclose=true){
        return self::InvokeEventCommand($controller, 'onStartDropTable', $autoclose);
    }
    public static function InvokeEventCommand(BaseController $controller, string $command, $autoclose=true){
        $rdb = $controller->getDb();
        if ($rdb){
            if (method_exists($rdb,  $command)) {
                $rdb->onStartDropTable();
            }
            if ($autoclose)
                $rdb->close();
        }
    }
}