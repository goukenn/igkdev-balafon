<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IDbQueryGrammar.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Database;
use IGK\Database\IGrammar;

/**
 * represent a query gramar interface creator 
 * @package IGK\System\Database
 */
interface IDbQueryGrammar extends IGrammar {
    /**
     * create a table query 
     * @param string $table_name 
     * @param array $columninfo list of column information
     * @param ?string|array $desc table description or extra option to create query 
     * - association array . description|prefix|indexes|Engine
     * @return mixed 
     */
    function createTableQuery(string $table_name, array $columninfo, $desc = null);
    /**
     * create a select query
     * @param string $table_name 
     * @param array $condition 
     * @param mixed $options 
     * @param mixed $inf 
     * @return ?string
     */
    function createSelectQuery(string $table_name, ?array $condition, $options=null): ?string;
    /**
    * create and insert query
    * @param string $table_name
    * @param mixed $values
    * @param mixed $tableInfo
    * @return ?string
    */
    function createInsertQuery(string $table_name, $values, $tableInfo= null):?string;
    /**
     * create update query
     * @param string $table_name 
     * @param mixed $values 
     * @return mixed 
     */
    function createUpdateQuery(string $table_name, $values): ?string;
    /**
    * auto generate doc.
    * @param string $table
    * @param mixed $column_info
    * @param mixed $nk
    * @param ?string $db db name
    * @return mixed
    */
    function add_foreign_key(string $table, $column_info, $nk = null, $db = null);
    /**
     * 
     * @param mixed $type type of operation
     * @param mixed $a first expression
     * @param mixed $b second expression
     * @return mixed 
     */
    function createJoinOperation($type, $a, $b);


    /**
     * build query on expression
     * @param string $first_column 
     * @param string $second_column 
     * @return null|string 
     */
    function joinOnEqual(string $first_column, string $second_column):?string;
}