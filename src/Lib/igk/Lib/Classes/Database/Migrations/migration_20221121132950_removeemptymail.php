<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20221121 13:29:50
namespace IGK\Database\Migrations;

use IGK\Database\MigrationBase;
use IGK\System\Database\SchemaMigrationBuilder;

///<summary></summary>
/**
* 
* @package IGK\Database\Migrations
*/
class RemoveEmptyMail extends MigrationBase{
	/**
	* update database 
	*/
	public function up(SchemaMigrationBuilder $builder){
	    // $builder->addColumn(...)
		// $table = $builder->getPrefixTable('prospections');
		// $builder->removeColumn($table, 'prs');
		// $builder->addColumn($table, [
		// 	"clName"=>"prsEmail",
		// 	"clType"=>"varchar",
		// 	"clTypeLength"=>255
		// ]);
	}
	/** 
	* downgrade database 
	*/
	function down(SchemaMigrationBuilder $builder){
	    // $builder->rmColumn(...);
	}
}