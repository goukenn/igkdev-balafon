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
* auto generate doc.
* @package IGK\Database
*/
class DbSingleValueResult{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $RowCount, $query, $type, $value;

    /**
    * auto generate doc.
    * @param mixed $name
    */

    public function __get($name){
        if(method_exists($this, $name)){
            return $this->$name();
        }
        return null;
    }

    /**
    * auto generate doc.
    */

    public function __toString(){
        return $this->getValue();
    }

    /**
    * auto generate doc.
    */

    public function getResultType(){
        return $this->type;
    }

    /**
    * auto generate doc.
    * @param int $index
    */

    public function getRowAtIndex(int $index){
        return null;
    }

    /**
    * auto generate doc.
    */

    public function getRowCount(){
        return 0;
    }

    /**
    * auto generate doc.
    */

    public function getRows(){
        return [];
    }

    /**
    * auto generate doc.
    */

    public function getValue(){
        return $this->value;
    }

    /**
    * auto generate doc.
    */

    public function resultTypeIsBoolean(){
        return ($this->type == "boolean");
    }

    /**
    * auto generate doc.
    */

    public function sortBy(){    }

    /**
    * auto generate doc.
    */

    public function Success(){
        return ($this->type == "boolean") && ($this->value == true);
    }
}