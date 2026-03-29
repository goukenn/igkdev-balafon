<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaLoadEntriesFromSchemaInfo.php
// @date: 20230120 17:37:44
namespace IGK\Database;
/**
* auto generate doc.
* @package IGK\Database
*/
class DbSchemaLoadEntriesFromSchemaInfo{
    /**
    * Property: data.
    * @var mixed
    */
    var $Data;
    /**
    * Property: entries.
    * @var mixed
    */
    var $Entries;
    /**
    * Property: relations.
    * @var mixed
    */
    var $Relations;
    /**
    * Property: relations def.
    * @var mixed
    */
    var $RelationsDef;
    /**
    * Property: migrations.
    * @var mixed
    */
    var $Migrations;
    /**
    * Property: version.
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