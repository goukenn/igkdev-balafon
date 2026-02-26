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
* auto generate doc.
* @package IGK\Tests\Database
*/
class RefColumnMappingMockingModel extends ModelBase{

    /**
    * auto generate doc.
    */
    public function getDataTableDefinition(){
        return [];
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    public function getTable(){
        return 'mocking';
    }
}