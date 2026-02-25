<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DataAdapterBase.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Database;
use IGK\Helper\Database;
use IGK\System\Console\Logger;
use IGK\System\Database\IDbQueryGrammar;
use IGK\System\Database\IDbSendQueryListener;
use IGKException;
use IGKObject;
use function igk_ilog as _log;
use function igk_getv as getv;
/**
 * Represent IGKDataAdapter class
 */
abstract class DataAdapterBase extends IGKObject implements IDataDriver
{
    // + | register user
    private static $sm_regAdapter;
    protected $m_name;
    protected $m_relations;
    protected $m_filter_column = true;
    protected static $LENGTHDATA = ["int", "varchar", "char", "decimal"];
    /**
     * 
     * @param string $tablename 
     * @return mixed 
     */
    public function select_all(string $tablename) {}
    /**
     * get tor set the resolve link listener 
     * @var ?IDbResolveLinkListener
     */
    var $resolveLinkListener;
    /**
     * inject a send db query listener 
     * */
    public abstract function setSendDbQueryListener(?IDbSendQueryListener $listener);
    /**
     * get the send db query listener
     * @return null|IDbSendQueryListener 
     */
    public abstract function getSendDbQueryListener(): ?IDbSendQueryListener;
    function getHasError()
    {
        return false;
    }
    function getErrorCode()
    {
        return 0;
    }
    function getError()
    {
        return 0;
    }
    /**
     * override it to check if can process query execution
     * @param string $context context that ask to process 
     * @return bool
     */
    public function canProcess(string $context)
    {
        return true;
    }
    public function getEngineSupport(): bool
    {
        return true;
    }
    /**
     * get the filter column flags
     * @return bool 
     */
    public function getFilter(): bool
    {
        return $this->m_filter_column;
    }
    /**
     * 
     * @param bool $filter 
     * @return void 
     */
    public function setFilter(bool $filter){
        $this->m_filter_column = $filter;
    }
    public function getIsLengthData(string $type): bool
    {
        return in_array($type, static::$LENGTHDATA);
    }
    /**
     * create alter table 
     * @param null|array $options 
     * @return string 
     */
    public function createAlterTableFormat(?array $options = null): string
    {
        return "ALTER TABLE %s ADD %sFOREIGN KEY (%s) REFERENCES %s ON DELETE RESTRICT ON UPDATE RESTRICT;";
    }
    /**
     * get if adapter name is registered
     * @param string #adName
     * @param IGK\Database\adName #Parameter#830480dd 
     * @return void 
     */
    public static function IsRegister(?string $adName = null)
    {
        return $adName && isset(self::$sm_regAdapter[$adName]);
    }
    /**
     * get data table definition info
     * @param string $tablename 
     * @return mixed 
     */
    abstract function getDataTableDefinition(string $tablename);
    public static function GetAdapter($controllerOrAdpaterName, $throwException = false)
    {
        $n = IGK_STR_EMPTY;
        if (is_string($controllerOrAdpaterName)) {
            if (empty($controllerOrAdpaterName)) {
                return null;
            }
            $n = $controllerOrAdpaterName;
        } else if (is_object($controllerOrAdpaterName)) {
            if ($controllerOrAdpaterName instanceof self)
                return $controllerOrAdpaterName;
            if (igk_is_controller($controllerOrAdpaterName))
                $n = $controllerOrAdpaterName->getDataAdapterName();
        }
        return self::CreateDataAdapter($n, $throwException);
    }
    /**
     * 
     * @param string $table 
     * @param mixed $columninfo 
     * @return false|void 
     */
    public function pushRelations(string $table, $columninfo)
    {
        if (!$this->m_relations)
            return false;
        if (!isset($this->m_relations->relations[$table])) {
            $this->m_relations->relations[$table] = [];
        }
        $this->m_relations->relations[$table][] = [
            "target" => $columninfo->clLinkType,
            "column" => $columninfo,
            "ctrl" => $this->m_relations->ctrl,
            "info" => []
        ];
    }
    /**
     * push entries
     * @param string $table 
     * @param mixed $entries 
     * @param mixed $tableInfo 
     * @return false|void 
     */
    public function pushEntries(string $table, $entries, $tableInfo)
    {
        if (!$this->m_relations)
            return false;
        $this->m_relations->info[$table] = $tableInfo;
        if (!isset($this->m_relations->entries[$table])) {
            $this->m_relations->entries[$table] = [];
        }
        $this->m_relations->entries[$table][] = $entries;
    }
    /**
     * init db info 
     * @param mixed $ctrl 
     * @return void 
     */
    public function beginInitDb($ctrl = null)
    {
        $this->m_relations = (object)["relations" => [], "entries" => [], "ctrl" => $ctrl];
    }
    /**
     * end db init info
     * @param array $tb loaded info definition 
     * @return void 
     * @throws IGKException 
     */
    public function endInitDb(array $tb)
    {
        if (is_null($this->m_relations)) {
            igk_dev_wln_e(__FILE__ . ":" . __LINE__, "please call beginInitDb first");
        }
        $_grammar = $this->getGrammar();
        $links = [];
        if ($this->m_relations->relations) {
            foreach ($this->m_relations->relations as $tbname => $r) {
                foreach ($r as $p) {
                    $ctrl = $p["ctrl"];
                    $c = clone ($p["column"]);
                    $c->clLinkType = igk_db_get_table_name($c->clLinkType, $ctrl);
                    if ($c->clLinkConstraintName) {
                        $c->clLinkConstraintName = igk_db_get_table_name($c->clLinkConstraintName, $ctrl);
                    }
                    if ($c->clLinkType) {
                        if ($tt = igk_getv($tb, $c->clLinkType)) {
                            $prefix = $tt->prefix;
                            $c->clLinkColumn = Database::AutoPrefixColumn($c->clLinkColumn ?? IGK_FD_ID, $prefix);
                        }
                    }
                    $query = $_grammar->add_foreign_key($tbname, $c);
                    if (is_null($query)) {
                        igk_ilog("can't create foreign key. possibility on constraint name exists");
                        continue;
                    }
                    if (! $this->sendQuery($query)) {
                        _log(implode("\n", ["query failed: ", $query, $this->last_error()]));
                    }
                    if (!isset($links[$tbname])) {
                        $links[$tbname] = [];
                    }
                    if (!isset($links[$tbname][$c->clLinkType])) {
                        $links[$tbname][$c->clLinkType] = 0;
                    }
                    if (!isset($links[$tbname][$c->clLinkType])) {
                        igk_dev_wln_e('prop! to fix', $tbname, $c->clLinkType, $links[$tbname]);
                    }
                    $links[$tbname][$c->clLinkType]++;
                }
            }
        }
        if ($this->m_relations->entries) {
            //sort links data - by requiring links
            uksort($this->m_relations->entries, function ($a, $b) use ($links) {
                if ($a == $b) {
                    return 0;
                }
                if (isset($links[$a])) {
                    if (isset($links[$b])) {
                        if (!in_array($b, $links[$a])) {
                            return -1;
                        }
                    }
                    return 1;
                }
                return -1;
            });
            foreach ($this->m_relations->entries as $tbname => $r) {
                $info = getv($this->m_relations->info, $tbname);
                //init entries
                Logger::info('init entries : ' . $tbname);
                foreach ($r as $b) {
                    foreach ($b as $row) {
                        $query = $_grammar->createInsertQuery($tbname, $row, $info);
                        Logger::info($query);
                        if (!$this->sendQuery($query, false)) {
                            _log(implode("\n", ["query failed: ", $query, $this->last_error()]));
                        }
                    }
                }
            }
        }
        // unset($this->m_relations);
        $this->m_relations = null;
    }
    public function getName()
    {
        return $this->m_name;
    }
    /**
     * get last error
     * @return mixed 
     */
    public abstract function last_error();
    /**
     * send query to database
     * @param string $query 
     * @param bool $throwex indicate to throw exception on error
     * @param mixed $options extra option to pass
     * @param bool $autoclose close the connection
     * @return null|bool|IDbQueryResult|mixed result data
     * @throws \Error if query is null
     */
    public abstract function sendQuery(string $query, $throwex = true, $options = null, $autoclose = false);
    /**
     * 
     * @return null|IDbQueryGrammar grammar object
     */
    public function getGrammar()
    {
        return null;
    }
    /**
     * get select wquery expression 
     * @param mixed $express 
     * @param mixed $tinf 
     * @return string|null 
     */
    public function GetExpressQuery($express, $tinf, $seperator = '.')
    {
        if ($this->resolveLinkListener) {
            if (!$this->resolveLinkListener->resolve($tinf->clLinkType)) {
                return null;
            }
        }
        $b = explode($seperator, $express);
        $value = implode($seperator, array_slice($b, 1));
        if ($nvalue = igk_regex_get("/\[(?P<value>([^\]]+))\]/", "value", $value)) {
            $value = $nvalue;
        }
        $sl = [$b[0] => $value];
        if ($b = $this->getGrammar()->createSelectQuery($tinf->clLinkType, $sl, ["Columns" => [$tinf->clLinkColumn ?? IGK_FD_ID]])) {
            return $b = "(" . rtrim(trim($b), ";") . ")";
        }
        return null;
    }
    /**
     * 
     * @param mixed $tablename
     */
    public function ClearTable($tablename) {}
    /**
     * 
     */
    abstract public function close();
    abstract function exist_column(string $table, string $column, $db = null): bool;
    /**
     * 
     * @param mixed $params
     */
    protected function configure($params) {}
    /**
     * 
     * @param mixed $ctrl the default value is null
     */
    abstract public function connect($ctrl = null);
    /**
     * 
     * @param mixed $ctrl
     * @param mixed $throwexception
     * @param mixed $newAdapter the default value is 0
     * @param mixed $params the default value is null
     */
    public static function CreateDataAdapter($ctrl, $throwexception = true, $newAdapter = 0, $params = null)
    {
        if (empty($ctrl)) {
            igk_wln_e("can't create data adapter from empty value");
            return null;
        }
        // + | --------------------------------------------------------------------
        // + | initialize comment
        // + |
        self::GetAdapters();
        $adapt = &self::$sm_regAdapter;
        $n = IGK_STR_EMPTY;
        $key = IGK_STR_EMPTY;
        $db_adapter = igk_environment()->db_adapters;
        if (is_string($ctrl)) {
            $key = strtoupper($ctrl);
            if (!($n = igk_getv($db_adapter, $ctrl))) {
                $n = "IGK" . $ctrl . "DataAdapter";
            }
        } else {
            $key = strtoupper($ctrl->getDataAdapterName());
            $n = "IGK" . $key . "DataAdapter";
        }
        if (!$newAdapter && isset($adapt[$key])) {
            return $adapt[$key];
        }
        if (isset($db_adapter[$n])) {
            $n = $db_adapter[$n];
        }
        if (!igk_reflection_class_isabstract($n)) {
            $out = igk_create_adapter_from_classname($n);
            if ($out) {
                $adapt[$key] = $out;
                $out->m_name = $key;
                return $out;
            }
        } else {
            $c = igk_get_env("sys://dataadapter");
            if ($c && isset($c[$key])) {
                $o = $c[$key];
                $out = new $o();
                $adapt[$key] = $out;
                $out->m_name = $key;
                return $out;
            }
        }
        if ($throwexception) {
            // igk_wln_e(get_defined_vars(), $adapt, class_exists($n));
            igk_die("DataAdapter: [" . $ctrl . "] not found. Driver class expected : " . $n);
        }
        return null;
    }
    /**
     * 
     * @param mixed $list 
     * @return void 
     */
    public static function Register($list)
    {
        $adapts = &igk_environment()->createArray("db_adapters");
        foreach ($list as $k => $v) {
            if (class_exists($v)) {
                $adapts[$k] = $v;
            }
        }
    }
    /**
     * 
     */
    abstract public function createEmptyResult();
    /**
     * create table
     * @param mixed $tablename
     * @param mixed $columninfoArray
     * @param mixed $entries the default value is null
     * @param string $desc table description
     * @param string $options driver table options
     * @param bool
     */
    public function createTable(string $tablename, $columninfoArray, $entries = null, ?string $desc = null, $options = null)
    {
        return false;
    }
    /**
     * drop table
     * @param string|array<string> $tablename 
     * @return bool
     */
    public function dropTable($tablename)
    {
        return false;
    }
    /**
     * create link expression
     * @param string $table table name
     * @param array $column 
     * @param array $value 
     * @param mixed $columnkey 
     * @return null|DbLinkExpression 
     */
    public function createLinkExpression($table, $column, $value, $columnkey)
    {
        return null;
    }
    /**
     * 
     * @param mixed $tablename
     * @param mixed $entries
     * 
     */
    public function delete($tablename, $condition = null)
    {
        igk_die("function " . __FUNCTION__ . " not implements");
    }
    /**
     * 
     * @return false 
     */
    public function beginTransaction()
    {
        return false;
    }
    /**
     * 
     * @return false 
     */
    public function commit()
    {
        return false;
    }
    /**
     * 
     * @return false 
     */
    public function rollback()
    {
        return false;
    }
    /**
     * end transaction helper
     * @param bool $result 
     * @return void 
     */
    public function endTransaction(bool $result)
    {
        if ($result) {
            $this->commit();
        } else {
            $this->rollback();
        }
    }
    /**
     * create fetch result
     * @param string $query 
     * @param null|IGK\Database\ModelBase $model 
     * @return null|IDbFetchResult|DbFetchResult 
     */
    public function createFetchResult(string $query, ?\IGK\Models\ModelBase $model = null, ?IDataDriver $driver = null)
    {
        return null;
    }
    public function last_id()
    {
        return -1;
    }
    /**
     * 
     * @param mixed $tablename
     */
    public function deleteAll($tablename, $condition = null)
    {
        igk_die("function " . __FUNCTION__ . " not implements");
    }
    /**
     * 
     */
    public function drop($tablename, $condition = null)
    {
        $this->deleteAll($tablename, $condition);
    }
    /**
     * 
     */
    public function flushForInitDb($complete = null) {}
    /**
     * 
     */
    public static function GetAdapters()
    {
        if (self::$sm_regAdapter === null) {
            self::$sm_regAdapter = array();
            self::LoadAdapter();
        }
        return self::$sm_regAdapter;
    }
    /**
     * 
     */
    public function getApp()
    {
        return igk_app();
    }
    /**
     * get the db identifier
     */
    public function getDbIdentifier()
    {
        return "db";
    }
    /**
     * 
     */
    public function getIsAvailable()
    {
        return true;
    }
    /**
     * 
     */
    public function initForInitDb() {}
    /**
     * primary insert class 
     * @param mixed $table 
     * @param mixed $entries 
     * @param mixed $table table info
     * @param bool $throwException 
     * @return false 
     * @throws IGKException 
     */
    public function insert($table, $entries, $tableinfo = null, bool $throwException = true)
    {
        if ($throwException) {
            throw new IGKException(__CLASS__ . " - [warning] :::: must be overrided");
        }
        return false;
    }
    /**
     * start 
     */
    public static function Load()
    {
        if (defined("IGK_INIT"))
            return;
        self::LoadAdapter();
    }
    /**
     * load db data adapters
     */
    private static function LoadAdapter()
    {
        $fc = igk_io_syspath(IGK_ADAPTER_CACHE);
        $n = "/^(IGK)?(?P<name>([^\\\\]+))DataAdapter$/i";
        if (empty($fc)) {
            return false;
        }
        $b = igk_environment()->get("db_adapters");
        if (igk_io_file_exists($fc, true)) {
            $src = igk_io_read_allfile($fc);
            if ($src) {
                foreach (explode(IGK_LF, $src) as $k) {
                    if (empty(trim($k)))
                        continue;
                    $key = strtoupper($k);
                    if (preg_match_all($n, $key, $tab)) {
                        $key = $tab["name"][0];
                    }
                    if (class_exists($k, false)) {
                        self::$sm_regAdapter[$key] = new $k();
                        self::$sm_regAdapter[$key]->m_name = $key;
                    }
                }
            }
            if ($b) { // resolv key name
                $t = [];
                foreach ($b as $k => $v) {
                    $v_u = strtoupper($v);
                    if (isset(self::$sm_regAdapter[$v_u]) &&  preg_match_all($n, $k, $t)) {
                        $obj = self::$sm_regAdapter[$v_u];
                        unset(self::$sm_regAdapter[$v_u]);
                        $s = $t["name"][0];
                        self::$sm_regAdapter[strtoupper($s)] = $obj;
                    }
                }
            }
        } else {
            $v_tr = array();
            $m = "";
            foreach (get_declared_classes() as $k => $v) {
                $cl = basename(igk_uri($v));
                if (preg_match($n, $cl)) {
                    // igk_wln(__FILE__.":".__LINE__, $v);
                    $t = array();
                    preg_match_all($n, $cl, $t);
                    $s = $t["name"][0];
                    if (is_subclass_of($v, self::class) && !igk_reflection_class_isabstract($v)) {
                        $v_tr[strtoupper($s)] = new $v();
                        $m .= $v . IGK_LF;
                    }
                }
            }
            if ($b) {
                foreach ($b as $k => $v) {
                    if (preg_match($n, $k)) {
                        $t = array();
                        preg_match_all($n, $k, $t);
                        $s = $t["name"][0];
                        if (!igk_reflection_class_isabstract($v) && igk_reflection_class_extends($v, "IGKDataAdapter")) {
                            $v_tr[strtoupper($s)] = new $v();
                            $m .= $v . IGK_LF;
                        }
                    }
                }
            }
            self::$sm_regAdapter = $v_tr;
            igk_io_w2file($fc, $m);
        }
    }
    /**
     * 
     */
    public function makeCurrent()
    {
        DbQueryDriver::$Config["db"] = $this->getDbIdentifier();
    }
    /**
     *  override to manage the open connection counter
     */
    public function openCount()
    {
        return 0;
    }
    /**
     * 
     */
    public static function ResetDataAdapter()
    {
        igk_environment()->set("sys://dataadapter", null);
        self::$sm_regAdapter = array();
        self::LoadAdapter();
    }
    /**
     * 
     * @param mixed $tablename
     * @param mixed $condition
     * @param mixed $options
     * @return null|\IGK\Database\DbQueryResult
     */
    public function select($tablename, $condition = null, $options = null)
    {
        return null;
    }
    /**
     * select all data form table
     * @param string $tbname
     * @return ?IDbQueryResult $tbname table
     */
    public function selectAll(string $tbname)
    {
        return null;
    }
    /**
     * select count
     * @param mixed $tbname 
     * @param mixed $where 
     * @param mixed $options 
     * @return int|\IGK\Database\DbQueryResult
     */
    public function selectCount(string $tbname, ?array $where = null, ?array $options = null)
    {
        return 0;
    }
    /**
     * return select query
     * @return string|null
     */
    public function get_query(string $tbname, ?array $where = null, ?array $options = null)
    {
        throw new IGKException('not implement');
    }
    /**
     * 
     * @param mixed $tablename
     * @param mixed $conditions the default value is null
     * @param mixed $options the default value is null
     */
    public function selectAndWhere($tablename, $conditions = null, $options = null)
    {
        igk_die("function " . __FUNCTION__ . " not implements");
    }
    /**
     * 
     * @param mixed $dbname
     */
    public function selectdb(?string $dbname = null) {}
    /**
     * 
     */
    public function selectLastId()
    {
        return null;
    }
    /**
     * 
     * @param mixed $tablename
     * @param mixed $entrie
     */
    public function update($tablename, $entries, $condition = null, $tableinfo = null)
    {
        return false;
    }
    /**
     * list supported tables
     * @return mixed 
     */
    abstract function listTables();
}