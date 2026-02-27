<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DataAdapterBase.php
// @date: 20220803 13:48:57
// @desc: 
// @file: DataAdapterBase.php
namespace IGK\System\Database\MySQL;
use Exception;
use IGKException;
use IGK\Database\SQLDataAdapter;
use IGK\System\Database\MySQL\Controllers\MySQLDataController;
use IGK\System\Database\NoDbConnection;
use IGK\System\Database\SQLGrammar;
use IGK\System\Exceptions\NotImplementException;
use IGK\Constants;
use IGKEvents;

// if (!defined(__FILE__)){
//     define(__FILE__, 1);
/**
 * Represent DataAdapterBase class
 */
abstract class DataAdapterBase extends SQLDataAdapter
{

    /**
    * Property: controller.
    * @var mixed
    */
    private $m_controller;

    /**
    * Name of dbname.
    * @var mixed
    */
    private $m_dbname;

    /**
    * Property: error.
    * @var mixed
    */
    private $m_error;

    /**
    * Property: errormsg.
    * @var mixed
    */
    private $m_errormsg;

    /**
    * Property: time.
    * @var mixed
    */
    private $m_time;

    /**
    * Property: empty result.
    * @var mixed
    */
    private static $sm_emptyResult;
    /**
     * db manager/driver of query 
     * @var mixed 
     */
    protected $m_dbManager;
    /**
     * in transaction
     * @var false
     */
    protected $inTransaction = false;
    /**
     * check if adapter can process query 
     * @param string $context 
     * @return bool 
     */

    public function canProcess(?string $context = null)
    {
        return  !($this->m_dbManager instanceof NoDbConnection);
    }

    /**
    * auto generate doc.
    * @param mixed $ctrl the default value is null
    */

    public function __construct($ctrl = null)
    {
        $this->m_controller = $ctrl;
        $this->m_dbManager = $this->_createDriver();
        if ($this->m_dbManager == null) {
            if (defined('IGK_DEBUG')) {
                throw new IGKException("/!\\ Manager not created.");
            } else {
                igk_ilog(__METHOD__ . "::" . __LINE__, "/!\\ Failed to create database manager.");
            }
            igk_die("failed to create MySQL database manager. msqli or mysql not present. please install it");
        } else {
            $this->m_dbManager->setCloseCallback(array($this, 'closeCallback'));
            $this->m_dbManager->setOpenCallback(array($this, 'openCallback'));
        }
        if ($this->connect()) {
            register_shutdown_function(
             // igk_reg_hook(IGKEvents::HOOK_APP_SHUTDOWN, 
                function () {
                $c = $this->OpenCount();
                while ($this->OpenCount() > 0) {
                    $this->close();
                    if ($c == $this->OpenCount()) {
                        new IGKException("failed to close connection");
                    }
                }
            });
        }
    }

    /**
    * auto generate doc.
    * @return object data manager
    */

    protected function _createDriver()
    {
        die("must create a driver");
    }

    /**
    * auto generate doc.
    */

    public function beginTransaction()
    {
        $this->sendQuery("START TRANSACTION", true, null, false);
        $this->inTransaction = true;
    }

    /**
    * auto generate doc.
    * @param mixed $leaveOpen the default value is false
    */

    public function close($leaveOpen = false)
    {
        if ($this->m_dbManager != null) {
            $this->m_dbManager->close($leaveOpen);
            if ($this->m_dbManager->OpenCount() <= 0) {
                $this->_setDbName(null);
            }
        }
    }

    /**
    * auto generate doc.
    */

    public function closeAll()
    {
        if ($this->m_dbManager) {
            $this->m_dbManager->closeAll();
        }
        $this->_setDbName(null);
    }

    /**
    * auto generate doc.
    */

    public function closeCallback()
    {
        $this->_setDbName(null);
    }

    /**
    * auto generate doc.
    */

    public function commit()
    {
        $this->sendQuery("COMMIT");
        $this->inTransaction = false;
    }

    /**
    * auto generate doc.
    * @param mixed $array
    */

    public function configure($array)
    {
        $this->m_dbManager->configure($array);
    }

    /**
    * Resets Db Manager.
    */
    public function resetDbManager()
    {
        $this->m_dbManager = null;
        $this->m_dbManager = $this->_createDriver();
    }
    /**
     * if miss to select db 
     * @return bool 
     */

    public function getNoSelectDbErrorAutoClose(): bool
    {
        return $this->m_dbManager->getNoSelectDbErrorAutoClose();
    }

    /**
    * Sets No Select Db Error Auto Close.
    * @param bool $value
    */
    public function setNoSelectDbErrorAutoClose(bool $value)
    {
        $this->m_dbManager->setNoSelectDbErrorAutoClose($value);
    }

    /**
    * auto generate doc.
    * @param mixed $selectdb the default value is true
    */

    public function connect($dbnamemix = null, $selectdb = true)
    {
        $this->makeCurrent();
        if (($this->m_dbManager == null) || (!$this->m_dbManager->connect())) {
            if (get_class($this->m_dbManager) != \IGK\System\Database\NoDbConnection::class) {
                igk_ilog_assert(
                    !igk_sys_env_production(),
                    $this->m_dbManager ? "can't connect with DBManager: " . get_class($this->m_dbManager) :
                        "dbManager is null"
                );
            } else {
                if (igk_environment()->isDev()) {
                    igk_ilog(
                        "no db adapter available / failed to connect: " . igk_env_count(__METHOD__) .
                            (version_compare(Constants::CorePHPVersion(), "7.3", "<=")
                                ? " connection failed : check mysql_native_password vs caching_sha2_password" : "")
                    );
                }
            }
            return false;
        }
        $dbs = igk_get_env("sys://Db/NODBSELECT");
        $dbname = $this->m_dbname;
        if (is_string($dbnamemix))
            $dbname = $dbnamemix;
        if (!$dbs && $selectdb) {
            $dbname = is_null($dbname) ? $this->app->Configs->db_name : $dbname;
            if ($dbname && !$this->selectdb($dbname)) {
                if (!$this->getNoSelectDbErrorAutoClose()) {
                    $this->close();
                }
                // connected 
                return false;
            }
            $this->_setDbName($dbname);
        }
        return true;
    }
    private function _setDbName($dbname)
    {
        $this->m_dbname = $dbname;
        $this->m_dbManager->db_name = $dbname;
    }

    /**
    * auto generate doc.
    * @param mixed $dbpwd
    */

    public function connectTo($dbserver, $dbname, $dbuser, $dbpwd)
    {
        return $this->m_dbManager->connectTo($dbserver, $dbname, $dbuser, $dbpwd);
    }
    /**
     * count number of items in 
     * @param string $tbname
     * @param mixed|array|object $where the default value is null
     * @param mixed|array|object $options passing grammar options
     */

    public function selectCount(string $tbname, $where = null, $options = null)
    {
        if (!$options)
            $options = [];
        $options["Columns"] = [
            "Count(*) as count"
        ];
        $query = $this->getGrammar()->createSelectQuery($tbname, $where, $options);
        try {
            $g = $this->sendQuery($query, false);
            return $g;
        } catch (Exception $ex) {
            igk_ilog("Exception: " . $ex->getMessage());
        }
        return 0;
    }

    /**
    * auto generate doc.
    * @param mixed $result the default value is false
    */

    public function createEmptyResult($result = false)
    {
        return IGKMySQLQueryResult::CreateResult($result);
    }

    /**
    * auto generate doc.
    * @param mixed $conditions
    * @return mixed
    */

    public function delete($tablename, $conditions = null)
    {
        $r = null;
        if ($this->m_dbManager != null) {
            $r = $this->m_dbManager->delete($tablename, $conditions);
        }
        return $r;
    }

    /**
    * auto generate doc.
    * @param mixed $tablename
    * @return mixed
    */

    public function deleteAll($tablename, $condition = null)
    {
        $r = null;
        if ($this->m_dbManager != null)
            $r = $this->m_dbManager->deleteAll($tablename, $condition);
        return $r;
    }
    /**
     * drop all relations
     */

    public function dropAllRelations()
    {
        return MySQLDataController::DropAllRelations($this, $this->m_dbname);
    }

    /**
    * auto generate doc.
    * @param mixed $tbname
    */

    public function dropTable($tbname)
    {
        if (($this->m_dbManager != null) && $this->m_dbManager->isConnect())
            return MySQLDataController::DropTable($this, $tbname, $this->DbName);
        return null;
    }

    /**
    * auto generate doc.
    */

    public function flushForInitDb($complete = null)
    {
        if ($this->m_dbManager)
            $this->m_dbManager->flushForInitDb($complete);
    }

    /**
    * auto generate doc.
    */

    public function getAllRelations()
    {
        return MySQLDataController::GetAllRelations($this, $this->m_dbname);
    }

    /**
    * auto generate doc.
    * @param mixed $s
    */

    public function getConstraint_Index($s)
    {
        if ($this->m_dbManager != null)
            return MySQLDataController::GetConstraint_Index($this, $s, $this->DbName);
        return null;
    }

    /**
    * auto generate doc.
    */

    public function getDbName(): ?string
    {
        if ($listener = $this->getSendDbQueryListener()) {
            return $listener->getDbName();
        }
        return $this->m_dbname;
    }

    /**
    * auto generate doc.
    */

    public function getError()
    {
        return $this->m_error;
    }

    /**
    * auto generate doc.
    * @param mixed $type
    */

    public function getFormat($type)
    {
        switch (strtolower($type)) {
            case 'time':
                return IGK_MYSQL_TIME_FORMAT;
            case 'datetime':
                return IGK_MYSQL_DATETIME_FORMAT;
            case 'date':
                return IGK_MYSQL_DATE_FORMAT;
        }
        return "";
    }

    /**
    * auto generate doc.
    */

    public function getIsAvailable()
    {
        return ($this->m_dbManager != null);
    }

    /**
    * auto generate doc.
    */

    public function getIsConnect(): bool
    {
        return $this->m_dbManager->getIsConnect();
    }

    /**
    * auto generate doc.
    */

    public function getLastQuery()
    {
        return $this->m_dbManager->getLastQuery();
    }

    /**
    * auto generate doc.
    */

    public function getResId()
    {
        return  $this->m_dbManager ? $this->m_dbManager->getResId() : null;
    }

    /**
    * auto generate doc.
    */

    public function getStored()
    {
        return $this->m_dbManager ? $this->m_dbManager->getStored() : null;
    }

    /**
    * auto generate doc.
    */

    public function getStoredRequired()
    {
        return $this->m_dbManager ? $this->m_dbManager->getStoredRequired() : null;
    }

    /**
    * auto generate doc.
    */

    public function getTabInitInfo()
    {
        return $this->m_dbManager->getTabInitInfo();
    }

    /**
    * auto generate doc.
    */

    public function getTime()
    {
        $this->m_time = new IGKMySQLTimeManager($this);
        return $this->m_time;
    }

    /**
    * auto generate doc.
    */

    public function initForInitDb()
    {
        if ($this->m_dbManager)
            $this->m_dbManager->initForInitDb();
    }

    /**
    * auto generate doc.
    * @param mixed $callback
    */

    public function initSystablePushInitItem($tablename, $callback)
    {
        return $this->m_dbManager && $this->m_dbManager->initSystablePushInitItem($tablename, $callback);
    }

    /**
    * auto generate doc.
    * @param mixed $tablename
    */

    public function initSystableRequired($tablename)
    {
        return $this->m_dbManager && $this->m_dbManager->initSystableRequired($tablename);
    }

    /**
    * auto generate doc.
    * @param mixed $tbN
    */

    public function IsStoredTable($tbN)
    {
        $g = $this->getStored();
        return isset($g[$tbN]);
    }

    /**
    * auto generate doc.
    */

    public function last_id()
    {
        return $this->m_dbManager->last_id();
    }

    /**
    * auto generate doc.
    * @param ?string $filter
    */

    public function listTables(?string $filter=null)
    {
        return $this->getGrammar()->listTables($filter);
    }

    /**
    * auto generate doc.
    */

    public function openCallback()
    {
        igk_log_write_i(__CLASS__, "open connection");
    }

    /**
    * auto generate doc.
    */

    public function openCount()
    {
        if ($this->m_dbManager)
            return $this->m_dbManager->openCount();
        return 0;
    }

    /**
    * Returns true if Connect.
    */
    public function isConnect()
    {
        return $this->openCount() > 0;
    }

    /**
    * auto generate doc.
    */

    public function Reset()
    {
        if ($this->m_dbManager != null)
            $this->m_dbManager->closeAll();
        $this->m_dbManager = $this->_createDriver() ?? igk_die("failed to recreate db connection");
    }

    /**
    * auto generate doc.
    */

    public function rollback()
    {
        $this->sendQuery("ROLLBACK");
    }

    /**
    * auto generate doc.
    * @param mixed $dbname
    */

    public function selectdb(?string $dbname = null): bool
    {
        if (($this->m_dbManager != null) && !empty($dbname)) {
            try {
                $r = $this->m_dbManager->selectdb($dbname);
                if ($r) {
                    $this->_setDbName($dbname);
                } else {
                    if (!igk_sys_env_production()) {
                        igk_ilog(implode(',', ["can't select database \"{$dbname}\". Database not found.", __FILE__ . ":" . __LINE__]));
                    }
                }
                return $r;
            } catch (\Exception $ex) {
            }
        }
        return false;
    }
    /**
     * retrieve the selecgted db 
     */

    public function getselectdb(){
        return $this->m_dbname ?? ($this->m_dbManager? $this->m_dbManager->getselectdb() : null) ?? null;
    }

    /**
    * auto generate doc.
    * @return mixed
    */

    public function selectLastId()
    {
        $r = null;
        if ($this->m_dbManager != null)
            $r = $this->m_dbManager->selectLastId();
        return $r;
    }
    /**
     * set foreign check 
     * @param int|bool $d
     */

    public function setForeignKeyCheck($d)
    {
        if (is_bool($d)) {
            $d = $d ? 1 : 0;
        }
        if (is_integer($d))
            $this->sendQuery("SET foreign_key_checks=" . igk_db_escape_string($d) . ";");
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    protected function setLastQuery($v)
    {
        throw new NotImplementException(__FUNCTION__);
    }

    /**
    * auto generate doc.
    * @param mixed $querytabinfo the default value is null
    */

    public function update($tbname, $entries, $where = null, $querytabinfo = null)
    {
        if (($entries == null) || ($this->m_dbManager == null)) {
            return false;
        }
        return $this->m_dbManager->update($tbname, $entries, $where, $querytabinfo);
    }
    /**
     * create table info query
     * @param SQLGrammar $grammar 
     * @param string $table 
     * @param string $dbname 
     * @return string 
     * @throws IGKException 
     */

    public function createTableColumnInfoQuery(SQLGrammar $grammar, string $table, string $column, string $dbname): string
    {
        $tbname = $this->m_dbManager->escape_string($table);
        $query =  "DESCRIBE " . $tbname . " " . $column;
        return $query;
    }
}