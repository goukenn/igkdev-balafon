<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDatabaseCreator.php
// @date: 20230423 10:36:49
namespace IGK\Database;
/**
* database driver selector
* @package IGK\Database
*/
interface IDatabaseCreator{
    /**
     * select database
     * @param null|string $dbname 
     * @return mixed 
     */
    function selectDb(?string $dbname=null);
    /**
     * db name or string expression
     * @param null|string $dbname 
     * @return mixed 
     */
    function createDb(?string $dbname=null);
}