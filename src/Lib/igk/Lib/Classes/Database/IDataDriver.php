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
    function getDbName():?string;
    function getFilter():bool;
    function isTypeSupported(string $type):bool;
    function escape($column):string;
    function escape_string($v):string;
    function escape_table_name(string $v):string;
    function escape_table_column(string $v):string;
    function pushRelations($tbname, $v);
    function supportDefaultValue(string $type):bool;
    function isAutoIncrementType(string $type):bool;

    function sendQuery(string $query);

    function getDataValue($value, $tinf);

    function getIsLengthData(string $type) : bool;

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

    function createTableColumnInfoQuery(SQLGrammar $grammar, string $table, string $dbname):string;

    /**
     * check that a constraint exists
     * @param string $name 
     * @return bool 
     */
    function constraintExists(string $name):bool;
}
