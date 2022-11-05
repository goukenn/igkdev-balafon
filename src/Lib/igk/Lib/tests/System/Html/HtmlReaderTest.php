<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlReaderTest.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\System\Html;

use Exception;
use IGK\Helper\Activator;
use IGK\Tests\BaseTestCase;
use IGKException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;

class HtmlReaderTest extends BaseTestCase{
    function test_read_php_processor(){
        // + | T  
        $n = igk_create_notagnode();
        $n->load("<?php\n echo 'bonjour';");
        $this->assertEquals(
            "<?php\n echo 'bonjour';",
            $n->render(),
            "read content"
        );
    }
    function test_read_php_processor_with_comment(){
        // + | T  
        $n = igk_create_notagnode();
 
        // $src = file_get_contents(igk_io_projectdir()."/L81/Views/dashboard/settings.phtml");
        $src = "<?php\necho 'bonjour';\n//\"\n\necho 'sample';";
        $n->load($src);
        $this->assertEquals(
            "<?php\necho 'bonjour';\n//\"\n\necho 'sample';",
            $n->render(),
            "read content"
        ); 
        $this->assertStringEndsNotWith("\"", $src,  "comment files must not end with \"");
    }

    /**
     * loading text content
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_loading_content(){
        
        $n = igk_create_node("div");
        $n->Content = "item <b>sample</b> info";
        $this->assertEquals(
            "<div>item <b>sample</b> info</div>",
            $n->render()
        ); 
    }

    public function test_loading_attr_expression(){
        $n = igk_create_node("div");
        $n->load("<div><igk:attr-expression id='info' /><igk:usesvg igk:args='data' title='data'/></div>");
        $this->assertEquals(
            '<div><div id="info"><span title="data"></span></div></div>',
            $n->render(),
            "not resolved"
        ); 
    }

    public function test_bind_expression(){
        $n = igk_create_node("div");
        $c = new \IGK\System\Html\HtmlBindingContextOptions();
        $c->transformToEval = true;  
        $n->load('<<?= $x ?>>info</<?= $x ?>>', $c);
        $this->assertEquals(
            '<div><<?= $x ?>>info</<?= $x ?>></div>',
            $n->render(),
            "not resolved"
        ); 
    }
    public function test_bind_expression_block(){
        $n = igk_create_node("div");
        $c = new \IGK\System\Html\HtmlBindingContextOptions();
        $c->transformToEval = true;  
        $n->load('<block-<?= $x ?>>info</block-<?= $x ?>>', $c);
        $this->assertEquals(
            '<div><block-<?= $x ?>>info</block-<?= $x ?>></div>',
            $n->render(),
            "not resolved"
        ); 
    }
    public function test_bind_expression_block_middle(){
        $n = igk_create_node("div");
        $c = new \IGK\System\Html\HtmlBindingContextOptions();
        $c->transformToEval = true;  
        $n->load('<block-<?= $x ?>-sample>info</block-<?= $x ?>-sample>', $c);
        $this->assertEquals(
            '<div><block-<?= $x ?>-sample>info</block-<?= $x ?>-sample></div>',
            $n->render(),
            "not resolved"
        ); 
    }
}
