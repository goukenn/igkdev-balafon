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
    var $db_name;

    public function __toString()
    {
        return __CLASS__;
    }
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
    function getVersion():string{
        return '';
    }

    public function __call($n, $name){
        return null;
    }
    public static function __callStatic($name, $arguments)
    { 
        return null;        
    }
    public function tableExists():bool{
        return false;
    }
    public function getIsConnect():bool{
        return false;
    }
}
