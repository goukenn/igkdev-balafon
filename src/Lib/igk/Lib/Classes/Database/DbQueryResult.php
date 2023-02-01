<?php
// @file: IGKQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;

use IGKException;
use IGKIterator;
use IGKObject;
use IGKSorter;

abstract class DbQueryResult extends IGKObject implements IDbQueryResult{
    private $m_error, $m_errormsg;
    /**
     * key name use to filter result
     */
    public const CALLBACK_OPTS= \IGK\Database\DbConstants::CALLBACK_OPTS;
    ///<summary></summary>
    public function createEmptyEntry(){
        return null;
    }
    ///<summary></summary>
    public function getColumns(){
        return null;
    }
    ///<summary></summary>
    public function getError(){
        return $this->m_error;
    }
    ///<summary></summary>
    public function getHasError(){
        return !empty($this->m_error);
    }
    ///<summary></summary>
    public function getHasRow(){
        return ($this->getRowCount() > 0);
    }
    ///<summary>return the itarator that can be used to iterate shift onto an element</summary>
    public function getIterator(){
        $t=new IGKIterator($this->getRows());
        return $t;
    }
    ///<summary></summary>
    public function getResultType(){
        return "unknow";
    }
    ///<summary></summary>
    public function getRowCount(){
        return 0;
    }
    ///<summary></summary>
    /**
     * get rows
     * @return null|Iterable|array
     */
    public function getRows(){
        return null;
    }
    ///<summary></summary>
    public function getSuccess(){
        return false;
    }
    ///<summary></summary>
    public function getValue(){
        return null;
    }
    ///<summary></summary>
    public function ResultTypeIsBoolean(){
        return $this->getResultType() == "boolean";
    }
    ///<summary></summary>
    ///<param name="error"></param>
    protected function setError($error){
        $this->m_error=$error;
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    protected function setErrorMsg($msg){
        $this->m_errormsg=$msg;
    }
    ///<summary>sort this result by </summary>
    public function SortBy($key, $asc=true){
        $t=new IGKSorter();
        $t->key=$key;
        $t->asc=$asc;
        $t->Sort($this);
        return $this;
    }
    ///<summary> if all row loaded get row to other keys</summary>
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
