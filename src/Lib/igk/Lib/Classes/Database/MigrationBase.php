<?php
// @author: C.A.D. BONDJE DOUE
// @file: MigrationBase.php
// @date: 20221112 07:49:45
namespace IGK\Database;
use IGK\System\Database\SchemaBuilder;
use IGK\System\Database\SchemaMigrationBuilder;
/**
* migration base class
* @package IGK\Database
*/
abstract class MigrationBase{

    /**
    * auto generate doc.
    * @param SchemaMigrationBuilder $builder
    */
    abstract function up(SchemaMigrationBuilder $builder);
    abstract function down(SchemaMigrationBuilder $builder);
}