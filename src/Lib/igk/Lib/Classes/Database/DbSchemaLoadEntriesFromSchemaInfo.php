<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaLoadEntriesFromSchemaInfo.php
// @date: 20230120 17:37:44
namespace IGK\Database;


///<summary></summary>
/**
* 
* @package IGK\Database
*/
class DbSchemaLoadEntriesFromSchemaInfo{
    var $Data;
    var $Entries;
    var $Relations;
    var $RelationsDef;
    var $Migrations;
    var $Version;

    public function __set($n,$v){
        igk_die('not allowed '.$n);
    }
}