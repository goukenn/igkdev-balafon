<?php
// @file: IGKQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Database\IDbQueryResult;
/**
 * core query result 
 * @package 
 */
abstract class IGKQueryResult extends IGKObject implements IDbQueryResult{
    private $m_error, $m_errormsg;
    public const CALLBACK_OPTS= \IGK\Database\DbConstants::CALLBACK_OPTS;
    public const RESULTHANDLER="@result_handler";
    public function createEmptyEntry(){
        return null;
    }
    public function getColumns(){
        return null;
    }
    public function getError(){
        return $this->m_error;
    }
    public function getHasError(){
        return !empty($this->m_error);
    }
    public function getHasRow(){
        return ($this->getRowCount() > 0);
    }
    public function getIterator(){
        $t=new IGKIterator($this->getRows());
        return $t;
    }
    public function getResultType(){
        return "unknow";
    }
    public function getRowCount(){
        return 0;
    }
    /**
     * 
     * @return mixed|array|iterable rows list
     */
    public function getRows(){
        return null;
    }
    public function getSuccess(){
        return false;
    }
    public function getValue(){
        return null;
    }
    public function resultTypeIsBoolean(){
        return $this->getResultType() == "boolean";
    }
    protected function setError($error){
        $this->m_error=$error;
    }
    protected function setErrorMsg($msg){
        $this->m_errormsg=$msg;
    }
    public function SortBy($key, $asc=true){
        $t=new IGKSorter();
        $t->key=$key;
        $t->asc=$asc;
        $t->Sort($this);
        return $this;
    }
    public function to_key_array($keyname){
        $tm=[];
        foreach($this->getRows() as $r){
            $tm[$r->$keyname]=$r;
        }
        return $tm;
    }
}