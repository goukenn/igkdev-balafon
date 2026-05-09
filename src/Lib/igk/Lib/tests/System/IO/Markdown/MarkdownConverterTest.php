<?php
// @author: C.A.D. BONDJE DOUE
// @file: MardownConverterTest.php
// @date: 20241105 09:16:09
namespace IGK\Tests\System\IO\Markdown;

use Exception;
use IGK\System\IO\Markdown\MarkdownConverter;
use IGK\Tests\BaseTestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * auto generate doc.
 * @package IGK\Tests\System\IO\Markdown
 * @author C.A.D. BONDJE DOUE
 */
class MarkdownConverterTest extends BaseTestCase
{
    /**
     * auto generate doc.
     * @param string $src
     * @param bool $allowDocumentLink
     * @return
     */
    private function _transform(string $src, bool $allowDocumentLink = false)
    {
        $converter = new MarkdownConverter;
        $converter->allowLinkDocument = $allowDocumentLink;
        $l = $converter->transformToHtml($src);
        return $l;
    }
    /**
     * Tests mdconverter string.
     */
    public function test_mdconverter_string()
    {
        $src = '**bonjour * tout * le monde `est` present **';
        $d = $this->_transform($src);
        $this->assertEquals('<b>bonjour <i> tout </i> le monde <code>est</code> present </b>', $d);
    }
    /**
     * test two table
     * @return void 
     * @throws Exception 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_mdconverter_two_tables()
    {
        $src = implode("\n", [
            'Name of data| Description Node',
            '- | -',
            'igkdev | www.igkdev.com',
            'jour | **`null`**',
            '',
            'Sample |',
            '-|',
            'info |'
        ]);
        $d = $this->_transform($src);
        $this->assertEquals(
            '<table class="igk-table"><tr><th>Name of data</th><th>Description Node</th></tr><tr><td>igkdev</td><td>www.igkdev.com</td></tr><tr><td>jour</td><td><b><code>null</code></b></td></tr></table>' .
                '<table class="igk-table"><tr><th>Sample</th></tr><tr><td>info</td></tr></table>',
            $d
        );
    }
    /**
     * Tests mdconverter emoji.
     */
    public function test_mdconverter_emoji()
    {
        $src = implode("\n", [
            '    emoji :cup: data',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('emoji ☕️ data', $d);
    }
    /**
     * Tests mdconverter headers.
     */
    public function test_mdconverter_headers()
    {
        $src = implode("\n", [
            "# Title",
            "## h2",
            "### h3",
            "#### h4",
            "##### h5",
            "###### h6"
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<h1>Title</h1><h2>h2</h2><h3>h3</h3><h4>h4</h4><h5>h5</h5><h6>h6</h6>', $d);
    }
    /**
     * Tests mdconverter image.
     */
    public function test_mdconverter_image()
    {
        $src = implode("\n", [
            '![favicon igkdev.com](https://igkdev.com/favicon.ico)',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<img alt="favicon igkdev.com" src="https://igkdev.com/favicon.ico"/>', $d);
    }
    /**
     * Tests mdconverter at import.
     */
    public function test_mdconverter_at_import()
    {
        $src = '@igkdev is the best';
        $d = $this->_transform($src);
        $this->assertEquals('<a href="/@igkdev"><span class="mention">@igkdev</span></a> is the best', $d);
    }
    /**
     * Tests mdconverter order.
     */
    public function test_mdconverter_order()
    {
        $n = igk_create_notagnode();
        $n->markdown(implode("\n", [
            "# Hello sample",
            "- printing demonstration",
            "- left",
            "```sh",
            "# shel code ",
            "```",
            "info"
        ]));
        $s = $n->render();
        $this->assertEquals(
            '<div class="md-doc"><h1>Hello sample</h1><ul class="list"><li>printing demonstration</li><li>left</li></ul><code class="igk-code code-sh"># shel code</code><p>info</p></div>',
            $s,
            'missing order definition'
        );
    }
    /**
     * Tests mdconverter escaped.
     */
    public function test_mdconverter_escaped()
    {
        $src = implode("\n", [
            '1. Info \`data\`',
            '2. : 🎂',
            '3. : 🥤'
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<ol><li>Info `data`</li><li>: 🎂</li><li>: 🥤</li></ol>', $d);
    }
    /**
     * Tests mdconverter ordered.
     */
    public function test_mdconverter_ordered()
    {
        $src = implode("\n", [
            '1. a',
            '1. b',
            '',
            '1. Orange',
            '2. ```Mangoes```'
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<ol><li>a</li><li>b</li></ol><ol><li>Orange</li><li><code>Mangoes</code></li></ol>', $d);
    }
    /**
     * Tests mdconverter task.
     */
    public function test_mdconverter_task()
    {
        $src = implode("\n", [
            '- [ ] sample task',
            '- [x] sample complete task',
            '- [-] sample in progress complete task',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<ul class="igk-task-list"><li class="type-start">sample task</li><li class="type-complete">sample complete task</li><li class="type-progress">sample in progress complete task</li></ul>', $d);
    }
    /**
     * Tests mdconverter code.
     */
    public function test_mdconverter_code()
    {
        $src = implode("\n", [
            '```json',
            '{"basic":"information",',
            '     info: "sample"}',
            '```',
            '# end code'
        ]);
        $d = $this->_transform($src);
        $this->assertEquals(implode("\n", [
            '<code class="igk-code code-json">{"basic":"information",',
            '     info: "sample"}</code><h1>end code</h1>'
        ]), $d);
    }
    /**
     * Tests mdconverter multi expression code.
     */
    public function test_mdconverter_multi_expression_code()
    {
        $src = implode("\n", [
            '#### Views options',
            '',
            'passing parameters to layout',
            '',
            '```php',
            '//#{{% expression %}}',
            '```',
            '',
            '',
            '##### default expression',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals(implode("\n", [
            '<h4>Views options</h4><p>passing parameters to layout</p><code class="igk-code code-php">//#{{% expression %}}</code><h5>default expression</h5>'
        ]), $d);
    }
    /**
     * Tests mdconverter leave md.
     */
    public function test_mdconverter_leave_md()
    {
        // + | --------------------------------------------------------------------
        // + | remove empty line and remove non used data
        // + |
        $src = implode("\n", [
            '',
            '',
            '#',
            '',
            '## Docker - ',
            '',
            '```',
            'docker compose down',
            '```',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals(implode("\n", [
            '<h2>Docker - </h2><code class="igk-code">docker compose down</code>'
        ]), $d, 'ignore starting empty line ');
    }
    /**
     * Tests mdconverter document link.
     */
    public function test_mdconverter_document_link()
    {
        $src = implode("\n", [
            '# document',
            '- [link](#sample)',
            '## sample',
            'writing sample'
        ]);
        $d = $this->_transform($src, true);
        $this->assertEquals(implode("\n", [
            '<h1 id="document">document</h1><ul class="list"><li><a href="#sample">link</a></li></ul><h2 id="sample">sample</h2><p>writing sample</p>'
        ]), $d);
    }
    /**
     * Tests mdconverter hr.
     */
    public function test_mdconverter_hr()
    {
        $src = implode("\n", [
            '# document',
            '---',
            'writing sample'
        ]);
        $d = $this->_transform($src, true);
        $this->assertEquals(implode("\n", [
            '<h1 id="document">document</h1><hr class="hrule"/><p>writing sample</p>',
        ]), $d);
    }
    /**
     * Tests mdconverter litteral.
     */
    public function test_mdconverter_litteral()
    {
        $d = $this->_transform('info < et >', false);
        $this->assertEquals('info &lt; et &gt;', $d);
    }
    /**
     * auto generate doc.
     * @return void
     */
    public function test_mdconverter_lines()
    {
        $d = $this->_transform(implode("\n", [
            "`sample` : line1  ",
            "line2 ",
            <<<EOF
## the-code
data la sample 
EOF
        ]), false);
        $this->assertEquals(
            '<p><code>sample</code> : line1 <br/>line2 </p><h2>the-code</h2><p>data la sample </p>',
            $d
        );
    }
    /**
     * Tests mdconverter load def resource.
     */
    public function test_mdconverter_load_def_resource()
    {
        $d = $this->_transform(implode("\n", [
            "[click](#click-me)  ",
            "# intro {#click-me} ",
        ]), true);
        $this->assertEquals('<p><a href="#click-me">click</a> </p><h1 id="click-me">intro </h1>', $d);
    }
    /**
     * Tests mdconverter chain state.
     */
    public function test_mdconverter_chain_state()
    {
        $d = $this->_transform(implode("\n", [
            "```php",
            "\$x = 4;",
            "```",
            "",
            "**Cas d'usage :**",
            "martyr"
        ]), true);
        $this->assertEquals("<code class=\"igk-code code-php\">\$x = 4;</code><p><b>Cas d'usage :</b><br/>martyr</p>", $d);
    }
    /**
     * Tests mdconverter load array.
     */
    public function test_mdconverter_load_array()
    {
        $src = implode("\n", [
            '| a | b |',
            '|------------|-----------|',
            '| `.xsm` | Écran < 576px |',
        ]);
        $d = $this->_transform($src, true);
        $this->assertEquals('<table class="igk-table"><tr><th>a</th><th>b</th></tr><tr><td><code>.xsm</code></td><td>Écran &lt; 576px</td></tr></table>', $d);
    }
    /**
     * Tests mdconverter inline code with html entities.
     */
    public function test_mdconverter_inline_code_with_html_entities()
    {
        $src = implode("\n", [
            'the heredoc `<<<EOR ... EOR`',
        ]);
        $d = $this->_transform($src, true);
        $this->assertEquals('the heredoc <code>&lt;&lt;&lt;EOR ... EOR</code>', $d);
    }

    /**
     * Tests mdconverter node multiple.
     */
    public function test_mdconverter_node_multiple()
    {
        $n = igk_create_notagnode();
        $n->markdown(implode("\n", [
            "info case ",
            "- printing **demonstration** base",
            "marker"
        ]));
        $s = $n->render();
        $this->assertEquals(
            '<div class="md-doc"><p>info case </p><ul class="list"><li>printing <b>demonstration</b> base</li></ul><p>marker</p></div>',
            $s,
            'merging definition',
        );
    }

    /**
     * Tests mdconverter node multiple after header.
     */
    public function test_mdconverter_node_multiple_after_header()
    {
        $n = igk_create_notagnode();
        $n->markdown(implode("\n", [
            "info case ",
            "## title",
            "marker"
        ]));
        $s = $n->render();
        $this->assertEquals(
            '<div class="md-doc"><p>info case </p><h2>title</h2><p>marker</p></div>',
            $s,
            'merging definition',
        );
    }

    /**
     * Tests mdconverter node mixed.
     */
    public function test_mdconverter_node_mixed()
    {
        $n = igk_create_notagnode();
        $n->markdown(implode("\n", [
            "a ",
            "- b ",
            "c ",
            "# d",
            "m"
        ]));
        $s = $n->render();
        $this->assertEquals(
            '<div class="md-doc"><p>a </p><ul class="list"><li>b </li></ul><p>c </p><h1>d</h1><p>m</p></div>',
            $s,
            'merging definition',
        );
    }

    /**
     * Tests mdconverter line feed.
     */
    public function test_mdconverter_line_feed()
    {
        $n = igk_create_notagnode();
        $n->markdown(implode("\n", [
            "** b ** info",
            "du jour ",
            "- b ",
        ]));
        $s = $n->render();
        $this->assertEquals(
            '<div class="md-doc"><p><b> b </b> info<br/>du jour </p><ul class="list"><li>b </li></ul></div>',
            $s,
            'line feed ' . __METHOD__,
        );
    }

    /**
     * Tests mdconverter quote marker.
     */
    public function test_mdconverter_quote_marker()
    {
        $n = igk_create_notagnode();
        $n->markdown(implode("\n", [
            "> this is a",
            "> quote ",
            "x ",
        ]));
        $s = $n->render();
        $this->assertEquals(
            '<div class="md-doc"><blockquote>this is a<br/>quote </blockquote><p>x </p></div>',
            $s,
            'quote marker ' . __METHOD__,
        );
    }

    /**
     * Tests mdconverter subitem.
     */
    public function test_mdconverter_subitem()
    {
        $g = MarkdownConverter::TreatMarkdownSubItem("        - info");
        $this->assertEquals(
            json_encode(["depth" => 2, "value" => "info"]),
            json_encode($g)
        );
        $g = MarkdownConverter::TreatMarkdownSubItem("       - info");
        $this->assertEquals(
            json_encode(["depth" => 0, "value" => "info"]),
            json_encode($g)
        );
        $g = MarkdownConverter::TreatMarkdownSubItem("\t\t\t- info");
        $this->assertEquals(
            json_encode(["depth" => 3, "value" => "info"]),
            json_encode($g)
        );
    }

    /**
     * auto generate doc.
     * @return
     */
    public function test_mdconverter_array_escaped()
    {
        $n = igk_create_notagnode();
        $ts = implode("\n", [
            "a|b",
            "`c \|d` | quote ",
        ]);
        $conv = new MarkdownConverter;
        $l = $conv->transformToHtml($ts);

        $n->markdown($ts);

        $this->assertEquals(
            '<div class="md-doc"><table class="igk-table"><tr><th>a</th><th>b</th></tr><tr><td><code>c \|d</code></td><td>quote</td></tr></table></div>',
            $n->render()
        );
    }

    /**
     * 
     * @return void 
     */
    public function test_mdconverter_split_line_escaped()
    {
        $this->assertEquals(
            'hello <br/> util',
            $this->_mdconvert_tohtml('hello \\\\n util'),
            'not splitted escaped'
        );
    }

    public function test_mdconverter_table_with_no_header()
    {
        $this->assertEquals(
            '<table class="igk-table"><tr><td>name</td><td>version</td></tr></table>',
            $this->_mdconvert_tohtml(implode("\n", ['-|-', 'name|version'])),
            'not splitted escaped'
        );
    }
    public function test_mdconverter_sub_quoted_list()
    {
        $this->assertEquals(
            '<ul class="list"><li>list<blockquote class="subquote-0">sub element 1<br/>sub element 2</blockquote></li></ul><p>ok</p>',
            $this->_mdconvert_tohtml(implode("\n", [
                '- list',
                "\t> sub element 1",
                "\t> sub element 2",
                '',
                'ok'
            ])),
            'sub not defined'
        );
    }
    public function test_mdconverter_sub_quoted_list2()
    {
        $this->assertEquals(
            '<ul class="list"><li>list<blockquote class="subquote-0">sub element 1</blockquote></li><li>list 2<blockquote class="subquote-0">sub element 2</blockquote></li></ul>',
            $this->_mdconvert_tohtml(implode("\n", [
                '- list',
                "\t> sub element 1",
                "- list 2",
                "\t> sub element 2",
                '',
            ])),
            'sub not defined'
        );
    }
     public function test_mdconverter_sub_quoted_no_parent()
    {
        $this->assertEquals(
            '<blockquote class="subquote-0">sub element 1</blockquote><p>list 2</p>',
            $this->_mdconvert_tohtml(implode("\n", [ 
                "\t> sub element 1",
                "list 2",  
            ])),
            'sub not '
        );
    }
 public function test_mdconverter_sub_quoted_combine()
    {
        $this->assertEquals(
            '<blockquote class="subquote-0">one<blockquote class="subquote-1">two</blockquote>three</blockquote>',
            $this->_mdconvert_tohtml(implode("\n", [ 
                "\t> one",
                "\t\t> two",
                "\t> three",  
            ])),
            'sub not '
        );
    }


    public function test_mdconverter_list_sub_combine()
    {
        $this->assertEquals(
            '<ul class="list"><li>one<ul class="sublist-0"><li>two</li></ul></li><li>three</li></ul>',
            $this->_mdconvert_tohtml(implode("\n", [ 
                "- one",
                "\t- two",
                "- three",  
            ])),
            'sub not '
        );
    }

 public function test_mdconverter_with_sub_only()
    {
        $this->assertEquals(
            '<blockquote class="subquote-0">A</blockquote><p>B </p><ul class="sublist-0"><li><b>C</b></li><li><b>D</b></li></ul>',
            $this->_mdconvert_tohtml(implode("\n", [  
<<<EOF

    > A
B 
    - **C**
    - **D**
-|- 
EOF
            ])),
            'sub not '
        );
    }


    /**
     * just convert to html 
     * @param string $src 
     * @return string 
     */
    private function _mdconvert_tohtml(string $src)
    {
        $conv = new MarkdownConverter; 
        $l = $conv->transformToHtml($src);
        return $l;
    }
}
