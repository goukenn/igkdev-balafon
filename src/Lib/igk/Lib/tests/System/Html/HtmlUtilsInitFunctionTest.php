<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlUtilsInitFunctionTest.php
// @date: 20230311 07:15:03
namespace IGK\Tests\System\Html;

use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
 * 
 * @package IGK\Tests\System\Html
 */
class HtmlUtilsInitFunctionTest extends BaseTestCase
{
    public function test_render()
    {
        $this->assertEquals(
            "<div>hello!!!</div>",
            HtmlUtils::Init(igk_create_node('div'), 'hello!!!')
        );
    }
    public function test_render_with_array()
    {

        HtmlUtils::Init($n = igk_create_node('div'), [
            "h1" => "presentation"
        ]);
        $this->assertEquals(
            "<div><h1>presentation</h1></div>",
            $n->render()
        );
    }

    public function test_render_with_array_last()
    {
        // last item 
        $this->assertEquals(
            "<h1>presentation</h1>",
            HtmlUtils::Init(igk_create_node('div'), [
                "h1" => "presentation"
            ])->render()
        );
    }
    public function test_render_with_array_multi()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            ["@_t:h1" => "presentation1"],
            ["@_t:h1" => "presentation2"],

        ]);
        $this->assertEquals(
            "<div><h1>presentation1</h1><h1>presentation2</h1></div>",
            $n->render()
        );
    }
    public function test_render_class()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            ["@_t:h1" => ['_' => ['class' => 'basic'], "presentation1"]],
            ["@_t:h1" => "presentation2"],

        ]);
        $this->assertEquals(
            "<div><h1 class=\"basic\">presentation1</h1><h1>presentation2</h1></div>",
            $n->render()
        );
    }

    public function test_render_class_pseudo()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            ["@_t:h1" => ['_' => ['class' => ['basic' => true, 'litteral' => false]], "presentation1"]],
            ["@_t:h1" => "presentation2"],

        ]);
        $this->assertEquals(
            "<div><h1 class=\"basic\">presentation1</h1><h1>presentation2</h1></div>",
            $n->render()
        );
    }
    public function test_render_after_node()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            "span" => "after",
            ["@_t:h1" => "presentation1"],
            ["@_t:h1" => "presentation2"],
        ]);
        $this->assertEquals(
            "<div><span>after</span><h1>presentation1</h1><h1>presentation2</h1></div>",
            $n->render()
        );
    }
    public function test_render_form()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            "form" => [],
        ]);
        $this->assertEquals(
            '<div><form action="." class="igk-form" method="POST"><div class="content"></div></form></div>',
            $n->render()
        );
    }
    public function test_render_form_ajx()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            "form" => [
                "::ajx" => ""
            ],
        ]);
        $this->assertEquals(
            '<div><form action="." class="igk-form" igk-ajx-form="1" method="POST"><div class="content"></div></form></div>',
            $n->render()
        );
    }
    public function test_render_form_ajx_call_twice()
    {
        // last item 
        HtmlUtils::Init($n = igk_create_node('div'), [
            "form" => [
                ["::ajx" => ""],
                ["::ajx" => ""],
            ],
        ]);
        $this->assertEquals(
            '<div><form action="." class="igk-form" method="POST"><div class="content"><div><ajx></ajx></div><div><ajx></ajx></div></div></form></div>',
            $n->render()
        );
    }
    public function test_render_form_ajx_call_twice_2()
    {
        // last item 

        HtmlUtils::Init($n = igk_create_node('div'), [
            "form" => [
                ["::fn()" => "ajx"],
                ["::fn()" => ["ajx"]],
            ],
        ]);
        $this->assertEquals(
            '<div><form action="." class="igk-form" igk-ajx-form="1" method="POST"><div class="content"></div></form></div>',
            $n->render()
        );
    }
    public function test_render_form_ajx_call_twice_3()
    {
        // last item  
        HtmlUtils::Init($n = igk_create_node('div'), [
            "form" => [
                ["::fn()" => "ajx", "span" => "hello"],
                ["::fn()" => ["ajx"]],
            ],
        ]);
        $this->assertEquals(
            '<div><form action="." class="igk-form" igk-ajx-form="1" method="POST"><div class="content"><span>hello</span></div></form></div>',
            $n->render()
        );
    }
    public function test_render_loop()
    {
        // last item  
        HtmlUtils::Init(
            $n = igk_create_node('div'),
            [
                "loop" => ["@" => [range(1, 3)], "fn()" => function ($a, $i) {
                    $a->li()->Content = "OK" . $i;
                }], 'notagnode'
            ],
        );
        $this->assertEquals(
            '<div><li>OK1</li><li>OK2</li><li>OK3</li>notagnode</div>',
            $n->render()
        );
    }
}
