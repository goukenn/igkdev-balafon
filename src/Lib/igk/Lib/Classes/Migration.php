<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Migration.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\System\Database\Migrations;
/**
* Migration.
* @package IGK\System\Database\Migrations
*/
abstract class Migration{
    /**
     * Applies the migration (runs the forward schema changes).
     */
    public function up(){
    }
    /**
     * Reverts the migration (rolls back the schema changes).
     */
    public function down(){
    }
}