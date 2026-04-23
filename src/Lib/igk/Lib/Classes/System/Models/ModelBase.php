<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelBase.php
// @date: 20221120 23:18:02
// @desc: 
namespace IGK\System\Models;
use ArrayAccess;
use Closure;
use Error;
use Exception;
use IGK\Actions\Dispatcher;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Database\IDbArrayResult;
use IGK\Database\RefColumnMapping;
use IGK\Helper\Database;
use IGK\Helper\StringUtility;
use IGK\Helper\Utility;
use IGK\System\Caches\DBCaches;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\JsonSerializableTrait;
use IGKEvents;
use IGKException;
use IGKSysUtil;
use JsonSerializable;
use IGK\Models\ModelEntryExtension;
use IGK\System\Database\DbUtils;
use IGK\System\Database\Helper\DbUtility;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Traits\MacrosConstant;
use IGK\Constants;
use IGK\Database\DbRowDefEntry;
use IGK\Helper\StringDisplay;
use IGK\System\IInjectable;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

require_once IGK_LIB_CLASSES_DIR . '/Models/Inc/ModelEntryExtension.php';
/**
 * root model base 
 * @package IGK\System\Models
 */
abstract class ModelBase implements ArrayAccess, JsonSerializable, IDbArrayResult, IInjectable
{
    use ArrayAccessSelfTrait;
    use JsonSerializableTrait;
    /**
    * Constant: auth key.
    * @var mixed
    */
    const AuthKey = '::auth';
    /**
    * Constant: closure seperator.
    * @var mixed
    */
    const ClosureSeperator = "@";
    /**
    * Constant: static separator.
    * @var mixed
    */
    const StaticSeparator = "::";
    /**
    * Constant: extra field option.
    * @var mixed
    */
    const EXTRA_FIELD_OPTION = 'extra';
    /**
    * auto generate doc.
    */    const JoinOnMethodPrefix = "joinOn";
    /**
    * auto generate doc.
    */    const TargetOnMethodPrefix = "targetOn";
    /**
     * now function 
     */
    const FC_NOW = 'NOW()';
    /**
    * Property: mock instance.
    * @var mixed
    */
    private static $mock_instance;
    /**
    * Property: model.
    * @var mixed
    */
    private static $sm_model;
    /**
    * Flag: is new.
    * @var mixed
    */
    private $m_isNew;
    /**
     * alias keys
     * @var ?array
     */
    private $m_alias;
    /**
     * get the table prefix stored on initialize 
     * @return mixed 
     */
    protected final
    function tablePrefix()
    {
        return $this->getTableInfo()->prefix;
    }
    /**
     * get if this module is new one
     * @return bool 
     */
    public function isNew(): ?bool
    {
        if (is_null($this->m_isNew)) {
            if ($id = $this::last_id()) {
                $primary = $this->getRefId();
                $this->m_isNew = $this->$primary == $id;
            }
        }
        return $this->m_isNew;
    }
    /**
     * current raw keys
     * @return null|array 
     */
    public function getRowKeys(): ?array
    {
        return array_keys((array)$this->raw);
    }
    /**
     * retrieve model info
     * @var IGK\Models\Models
     * @return array
     */
    public static function &RegisterModels(): array
    {
        if (self::$sm_model === null) {
            self::$sm_model = [];
        }
        return self::$sm_model;
    }
    /**
     * resolve column name
     * @param string $name 
     * @return string 
     * @throws IGKException 
     */
    public function getColumn(string $name)
    {
        static $list = null;
        if (is_null($list)) {
            $list = array();
        }
        $inf = $this->getTableColumnInfo() ?? [];
        if (isset($inf[$name])) {
            return $name;
        }
        $v_tabinfo = $this->getTableInfo();
        $prefix = $v_tabinfo->prefix;
        $n = strtolower(static::class . "::" . $prefix . $name);
        if (isset($list[$n])) {
            return $list[$n];
        }
        $cl = static::class;
        foreach ($inf as $i) {
            $k = strtolower($cl . "::" . $i->clName);
            $list[$k] = $i->clName;
            if (($n == $k) || (
                ((!$prefix) && preg_match("/" . $name . "$/i", $i->clName))
            )) {
                return $i->clName;
            }
        }
        igk_die(sprintf('column %s not found', $n));
    }
    /**
     * stored macros
     * @var mixed
     */
    private static $sm_macros;
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
    * auto generate doc.
    * @var array
    */
    protected $props_keys;
    /**
    * auto generate doc.
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
    protected $display = null;
    /**
     * model controller class name 
     * @var string
     */
    protected $controller = SysDbController::class;
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
    /**
    * Returns Update Unset.
    */
    public function getUpdateUnset()
    {
        return $this->update_unset;
    }
    /**
    * Returns true if Macros Initialize.
    */
    public static function IsMacrosInitialize()
    {
        return !is_null(self::$sm_macros);
    }
    /**
    * auto generate doc.
    * @return object
    */
    public function _json_serialize()
    {
        return  (object)array_filter($this->to_array());
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
    public function getViewFilter()
    {
        if ($this->viewFilter === null) {
            $name = $this::name();
            $this->viewFilter = $this->getController()::ns("Database\\ViewFilter\\" . $name . "ViewFilter");
        }
        return $this->viewFilter;
    }
    /**
     * change set raw value
     * @param mixed $name 
     * @param mixed $value 
     * @return $this 
     */
    public function set($name, $value)
    {
        $this->raw->{$name} = $value;
        return $this;
    }
    /**
     * how to display this model
     * @return ?string 
     * @throws Exception 
     */
    public function display()
    {
        $cl = get_class($this);
        if (is_callable($fc = $cl::__callStatic("getMacro", ["display"]))){
            $fc = $fc->bindTo($this);
            $v_reflect_func = new ReflectionFunction($fc);
            $params = Dispatcher::GetInjectArgs($v_reflect_func, [], $this->getController());
            return call_user_func_array($fc, $params);
        }
        $d = $this->display;
        if ($d){
            return StringDisplay::Display($d, array_keys($this->to_array()), $this);
        } 
        return null;
    }
    /**
     * get reference primary key column
     * @return string 
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    /**
     * get reference id key column
     * @return string 
     */
    public function getRefId()
    {
        return $this->refId;
    }
    /**
     * get form fields
     * @return ?array 
     */
    public function getFormFields(): ?array
    {
        return $this->form_fields;
    }
    /**
     * return the display column properties
     * @return ?string 
     */
    public function getDisplay()
    {
        return $this->display;
    }
    /**
     * get custom table column info to create dummy row
     * @return null|array 
     */
    protected function _getTableColumnInfo(): ?array
    {
        if (method_exists($this, "getDataTableDefinition")) {
            if ($g = $this->getDataTableDefinition()) {
                return $g->tableRowReference;
            }
        }
        return null;
    }
    /**
     * create dummy row
     * @return object|null dummy raw 
     */
    protected function createRow()
    {
        if ($inf = $this->_getTableColumnInfo()) {
            return DbSchemas::CreateObjFromInfo($inf);
        }
        $ctrl = igk_getctrl($this->controller ?? SysDbController::class);
        return DbSchemas::CreateRow($this->getTable(), $ctrl);
    }
    /**
    * Flag: is create mocking.
    * @var mixed
    */
    private static $sm_isCreateMocking;
    /**
     * create a mock instance. 
     * @param string $classname 
     * @return mixed 
     * @throws Exception 
     */
    private static function CreateMockInstance(string $classname)
    {
        static $sm_mocking_creation;
        if (DbSchemas::IsLoadingFromSchema()) {
            igk_die("Can't create a mock instance. DbSchemas is loading... " . static::class);
        }
        if (is_null($sm_mocking_creation)) {
            $sm_mocking_creation = [];
        }
        if (isset($sm_mocking_creation[$classname])) {
            /**
             * possibility of not registrated class or mocking 
             */
            igk_trace();
            igk_die("flag to create mocking instance. " . $classname);
        }
        $sm_mocking_creation[$classname] = 1;
        self::$sm_isCreateMocking = true;
        if (self::$mock_instance === null) {
            self::$mock_instance = [];
        }
        if (!($m = igk_getv(self::$mock_instance, $classname))) {
            $m = new $classname(null, 1);
            self::$mock_instance[$classname] = $m;
        }
        unset($sm_mocking_creation[$classname]);
        self::$sm_isCreateMocking = null;
        return $m;
    }
    /**
     * check for mocking creating
     * @return null|bool 
     */
    public static function IsCreateMocking(): ?bool
    {
        return self::$sm_isCreateMocking;
    }
    /**
     * check if model created mock instance
     * @return bool 
     */
    public static function IsMockInstance($model)
    {
        return igk_getv(self::$mock_instance, static::class) === $model;
    }
    /**
    * auto generate doc.
    * @param bool $unset unset unused property definition
    * @return void
    */
    public function __construct($raw = null, $mock = 0, bool $unset = false)
    {
        $this->_initialize($raw, $mock, $unset);
        $tab = &self::RegisterModels();
        // + | if ($tab && !isset($tab[$tb = $this->table()])) {
        if (!isset($tab[$tb = $this->table()])) {
            $ctrl = $this->getTableInfoController();
            $tab[$tb] = (object)[
                'model' => static::class,
                'info' => [],
                "ref" => DbSchemas::GetTableRowReference($tb, $ctrl),
                'referenceController' => $ctrl
            ];
        }
        if (empty($this->controller)) {
            $this->controller = $tab[$tb]->referenceController;
        }
    }
    /**
     * initialize the model
     * @param mixed|array|object $raw 
     * @param int $mock 
     * @param bool $unset 
     * @return void 
     * @throws IGKException 
     * @throws Error 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _initialize($raw = null, $mock = 0, $unset = false)
    {
        ($raw && !is_array($raw) && !is_object($raw)) && igk_die('raw not a valid data');
        $t =  $this->getTable();
        $ctrl = $this->getController();
        $tableReference = null;
        $v_inf = DBCaches::GetColumnInfo($t, $ctrl, $tableReference);
        $this->raw = $raw && ($raw instanceof static) ? $raw : $this->createRow();
        if (!$this->raw && !$mock) {
            if (igk_environment()->isDev()) {
                igk_wln([
                    "access" => __FILE__ . ":" . __LINE__,
                    "msg" => "raw is null",
                    "class" => get_class($this),
                    "data" => $raw,
                    "controller" => $this->controller,
                    "table" => $t,
                    "current_raw" => $this->raw,
                ]);
            }
            throw new \IGKException("Failed to create dbrow: missing table definition " . $t);
        }
        // + | ----------------------------------------------------------
        // + | copy raw if not instance 
        // + | 
        if ($raw && ($raw !== $this->raw)) {
            $trow = ($this->raw instanceof DbRowDefEntry) ? $this->raw->initDefArray() :
                array_fill_keys(array_keys((array)$this->raw), 1);
            $props = $trow;
            if ($unset && $this->hidden) {
                foreach ($this->hidden as $k) {
                    $props[$k] = 0;
                }
            }
            $v_prefix = igk_getv($tableReference, 'prefix');
            foreach ($raw as $k => $v) {
               if (self::_CheckPropertyExists($props, $k, $v_prefix)){
                    $this->raw->$k = Database::GetValueFromLayoutInfo($v, $k, $v_inf);
                    unset($props[$k]);
                }
            }
            if ($raw instanceof RefColumnMapping) {
                $this->m_alias = $raw->getAlias();
            }
            if ($unset && (count($props) > 0)) {
                foreach (array_keys($props) as $v) {
                    unset($this->raw->$v);
                }
                $this->props_keys = $props;
            }
        }
        $this->is_mock = $mock;
    }
    /**
    * auto generate doc.
    * @param mixed $props
    * @param mixed & $k
    * @param null|mixed $prefix
    * @return bool
    */
    protected static function _CheckPropertyExists($props, &$k, $prefix=null):bool{
        if (key_exists($k, $props)){
            return true;
        }
        if($prefix)
            if ($k!=($ck = StringUtility::AutoPrefix($k, $prefix))){
                if (key_exists($ck, $props)){
                    $k = $ck;
                    return true;
                }
            }
        return false;
    }
    /**
    * destructor
    * @param mixed $name
    * @param mixed $value
    */
    public function __set($name, $value)
    {
        $prefix = $this->tablePrefix();
        $pname = DbUtility::TreatColumnName($name, $prefix);
        if ($pname != $name) {
            $name = $pname;
        }
        if ($this->is_mock()) {
            if (is_null($this->raw)) {
                $this->raw = (object)[];
            }
            $this->raw->$name = $value;
            return $this;
        }
        if ($this->raw && (property_exists($this->raw, $name) || isset($this->props_keys[$name]))) {
            $this->raw->$name = $value;
            return;
        }
        throw new IGKException("Failed to access " . $name);
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name)
    {
        if (method_exists($this, $m = "get" . $name)) {
            return $this->$m();
        }
        $prefix = $this->tablePrefix();
        $pname = DbUtility::TreatColumnName($name, $prefix);
        if ($pname != $name) {
            $name = $pname;
        }
        if (!property_exists($this->raw, $name)) {
            if ($rp = $this->m_alias) {
                $name = igk_getv($rp, $name);
            }
        }
        if (igk_environment()->isDev() && $name && (strpos($name, "::") !== 0)) {
            if ((($this->raw instanceof DbRowDefEntry) && !$this->raw->keyExists($name)) &&
                (!property_exists($this->raw, $name))){
                igk_die("model property [" . static::class . "::$name] not present");
            }
        }
        return igk_getv($this->raw, $name);
    }
    /**
    * Access offset exists.
    * @param mixed $offset
    * @return bool
    */
    protected function _access_offsetExists($offset): bool
    {
        return false;
    }
    /**
    * Access offset get.
    * @param mixed $offset
    */
    protected function _access_offsetGet($offset)
    {
        return $this->$offset;
    }
    /**
    * Access offset set.
    * @param mixed $offset
    * @param mixed $value
    * @return void
    */
    protected function _access_offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }
    /**
    * Access offset unset.
    * @param mixed $offset
    * @return void
    */
    protected function _access_offsetUnset($offset): void {}
    /**
    * Geturi.
    */
    public function geturi()
    {
        return $this->clhref;
    }
    /**
     * return the defined table's name 
     * @return null|string 
     */
    public function getDefTable(): ?string
    {
        return $this->table;
    }
    /**
     * return the current table name
     * @return ?string 
     */
    public function getTable()
    {
        return IGKSysUtil::DBGetTableName($this->table, $this->getController());
    }
    /**
     * get current table column info info
     * @return ?array<string, \IGK\Database\DbColumnInfo> 
     * @throws IGKException 
     */
    public function getTableColumnInfo(): ?array
    {
        $tn = $this->getTable();
        if ($g = DBCaches::GetColumnInfo($tn, $this->getController())) {
            if (is_array($g)) {
                return $g;
            }
            return $g->columnInfo;
        }
        return null;
    }
    /**
     * retrieve cache table info
     * @var IGK\System\Models\getTableInfo
     */
    public function getTableInfo()
    {
        $tn = $this->getTable();
        if ($g = DBCaches::GetTableInfo($tn, $this->getController())) {
            return $g;
        }
        return null;
    }
    /**
     * get table info controller 
     * @return null|BaseController 
     * @throws IGKException 
     */
    protected function getTableInfoController()
    {
        return igk_getctrl($this->controller ?? SysDbController::class);
    }
    /**
    * auto generate doc.
    * @return null|BaseController|void
    */
    public function getController()
    {
        if (!empty($this->controller))
            return igk_getctrl($this->controller, false);
    }
    /**
    * auto generate doc.
    * @param null|string $utility_name
    * @return object|null
    */
    public function utility(?string $utility_name = null)
    {
        $cl = $utility_name ?? basename(igk_uri(get_class($this)));
        $ctrl = $this->getController();
        $m = $ctrl->modelUtility($cl);
        return $m;
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
     * @return mixed 
     */
    public function __debugInfo()
    {
        return [];
    }
    /**
    * auto generate doc.
    * @return
    */
    private static function &_InitDbMacros()
    {
        // + initialize macro definition
        //
        $macros = [
            MacrosConstant::RegisterMacroMethod => function (string $name, callable $callback) use (&$macros) {
                if (is_callable($callback)) {
                    $callback = Closure::fromCallable($callback);
                }
                if (__CLASS__ == static::class) {
                    $macros[$name] = $callback;
                } else {
                    $macros[static::class . self::ClosureSeperator . $name] = $callback;
                }
            },
            MacrosConstant::UnRegisterExtensionMethod   => function (string $name) use (&$macros) {
                unset($macros[static::class . self::ClosureSeperator . $name]);
            },
            /**
             * return the callable
             */
            MacrosConstant::getMacroMethod => function (string $name) use (&$macros): ?callable { 
                $l = [self::ClosureSeperator, self::StaticSeparator];
                $r = null;
                if (StringUtility::StrArrayContains($name, $l)){
                    $r= igk_getv($macros, $name);
                }             
                else $r = igk_getv($macros, static::class . self::ClosureSeperator . $name);                
                return (is_null($r) || is_callable($r)) ? $r : null;
            },
            "updateRawFrom" => function (ModelBase $target, ModelBase $g) {
                if (get_class($target) == get_class($g)) {
                    $target->raw = $g->raw;
                }
            },
            MacrosConstant::RegisterExtensionMethod => function ($classname) use (&$macros) {
                $cl = igk_getv($macros, MacrosConstant::REF_MACROS) ?? static::class;                
                $f = igk_sys_reflect_class($classname);
                foreach ($f->getMethods() as $k) {
                    if ($k->isStatic()) {
                        $macros[$cl . self::StaticSeparator . $k->getName()] = [$classname, $k->getName()];
                    }
                }
            },
            MacrosConstant::getMacroKeysMethod => function (?ModelBase $filter = null) use (&$macros) {
                if (is_array($macros)) {
                    $v_key = array_keys($macros);
                    if ($filter) {
                        $cl = get_class($filter);
                        return array_filter($v_key, function ($i) use ($cl) {
                            return igk_str_startwith($i, $cl);
                        });
                    }
                    return $v_key;
                }
            },
            "getInstance" => function () {
                return igk_environment()->createClassInstance(static::class);
            }
        ];
        return $macros;
    }
    /**
    * auto generate doc.
    * @return
    */
    private static function _InitMacros(){
         if (self::$sm_macros === null) {
            self::$sm_macros = &self::_InitDbMacros();
            require_once(IGK_LIB_CLASSES_DIR . "/Models/Inc/DefaultModelEntryExtensions.pinc");
            // + | ----------------------------------------------------
            // + | init all model
            // + |
            igk_hook(IGKEvents::HOOK_MODEL_INIT, [static::class]);
        }
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
        self::_InitMacros();
        $_instance_class = static::CreateMockInstance(static::class);
        if ($fc = igk_getv(self::$sm_macros, $name)) {
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
        $result = self::_InvokeMacros(self::$sm_macros, $name, $_instance_class, $arguments, $failed);
        if (!$failed) {
            return $result;
        }
        if (static::class === __CLASS__) {
            return;
        }
        $c = $_instance_class;
        if (method_exists($c, $name)) {
            return $c->$name(...$arguments);
        }
        $tconst = igk_sys_reflect_class_get_constants(static::class);
        $p = Constants::DB_MODEL_FULLNAME_FIELD_PREFIX;
        if (preg_match('/^' . $p . '/', $name)) {
            $fns = $name;
            $name = substr($fns, strlen($p));
            if (in_array($kp = Constants::DB_MODEL_FIELD_PREFIX . $name, array_keys($tconst))) {
                return $c->column($tconst[$kp]);
            }
            $name = $fns;
        }
        if (igk_environment()->isDev()) {
            igk_dev_wln(array_keys(self::$sm_macros));
            igk_dev_wln("call :" . $name);
            igk_trace();
        }
        igk_die("ModelBase: failed to call [" . $name . "] - " . static::class);
    }
    /**
     * invoke registrated macros function 
     * @param mixed $macros macros list 
     * @param string $name to get 
     * @param mixed $instance that will call the 
     * @param mixed $arguments arguments to pass 
     * @param bool $failed ref failed result 
     * @return mixed 
     * @throws IGKException 
     */
    private static function _InvokeMacros($macros, $name, $instance, $arguments, &$failed = false)
    {
        $R_macros =  & self::$sm_macros;
        $key = static::class . self::StaticSeparator . $name;
        if ($fc = igk_getv($macros, $key)) {
                // + | try to inject argument
                $tc = $fc;
                if (is_array($tc)) {
                    $tc = Closure::fromCallable($tc);
                }
                $parameters = (new ReflectionFunction($tc))->getParameters();
                array_shift($parameters);
                $arguments = Dispatcher::GetInjectArgsByParameters($parameters, $arguments);
            array_unshift($arguments, $instance);
            return $fc(...$arguments);
        }
        $key = static::class . self::ClosureSeperator . $name;
        if ($fc = igk_getv($macros, $key)) {
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc);
            }
            $fc = $fc->bindTo($instance);
            return $fc(...$arguments);
        }
        if ($fc = igk_getv($macros, $name)) {
            // + | --------------------------------------------------------------------
            // + | call direct macros
            // + |            
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc); 
            }
            return $fc(...$arguments);
        }
        $key = igk_uri('@auto_register/' . get_class($instance));
        if (!isset($R_macros[$key])) {
            if ($cl = Database::GetMacroClass($instance)) {
                $R_macros[MacrosConstant::REF_MACROS] = get_class($instance);
                $reg_ext_fc = igk_getv($R_macros, MacrosConstant::RegisterExtensionMethod);
                call_user_func_array($reg_ext_fc, [$cl]); 
                unset($R_macros[MacrosConstant::REF_MACROS]);
                if (method_exists($cl, $name)) {
                    $fc = [$cl, $name];
                    $R_macros[$name] = $fc;
                    $parameters = (new ReflectionMethod($cl, $name))->getParameters();
                    array_shift($parameters);
                    $arguments = Dispatcher::GetInjectArgsByParameters($parameters, $arguments);
                    $instance && array_unshift($arguments, $instance);
                    return $fc(...$arguments);
                }
                $R_macros[$key] = $cl;
            }
        }
        if (method_exists($T1 = ModelEntryExtension::class, $name)) {
            $fc = [$T1, $name];
            $key = implode(MacrosConstant::StaticSeparator,$fc);
            if (!isset($R_macros[$key]))
                $R_macros[$key] = $fc;
            $instance && array_unshift($arguments, $instance);
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
        // + | reserve joinOn+ method 
        // + |  
        if (preg_match("/^(" . self::JoinOnMethodPrefix . "(?P<name>\w+))/", $name, $tab)) {
            $b = DbUtils::GetDeclaredColumnConstants($this);
            $n = $tab['name'];
            $b = array_filter($b, function ($i) use ($n) {
                return  $n == StringUtility::ConstantToCamelCaseClassName($i);
            });
            if (count($b) == 1) {
                $cl = $b[key($b)];
                return $this->joinTableColumnOn($cl, ...$arguments);
            }
            throw new IGKException("invalid call : " . self::JoinOnMethodPrefix);
        }
        if (preg_match("/^(" . self::TargetOnMethodPrefix . "(?P<name>\w+))/", $name, $tab)) {
            $b = DbUtils::GetDeclaredColumnConstants($this);
            $n = $tab['name'];
            $b = array_filter($b, function ($i) use ($n) {
                return  $n == StringUtility::ConstantToCamelCaseClassName($i);
            });
            if (count($b) == 1) {
                $cl = $b[key($b)];
                return $this->joinTableTargetOn($cl, ...$arguments);
            }
            throw new IGKException("invalid call : " . self::TargetOnMethodPrefix);
        }
        $failed = false;
        if (is_null(self::$sm_macros)){
            self::_InitMacros();
        }
        $result = self::_InvokeMacros(self::$sm_macros, $name, $this, $arguments, $failed);
        if ($failed && igk_environment()->isDev()) {
            $msg = sprintf("failed to call macros %s::%s", static::class, $name);
            if (!defined('IGK_THROW_MISSING_MACROS_EXCEPTION')) {
                echo '<div ><font color="red"><b>' . $msg . '</b></font></div>';
                igk_trace();
                igk_dev_wln($msg);
            } else {
                throw new IGKException($msg);
            }
        }
        return $result;
    }
    /**
     * model to json
     * @param mixed|null $options 
     * @param mixed|null $json_flag json flag
     * @return string|false 
     */
    public function to_json($options = null, int $json_flag=0)
    {
        return Utility::To_JSON($this->raw, $options, $json_flag);
    }
    /**
    * Returns true if mock.
    */
    public function is_mock()
    {
        return $this->is_mock;
    }
    /**
     * return raw data
     * @return mixed 
     */
    public function to_array($alias = false): array
    {
        if ($this->m_alias) {
            $keys = array_keys($this->m_alias);
            return array_combine($keys, array_map(function ($a) {
                if (false !== strpos($a, '.')) {
                    $a = implode('.', array_slice(explode('.', $a), -1));
                }
                return igk_getv($this->raw, $a);
            }, $this->m_alias));
        }
        return (array)$this->raw;
    }
    /**
     * update field and return a boolean
     * @return bool 
     */
    public function save(bool $autoupdate = true): bool
    {
        $pkey = $this->primaryKey;
        if (!empty($pkey)) {
            $cond = [$this->primaryKey => $this->$pkey];
            if ($r = $this->update($this->raw, $cond)) {
                $u =  is_bool($r) ? $r : $r->success();
                if ($u && $autoupdate) {
                    if ($m = $this->select_row($cond)) {
                        $this->raw  = $m->raw;
                    }
                }
                return $u;
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
    public static function GetModels(BaseController $controller)
    {
        $dir = $controller->getClassesDir() . "/Models"; 
        $hdir = opendir($dir);
        $tab = [];
        $main_cl = ModelBase::class;
        if ($ns = $controller->getEntryNamespace())
            $ns .= "\\Models";
        $ns = str_replace('/', '\\', str_replace("\\", "/", $ns));
        while ($c = readdir($hdir)) {
            if (($c == "..") || ($c == ".")) {
                continue;
            }
            if (preg_match("/\.php$/", $c)) {
                $file = implode("/", [$dir, $c]);
                if ($file == __FILE__) {
                    continue;
                }
                $name = substr($c, 0, -4);
                if ($name == Constants::ENTRY_BASE_MODEL_CLASS)
                    continue;
                include_once($file);
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
        return static::__callStatic("select_query_rows", $arguments); 
    }
    /**
    * unset innacessible property
    * @param mixed $name
    */
    public function __unset($name)
    {
        $this->$name = null;
        unset($this->raw->$name);
    }
    /**
    * check if isset innaccessible property
    * @param mixed $name
    */
    public function __isset($name)
    {
        if (isset($this->raw->$name)) {
            return true;
        }
        if ($prefix = $this->tablePrefix()) {
            $pname = DbUtility::TreatColumnName($name, $prefix);
            return isset($this->raw->{$pname});
        }
        return false;
    }
    /**
     * check if column exists in raw definition
     * @param string $name 
     * @return bool 
     */
    public function columnExists(string $name)
    {
        return property_exists($this->raw, $name);
    }
}