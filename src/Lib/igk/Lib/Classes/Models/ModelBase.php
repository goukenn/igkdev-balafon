<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelBase.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Models;

 

require_once __DIR__ . "/Inc/ModelEntryExtension.php";


/**
 * model base
 * @package IGK\Models
 * @method static ?static|bool create(array|object|static $definition, bool $update=true, bool $raise_exception=true) - create a row entries
 * @method static static createEmptyRow() - create a empty row - do not insert into database
 * @method static \IGK\Database\DataAdapterBase|null DataAdapter driver() - get the data adapter
 * @method static ?bool insertIfNotExists(array $conditions, ?array $options = null) macros:Insert if condition not meet.
 * @method static object|null insert() macros function - DefaultModelEntryExtension
 * @method static array|null select(?array $condition=null) macros function - DefaultModelEntryExtension
 * @method static array|array<static>|null select_all(?array $condition=null, null|array|DbQueryOptions $options=null) macros function  
 * @method static bool drop() macros function drop table 
 * @method static bool createTable() macros function create table if not exists
 * @method static bool delete(null|array|object $condition) macros function delete table's entries
 * @method static ?static select_row($condition, $options=null) macros function : select single row
 * @method static void beginTransaction() macros function
 * @method static object|null cacheIsRow() macros function
 * @method static static|null cacheRow($conditions) macros function get a cached row. for this running instance. not it throws and error if rows not found
 * @method static ?array colKeys() macros function
 * @method static string column(string $column) macros function get column full name
 * @method static void commit() macros function
 * @method static int count(?array $conditions) macros function
 * @method static object|null createFromCache() macros function
 * @method static static createCondition() macros function create condition object
 * @method static ?static createIfNotExists($data_condition, ?object|array $extra=null extra model field definition, & $new = false new field) macros function: create if not exists
 * @method static string display() macros function return a string used for display
 * @method static array|Iterable|null formFields($edit=false, ?array $unsetKeys=null) macros function
 * @method static array formSelectData() macros function : form selection data
 * @method static void form_select_all() macros function
 * @method static void id() macros function
 * @method static void insert() macros function
 * @method static void insertOrUpdate() macros function
 * @method static void last_error() macros function
 * @method static void last_id() macros function
 * @method static void linkCondition() macros function
 * @method static ModelBase model() macros function return object model
 * @method static \IGK\System\Database\QueryBuilder prepare() macros function prepare data query builder
 * @method static void primaryKey() macros function
 * @method static IIGKQueryResult query(string $query) macros function send query string
 * @method static object|null|bool requestAdd() macros function add model entry by request
 * @method static void requestUpdate() macros function
 * @method static void rollback() macros function
 * @method static void select() macros function 
 * @method static string get_query() macros function get select query to send
 * @method static IIGKQueryResult select_query() macros function
 * @method static null|iterable select_query_rows($conditions=null, $options=null) macros function
 * @method static void select_row_query() macros function
 * @method static \IGK\Database\IDbQueryFetchResult select_fetch(?array $conditions[], array? $options[]) macros function return a fetch result
 * @method static string table() macros function
 * @method static null|IIGKQueryResult update() macros function
 * @method static void updateOrCreateIfNotExists() macros function
 * @method static bool registerExtension($classname) macros helper register static function attache to class
 * @method static void registerMacro($macroName, Callable|array $callable) register macros
 * @method static IGK\System\Database\Factories\FactoryBase factory(number) factory for seeder
 * @method static array queryColumns(?array filter=null, bool useall if filter user all column by filter column with as property) macros function query columns
 * @method array to_array(); return model's array data
 * @method static \IGK\System\Database\DbConditionExpressionBuilder query_condition(string operand); OR|AND query condition 
 * @method void set(name, value): set value
 * @method static \IGK\Database\DataAdapterBase driver() macros helper get the driver attached to the current model
 * @method static string get_insert_query() marcros helper insert query 
 * @method static ?static Get(string $column, mixed $value, $autoinsert=null) macros function get row from defined value autoinsert
 * @method static ?static GetCache($column, $id, $autoinsert=null) macros function get row from defined value autoinsert
 * @method static ?static getv($array, $i) macros function convert class
 * @method static \IGK\System\Database\QueryBuilder with(string $table, ?string $propertyName=null) prepare command with table 
 */
abstract class ModelBase  extends \IGK\System\Models\ModelBase {

}