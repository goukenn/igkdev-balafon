<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArticleNoTagNodeTest.php
// @date: 20220824 18:23:46
// @desc: 
namespace IGK\Tests;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use PHPUnit\Framework\TestCase;

require_once(__DIR__.'/bak.pinc');
/**
* Article no tag node test.
* @package IGK\Tests
*/
class ArticleNoTagNodeTest extends BaseTestCase
{
    /**
    * Tests loop in template file.
    */
    public function test_loop_in_template_file()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $s = "";
        $template = <<<'HTML'
<li *for="$raw" >
    {{ $raw }}
</li>
HTML;
        $g = tempnam(sys_get_temp_dir(), "text-"); 
        igk_io_w2file($g, $template);
        $n = igk_create_node("notagnode");
        $data = [1, 2, 'test_loop_in_template_file'];
        $n->article($ctrl, $g, $data);
        @unlink($g);
        $s = $n->render();
        $this->assertEquals(
            "<li> 1 </li><li> 2 </li><li> test_loop_in_template_file </li>",
            $s,
            "Base"
        );
    }
    /**
    * Tests loop in content.
    */
    public function test_loop_in_content()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $s = "";
        $template = <<<'HTML'
<igk:notagnode *for="$raw"><li> {{ $raw }} </li></igk:notagnode>
HTML;
        $g = tempnam(sys_get_temp_dir(), "text-"); 
        igk_io_w2file($g, $template);
        $n = igk_create_node("notagnode");
        $data = [1, 2];
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext);
        unlink($g);
        $s = $n->render();
        $this->assertEquals(
            "<li> 1 </li><li> 2 </li>",
            $s,
            "Base"
        );
    }
    /**
    * Tests script template.
    */
    public function test_script_template()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $template = <<<'HTML'
        <igk:notagnode  *for="$raw" >
            <dt><script> {{ $raw->name }} </script> {{ $raw->type }} </dt>
        </igk:notagnode>
        HTML;
        $n = igk_create_node("notagnode");
        $data = [(object)[
            "name" => "font-level",
            "type" => "em",
            "class" => "ft-l-*",
            "description" => <<<'HTML'
<div>presentation \' et information et de jour ' </div>
HTML        ]];
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext);
        $s = $n->render();
        $this->assertEquals(
            '<dt><script>{{ $raw->name }}</script> em </dt>',
            $s
        );
    }
    /**
    * Tests script template 2.
    */
    public function test_script_template_2()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $template = <<<'HTML'
        <igk:notagnode  *for="$raw" >
            <dt><script> {{ $raw->name }} </script> {{ $raw->type }} </dt>
        </igk:notagnode>
        HTML;
        $n = igk_create_node("notagnode");
        $data = [
            (object)["type" => "em"],
            (object)["type" => "px"]
        ];
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext);
        $s = $n->render();
        $this->assertEquals(
            '<dt><script>{{ $raw->name }}</script> em </dt><dt><script>{{ $raw->name }}</script> px </dt>',
            $s
        );
    }


    public function test_script_template_3()
    {
        $n = igk_create_node("notagnode");
        $data = [
            (object)["type" => "em"],
            (object)["type" => "px"]
        ];
        $n->loop($data)->div()->Content = '{{ $raw->name }} {{ $raw->type }}';
        $s = $n->render();
        $this->assertEquals(
            '<div> em</div><div> px</div>',
            $s
        );
    }



    public function test_template()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $s = "--";
        // code is skipped in reading for component so it will not be render properl
        $template = <<<'HTML'
<igk:notagnode  *for="$raw" >
    <dt><code> {{ $raw->name }} </code> {{ $raw->type }} </dt>
</igk:notagnode>
HTML;
        $n = igk_create_node("notagnode");
        $data = [(object)[
            "name" => "font-level",
            "type" => "em",
            "class" => "ft-l-*",
            "description" => <<<'HTML'
<div>presentation \' et information et de jour ' </div>
HTML        ]];
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext);
        $s = $n->render();
        $this->assertEquals(
            '<dt><code> font-level </code> em </dt>',
            $s,
            sprintf("test %s failed", __FUNCTION__)
        );
    }
    /**
    * Tests loop with object expression.
    */
    public function test_loop_with_object_expression()
    {
        $template = <<<'HTML'
<ul><li *for="$raw['y']">item: {{$raw}}</li></ul>
HTML;
        $data = [
            'x' => [1, 2, 4],
            'y' => [11, 12, 14],
        ];
        $s = $this->_bind_article($template, $data);
        $this->assertEquals('<ul><li>item: 11</li><li>item: 12</li><li>item: 14</li></ul>', $s);
    }
    /**
    * auto generate doc.
    * @param mixed $template
    * @param mixed $data
    * @param null|BaseController $ctrl
    * @return mixed
    */
    private function _bind_article($template, $data, ?BaseController $ctrl = null)
    {
        $ctrl = $ctrl ?? SysDbController::ctrl();
        $n = igk_create_notagnode();
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext);
        return $n->render();
    }
}