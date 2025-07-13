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
class DbSingleValueResult{
    var $RowCount, $query, $type, $value;
    public function __get($name){
        if(method_exists($this, $name)){
            return $this->$name();
        }
        return null;
    }
    public function __toString(){
        return $this->getValue();
    }
    public function getResultType(){
        return $this->type;
    }
    public function getRowAtIndex(int $index){
        return null;
    }
    public function getRowCount(){
        return 0;
    }
    public function getRows(){
        return [];
    }
    public function getValue(){
        return $this->value;
    }
    public function resultTypeIsBoolean(){
        return ($this->type == "boolean");
    }
    public function sortBy(){    }
    public function Success(){
        return ($this->type == "boolean") && ($this->value == true);
    }
}