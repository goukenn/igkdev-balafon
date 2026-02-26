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
    * auto generate doc.
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
    * 
    */

    function close(){}
    /**
    * 
    */

    function closeAll(){}
    /**
    * 
    */

    function connect(){
        return false;
    }
    /**
    * 
    */

    function initForInitDb(){}
    /**
    * 
    */

    function insert(){
        return false;
    }
    /**
    * 
    */

    public function openCount(){
        return -1;
    }

    /**
    * auto generate doc.
    */
    public function isConnect(){
        return false;
    }
    /**
    * 
    * @param mixed $query
    */

    function sendQuery($query){
        return null;
    }
    /**
    * 
    */

    function setCloseCallback(){}
    /**
    * 
    */

    function setOpenCallback(){}
    /** */

    function flushForInitDb(){}

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @return bool
    */
    public function tableExists():bool{
        return false;
    }

    /**
    * auto generate doc.
    * @return bool
    */
    public function getIsConnect():bool{
        return false;
    }
}