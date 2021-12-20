<?php
// @file: IGKQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

abstract class IGKQueryResult extends IGKObject{
    private $m_error, $m_errormsg;
    public const CALLBACK_OPTS="@callback";
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
     * 
     * @return mixed|array|iterable rows list
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
}
