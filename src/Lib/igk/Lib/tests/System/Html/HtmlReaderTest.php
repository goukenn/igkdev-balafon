<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlReaderTest.php
// @date: 20220803 13:48:54
// @desc: 
// @cmd : phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Html/HtmlReaderTest.php

namespace IGK\Tests\System\Html;

use Exception;
use IGK\Controllers\SysDbController;
use IGK\Helper\Activator;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\Tests\BaseTestCase;
use IGKException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionException;

/**
* Html reader test.
* @package IGK\Tests\System\Html
*/
class HtmlReaderTest extends BaseTestCase
{

    /**
    * Tests html missing p close.
    */
    public function test_html_missing_p_close()
    {
        // + | --------------------------------------------------------------------
        // + | p must be clause .... 
        // + | 
        $n = igk_create_notagnode();
        $n->load(<<<'HTML'
<body>
    <div class="tm-cta3-content-wrapper">
        <p> Nous offrons des services</p>
        <p>
    </div>
    <div>info</div>
</body>
HTML);
        $this->assertEquals(
            '<body><div class="tm-cta3-content-wrapper"><p> Nous offrons des services</p><p></p></div><div>info</div> </body>',
            $n->render(),
            "read comment style not ok"
        );
    }

    /**
    * auto generate doc.
    * @return void
    */
    public function test_html_reading_attr_link_reading()
    {
        $n = igk_create_notagnode();
        $n->load('<a d-f_0="y:100%;" d-f_0_mask="u:t;y:100%;" d-f_1="e:power0.in;">link</a>');
        $this->assertEquals(
            '<a d-f_0="y:100%;" d-f_0_mask="u:t;y:100%;" d-f_1="e:power0.in;">link</a>',
            $n->render(),
            "read comment style not ok"
        );
    }

    /**
    * Tests html reading style comment.
    */
    public function test_html_reading_style_comment()
    {
        $n = igk_create_notagnode();
        $n->load("<style>/** Mega Menu CSS: fs **/</style>");
        $this->assertEquals(
            "<style>/** Mega Menu CSS: fs **/</style>",
            $n->render(),
            "read comment style not ok"
        );
    }

    /**
    * Tests read php processor.
    */
    function test_read_php_processor()
    {
        // + | T  
        $n = igk_create_notagnode();
        $n->load("<?php\n echo 'bonjour';");
        $this->assertEquals(
            "<?php\n echo 'bonjour';",
            $n->render(),
            "read content"
        );
    }

    /**
    * Tests read php processor with comment.
    */
    function test_read_php_processor_with_comment()
    {
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
    public function test_loading_content()
    {

        $n = igk_create_node("div");
        $n->Content = "item <b>sample</b> info";
        $this->assertEquals(
            "<div>item <b>sample</b> info</div>",
            $n->render()
        );
    }

    /**
    * Tests loading attr expression.
    */
    public function test_loading_attr_expression()
    {
        $n = igk_create_node("div");
        $n->load("<div><igk:attr-expression id='info' /><igk:usesvg igk:args='data' title='data'/></div>");
        $this->assertEquals(
            '<div><div id="info"><span title="data"></span></div></div>',
            $n->render(),
            "not resolved"
        );
    }

    /**
    * Tests bind expression.
    */
    public function test_bind_expression()
    {
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

    /**
    * Tests bind expression block.
    */
    public function test_bind_expression_block()
    {
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

    /**
    * Tests bind expression block middle.
    */
    public function test_bind_expression_block_middle()
    {
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

    /**
    * Tests read encapsed branck.
    */
    public function test_read_encapsed_branck()
    {
        $pos = 0;

        $str = <<<'EOF'
'alert("Êtes l'avis ?")'
EOF;
        $this->assertEquals(
            "'alert(\"Êtes l'avis ?\")'",
            igk_str_read_brank($str, $pos, "'", "'", null, true, true, '"')
        );
    }

    /**
    * Tests read encapsed string.
    */
    public function test_read_encapsed_string()
    {
        $n = igk_create_notagnode();
        $n->Content = (<<<EOF
<a onClick='alert("Êtes-vous sûr de bien vouloir supprimer l'avis ?")'  href='supprimer_avis.php?id=16563' class='blog_link'>Supprimer</a>
EOF);
        $this->assertEquals(
            '<a class="blog_link" href="supprimer_avis.php?id=16563" onClick="alert(&quot;Êtes-vous sûr de bien vouloir supprimer l\'avis ?&quot;)">Supprimer</a>',
            $n->render()
        );
    }

    /**
    * Tests read empty ignore.
    */
    public function test_read_empty_ignore()
    {
        $n = igk_create_notagnode();
        $ldcontext = igk_createloading_context(SysDbController::ctrl(), []);
        $ldcontext->engineNode = $n;

        $n->Content = (<<<EOF
<p>{{ \$raw->experience }}</p>
EOF);

        igk_html_article_bind_content(
            $n,
            "<p>{{ \$raw->experience }}=Sample for what</p>",
            true,
            $ldcontext,
            "__dummy__",
            SysDbController::ctrl(),
            (object)[null],
            false
        );

        $this->assertEquals(
            '<p>{{ $raw->experience }}</p><p>=Sample for what</p>',
            $n->render()
        );
    }

    /**
    * Tests load activate attribute.
    */
    public function test_load_activate_attribute()
    {
        $n = igk_create_notagnode();

        $n->load("<script scoped></script>");
        $r  =  $n->getElementsByTagName('script')[0];
        $this->assertEquals(
            1,
            count($r->getAttributes()->to_array())
        );
    }

    /**
    * Tests load activate attribute 2.
    */
    public function test_load_activate_attribute_2()
    {
        $n = igk_create_notagnode();

        $n->load("<script scoped/>");
        $r  =  $n->getElementsByTagName('script')[0];
        $this->assertEquals(
            1,
            count($r->getAttributes()->to_array())
        );
    }

    /**
    * auto generate doc.
    * @return void
    */
    public function test_read_comment_with_single_cote()
    {
        // end with line feed on not line fied
        $t = "<code>// creation d'un noeud simple</code>";
        $n = igk_create_notagnode();
        $n->load($t);
        $this->assertEquals(
            "<code>// creation d'un noeud simple</code>",
            $n->render()
        );
    }

    /**
    * Tests read comment with single cote 2.
    */
    public function test_read_comment_with_single_cote_2()
    {
        $t = implode(
            "\n",
            [
                "<code>// creation d'un noeud simple",
                "\$x= ''; // <div>information : ok </div>",
                "</code>"
            ]
        );
        $n = igk_create_notagnode();
        $n->load($t);
        $this->assertEquals(
            $t,
            $n->render()
        );
    }

    /**
    * Tests load activate attribute list.
    */
    public function test_load_activate_attribute_list()
    {
        $src =
            implode("\n", [
                '<div sample',
                '    = "44"',
                '    X',
                '    y>',
                '    x</div>'
            ]);

        $d = igk_create_node('p');
        $d->load($src);

        $this->assertEquals('<p><div X="true" sample="44" y="true"> x</div></p>', $d->render());
    }

    /**
    * Tests reading data.
    */
    public function test_reading_data()
    {
        $src = implode("\n", [
            '<Column',
            'clIsUnique ="false"',
            'clAutoIncrement ',
            'clIsPrimary',
            '></Column>'
        ]);
        $d = igk_create_node('p');
        $d->load($src);

        $this->assertEquals('<p><Column clAutoIncrement="true" clIsPrimary="true" clIsUnique="false"></Column></p>', $d->render());
    }
}
