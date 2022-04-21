<?php

namespace IGK\Models;

use ArrayAccess;
use Closure;
use Exception;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Helper\Utility; 
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\JsonSerializableTrait;
use IGKEvents;
use IGKException; 
use IGKSysUtil;
use JsonSerializable;  

require_once __DIR__ . "/ModelEntryExtension.php";


/**
 * model base
 * @package IGK\Models
 * @method static ModelBase create() - create a row entries
 * @method static ModelBase createEmptyRow() - create a empty row - do not insert into database
 * @method static \IGK\Database\DataAdapterBase|null DataAdapter driver() - get the data adapter
 * @method static object|null insertIfNotExists(?array condition = null, ?array options = null) macros:Insert if condition not meet.
 * @method static object|null insert() macros function - DefaultModelEntryExtension
 * @method static array|null select(?array $condition=null) macros function - DefaultModelEntryExtension
 * @method static array|null select_all(?array $condition=null, ?array $options=null) macros function  
 * @method static bool drop() macros function drop table 
 * @method static bool createTable() macros function create table if not exists
 * @method static bool delete(null|array|object $condition) macros function delete table's entries
 * @method static object|null select_row($condition) macros function : select single row
 * @method static void beginTransaction() macros function
 * @method static object|null cacheIsRow() macros function
 * @method static object|null cacheRow() macros function
 * @method static void colKeys() macros function
 * @method static string column(string $column) macros function get column full name
 * @method static void commit() macros function
 * @method static int count(?array $conditions) macros function
 * @method static object|null createFromCache() macros function
 * @method static bool|object createIfNotExists(object $object, ?mixed $condition) macros function: create if not exists
 * @method static string display() macros function return a string used for display
 * @method static array|Iterable|null formFields() macros function
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
 * @method static void query() macros function
 * @method static object|null|bool requestAdd() macros function add model entry by request
 * @method static void requestUpdate() macros function
 * @method static void rollback() macros function
 * @method static void select() macros function
 * @method static void select_all() macros function
 * @method static string get_query() macros function get select query to send
 * @method static IIGKQueryResult select_query() macros function
 * @method static void select_query_rows() macros function
 * @method static void select_row_query() macros function
 * @method static \IGK\Database\IDbQueryFetchResult select_fetch() macros function return a fetch result
 * @method static string table() macros function
 * @method static void update() macros function
 * @method static void updateOrCreateIfNotExists() macros function
 * @method static void registerExtension($classname) macros helper register static function attache to class
 * @method static void registerMacro($macroName, Callable|array $callable) register macros
 * @method static IGK\System\Database\Factories\FactoryBase factory(number) factory for seeder
 * @method static array queryColumns(?array filter=null, bool useall if filter user all column by filter column with as property) macros function query columns
 * @method array to_array();
 * @method static \IGK\System\Database\DbConditionExpressionBuilder query_condition(string operand); OR|AND query condition 
 * @method void set(name, value): set value
 * @method static \IGK\Database\DataAdapterBase driver() macros helper get the driver attached to the current model
 * @method string get_insert_query() marcros helper insert query
 */
abstract class ModelBase implements ArrayAccess, JsonSerializable
{
	use ArrayAccessSelfTrait;
    use JsonSerializableTrait;

    static $mock_instance;
    /**
     * stored macros
     * @var mixed
     */
    private static $macros;
    /**
     * table's name
     * @var string
     */
    protected $table;

    /**
     * raw data
     * @var mixed
     */
    protected $raw;

    /**
     * 
     * @var mixed
     */
    protected $primaryKey = "clId";

    /**
     * column name that match the last inserted id. \
     * in order to be refId column must be a number type, with autoincrement
     * @var string
     */
    protected $refId = "clId";

    /**
     * column use for display
     * @var string
     */
    protected $display = "clName";

    /**
     * model controller
     * @var string
     */
    protected $controller;

    /**
     * class used for factory
     * @var mixed
     */
    protected $factory;

    /**
     * field list use to create forms
     * @var array
     */
    protected $form_fields = [];

    /**
     * fillable list use data
     * @var mixed
     */
    protected $fillable;

    /**
     * hidden list data
     * @var mixed
     */
    protected $hidden;

    /**
     * for mocking object
     * @var mixed
     */
    private $is_mock;

    /**
     * define unset field for update
     * @var mixed
     */
    protected $update_unset;

    public function getUpdateUnset()
    {
        return $this->update_unset;
    }

    public function _json_serialize(){
        return json_encode((object)array_filter($this->to_array()));
    }

    public function getFactory()
    {
        if ($this->factory === null) {
            $name = basename(igk_io_dir(get_class($this)));
            $this->factory = $this->getController()::ns("Database\\Factories\\" . $name . "Factory");
        }
        return $this->factory;
    }
    public function set($name, $value)
    {
        $this->raw->{$name} = $value;
        return $this;
    }
    /**
     * how to display this model
     * @return mixed 
     * @throws Exception 
     */
    public function display()
    {        
        $d = $this->display;
        if (isset($this->raw->{$d}))
            return $this->{$d};
        $cl = get_class($this); 
        if (is_callable($fc = $cl::__callStatic("getMacro", ["display"]))){
            return $fc($this);
        }
        return $this->to_json();
    }
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    public function getRefId(){
        return $this->refId;
    }
    public function getFormFields()
    {
        return $this->form_fields;
    }
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * create dummy row
     * @return object|null dummy raw 
     */
    protected function createRow()
    {   
        if (method_exists($this, "getDataTableDefinition")){
            if ($g = $this->getDataTableDefinition()){
                $inf = $g["tableRowReference"];
                return DbSchemas::CreateObjFromInfo($inf);
            }
        } 
        $ctrl = igk_getctrl($this->controller ?? SysDbController::class);
        return DbSchemas::CreateRow($this->getTable(), $ctrl);
    }

    private static function CreateMockInstance($classname){
        
        if (self::$mock_instance===null){
            self::$mock_instance = [];
        }
        if (!($m = igk_getv(self::$mock_instance, $classname))){
            $m =  new $classname(null, 1);
            self::$mock_instance[$classname] = $m;
        }

        return $m;

    }
    /**
     * check if model created mock instance
     * @return bool 
     */
    public static function IsMockInstance(){
        return isset(self::$mock_instance[static::class]);
    }

    public function __construct($raw = null, $mock=0)
    {
        $this->raw = $raw && ($raw instanceof static) ? $raw :  $this->createRow();
        if (!$this->raw && !$mock) {
            if (igk_environment()->is("DEV")){
                igk_trace();
                igk_wln(__FILE__ . ":" . __LINE__, "raw is null",
                get_class($this), 
                $raw,
                $this->controller, $this->getTable());
            } 
            die("Failed to create dbrow: " . $this->getTable());
        }
        //
        // + copy raw
        //
        if ($raw && ($raw!== $this->raw)) {
            foreach ($raw as $k => $v) {
                if (property_exists($this->raw, $k)) {
                    $this->raw->$k = $v;
                }
            }
        }
        $this->is_mock = $mock;
        
    }
    public function __set($name, $value)
    {
        if (property_exists($this->raw, $name)) {
            $this->raw->$name = $value;
            return;
        }
        throw new IGKException("Failed to access " . $name);
    }
    public function __get($name)
    {
        if (method_exists($this, $m = "get" . $name)) {
            return $this->$m();
        }
        if (igk_environment()->is("DEV")) {
            if (!property_exists($this->raw, $name) && (strpos($name, "::") !== 0)) {
                igk_trace();
                die("property " . static::class . "::$name not present");
            }
        }
        return igk_getv($this->raw, $name);
    }

    protected function _access_offsetExists($offset): bool
    {
        return false;
    }

    protected function _access_offsetGet($offset) 
    {
        return $this->$offset;
    }

    protected function _access_offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    protected function _access_offsetUnset($offset): void
    {
        
    }

    public function geturi()
    {
        return $this->clhref;
    }


    /**
     * return the current table string
     * @return mixed 
     */
    public function getTable()
    {
        return IGKSysUtil::DBGetTableName($this->table, $this->getController());
    }
    /**
     * get current table info
     * @return mixed 
     * @throws IGKException 
     */
    public function getTableInfo()
    {
        $ctrl = igk_getctrl($this->controller ?? SysDbController::class);
        $r =  $ctrl::getDataTableDefinition($this->getTable());
        return igk_getv($r, "ColumnInfo");
    }
    public function getController()
    {
        return igk_getctrl($this->controller, false);
    }
    /**
     * get system dataadapter
     * @return \IGK\Database\DataAdapterBase  
     * @throws IGKException 
     */
    public static function GetSystemDataAdapter()
    {
        return igk_get_data_adapter(igk_getctrl(SysDbController::class));
    }
    /**
     * get current data adapter
     * @return null|\IGK\Database\DataAdapterBase   
     * @throws IGKException 
     */
    public function getDataAdapter()
    {
        if ($this->controller)
            return igk_get_data_adapter($this->getController());
        return self::GetSystemDataAdapter();
    }
    /**
     * disable debug
     * @return null 
     */
    public function __debugInfo()
    {
        return null;
    }

    /**
     * calling static member function
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws Exception 
     */
    public static function __callStatic($name, $arguments)
    {
        if (self::$macros === null) {
            // 
            // + initialize macro definition
            //
            $macros = [                
                "registerMacro" => function ($name, callable $callback) use ( & $macros ) {

                    if (is_callable($callback)) {
                        $callback = Closure::fromCallable($callback);
                    }
                    if (__CLASS__ == static::class) {
                        $macros[$name] = $callback;
                    } else {
                        $macros[igk_ns_name(static::class . "/" . $name)] = $callback;
                    }
                },
                "unregisterMacro" => function ($name) use (& $macros) {
                    unset($macros[igk_ns_name(static::class . "/" . $name)]);
                },
                /**
                 * return the callable
                 */
                "getMacro"=>function($name) use (& $macros ): ?callable{
                
                    return igk_getv($macros, igk_ns_name(static::class . "/" . $name));
                },
                "updateRaw"=>function(ModelBase $target, ModelBase $g){ 
                    if (get_class($target) == get_class($g)){
                        $target->raw = $g->raw;
                    }
                },
                "registerExtension" => function ($classname) use (& $macros) {

                    $f = igk_sys_reflect_class($classname);
                    foreach ($f->getMethods() as $k) {
                        if ($k->isStatic()) {
                            $macros[$k->getName()] = [$classname, $k->getName()];
                        }
                    }
                },
                "getMacroKeys" => function () {
                    return array_keys(self::$macros);
                },
                "getInstance" => function ($name) {
                    return igk_environment()->createClassInstance(static::class);
                }
            ];

            self::$macros = & $macros; 
            // register call extension
            $f = igk_sys_reflect_class(ModelEntryExtension::class);
            foreach ($f->getMethods() as $k) {
                if ($k->isStatic()) {
                    self::$macros[$k->getName()] = [ModelEntryExtension::class, $k->getName()];
                }
            }
            require_once(__DIR__ . "/DefaultModelEntryExtensions.pinc");
            igk_hook(IGKEvents::HOOK_MODEL_INIT, []);
        }

        $_instance_class = static::CreateMockInstance(static::class);

        if ($fc = igk_getv(self::$macros, $name)) {
            $bind = 1;
            if (is_array($fc)) {

                array_unshift($arguments, $_instance_class);
                $bind = 0;
            }
            if ($bind && (static::class !== __CLASS__)) {
                $fc = Closure::bind($fc, null, static::class);
                if (!$fc) {
                    igk_die("Can't bind : ", $name);
                }
            }
            return $fc(...$arguments);
        }
        if ($tfc = igk_getv(self::$macros, igk_ns_name(static::class . "/" . $name))) {
            // + | ----------------------------------------
            // + | bind to instance or call it as extension 
            
            if ($fc = @$tfc->bindTo($_instance_class)){
                return $fc(...$arguments);
            }else {
                array_unshift($arguments, $_instance_class);
                return $tfc(...$arguments);
            }
        }
        if (static::class === __CLASS__) {
            return;
        }
        $c = $_instance_class;
        if (method_exists($c, $name)) {
            return $c->$name(...$arguments);
        }
        igk_dev_wln(array_keys(self::$macros));
        igk_wln("call :".$name);
        igk_trace();
        die("ModelBase: failed to call [" . $name . "] - ".static::class);
    }

    /**
     * call macro on this model
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws Exception 
     */
    public function __call($name, $arguments)
    {

        static $regInvoke;

        if ($regInvoke === null) {
            $regInvoke = 1;
        }
        if ($fc = igk_getv(self::$macros, igk_ns_name(static::class . "/" . $name))) {
            $fc = $fc->bindTo($this);
            return $fc(...$arguments);
        }

        if ($fc = igk_getv(self::$macros, $name)) {
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc);
            }
            array_unshift($arguments, $this);
            //$fc = $fc->bindTo($this); 
            return $fc(...$arguments);
        }
        if (igk_environment()->is("DEV")) {
            igk_trace();
            igk_wln_e("failed to call ", $name);
        }
    }

    /**
     * model to json
     * @param mixed|null $options 
     * @return string|false 
     */
    public function to_json($options = null)
    {
        return Utility::To_JSON($this->raw, $options);
    }

    public function is_mock()
    {
        return $this->is_mock;
    }

    /**
     * return raw data
     * @return mixed 
     */
    public function to_array()
    {
        return (array)$this->raw;
    }
    public function save()
    {
        $pkey = $this->primaryKey;
        $r = $this->getDataAdapter()->update($this->getTable(), $this->raw, [$this->primaryKey => $this->$pkey]);
        return $r && $r->success();
    }
	/**
	 * return json data
	 * @return string|false 
	 * @throws Exception 
	 */
	public function __toString()
	{
		return $this->to_json();
	}

    /**
     * retrieve all registrated model
     * @return array
     */
    public static function GetModels()
    {
        $dir = dirname(__FILE__);
        $hdir = opendir($dir);
        $tab = [];
        $main_cl = static::class;
        $ns = str_replace('/', '\\', dirname(str_replace("\\", "/", $main_cl)));

        while ($c = readdir($hdir)) {
            if (($c == "..") || ($c == ".")) {
                continue;
            }
            if (preg_match("/\.php$/", $c)) {
                $file = implode("/", [$dir, $c]);
                if ($file == __FILE__) {
                    continue;
                }
                include_once($file);
                $name = substr($c, 0, -4);
                $cl = $ns . "\\" . $name;
                if (class_exists($cl) && is_subclass_of($cl, $main_cl)) {
                    $tab[] = $cl;
                }
            }
        }

        closedir($hdir);
        return $tab;
    }
    /**
     * invoke loading
     * @param mixed $arguments 
     * @return mixed 
     * @throws Exception 
     */
    public function __invoke(...$arguments)
    {
        // igk_wln_e("loading", $arguments);
        return static::__callStatic("select_query_rows", $arguments); // ("select_all", $arguments);
    }
}
