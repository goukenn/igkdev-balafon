<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerDbExtensionTrait.php
// @date: 20230703 14:24:34
namespace IGK\Controllers\Traits;

use IGK\Controllers\BaseController;
use IGK\Database\DbSchemasConstants;
use IGK\Helper\DbUtilityHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\ColumnMigrationInjector;
use IGK\System\Database\MigrationHandler;
use IGKEvents;

///<summary></summary>
/**
* 
* @package IGK\Controllers\Traits
*/
trait ControllerDbExtensionTrait{

     ///<summary>drop list data base</summary>
    /**
     * drop list data base
     */
    public static function dropDb(BaseController $controller, $navigate = 1, $force = false)
    {

        $ctrl = $controller;

        $func = function () use ($ctrl) {
            // + | --------------------------------------------------------------------
            // + | raise utility command
            // + |
            DbUtilityHelper::InvokeOnStartDropTable($ctrl, true);
            igk_hook(IGKEvents::HOOK_DB_START_DROP_TABLE, $ctrl);
        };
        $_vinit = 0;

        if ($force || $ctrl->getCanInitDb()) {
            if (!$ctrl->getUseDataSchema()) {
                $db = self::getDataAdapter($ctrl);
                if (
                    !empty($table = $ctrl->getDataTableName()) &&
                    $ctrl->getDataTableInfo() &&
                    $db && $db->connect()
                ) {
                    $table = igk_db_get_table_name($table, $ctrl);
                    $func();
                    $db->dropTable($table); // ctrl->getDataTableName());
                    $db->close();
                }
            } else {
                $tb = $ctrl::loadDataFromSchemas();
                $db = self::getDataAdapter($ctrl);
                if ($force || ($db && $db->connect())) {
                    $migHandle = new MigrationHandler($controller);
                    $migHandle->down();

                    $v_tblist = [];
                    if ($tables = igk_getv($tb, "tables")) {
                        foreach (array_keys($tables) as $k) {
                            $v_tblist[$k] = $k;
                        }
                    }
                    $func();
                    igk_hook(IGKEvents::HOOK_DB_MIGRATE, [
                        'type'=>DbSchemasConstants::Downgrade,
                        'ctrl'=>$controller 
                    ]);
                    $db->dropTable($v_tblist);
                    $_vinit = 1;
                    $db->close();
                }
            }
        }
        if ($navigate) {
            $controller->View();
            igk_navtocurrent();
        }
        return $_vinit;
    }
  /**
     * remove column 
     * @param BaseController $ctrl 
     * @param string $table 
     * @param mixed|IDbColumnInfo|object $info 
     * @return mixed 
     * @throws IGKException 
     */
    public static function db_rm_column(BaseController $ctrl, string $table, $info)
    {
        $is_obj = is_object($info);
        if ($is_obj) {
            $name = $info->clName;
        } else {
            $name = $info;
        }
        Logger::warn('remove column: '.$table. ' '.$name);
        $ad = self::getDataAdapter($ctrl);
        if ($ad->exist_column($table, $name)) {
            if (
                (($is_obj && $info->clLinkType) || is_string($info)) &&
                ($query = $ad->grammar->remove_foreign($table, $name))
            ) {
                // remove foreign queries
                $ad->sendMultiQuery($query);
            }
            $query = $ad->grammar->rm_column($table, $name);
            return $ad->sendQuery($query);
        }
        return false;
    }
    /**
     * add column
     * @param IGK\Controllers\Traits\BaseController $ctrl 
     * @param mixed $table 
     * @param mixed $info 
     * @param mixed $after 
     * @return true|void 
     */
    public static function db_add_column(BaseController $ctrl, $table, $info, $after = null)
    {
        $ad = self::getDataAdapter($ctrl);
        ColumnMigrationInjector::Inject($ad, $table, [new ColumnMigrationInjector($info), "add"]);
        if (!$ad->exist_column($table, $info->clName)) {
            if ($query = $ad->grammar->add_column($table, $info, $after)) {
                if ($ad->sendQuery($query)) {
                    if ($info->clLinkType) {
                        $query_link = $ad->grammar->add_foreign_key($table, $info);
                        $ad->sendQuery($query_link);
                    }
                    //
                    return true;
                }
            }
        }
    }

    /**
     * rename column extension macros
     */
    public static function db_rename_column(BaseController $ctrl, $table, $column, $new_column_name)
    {
        $ad = self::getDataAdapter($ctrl); 
        if ($ad->exist_column($table, $column)) {
            if (!$ad->exist_column($table, $new_column_name)) {
                if ($query = $ad->grammar->rename_column($table, $column, $new_column_name)) {
                    return $ad->sendQuery($query);
                } else {
                    $n_info = igk_getv($ad->getColumnInfo($table, $column), $column);
                    if ($n_info){
                        if (empty($n_info->clName)){
                            $n_info->clName = $column;
                        }
                    }
                    if ($n_info && ($query = $ad->grammar->change_column($table, $n_info, $new_column_name))) {
                        return $ad->sendQuery($query);
                    }
                }
            } else {
                if(strtolower($column) == strtolower($new_column_name)){
                    // new column resolving. changing with case sensitivity
                     $query = $ad->grammar->rename_column($table, $column, $new_column_name);
                    if ($query){
                       return $ad->sendQuery($query);  
                    }           
                    
                }

                Logger::warn('target column already exists : %s.%s ',$table, $new_column_name );
                return false;
                //remove last column - add new column with n_info- because au case sensivity
                //$query = $ad->grammar->rename_column($table, $column, $new_column_name);
                //if ($query){
                //    return $ad->sendQuery($query);             
                //} else {
                    // server do not support rename of column.
                //}
            }
        }
        return false;
    }

    /**
     * add index
     * @param BaseController $ctrl 
     * @param string $table 
     * @param mixed $column 
     * @return mixed 
     */
    public static function db_add_index(BaseController $ctrl, string $table, $column){
        $ad = self::getDataAdapter($ctrl);  
        $query = $ad->grammar->add_index($table, $column);
        if ($query){
            return  $ad->sendQuery($query);
        }
    }
    public static function db_drop_index(BaseController $ctrl, string $table, $column){
        $ad = self::getDataAdapter($ctrl);  
        $query = $ad->grammar->drop_index($table, $column);
        if ($query){
            try{ 
                return  $ad->sendQuery($query);
            } catch(\Exception $ex){
                Logger::danger(implode("\n", [__METHOD__, $ex->getMessage()]));
                return false;
            }
        }
    }
    public static function db_drop_column(BaseController $ctrl, string $table, $column){
        $ad = self::getDataAdapter($ctrl);  
        $query = $ad->grammar->drop_column($table, $column);
        if ($query){
            try{ 
                return  $ad->sendQuery($query);
            } catch(\Exception $ex){
                Logger::danger(implode("\n", [__METHOD__, $ex->getMessage()]));
                return false;
            }
        }
    }
    public static function db_change_column(BaseController $ctrl, string $table, $info)
    {
        $ad = self::getDataAdapter($ctrl); 
        if ($ad->exist_column($table, $info->clName)) {
            // drop foreign key if column 
            $ad->drop_foreign_key($table, $info);

            if ($query = $ad->grammar->change_column($table, $info)) {
                if ($r = $ad->sendQuery($query)) {
                    if ($info->clLinkType) {
                        if ($info->clLinkConstraintName) {
                            $info = clone ($info);
                            $info->clLinkConstraintName = igk_db_get_table_name($info->clLinkConstraintName, $ctrl);
                        }
                        if ($query_link = $ad->grammar->add_foreign_key($table, $info)) {
                            $ad->sendQuery($query_link);
                        }
                    }
                    return true;
                }
            }
        }
    }
}