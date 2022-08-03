<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DataAdapter.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Database\MySQL;

use IGK\Database\DbColumnInfo;
use IGK\System\Database\MySQL\DataAdapterBase;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\NoDbConnection;
use IGK\Database\DbQueryResult;
use IGK\Database\IDataDriver;
use IGKException;
use IGKQueryResult;
use ModelBase;

use function igk_getv as getv;


/**
 * MySQL Data Adapter 
 */
class DataAdapter extends DataAdapterBase
{
    private $queryListener;
    private static $_initAdapter;

    const SELECT_DATA_TYPE_QUERY = 'SELECT distinct data_type as type FROM INFORMATION_SCHEMA.COLUMNS';
    const SELECT_VERSION_QUERY = "SHOW VARIABLES where Variable_name='version'";


    public function supportGroupBy()
    {
        return true;
    }

    public function escape_table_name(string $v): string
    {
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
    public function isTypeSupported($type): bool
    {
        static $supportedList;
        if ($supportedList === null) {
            $supportedList = [];
            if ($g = $this->sendQuery(self::SELECT_DATA_TYPE_QUERY)) {
                foreach ($g->getRows() as $r) {
                    $supportedList[] = strtolower($r->type);
                }
            }
        }
        return in_array(strtolower($type), $supportedList);
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
            $s = DbQueryDriver::Create([
                "server" => $cnf->db_server,
                "user" => $cnf->db_user,
                "pwd" => $cnf->db_pwd,
                "port" => $cnf->db_port
            ]);
            if ($s == null) {
                igk_set_env("sys://db/error", "no db manager created");
                $s = new NoDbConnection();
            } else {
                $s->setAdapter($this);
            } 
            return $s;
        }
        return null;
    }
    public function escape_string($v): string
    {
        if (is_object($v)) {
            $v = "" . $v;
        }
        $v = stripslashes($v);
        $b = $this->getResId();
        if ($b) {
            return mysqli_real_escape_string($b, $v);
        }
        return addslashes($v);
    }

    /**
     * 
     * @param mixed $value 
     * @param mixed|DbColumnInfo $tinf 
     * @return mixed 
     */
    public function getDataValue($value, $tinf)
    {
        if (preg_match("/^date$/i", $tinf->clType)) {
            $value = date("Y-m-d", strtotime($value));
        }
        if (preg_match("/^datetime$/i", $tinf->clType)) {
            $value = date(\IGKConstants::MYSQL_DATETIME_FORMAT, strtotime($value));
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
    public function createdb($dbname)
    {
        if ($this->m_dbManager != null){
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
    public function createTable($tablename, $columninfoArray, $entries = null, $desc = null)
    {
        if (($this->m_dbManager != null) && !empty($tablename) && $this->m_dbManager->isConnect())  {

            if (!$this->tableExists($tablename)) {
                // igk_debug_wln("en:", __FILE__.":".__LINE__,  get_class($this->m_dbManager));
                // igk_wln_e("try:::::consolll");                
                $s = $this->m_dbManager->createTable($tablename, $columninfoArray, $entries, $desc, $this->DbName);
                if (!$s) {
                    igk_ilog("failed to create table [" . $tablename . "] - " . $this->m_dbManager->getError());
                    igk_ilog(get_class($this->m_dbManager), __METHOD__);
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
     * 
     * @param mixed $tablename
     * @param mixed $entry
     * @param mixed $tableinfo the default value is null
     */
    public function insert($tablename, $entry, $tableinfo = null)
    {
        if ($query = $this->getGrammar()->createInsertQuery($tablename, $entry, $tableinfo)) {
            return $this->sendQuery($query);
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
     * 
     * @param mixed $tbname
    
     */
    public function selectAll($tbname)
    {
        if ($q = $this->getGrammar()->createSelectQuery($tbname)) {
            return $this->sendQuery($q);
        }
        return null;
    }

    public function getColumnInfo(string $table, ?string $dbname = null)
    {
        $data =  $this->getGrammar()->get_column_info($table, $dbname);
        $outdata = [];
        array_map(function ($v) use ($table, &$outdata) {
            $cl = [];
            $ctype = trim($v->Type);
            $tab = array();
            $ctype = trim($v->Type);
            preg_match_all("/^((?P<type>([^\(\))]+)))\\s*((\((?P<length>([0-9]+))\)){0,1}|(.+)?)$/i", trim($v->Type), $tab);

            $cl["clType"] = $this->getGrammar()->ResolvType(getv($tab["type"], 0, "Int"));
            if (strtolower($cl["clType"]) == "enum") {
                $cl["clEnumValues"] = substr($ctype, strpos($ctype, "(") + 1, -1);
            } else {
                $cl["clTypeLength"] = getv($tab["length"], 0, 0);
            }
            if ($v->Default)
                $cl["clDefault"] = $v->Default;
            if ($v->Comment) {
                $cl["clDescription"] = $v->Comment;
            }
            $cl["clAutoIncrement"] = preg_match("/auto_increment/i", $v->Extra) ? "True" : null;
            $cl["clNotNull"] = preg_match("/NO/i", $v->Null) ? "True" : null;
            $cl["clIsPrimary"] = preg_match("/PRI/i", $v->Key) ? "True" : null;
            $cl["clIsUnique"] = preg_match("/UNI/i", $v->Key) ? "True" : null;
            if (preg_match("/(MUL|UNI)/i", $v->Key)) {
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
            $outdata[$v->Field] = (object)$cl;
        }, $data);
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
    public function sendQuery($query, $throwex = true, $options = null)
    {
        $listener = $this->queryListener ?? $this->m_dbManager;
        if ($listener) {
            $options = $options ?? (object)[];
            $r = $listener->sendQuery($query, $throwex, $options);
            if ($r === false)
                return false;
            if ($r instanceof DbQueryResult) {
                return $r;
            }

            if ($r !== null) {
                if ($res = igk_getv($options, IGKQueryResult::RESULTHANDLER)) {
                    return $res->handle($r);
                }
                return IGKMySQLQueryResult::CreateResult($r, $query, $options);
            }
        }
        return null;
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
     * 
     * @param mixed $tablename
     */
    public function tableExists($tablename)
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
