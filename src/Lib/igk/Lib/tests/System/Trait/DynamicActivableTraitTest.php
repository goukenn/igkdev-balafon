<?php
// @author: C.A.D. BONDJE DOUE
// @file: DynamicActivableTraitTest.php
// @date: 20260426 20:32:18
// @note: to avoid phpunit warning need to put interface in the same file as test
namespace IGK\Tests\System\Trait;

use IGK\Helper\Activator;
use IGK\Helper\ActivatorReference;
use IGK\Tests\BaseTestCase;


/**
* auto generate doc.
* @package IGK\Tests\System\Trait
* @property mixed $x
* @property mixed $y
*/
interface IDynamicActivableTraitTest{

}
/**
* auto generate doc.
* @package IGK\Tests\System\Trait
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Tests\System\Trait
*/
class DynamicActivableTraitTest extends BaseTestCase{
    function test_dynactivate_reference(){
        $x = 4;
        $g = ['x'=>& $x]; 
        $info = Activator::CreateNewInstance(IDynamicActivableTraitTest::class, $g);
        $info->x = 8; 
        $this->assertEquals(4, $x , 'not equal to 4');
    }
    /**
    * auto generate doc.
    * @return void
    */
    function test_dynactivate_with_reference(){
        $x = 4; 
        $g = ['x'=> ActivatorReference::Create($x)]; 
        $info = Activator::CreateNewInstance(IDynamicActivableTraitTest::class, $g);
        $info->x = 8; 
        $this->assertEquals(8, $x , 'not equal to 4');
    }
    /**
    * auto generate doc.
    * @return void
    */
    function test_dynactivate_with_reference_assert(){
        $x = 4; 
        $g = ['x'=> ActivatorReference::Create($x)]; 
        $info = Activator::CreateNewInstance(IDynamicActivableTraitTest::class, $g);
        $x = 8; 
        $this->assertEquals($info->x, $x , 'not equal to 4');
    }
    /**
    * auto generate doc.
    * @return void
    */
    function test_dynactivate_extract_update(){
        $info = $this->_ref_info(); 
        extract(igk_extract_assoc( $info, 'x*|y'), EXTR_REFS); 
        $x = 8; 
        $y = 10;
        $this->assertEquals($info->x, $x , 'not the same');
        $this->assertNotEquals($info->y, $y , 'are references');
    }
    /**
    * auto generate doc.
    * @return void
    */
    private function _ref_info(){
        $x = 4;
         $g = ['x'=> ActivatorReference::Create($x)]; 
        $info = Activator::CreateNewInstance(IDynamicActivableTraitTest::class, $g);
        return $info;
    }


}