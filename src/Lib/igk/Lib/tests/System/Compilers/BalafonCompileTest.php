<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCompileTest.php
// @date: 20220830 17:44:36
// @desc: 
namespace IGK\Tests\System\Compilers;

use IGK\Tests\BaseTestCase;

class BalafonCompileTest extends BaseTestCase{

    public function test_compile_file(){
        $s = "";
        $s .= <<<'PHP'
if (!function_exists('__')){
    function __($n){
        return igk_resources_gets(...func_get_args());
    };
}else {
    if (!class_exists(translator::class, false)){
        require_once IGK_LIB_CLASSES_DIR.'/IGKTranslator.php';

        class translator extends \IGK\IGKTranslator{        
        }
    }
}
$params = ['code'=>413];
include('/Volumes/Data/wwwroot/core/Projects/igk_default/Views/.error/413.phtml');
PHP;
        //$s .= "include('/Volumes/Data/wwwroot/core/Projects/igk_default/Views/.error/413.phtml');";
        ob_start();
        eval($s);
        $ob = ob_get_contents();
        ob_end_clean();
        $this->assertEquals("response...", 
        $ob, "failed");
    }
}