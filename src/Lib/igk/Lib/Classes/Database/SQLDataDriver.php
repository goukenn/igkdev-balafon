<?php
// @author: C.A.D. BONDJE DOUE
// @file: SQLDataDriver.php
// @date: 20260831 14:54:04
namespace IGK\Database;


/**
* SQL datadriver 
* @package IGK\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class SQLDataDriver extends DataAdapterBase implements IDataDriver{
    public abstract function getNullValue():?string;
    /**
     * check if typ is numeric
     * @param string $type 
     * @return bool 
     */
    public abstract function isNumeric(string $type):bool;
}