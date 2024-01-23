<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NoDbConnection.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

///<summary>Represente class: IGKNoDbConnection</summary>
/**
* Represente IGKNoDbConnection class
*/
class NoDbConnection{
    public function __toString()
    {
        return __CLASS__;
    }
    public function __debugInfo()
    {
        return [];
    }
    ///<summary></summary>
    /**
    * 
    */
    function close(){}
    ///<summary></summary>
    /**
    * 
    */
    function closeAll(){}
    ///<summary></summary>
    /**
    * 
    */
    function connect(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    function initForInitDb(){}
    ///<summary></summary>
    /**
    * 
    */
    function insert(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function openCount(){
        return -1;
    }
    public function isConnect(){
        return false;
    }
    ///<summary></summary>
    ///<param name="query"></param>
    /**
    * 
    * @param mixed $query
    */
    function sendQuery($query){
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    function setCloseCallback(){}
    ///<summary></summary>
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
