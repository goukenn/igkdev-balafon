<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLoopTest.php
// @date: 20221109 11:10:47
// @desc: 
// @phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Html/Dom/HtmlLoopTest.php
namespace IGK\Tests\System\Html\Dom;
use IGK\System\Html\Dom\HtmlNode;
use IGK\Tests\BaseTestCase;

/**
* Html loop test.
* @package IGK\Tests\System\Html\Dom
*/
class HtmlLoopTest extends BaseTestCase
{
    /**
    * Tests loop class attribute.
    */
    public function test_loop_class_attribute()
    {
        $t = new HtmlNode("div");
        $t->div()->loop(3)->div()->host(function ($a) {
            $a["*class"] = [
                "presentation" => true,
            ];
            $a->Content = " welcome {{ \$raw }} ";
        });
        $b = $t->render();
        $this->assertEquals(
            '<div><div><div class="presentation"> welcome 0 </div></div><div><div class="presentation"> welcome 1 </div></div><div><div class="presentation"> welcome 2 </div></div></div>',
            $b,
        );
    }
    /**
    * Tests loop class1.
    */
    public function test_loop_class1()
    {
        $t = new HtmlNode("div");
        $t['*class'] = ['$raw==1 ?"item-2":null'];
        $this->assertEquals(
            '<div *class="[&quot;$raw==1 ?&quot;item-2&quot;:null&quot;]"></div>',
            $t->render()
        );
    }
    /**
    * Tests loop class expression.
    */
    public function test_loop_class_expression()
    {
        $t = new HtmlNode("div");
        $t->div()->loop(3)->div()->host(function ($a) {
            $a["*class"] = "igk_css_litteral([\$raw==1 ? 'item-2':null, \$raw==2 ? 'item-3': null])";
            $a->Content = " welcome {{ \$raw }} ";
        });
        $s = $t->render();
        $this->assertEquals(
            '<div><div><div class=""> welcome 0 </div></div><div><div class="item-2"> welcome 1 </div></div><div><div class="item-3"> welcome 2 </div></div></div>',
            $s,
        );
    }
    /**
    * Tests loop class href.
    */
    public function test_loop_class_href()
    {
        $t = new HtmlNode("p");
        $t->div()->loop(3)->div()->host(function ($a) {
            $a["*class"] = "igk_css_litteral([\$raw==1 ?\"item-2\":null, \$raw==2 ? \"item-3\": null])";
            $lnk = $a->a('#')->setAttribute("*href", '$raw');
            $lnk->Content = "data";
            $a->Content = " welcome {{ \$raw }} ";
        });
        $options = ["PreserveAttribOrder" => false];
        $s = $t->render($options);
        $this->assertEquals(
            '<p><div><div class=""> welcome 0 <a href="0">data</a></div></div><div><div class="item-2"> welcome 1 <a href="1">data</a></div></div><div><div class="item-3"> welcome 2 <a href="2">data</a></div></div></p>',
            $s
        );
    }
    /**
    * Tests loop class key.
    */
    public function test_loop_class_key()
    {
        $t = new HtmlNode("div");
        $t->p()->loop(3)->div()->host(function ($a) {
            $a->a('#')->setAttribute("*href", '$raw')->Content = "data";
            $a->Content = " welcome {{ \$raw }} - {{ \$key }}";
        });
        $s = $t->render();
        $this->assertEquals(
            '<div><p><div> welcome 0 - 0<a href="0">data</a></div></p><p><div> welcome 1 - 1<a href="1">data</a></div></p><p><div> welcome 2 - 2<a href="2">data</a></div></p></div>',
            $s
        );
    }
    /**
    * Tests loop with class array off expression 1.
    */
    public function test_loop_with_class_array_off_expression_1()
    {
        $t = new HtmlNode("div");
        $t->div()->loop(1)->div()->host(function ($a) {
            $a["*class"] = ['$raw==1 ?"item-2":null'];
            $a->Content = "welcome";
        });
        $s = $t->render();
        $this->assertEquals(
            '<div><div><div class="">welcome</div></div></div>',
            $s,
        );
    }
    /**
     * writing with loop and host method 
     * @return void 
     */
    public function test_coredom_loop()
    {
        $n = igk_create_notagnode();
        $n->div()->loop([1, 2, 3])->li()->Content = 'hello world';
        $this->assertEquals('<div><li>hello world</li></div><div><li>hello world</li></div><div><li>hello world</li></div>', $n->render(), 
        'Looping entirely node failed');
        $n = igk_create_notagnode();
        $n->div()->loop([1, 2, 3])->host(function ($n, $i) {
            $n->li()->Content = sprintf('hello world %s', $i);
        });
        $this->assertEquals('<div><li>hello world 1</li><li>hello world 2</li><li>hello world 3</li></div>', $n->render(),
        'Loop host failed');
    }
}