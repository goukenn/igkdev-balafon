<?php
// @file: IGKQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;
use IGKException;
use IGKIterator;
use IGKObject;
use IGKSorter;
/**
* Db query result.
* @package IGK\Database
*/
abstract class DbQueryResult extends IGKObject implements IDbQueryResult{
    /**
    * Properties: error, errormsg.
    * @var mixed
    */
    private $m_error, $m_errormsg;
    /**
     * key name use to filter result
     */
    public const CALLBACK_OPTS= \IGK\Database\DbConstants::CALLBACK_OPTS;
    /**
    * Creates Empty Entry.
    */
    public function createEmptyEntry(){
        return null;
    }
    /**
    * Returns Columns.
    */
    public function getColumns(){
        return null;
    }
    /**
    * Returns Error.
    */
    public function getError(){
        return $this->m_error;
    }
    /**
    * Returns Has Error.
    */
    public function getHasError(){
        return !empty($this->m_error);
    }
    /**
    * Returns Has Row.
    */
    public function getHasRow(){
        return ($this->getRowCount() > 0);
    }
    /**
    * Returns Iterator.
    */
    public function getIterator(){
        $t=new IGKIterator($this->getRows());
        return $t;
    }
    /**
    * Returns Result Type.
    */
    public function getResultType(){
        return "unknow";
    }
    /**
    * Returns Row Count.
    */
    public function getRowCount(){
        return 0;
    }
    /**
     * get rows
     * @return null|Iterable|array
     */
    public function getRows(){
        return null;
    }
    /**
    * Returns Success.
    */
    public function getSuccess(){
        return false;
    }
    /**
    * Returns Value.
    */
    public function getValue(){
        return null;
    }
    /**
    * Result type is boolean.
    */
    public function resultTypeIsBoolean(){
        return $this->getResultType() == "boolean";
    }
    /**
    * Sets Error.
    * @param mixed $error
    */
    protected function setError($error){
        $this->m_error=$error;
    }
    /**
    * Sets Error Msg.
    * @param mixed $msg
    */
    protected function setErrorMsg($msg){
        $this->m_errormsg=$msg;
    }
    /**
    * Sorts By.
    * @param mixed $key
    * @param mixed $asc
    */
    public function SortBy($key, $asc=true){
        $t=new IGKSorter();
        $t->key=$key;
        $t->asc=$asc;
        $t->Sort($this);
        return $this;
    }
    /**
    * To key array.
    * @param mixed $keyname
    */
    public function to_key_array($keyname){
        $tm=[];
        foreach($this->getRows() as $r){
            $tm[$r->$keyname]=$r;
        }
        return $tm;
    }
    /**
     * get row at index
     * @param int $index 
     * @return mixed 
     * @throws IGKException 
     */
    public function getRowAtIndex(int $index){
        return igk_getv(array_values($this->getRows()), $index);
    }
}