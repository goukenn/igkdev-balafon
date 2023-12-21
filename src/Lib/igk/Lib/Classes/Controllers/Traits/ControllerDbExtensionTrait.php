<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerDbExtensionTrait.php
// @date: 20230703 14:24:34
namespace IGK\Controllers\Traits;

use IGK\Controllers\BaseController;
use IGK\System\Console\Logger;
use IGK\System\Database\ColumnMigrationInjector;

///<summary></summary>
/**
* 
* @package IGK\Controllers\Traits
*/
trait ControllerDbExtensionTrait{
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
                $is_obj && $info->clLinkType &&
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

    public static function db_change_column(BaseController $ctrl, $table, $info)
    {
        $ad = self::getDataAdapter($ctrl);
        igk_environment()->querydebug = 1;
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