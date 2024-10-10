<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitClassBuilderTest.php
// @date: 20240921 08:32:19
namespace IGK\Tests\Database\Models\Helper;

use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbDisplayExpression;
use IGK\Database\IDbMigrationInfo;
use IGK\Database\Models\Helper\InitClassBuilder;
use IGK\System\Database\Helper\DbUtility;
use IGK\Tests\BaseTestCase;
use TBN\Tests\DummyCtrl;

///<summary></summary>
/**
* 
* @package IGK\Tests\Database\Models\Helper
* @author C.A.D. BONDJE DOUE
*/
class InitClassBuilderTest extends BaseTestCase{
    /**
     * 
     * @return IDbMigrationInfo 
     */
    protected function _getMigrationInfo(){
        $o = igk_createobj();
        $o->columnInfo = [
            "id"=>DbColumnInfo::CreateAutoInc('id'),
            "name"=>new DbColumnInfo(['clName'=>'name','clType'=>'varchar(30)', 'clIsUnique'=>true])
        ];
        return $o;
    }
    public function test_dbmodel_initclass_builder(){ 
        $migrationInfo = $this->_getMigrationInfo(); 
        $g = InitClassBuilder::BuildInitialModelClass("Dummy", "Dummy", $migrationInfo, DummyClassBuildController::ctrl());
        $this->assertTrue(!empty($g));
    }

   
}

class DummyClassBuildController extends BaseController{

}