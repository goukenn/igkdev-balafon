<?php 
 // @author: C.A.D. BONDJE DOUE
 // @filename: HtmlReaderSkipTest.php
 // @date: 20221129 11:04:13
 // @desc: skip test  
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Html/HtmlReaderSkipTest.php
namespace IGK\Tests\System\Html;

use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html
*/
class HtmlReaderSkipTest extends BaseTestCase{
    function test_skip_script(){
        $n = igk_create_node("div");         
        $n->load(<<<'HTML'
<script>if (i<data){console.log('info');}</script>
HTML);
        $this->assertEquals(<<<'HTML'
<div><script>if (i<data){console.log('info');}</script></div>
HTML,   $n->render());

    }

    function test_skip_code(){
        $n = igk_create_node("div");
        
        $n->load(<<<'HTML'
<code>if (i<data){console.log('info');}</code>
HTML);
        $this->assertEquals(<<<'HTML'
<div><code>if (i<data){console.log('info');}</code></div>
HTML,   $n->render());

    }

    function test_skip_area_code(){
        $n = igk_create_node("div");
        igk_debug(1);
        $n->load(<<<'HTML'
<textarea>if <div>Information du jour </div></textarea>
HTML);
        $this->assertEquals(<<<'HTML'
<div><textarea>if <div>Information du jour </div></textarea></div>
HTML,   $n->render());

    }
}