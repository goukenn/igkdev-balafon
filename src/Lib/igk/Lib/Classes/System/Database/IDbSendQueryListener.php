<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbSendQueryListener.php
// @date: 20231220 11:53:29
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
interface IDbSendQueryListener{
    /**
     * return dash query last error
     * @return mixed 
     */
    function getLastError();
    /**
     * 
     * @param mixed $query 
     * @param bool $throwex 
     * @return mixed 
     */
    function sendMultiQuery($query, $throwex = true);
    /**
     * send query to db system
     * @param string $query 
     * @return mixed 
     */
    function sendQuery(string $query);
    /**
     * get db name
     * @return null|string 
     */
    function getDbName():?string;
}