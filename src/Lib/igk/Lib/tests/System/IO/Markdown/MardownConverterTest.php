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
 * 
 * @package IGK\Tests\System\IO\Markdown
 * @author C.A.D. BONDJE DOUE
 */
class MardownConverterTest extends BaseTestCase
{
    // public static function suite(){
    //     return new TestSuite(static::class);//  'markdown';
    // }
    private function _transform(string $src, $allowDocumentLink=false)
    {
        $converter = new MarkdownConverter;
        $converter->allowLinkDocument = $allowDocumentLink;
        $l = $converter->transformToHtml($src);
        return $l;
    }
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
            '<table class="igk-table"><tr><th>Name of data</th><th>Description Node</th></tr><tr><td>igkdev</td><td>www.igkdev.com</td></tr><tr><td>jour</td><td><b><code>null</code></b></td></tr></table>'
            .'<table class="igk-table"><tr><th>Sample</th><th></th></tr><tr><td>info</td><td></td></tr></table>'
            , $d
        );
    }
    public function test_mdconverter_emoji()
    {
        $src = implode("\n", [
            '    emoji :cup: data',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('emoji ☕️ data', $d);
    }
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
    public function test_mdconverter_image()
    {
        $src = implode("\n", [
            '![favicon igkdev.com](https://igkdev.com/favicon.ico)',
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<img alt="favicon igkdev.com" src="https://igkdev.com/favicon.ico"/>', $d);
    }
    public function test_mdconverter_at_import()
    {
        $src = '@igkdev is the best';
        $d = $this->_transform($src);
        $this->assertEquals('<a href="/@igkdev"><span class="mention">@igkdev</span></a><p> is the best</p>', $d);
    }
    public function test_mdconverter_escaped()
    {
        // escaped
        $src = implode("\n", [
            '1. Info \`data\`',
            '2. : 🎂',
            '3. : 🥤'
        ]);
        $d = $this->_transform($src);
        $this->assertEquals('<ol><li class="i">Info `data`</li><li class="i">: 🎂</li><li class="i">: 🥤</li></ol>', $d);
    }
    public function test_mdconverter_ordered()
    {
        // ordered
        $src = implode("\n", [
            '1. Info data',
            '1. Info info',
            // '',
            '',
            '1. Orange',
            '2. ```Mangoes```'
        ]);

        $d = $this->_transform($src);
        $this->assertEquals('<ol><li class="i">Info data</li><li class="i">Info info</li></ol><ol><li class="i">Orange</li><li class="i"><code>Mangoes</code></li></ol>', $d);
    }
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
            '<h4>Views options</h4>passing parameters to layout <code class="igk-code code-php">//#{{% expression %}}</code><h5>default expression</h5>'
        ]), $d);
    }
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
        ]), $d);
    }

    public function test_mdconverter_document_link(){
        $src = implode("\n", [
            '# document',
            '- [link](#sample)',
            '## sample',
            'writing sample'
        ]);
        $d = $this->_transform($src, true);
        $this->assertEquals(implode("\n", [
            '<h1 id="document">document</h1><ul class="list"><li class="i"><a href="#sample">link</a></li></ul><h2 id="sample">sample</h2><p>writing sample</p>'
        ]), $d);
    }

    public function test_mdconverter_hr(){
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
}
