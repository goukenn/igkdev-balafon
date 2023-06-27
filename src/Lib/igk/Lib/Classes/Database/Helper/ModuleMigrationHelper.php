<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleMigrationHelper.php
// @date: 20230617 00:36:22
namespace IGK\Database\Helper;

use IGK\System\Controllers\ApplicationModules;

///<summary></summary>
/**
* use to handle module database migration.
* @package IGK\Database\Helper
*/
class ModuleMigrationHelper{
    /**
     * this method will inject database migration to core system. by update the default system manager.
     * @param ApplicationModules $module 
     * @return void 
     */
    public static function Migrate(ApplicationModules $module){
        // + | ----------------------------------------------------------------------
        // + | Get file and inject migration to array list.
        // + | by defeault initialize database create module fields.
        // + | ----------------------------------------------------------------------


    }
}