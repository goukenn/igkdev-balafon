<?php
// @author: C.A.D. BONDJE DOUE
// @file: RefColumnMappingMockingModelTest.php
// @date: 20260226 19:25:32
namespace IGK\Tests\Database;

use IGK\Database\DbColumnInfo; 
use IGK\Models\ModelBase;
/**
* 
* @package IGK\Tests\Database
* @author C.A.D. BONDJE DOUE
*/
/**
* Ref column mapping mocking model.
* @package IGK\Tests\Database
*/
class RefColumnMappingMockingModel extends ModelBase{

    /**
    * Returns Data Table Definition.
    */
    public function getDataTableDefinition(){
        return [];
    }

    /**
    * Get table column info.
    * @return ?array
    */
    protected function _getTableColumnInfo() : ?array{
        return [
            'id'=>new DbColumnInfo(['clName'=>'id','clAutoIncrement'=>true]),
            'name'=>new DbColumnInfo(['clName'=>'name', 'clType'=>'varchar(30)']),
            'test'=>new DbColumnInfo(['clName'=>'test', 'clType'=>'varchar(30)']),
        ];
    }

    /**
    * Returns Table.
    */
    public function getTable(){
        return 'mocking';
    }
}