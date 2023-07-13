<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DataAdapter.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Database\MySQL;

use Error;
use IGK\Database\DbColumnInfo;
use IGK\System\Database\MySQL\DataAdapterBase;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\NoDbConnection;
use IGK\Database\DbQueryResult;
use IGK\Database\IDataDriver;
use IGK\Helper\Activator;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGKException;
use IGKQueryResult;
use ModelBase;
use ReflectionException;

use function igk_getv as getv;


/**
 * MySQL Data Adapter 
 */
class DataAdapter extends DataAdapterBase
{
    private $queryListener;
    private static $_initAdapter;
    private static $supportedList;

    const SELECT_DATA_TYPE_QUERY = 'SELECT distinct data_type as type FROM INFORMATION_SCHEMA.COLUMNS';
    const SELECT_VERSION_QUERY = "SHOW VARIABLES where Variable_name='version'";
    const DB_INFORMATION_SCHEMA = 'information_schema';

    /**
     * get date time format
     * @return string 
     */
    function getDateTimeFormat():string{
        return IGK_MYSQL_DATETIME_FORMAT;
    }

    /**
     * drop colum
     * @param string $table 
     * @param string $column_name 
     * @return void 
     */
    public function drop_column(string $table, string $column_name){
        if ($this->exist_column($table, $column_name)){
            $q = $this->getGrammar()->createDropColumnQuery($table, $column_name);
            return $this->sendQuery($q);
        }
    }
    /**
     * check that a constraint exists
     * @param string $name 
     * @return bool 
     */
    function constraintExists(string $name): bool
    {
        $name = $this->escape_string($name);
        $g = $this->sendQuery(
            sprintf(
                'SELECT * FROM %s.TABLE_CONSTRAINTS where CONSTRAINT_NAME=\'' . $name . '\';',
                self::DB_INFORMATION_SCHEMA
            )
        );
        if ($g && ($g->getRowCount() > 0)) {
            return true;
        }
        return false;
    }
    function constraintForeignKeyExists(string $name): bool
    {
        $name = $this->escape_string($name);
        $g = $this->sendQuery(
            sprintf(
                'SELECT * FROM %s.TABLE_CONSTRAINTS where CONSTRAINT_NAME=\'' . $name . '\' AND CONSTRAINT_TYPE=\'FOREIGN KEY\' ;',
                self::DB_INFORMATION_SCHEMA
            )
        );
        if ($g && ($g->getRowCount() > 0)) {
            return true;
        }
        return false;
    }

    /**
     * check for existing column
     * @param string $table 
     * @param string $column 
     * @param mixed $db 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    function exist_column(string $table, string $column, $db = null): bool
    {

        $db = $db ?? $this->getDbName() ?? igk_die("no db name");
        $grammar = $this->getGrammar();

        // $this->selectdb();

        $q = $grammar->createSelectQuery( self::DB_INFORMATION_SCHEMA. ".COLUMNS", [
            "TABLE_NAME" => $table,
            "TABLE_SCHEMA" => $db,
            "COLUMN_NAME" => $column,
        ]);
        //igk_wln_e("the query .... ", $q);
        $r = $this->sendQuery($q);
       //  $this->selectdb($db);
        $row = null;
        if ($r) {
            if ($r->ResultTypeIsBoolean()) {
                return $r->value;
            }
            $row = $r->getRowAtIndex(0);
        }
        return $row != null;
    }
    public function drop_foreign_key($table, $info)
    {
        if ($query = $this->remove_foreign($table, $info->clName)) {
            // if (is_array($query)){
                $this->sendMultiQuery($query);
            // }
            //$this->sendQuery($query);
        }
        if ($query = $this->remove_unique($table, $info->clName)) {
            $this->sendQuery($query);
        }
    }
    /**
     * get remove foreign query
     * @param string $table 
     * @param string $info 
     * @param mixed $db 
     * @return null|string 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function remove_foreign(string $table, string $info, $db = null): ?string
    {      
        static $check_exist = null;
        $adapter  = $this;
        $db = $db ?? $adapter->getDbName();
        $r = null;
        $foreign_exists = false;
        $inno_db_table = self::DB_INFORMATION_SCHEMA.".INNODB_FOREIGN_COLS";
        try {

            // check that inodb 
            try{
                $foreign_exists = $check_exist ?? $check_exist = $this->tableExists($inno_db_table);
            } catch (\Exception $ext) {
                $foreign_exists = false;
            }

            //   throw new \IGKException('missing column : '. $inno_db_table) ;
            if ($foreign_exists) {
                $query = sprintf(
                    "SELECT * FROM %s.TABLE_CONSTRAINTS LEFT JOIN %s on(" .
                        "CONCAT(CONSTRAINT_SCHEMA,'/',CONSTRAINT_NAME)=ID" .
                        ") " .
                        "WHERE TABLE_NAME='$table' and CONSTRAINT_SCHEMA='$db' AND FOR_COL_NAME='$info'",
                    self::DB_INFORMATION_SCHEMA,
                    $inno_db_table
                );
            } else {
                $query = sprintf(
                    "SELECT * FROM %s.TABLE_CONSTRAINTS ".
                        "WHERE TABLE_NAME='$table' and CONSTRAINT_SCHEMA='$db'",
                    self::DB_INFORMATION_SCHEMA
                );
            }
            $r = $this->sendQuery($query);
        } catch (\Exception $ex) {
            igk_ilog($ex->getMessage());
            Logger::danger($ex->getMessage());
        }
        $this->selectdb($db);
        $columns = [];
        if ($r) {
            foreach ($r->getRows() as $c) {
                $columns[$c->CONSTRAINT_SCHEMA . "/" . $c->CONSTRAINT_NAME] = $c->CONSTRAINT_NAME;
            }
            if ($columns) {
                $ck = array_values($columns);
                $q = implode(";", array_filter(array_map(
                    function($c)use($adapter, $table, $foreign_exists){
                    if ($c=='PRIMARY')
                        return null;
                    $q  = "ALTER TABLE ";
                    $q .= "`" . $table . "` DROP FOREIGN KEY ";
                    $q .= $adapter->escape($c). " ";
                    return trim($q);
                }, $ck)));
                return $q;
            }
        }
        return null;
    }

    public function remove_unique(string $table, string $info, $db = null)
    {
        $adapter  = $this;
        $db = $db ?? $adapter->getDbName();

        // do not select information schemas
        // $this->selectdb(self::DB_INFORMATION_SCHEMA);
        $query = sprintf(
            "SELECT * FROM %s.TABLE_CONSTRAINTS " .
                "WHERE TABLE_NAME='$table' and CONSTRAINT_TYPE='UNIQUE' and CONSTRAINT_SCHEMA='$db' AND CONSTRAINT_NAME='$info'",
            self::DB_INFORMATION_SCHEMA
        );
        try {
            $r = $this->sendQuery($query);
        } catch (\Exception $ex) {
            igk_dev_wln_e("remove uniquer error : " . $ex->getMessage());
        }
        $this->selectdb($db);
        $columns = [];
        if ($r) {
            foreach ($r->getRows() as $c) {
                $columns[$c->CONSTRAINT_SCHEMA . "/" . $c->CONSTRAINT_NAME] = $c->CONSTRAINT_NAME;
            }
            if ($columns) {
                $ck = implode(', ', array_values($columns));
                $q  = "ALTER TABLE ";
                $q .= "`" . $table . "` DROP INDEX ";
                $q .= $adapter->escape($ck) . " ";
                return $q;
            }
        }
        return null;
    }

    /**
     * drop foreing keys tables 
     * @param mixed $keys 
     * @param int $type 1 = UNIQUE, 0ther is FOREIGN KEYS 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    function dropForeignKeys($keys, int $type = 0)
    {
        $type = igk_getv([1 => 'UNIQUE'], $type, 'FOREIGN KEY');
        $db = $this->getDbName();
        foreach ($keys as $table) {
            $q = sprintf("SELECT * FROM %s.TABLE_CONSTRAINTS where ", self::DB_INFORMATION_SCHEMA);
            $q .= "TABLE_NAME='" . $table . "'";
            $q .= "AND CONSTRAINT_SCHEMA='" . $db . "' ";
            $q .= "AND CONSTRAINT_TYPE='" . $type . "';";
            $g = $this->sendQuery($q);
            if ($g) {
                foreach ($g->getRows() as $r) {
                    $name = $r->CONSTRAINT_NAME;
                    $table = $r->TABLE_NAME;
                    $is = $this->sendQuery('ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $name . ';');
                }
            }
        }
        return $g;
    }
    public function supportGroupBy()
    {
        return true;
    }
    /**
     * escape table name
     * @param string $v 
     * @return string 
     */
    public function escape_table_name(string $v): string
    {
        if (preg_match('/^`.*`$/',$v)){
            return $v;
        }
        if (strpos($v,".") !== false){
            $g = $this->getGrammar();
            return  $g::EscapeTableName($v, $this);            
        }        
        return '`' . $v . '`';
    }

    public function escape_table_column(string $v): string
    {
        return '`' . $v . '`';
    }

    /**
     * create a fetch result
     * @param string $query query to send
     * @param ?\IGK\Models\ModelBase $model source model
     * @return MYSQLQueryFetchResult 
     */
    public function createFetchResult(string $query, ?\IGK\Models\ModelBase $model = null, ?IDataDriver $driver = null)
    {
        $driver = $driver ?? ($model ? $model->getDataAdapter() : igk_get_data_adapter(IGK_MYSQL_DATAADAPTER));
        return MYSQLQueryFetchResult::Create($query, $driver, $model);
    }
    public function isAutoIncrementType(string $type): bool
    {
        return in_array(strtolower($type), ["int", "bigint"]);
    }
    public function update($tbname, $entries, $where = null, $querytabinfo = null)
    {
        if ($query = $this->getGrammar()->createUpdateQuery($tbname, $entries, $where, $querytabinfo)) {
            return $this->sendQuery($query);
        }
    }
    /**
     * 
     * @param string $tbname 
     * @param null|array $where 
     * @param null|array $option 
     * @return string 
     * @throws IGKException 
     */
    public function get_query(string $tbname, ?array $where = null, ?array $options = null)
    {
        return $this->getGrammar()->createSelectQuery($tbname, $where, $options);
    }
    /**
     * retrieve data table definition 
     * @param mixed $table 
     * @return null|array definition
     * @throws IGKException 
     */
    public function getDataTableDefinition($table)
    {
        if ($ctrl = igk_getctrl(IGK_MYSQL_DB_CTRL, false)) {
            return $ctrl->getDataTableDefinition($table);
        }
    }

    ///<summary></summary>
    ///<param name="ctrl" default="null"></param>
    /**
     * 
     * @param mixed $ctrl the default value is null
     */
    public function __construct($ctrl = null)
    {
        parent::__construct($ctrl);
    }
    /**
     * check if driver support typ
     * @param string $type 
     * @return bool 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function isTypeSupported($type): bool
    {

        if (self::$supportedList === null) {
            self::_InitSupportedTypes($this);
            // self::$supportedList = [];
            // if ($g = $this->sendQueryAndLeaveOpen(self::SELECT_DATA_TYPE_QUERY)) {
            //     foreach ($g->getRows() as $r) {
            //         self::$supportedList[] = strtolower($r->type);
            //     }
            //     // + | update timestamp if support datetime - OVH MISSING DATA
            //     $t = &self::$supportedList;
            //     if (!in_array('timestamp', $t) && in_array('datetime', $t)) {
            //         $t[] = 'timestamp';
            //     }
            // }
        }
        return in_array(strtolower($type),  self::$supportedList);
    }
    private static function _InitSupportedTypes($ad)
    {
        self::$supportedList = [];
        if ($g = $ad->sendQueryAndLeaveOpen(self::SELECT_DATA_TYPE_QUERY)) {
            foreach ($g->getRows() as $r) {
                self::$supportedList[] = strtolower($r->type);
            }
            // + | update timestamp if support datetime - OVH MISSING DATA
            $t = &self::$supportedList;
            if (!in_array('timestamp', $t) && in_array('datetime', $t)) {
                $t[] = 'timestamp';
            }
        }
    }
    /**
     * 
     * @var  
     */
    public static function GetSupportedType()
    {
        if (is_null(self::$supportedList)) {
            if ($ad = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER)) {
                self::_InitSupportedTypes($ad);
            }
        }
        return self::$supportedList;
    }
    public function sendQueryAndLeaveOpen(string $query)
    {
        return $this->sendQuery($query, true, null, false);
    }
    /**
     * allow supported default value
     * @param mixed $type 
     * @return bool 
     */
    public function supportDefaultValue($type): bool
    {
        return in_array($type, ["float", "int", "varchar", "enum", "datetime", "time", "float"]);
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function _createDriver()
    {
        if (class_exists(DbQueryDriver::class)) {
            $this->makeCurrent();
            $cnf = $this->app->Configs;
            $error = null;
            $s = DbQueryDriver::Create([
                "server" => $cnf->db_server,
                "user" => $cnf->db_user,
                "pwd" => $cnf->db_pwd,
                "port" => $cnf->db_port
            ],  $error);
            if ($s == null) {
                igk_set_env("sys://db/error", "no db manager created");
                error_log("DB_ERROR: " . $error);
                $s = new NoDbConnection();
            } else {
                $s->setAdapter($this);
            }
            return $s;
        }
        return null;
    }

    public function escape_string(?string $v = null): string
    {
        if (is_null($v)) {
            return 'NULL';
        }
        if (is_object($v)) {
            $v = "" . $v;
        }
        $v = stripslashes($v ?? '');
        $b = $this->getResId();
        if ($b) {
            return mysqli_real_escape_string($b, $v);
        }
        return addslashes($v);
    }

    /**
     * filter data type value 
     * @param mixed $value 
     * @param mixed|DbColumnInfo $tinf 
     * @return mixed 
     */
    public function getDataValue($value, $tinf)
    {
        if ($type = $tinf->clType) {
            if (preg_match("/^date$/i", $type)) {
                $value = date("Y-m-d", strtotime($value));
            } else if (preg_match("/^datetime$/i", $type) && $tinf->clNotNull) {
                $value = date(\IGKConstants::MYSQL_DATETIME_FORMAT, strtotime($value));
            }
        }
        return $value;
    }
    ///<summary>display value</summary>
    /**
     * display value
     */
    public function __toString()
    {
        return __CLASS__;
    }

    public function get_charset()
    {
        $b = $this->m_dbManager->getResId();
        if ($b) {
            return mysqli_character_set_name($b);
        }
        return "";
    }
    public function set_charset($charset = "utf-8")
    {
        $b = $this->m_dbManager->getResId();
        if ($b) {
            return mysqli_set_charset($b, $charset);
        }
    }

    public function delete($tablename, $conditions = null)
    {
        if ($query = $this->getGrammar()->createDeleteQuery($tablename, $conditions)) {
            return $this->sendQuery($query, false);
        }
        return false;
    }

    ///<summary> add column</summary>
    ///<param name="tbname">the table name</param>
    ///<param name="name">the table name</param>
    /**
     *  add column
     * @param string $tbname the table name
     * @param string $name column name
     */
    public function addColumn($tbname, $name)
    {
        if (empty($tbname))
            return false;
        $grammar = $this->getGrammar();
        $tbname = igk_db_escape_string($tbname);
        $columninfo = "";
        if (is_object($name)) {
            $query = $grammar->add_column($tbname, $name);
        } else {
            $columninfo .= "Int(9) NOT NULL";
            $name = igk_db_escape_string($name);
            $query = "ALTER TABLE `{$tbname}` ADD `{$name}` " . $columninfo;
        }
        return $this->sendQuery($query, false);
    }
    public function resetAutoIncrement($table, $value = 1)
    {
        $table =  igk_db_escape_string($table);
        $query = "SELECT Count(*) as count FROM `{$table}`";
        $value = max($value, 1);
        if (($r = $this->sendQuery($query)) && ($r->getRowCount() == 0)) {
            return $this->sendQuery("ALTER `{$table}` AUTO_INCREMENT {$value}");
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
     * 
     * @param mixed $tbname
     */
    public function clearTable($tbname)
    {
        $tbname = igk_mysql_db_tbname($tbname);
        return $this->sendQuery("TRUNCATE `" . $tbname . "` ;")->Success && $this->sendQuery("ALTER TABLE `" . $tbname . "` AUTO_INCREMENT =1;")->Success;
    }
    ///<summary></summary>
    ///<param name="dbname"></param>
    /**
     * create database
     * @param mixed $dbname
     */
    public function createdb(?string $dbname = null)
    {
        if ($this->m_dbManager != null) {
            return $this->m_dbManager->createDb($dbname);
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="columninfoArray"></param>
    ///<param name="entries" default="null"></param>
    ///<param name="desc" default="null"></param>
    /**
     * 
     * @param mixed $tablename
     * @param mixed $columninfoArray
     * @param mixed $entries the default value is null
     * @param mixed $desc the default value is null
     */
    public function createTable(string $tablename, $columninfoArray, $entries = null, $desc = null, $options=null)
    {  
        if (($this->m_dbManager != null) && !empty($tablename) && $this->m_dbManager->isConnect()) {

            if (!($this->tableExists($tablename))) {
                igk_ilog('db try to create table > ' . $tablename);                
                $s = $this->m_dbManager->createTable($tablename, $columninfoArray, $entries, $desc, $options);
                if (!$s) {
                    igk_ilog("failed to create table [" . $tablename . "] - " . $this->m_dbManager->getError());
                    igk_ilog(get_class($this->m_dbManager), __METHOD__);
                } else {
                    igk_ilog(sprintf('db [%s] success', $tablename));
                    Logger::success(sprintf('db - create table - %s - success',  $tablename));
                }
                return $s;
            }
        }
        return false;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function die_error()
    {
        return igk_mysql_db_error();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDbIdentifier()
    {
        return "mysqli";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getError()
    {
        return $this->m_dbManager->getError();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getErrorCode()
    {
        return $this->m_dbManager->getErrorCode();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getHasError()
    {
        return $this->m_dbManager->getHasError();
    }

    ///<summary>create table links definition </summary>
    ///return true if this table still have link an register ctrl data
    /**
     * create table links definition
     */
    public function haveNoLinks($tablename, $ctrl = null)
    {
        return $this->m_dbManager->haveNoLinks($tablename, $ctrl);
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="entry"></param>
    ///<param name="tableinfo" default="null"></param>
    /**
     * adapter send query with grammar helper
     * @param mixed $tablename
     * @param mixed $entry
     * @param mixed $tableinfo the default value is null
     */
    public function insert($tablename, $entry, $tableinfo = null, bool $throwException = true, $options = null, $autoclose = false)
    {

        if ($query = $this->getGrammar()->createInsertQuery($tablename, $entry, $tableinfo)) {
            return $this->sendQuery($query, $throwException, $options, $autoclose);
        }
    }
    ///<summary>insert array in items by building as semi-column separated query</summary>
    public function insert_array($tbname, $values, $throwex = 1)
    {

        $query = "";
        $ch = "";
        foreach ($values as  $v) {
            $query .= $ch . $this->getGrammar()->createInsertQuery($tbname, $v, null);
            $ch = " ";
        }
        return $this->sendMultiQuery($query, $throwex);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function restoreRelationChecking()
    {
        return $this->sendQuery("SET foreign_key_checks=1;");
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $tbname
     * @param mixed $name
     */
    public function rmColumn($tbname, $name)
    {
        if ($query = $this->getGrammar()->rm_column($tbname, $name)) {
            return $this->sendQuery($query, false);
        }
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
     * select all
     * @param mixed $tbname    
     */
    public function selectAll($tbname)
    {
        if ($q = $this->getGrammar()->createSelectQuery($tbname)) {
            return $this->sendQuery($q);
        }
        return null;
    }
    /**
     * select all helper
     * @param string $table 
     * @param null|array $conditions 
     * @return object 
     * @throws IGKException 
     * @throws Error 
     */
    public function select_all(string $table, ?array $conditions = null)
    {
        return $this->select($table, $conditions);
    }
    public function getColumnInfo(string $table, ?string $column_name = null)
    {
        // get descriptions data for columns
        $data =  $this->getGrammar()->get_column_info($table, $column_name);
        $outdata = [];
        $data && array_map(function ($v) use ($table, &$outdata) {
            $cl = [];
            if (empty($v->Type)){
                igk_dev_wln_e("stop is null");
            }
            $ctype = $v->Type ? trim($v->Type) : 'Int';
            $tab = array(); 
            preg_match_all("/^((?P<type>([^\(\))]+)))\\s*((\((?P<length>([0-9]+))\)){0,1}|(.+)?)$/i", trim($ctype), $tab);

            $cl["clType"] = $this->getGrammar()->ResolvType(getv($tab["type"], 0, "Int"));

            if (strtolower($cl["clType"]) == "enum") {
                $cl["clEnumValues"] = substr($ctype, strpos($ctype, "(") + 1, -1);
            } else {
                $cl["clTypeLength"] = getv($tab["length"], 0, 0);
            }
            if (isset($v->Default))
                $cl["clDefault"] = $v->Default;
            if (isset($v->Comment)) {
                $cl["clDescription"] = $v->Comment;
            }
            $cl["clAutoIncrement"] = $v->Extra && preg_match("/auto_increment/i", $v->Extra) ? "True" : null;
            $cl["clNotNull"] = $v->Null && preg_match("/NO/i", $v->Null) ? "True" : null;
            $cl["clIsPrimary"] = $v->Key && preg_match("/PRI/i", $v->Key) ? "True" : null;
            $cl["clIsUnique"] = $v->Key && preg_match("/UNI/i", $v->Key) ? "True" : null;
            if ($v->Key && preg_match("/(MUL|UNI)/i", $v->Key)) {
                $rel = $this->getGrammar()->get_relation($table, $v->Field, $this->getDbName());
                if ($rel) {
                    $cl["clLinkType"] = $rel->REFERENCED_TABLE_NAME;
                    $cl["clLinkColumn"] = $rel->REFERENCED_COLUMN_NAME;
                    $cl["clLinkConstraintName"] = $rel->CONSTRAINT_NAME;
                }
            }
            if (!empty($v->Extra) && (($cpos = strpos($v->Extra, "on update ")) !== false)) {
                $c = trim(substr($v->Extra, $cpos + 10));
                if (in_array($c, ["CURRENT_TIMESTAMP"]))
                    $cl["clUpdateFunction"] = "Now()";
            }
            $cl = Activator::CreateNewInstance(DbColumnInfo::class, $cl);
            if (empty($cl->clName)){
                $cl->clName = $v->Field;
            }
            $outdata[$v->Field] = $cl;
        }, [(object)$data]);
        return $outdata;
    }

    ///<summary></summary>
    ///<param name="query"></param>
    ///<param name="throwex" default="true">throw exception</param>
    ///<param name="options" default="null">use to filter the query result. the default value is null</param>
    /**
     * 
     * @param mixed $query
     * @param mixed $throwex the default value is true
     * @param mixed $options extra option. used by query result
     * @return DbQueryResult|\Iterable|null|bool
     */
    public function sendQuery(string $query, $throwex = true, $options = null, $autoclose = false)
    {
        $listener = $this->queryListener ?? $this->m_dbManager;
        $r = null;
        if ($listener) {
            $options = $options ?? (object)[];
            $r = $listener->sendQuery($query, $throwex, $options);
            // if ($r === false)
            //     return false;
            if ($r instanceof DbQueryResult) {
                return $r;
            }
            if ($r !== null) {
                if ($res = igk_getv($options, IGKQueryResult::RESULTHANDLER)) {
                    $r = $res->handle($r);
                } else {
                    if (!is_bool($r)) {
                        $r = IGKMySQLQueryResult::CreateResult($r, $query, $options);
                    } else {
                        $v = $r; 
                        $r = new BooleanQueryResult($r, $query, $listener->getLastError());
                       
                    }
                }
            }
        }
        if ($autoclose && !$this->inTransaction) {
            $this->close();
        }
        return $r;
    }
    public function sendMultiQuery($query, $throwex = true)
    {
        $sendquery = $this->queryListener ?? $this->m_dbManager;
        if ($sendquery) {
            $r = $sendquery->sendMultiQuery($query, $throwex);
            if ($r !== null) {
                return 1;
            }
        }
        return null;
    }
    /**
     * return version 
     * @return mixed 
     */
    public function getVersion():string{
        return $this->m_dbManager->getVersion();
    }
    /**
     * get adapter type
     * @return string 
     */
    public function getType():string{
        return IGK_MYSQL_DATAADAPTER;
    }
    ///<summary></summary>
    ///<param name="listener"></param>
    /**
     * 
     * @param mixed $listener
     */
    public function setSendDbQueryListener($listener)
    {
        $this->queryListener = $listener;
    }
    public function getSendDbQueryListener()
    {
        return $this->queryListener;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function stopRelationChecking()
    {
        return $this->sendQuery("SET foreign_key_checks=0;");
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    /**
     * check if table exists
     * @param mixed $tablename
     */
    public function tableExists(string $tablename): bool
    {
        return $this->m_dbManager->tableExists($tablename);
    }
    public function __debugInfo()
    {
        return [];
    }
    public function last_error()
    {
        return $this->m_dbManager->getError();
    }
}
