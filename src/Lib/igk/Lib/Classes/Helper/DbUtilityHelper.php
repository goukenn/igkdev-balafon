<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbUtilityHelper.php
// @date: 20221116 13:08:10
namespace IGK\Helper;
use IGK\Controllers\BaseController;

/**
* auto generate doc.
* @package IGK\Helpers
*/
class DbUtilityHelper{
    /**
    * Invoke on start drop table.
    * @param BaseController $controller
    * @param mixed $autoclose
    */
    public static function InvokeOnStartDropTable(BaseController $controller, $autoclose=true){
        return self::InvokeEventCommand($controller, 'onStartDropTable', $autoclose);
    }
    /**
    * Invoke event command.
    * @param BaseController $controller
    * @param string $command
    * @param mixed $autoclose
    */
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