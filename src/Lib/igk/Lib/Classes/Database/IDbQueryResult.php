<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IDbQueryResult.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Database;

/**
 * db query result
 * @package IGK\Database
 * @property ?array $Rows data entries 
 * @property ?int $RowCount number of data entries
 */
interface IDbQueryResult{
    /**
     * get rows
     * @return ?array
     */
    function getRows();

    /**
     * number of row
     * @return ?int
     */
    function getRowCount();

    /**
     * 
     * @return mixed 
     */
    // function to_array();

    /**
     * get if query success
     * @return bool 
     */
    function success(): bool;
}