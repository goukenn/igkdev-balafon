<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssUtilsTest.php
// @date: 20230316 17:28:12
namespace IGK\Tests\System\Css;

use IGK\System\Html\Css\CssUtils;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Css
*/
class CssUtilsTest extends BaseTestCase{
    public function test_detected_operator(){
        $tabs = CssUtils::GetClassValues("-value +info");

        $this->assertEquals([
            ["value", "-"],
            ["info", "+"],
        ], $tabs);
    }

    public function test_remove_transform(){
        $v =  'color:[cl:red]; [trans:2.s ease] display:block';
                    //remove brank definitions 
                    
        $v = CssUtils::RemoveNoTransformPropertyStyle($v);
        $this->assertEquals('color:[cl:red]; [trans:2.s ease]', $v);
    }
    public function test_remove_transform_before(){
        $v =  'color:blue; border: 1px solid [cl:red]; display:block';
                    //remove brank definitions 
                    
        $v = CssUtils::RemoveNoTransformPropertyStyle($v);
        $this->assertEquals('border: 1px solid [cl:red];', $v);
    }
    public function test_check_remove_transform_litteral_style(){
        $v ="{sys:posab, fitw} margin-top:-10px; {sys:flex}  content:'{present}';   color:indigo;"; 
        $v = CssUtils::RemoveTransformLitteralFrom($v);
        $this->assertEquals('margin-top:-10px;content:\'{present}\';   color:indigo;', $v);
    }
    public function test_check_default_style(){
        $v ="{sys:posab, fitw} margin-top:-10px; visibility: hidden; [trans: .5s all ease-out] opacity:0; left:0px; right:0px; z-index: 100; min-height: 80px; background-color: [cl:menuLayerBackground,#222a];"; 
        $v = CssUtils::RemoveNoTransformPropertyStyle($v);
        $this->assertEquals('[trans: .5s all ease-out] background-color: [cl:menuLayerBackground,#222a];', $v);
    }

    public function test_check_default_in_string_style(){
        $v ="m:['sys: -10px;'];"; 
        $v = CssUtils::RemoveNoTransformPropertyStyle($v);
        $this->assertEquals("m:['sys: -10px;'];", $v);
    }
}