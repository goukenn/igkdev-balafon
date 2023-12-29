<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelBase.php
// @date: 20221120 23:18:02
// @desc: 

namespace IGK\System\Models;

use ArrayAccess;
use Closure;
use Exception;
use IGK\Actions\Dispatcher;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Database\IDbArrayResult;
use IGK\Database\RefColumnMapping;
use IGK\Helper\Database;
use IGK\Helper\Utility;
use IGK\System\Caches\DBCaches;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\JsonSerializableTrait;
use IGKEvents;
use IGKException;
use IGKSysUtil;
use JsonSerializable;
use IGK\Models\ModelEntryExtension;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IToArrayResolver;
use IGK\System\Traits\MacrosConstant;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

require_once IGK_LIB_CLASSES_DIR . '/Models/Inc/ModelEntryExtension.php';
/**
 * root model base 
 * @package IGK\System\Models
 */
abstract class ModelBase implements ArrayAccess, JsonSerializable, IDbArrayResult
{
    use ArrayAccessSelfTrait;
    use JsonSerializableTrait;
    const AuthKey = '::auth';
    const ClosureSeperator = "@";
    const StaticSperator = "::";
    /**
     * now function 
     */
    const FC_NOW = 'NOW()';
    private static $mock_instance;
    private static $sm_model;
    private $m_isNew;
    /**
     * alias keys
     * @var ?array
     */
    private $m_alias;
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
    public function getRowKeys():?array{
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
    public function getColumn(string $name){
        static $list = null;
        if (is_null($list)){
            $list = array();
        }
        $inf = $this->getTableColumnInfo() ?? igk_die("table info required.");
        if (isset($inf[$name])){
            return $name;
        }
        $tabprefix = $this->getTableInfo();
        $prefix = $tabprefix->prefix;
        $n = strtolower(static::class."::".$prefix.$name);
        if (isset($list[$n])){
            return $list[$n];
        }
        $cl = static::class;
        foreach($inf as $i){
            $k = strtolower($cl."::".$i->clName);
            $list[$k] = $i->clName;
            if (($n==$k) || (
                ((!$prefix) && preg_match("/".$name."$/i", $i->clName))
            ))
            {
                return $i->clName;
            }
            
        }
        igk_die(sprintf('column %s not found',$n));
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

    public function getUpdateUnset()
    {
        return $this->update_unset;
    }

    public static function IsMacrosInitialize()
    {
        return !is_null(self::$macros);
    }
    /**
     * 
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
     * @return mixed 
     * @throws Exception 
     */
    public function display()
    {
        $d = $this->display;
        if (isset($this->raw->{$d}))
            return $this->{$d};
        $cl = get_class($this);
        if (is_callable($fc = $cl::__callStatic("getMacro", ["display"]))) {
            return $fc($this);
        }
        return $this->to_json();
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
    public function getFormFields():?array
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
    protected function _getTableColumnInfo():?array{
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
    private static $sm_isCreateMocking;

    private static function CreateMockInstance($classname)
    {
        static $sm_mocking_creation;
        if (DbSchemas::IsLoadingFromSchema()){
            igk_die("can't create a mock instance because of loadingFromSchema. ".static::class);
        }
        if (is_null($sm_mocking_creation)){
            $sm_mocking_creation = [];
        }
        if (isset($sm_mocking_creation[$classname])){
            /**
             * possibility of not registrated class or mocking 
             */
            igk_trace();
            igk_die("flag to create mocking instance. ".$classname);
        }
        $sm_mocking_creation[$classname] = 1;
        self::$sm_isCreateMocking = true;

        if (self::$mock_instance === null) {
            self::$mock_instance = [];
        }
        if (!($m = igk_getv(self::$mock_instance, $classname))) {
            //DBCaches::IsInitializing() ?? igk_die('caching initializing. cant not register a mocking instance');
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
    public static function IsCreateMocking(): ?bool{
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
     * 
     * @param mixed $raw 
     * @param int $mock 
     * @param bool $unset unset marker definition
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function __construct($raw = null, $mock = 0, $unset = false)
    {
        $this->_initialize($raw, $mock, $unset);
        $tab = &self::RegisterModels();
        if ($tab && !isset($tab[$tb = $this->table()])) {
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
    protected function _initialize($raw = null, $mock = 0, $unset = false)
    {
        $t =  $this->getTable(); 
        $v_inf = DBCaches::GetColumnInfo($t, $this->getController());
        $this->raw = $raw && ($raw instanceof static) ? $raw : $this->createRow();
        if (!$this->raw && !$mock) {
           // $r =  DBCaches::GetCacheData();
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
            $props = array_fill_keys(array_keys((array)$this->raw), 1);

            if ($unset && $this->hidden){
                foreach($this->hidden as $k){
                    $props[$k]= 0;
                }
            }


            foreach ($raw as $k => $v) {
                if (property_exists($this->raw, $k)) {
                    if ($props[$k]){
                        $this->raw->$k = Database::GetValueFromLayoutInfo($v, $k, $v_inf);
                    }
                    unset($props[$k]);
                }
            }
            if ($raw instanceof RefColumnMapping){
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
    public function __set($name, $value)
    {
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
        // igk_wln_e("v ---- ", get_class($this), $this->getRefId(), $this->getTableInfo());
        // igk_trace();
        // igk_exit();
        throw new IGKException("Failed to access " . $name);
    }
    public function __get($name)
    {
        if (method_exists($this, $m = "get" . $name)) {
            return $this->$m();
        }
        if (!property_exists($this->raw, $name)){
            if ($rp = $this->m_alias){                
                $name = igk_getv($rp , $name);
            }
        }
        if (igk_environment()->isDev()) {
            if (!property_exists($this->raw, $name) && (strpos($name, "::") !== 0)) {
                igk_die("property [" . static::class . "::$name] not present");
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
     * return the defined table 
     * @return null|string 
     */
    public function getDefTable():?string{
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
     * @return ?array key|columninfo 
     * @throws IGKException 
     */
    public function getTableColumnInfo(): ?array
    {
        $tn = $this->getTable();
        if($g = DBCaches::GetColumnInfo($tn, $this->getController())){
            if (is_array($g)){
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
    public function getTableInfo(){
        $tn = $this->getTable();
        if($g = DBCaches::GetTableInfo($tn, $this->getController())){
            return $g;
        }       
        return null;
    }
    protected function getTableInfoController()
    {
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
                "registerMacro" => function ($name, callable $callback) use (&$macros) {

                    if (is_callable($callback)) {
                        $callback = Closure::fromCallable($callback);
                    }
                    if (__CLASS__ == static::class) {
                        $macros[$name] = $callback;
                    } else {
                        $macros[static::class . self::ClosureSeperator . $name] = $callback;
                    }
                },
                MacrosConstant::UnRegisterExtensionMethod   => function ($name) use (&$macros) {
                    unset($macros[static::class . self::ClosureSeperator . $name]);
                },
                /**
                 * return the callable
                 */
                "getMacro" => function ($name) use (&$macros): ?callable {
                    return igk_getv($macros, static::class . self::ClosureSeperator . $name);
                },
                "updateRaw" => function (ModelBase $target, ModelBase $g) {
                    if (get_class($target) == get_class($g)) {
                        $target->raw = $g->raw;
                    }
                },
                MacrosConstant::RegisterExtensionMethod => function ($classname) use (&$macros) {
                    $cl = static::class;
                    $f = igk_sys_reflect_class($classname);
                    foreach ($f->getMethods() as $k) {
                        if ($k->isStatic()) {
                            $macros[$cl . self::StaticSperator . $k->getName()] = [$classname, $k->getName()];
                        }
                    }
                },
                "getMacroKeys" => function () use (&$macros) {
                    return array_keys($macros);
                },
                "getInstance" => function () {
                    return igk_environment()->createClassInstance(static::class);
                }
            ];

            self::$macros = &$macros;
           
            require_once(IGK_LIB_CLASSES_DIR . "/Models/Inc/DefaultModelEntryExtensions.pinc");
            // + | ----------------------------------------------------
            // + | init all model
            // + |
            igk_hook(IGKEvents::HOOK_MODEL_INIT, [static::class]);
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
        if (!$failed) {
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
        if (igk_environment()->isDev()) {
            igk_dev_wln(array_keys(self::$macros));
            igk_wln("call :" . $name);
            igk_trace();
        }
        die("ModelBase: failed to call [" . $name . "] - " . static::class);
    }
    /**
     * invoke registrated macros function 
     * @param mixed $macros 
     * @param mixed $name 
     * @param mixed $instance 
     * @param mixed $arguments 
     * @param bool $failed 
     * @return mixed 
     * @throws IGKException 
     */
    private static function _InvokeMacros($macros, $name, $instance, $arguments, &$failed = false)
    {
        $key = static::class . self::StaticSperator . $name;
        if ($fc = igk_getv($macros, $key)) {
            // static closure
            if ($arguments){
                // + | try to inject argument
                $tc = $fc;
                if (is_array($tc)){
                    $tc = Closure::fromCallable($tc);
                }
                $parameters = (new ReflectionFunction($tc))->getParameters();
                array_shift($parameters);
                $arguments = Dispatcher::GetInjectArgsByParameters($parameters, $arguments);
            }
            array_unshift($arguments, $instance);
            return $fc(...$arguments);
        }
        $key = static::class . self::ClosureSeperator . $name;
        if ($fc = igk_getv($macros, $key)) {
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
        $key = igk_uri('@auto_register/'.get_class($instance));
        if (!isset(self::$macros[$key])){

        
            $path = \Database\Macros::class.'\\'.
            ucfirst(basename(igk_uri(get_class($instance)))).'Macros';
            // auto register load - macros class 
            if ($cl = $instance->getController()
                ->resolveClass($path)){
                $instance::RegisterExtension($cl);
                if (method_exists($cl, $name)) {
                    $fc = [$cl, $name];
                    self::$macros[$name] = $fc; 
                    $instance && array_unshift($arguments, $instance);
                    return $fc(...$arguments);
                }
                self::$macros[$key] = 1;
            }
    }
        // $f = igk_sys_reflect_class(ModelEntryExtension::class);
        if (method_exists(ModelEntryExtension::class, $name)) {
            $fc = [ModelEntryExtension::class, $name];
            self::$macros[$name] = $fc; 
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
       
        $failed = false;
        $result = self::_InvokeMacros(self::$macros, $name, $this, $arguments, $failed); 
        if ($failed && igk_environment()->isDev()) {
            $msg = sprintf("failed to call macros %s::%s", static::class, $name);
            if (!defined('IGK_THROW_MISSING_MACROS_EXCEPTION')){
                igk_trace();
                igk_dev_wln(array_keys(self::$macros));
                igk_dev_wln($msg);
            }else {
                throw new IGKException($msg);
            }
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
    public function to_array($alias=false):array
    {
        if ($this->m_alias){
            $keys = array_keys($this->m_alias);
            return array_combine($keys, array_map(function($a){
                return igk_getv($this->raw, $a);
            }, $this->m_alias));
        }
        return (array)$this->raw;
    }
    /**
     * update field and return a boolean
     * @return bool 
     */
    public function save(bool $autoupdate=true): bool
    {
        $pkey = $this->primaryKey;
        if (!empty($pkey)) {
            $cond = [$this->primaryKey => $this->$pkey];
            if ($r = $this->update($this->raw, $cond)) {
                $u =  is_bool($r) ? $r : $r->success();
                if ($u && $autoupdate){
                    if ($m = $this->select_row($cond)){
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
        $dir = $controller->getClassesDir()."/Models";//  dirname(__FILE__);
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
                if ($name == \ModelBase::class)
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
        // igk_wln_e("loading", $arguments);
        return static::__callStatic("select_query_rows", $arguments); // ("select_all", $arguments);
    }
    public function __unset($name)
    {
        $this->$name = null;
        unset($this->raw->$name);
    }
    public function __isset($name){
        return isset($this->raw->$name);        
    }
}
