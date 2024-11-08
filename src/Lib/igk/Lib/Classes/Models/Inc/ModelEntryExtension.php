<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelEntryExtension.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\Models;

use Closure;
use Error;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Database\DbConstants;
use IGK\Database\DbDisplayExpression;
use IGK\Database\DbExpression;
use IGK\Database\DbQueryCondition;
use IGK\Database\DbQueryOptions;
use IGK\System\Database\QueryBuilder;
use IGK\Database\DbQueryResult;
use IGK\Database\DbSchemas;
use IGK\Database\IDbQueryResult;
use IGK\Database\RefColumnMapping;
use IGK\Helper\JSon;
use IGK\Models\Caches\CacheModels;
use IGK\System\Console\Commands\DbDropForeignKeysCommand;
use IGK\System\Console\Logger;
use IGK\System\Models\Traits\ModelExtensionTrait;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\System\Database\DbQuerySelectColumnBuilder;
use IGK\System\Database\DbUtils;
use IGK\System\Database\Helper\DbUtility;
use IGK\System\Database\JoinTableOp;
use IGK\System\Database\SQLGrammar;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Forms\Helper\FormFieldHelper;
use IGK\System\Html\Forms\IFormFieldDbOptions;
use IGK\System\Models\Traits\ModelInitDbExtensionTrait;
use IGK\System\Regex\MatchPattern;
use IGKEvents;
use IGKException;
use IGKQueryResult;
use IGKSysUtil;
use ReflectionClass;
use ReflectionException;
use stdClass;

use function igk_resources_gets as __;
use function igk_getv as getv;
use function igk_get_robjs as get_robjs;
use function igk_count as fcount;
use function igk_environment as environment;
use function igk_form_input_type as form_input_type;

// require_once IGK_LIB_CLASSES_DIR . 

///<summary>Extension</summary>
abstract class ModelEntryExtension
{
    use ModelExtensionTrait;
    use ModelInitDbExtensionTrait;

    ///<summary>get the current model expression</summary>
    /**
     * get model instance
     * @param ModelBase $model 
     * @return ModelBase 
     */
    public static function model(ModelBase $model)
    {
        return $model;
    }
    /**
     * get table model prefix
     * @param ModelBase $model 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function columnPrefix(ModelBase $model){
        return $model->getTableInfo()->prefix ?? '';
    }
    /**
     * get model name
     * @param ModelBase $model 
     * @return string 
     */
    public static function name(ModelBase $model)
    {
        return basename(igk_dir(get_class($model)));
    }
    /**
     * return the instance model
     * @param ModelBase $model 
     * @param mixed $tab 
     * @return mixed 
     * @throws IGKException 
     */
    public static function getv(ModelBase $model, $tab, $key)
    {
        return igk_getv($tab, $key);
    }
    /**
     * retrieve controller static extension
     * @param ModelBase $model 
     * @return null|BaseController 
     * @throws IGKException 
     */
    public static function controller(ModelBase $model)
    {
        return $model->getController();
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $raw 
     * @param bool $idresult 
     * @return null|object 
     */
    public static function create(ModelBase $model, $raw = null, bool $update = true, bool $throwException = true)
    {
        //+ | fix db create result 
        $cl = get_class($model);
        $c = new $cl($raw);
        if ($craw = $c->to_array()) {
            $g = $c->insert($craw, $update, $throwException);
            if  (is_bool($g) && (!$g)){                
                    return null;                
            }
            if (( $g  instanceof IDbQueryResult) && !$g->success()) {
                return null;
            } 
            if ($g instanceof $model) {
                $c->updateRaw($g);
            } else {
                return null;
            }
        }
        return $c;
    }
    /**
     * 
     * @param ModelBase $model 
     * @return stdClass|null 
     * @throws IGKException 
     */
    public static function createEmptyRow(ModelBase $model)
    {
        return DbSchemas::CreateRow($model->getTable(), $model->getController());
    }
    /**
     * create a model from an object. 
     * @param ModelBase $ctrl 
     * @param mixed $object 
     * @return mixed 
     * @throws Exception 
     */
    public static function createFromCache(ModelBase $model, ?object $identifier, $conditions=null)
    {
        static $caches;
        if ($caches === null) {
            $caches = [];
        }
        if (is_null($identifier)) {
            return null;
        }

        $id = spl_object_id($identifier);
        if ($v = getv($caches, $id)) {
            return $v->_cache;
        }
        if ($model->is_mock()) {
            $_obj = $model->select_row($conditions);
        } else {
            $_obj = $model;
        }
        $v = (object)["_cache" => $_obj, "object" => $identifier];
        $caches[$id] = $v;
        return $v->_cache;
    }

    /**
     * 
     * @param ModelBase $model 
     * @param mixed $condition 
     * @param mixed $extra append field conditions
     * @return null|ModelBase|bool 
     */
    public static function createIfNotExists(ModelBase $model, $condition, $extra = null, &$new = false)
    {
        $new = false;
        //create condition query 
        $info = $model->getTableColumnInfo();
        if (is_null($info)){
            Logger::warn('missage table info');
            // return null;
        }
    
        $tconditions = $info ? DbQuerySelectColumnBuilder::Build($info, $condition, true) : $condition;

        if (empty($tconditions) || !($row = $model->select_row($tconditions))) {
            if ($extra) {
                $condition = (object)$condition;
                foreach ($extra as $k => $v) {
                    $condition->$k = $v;
                }
            }
            $new = true;
            return $model::create($condition);
        }
        return $row;
    }
    public static function insertOrUpdate(ModelBase $model, $condition, callable $updating = null)
    {
        if (!($row = $model->select_row($condition))) {
            return $model::create($condition);
        }
        if ($updating)
            $updating($condition);
        $p = $model->getPrimaryKey();
        $model::update($condition, [$p => $row->{$p}]);
        return null;
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $condition 
     * @return int 
     */
    public static function select_count(ModelBase $model, $condition){
        $r= $model->getDataAdapter()->selectCount($model::table(), $condition);
        if ($r instanceof IDbQueryResult){ 
            $row = igk_getv($r->getRows(), 0);
            $s = $row['count'];//->count; //DbConstants::COUNT_ALL_COLUMNS];
            return intval($s);
        }
        return -1;
    }
    /**
     * insert if not exists
     * @param ModelBase $model 
     * @param null|array $conditions 
     * @param null|array $options with extra field to insert model if not found
     * @param bool update true to send a select query with the last inserted id 
     * @return null|false|ModelBase - null for contains items false missing 
     * @throws IGKException 
     */
    public static function insertIfNotExists(ModelBase $model, ?array $conditions, ?array $options = null, $update = false)
    {
        $def = $model->getTableInfo();
        $columns = $model->getTableColumnInfo();
        $prefix = $def->prefix;
        $conditions = DbUtility::TreatSelectCondition($columns, $conditions, $prefix); 
        // | - PEREPARE CONDITION TO AVOID DUPLICATE
        $t_select = DbUtility::PreparateConditionsListToAvoidDuplicate($columns, $conditions);
        $row = null;
        $count = $model->select_count($t_select);
        if ($count==0){
            if ($tab = !$options ? [] : igk_getv($options, "extra")) {
                $conditions = array_merge($conditions, $tab);
            }
            $row = self::insert($model, $conditions, $update);
        } else {
            if ($count==1){
                $row = $model->select_row($t_select);
            }
        }
        return $row;
    }
    public static function updateOrCreateIfNotExists(ModelBase $model, $condition, $update_extras = null)
    {
        if (!($row = $model->select_row($condition))) {
            if ($update_extras) {
                $condition = array_merge($condition, $update_extras);
            }
            return $model::create($condition);
        }
        if ($update_extras) {
            $condition = array_merge($condition, $update_extras);
        }
        $p = $model->getPrimaryKey();
        return $model::update($condition, [$p => $row->{$p}]);
    }
    public static function beginTransaction(ModelBase $model)
    {
        return $model->getDataAdapter()->beginTransaction();
    }
    public static function commit(ModelBase $model)
    {
        return $model->getDataAdapter()->commit();
    }
    public static function rollback(ModelBase $model)
    {
        return $model->getDataAdapter()->rollback();
    }
    public static function endTransaction(ModelBase $model, bool $result)
    {
        return $model->getDataAdapter()->endTransaction($result);
    }

    /**
     * helper: call select_all
     * @param ModelBase $model 
     * @return array 
     * @throws Exception 
     */
    public static function select(ModelBase $model, $conditions = null, $options = null)
    {
        return self::select_all($model, $conditions, $options);
    }

    /**
     * select all return an array
     * @param ModelBase $model 
     * @param mixed $conditions 
     * @param mixed $options 
     * @return array 
     * @throws IGKException 
     */
    public static function select_all(ModelBase $model, $conditions = null, $options = null)
    {
        /**
         * @var ?array $columns
         */
        $tab = [];
        $driver = $model->getDataAdapter();
        if (!$driver){
            igk_dev_wln_e('driver is null');
            return null;
        }
        $cl = get_class($model);
        if ($data = $driver->select($model->getTable(), $conditions, $options)) {
            $columns = ($options ? igk_getv($options, DbQueryOptions::PROP_COLUMNS) : null);
            if ($columns && !is_array($columns)){
                $columns = [$columns];
            }

            foreach ($data->getRows() as $row) {
                $v_data = $row->to_array();
                if ($columns) {
                    $v_data = new RefColumnMapping($v_data, $columns);
                }
                $c = new $cl($v_data, 0, !empty($columns)); 
                $tab[] = $c;
            }
        }
        return $tab;
    }
    /**
     * get model dataadapter driver
     * @param ModelBase $model 
     * @return mixed 
     * @throws IGKException 
     */
    public static function driver(ModelBase $model)
    {
        return $model->getDataAdapter();
    }
    
    /**
     * create query condition for grammar helper
     * @param ModelBase $model 
     * @param string $operand 
     * @param null|array $field_key_values 
     * @return DbConditionExpressionBuilder 
     */
    public static function query_condition(ModelBase $model, string $operand, ?array $field_key_values = null)
    {
        $g = new DbConditionExpressionBuilder($operand);
        if ($field_key_values !== null) {
            foreach ($field_key_values as $k => $v) {
                $g->add($k, $v);
            }
        }
        return $g;
    }
    public static function query_all(ModelBase $model, $conditions = null, $options = null)
    {
        $driver = $model->getDataAdapter();
        return  $driver->select($model->getTable(), $conditions, $options);
    }
    public static function count(ModelBase $model, $conditions = null, $options = null)
    {
        $driver = $model->getDataAdapter();
        $r = 0;
        if ($m = $driver->selectCount($model->getTable(), $conditions, $options)) {
            $r = $m->getRowAtIndex(0)->count;
        }
        return $r;
    }
    /**
     * select the first item 
     * @param ModelBase $model 
     * @param mixed $conditions 
     * @param mixed $options 
     * @param bool $autoclose 
     * @return void 
     */
    public static function select_first(ModelBase $model, $conditions = null, $options = null, $autoclose = false)
    {
        if (is_null($options)) {
            $options = ["Limit" => "1"];
        } else {
            $options["Limit"] = 1;
        }
        return self::select_row($model, $conditions, $options, $autoclose);
    }
    /**
     * select sigle row
     * @param ModelBase $model 
     * @param mixed $conditions treated conditions
     * @param mixed $options 
     * @return object|null 
     * @throws IGKException 
     */
    public static function select_row(ModelBase $model, $conditions, $options = null, $autoclose = false)
    {
        $cl = get_class($model);

        if (is_numeric($conditions)) {
            $conditions = [$model->getPrimaryKey() => $conditions];
        } else if ($conditions) {
            $def = $model->getTableInfo();
            $columns = $model->getTableColumnInfo();
            $prefix = $def->prefix;
            $conditions = DbUtility::TreatSelectCondition($columns, (array)$conditions, $prefix); 
        }
        $ad = $model->getDataAdapter();
        if ($ad === null){
            return null; //igk_wln_e("missing",  $model, $model->getController(), $model->getController()->getDataAdapter());
        } 
        $r = $ad->select($model->getTable(), $conditions, $options, $autoclose);
        // $r = $model->select_all($conditions, $options, $autoclose);

        if ($r && $r->getRowCount() == 1) {
            $g = $r->getRowAtIndex(0);
            $g->{"sys:table"} = $model->getTable();
            return new $cl($g->to_array(), 0, true);
        }
        return null;
    }

    public static function select_row_query(ModelBase $model, $conditions, $options = null)
    {
        $r = static::select_query($model, $conditions, $options);
        if ($r && $r->RowCount == 1) {
            $g = $r->getRowAtIndex(0);
            $g->{"sys:table"} = $model->getTable();
            return $g;
        }
        return null;
    }
    /**
     * send a select query 
     * @param ModelBase $model 
     * @param mixed $conditions 
     * @param mixed $options 
     * @return null|DbQueryResult 
     * @throws IGKException 
     */
    public static function select_query(ModelBase $model, $conditions = null, $options = null)
    {
        return $model->getDataAdapter()->select($model->getTable(), $conditions, $options);
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $conditions 
     * @param mixed $options 
     * @return null|iterable 
     * @throws IGKException 
     */
    public static function select_query_rows(ModelBase $model, $conditions = null, $options = null)
    {
        if ($g = static::select_query($model, $conditions, $options)) {
            return $g->getRows();
        }
        return null;
    }
    /**
     * retrieve select query to send
     * @param ModelBase $model 
     * @param mixed $conditions 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     */
    public static function get_query(ModelBase $model, $conditions = null, $options = null)
    {
        return $model->getDataAdapter()->get_query($model->getTable(), $conditions, $options);
    }
    /***
     * override on model or extension to resolve from mixing data
     */
    public static function resolve(ModelBase $model, $data){
        return null;
    }


    public static function update(ModelBase $model, $value = null, $conditions = null)
    {
        $driver = $model->getDataAdapter();
        $primary = $model->getPrimaryKey();
        $tbinfo = $model->getTableColumnInfo();
        $table = $model->getTable();
        if ($model->is_mock()) {
            if ($value === null) {
                $value = $model->to_array();
            }
            if (is_numeric($conditions)) {
                $conditions = [$model->getPrimaryKey() => $conditions];
            }
            return $driver->update($table, $value, $conditions, $tbinfo);
        }
        if ($value === null) {
            $value = $model->to_array();
            if ($g = $model->getUpdateUnset()) {
                foreach ($g as $k) {
                    unset($value[$k]);
                }
            }
            return $driver->update($table, $value, [$primary => $model->$primary], $tbinfo);
        }
        $r = $driver->update($table, $value, $conditions, $tbinfo);
        return $r;
    }
    /**
     * delete model's entries 
     * @param ModelBase $model 
     * @param mixed $conditions if null try to delete all 
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function delete(ModelBase $model, $conditions = null)
    {
        $driver = $model->getDataAdapter();
        $_pm = $model->getPrimaryKey();
        if ($model->is_mock()) {
            if (is_numeric($conditions)) {
                $conditions = [$_pm => $conditions];
            }
        } else {
            $conditions = [$_pm => $model->{$_pm}];
        }
        return $driver->delete($model->getTable(), $conditions);
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $entry entry data
     * @param bool $update update mode
     * @return null|ModelBase|bool 
     * @throws IGKException 
     */
    public static function insert(ModelBase $model, $entry, $update = true, bool $throwException = true)
    {
        $ad = $model->getDataAdapter();
        if (!$ad->getIsConnect()) {
            return false;
        }
        $info = $model->getTableColumnInfo();

        if ($ad->insert($model->getTable(), $entry, $info, $throwException)) {
            if ($update) {
                $model_class = get_class($model);
                $model = $model->is_mock() ? 
                    new $model_class() : $model;
                // self::_updateRerenceModel()
               // $v_row = $model->is_mock() ? 
                $ref_id = $model->getRefId();


                if (($id = $ad->last_id()) && ($id !== -1)) {                   
                    $model->$ref_id = $id;
                    // + | update new field
                    $model->isNew();
                    $s = $model::select_row([$ref_id => $id]);
                    if (!$s) {
                        igk_dev_wln_e("could not retrieve stored data", $ref_id, $id);
                        return false;
                    }
                    if (is_object($entry)) {
                        if ($s) {
                            foreach ($s->to_array() as $k => $v) {
                                $entry->$k = $v;
                            }
                        }
                        igk_hook(IGKEvents::HOOK_DB_INSERT, ['row' => $s]);
                        return $s;
                    }
                    if ($s) {
                        foreach ($s->to_array() as $k => $v) {
                            $model->$k = $v;
                        }
                    }
                    igk_hook(IGKEvents::HOOK_DB_INSERT, ['row' => $model]);
                    return $model;
                }
            }
            igk_hook(IGKEvents::HOOK_DB_INSERT, ['row' => $entry]);
            return true;
        }
        // + | clear hook
        igk_unreg_hook(IGKEvents::HOOK_DB_INSERT, null);
        return false;
    }
    /**
     * request last inserted model
     * @return ?static 
     */
    public static function last(ModelBase $model, ?string $column=null){
        $key = $model->getPrimaryKey();
        return $model->select_row([$column ?? $key => $model->last_id()]);
    }
    public static function last_id(ModelBase $model)
    {
        return $model->getDataAdapter()->last_id();
    }
    public static function last_error(ModelBase $model)
    {
        return $model->getDataAdapter()->last_error();
    }
    public static function select_rand_row(ModelBase $model, string $column, ?array $columns = null)
    {
        $ad = $model->getDataAdapter();
        $table = $model->table();
        if ($query = $ad->getGrammar()->createRandomQueryTableOnColumn($table, $column, $columns)) {
            return self::query($model, $query);
        }
    }
    /**
     * check if table exists
     * @param ModelBase $model 
     * @return bool 
     * @throws IGKException 
     */
    public static function tableExists(ModelBase $model, bool $throwException=true): bool
    {
        $table = $model::table();
        return $model->getDataAdapter()->tableExists($table, $throwException);
    }
    /**
     * send query 
     * @param ModelBase $model 
     * @param mixed $query 
     * @return null|bool|IDbQueryResult 
     * @throws IGKException 
     */
    public static function query(ModelBase $model, string $query)
    {
        $driver = $model->getDataAdapter();
        return $driver->sendQuery($query);
    }
    /**
     * create a factory class 
     * @param ModelBase $model 
     * @param $count number of item to create 
     * @param null|string $class_name 
     * @param mixed $args,... params to pass
     * @return object 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function factory(ModelBase $model, int $count,  ?string $class_name = null)
    {
        // create a factory object         
        $cl = $class_name ?? $model->getFactory();
        if (class_exists($cl)) {
            $arg = array_slice(func_get_args(), 3);
            array_unshift($arg, $model, $count);
            return new $cl(...$arg);
        }
        igk_die("factory class not found . " . $cl);
    }
    public static function viewFilter(ModelBase $model)
    {
        // create a factory object         
        $cl = $model->getViewFilter();
        if (class_exists($cl)) {
            $arg = func_get_args();
            return new $cl(...$arg);
        }
        igk_die("view filter class not found . " . $cl);
    }
    /**
     * display for this model view
     * @return void 
     */
    public static function display(ModelBase $model, string $separator = '')
    {
        return "display:" . $model->to_json();
    }

    /**
     * return the selected data
     * @param ModelBase $model 
     * @return string 
     * @throws Exception 
     */
    public static function formSelectData(ModelBase $model, $selected = null, ?callable $callback = null)
    {
        $data = [];
        foreach ($model::select_all() as $m) {
            if ($callback) {
                if ($g = $callback($m)) {
                    $data[] = $g;
                }
                continue;
            }
            $g = ["i" => $m->{$m->getPrimaryKey()}, "t" => $m->display()];
            if ((is_callable($selected) && $selected($m)) || ($selected && ($selected == $g["i"]))) {
                $g["selected"] = true;
            }
            $data[] = $g;
        }
        return $data;
    }

    /**
     * column keys
     */
    public static function colKeys(ModelBase $model)
    {
        $rinfo = $model->getTableColumnInfo();
        if ($tablekey = $rinfo) {
            $inf = [];
            array_map(function ($b) use (&$inf) {
                $inf[$b->clName]  = $b;
            }, $tablekey);
            return array_keys($inf);
        }
        return null;
    }    
    /**
     * create a mapper to map columns to keys 
     * @param ModelBase $model 
     * @param array<string colName, string  mapKey> $keys 
     * @return void 
     * @throws IGKException 
     */
    public static function map(ModelBase $model, array $keys){
        $cols = self::colKeys($model);
        $tab = [];
        foreach($cols as $k){ 
            $t = igk_getv($keys, $k) ;
            if ($t){
                $tab[$t] = $model->{$k}; 
            }
        }
        return (object)$tab;
    }
    /**
     * drop the table
     */
    public static function drop(ModelBase $model)
    {
        $driver = $model->getDataAdapter();
        return $driver->dropTable($model->getTable());
    }

    /**
     * simple drop column
     * @param ModelBase $model 
     * @param string $column 
     * @param bool $force 
     * @return mixed|void 
     * @throws IGKException 
     * @throws Error 
     */
    public static function dropColumn(ModelBase $model, string $column , bool $force= false ){
        $driver = $model->getDataAdapter();
        $grammar = $driver->getGrammar();
        if ($grammar instanceof SQLGrammar){

            $query = $grammar->createDropColumnQuery($model->table(), $column);
            $driver->setForeignKeyCheck(false);
            $r = $driver->sendQuery($query);
            $driver->setForeignKeyCheck(true);
            return $r;
        }
    }
    public static function createTable(ModelBase $model)
    {
        $driver = $model->getDataAdapter();
        $info = $model->getDataTableDefinition();
        return $driver->createTable($model::table(), igk_getv($info, "tableRowReference"), igk_getv($info, "description"));
    }

    /**
     * return this model form fields
     * @param ModelBase $model 
     * @param bool $edition
     * @return array 
     * @throws IGKException 
     */
    public static function formFields(ModelBase $model, $edit = false, ?array $unsetKeys = null)
    {
        $cl = $model->getFormFields();
        $t = [];

        $inf =  $model->getTableColumnInfo();
        $ctrl = $model->getController();
        $prefix = $model->getTableInfo()->prefix; // getPrefix();
        $binfo = [];
        $v_tabinfo = null;
        $v_ofd = [];


        if (is_null($unsetKeys) && $edit){
            $unsetKeys = [$model->getPrimaryKey()];
        }

        $b = (igk_count($cl) > 0) ? $cl : array_keys($model->to_array());
        // igk_wln_e($model->to_json());
        //use only data for field
        foreach ($b as $v) {
            if (!isset($inf[$v]))
                continue;
            $info  = $inf[$v];
            $r = ["type" => "text", "value" => $model->$v];
            $type = !empty($info->clInputType) ? form_input_type($info->clInputType) : $info->clType;
            // + | --------------------------------------------------------------------
            // + | form handle info 
            // + |
            $js = self::GetFormFieldInfo($info); 
            $v_placeholder = null;
            if ($js){
                list($placeholder, $ctype, $label_text) = igk_extract($js, 'placeholder|type|label_text');
                $v_placeholder = $placeholder ? __($placeholder) : null;
                if (empty($info->clInputType) && $ctype){
                    $type = preg_match("/\b(textarea)\b/",$ctype) ? $ctype : form_input_type($ctype); 
                    $r['type'] = $type;
                } 
                if ($label_text){
                    $r['label_text'] = __($label_text);
                }
            } 

            $attribs = [];
            if ($info->clRequire || $info->clNotNull) {
                $attribs["required"] = "required";
                $r["required"] = 1;
            }
            $link = $info->clLinkType;
            if ($link) {
                if (is_null($v_tabinfo)) {
                    $v_tabinfo = $ctrl->getDataTableDefinition(null) ?? igk_die("global table definition is null");
                }
                $r["type"] = "select";
                if (!($binf = getv($binfo, $link))) {
                    // $binf = igk_db_get_table_info($link);
                    $binf = DbSchemas::GetTableColumnInfo($link);
                    $binfo[$link] = $binf;
                }
                $v_cl = null;
                if ($v_tabinfo->tables[$link]) {
                    $v_cl = $v_tabinfo->tables[$link]->modelClass;
                } else {
                    igk_dev_wln_e("link:not found ", $link);
                    igk_die('link:not found');
                }
                if ($v_cl) {
                    // class defined :
                    $stb = [];
                    $m = $v_cl::model();
                    $_primary_key = $m->getPrimaryKey(); 
                    $_display_key = $m->getDisplay() ?? $_primary_key; 
                    
                    $display_expression =  is_string($_display_key) && DbDisplayExpression::IsDisplayExpression($_display_key); 
                    if (!$display_expression && $_display_key && !is_array($_display_key) && !$m->columnExists($_display_key)){
                        $_display_key = $_primary_key;
                    }
                    
                

                    $rows = $v_cl::select_all();
                    foreach ($rows as $m) {
                        $v_mt = null;
                        if ($display_expression){
                            $v_mt = DbDisplayExpression::RenderDisplayExpression($_display_key, $m); 
                        }else if (is_array($_display_key)){
                            $v_mt = implode("-", array_map(function($i)use($m){
                                return igk_getv($m, $i);
                            }, $_display_key));
                        } else {
                            $v_mt = $m->{$_display_key};
                        }
                        $stb[] = ["i" => $m->{$_primary_key}, "t" => $v_mt];
                    }
                    $r["data"] = $stb;
                }
                $r["selected"] = $model->$v;
            } else {
                switch (strtolower($type)) {
                    case "enum":
                        $attribs["maxlength"] = $info->clTypeLength;
                        $attribs["list"] = strtolower($v . "-datalist");
                        if (!empty($info->clDescription)) {
                            $attribs["placeholder"] = __($info->clDescription);
                        }
                        if (!$edit && !empty($info->clDefault)) {
                            $r["value"] = $info->clDefault;
                        }
                        if ($info->clNotNull) {
                            $attribs["required"] = "required";
                        }
                        $r["type"] = "text";
                        $r["attribs"] = $attribs;
                        $t[$v] = $r;
                        $r = [];
                        $r["type"] = "datalist";
                        $stb = [];
                        foreach (explode(',', $info->clEnumValues) as $g) {
                            $stb[] = ["i" => $g, "t" => $g];
                        }
                        $r["data"] = $stb;
                        $r["id"] = strtolower($v . "-datalist");
                        $attribs["maxlength"] = $info->clTypeLength;
                        $t[$v . "-datalist"] = $r;
                        continue 2;
                    case "bool":
                    case "checkbox":
                        $r["type"] = "checkbox";
                        unset($r["value"]);
                        $model->$v && ($attribs["checked"] = "checked");
                        if (!$info->clRequire) {
                            unset($r["required"]);
                            unset($attribs["required"]);
                        }
                        break;
                    case "text":
                        $r["type"] = "textarea";
                        if (!empty($info->clInputMaxLength)) {
                            $attribs["maxlength"] = $info->clInputMaxLength;
                        }
                        break;
                    case "date":
                        $r["type"] = "date";
                        break;
                    case "datetime":
                    case "timespan":
                        $r["type"] = "datetime-local";
                        if (!$edit && environment()->is("DEV")) {
                            $r["value"] = "1983-08-04T20:00";
                        }
                        break;
                    case "int":
                        $r["type"] = "int";
                        $attribs["maxlength"] = 9;
                        $attribs["pattern"] = MatchPattern::Int;
                        break;
                    case "float":
                        $r["type"] = "float";
                        $attribs["maxlength"] = 9;
                        $attribs["pattern"] = MatchPattern::Single;
                        break;
                    case "password":
                        $r["type"] = "password";
                        $attribs["igk-validate-pwd"] = "1";
                        $attribs["autocomplete"] = "off";
                        break;
                    case "varchar":
                    default:
                        $attribs["maxlength"] = $info->clTypeLength;
                        break;
                }
            }

           
            
            if (!$v_placeholder && !empty($info->clDescription)) {
                $v_placeholder = __($info->clDescription);
            }
            if ($v_placeholder)
            $attribs["placeholder"]=$v_placeholder;

            if (!$edit && !empty($info->clDefault)) {
                $r["value"] = $info->clDefault;
            }

            $attribs["autocomplete"] = "off";
            $r["attribs"] = $attribs; 
            $t[$v] = $r;
            $keyName = DbUtility::RemoveColumnPrefixName($v, $prefix);
            if ($keyName != $v){
                $v_ofd[$v] = $keyName; 
            }
        }
        $t["::model"] = ["type" => "hidden", "value" => base64_encode(get_class($model))];
        if ($ctrl)
            $t["::ctrl"] = ["type" => "hidden", "value" => base64_encode(get_class($ctrl))];
        if ($unsetKeys) {
            $t = array_diff_key($t, array_flip($unsetKeys));
        }
        if ($prefix && $v_ofd){
            // because need to order the key list
            $t = self::UpdateFieldAssociationPrefixKey($t, $v_ofd);

           
        }
        return $t;
    }
    /**
     * replace association key 
     * @param mixed $t 
     * @param mixed $v_ofd 
     * @return array 
     */
    static function UpdateFieldAssociationPrefixKey($t, $v_ofd){
        return igk_array_replace_key_array($t, $v_ofd);
    }

    /**
     * 
     * @return ?IFormFieldDbOptions 
     */
    static function GetFormFieldInfo($info){
        if (is_string(($v_js = $info->clFormFieldsOptions))){ 
            return igk_json_parse($v_js);
        } 
        return null;
    }
    /**
     * get entry model
     * @param ModelBase $model 
     * @param mixed $column 
     * @param mixed $id
     * @return object|ModelBase|null 
     */
    public static function Get(ModelBase $model, $column = null, $id = null, $autoinsert = null)
    {
        if ($id instanceof $model) {
            return $id;
        }
        if (is_null($column)) {
            $column = $model->getPrimaryKey() ?? igk_die("no primary key provided.");
        }
        $tab = [$column => $id];
        $r =  $model::select_row($tab);
        if (!$r && $autoinsert) {
            if ($autoinsert instanceof \closure) {
                $autoinsert($model);
            } else if (is_array($autoinsert)) {
                $tab = $autoinsert;
            }
            $r = $model::insert($tab);
        }
        return $r;
    }
    /**
     * get single row column column value
     * @param ModelBase $model 
     * @param string $valueColumn 
     * @param string $column 
     * @param mixed $id 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetValue(ModelBase $model, string $valueColumn, string $column, $id)
    {
        if (is_null($column)) {
            $column = $model->getPrimaryKey() ?? igk_die("no primary key provided.");
        }
        $tab = [$column => $id];
        $r =  $model::select_row($tab, ['Columns' => [$valueColumn]]);
        if ($r) {
            return $r->$valueColumn;
        }
        return $r;
    }
    /**
     * get cached properties
     * @param ModelBase $model 
     * @param mixed $column 
     * @param mixed $id 
     * @param mixed $autoinsert 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetCache(ModelBase $model, $column, $id, $autoinsert = null): ?object
    {
        $cl = $model->getColumn($column);
        $key = CacheModels::GetCacheKey($model, $cl, $id);
        if ($o = CacheModels::Get($key)) {
            return $o;
        }
        if ($o = self::Get($model, $cl, $id, $autoinsert)) {
            CacheModels::Register($key, $o);
        }
        if (is_bool($o)) {
            return null;
        }
        return $o;
    }
    /**
     * 
     * @param ModelBase $model 
     * @param string $column 
     * @param mixed $value 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetCacheColumn(ModelBase $model, string $column, $value){
        $r = self::GetCache($model, $column, $value);
        if ($r){
            return $r->{$column};
        }
        return null;
    }
    ///<summary>return the model table name</summary>
    /**
     * return model table full path
     * @param ModelBase $model 
     * @param null|string $column 
     * @return string 
     * @throws IGKException 
     */
    public static function table(ModelBase $model, ?string $column = null)
    {
        if (!empty($column))
            $column = "." . $column;
        return $model->getTable() . $column;
    }
    /**
     * build column name
     * @param ModelBase $model 
     * @param mixed $column 
     * @return void 
     */
    public static function column(ModelBase $model, $column)
    {
        return static::table($model, $column);
    }
    public static function primaryKey(ModelBase $model)
    {
        return $model->getPrimaryKey();
    }
    /**
     * get the cached row
     * @param ModelBase $model 
     * @param mixed $primaryKeyIdentifier 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException if row not found
     */
    public static function cacheRow(ModelBase $model, $primaryKeyIdentifier, $throw = true)
    {
        static $states;
        if ($states === null) {
            $states = [];
        }
        $id = $primaryKeyIdentifier;
        if (is_array($id))
            $id = json_encode($primaryKeyIdentifier);
        $cl  = get_class($model);
        $key = $cl . "/" . $id;
        if ($row = getv($states, $key)) {
            return $row;
        }
        $g = null;
        if (is_array($primaryKeyIdentifier))
            $g = $primaryKeyIdentifier;
        else
            $g = [$model->getPrimaryKey() => $primaryKeyIdentifier];
        $row = $cl::select_row($g);
        if (!$row) {
            if ($throw)
                throw new IGKException("Row not found " . $model->getTable() . ":" . json_encode($g));
            return null;
        }
        $states[$key] = $row;
        return $row;
    }
    /**
     * get cache row
     * @param ModelBase $model 
     * @param mixed $primaryKeyIdentifier 
     * @return ?ModelBase object 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function cacheIsRow(ModelBase $model, $primaryKeyIdentifier)
    {
        return static::cacheRow($model, $primaryKeyIdentifier, false);
    }

    /**
     * 
     * @param ModelBase $model 
     * @param null|callable $filter 
     * @param mixed $condition 
     * @param mixed $options 
     * @return array 
     */
    public static function form_select_all(ModelBase $model, ?callable $filter = null, $condition = null, $options = null)
    {
        if ($filter === null) {
            $filter = function ($r) use ($model) {
                return ["i" => $r->{$model->getPrimaryKey()}, "t" => $r->{$model->getDisplay()}];
            };
        }

        if ($options == null) {
            $options = [];
        }
        $tab = [];
        $options[DbQueryResult::CALLBACK_OPTS] =
            function ($row) use ($filter, &$tab) {
                if ($g = $filter($row)) {
                    $tab[] = $g;
                }
                return false;
            };
        $model::select_all($condition, $options);
        return $tab;
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $expression 
     * @return mixed 
     * @throws IGKException 
     */
    public static function linkCondition(ModelBase $model, $expression)
    {
        $express = explode(".", $expression);
        if (fcount($express) != 2) {
            throw new IGKException("expression not valid");
        }
        return $model->getDataAdapter()->createLinkExpression($model::table(), $express[0], $express[1], $model->getPrimaryKey());
    }
    /**
     * udpate with request
     * @param ModelBase $model 
     * @return mixed 
     */
    public static function requestUpdate(ModelBase $model)
    {
        $b = get_robjs($model->getFormFields());
        return $model::update((array)$b, $model->{$model->getPrimaryKey()});
    }

    public static function requestAdd(ModelBase $model)
    {
        $b = get_robjs($model->getFormFields());
        return $model::createIfNotExists((array)$b);
    }

    /**
     * return primary key id
     * @param ModelBase $model 
     * @param mixed $condition 
     * @return mixed 
     */
    public static function id(ModelBase $model, $condition = null)
    {
        if ($model->is_mock()) {
            if (!empty($condition) && ($trow = $model->cacheIsRow($condition))) {
                return $trow->{$model->getPrimaryKey()};
            }
            return 0;
        }
        return $model->{$model->getPrimaryKey()};
    }
    /**
     * get prepare query builder
     * @param ModelBase $model 
     * @return QueryBuilder 
     */
    public static function prepare(ModelBase $model)
    {
        return new QueryBuilder($model);
    }
    /**
     * start prepare with 
     * @param ModelBase $model 
     * @param mixed $modelUnion 
     * @param null|string $propertyName 
     * @return QueryBuilder 
     */
    public static function with(ModelBase $model, $modelUnion, ?string $propertyName = null)
    {
        $model = self::prepare($model);
        $model->with($modelUnion, $propertyName);
        return $model;
    }

    public static function modelTableInfo(ModelBase $model)
    {
        return $model->getTableColumnInfo();
    }
    /**
     * retrieve query column
     * @param ModelBase $model 
     * @return array
     */
    public static function queryColumns(ModelBase $model, ?array $filter = null, bool $useall = false)
    {
        $tab = array_map(function ($a) use ($model) {
            return $model::column($a);
        }, array_keys(self::modelTableInfo($model)));
        $tab = array_combine($tab, $tab);
        if ($filter) {
            $ctab = [];
            foreach ($filter as $k => $v) {
                if (is_numeric($k) && is_string($v)) {
                    $k = $v;
                    $v = $model::column($k);
                }
                if (isset($tab[$k]) || (($k = $model::column($k)) && isset($tab[$k]))) {
                    $ctab[$k] = $v;
                    if ($useall) {
                        $tab[$k] = $v;
                    }
                }
            }
            if (!$useall) {
                $tab = $ctab;
            }
        }
        return $tab;
    }
    /**
     * 
     * @param ModelBase $model 
     * @return void 
     */
    public static function select_fetch(ModelBase $model, ?array $condition = null, ?array $options = null)
    {
        $driver = $model->getDataAdapter();
        $inf = $model->modelTableInfo();
        $query = $driver->getGrammar()->createSelectQuery($model->table(), $condition, $options, $inf);
        $res = $driver->createFetchResult($query, $model);
        $driver->sendQuery($query, false, array_merge($options ?? [], [
            IGKQueryResult::RESULTHANDLER => $res
        ]), null, false);
        return $res;
    }
    public static function get_insert_query(ModelBase $model)
    {
        $inf = $model->modelTableInfo();
        $driver = $model->getDataAdapter();
        $query = $driver->getGrammar()->createInsertQuery($model->table(), $model, $inf);
        return $query;
    }

    /**
     * create condition row
     * @param ModelBase $model 
     * @return DbQueryCondition 
     */
    public static function createCondition(ModelBase $model)
    {
        return new \IGK\Database\DbQueryCondition($model::createEmptyRow());
    }

    /**
     * get entry details
     * @param ModelBase $model source model
     * @param string $className target model
     * @param string $column column name that identifier the foreign key.
     * @return mixed 
     */
    public static function getDetails(ModelBase $model, string $className, string $column)
    {
        return $className::select_query_rows([$column => $model]);
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $params if first item is not array will add
     * @return mixed
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Add(ModelBase $model, $params){
        return self::_Add($model, false, ...array_slice(func_get_args(), 1));        
    }
    public static function AddIfNotExists(ModelBase $model, $params)
    {
        return self::_Add($model, true, ...array_slice(func_get_args(), 1));
    }
    /**
     * 
     * @param ModelBase $model 
     * @param bool $check 
     * @param mixed $params 
     * @return null|object|bool|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _Add(ModelBase $model, bool $check, $params)
    {
        $info =  $model->getTableColumnInfo();
        $controller = $model->getController();
        if (is_null($controller)) {
            igk_die(__FUNCTION__ . " failed:[controller] is null model name = " . get_class($model));
        }
        if ($params && !is_array($params)) {
            $args = IGKSysUtil::DBGetPhpDocModelArgEntries((array)$info, $controller);            
            $row = $model::createEmptyRow();
            if (is_null($row)){   
                $trow = DbSchemas::CreateRow($model->getTable(), $controller);          
                igk_die('failed to create an empty row to add. missing table definitions '.get_class($model));
            } 
            $g = array_keys($args);
            $count = count($g);
            $index = 0;
            foreach (array_slice(func_get_args(), 2) as $tg) {
                $n = $g[$index];
                $row->$n = $tg;
                $index++;
                if ($index>=$count){
                    break;
                }
            }
            if ($check) {
                // check that entries for unique column 
                $tb_uniques = self::GetUniqueRowFields($info, $row);
                foreach ($tb_uniques as $uniques) {
                    if ($r = $model::select_row($uniques, ["Limit" => 1])) {
                        return $r;
                    }
                }
            }
            return $model::create($row, true);
        }
        if (is_object($params) || is_array($params)) {
            return self::create($model, $params, true);
        }
    }
    /**
     * helper: get unique row fields
     * @param mixed $info 
     * @param mixed $row 
     * @return array 
     */
    public static function GetUniqueRowFields($info, $row)
    {
        $tinfo = [];
        $unique = [];
        foreach ($info as $key => $value) {
            if ($value->clIsUniqueColumnMember) {
                $index = $value->clColumnMemberIndex ?? 0;
                if (!isset($tinfo[$index])) {
                    $tinfo[$index] = (object)[];
                }
                $tinfo[$index]->$key = $row->$key;
            }
            if ($value->clIsUnique) {
                $unique[$value->clName] = [$value->clName => $row->$key];
            }
        }
        $tinfo = array_merge($tinfo, $unique);
        $g = [];
        foreach ($tinfo as $c) {
            if (!is_array($c)) {
                $c = (array)$c;
            }
            $g[] = $c;
            if (!empty($t = array_filter($c)) && (count($c) == count($t))) {
                $g[] = $t;
            }
        }
        return array_unique($g, SORT_REGULAR);
    }
    /**
     * dump export raw data
     * @param ModelBase $model 
     * @param null|array $data 
     * @return string|null 
     * @throws Exception 
     */
    public static function dump_export(ModelBase $model, ?array $data = null)
    {
        $row = $model->createEmptyRow();
        if ($data) {
            igk_full_fill($row, $data);
        }
        return var_export($row, true);
    }
    /**
     * describe from info schema
     * @param ModelBase $model 
     * @return array 
     * @throws IGKException 
     */
    public static function schema_describe(ModelBase $model)
    {
        $columns = [];
        $info =  $model->getTableColumnInfo();
        $t = $model->getTable();
        foreach ($info as $cl) {
            if (is_null($cl)) {
                continue;
            }

            if ($cl->clIsUniqueColumnMember) {
                if (!isset($columns[$t])) {
                    $columns[$t] = [];
                }
                $index = $cl->clColumnMemberIndex;
                $columns[$t][$index][] = $cl->clName;
            }
        }
        return $columns;
    }

    /**
     * invoke display result 
     * @param ModelBase $model 
     * @param mixed $result 
     * @return null|string|ModelBase 
     */
    public static function DisplayResult(ModelBase $model, $result)
    {
        if (is_null($result)) {
            return null;
        }
        $prop = $model->getDisplay();
        if ($result instanceof $model) {
            if (isset($result->$prop)) {
                return $result->display();
            } else {
                if ($cl = $model->getController()->resolveClass(EntryClassResolution::DbMacrosDisplay )) {
                    $g = new $cl();
                    return $g->display($result);
                }
            }
        }
        return JSon::Encode($model);
    }
    /**
     * select cache and display raw value
     * @param ModelBase $model 
     * @param mixed $result 
     * @return void 
     */
    public static function DisplayRow(ModelBase $model, $condition)
    {
        if ($g = $model->cacheRow($condition)) {
            return $g->display();
        }
        return null;
    }

    /**
     * convert to condition fields
     * @param ModelBase $model 
     * @param string $column 
     * @return array 
     */
    public static function condition(ModelBase $model, string $column)
    {
        return [$column => $model->$column];
    }

    /**
     * validate model data before saving or insert
     * @return void 
     */
    public static function validate(ModelBase $model)
    {
        $g = $model->getTableColumnInfo();
        $numfield = explode("|", "int|bigint|float|double|integer");
        foreach ($model->to_array() as $k => $v) {
            $info = $g[$k];
            if ($v) {
                if (in_array(strtolower($info->clType), $numfield)) {
                    if (is_numeric($v)) {
                        continue;
                    }
                    return false;
                }
                if (is_string($v)) {

                    if (preg_match("/<(.)+>/i", $v)) {
                        $v = preg_replace("/\\s+/", " ", $v);
                        // contains html tag - if not allowed skip - tag
                        $v = preg_replace("/<([^>])+>/i", "", $v);
                    }
                    $model->$k = $v;
                }
            }
        }
    }
    /**
     * retrieve column's list from model 
     * @param ModelBase $model 
     * @param null|Closure|string $prefix 
     *  - if `Closure` (string $column_name, string $full_column_name, ModelBase $model):?string
     * @param null|Closure|array|string $filter 
     * @return null 
     * @throws IGKException 
     */
    public static function columnList(ModelBase $model, $prefix=null, $filter=null){
        $keys = array_keys($model->getTableColumnInfo());
        $tkey = null;
        $keys = array_map(function($a)use($model, & $tkey, $prefix, $filter){
            if ($filter){
                $cond = ($filter instanceof Closure) && ($filter($a, $model));
                $cond = $cond || (is_array($filter) && in_array($a, $filter));
                $cond = $cond || (is_string($filter) && preg_match($filter, $a));
    
                if ($cond) return;
            }
            $t = $model::column($a);
            $v = null;
            if ($prefix instanceof Closure){
                $v = $prefix($a, $t, $model);
            }else if (is_string($prefix) && (strlen($prefix = trim($prefix))>0)){
                $v = $prefix.$a;
            } else if (is_array($prefix) && key_exists($a, $prefix)){
                $v = $prefix[$a];
            }
            if ($v)
                $tkey[$t] = $v; 
            else 
                $tkey[] = $t;
        }, $keys);
        return $tkey;
    }
    /**
     * 
     * @param ModelBase $model 
     * @param array $args 
     * @return array 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function columnOnlyArray(ModelBase $model, array $args){
        $prefix = igk_getv($args, 'prefix');
        unset($args['prefix']);
        $tm = array_unique(array_values($args));
        $tp = DbUtils::ModelColumns($model, ...$tm);
        $l = DbUtils::OnlyColumnFilterRegex(implode("|",array_values($tp)));
        if (empty($l)){
            $l = null;
        }
        return $model::columnList($prefix, $l);

    }

    public static function columnSelectArray(ModelBase $model, string $column, ...$args){
        $m = count($args)>0? $args:[];
        $tp = DbUtils::ModelColumns($model, ...array_merge([$column], $m)); 
        return array_values($tp); 
    }

    /**
     * pad string value
     * @param ModelBase $model 
     * @param string $column 
     * @param mixed $value 
     * @param int $pad 
     * @param int $def default length if clTypeLength is missing 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function strPadValue(ModelBase $model, string $column, $value, string $pad_value='0', int $pad=STR_PAD_LEFT, int $def=10){
        $cl_ref= $column;
        $ln =igk_getv($model->getTableColumnInfo(), $cl_ref) ?? igk_die("missing columns");
        $length = intval($ln->clTypeLength ?? $def);
        return str_pad($value, $length, $pad_value, $pad);

    }

    /**
     * 
     * @param ModelBase $model 
     * @param string $column_short_name 
     * @param mixed $call calling expression column_full_name/callable
     * @param mixed $type operator type
     * @return array 
     * @throws IGKException 
     */
    public static function joinTableColumnOn(ModelBase $model, string $column_short_name, $call=null, string $type=null, $operand=JoinTableOp::EQUAL){
        $cl = get_class($model);
		$rt = $cl::column($column_short_name);
        $grammar = $model->getDataAdapter()->getGrammar();

		$c = [];
		if ($call){
            $s = $grammar->createJoinOperation($operand, $rt, $call);
		    $c[] = $s;
            if ($type)
		        $c["type"] = $type;
		    return [$cl::table()=>$c];
		}
		return [$cl::table()]; 
    }

    public static function joinTableTargetOn(ModelBase $model, $column){
        return $model::column($column);
    }
    /**
     * create a select query expression
     * @param ModelBase $model 
     * @param null|array $conditions 
     * @param mixed $options 
     * @return null|string 
     * @throws IGKException 
     */
    public static function selectExpression(ModelBase $model, ?array $conditions=null, $options=null){
        return $model->getDataAdapter()->getGrammar()->createSelectQuery($model::table(), $conditions, $options);
    }
  	/**
	 * get cached id
	 * @param mixed $value primary key value
	 * @return mixed 
	 */
	public static function GetCacheIdRowDisplay(ModelBase $model, $value)
	{
		$p = $model->primaryKey;
		$r = $model::GetCache($p, $value);
		return ($r ? $r->display() : null) ?? $r->{$p};
	}
}
