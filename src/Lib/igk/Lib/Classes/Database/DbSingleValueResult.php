<?php
// @file: IGKDBSingleValueResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;
/**
* Db single value result.
* @package IGK\Database
*/
class DbSingleValueResult{
    /**
    * Properties: row count, query, type, value.
    * @var mixed
    */
    var $RowCount, $query, $type, $value;
    /**
    * Magic getter for dynamic properties.
    * @param mixed $name
    */
    public function __get($name){
        if(method_exists($this, $name)){
            return $this->$name();
        }
        return null;
    }
    /**
    * Returns string representation.
    */
    public function __toString(){
        return $this->getValue();
    }
    /**
    * Returns Result Type.
    */
    public function getResultType(){
        return $this->type;
    }
    /**
    * Returns Row At Index.
    * @param int $index
    */
    public function getRowAtIndex(int $index){
        return null;
    }
    /**
    * Returns Row Count.
    */
    public function getRowCount(){
        return 0;
    }
    /**
    * Returns Rows.
    */
    public function getRows(){
        return [];
    }
    /**
    * Returns Value.
    */
    public function getValue(){
        return $this->value;
    }
    /**
    * Result type is boolean.
    */
    public function resultTypeIsBoolean(){
        return ($this->type == "boolean");
    }
    /**
    * Sorts By.
    */
    public function sortBy(){    }
    /**
    * Success.
    */
    public function Success(){
        return ($this->type == "boolean") && ($this->value == true);
    }
}