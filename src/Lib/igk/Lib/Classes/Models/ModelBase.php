<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelBase.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Models;

use ArrayAccess;
use Closure;
use Exception;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Database\IDbArrayResult;
use IGK\Helper\Utility; 
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\JsonSerializableTrait;
use IGKEvents;
use IGKException; 
use IGKSysUtil;
use Illuminate\Auth\Events\Failed;
use JsonSerializable;  

require_once __DIR__ . "/ModelEntryExtension.php";


/**
 * model base
 * @package IGK\Models
 * @method static ?static|bool create(array|object|static $definition, bool $update=true) - create a row entries
 * @method static static createEmptyRow() - create a empty row - do not insert into database
 * @method static \IGK\Database\DataAdapterBase|null DataAdapter driver() - get the data adapter
 * @method static object|null insertIfNotExists(?array condition = null, ?array options = null) macros:Insert if condition not meet.
 * @method static object|null insert() macros function - DefaultModelEntryExtension
 * @method static array|null select(?array $condition=null) macros function - DefaultModelEntryExtension
 * @method static array|null select_all(?array $condition=null, null|array|DbQueryOptions $options=null) macros function  
 * @method static bool drop() macros function drop table 
 * @method static bool createTable() macros function create table if not exists
 * @method static bool delete(null|array|object $condition) macros function delete table's entries
 * @method static ?static select_row($condition) macros function : select single row
 * @method static void beginTransaction() macros function
 * @method static object|null cacheIsRow() macros function
 * @method static object|null cacheRow() macros function
 * @method static void colKeys() macros function
 * @method static string column(string $column) macros function get column full name
 * @method static void commit() macros function
 * @method static int count(?array $conditions) macros function
 * @method static object|null createFromCache() macros function
 * @method static static createCondition() macros function create condition object
 * @method static ?static createIfNotExists($data_condition, ?mixed $extra=null) macros function: create if not exists
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
 * @method static ?static Get($column, $id, $autoinsert=null) macros function get row from defined value autoinsert
 * @method static ?static GetCache($column, $id, $autoinsert=null) macros function get row from defined value autoinsert
 * @method static ?static getv($array, $i) macros function convert class
 * @method static \IGK\System\Database\QueryBuilder with(string $table, ?string $propertyName=null) prepare command with table 
 */
abstract class ModelBase implements ArrayAccess, JsonSerializable, IDbArrayResult
{
	use ArrayAccessSelfTrait;
    use JsonSerializableTrait;
    const ClosureSeperator = "@";
    const StaticSperator = "::";
    private static $mock_instance;
    private static $sm_model;
    /**
     * retrieve model info
     * @var IGK\Models\Models
     * @return array
     */
    public static function & RegisterModels(): array{
        if (self::$sm_model===null){
            self::$sm_model = [];
        }
        return self::$sm_model;
    }

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
     * @var array
     */
    protected $props_keys;

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
     * class used for view db view
     * @var string
     */
    protected $viewFilter;

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
    protected $is_mock;

    /**
     * define unset field for update
     * @var mixed
     */
    protected $update_unset;

    public function getUpdateUnset()
    {
        return $this->update_unset;
    }

    public static function IsMacrosInitialize(){
        return !is_null(self::$macros);
    }

    public function _json_serialize(){
        return json_encode((object)array_filter($this->to_array()));
    }

    /**
     * get factory class
     * @return string 
     * @throws IGKException 
     */
    public function getFactory()
    {
        if ($this->factory === null) {
            $name = $this::name(); 
            $this->factory = $this->getController()::ns("Database\\Factories\\" . $name . "Factory");
        }
        return $this->factory;
    }
    /**
     * get view filter class 
     * @return string
     */
    public function getViewFilter(){
        if ($this->viewFilter === null) {
            $name = $this::name(); 
            $this->viewFilter = $this->getController()::ns("Database\\ViewFilter\\" . $name . "ViewFilter");
        }
        return $this->viewFilter;
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
                $inf = $g->tableRowReference;
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
    public static function IsMockInstance($model){
        return igk_getv(self::$mock_instance, static::class) === $model;
    }

    public function __construct($raw = null, $mock=0, $unset=false)
    {
        $this->_initialize($raw, $mock, $unset);   
        $tab = & self::RegisterModels();       
        if (!isset($tab[$tb = $this->table()])){
            $ctrl = $this->getTableInfoController();              
            $tab[$tb]= (object)[
                'model'=> static::class,
                'info'=> [],
                "ref" => DbSchemas::GetTableRowReference($tb, $ctrl)
            ]; 
        }
    }
    protected function _initialize($raw=null, $mock=0, $unset=false){
        $this->raw = $raw && ($raw instanceof static) ? $raw : $this->createRow();
        if (!$this->raw && !$mock) {
            if (igk_environment()->isDev()){
              
                igk_wln(["access"=>__FILE__ . ":" . __LINE__, 
                "msg"=>"raw is null",
                "class"=>get_class($this), 
                "data"=>$raw,
                "controller"=>$this->controller, 
                "table"=>$this->getTable(), 
                "dummy"=>$this->raw,               
                ]);
            } 
            throw new \IGKException("Failed to create dbrow: " . $this->getTable());
        }
        // + | ----------------------------------------------------------
        // + | copy raw if not instance 
        // + | 
        if ($raw && ($raw!== $this->raw)) {
            $props = array_fill_keys(array_keys((array)$this->raw), 1);           
            foreach ($raw as $k => $v) {
                if (property_exists($this->raw, $k)) {
                    $this->raw->$k = $v;
                    unset($props[$k]);
                }
            }
            if ($unset && (count($props)>0)){
                foreach(array_keys($props) as $v){
                    unset($this->raw->$v);
                }
                $this->props_keys = $props;
            }           
        }
        $this->is_mock = $mock;     
    }
    public function __set($name, $value)
    {
        if ($this->is_mock()){
            if (is_null($this->raw)){
                $this->raw = (object)[];
            }
            $this->raw->$name = $value;
            return $this;
        }
        if ($this->raw && (property_exists($this->raw, $name)|| isset($this->props_keys[$name]))) {
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
        if (igk_environment()->isDev()) {
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
    public function getTable(){
        return IGKSysUtil::DBGetTableName($this->table, $this->getController());
    }
    /**
     * get current table info
     * @return mixed 
     * @throws IGKException 
     */
    public function getTableInfo()
    {
        $ctrl = $this->getTableInfoController(); 
        $r =  $ctrl::getDataTableDefinition($this->getTable());
        if (is_null($r) && igk_environment()->isDev()){
            igk_wln_e("column info can't be resolved for ", $this->getTable());
        }
        return $r->columnInfo;
    }
    protected function getTableInfoController(){
        return igk_getctrl($this->controller ?? SysDbController::class);
    }
    public function getController()
    {
        if (!empty($this->controller))
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
            // ---------------------------------------------------------------------------
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
                        $macros[static::class . self::ClosureSeperator . $name] = $callback;
                    }
                },
                "unregisterMacro" => function ($name) use (& $macros) {
                    unset($macros[static::class . self::ClosureSeperator . $name]);
                },
                /**
                 * return the callable
                 */
                "getMacro"=>function($name) use (& $macros ): ?callable{                
                    return igk_getv($macros, static::class . self::ClosureSeperator . $name);
                },
                "updateRaw"=>function(ModelBase $target, ModelBase $g){ 
                    if (get_class($target) == get_class($g)){
                        $target->raw = $g->raw;
                    }
                },
                "registerExtension" => function ($classname) use (& $macros) {
                    $cl = static::class;
                    $f = igk_sys_reflect_class($classname);
                    foreach ($f->getMethods() as $k) {
                        if ($k->isStatic()) {
                            $macros[$cl.self::StaticSperator.$k->getName()] = [$classname, $k->getName()];
                        }
                    }
                },
                "getMacroKeys" => function()use(& $macros){
                    return array_keys($macros);
                },
                "getInstance" => function(){
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
            // 
            // init all model so that will be 
            //
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

        $failed = false;
        $result = self::_InvokeMacros(self::$macros, $name, $_instance_class, $arguments, $failed);
        if (!$failed){
            return $result;
        }

        // if ($tfc = igk_getv(self::$macros, static::class . self::ClosureSeperator . $name)) {
        //     // + | ----------------------------------------
        //     // + | bind to instance or call it as extension             
        //     if ($fc = @$tfc->bindTo($_instance_class)){
        //         return $fc(...$arguments);
        //     }else {
        //         array_unshift($arguments, $_instance_class);
        //         return $tfc(...$arguments);
        //     }
        // }
        if (static::class === __CLASS__) {
            return;
        }
        $c = $_instance_class;
        if (method_exists($c, $name)) {
            return $c->$name(...$arguments);
        }
        if (igk_environment()->isDev()){
            igk_dev_wln(array_keys(self::$macros));
            igk_wln("call :".$name);
            igk_trace();
        }
        die("ModelBase: failed to call [" . $name . "] - ".static::class);
    }
    private static function _InvokeMacros($macros, $name, $instance, $arguments, & $failed=false){
        $key = static::class .self::StaticSperator. $name;
        if ($fc = igk_getv($macros, $key)){
            // static closure
            array_unshift($arguments, $instance); 
            return $fc(...$arguments);
        }
        $key = static::class .self::ClosureSeperator. $name;
        if ($fc = igk_getv($macros, $key)){
            // instance closure          
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc);
            }
            $fc = $fc->bindTo($instance);
            return $fc(...$arguments);
        }

        if ($fc = igk_getv($macros, $name)) {
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc);
            }
            array_unshift($arguments, $instance);
            return $fc(...$arguments);
        }
        $failed = true;
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

        // static $regInvoke;
        // if ($regInvoke === null) {
        //     $regInvoke = 1;
        // }
        $failed = false;
        $result = self::_InvokeMacros(self::$macros, $name, $this, $arguments, $failed);       
        // $regInvoke = null;
        if ($failed && igk_environment()->isDev()) {
            igk_trace();
            igk_wln(array_keys(self::$macros));
            igk_wln_e(sprintf("failed to call macros %s::%s", static::class, $name));
        }
        return $result;
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
    /**
     * update field and return a boolean
     * @return bool 
     */
    public function save():bool
    {
        $pkey = $this->primaryKey;
        if (!empty($pkey)){
            if ($r = $this->update($this->raw,  [$this->primaryKey => $this->$pkey] )){
                return $r->success();
            }
        } 
        return false;
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
