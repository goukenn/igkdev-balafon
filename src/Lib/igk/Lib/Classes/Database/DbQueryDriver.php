<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbQueryDriver.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Database;
use Exception;
use IGK\System\Console\Logger;
use IGK\System\Database\Exceptions\MissingTableException;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\NoDbConnection;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Number;
use IGKEvents;
use IGKException;
use IGKObject;
use IGK\IDbManager;
use mysqli;
use ReflectionException;
use Throwable;
/**
 * Represent DbQueryDriver class
 */
abstract class DbQueryDriver extends IGKObject implements IDbManager
{
    /**
    * Property: fkeys.
    * @var mixed
    */
    private $fkeys;
    /**
    * auto generate doc.
    * @var \IGK\Database\DataAdapterBase adapter used by this driver
    */
    private $m_adapter;
    /**
    * Callback handler for close callback.
    * @var mixed
    */
    private $m_closeCallback;
    /**
    * Property: dbpwd.
    * @var mixed
    */
    private $m_dbpwd;
    /**
    * Property: charset.
    * @var mixed
    */
    private $m_charset;
    // private $m_dbselect;
    /**
    * Property: dbport.
    * @var mixed
    */
    private $m_dbport; // store the port
    /**
    * Property: dbserver.
    * @var mixed
    */
    private $m_dbserver;
    /**
    * Property: dbuser.
    * @var mixed
    */
    private $m_dbuser;
    /**
    * Flag: isconnect.
    * @var mixed
    */
    private $m_isconnect;
    /**
    * Property: last query.
    * @var mixed
    */
    private $m_lastQuery;
    /**
    * Callback handler for open callback.
    * @var mixed
    */
    private $m_openCallback;
    /**
    * Count: open count.
    * @var mixed
    */
    private $m_openCount;
    /**
    * Property: dboptions.
    * @var mixed
    */
    private $m_dboptions;
    /**
    * Property: last error.
    * @var mixed
    */
    private $m_lastError;
    /**
    * Property: resource.
    * @var mixed
    */
    protected $m_resource;
    /**
    * Property: error.
    * @var mixed
    */
    protected $m_error;
    /**
    * Property: error code.
    * @var mixed
    */
    protected $m_errorCode;
    /**
    * Property: lengthdata.
    * @var mixed
    */
    private static $LENGTHDATA = array("int" => "Int", "varchar" => "VarChar", "char" => "Char");
    /**
    * Property: store.
    * @var mixed
    */
    private static $__store;
    // private static $sm_resid;
    /**
    * Property: config.
    * @var mixed
    */
    public  static $Config;
    /**
    * Property: idd.
    * @var mixed
    */
    static $idd = 0;
    /**
    * Constant: driver mysqli.
    * @var mixed
    */
    const DRIVER_MYSQLI = "MySQLI";
    /**
    * Property: no select db error auto close.
    * @var mixed
    */
    private $m_noSelectDbErrorAutoClose = false;
    /**
    * Returns No Select Db Error Auto Close.
    * @return bool
    */
    public function getNoSelectDbErrorAutoClose():bool{
        return $this->m_noSelectDbErrorAutoClose;
    }
    /**
    * Sets No Select Db Error Auto Close.
    * @param bool $value
    */
    public function setNoSelectDbErrorAutoClose(bool $value){
        $this->m_noSelectDbErrorAutoClose = $value;
    }
    /**
     * get driver initialized db server 
     * @return mixed 
     */
    public function getServer()
    {
        return $this->m_dbserver;
    }
    /**
     * get driver initialized db user 
     * @return mixed 
     */
    public function getUser()
    {
        return $this->m_dbuser;
    }
    /**
     * get driver initialized port number
     * @return mixed 
     */
    public function getPort()
    {
        return $this->m_dbport;
    }
    /**
     * get driver initialized password
     * @return mixed 
     */
    public function getPwd()
    {
        return $this->m_dbpwd;
    }
    /**
     * get initialize charset definition
     * @return mixed 
     */
    public function getCharset(){
        return $this->m_charset;
    }
    /**
     * .ctr
     */
    private function __construct($name)
    {
        $this->m_name = $name;
    }
    /**
    * auto generate doc.
    * @param mixed $forceload the default value is 0
    */
    private function __initTableEntries($tablename, $entries, $forceload = 0)
    {
        if (!$forceload && igk_get_env("pinitSDb")) {
            igk_reg_hook(IGKEvents::HOOK_DB_INIT_ENTRIES, array(new DbEntryToLoad($this, $tablename, $entries), "loadEntries"));
            return;
        }
        // igk_db_load_entries($this, $tablename, $entries);
    }
    /**
    * auto generate doc.
    * @param mixed|array|IDbSendQueryOptions $option null or array key of object
    * @return mixed|object|null
    */
    private function _sendQuery($query, $options = null, bool $autoclose = false)
    {
        return $this->getSender()->sendQuery($query, true, $options, null, $autoclose);
    }
    /**
    * auto generate doc.
    */
    public function __sleep()
    {
        if ($this->m_openCount > 1) {
            igk_die("open count must not be greather than 1");
        }
        return array();
    }
    /**
    * auto generate doc.
    */
    public function __wakeup()
    {
        igk_dev_wln_e(
            "wake up not allowed: unfortunally query driver being store in session",
            $this->m_openCount,
            get_class($this)
        );
    }
    /**
    * auto generate doc.
    * @param mixed $leaveOpen the default value is false
    */
    public function close($leaveOpen = false)
    {
        if ($this->getIsConnect()) {
            if ($leaveOpen && ($this->m_openCount == 1)) {
                return;
            }
            $this->m_openCount--;
            if ($this->m_openCount <= 0) {
                if (igk_db_is_resource($this->m_resource))
                    igk_mysql_db_close($this->m_resource);
                $this->m_isconnect = false;
                $this->m_resource = null;
                // self::SetResId(null, __FUNCTION__);
                $this->m_openCount = 0;
            }
        }
    }
    /**
    * Returns true if Connect.
    */
    public function isConnect()
    {
        return $this->m_resource && ($this->m_openCount > 0);
    }
    /**
    * auto generate doc.
    */
    public function closeAll()
    {
        if (igk_db_is_resource($this->m_resource)) {
            igk_mysql_db_close($this->m_resource);
            $this->m_isconnect = false;
            $this->m_resource = null;
            $this->m_openCount = 0;
        }
        if ($this->m_closeCallback) {
            call_user_func_array($this->m_closeCallback, array());
        }
    }
    /**
    * Escape string.
    * @param mixed $v
    */
    public function escape_string($v)
    {
        return igk_db_escape_string($v);
    }
    /**
    * auto generate doc.
    */
    public function connect()
    {
        if ($this->m_isconnect && $this->m_resource) {
            if (@$this->m_resource->ping()) {
                $this->m_openCount++;
                // if ($this->m_openCount > 2){
                //     igk_trace();
                //     igk_wln_e("open count : ".$this->m_openCount);
                // }
                igk_set_env("lastCounter", igk_ob_get_func('igk_trace'));
                return true;
            }
            $lcount = $this->m_openCount;
            $this->m_openCount = 0;
            $this->m_isconnect = false;
            $this->m_openCount = 0;
            igk_die("[igk] The connection was not closed properly :::ping failed : " .
                $lcount . " <br />");
        }
        $r = igk_db_connect($this);
        try {
            if (igk_db_is_resource($r) && $this->initialize($r)) {
                $this->m_isconnect = true;
                $this->m_resource = $r;
                $this->m_openCount = 1;
                if ($this->m_adapter){
                   // $this->setAdapter()
                   if (($this->m_adapter instanceof IDataDriverCharsetSupport) && $this->m_charset){
                        $this->m_adapter->set_charset($this->m_charset);
                    }
                }
                return true;
            } else {
                $_error = __CLASS__ . "::Error : SERVER RESOURCE # ";
                igk_notify_error($_error, 'sys');
                $error = igk_db_last_connect_error();
                $this->m_lastError = $error;
            }
        } catch (Exception $ex) {
            Logger::danger($ex->getMessage());
            igk_ilog($ex->getMessage());
        }
        $this->m_isconnect = false;
        $this->m_resource = null;
        return false;
    }
    /**
    * Returns Last Error.
    */
    public function getLastError()
    {
        return $this->m_lastError;
    }
    /**
     * configure and initialize db - driver
     * @param mixed $resource depending driver
     * @return bool
     */
    protected abstract function initialize($resource): bool;
    /**
    * auto generate doc.
    * @param mixed $dbpwd
    */
    public function connectTo($dbserver, $dbname, $dbuser, $dbpwd)
    {
        if ($this->m_isconnect)
            $this->close();
        $r = @\igk_db_connect($dbserver, $dbuser, $dbpwd);
        if (\igk_db_is_resource($r)) {
            $t = \igk_db_query("SELECT SUBSTRING_INDEX(CURRENT_USER(),'@',1)", $r);
            if ($t && (\igk_db_num_rows($t) == 1)) {
                $this->m_isconnect = true;
                $this->m_resource = $r;
                $this->m_openCount = 1;
                $tt = $this->selectdb($dbname);
                if (!$tt) {
                    igk_debug_wln("DB Not selected : " . $dbname);
                }
                return $tt;
            }
        } else {
            $s = igk_mysql_db_error();
            igk_notifyctrl()->addError("MySQLError  : " . $s);
            igk_debug_wln("ERROR : " . $s);
        }
        $this->m_isconnect = false;
        $this->m_resource = null;
        return false;
    }
    /**
    * auto generate doc.
    */
    public static function Create(?array $options = null, &$error = null)
    {
        static $driver_storage;
        if ($driver_storage === null) {
            $driver_storage = [];
        }
        $name = "mysql"; 
        $app_cnf = igk_app()->getConfigs();
        $dbserver = (key_exists("server", $options) ?   $options["server"] : func_get_arg(0)) ?? '';
        $dbuser = (key_exists("user", $options)  ? $options["user"] : func_get_arg(1)) ??'';
        $dbpwd = (key_exists("pwd", $options) ? $options["pwd"] : func_get_arg(2)) ?? '';
        $port = (key_exists("port", $options) ?  $options["port"] : func_get_arg(3) ) ?? '';
        $dbname = (key_exists("dbname", $options) ? $options["dbname"] : igk_getv(func_get_args(), 4)) ??  $app_cnf->db_name;
        $dbcharset = (key_exists("dbcharset", $options) ? $options["charset"] : igk_getv(func_get_args(), 5)) ??  $app_cnf->db_charset;
        // $dbserver="localhost", $dbuser="root", $dbpwd="", $port = null){
        $cl = static::class;
        $out = new $cl($name);
        if (is_object($dbserver)) {
            //principal info 
            $out->m_dbserver = trim($dbserver->server);
            $out->m_dbuser = trim($dbserver->user);
            $out->m_dbpwd = trim($dbserver->pwd);
            $out->m_dbport = $dbserver->port;
            $out->m_charset = igk_getv($dbserver, 'charset');
        } else {
            $out->m_dbserver = trim($dbserver);
            $out->m_dbuser = trim($dbuser);
            $out->m_dbpwd = trim($dbpwd);
            $out->m_dbport = $port;
            $out->m_charset = trim($dbcharset ??'');
        }
        try {
            $out->connect();
        } catch (\Exception $_) {
            $out->m_isconnect = false;
            $error = $_->getMessage();
            // remove last error in case last error - 
            if (igk_is_cmd() && error_get_last()) {
                error_clear_last();
            }
        }
        if ($out->m_isconnect) {
            if (igk_environment()->isDev()  && !empty($dbname)) {
                $out->createDb($dbname);
            }
            $out->close();
            $driver_storage[$name] = $out;
        } else {
            $out = null;
            $driver_storage[$name] = null; //  new NoDbConnection();
            $driver_storage[$name] = new NoDbConnection();
        }
        return $out;
    }
    /**
     * create database 
     * @param mixed $db db connection string
     */
    public function createdb($db)
    {
        if (!$this->getIsConnect())
            return false;
        // + | ------------------- 
        return $this->sendQuery("CREATE DATABASE IF NOT EXISTS `" . $this->escape_string($db) . "`;", true);
    }
    /**
    * auto generate doc.
    * @param mixed $options options
    * @return bool|null
    */
    public function createTable(string $tbname, array $columninfo, $entries = null, $desc = null,  $dbname=null, ?string $prefix=null, $extra=null)
    {
        if (!$this->getIsConnect())
            return false;
        if ($grammar = $this->m_adapter->getGrammar()) {    
            // + | --------------------------------------------------------------------
            // + | load extra definition on query driver 
            // + |
            $d = [
                'description'=>$desc,  
                'dbname'=>$dbname, 
                'prefix'=>$prefix
            ];
            if ($extra){
                list($indexes, $Engine) = igk_extract($extra, 'indexes|Engine');
                if ($indexes)
                    $d['indexes'] = $indexes;
                if ($Engine){
                    $d['Engine']= $Engine;
                }
            }    
            $query = $grammar->createTableQuery($tbname, $columninfo, $d);
            if ($this->sendQuery($query)) {
                if ($entries) {
                    $this->m_adapter->pushEntries($tbname, $entries, $columninfo);
                }
                igk_hook(IGKEvents::HOOK_DB_TABLECREATED, [$this, $tbname]);
                return true;
            }
        }
        return null;
    }
    ///delete item in tables
    /**
    * auto generate doc.
    */
    public function delete($tbname, $values = null)
    {
        return $this->m_adapter->delete($tbname, $values);
    }
    /**
     * delete all items
     */
    public function deleteAll($tbname, $condition = null)
    {
        return $this->m_adapter->delete($tbname, $condition);
    }
    /**
    * auto generate doc.
    */
    protected abstract function dieinfo($t, $msg = "", $code = 0);
    /**
    * auto generate doc.
    */
    function dieNotConnect()
    {
        try {
            if (!$this->getIsConnect()) {
                igk_trace();
                igk_die("/!\\ DB Not connected");
            }
        } catch (Throwable $ex) {
            igk_wln_e("error:" . $ex->getMessage());
        }
    }
    /**
    * auto generate doc.
    * @param mixed $tablename
    */
    public function dropTable($tablename)
    {
        igk_die(__METHOD__ . " not implement");
    }
    /**
    * auto generate doc.
    */
    public function flushForInitDb($complete = null)
    {
        $v_infkey = "sys://db/tabfinfo/data";
        $v_tableinit_info = igk_get_env($v_infkey);
        $ad = $this->m_adapter;
        $tb = $v_tableinit_info ? igk_getv(igk_getv($v_tableinit_info, "__failed"), 0) : [];
        $tbs = $v_tableinit_info ? igk_getv($v_tableinit_info, "__linkdata") : [];
        if ($ad->connect()) {
            if (igk_count($tbs) > 0) {
                foreach ($tbs as $k => $v) {
                    $queries = $v_tableinit_info[$k];
                    foreach ($queries as $q) {
                        $ad->sendQuery($q);
                    }
                }
            }
            $sender = $this->getSender();
            $tbs = igk_getv($v_tableinit_info, "__linkdata");
            if ((igk_count($tbs) > 0) && $ad->connect()) {
                foreach ($tbs as $k => $v) {
                    $queries = $v_tableinit_info[$k];
                    foreach ($queries as $q) {
                        $this->_sendQuery($q);
                    }
                }
            }
            if (is_array($tb) && (igk_count($tb) > 0)) {
                igk_debug_wln("send failed table .... creation ");
                foreach ($tb as $k => $v) {
                    $sender->sendQuery($v);
                }
            }
            if (is_callable($complete)) {
                $complete();
            }
            $ad->close();
        }
        if (($dg = igk_debuggerview()) && ($msg = $dg->getMessage())) {
            igk_wln_assert(!empty($msg), $msg);
            return !empty($msg);
        }
    }
    /**
    * auto generate doc.
    */
    public function getdatabases()
    {
        if (!$this->getIsConnect())
            return;
        $t = $this->getSender()->sendQuery("SHOW DATABASES");
        return $t;
    }
    /**
    * auto generate doc.
    */
    public function getDbServer()
    {
        return $this->m_dbServer;
    }
    /**
    * auto generate doc.
    */
    public function getDbUser()
    {
        return $this->m_dbUser;
    }
    /**
     * retrieve driver error 
     * @return mixed 
     * @throws IGKException 
     */
    protected function getDriverError()
    {
        return igk_mysql_db_error($this->m_resource);
    }
    /**
     * retrieve driver code 
     */
    protected function getDriverErrorCode()
    {
        return igk_mysql_db_errorc($this->m_resource);
    }
    /**
    * auto generate doc.
    */
    public function getError()
    {
        return $this->m_error;
    }
    /**
    * Returns Error Code.
    */
    public function getErrorCode()
    {
        return $this->m_errorCode;
    }
    /**
    * auto generate doc.
    * @param mixed $throwError the default value is 1
    * @return ?callable db function to call
    */
    public static function GetFunc($n, $throwError = 1)
    {
        $tn = self::$Config["db"];
        if (empty($tn))
            return null;
        $fc = igk_getv(self::$Config[$tn]["func"], $n);
        if (empty($fc))
            $fc = null;
        return $fc ?? ($throwError ? igk_die("no <b>{$n}</b> found in {$tn} dataadapter ") : null);
    }
    /**
     * get if last execution has an error
     */
    public function getHasError()
    {
        return igk_mysql_db_has_error();
    }
    /**
    * auto generate doc.
    */
    public function getIsConnect()
    {
        return $this->m_isconnect;
    }
    /**
     * retrieve last send query 
     */
    public function getLastQuery()
    {
        return $this->m_lastQuery;
    }
    /**
    * auto generate doc.
    * @param mixed $name
    */
    public function getNewContraintKeys($tablename, $name)
    {
        if ($this->fkeys == null)
            $this->fkeys = array();
        $s = "csk_" . ((strlen($name) > 3) ? substr($name, 0, 3) : $name) . Number::ToBase(count($this->fkeys) + 1, 16, 4);
        $this->fkeys[] = $s;
        return $s;
    }
    /**
     * get the resources
     * @return mixed 
     */
    public function getResId()
    {
        return $this->m_resource;
    }
    // // /**
    // * 
    // */
    // public static function GetResId(){
    //     return self::$sm_resid;
    // }
    /**
    * auto generate doc.
    */
    private function getSender()
    {
        return $this->m_adapter ?? $this;
    }
    /**
    * auto generate doc.
    * @param mixed * $tinfo the default value is null
    */
    public static function GetValue($k, $rowInfo = null, &$tinfo = null)
    {
        $sys = self::$Config["db"];
        if (empty($sys))
            return null;
        $m = igk_getv(self::$Config[$sys], $k);
        if (igk_is_callable($m)) {
            return $m($rowInfo, $tinfo);
        }
        return $m;
    }
    /**
    * auto generate doc.
    * @param mixed $ctrl the default value is null
    */
    public function haveNoLinks($tabname, $ctrl = null)
    {
        $v_infkey = "sys://db/tabfinfo/data";
        $v_tableinit_info = igk_get_env($v_infkey);
        $c = &$v_tableinit_info["__tables"];
        if (isset($c[$tabname])) {
            if ($ctrl != null) {
                $c[$tabname]["callback"] = function () use ($tabname) {
                    igk_hook(IGKEvents::HOOK_DB_DATA_ENTRY, [$this, $tabname, 0]);
                };
            }
            return 0;
        }
        return 1;
    }
    /**
    * auto generate doc.
    * @param mixed $callback
    */
    public static function Init($callback)
    {
        if (self::$Config == null)
            self::$Config = array();
        $callback(self::$Config);
    }
    /**
    * auto generate doc.
    */
    public static function InitDefault(string $driverName = 'mysql', string $dbname = '')
    {
        igk_die("Not implement");
        $db = new DbQueryDriver($driverName);
        $db->connect();
        $db->selectdb($$dbname);
        return $db;
    }
    /**
     * reset db initialize algorithm algorithm
     */
    public function initForInitDb()
    {
        igk_set_env("sys://db/tabfinfo/data", null);
    }
    /**
    * auto generate doc.
    * @param mixed $tableinfo the default value is null
    */
    public function insert($tbname, $values, $tableinfo = null)
    {
        $this->dieNotConnect();
        $tableinfo = $tableinfo ?? DbSchemas::GetTableColumnInfo($tbname);
        return $this->m_adapter->insert($tbname, $values, $tableinfo);
    }
    /**
    * auto generate doc.
    */
    public static function IsMySQLi()
    {
        return self::Is(self::DRIVER_MYSQLI);
    }
    /**
     * check for driver name
     * @param string $driverName 
     * @return bool 
     */
    public static function Is(string $driverName): bool
    {
        // + | 
        $s = self::$Config["db"];
        return ($s == strtolower($driverName));
    }
    ///get the last inserted id
    /**
     * get driver last id
     */
    public function last_id()
    {
        return igk_mysql_db_last_id($this->m_resource);
    }
    /**
     * get connection open counter
     */
    public function openCount()
    {
        return $this->m_openCount;
    }
    /**
    * auto generate doc.
    */
    public static function RestoreConfig()
    {
        DbQueryDriver::$Config = self::$__store;
        self::$__store = null;
    }
    /**
    * auto generate doc.
    * @param mixed $cbinfo
    */
    public static function SaveConfig($cbinfo)
    {
        $ctn = array_merge(DbQueryDriver::$Config);
        $g = DbQueryDriver::$Config["func"];
        DbQueryDriver::$Config["db"] = "user";
        $tab = array("escapestring" => function ($v) use ($cbinfo) {
            return $cbinfo->escapeString($v);
        });
        DbQueryDriver::$Config["func"] = $tab;
        self::$__store = $ctn;
    }
    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */
    public function select($table, $cond = null, $options = null)
    {
        $this->dieNotConnect();
        return $this->m_adapter->select($table, $cond, $options);
        // $query=IGKSQLQueryUtils::GetSelectQuery($this, $table, $cond, $options);
        // $s=$this->_sendQuery($query, $options);
        // return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $dbname
    */
    function selectdb($dbname)
    {
        $this->dieNotConnect();
        $mysql_func = self::GetFunc("selectdb");
        if (self::$Config["db"] == "mysqli") {
            if ($this->m_resource) {
                if (!@$this->m_resource->ping())
                    return false;
                // + | dev list information schema resource
                if ($dbname == "information_schema") {
                    igk_environment()->set("mysql_resource", $this->m_resource);
                }
                return $mysql_func($this->m_resource, $dbname);
            }
            return false;
        } else {
            return $mysql_func($dbname);
        }
    }
    /**
    * auto generate doc.
    */
    public function selectLastId()
    {
        return IGKMySQLQueryResult::CreateResult($this->_sendQuery("SELECT LAST_INSERT_ID()"));
    }
    /**
     * retrieve the last query 
     * @return mixed|void 
     */
    public static function LastQuery(){
        $g = igk_environment()->get(IGK_ENV_QUERY_LIST);
        if ($g){
            return array_pop($g);
        }
    }
    /**
     * send query and return resources
     * @param mixed $query
     * @param bool|option $throwex throw 
     * @return resource|null 
     */
    public function sendQuery($query, $throwex = true, $options = null)
    {   
        $v_env = igk_environment();
        $v_qdebug = $v_env->querydebug;
        if (igk_db_is_resource($this->m_resource)) {
            if ($v_qdebug ) {
                $of = Logger::offscreen();
                if ($of)
                    $of->print("query:*** " . $query);
                igk_push_env(IGK_ENV_QUERY_LIST, $query);
                igk_environment()->write_debug("<span>query &gt; </span><code type=\"sql\">{$query}</code>" );
            }
            if ($v_env->isOps() &&  $v_qdebug ) {
                igk_ilog("send : " . $query);
            }
            $this->setLastQuery($query);
            // + | --------------------------------------------------------------------
            // + | depend on the quere engine can throw exception : data missing
            // + |
            $nolog = is_bool($options) ? $options : (is_object($options) ? igk_getv($options, 'nolog', false) : false);
            $t = igk_db_query($query, $this->m_resource);
            $error = "";
            $code = 0;
            if (!$t && !$nolog) {
                // $l = mysqli_error($this->m_resource);            
                $error = $this->getDriverError($this->m_resource);
                $code = $this->getDriverErrorCode();
                $this->m_error = $error;
                $this->m_errorCode = $code;
                $log = ["DBQueryError" => $error];
                if (igk_environment()->isDev()) {
                    $log = array_merge($log, ["Query" => $query, "File" => __FILE__, "Line" => __LINE__]);
                }
                igk_ilog(implode('\n', $log), 'BLF-DBQuery');
            }
            if ($throwex && !$t) {
                $this->dieinfo(
                    $t,
                    "<div>/!\\ SQL Query Error : $error </div><div style='font-style:normal;'>" . igk_html_query_parse($query) . "</div>",
                    $code
                );
            } else if (!$t)
                return null;
            return $t;
        }
        return null;
    }
    /**
    * Sends Multi Query.
    * @param mixed $query
    * @param mixed $throwex
    */
    public function sendMultiQuery($query, $throwex = true)
    {
        $v_qdebug = igk_environment()->querydebug;
        if (igk_db_is_resource($this->m_resource)) {
            $this->setLastQuery($query);
            if ($v_qdebug) {
                igk_dev_wln("query:--m " . $query);
                igk_push_env(IGK_ENV_QUERY_LIST, $query);
            }
            $t = igk_db_multi_query($query, $this->m_resource);
            if ($throwex) {
                $this->dieinfo($t, "/!\\ SQLQuery Error:<div style='font-style:normal;'>" . igk_html_query_parse($query) . "</div>");
            } else if (!$t)
                return null;
            return $t;
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $o
    */
    public function setAdapter($o)
    {
        $this->m_adapter = $o;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setCloseCallback($v)
    {
        $this->m_closeCallback = $v;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    private function setLastQuery($v)
    {
        $this->m_lastQuery = $v;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setOpenCallback($v)
    {
        $this->m_openCallback = $v;
    }
    /**
    * auto generate doc.
    * @param mixed $tablename
    */
    public function tableExists(string $tablename, bool $throwex=true): bool
    {
        if (empty($tablename))
            return false;
        try {
            $tablename = $this->escape_table_name($tablename);
            $s = $this->sendQuery(
                "SELECT Count(*) FROM " . $tablename . "",
                true
            );
            if (is_bool($s))
                return $s;
            if ($s) {
                return true;
            }
        } catch (Exception $ex) {
            igk_dev_ilog($s = __METHOD__ . ":" . $ex->getMessage());
            if ($throwex){
                throw new MissingTableException($tablename);
            }
        }
        return false;
    }
    ///update data table
    /**
    * auto generate doc.
    */
    public function update($tbname, $entry, $where = null, $querytabinfo = null)
    {
        return $this->m_adapter->update($tbname, $entry, $where, $querytabinfo);
    }
    /**
    * Escape table name.
    * @param string $tbname
    */
    protected function escape_table_name(string $tbname)
    {
        return $this->m_adapter->escape_table_name($tbname);
    }
}