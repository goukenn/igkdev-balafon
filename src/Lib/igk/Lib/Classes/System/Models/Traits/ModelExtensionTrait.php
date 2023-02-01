<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelExtensionTrait.php
// @date: 20230131 14:24:39
namespace IGK\System\Models\Traits;


///<summary></summary>
/**
 * 
 * @package IGK\Ssytem\Models\Traits
 */
trait ModelExtensionTrait
{
    /**
     * extension methods 
     */
    public abstract static function Add(\IGK\Models\ModelBase $model, $params);
    /**
     * extension methods 
     */
    public abstract static function AddIfNotExists(\IGK\Models\ModelBase $model, $params);
    /**
     * extension methods 
     */
    public abstract static function DisplayResult(\IGK\Models\ModelBase $model, $result);
    /**
     * extension methods 
     */
    public abstract static function Get(\IGK\Models\ModelBase $model, $column = null, $id = null, $autoinsert = null);
    /**
     * extension methods 
     */
    public abstract static function GetCache(\IGK\Models\ModelBase $model, $column, $id, $autoinsert = null): ?object;
    /**
     * extension methods 
     */
    public abstract static function _Add(\IGK\Models\ModelBase $model, bool $check, $params);
    /**
     * extension methods 
     */
    public abstract static function beginTransaction(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function cacheIsRow(\IGK\Models\ModelBase $model, $primaryKeyIdentifier);
    /**
     * extension methods 
     */
    public abstract static function cacheRow(\IGK\Models\ModelBase $model, $primaryKeyIdentifier, $throw = true);
    /**
     * extension methods 
     */
    public abstract static function colKeys(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function column(\IGK\Models\ModelBase $model, $column);
    /**
     * extension methods 
     */
    public abstract static function commit(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function condition(\IGK\Models\ModelBase $model, string $column);
    /**
     * extension methods 
     */
    public abstract static function controller(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function count(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function create(\IGK\Models\ModelBase $model, $raw = null, bool $update = true, bool $throwException = true);
    /**
     * extension methods 
     */
    public abstract static function createCondition(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function createEmptyRow(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function createFromCache(\IGK\Models\ModelBase $model, $object);
    /**
     * extension methods 
     */
    public abstract static function createIfNotExists(\IGK\Models\ModelBase $model, $condition, $extra = null, &$new = false);
    /**
     * extension methods 
     */
    public abstract static function createTable(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function delete(\IGK\Models\ModelBase $model, $conditions = null);
    /**
     * extension methods 
     */
    public abstract static function display(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function driver(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function drop(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function dump_export(\IGK\Models\ModelBase $model, array $data = null);
    /**
     * extension methods 
     */
    public abstract static function endTransaction(\IGK\Models\ModelBase $model, bool $result);
    /**
     * extension methods 
     */
    public abstract static function factory(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function formFields(\IGK\Models\ModelBase $model, $edit = false, array $unsetKeys = null);
    /**
     * extension methods 
     */
    public abstract static function formSelectData(\IGK\Models\ModelBase $model, $selected = null, callable $callback = null);
    /**
     * extension methods 
     */
    public abstract static function form_select_all(\IGK\Models\ModelBase $model, callable $filter = null, $condition = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function getDetails(\IGK\Models\ModelBase $model, string $className, string $column);
    /**
     * extension methods 
     */
    public abstract static function get_insert_query(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function get_query(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function getv(\IGK\Models\ModelBase $model, $tab, $key);
    /**
     * extension methods 
     */
    public abstract static function id(\IGK\Models\ModelBase $model, $condition = null);
    /**
     * extension methods 
     */
    public abstract static function insert(\IGK\Models\ModelBase $model, $entries, $update = true, bool $throwException = true);
    /**
     * extension methods 
     */
    public abstract static function insertIfNotExists(\IGK\Models\ModelBase $model, array $condition, array $options = null, $update = false);
    /**
     * extension methods 
     */
    public abstract static function insertOrUpdate(\IGK\Models\ModelBase $model, $condition, callable $updating = null);
    /**
     * extension methods 
     */
    public abstract static function last_error(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function last_id(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function linkCondition(\IGK\Models\ModelBase $model, $expression);
    /**
     * extension methods 
     */
    public abstract static function model(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function modelTableInfo(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function name(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function prepare(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function primaryKey(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function query(\IGK\Models\ModelBase $model, string $query);
    /**
     * extension methods 
     */
    public abstract static function queryColumns(\IGK\Models\ModelBase $model, array $filter = null, bool $useall = false);
    /**
     * extension methods 
     */
    public abstract static function query_all(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function query_condition(\IGK\Models\ModelBase $model, string $operand, array $field_key_values = null);
    /**
     * extension methods 
     */
    public abstract static function requestAdd(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function requestUpdate(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function rollback(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function schema_describe(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function select(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function select_all(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function select_fetch(\IGK\Models\ModelBase $model, array $condition = null, array $options = null);
    /**
     * extension methods 
     */
    public abstract static function select_first(\IGK\Models\ModelBase $model, $conditions = null, $options = null, $autoclose = false);
    /**
     * extension methods 
     */
    public abstract static function select_query(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function select_query_rows(\IGK\Models\ModelBase $model, $conditions = null, $options = null);
    /**
     * extension methods 
     */
    public abstract static function select_row(\IGK\Models\ModelBase $model, $conditions, $options = null, $autoclose = false);
    /**
     * extension methods 
     */
    public abstract static function select_row_query(\IGK\Models\ModelBase $model, $conditions, $options = null);
    /**
     * extension methods 
     */
    public abstract static function table(\IGK\Models\ModelBase $model, string $column = null);
    /**
     * extension methods 
     */
    public abstract static function tableExists(\IGK\Models\ModelBase $model): bool;
    /**
     * extension methods 
     */
    public abstract static function update(\IGK\Models\ModelBase $model, $value = null, $conditions = null);
    /**
     * extension methods 
     */
    public abstract static function updateOrCreateIfNotExists(\IGK\Models\ModelBase $model, $condition, $update_extras = null);
    /**
     * extension methods 
     */
    public abstract static function validate(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function viewFilter(\IGK\Models\ModelBase $model);
    /**
     * extension methods 
     */
    public abstract static function with(\IGK\Models\ModelBase $model, $modelUnion, string $propertyName = null);
}
