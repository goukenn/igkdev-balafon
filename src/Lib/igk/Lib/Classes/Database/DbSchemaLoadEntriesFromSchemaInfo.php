<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaLoadEntriesFromSchemaInfo.php
// @date: 20230120 17:37:44
namespace IGK\Database;
/**
* 
* @package IGK\Database
*/
class DbSchemaLoadEntriesFromSchemaInfo{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Data;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Entries;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Relations;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $RelationsDef;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Migrations;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Version;

    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n,$v){
        igk_die('not allowed '.$n);
    }
}