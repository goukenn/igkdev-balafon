<?php
// @author: C.A.D. BONDJE DOUE
// @file: NodeArgBuilderTest.php
// @date: 20260916 14:36:41
namespace IGK\Tests\System\Html;

use IGK\Tests\BaseTestCase;
use function igk_html_host as _h;

/**
 * 
 * @package IGK\Tests\System\Html
 * @author C.A.D. BONDJE DOUE
 */
class NodeArgBuilderTest extends BaseTestCase
{
    public function test_nodeargs_loop()
    {
        $m = _h("div.card", _h('@loop', [range(0, 2), function ($t, $r) {
            $t->span()->content = $r;
        }]));
        $this->expectOutputString('<div class="card"><span>0</span><span>1</span><span>2</span></div>');
        $m->renderAJX();
    }
    public function test_nodeargs_loadclass_with_number()
    {
        $t = _h('div.info.pad-width-2.5em.card', 'hello');
        $this->assertEquals('<div class="info pad-width-2.5em card">hello</div>', $t->render());
    }
    public function test_nodeargs_supporte_attrib_tag()
    {
        $r = _h('div.a.fs-[23pt]]');
        $g = $r->render();
        $this->assertEquals('<div class="a fs-[23pt]"></div>', $g);
    }
    public function test_nodeargs_dot_in_node()
    {
        $t = igk_create_node('div');
        $t->setClass('info .pad-width-2.5em card')->setContent('hello');
        $g = $t->render();
        $this->assertEquals('<div class="info pad-width-2.5em card">hello</div>', $g);
    }
    public function test_nodeargs_with_extra_node()
    {
        $t = _h('div.a:hover');
        $g = $t->render();
        $this->assertEquals('<div class="a:hover"></div>', $g);

        $t = _h('div.a.fs-22:hover');
        $g = $t->render();
        $this->assertEquals('<div class="a fs-22:hover"></div>', $g);
    }
    public function test_nodeargs_opacity()
    {
        $t = _h('div.text-red/80');
        $g = $t->render();
        $this->assertEquals('<div class="text-red/80"></div>', $g);
    }
    public function test_nodeargs_escaped_opacity()
    {
        $t = _h('div.text-red\/80');
        $g = $t->render();
        $this->assertEquals('<div class="text-red/80"></div>', $g);
    }
    public function test_nodeargs_hover_escaped_opacity()
    {
        $t = _h('div.dark:text-red/20:hover');
        $g = $t->render();
        $this->assertEquals('<div class="dark:text-red/20:hover"></div>', $g);
    }
     public function test_nodeargs_hover_escaped_opacity_2()
    {
        $t = _h('div.dark:fs-[20pt]:hover');
        $g = $t->render();
        $this->assertEquals('<div class="dark:fs-[20pt]:hover"></div>', $g);
    }
    public function test_nodeargs_dark_with_color()
    {
        $t = _h('div.dark:bg-[#040816]');
        $g = $t->render();
        $this->assertEquals('<div class="dark:bg-[#040816]"></div>', $g);
    }
}
