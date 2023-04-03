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
    function insert($table, $entries, bool $throwException=true);
    /**
     * get the db name. failed in case of no connection to db
     * @return null|string 
     */
    function getDbName():?string;
    /**
     * get if driver is connected
     * @return bool 
     */
    function getIsConnect(): bool;
    function getFilter():bool;
    function isTypeSupported(string $type):bool;
    function escape(?string $column=null):string;
    function escape_string(?string $v=null):string;
    function escape_table_name(string $v):string;
    function escape_table_column(string $v):string;
    function pushRelations(string $tbname, $v);
    function supportDefaultValue(string $type):bool;
    function isAutoIncrementType(string $type):bool;
    function tableExists(string $table): bool;
    function sendQuery(string $query, $throwex=true, $options=null, $autoclose=false);

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

    function createAlterTableFormat():string;

    /**
     * 
     * @param mixed $columninfo 
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
    function createTableColumnInfoQuery(SQLGrammar $grammar, string $table, string $dbname):string;

    /**
     * check that a constraint exists
     * @param string $name 
     * @return bool 
     */
    function constraintExists(string $name):bool;
    function constraintForeignKeyExists(string $name):bool;
}
