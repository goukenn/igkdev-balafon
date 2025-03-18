<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMinifierTest.php
// @date: 20241029 14:19:24
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssMinifier;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssMinifierTest extends BaseTestCase{
    public function test_cssminify_remove_whitespace(){
        $css = 'body     { background-color:        white; color:   indigo   }'; 
        $minifier = new CssMinifier;  
        $this->assertEquals('body{background-color:white;color:indigo}', 
        $minifier->minify($css));

    }
    public function test_cssminify_leave_comment(){
        $css = '/* information du jour */ body     { background-color:        white; color:indigo}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = true;
        $this->assertEquals('/* information du jour */body{background-color:white;color:indigo}', 
        $minifier->minify($css)); 
    }
    function test_cssminify_leave_remove_comment(){
        $css = '/* information du jour */ body     { background-color:        white; color:indigo}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('body{background-color:white;color:indigo}', 
        $minifier->minify($css)); 
    }
    function test_cssminify_leave_operator(){
        $css = '/* information du jour */ body{     aspect-ratio:    16                / 9}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('body{aspect-ratio:16 / 9}', 
        $minifier->minify($css)); 
    }
    function test_cssminity_function_call(){
       
        $css = ' body{width: calc(2em + 3px)   ; }'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('body{width:calc(2em + 3px);}', 
        $minifier->minify($css));
    }
    function test_cssminity_attribute(){
       
        $css = 'body  .color[   basic   ~= info    ] > d:first-child:not(.level) + .red{color:red;}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('body .color[basic~=info] > d:first-child:not(.level) + .red{color:red;}', 
        $minifier->minify($css));
    }
    function test_cssminity_custom_property_speudo(){
       
        $css = 'body:hover div:first-child { color: red;}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('body:hover div:first-child{color:red;}', 
        $minifier->minify($css));
    }
    function test_cssminity_important(){
       
        $css = 'div{background-color: indigo !important;}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('div{background-color:indigo !important;}', 
        $minifier->minify($css));
    }
    function test_cssminity_media(){
       
        $css = '@media (max-width: 300px)and(min-width:250px){div{background-color: indigo !important;}}'; 
        $minifier = new CssMinifier;  
        $minifier->preserveComment = false;
        $this->assertEquals('@media(max-width:300px) and (min-width:250px){div{background-color:indigo !important;}}', 
        $minifier->minify($css));
    }
}