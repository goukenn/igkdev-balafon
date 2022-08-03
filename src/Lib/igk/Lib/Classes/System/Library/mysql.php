<?php
// @author: C.A.D. BONDJE DOUE
// @filename: mysql.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Library;
use IGK\System\Database\MySQL\DataAdapter as MySQLDataAdapter;
class mysql extends \IGKLibraryBase{
    public function init():bool{
        if (!extension_loaded("mysqli")){
            return false;
        } 
    
        require_once IGK_LIB_CLASSES_DIR ."/System/Database/MySQL/DataAdapterBase.php";
        // require_once IGK_LIB_CLASSES_DIR ."/System/Database/MySQL/DataAdapter.php";
        require_once IGK_LIB_CLASSES_DIR."/System/Database/MySQL/igk_mysql_db.php";        
        // initialize function
        \IGK\Database\DataAdapterBase::Register( [
            IGK_MYSQL_DATAADAPTER=> MySQLDataAdapter::class
        ]);
        if (!class_exists("IGKMYSQLDataAdapter",false)){
            class_alias(\IGK\System\DataBase\MySQL\DataAdapter::class, 
            "IGKMYSQLDataAdapter");
        }
        return true;
    }
}