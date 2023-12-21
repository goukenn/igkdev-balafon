<?php
// @file: IGKDBSingleValueResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;
class DbSingleValueResult{
    var $RowCount, $query, $type, $value;
    ///<summary>Represente __get function</summary>
    ///<param name="name"></param>
    public function __get($name){
        if(method_exists($this, $name)){
            return $this->$name();
        }
        return null;
    }
    ///<summary>Represente __toString function</summary>
    public function __toString(){
        return $this->getValue();
    }
    ///<summary>Represente getResultType function</summary>
    public function getResultType(){
        return $this->type;
    }
    ///<summary>Represente getRowAtIndex function</summary>
    ///<param name="index" type="int"></param>
    public function getRowAtIndex(int $index){
        return null;
    }
    ///<summary>Represente getRowCount function</summary>
    public function getRowCount(){
        return 0;
    }
    ///<summary>Represente getRows function</summary>
    public function getRows(){
        return [];
    }
    ///<summary>Represente getValue function</summary>
    public function getValue(){
        return $this->value;
    }
    ///<summary>Represente resultTypeIsBoolean function</summary>
    public function resultTypeIsBoolean(){
        return ($this->type == "boolean");
    }
    ///<summary>Represente sortBy function</summary>
    public function sortBy(){    }
    ///<summary>Represente Success function</summary>
    public function Success(){
        return ($this->type == "boolean") && ($this->value == true);
    }
}
