<?php
// @author: C.A.D. BONDJE DOUE
// @file: FlattenPathTest.php
// @date: 20230118 21:03:32
namespace IGK\Tests\System\IO\Path;

use IGK\System\IO\Path;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\IO\Path
*/
class FlattenPathTest extends BaseTestCase{
    public function test_resolv(){ 
        $this->assertEquals(
            'assets/_prj_/CarRental/assets/css/animate.min.css',
            Path::FlattenPath('../assets/_prj_/CarRental/Data/../assets/css/animate.min.css')
        ); 
    }

    public function test_resolv_combine(){ 
        $this->assertEquals(
            'assets/_prj_/CarRental/assets/css/animate.min.css',
            Path::FlattenPath(Path::Combine('../assets/_prj_/CarRental/Data', '../assets/css/animate.min.css'))
        ); 
    }

    public function test_resolv_combine_1(){ 
        $this->assertEquals(
            'assets/_prj_/CarRental/assets/css/animate.min.css',
            Path::FlattenPath(Path::Combine('../assets/_prj_/CarRental/Data', '../assets/css/./animate.min.css'))
        ); 
    }
}