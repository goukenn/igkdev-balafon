<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IDataDriver.php
// @date: 20220610 13:55:11
// @desc: 
namespace IGK\Database;
use IGK\System\Database\SQLGrammar;
/**
 * represent data driver
 * @package 
 */
interface IDataDriver{
    /**
    * Inserts.
    * @param mixed $table
    * @param mixed $entries
    * @param bool $throwException
    * @return ?string
    */
    function insert($table, $entries, bool $throwException=true);
    /**
     * get the db name. failed in case of no connection to db
     * @return null|string 
     */
    function getDbName():?string;
    /**
    * Returns Version.
    * @return string
    */
    function getVersion():string;
    /**
    * Returns Type.
    * @return string
    */
    function getType():string;
    /**
     * check this driver support type length
     * @param string $type 
     * @param null|int $length 
     * @return bool 
     */
    function allowTypeLength(string $type, ?int $length = null):bool;
    /**
     * get if driver is connected
     * @return bool 
     */
    function getIsConnect(): bool;
    /**
    * Returns Filter.
    * @return bool
    */
    function getFilter():bool;
    /**
    * Returns true if Type Supported.
    * @param string $type
    * @return bool
    */
    function isTypeSupported(string $type):bool;
    /**
    * Escape.
    * @param null|string $column
    * @return string
    */
    function escape(?string $column=null):string;
    /**
    * Escape string.
    * @param null|string $v
    * @return string
    */
    function escape_string(?string $v=null):string;
    /**
    * Escape table name.
    * @param string $v
    * @return string
    */
    function escape_table_name(string $v):string;
    /**
    * Escape table column.
    * @param string $v
    * @return string
    */
    function escape_table_column(string $v):string;
    /**
    * Pushes Relations.
    * @param string $tbname
    * @param mixed $v
    * @return bool
    */
    function pushRelations(string $tbname, $v);
    function supportDefaultValue(string $type):bool;
    /**
    * Returns true if Auto Increment Type.
    * @param string $type
    * @return bool
    */
    function isAutoIncrementType(string $type):bool;
    /**
    * Table exists.
    * @param string $table
    * @param bool $throwex
    * @return bool
    */
    function tableExists(string $table, bool $throwex=true): bool;
    /**
    * Sends Query.
    * @param string $query
    * @param mixed $throwex
    * @param null|mixed $options
    * @param mixed $autoclose
    * @return string
    */
    function sendQuery(string $query, $throwex=true, $options=null, $autoclose=false);
    /**
     * retrieve used date time format
     * @return string 
     */
    function getDateTimeFormat():string;
    /**
     * get data value
     * @param mixed $value 
     * @param mixed $tinf 
     * @return mixed 
     */
    function getDataValue($value, $tinf);
    /**
     * check if data type support length
     * @param string $type 
     * @return bool 
     */
    function getIsLengthData(string $type) : bool;
    /**
     * get if support engine
     * @return bool 
     */
    function getEngineSupport():bool;
    /**
    * Creates Alter Table Format.
    * @return string
    */
    function createAlterTableFormat():string;
    /**
    * auto generate doc.
    * @param mixed $value
    * @return bool
    */
    function filterColumn($columninfo, $value):bool;
    /**
     * resolv driver parameter
     * @param string $key as auto_increment_word
     * @param mixed $rowInfo 
     * @param mixed $tableInfo 
     * @return null|string 
     */
    function getParam(string $key, $rowInfo=null, $tableInfo=null) : ?string;
    /**
     * get format created table 
     * @param null|array $options 
     * @return string 
     */
    function getCreateTableFormat(?array $options=null): ?string;
    /**
     * create table info query
     * @param SQLGrammar $grammar 
     * @param string $table 
     * @param string $dbname 
     * @return string 
     */
    function createTableColumnInfoQuery(SQLGrammar $grammar, string $table, string $column, string $dbname):string;
    /**
     * check that a constraint exists
     * @param string $name 
     * @return bool 
     */
    function constraintExists(string $name):bool;
    /**
    * Constraint foreign key exists.
    * @param string $name
    * @return bool
    */
    function constraintForeignKeyExists(string $name):bool;
    /**
     * get remove foreign query if adapter support foreign key relation
     * @param string $name 
     * @param mixed $column 
     * @return null|string 
     */
    function remove_foreign(string $name, string $column):?string;
    /**
     * flag data
     * @param bool $flag 
     * @return mixed 
     */
    function setForeignKeyCheck($flag);
    /**
     * in query builder retrieve column charset 
     * @param string $charset 
     * @return ?string 
     */
    function queryColumnCharset(string $charset):?string;
}