<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssPhpDetectorTestsTest.php
// @date: 20260917 23:14:38
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssPhpDetector;
use IGK\Tests\BaseTestCase;

/**
* 
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssPhpDetectorTestsTest extends BaseTestCase{
    public function test_cssphpdetector_build(){
        $detector = new CssPhpDetector;       
        $detector->list = ['a-87'=>1]; 
        $r = $detector->resolve('$p = "a-87";');
        $s= json_encode(array_keys($r ?? []));
        // igk_wln_e(__FILE__.":".__LINE__ , $s) ;
        $this->assertEquals('["a-87"]', $s);
    }
    public function test_cssphpdetector_detect_zindex(){
        $detector = new CssPhpDetector;       
        $detector->list = ['a-87'=>1]; 
        $r = $detector->resolve('$p = "z-10 dispflex";');
        $s= json_encode(array_keys($r ?? []));
        // igk_wln_e(__FILE__.":".__LINE__ , $s) ;
        $this->assertEquals('["z-10"]', $s);
    }
}