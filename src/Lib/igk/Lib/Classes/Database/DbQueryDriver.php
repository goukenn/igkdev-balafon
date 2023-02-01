<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbQueryDriver.php
// @date: 20220803 13:48:58
// @desc: 

///<summary>Represente class: DbQueryDriver</summary>
namespace IGK\Database;

use Exception;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\NoDbConnection;
use IGK\System\Number;
use IGKEvents;
use IGKException;
use IGKObject;
use IIGKdbManager;
use mysqli;
use Throwable;

/**
 * Represente DbQueryDriver class
 */
abstract class DbQueryDriver extends IGKObject implements IIGKdbManager
{
    private $fkeys;
    /**
     * 
     * @var \IGK\Database\DataAdapterBase adapter used by this driver
     */
    private $m_adapter;
    private $m_closeCallback;
    private $m_dbpwd;
    // private $m_dbselect;
    private $m_dbport; // store the port
    private $m_dbserver;
    private $m_dbuser;
    private $m_isconnect;
    private $m_lastQuery;
    private $m_openCallback;
    private $m_openCount;
    private $m_dboptions;
    private $m_lastError;
    protected $m_resource;
    protected $m_error;
    protected $m_errorCode;

    private static $LENGTHDATA = array("int" => "Int", "varchar" => "VarChar", "char" => "Char");
    private static $__store;
    // private static $sm_resid;
    public  static $Config;
    static $idd = 0;
    const DRIVER_MYSQLI = "MySQLI";
    public function getServer()
    {
        return $this->m_dbserver;
    }
    public function getUser()
    {
        return $this->m_dbuser;
    }
    public function getPort()
    {
        return $this->m_dbport;
    }
    public function getPwd()
    {
        return $this->m_dbpwd;
    }
    ///<summary>.ctr</summary>
    /**
     * .ctr
     */
    private function __construct($name)
    {
        $this->m_name = $name;
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="entries"></param>
    ///<param name="forceload"></param>
    /**
     * 
     * @param mixed $tablename
     * @param mixed $entries
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
    ///<summary></summary>
    ///<param name="query"></param>
    /**
     * 
     * @param mixed $query
     * @param mixed|array|IDbSendQueryOptions $option null or array key of object 
     * @return mixed|object|null
     */
    private function _sendQuery($query, $options = null, bool $autoclose=false){
        return $this->getSender()->sendQuery($query, true, $options, null, $autoclose);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function __sleep()
    {
        if ($this->m_openCount > 1) {
            igk_die("open count must not be greather than 1");
        }
        return array();
    }


    ///<summary></summary>
    /**
     * 
     */
    public function __wakeup()
    {
        igk_dev_wln_e(
            "wake up not allowed: unfortunally query driver being store in session",
            $this->m_openCount,
            get_class($this)
        );
    }
    ///<summary></summary>
    ///<param name="leaveOpen" default="false"></param>
    /**
     * 
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

    public function isConnect(){        
        return $this->m_resource && ($this->m_openCount>0);
    }
    ///<summary></summary>
    /**
     * 
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
    public function escape_string($v)
    {
        return igk_db_escape_string($v);
    }
    ///<summary></summary>
    /**
     * 
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

        if (igk_db_is_resource($r) && $this->initialize($r)) {
            $this->m_isconnect = true;
            $this->m_resource = $r;
            $this->m_openCount = 1;
            return true;
        } else {
            $_error = __CLASS__ . "::Error : SERVER RESOURCE # ";
            igk_notify_error($_error, "sys");          
            $error = igk_db_last_connect_error();                
            $this->m_lastError = $error;

        }
        $this->m_isconnect = false;
        $this->m_resource = null; 
        return false;
    }
 
    public function getLastError(){
        return $this->m_lastError;
    }

    protected abstract function initialize($resource);

    ///<summary></summary>
    ///<param name="dbserver"></param>
    ///<param name="dbname"></param>
    ///<param name="dbuser"></param>
    ///<param name="dbpwd"></param>
    /**
     * 
     * @param mixed $dbserver
     * @param mixed $dbname
     * @param mixed $dbuser
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
    ///<summary></summary>
    ///<param name="dbserver" default="localhost"></param>
    ///<param name="dbuser" default="root"></param>
    ///<param name="dbpwd" default=""></param>
    /**
     * 
     * @param mixed $dbserver the default value is "localhost"
     * @param mixed $dbuser the default value is "root"
     * @param mixed $dbpwd the default value is ""
     */
    public static function Create(?array $options = null, & $error = null)
    {

        static $driver_storage;

        if ($driver_storage === null) {
            $driver_storage = [];
        }
        $name = "mysql";
        // TODO: Driver handle
        // if (key_exists($name, $driver_storage)){
        //     return $driver_storage[$name];
        // }

        $dbserver = key_exists("server", $options) ?   $options["server"] : func_get_arg(0);
        $dbuser = key_exists("user", $options)  ? $options["user"] : func_get_arg(1);
        $dbpwd = key_exists("pwd", $options) ? $options["pwd"] : func_get_arg(2);
        $port = key_exists("port", $options) ?  $options["port"] : func_get_arg(3);
        $dbname = (key_exists("dbname", $options) ? $options["dbname"] : igk_getv(func_get_args(), 4)) ??  igk_app()->getConfigs()->db_name;

        // $dbserver="localhost", $dbuser="root", $dbpwd="", $port = null){
        $cl = static::class;
        $out = new $cl($name);
        if (is_object($dbserver)) {
            //principal info 
            $out->m_dbserver = trim($dbserver->server);
            $out->m_dbuser = trim($dbserver->user);
            $out->m_dbpwd = trim($dbserver->pwd);
            $out->m_dbport = $dbserver->port;
        } else {
            $out->m_dbserver = trim($dbserver);
            $out->m_dbuser = trim($dbuser);
            $out->m_dbpwd = trim($dbpwd);
            $out->m_dbport = $port;
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
    ///<summary></summary>
    ///<param name="db"></param>
    /**
     * 
     * @param mixed $db
     */
    public function createdb($db)
    {
        if (!$this->getIsConnect())
            return false;
        // + | ------------------- 
        return $this->sendQuery("CREATE DATABASE IF NOT EXISTS `" . $this->escape_string($db) . "`;", true );
    }
    ///<summary>create table</summary>
    /**
     * create table
     */
    public function createTable($tbname, array $columninfo, $entries = null, $desc = null, $dbname = null)
    {
        if (!$this->getIsConnect())
            return false;
        if ($grammar = $this->m_adapter->getGrammar()) {
            $query = $grammar->createTableQuery($tbname, $columninfo, $desc, $dbname);
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
     */
    public function delete($tbname, $values = null)
    {
        return $this->m_adapter->delete($tbname, $values);
    }
    ///<summary>delete all items</summary>
    /**
     * delete all items
     */
    public function deleteAll($tbname, $condition = null)
    {
        return $this->m_adapter->delete($tbname, $condition);
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="msg" default=""></param>
    /**
     * 
     * @param mixed $t
     * @param mixed $msg the default value is ""
     */
    protected abstract function dieinfo($t, $msg = "", $code = 0);

    ///<summary></summary>
    /**
     * 
     */
    function dieNotConnect()
    { 
        try {
            if (!$this->getIsConnect()) {
                igk_trace();
                igk_die("/!\\ DB Not connected");
            }
        } catch (Throwable $ex) {
            igk_wln_e("error:".$ex->getMessage());
        }
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    /**
     * 
     * @param mixed $tablename
     */
    public function dropTable($tablename)
    {
        igk_die(__METHOD__ . " not implement");
    }
    ///<summary></summary>
    /**
     * 
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
    ///<summary></summary>
    /**
     * 
     */
    public function getdatabases()
    {
        if (!$this->getIsConnect())
            return;
        $t = $this->getSender()->sendQuery("SHOW DATABASES");
        return $t;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDbServer()
    {
        return $this->m_dbServer;
    }
    ///<summary></summary>
    /**
     * 
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
    ///<summary></summary>
    /**
     * retrieve driver code 
     */
    protected function getDriverErrorCode()
    {
        return igk_mysql_db_errorc($this->m_resource);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getError()
    {
        return $this->m_error;
    }
    public function getErrorCode()
    {
        return $this->m_errorCode;
    }

    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="throwError" default="1"></param>
    /**
     * 
     * @param mixed $n
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
    ///<summary>get if last execution has an error</summary>
    /**
     * get if last execution has an error
     */
    public function getHasError()
    {
        return igk_mysql_db_has_error();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsConnect()
    {
        return $this->m_isconnect;
    }
    ///<summary>retrieve last send query </summary>
    /**
     * retrieve last send query 
     */
    public function getLastQuery()
    {
        return $this->m_lastQuery;
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $tablename
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
    // ///<summary></summary>
    // /**
    // * 
    // */
    // public static function GetResId(){
    //     return self::$sm_resid;
    // }
    ///<summary></summary>
    /**
     * 
     */
    private function getSender()
    {
        return $this->m_adapter ?? $this;
    }
    ///<summary></summary>
    ///<param name="k"></param>
    ///<param name="rowInfo" default="null"></param>
    ///<param name="tinfo" default="null" ref="true"></param>
    /**
     * 
     * @param mixed $k
     * @param mixed $rowInfo the default value is null
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
    ///<summary></summary>
    ///<param name="tabname"></param>
    ///<param name="ctrl" default="null"></param>
    /**
     * 
     * @param mixed $tabname
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
    ///<summary></summary>
    ///<param name="callback"></param>
    /**
     * 
     * @param mixed $callback
     */
    public static function Init($callback)
    {
        if (self::$Config == null)
            self::$Config = array();
        $callback(self::$Config);
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function InitDefault(string $driverName = 'mysql', string $dbname = '')
    {
        igk_die("Not implement");
        $db = new DbQueryDriver($driverName);
        $db->connect();
        $db->selectdb($$dbname);
        return $db;
    }
    ///<summary>reset db initialize algorithm algorithm</summary>
    /**
     * reset db initialize algorithm algorithm
     */
    public function initForInitDb()
    {
        igk_set_env("sys://db/tabfinfo/data", null);
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    ///<param name="tableinfo" default="null"></param>
    /**
     * 
     * @param mixed $tbname
     * @param mixed $values
     * @param mixed $tableinfo the default value is null
     */
    public function insert($tbname, $values, $tableinfo = null)
    {
        $this->dieNotConnect();
        $tableinfo = $tableinfo == null ? igk_db_getdatatableinfokey($tbname) : $tableinfo;
        return $this->m_adapter->insert($tbname, $values, $tableinfo);
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function IsMySQLi()
    {
        return self::Is(self::DRIVER_MYSQLI);
    }
    ///<summary>get if the current state is on driver</summary>
    public static function Is($driverName)
    {
        $s = self::$Config["db"];
        return ($s == strtolower($driverName));
        // DRIVER_MYSQLI
    }

    ///get the last inserted id
    /**
     * get driver last id
     */
    public function last_id()
    {
        return igk_mysql_db_last_id($this->m_resource);
    }
    ///<summary></summary>
    /**
     * get connection open counter
     */
    public function openCount()
    {
        return $this->m_openCount;
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function RestoreConfig()
    {
        DbQueryDriver::$Config = self::$__store;
        self::$__store = null;
    }
    ///<summary></summary>
    ///<param name="cbinfo"></param>
    /**
     * 
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
    ///<summary></summary>
    ///<param name="table"></param>
    ///<param name="cond" default="null"></param>
    ///<param name="options" default="null"></param>
    /**
     * 
     * @param mixed $table
     * @param mixed $cond the default value is null
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
    ///<summary></summary>
    ///<param name="dbname"></param>
    /**
     * 
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
    ///<summary></summary>
    /**
     * 
     */
    public function selectLastId()
    {
        return IGKMySQLQueryResult::CreateResult($this->_sendQuery("SELECT LAST_INSERT_ID()"));
    }
    ///<summary></summary>
    ///<param name="query"></param>
    ///<param name="throwex" default="true"></param>
    /**
     * send query and return resources
     * @param mixed $query
     * @param bool|option $throwex throw 
     * @return resource|null 
     */
    public function sendQuery($query, $throwex = true , $nolog = false)
    {

        if (igk_environment()->isDev()){    
            //igk_ilog('send - query > '. $query . ' ', 0, false);
            // if ($query == 'ALTER TABLE `igkdev.dev`.`tbigk_prospections` ADD FOREIGN KEY (prsopid) REFERENCES `igkdev.dev`.`tbigk_users`(`puId`) ON DELETE RESTRICT ON UPDATE RESTRICT;')       {
            // if (strstr($query,'fb_user_id')){ // `igkdev.dev`.`tbigk_prospections` ADD FOREIGN KEY (prsopid) REFERENCES `igkdev.dev`.`tbigk_users`(`puId`) ON DELETE RESTRICT ON UPDATE RESTRICT;')       {
            //     igk_dev_wln_e(__FILE__.":".__LINE__,  $query, "prospector query error");
            // }
        }  

        if (igk_db_is_resource($this->m_resource)) {
            if (igk_environment()->querydebug) {
                igk_dev_wln("query:*** " . $query);
                igk_push_env(IGK_ENV_QUERY_LIST, $query);
                igk_environment()->write_debug("<span>query &gt; </span>" . $query); 
            }
            $this->setLastQuery($query);    
            // + | --------------------------------------------------------------------
            // + | depend on the quere engine can throw exception : data missing
            // + |
                    
            $t = igk_db_query($query, $this->m_resource);
            $error = "";
            $code = 0;
            if (!$t && !$nolog) {
                $error = $this->getDriverError();
                $code = $this->getDriverErrorCode();
                $this->m_error = $error;
                $this->m_errorCode = $code;
                $log = ["DBQueryError" => $error];
                if (igk_environment()->isDev()){
                    $log = array_merge($log, ["Query" => $query, "File" => __FILE__, "Line"=>__LINE__]);
                }
                igk_ilog($log);
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
    ///<summary></summary>
    ///<param name="o"></param>
    /**
     * 
     * @param mixed $o
     */
    public function setAdapter($o)
    {
        $this->m_adapter = $o;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    public function setCloseCallback($v)
    {
        $this->m_closeCallback = $v;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    private function setLastQuery($v)
    {
        $this->m_lastQuery = $v;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    public function setOpenCallback($v)
    {
        $this->m_openCallback = $v;
    }

    ///<summary></summary>
    ///<param name="tablename"></param>
    /**
     * 
     * @param mixed $tablename
     */
    public function tableExists($tablename): bool
    {
        
        if (empty($tablename))
            return false;   
 
        try {
            $s = $this->sendQuery(
                "SELECT Count(*) FROM `" . igk_mysql_db_tbname($tablename) . "`", 
                true);
            if (is_bool($s))         
                return $s;
            if ($s) {
                return true;
            } 
        } catch (Exception $ex) { 
            igk_ilog($s = __METHOD__ . ":" . $ex->getMessage());
        }
        return false;
    }
    ///update data table
    /**
     */
    public function update($tbname, $entry, $where = null, $querytabinfo = null)
    {
        return $this->m_adapter->update($tbname, $entry, $where, $querytabinfo);
    }
}
