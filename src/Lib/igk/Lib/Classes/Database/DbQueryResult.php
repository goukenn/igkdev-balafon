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
* auto generate doc.
* @package IGK\Database
*/
abstract class DbQueryResult extends IGKObject implements IDbQueryResult{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_error, $m_errormsg;
    /**
     * key name use to filter result
     */
    public const CALLBACK_OPTS= \IGK\Database\DbConstants::CALLBACK_OPTS;

    /**
    * auto generate doc.
    */

    public function createEmptyEntry(){
        return null;
    }

    /**
    * auto generate doc.
    */

    public function getColumns(){
        return null;
    }

    /**
    * auto generate doc.
    */

    public function getError(){
        return $this->m_error;
    }

    /**
    * auto generate doc.
    */

    public function getHasError(){
        return !empty($this->m_error);
    }

    /**
    * auto generate doc.
    */

    public function getHasRow(){
        return ($this->getRowCount() > 0);
    }

    /**
    * auto generate doc.
    */

    public function getIterator(){
        $t=new IGKIterator($this->getRows());
        return $t;
    }

    /**
    * auto generate doc.
    */

    public function getResultType(){
        return "unknow";
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
    */

    public function getSuccess(){
        return false;
    }

    /**
    * auto generate doc.
    */

    public function getValue(){
        return null;
    }

    /**
    * auto generate doc.
    */

    public function resultTypeIsBoolean(){
        return $this->getResultType() == "boolean";
    }

    /**
    * auto generate doc.
    * @param mixed $error
    */

    protected function setError($error){
        $this->m_error=$error;
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    protected function setErrorMsg($msg){
        $this->m_errormsg=$msg;
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
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