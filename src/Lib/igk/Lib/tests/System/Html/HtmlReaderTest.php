<?php
namespace IGK\Tests\System\Html;

use IGK\Tests\BaseTestCase;

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
        $src = "<?php\necho 'bonjour';\n//\"\n\necho 'sample'; ";
        $n->load($src);
        $this->assertEquals(
            "<?php\necho 'bonjour';\n//\"\n\necho 'sample';",
            $n->render(),
            "read content"
        ); 
        $this->assertStringEndsNotWith("\"", $src,  "comment files must not end with \"");
    }
}
