<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NoDbConnection.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Database;
/**
* Represent IGKNoDbConnection class
*/
class NoDbConnection{

    /**
    * Name of db name.
    * @var mixed
    */
    var $db_name;

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return __CLASS__;
    }

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }

    /**
    * auto generate doc.
    */

    function close(){}

    /**
    * auto generate doc.
    */

    function closeAll(){}

    /**
    * auto generate doc.
    */

    function connect(){
        return false;
    }

    /**
    * auto generate doc.
    */

    function initForInitDb(){}

    /**
    * auto generate doc.
    */

    function insert(){
        return false;
    }

    /**
    * auto generate doc.
    */

    public function openCount(){
        return -1;
    }

    /**
    * Returns true if Connect.
    */
    public function isConnect(){
        return false;
    }

    /**
    * auto generate doc.
    * @param mixed $query
    */

    function sendQuery($query){
        return null;
    }

    /**
    * auto generate doc.
    */

    function setCloseCallback(){}

    /**
    * auto generate doc.
    */

    function setOpenCallback(){}

    /**
    * auto generate doc.
    */

    function flushForInitDb(){}

    /**
    * Returns Version.
    * @return string
    */
    function getVersion():string{
        return '';
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $n
    * @param mixed $name
    */
    public function __call($n, $name){
        return null;
    }

    /**
    * Triggered when calling an inaccessible or undefined static method.
    * @param mixed $name
    * @param mixed $arguments
    */
    public static function __callStatic($name, $arguments)
    { 
        return null;        
    }

    /**
    * Table exists.
    * @return bool
    */
    public function tableExists():bool{
        return false;
    }

    /**
    * Returns Is Connect.
    * @return bool
    */
    public function getIsConnect():bool{
        return false;
    }
}