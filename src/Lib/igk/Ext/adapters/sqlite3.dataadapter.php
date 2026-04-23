<?php
// @file: sqlite3.dataadapter.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Database\DbColumnInfo;
use IGK\Database\DbQueryDriver;
use IGK\Database\DbSchemas;
use IGK\Database\SQLDataAdapter;
use IGK\Helper\IO; 
use IGK\System\Console\Logger;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\SQLGrammar;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\Ext\Adapters\SQLite3\SQLite3Result;
use IGK\Helper\Activator;
use IGK\IDataAdapter;
use IGK\System\Database\Exceptions\MissingTableException;
use IGK\System\Database\IDbSendQueryListener;

define("IGK_SQL3LITE_KN", "sql3lite");
define("IGK_SQL3LITE_KN_TABLE_KEY", IGK_SQL3LITE_KN."::/tableName");
define("IGK_SQL3LITE_KN_QUERY_KEY", IGK_SQL3LITE_KN."::/query");
define("IGK_SQL3LITE_TYPE_NAME_INDEX", 2);
define("IGK_SQL3LITE_NAME_INDEX", 1);
require_once __DIR__ .'/SQL3QueryResult.php';
/**
* auto generate doc.
* @param * $info the default value is null
*/
function igk_sql3lite_autoincrement($r, & $info=null){
    if(preg_match("/^(int(eger)?)$/i", (strtolower($r->clType))) && ($r->clIsPrimary)){
        if($info !== null){
            $primkey="noprimkey://".$r->clName;
            $info[$primkey]=1;
        }
        return "primary key autoincrement";
    }
    return null;
}
/**
* auto generate doc.
*/
function igk_sql3lite_close(){
    $sq=IGKSQLite3DataAdapter::GetCurrent();
    return $sq && $sq->close();
}
/**
* auto generate doc.
*/
function igk_sql3lite_connect(){
    igk_wln(func_get_args());
    throw new IGKException("not permitted");
}
/**
* auto generate doc.
*/
function igk_sql3lite_error(){
    $c=IGKSQLite3DataAdapter::GetCurrent();
    if($c)
        return $c->sql->lastErrorMsg();
    return 0;
}
/**
* auto generate doc.
*/
function igk_sql3lite_error_code(){
    $c=IGKSQLite3DataAdapter::GetCurrent();
    if($c)
        return $c->sql->lastErrorCode();
    return 0;
}
/**
* auto generate doc.
* @param mixed $str
*/
function igk_sql3lite_escapestring(?string $str = null){
    $sq=IGKSQLite3DataAdapter::GetCurrent();
    igk_assert_die($sq->sql === null, 'SQLite3 Error : Sql is null');
    return $sq->sql->escapeString($str);
}
/**
* auto generate doc.
* @param mixed $requiretable the default value is 1
*/
function igk_sql3lite_fetch_field($r, $requiretable=1){
    $index=0;
    $v_k="field::/index";
    $v_inf_k="field::/index_info";
    $counter = igk_environment()->get(__FUNCTION__) ?? (object)['counter'];
    $index=igk_getv($counter, $v_k, 0);
    $v_inf=igk_getv($counter, $v_inf_k, 0);
    $tb=igk_getv($r, IGK_SQL3LITE_KN_TABLE_KEY);
    $ctx=IGKSQLite3DataAdapter::GetCurrent();
    $k=null;
    if($index < $r->numColumns()){
        if(!$v_inf){
            $h=$r->fetchArray(SQLITE3_NUM);
            $fieldnames=[];
            $fieldtypes=[];
            for($colnum=0; $colnum < $r->numColumns(); $colnum++){
                $fieldnames[]=$r->columnName($colnum);
                $fieldtypes[]=$r->columnType($colnum);
            }
            $r->reset();
            if(!empty($tb)){
                $q="pragma table_info('{$tb}')";
                $v_inf=$ctx->sql->query($q);
                igk_dev_wln($q);
                $r->$v_inf_k=$v_inf;
                while($d=$r->$v_inf_k->fetchArray(SQLITE3_NUM)){
                    igk_wln("i ");
                    igk_wln($d);
                }
            } 
        }
        $tab=[];
        $k=(object)array(
                "name"=>$r->columnName($index),
                "type"=>igk_sql3lite_tosql_data(igk_getv($tab,
                IGK_SQL3LITE_TYPE_NAME_INDEX)),
                "flags"=>igk_getv($tab,
                5),
                "table"=>$tb,
                "primary_key"=>igk_getv($tab,
                5),
            );
        $index++;
        $counter->$v_k = $index;
        igk_environment()->set(__FUNCTION__, $counter);
    }
    else{
        $r->reset();
        unset($r->$v_k);
        igk_environment()->set(__FUNCTION__, null);
    }
    return $k;
}
/**
* auto generate doc.
* @param mixed $r
*/
function igk_sql3lite_fetch_row($r){
    return $r->fetchArray(SQLITE3_NUM);
}
/**
* auto generate doc.
* @param mixed $r
* @return mixed
*/
function igk_sql3lite_fetch_assoc($r){
    return $r->fetchArray(SQLITE3_ASSOC);
}
/**
* auto generate doc.
*/
function igk_sql3lite_lastid(){
    return -1;
}
/**
* auto generate doc.
* @param mixed $r
*/
function igk_sql3lite_num_fields($r){
    return $r->numColumns();
}
/**
* auto generate doc.
* @param mixed $t
*/
function igk_sql3lite_num_rows($t){
    igk_sql3lite_fetch_row($t);
    if($t->numColumns() && $t->columnType(0) != SQLITE3_NULL){
        return 1;
    }
    else{
        return 0;
    }
}
/**
* auto generate doc.
* @param mixed $d
*/
function igk_sql3lite_tosql_data($d){
    if(is_null($d) || !preg_match_all("/(?P<type>([^\(\)])+)(\((?P<number>[0-9]+)\))?/i", $d, $tab))
        return "unknown";
    $d=igk_getv($tab["type"], 0);
    switch(strtolower($d)){
        case "integer":
        case "int":
        return MYSQLI_TYPE_SHORT;
        case "text":
        case "string":
        return MYSQLI_TYPE_STRING;
    }
    throw new IGKException("error : ".$d);
}
/**
*  represent SQLite3 database adapter
*/
class IGKSQLite3DataAdapter extends SQLDataAdapter implements IDataAdapter{
    /**
    * Name of fname.
    * @var mixed
    */
    private $fname;
    /**
    * Name of base file name.
    * @var mixed
    */
    private $m_base_file_name;
    /**
    * Property: creator.
    * @var mixed
    */
    private $m_creator;
    /**
    * Property: current.
    * @var mixed
    */
    private $m_current;
    /**
    * Property: lengthdata.
    * @var mixed
    */
    protected static $LENGTHDATA=array("int"=>"Int", "varchar"=>"VarChar");
    /**
    * Property: connexions.
    * @var mixed
    */
    private static $sm_connexions;
    /**
    * Collection of list.
    * @var mixed
    */
    private static $sm_list;
    /**
    * Property: sql.
    * @var mixed
    */
    private static $sm_sql;
    /**
    * Property: in transaction.
    * @var mixed
    */
    private $m_inTransaction = false;
    /**
    * Queries Column Charset.
    * @param string $charset
    * @return ?string
    */
    public function queryColumnCharset(string $charset): ?string { return '';}
    /**
    * Allows Type Length.
    * @param string $type
    * @param null|int $length
    * @return bool
    */
    public function allowTypeLength(string $type, ?int $length = null): bool { 
        return in_array($type, ['int','varchar']);
    }
    /**
    * Removes foreign.
    * @param string $name
    * @param string $column
    * @return ?string
    */
    public function remove_foreign(string $name, string $column): ?string {
        return null;
     }
    /**
    * Returns Date Time Format.
    * @return string
    */
    public function getDateTimeFormat(): string { 
        return IGK_MYSQL_TIME_FORMAT; 
    }
    /**
    * Exist column.
    * @param string $table
    * @param string $column
    * @param null|mixed $db
    * @return bool
    */
    public function exist_column(string $table, string $column, $db = null): bool {
        return false;
    }
    /**
    * Returns Type.
    * @return string
    */
    public function getType(): string { 
        return 'SQL3Lite';
    }
    /**
    * auto generate doc.
    * @return bool
    */
    public function getIsConnect(): bool {
        return !is_null($this->getSql());
     }
     /**
      * check for table exists
      * @param string $table 
      * @return bool 
      * @throws IGKException 
      * @throws EnvironmentArrayException 
      */
    public function tableExists(string $table, bool $throwex=true): bool
     {  
        $g = @$this->sql->exec('SELECT count(*) FROM '.$table );
        if (!$g){
            error_clear_last();
            if ($throwex){
                 throw new MissingTableException($table);
            }
        }
        return $g;
     }
      /**
     * check that a constraint exists
     * @param string $name 
     * @return bool 
     */
    function constraintExists(string $name):bool{
        return false;
    }
    /**
     * check that foreing key exists
     * @param string $name 
     * @return bool 
     */
    function constraintForeignKeyExists(string $name):bool{
        return false;
    }
       /**
     * create able info query
     * @param SQLGrammar $grammar 
     * @param string $table 
     * @param string $dbname 
     * @return string 
     * @throws IGKException 
     */
    public function createTableColumnInfoQuery(SQLGrammar $grammar, string $table,string $column, string $dbname): string
    {
        $query = sprintf("PRAGMA table_info(%s)", $table);
        return $query;
    }
    /**
    * Escape table name.
    * @param string $v
    * @return string
    */
    public function escape_table_name(string $v): string {
        return  $v;
    }
    /**
    * Escape table column.
    * @param string $v
    * @return string
    */
    public function escape_table_column(string $v): string { 
        return $v;
    }
    /**
    * Returns Data Value.
    * @param mixed $value
    * @param mixed $tinf
    */
    public function getDataValue($value, $tinf) { }
    /**
    * Returns Db Name.
    * @return ?string
    */
    public function getDbName(): ?string {        
        return $this->getIsConnect() ? "sqlite3-file://".$this->fname : null;        
     }
    /**
    * Returns Filter.
    * @return bool
    */
    public function getFilter(): bool
    {
        return false;
    }
    /**
    * Returns true if Type Supported.
    * @param string $type
    * @return bool
    */
    public function isTypeSupported(string $type): bool {
        return true;
     }
    /**
    * Support default value.
    * @param string $type
    * @return bool
    */
    public function supportDefaultValue(string $type): bool { 
        return true;
    }
    /**
    * Returns true if Auto Increment Type.
    * @param string $type
    * @return bool
    */
    public function isAutoIncrementType(string $type): bool { 
        return true;
    }
    /**
    * Returns Data Table Definition.
    * @param string $tablename
    */
    public function getDataTableDefinition(string $tablename) { 
        return null;
    }
    /**
     * strip slashes 
     * @param null|string $v 
     * @return string 
     */
    public function escape_string(?string $v=null):string{
        $v = stripslashes($v);
		return $this->sql->escapeString($v);
	}
    /**
    * Last error.
    */
    public function last_error()
    {
        return $this->sql->last_error();
    }
    /**
    * auto generate doc.
    */
    public function beginTransaction(){
        $this->sql->exec("BEGIN TRANSACTION");
        $this->m_inTransaction = true;
    }
    /**
    * Creates Random Query Table On Column.
    * @param string $table
    * @param string $column
    * @param null|array $columns
    * @return ?string
    */
    public function createRandomQueryTableOnColumn(string $table, string $column, ?array $columns=null): ?string {
        return null;   
    }
    /**
    * auto generate doc.
    */
    public function close(){
        $sql=$this->getConnectionManager();
        if($sql){
            $sql->count--;
            if(($sql->count<=0) && ($sql->Sql)){
                $sql->Sql->close();
                $sql=null;
                unset(self::$sm_sql[$this->fname]);
            }
        }
    }
    /**
    * auto generate doc.
    */
    public function commit(){
        $this->sql->exec("COMMIT");
        if ($this->m_inTransaction){
            $this->m_inTransaction = false;
            $this->close();
        }
    }
    /**
    * mixed. controller| filename |data value
    *
    */
    public function connect($dbname=null){
        if(func_num_args() > 0){
            if(igk_is_controller($dbname)){
                $f=igk_getv($dbname->Configs, "clSQLite3DataFile");
                $fulln=IO::GetDir(igk_io_ctrl_db_dir($dbname)."/{$f}");
                $ctn=$this->getConnexion($fulln);
                if(($ctn == null) || ($ctn->openCount<=0)){
                    $this->fname=$fulln;
                    $this->initConfig();
                    return 1;
                }
                return 1;
            }
            else if(is_string($dbname)){
                $this->fname=$dbname;
                $this->m_base_file_name = $this->getDatabaseFileName();
            }
        }
        $fulln="";
        $sql= $this->fname ? $this->getSql(): null;
        $c=0;
        if(!$sql){
            if($this->m_creator){
                $o=$this->m_creator;
                if(igk_is_callback_obj($o)){
                    $sql=igk_invoke_callback_obj(null, $o);
                }
                else if(is_object($o))
                    $sql=$o->createDb($this);
            }
            else{
                if(igk_is_controller($dbname)){
                    $f=igk_getv($dbname->Configs, "clSQLite3DataFile");
                    if(empty($f)){
                        igk_die("no file setup");
                    }
                    $fulln=IO::GetDir(igk_io_ctrl_db_dir($dbname)."/{$f}");
                }
                else if(is_string($dbname)){
                    $fulln=$dbname;
                }
                else{
                    if($dbname === null){
                        $fulln=igk_io_sys_datadir()."/databases/".igk_sys_getconfig("sqlite3.database", ".database.sqlite3");
                        IO::CreateDir(dirname($fulln));
                    }
                    else
                        igk_die("SQLite3: failed initiate data".$dbname);
                }
                $this->fname=$fulln;
                $this->initConfig();
                $this->storeConnexion($fulln, $sql);
                $sql=$this->sql;
                $c=1;
            }
            if(!$c){
                if($sql == null){
                    igk_die("/!\\No creator set to init the sql connection");
                }
                $this->initConfig();
                $this->storeConnexion("", $sql);
                $this->fname="";
                return 1;
            }
        }
        $man=$this->getConnectionManager();
        $man->count++;
        $this->initConfig();
        if($man->count == 1)
            $this->enableForeignKey(1);
        return 1;
    }
    /**
    * auto generate doc.
    */
    public function selectCount(string $table, ?array $where=null, $options=null){
        return 0;
    }
    /**
    * auto generate doc.
    * @param mixed $tb
    */
    public function countTable($tb){
        return $this->sendQuery("SELECT Count(*) as count FROM `".$tb."`;", $tb);
    }
    /**
    * create a sql db name
    */
    public function createDb(?string $dbname=null){        
        return true;
    }
    /**
    * auto generate doc.
    * @param mixed $info the default value is null
    */
    public function createEmptyResult($result=null, $query=null, $info=null){
        $r= SQLite3Result::CreateResult($result, $query, $info);
        return $r;
    }
    /**
    * auto generate doc.
    * @param mixed $obj the default value is null
    */
    public function createResult($r, $query=null, $obj=null){
        $inf=(object)array_merge(array(
            "source"=>$this,
            "handle"=>true,
            "adapterName"=>IGK_SQL3LITE_KN,
            "query"=>$query
        ), (array)$obj ?? []);
        return SQLite3Result::CreateResult($r, $query, $inf);
    }
    /**
    * auto generate doc.
    * @param ?array $options driver options
    */
    public function createTable($tbname, $columninfo, $entries=null, $desc=null,  $options=null){
        $query=self::CreateTableQuery($tbname, $columninfo, $entries, $desc, $options);
        $r=$this->sendQuery($query, $tbname);
        if($entries){
            igk_db_inserts($this, $tbname, $entries);
        }
        return $r;
    }
    /**
    * auto generate doc.
    * @param mixed $desc the default value is null
    */
    public static function CreateTableQuery($tbname, $columninfo, $entries=null, $desc=null){
        $query="CREATE TABLE IF NOT EXISTS `".igk_mysql_db_tbname($tbname)."`(";
        $tb=false;
        $primary="";
        $unique="";
        $funique="";
        $findex="";
        $uniques=array();
        $primkey="";
        $tinf=array();
        $foreignkey="";
        foreach($columninfo as $k=>$v){
            if(($v == null) || !is_object($v)){
                igk_die(__CLASS__." ::: Error table column info is not an object error for ".$tbname);
            }
            $v_lname = igk_getv($v, 'clName') ?? 
            (is_numeric($k) ? igk_die('missing column name'): $k );
            $primkey="noprimkey://".$v_lname;
            if($tb)
                $query .= ",";
            $v_name=igk_db_escape_string($v_lname);
            $v = Activator::CreateNewInstance(DbColumnInfo::class, $v);
            $query .= "`".$v_name."` ";
            $type=igk_getev($v->clType, "Int");
            if((strtolower($type) == "int") && ($v->clLinkType || $v->clAutoIncrement)){
                $type="INTEGER";
                $v->clIsIndex = 0;
                $v->clIsUnique = 0;
            }
            $query .= igk_db_escape_string($type);
            $s=strtolower($type);
            $number=false;
            if(isset(self::$LENGTHDATA[$s])){
                if($v->clTypeLength > 0){
                    $number=true;
                    $query .= "(".igk_db_escape_string($v->clTypeLength).") ";
                }
                else
                    $query .= " ";
            }
            else
                $query .= " ";
            if(!$number){
                if(($v->clNotNull) || ($v->clAutoIncrement))
                    $query .= "NOT NULL ";
                else
                    $query .= "NULL ";
            }
            else if($v->clNotNull){
                $query .= "NOT NULL ";
            }
            if($v->clAutoIncrement){
                $query .= DbQueryDriver::GetValue("auto_increment_word", $v, $tinf)." ";
            }
            $tb=true;
            if($v->clDefault || $v->clDefault === '0'){
                $query .= "DEFAULT '".igk_db_escape_string($v->clDefault)."' ";
            }
            if($v->clIsUnique){
                if(!empty($unique))
                    $unique .= ",";
                $unique .= "CONSTRAINT ".strtolower("constraint_".$v_name)." UNIQUE (`".$v_name."`)";
            }
            if($v->clIsUniqueColumnMember){
                if(isset($v->clColumnMemberIndex)){
                    $tindex=explode("-", $v->clColumnMemberIndex);
                    $indexes=array();
                    foreach($tindex as $kindex){
                        if(!is_numeric($kindex) || isset($indexes[$kindex]))
                            continue;
                        $indexes[$kindex]=1;
                        $ck='unique_'. $kindex;
                        $bf="";
                        if(!isset($uniques[$ck])){
                            $bf .= "CONSTRAINT  `clUC_".$ck."_index` UNIQUE(`".$v_name."`";
                        }
                        else{
                            $bf=$uniques[$ck];
                            $bf .= ", `".$v_name."`";
                        }
                        $uniques[$ck]=$bf;
                    }
                }
                else{
                    if(empty($funique)){
                        $funique="CONSTRAINT `clUnique_Column_".$v_name."_idx` UNIQUE (`".$v_name."`";
                    }
                    else
                        $funique .= ", `".$v_name."`";
                }
            }
            if($v->clIsPrimary && !isset($tinf[$primkey])){
                if(!empty($primary))
                    $primary .= ",";
                $primary .= "`".$v_name."`";
            }
            if(($v->clIsIndex || $v->clLinkType) && !$v->clIsUnique && !$v->clIsUniqueColumnMember && $v->clIsPrimary){
                if(!empty($findex))
                    $findex .= ",";
                $findex .= "KEY `".$v_name."_index` (`".$v_name."`)";
            }
            unset($tinf[$primkey]);
            if($v->clLinkType){
                $cl=igk_getv($v, "clLinkColumn", "clId");
                if(!empty($foreignkey))
                    $foreignkey .= ",";
                $foreignkey .= "FOREIGN KEY ({$v_name}) REFERENCES {$v->clLinkType}({$cl})";
            }
        }
        if(!empty($primary)){
            $query .= ", PRIMARY KEY  (".$primary.") ";
        }
        if(!empty($unique)){
            $query .= ", ".$unique." ";
        }
        if(!empty($funique)){
            $funique .= ")";
            $query .= ", ".$funique." ";
        }
        if(igk_count($uniques) > 0){
            foreach($uniques as  $v){
                $v .= ")";
                $query .= ", ".$v." ";
            }
        }
        if(!empty($findex))
            $query .= ", ".$findex;
        if(!empty($foreignkey)){
            $query .= ", ".$foreignkey;
        }
        $query .= ")";
        $query .= ";";
        return $query;
    }
    /**
    * auto generate doc.
    */
    public function dieNotConnect(){
        if($this->sql == null)
            throw new IGKException("sql3lite no connection available ");
    }
    /**
    * drop all present table
    */
    public function dropAllTables(){
        $r=$this->listTables();
        if($r){
            $this->sql->exec('PRAGMA foreign_keys=OFF');
            foreach($r->Rows as  $v){
                if($v->name == "sqlite_sequence"){
                    $this->deleteAll("sqlite_sequence");
                    continue;
                }
                $this->dropTable($v->name);
            }
        }
    }
    /**
    * auto generate doc.
    * @param mixed $tbname
    */
    public function dropTable($tbname){
        return $this->sendQuery("Drop Table IF EXISTS   `{$tbname}`", $tbname);
    }
    /**
    * auto generate doc.
    * @param mixed $b
    */
    public function enableForeignKey($b){
        $s=$b ? 'ON': 'OFF';
        $this->sql->exec('PRAGMA foreign_keys='.$s);
    }
    /**
    * auto generate doc.
    * @param mixed $str
    */
    public function escapeString($str){
        return $this->sql->escapeString($str);
    }
    /**
    * auto generate doc.
    */
    public function getConnectionManager(){
        if(self::$sm_sql)
            return igk_getv(self::$sm_sql, $this->fname);
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function getConnexion($n){
        $r=igk_getv(self::$sm_connexions, $n);
        return $r;
    }
    /**
    * auto generate doc.
    */
    public static function GetCurrent(){
        if(self::$sm_list == null)
            self::
        $sm_list=array();
        return igk_getv(self::$sm_list, 0);
    }
    /**
    * get entry database name
    */
    public function getDatabaseFileName(){
        if (!is_null($this->m_base_file_name)){
            return $this->m_base_file_name;
        }
        $r=$this->sendQuery('PRAGMA database_list', ':global:');
        $f=null;
        if($r && ($inf = $r->getRowAtIndex(0))){
            $f = $inf['file'];
        }
        $this->m_base_file_name = $f;
        return $f;
    }
    /**
    * auto generate doc.
    */
    public function getDatabaseVersion(){
        $r=$this->sendQuery('PRAGMA user_version;', ':global:');
        $f=null;
        if($r && ($r->RowCount == 1)){
            $f=$r->getRowAtIndex(0)->user_version;
        }
        return $f;
    }
    /**
    * get data schema
    * @param string $fmt format of the request dataschema. default is xml. acceptable is xml|obj
    */
    public function getDataSchema($entries=0, $fmt='xml'){
        $rep=null;
        switch($fmt){
            case 'xml':
            default:
            $rep=igk_create_xmlnode(IGK_SCHEMA_TAGNAME);
            $rep["Date"]=date('Y-m-d');
            $rep["Version"]=$this->getDatabaseVersion();
            $rep["Name"]=basename($this->getDatabaseFileName());
            $r=$this->listTables();
            $entry_node=null;
            if($entry_node == null)
                $entry_node=$rep->addXMLNode("Entries");
            if($r){
                $n=$r->Columns[0]->name;
                $e=false;
                foreach($r->Rows as $t){
                    if($e)
                        break 2;
                    $table_n=$t->$n;
                    if(($table_n == null) || ($table_n == "sqlite_sequence"))
                        continue;
                    $row=$rep->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array("TableName"=>$table_n));
                    $tinfo=array();
                    $tt=$this->countTable($table_n);
                    $b=$tt->Columns[0]->name;
                    if($entries){
                        $row["Entries"]=$tt->Rows[0]->$b;
                        $b=$this->select($table_n);
                        $trow=$entry_node->addXMLNode("Rows")->setAttribute("For", $table_n);
                        foreach($b->Rows as $kk=>$vv){
                            $trow->addXMLNode("Row")->setAttributes((array)$vv);
                        }
                    }
                    $tt=$this->sql->query("pragma table_info('{$table_n}')");
                    $itt=$this->sql->query("pragma index_list('{$table_n}')");
                    $ift=$this->sql->query("pragma foreign_key_list('{$table_n}')");
                    $tinfo=array();
                    $clinfo=(object)array();
                    $uc_index=1;
                    while($d=$itt->fetchArray(SQLITE3_NUM)){
                        $info=(object)array();
                        $cn=igk_getv($d, 1);
                        $clt=igk_getv($d, 3);
                        $por=$this->sql->query("PRAGMA index_info('{$cn}')");
                        $cl_count=0;
                        $cln=null;
                        while($rc=$por->fetchArray(SQLITE3_NUM)){
                            $cln=igk_getv($rc, 2);
                            if(!empty($info->$cln))
                                $info->$cln .= "|".$clt;
                            else
                                $info->$cln=$clt;
                            $cl_count++;
                            if(!isset($clinfo->$cln))
                                $clinfo->$cln=(object)array(
                                "is_Unique"=>0,
                                "is_UniqueColumnMember"=>0,
                                "cl_member_index"=>0,
                                "clRefType"=>null,
                                "clRefColumn"=>null,
                                "clInfo"=>$info
                            );
                            else{
                                if(is_array($clinfo->$cln->clInfo))
                                    $clinfo->$cln->clInfo[]=$info;
                                else{
                                    $clinfo->$cln->clInfo=array($clinfo->$cln->clInfo, $info);
                                }
                            }
                        }
                        if($cl_count > 1){
                            $info->clIsUniqueColumnMember=$uc_index++;
                            foreach(array_keys((array)$info) as $rmm=>$rtt){
                                if($rtt == 'clIsUniqueColumnMember')
                                    continue;
                                $clinfo->$rtt->is_UniqueColumnMember=1;
                                $clinfo->$rtt->cl_member_index=(empty($clinfo->$rtt->cl_member_index) ? $info->clIsUniqueColumnMember: $clinfo->$rtt->cl_member_index."-".$info->clIsUniqueColumnMember);
                            }
                        }
                        else{
                            if($clt == "u"){
                                $clinfo->$cln->is_Unique=1;
                            }
                        }
                        $tinfo[]=$info;
                    }
                    $fields=array();
                    if($tt){
                        $tbrelations=[];
                        while($relations=$ift->fetchArray(SQLITE3_NUM)){
                            $tbrelations[igk_getv($relations, 3)
                            ]=(object)["table"=>igk_getv($relations, 2), "sourceColumn"=>igk_getv($relations, 3), "targetColumn"=>igk_getv($relations, 4)];
                        }
                        while($d=$tt->fetchArray(SQLITE3_NUM)){
                            $fi=(object)array(
                                "clName"=>igk_getv($d,
                                1),
                                "clType"=>igk_getv($d,
                                IGK_SQL3LITE_TYPE_NAME_INDEX),
                                "clComment"=>"",
                                "clAutoIncrement"=>igk_getv($d,
                                5),
                                "clDefault"=>igk_getv($d,
                                4),
                                "clNotNull"=>igk_getv($d,
                                3)
                            );
                            if(isset($tbrelations[$fi->clName])){
                                $fi->clLinkType=$tbrelations[$fi->clName]->table;
                                if($tbrelations[$fi->clName]->targetColumn != IGK_FD_ID)
                                    $fi->clLinkTypeColumn=$tbrelations[$fi->clName]->targetColumn;
                            }
                            $v=$fi;
                            $cl= $row->addNode(IGK_COLUMN_TAGNAME);
                            $cl["clName"]=$fi->clName;
                            $tab=array();
                            preg_match_all("/^((?P<type>([^\(\))]+)))\\s*(\((?P<length>([0-9]+))\)){0,1}$/i", trim($fi->clType), $tab);
                            $cl["clType"]=igk_sql_data_type(igk_getv($tab["type"], 0, "Int"));
                            $cl["clTypeLength"]=igk_getv($tab["length"], 0, 0);
                            $cl["clAutoIncrement"]=$v->clAutoIncrement ? $v->clAutoIncrement: null;
                            $cl["clNotNull"]=$v->clNotNull ? $v->clNotNull: null;
                            $cl["clIsPrimary"]=$v->clAutoIncrement ? $v->clAutoIncrement: null;
                            $rinf=igk_getv($clinfo, $fi->clName);
                            $cl["clColumnMemberIndex"]=$rinf && $rinf->is_UniqueColumnMember ? $rinf->cl_member_index: null;
                            $cl["clIsUnique"]=$rinf && $rinf->is_Unique ? 1: null;
                            $cl["clIsUniqueColumnMember"]=$rinf && $rinf->is_UniqueColumnMember ? 1: null;
                            $cl["clLinkType"]=igk_getv($fi, "clLinkType");
                            $cl["clLinkTypeColumn"]=igk_getv($fi, "clLinkTypeColumn");
                        }
                    }
                    $tinfo[$fi->clName]=new DbColumnInfo((array)$fi);
                    $fields[]=$fi;
                    $tables[$table_n]=(object)array("tinfo"=>$tinfo, 'ctrl'=>"sys://mysql_db");
                }
            }
            break;
        }
        return $rep;
    }
    /**
    * auto generate doc.
    */
    public function getDbIdentifier(){
        return IGK_SQL3LITE_KN;
    }
    /**
    * auto generate doc.
    * @param mixed $n
    */
    public static function GetSchemaOptions($n){
        $ul=$n->addUL();
        $li=$ul->add("li");
        $li->addLabel("clsqlite3_file")->setContent(null)->addSpan()->Content="SQLite3 Db Filename";
        $li->addInput("clsqlite3_file", "text");
        return $n;
    }
    /**
    * auto generate doc.
    */
    public function getSql(){
        if(self::$sm_sql == null){
            self::
            $sm_sql=[];
        }
        return igk_getv(self::$sm_sql, $this->fname, function(){
            $s=new SQLite3($this->fname);
            $man=new IGKSQLiteConnectionManager();
            $man->Sql=$s;
            $man->count=0;
            self::$sm_sql[$this->fname]=$man;
            return $man;
        })->Sql;
    }
    /**
    * get sql version
    */
    public function getVersion():string{
        return $this->sql->version();
    }
    /**
    * auto generate doc.
    */
    protected function initConfig(){
        $this->makeCurrent();
        self::StoreStack($this);
    }
    /**
    * auto generate doc.
    * @param mixed $tablename
    */
    public function initSystableRequired($tablename){}
    /**
    * auto generate doc.
    */
    public function IsForeignKeyEnable(){
        $r=$this->sendQuery('PRAGMA foreign_keys;', ':global:');
        $f=0;
        if($r && ($r->RowCount == 1)){
            $f=$r->getRowAtIndex(0)->foreign_keys;
        }
        return $f;
    }
    /**
    * auto generate doc.
    */
    public function last_id(){
        return $this->sql->lastInsertRowID();
    }
    /**
    * auto generate doc.
    */
    public function listTables(){
        return $this->sendQuery("SELECT name FROM sqlite_master WHERE type='table';", "sqlite_master");
    }
    /**
    * auto generate doc.
    */
    public function numRows(){
        return -1;
    }
    /**
    * auto generate doc.
    */
    public function openCount(){
        $r=$this->getConnectionManager();
        return $r ? $r->count: 0;
    }
    /**
    * auto generate doc.
    */
    public function restoreRelationChecking(){
        $this->enableForeignKey(1);
    }
    /**
    * auto generate doc.
    */
    public function rollback(){
        $this->sql->exec("ROLLBACK");
        if ($this->m_inTransaction){
            $this->m_inTransaction = false;
            $this->close();
        }
    }
    /**
    * auto generate doc.
    * @param mixed $tbname the default value is null
    * @return null|IGK\Database\DbQueryResult
    */
    public function sendQuery($query, $throwex=true, $options=null, $autoclose=false){
        if (is_null($query)){
            return false;
        }
        $this->makeCurrent();
        $sql=$this->getSql();
        $tbname = $this->m_base_file_name;
        $r=null;
        if (igk_is_debug()){
            Logger::print('query > '.$query);
        }
        if(preg_match("/^(INSERT|UPDATE|DELETE|CREATE|DROP)/i", $query)){
            !($r=@$sql->exec($query)) && igk_die("query failed . ".$query. " error ".$this->sql->lastErrorMsg());
            return $r;
        }
        else{
            !($r=@$sql->query($query)) && igk_debug_or_local_die($this->sql->lastErrorMsg());
        }
        if($r && is_object($r)){
            $obj=igk_createObj();
            igk_setv($obj, IGK_SQL3LITE_KN_QUERY_KEY, $query);
            igk_setv($obj, IGK_SQL3LITE_KN_TABLE_KEY, $tbname);
            return $this->createResult($r, $query, $obj);
        }
        if($this->sql->lastErrorCode() == 0)
            return null;
        $obj= $this->createEmptyResult(false, $query, array("error"=>1, "errormsg"=>$this->sql->lastErrorMsg()));
        return $obj;
    }
    /**
    * auto generate doc.
    * @param mixed $listener
    */
    public function setCreatorListener($listener){
        $this->m_creator=$listener;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setDatabaseVersion($v){
        $this->sql->exec('PRAGMA user_version='.$v);
    }
    /**
    * auto generate doc.
    */
    public function setForeignKeyCheck($check){}
    /**
    * auto generate doc.
    */
    public function stopRelationChecking(){
        $this->enableForeignKey(0);
    }
    /**
    * auto generate doc.
    * @param mixed $sql
    */
    public function storeConnexion($fname, $sql){
        if(self::$sm_connexions == null)
            self::
        $sm_connexions=array();
        $this->m_current=(object)array("filename"=>$fname, "openCount"=>1, "sql"=>$sql);
        self::
        $sm_connexions[$fname]=$this->m_current;
    }
    /**
    * auto generate doc.
    * @param mixed $l
    */
    public static function StoreStack($l){
        self::GetCurrent();
        array_unshift(self::$sm_list, $l);
    }
    /**
     * direct select all 
     * @param string $table 
     * @param mixed $conditions 
     * @return mixed 
     * @throws IGKException 
     */
    public function select_all(string $table, $conditions=null){
        $r = $this->select($table, $conditions, null, false, false);
        if ($r && $r->fetch_all()){
            return $r->getRows();
        }
        return null;
    }
    /**
    * Selects row.
    * @param string $table
    * @param null|mixed $conditions
    */
    public function select_row(string $table, $conditions=null){
        $r = $this->select($table, $conditions, null, false, false);
        if ($r && ($f = $r->getRowAtIndex(0))){
            return $f;
        }
    }
    /**
    * Fetches assoc.
    * @param mixed $a
    */
    public function fetch_assoc($a){
        return null;
    }
}
/**
* Represent sql3literesult class
*/
DbQueryDriver::Init(function(& $conf){
    $n=IGK_SQL3LITE_KN;
    $conf["db"]=IGK_SQL3LITE_KN;
    $conf[$n]["func"]=array();
    $conf[$n]["auto_increment_word"]="igk_sql3lite_autoincrement";
    $conf[$n]["data_adapter"]="SQL3Lite";
    $t=array();
    $t["connect"]="igk_sql3lite_connect";
    $t["selectdb"]="";
    $t["check_connect"]="";
    $t["query"]="igk_sql3lite_fetch_query";
    $t["escapestring"]="igk_sql3lite_escapestring";
    $t["num_rows"]="igk_sql3lite_num_rows";
    $t["num_fields"]="igk_sql3lite_num_fields";
    $t["fetch_field"]="igk_sql3lite_fetch_field";
    $t["fetch_row"]="igk_sql3lite_fetch_row";
    $t["fetch_assoc"]="igk_sql3lite_fetch_assoc";
    $t["close"]="igk_sql3lite_close";
    $t["error"]="igk_sql3lite_error";
    $t["errorc"]="igk_sql3lite_error_code";
    $t["lastid"]="igk_sql3lite_lastid";
    $conf[$n]["func"]=$t;
});