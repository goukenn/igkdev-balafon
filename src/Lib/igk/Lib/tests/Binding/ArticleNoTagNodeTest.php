<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ArticleNoTagNodeTest.php
// @date: 20220824 18:23:46
// @desc: 

namespace IGK\Tests;

use PHPUnit\Framework\TestCase;


class ArticleNoTagNodeTest extends BaseTestCase
{
  
    public function test_loop_in_template_file()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $s = "";
        $template = <<<'HTML'
<igk:notagnode *for="$raw" >
     <li> {{ $raw }} </li>
</igk:notagnode>
HTML;
        $g = tempnam(sys_get_temp_dir(), "text-"); // tmpfile();
        igk_io_w2file($g, $template);
        $n = igk_create_node("notagnode");
        $data = [1, 2];
        $n->article($ctrl, $g, $data);

        // igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext = null);
        unlink($g);

        $s = $n->render();

        $this->assertEquals(
            "<li> 1 </li><li> 2 </li>",
            $s,
            "Base"
        );
    }
    
    public function test_loop_in_content()
    {
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $s = "";
        $template = <<<'HTML'
<igk:notagnode *for="$raw"><li> {{ $raw }} </li></igk:notagnode>
HTML;
        $g = tempnam(sys_get_temp_dir(), "text-"); // tmpfile();
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
    
    public function test_template(){
        $ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
        $s = "";
        // <igk:a igk:args="test" >Information</igk:a>
        $template = <<<'HTML'
<igk:notagnode  *for="$raw" >
    <dt><code> {{ $raw->name }} </code> {{ $raw->type }}  </dt>    
</igk:notagnode>
HTML;
        $g = tempnam(sys_get_temp_dir(), "text-"); // tmpfile();
        igk_io_w2file($g, $template);
        $n = igk_create_node("notagnode");
        $data = [(object)[
            "name"=>"font-level",
            "type"=>"em",
            "class"=>"ft-l-*",
            "description"=><<<'HTML'
<div>presentation \' et information et de jour ' </div>
HTML
        ]]; 
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        igk_html_bind_article_content($n, $template, $data, $ctrl, "test:d", true, $ldcontext);
        unlink($g);

        $s = $n->render();

        $this->assertEquals(
            '<dt><code> font-level </code> em </dt>',
            $s,
            "Base"
        );

    }


    public function test_render_indent(){
        $n = igk_create_node("sample");
        $n->notagnode()->div()->Content = "Hello";
        $this->assertEquals(<<<HTML
<sample>
\t<div>Hello</div>
</sample>
HTML,
 $n->render((object)["Indent"=>true]));
    }
   
}
