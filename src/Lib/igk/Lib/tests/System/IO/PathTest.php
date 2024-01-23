<?php
// @author: C.A.D. BONDJE DOUE
// @file: PathTest.php
// @date: 20230918 17:41:00
namespace IGK\Tests\System\IO;

use IGK\System\IO\Path;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\IO
*/
class PathTest extends BaseTestCase{
    public function test_relative_flatten_path(){
        $this->assertEquals(
            "/index.html", 
            Path::CombineAndFlattenPath("/about","../index.html")
        );

        $this->assertEquals(
            "/index.html", 
            Path::CombineAndFlattenPath("/about","/../index.html")
        );
        $this->assertEquals(
            "/index.html", 
            Path::CombineAndFlattenPath("/about","/../../index.html")
        );
    }

    public function test_relative_current_path(){
        $file = Path::CombineAndFlattenPath('/bondje', './');
        $this->assertEquals("/bondje", $file);
    }
}