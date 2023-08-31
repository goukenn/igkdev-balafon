<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNodeBuildTest.php
// @date: 20220803 13:48:54
// @desc: 
// @test-command: phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Html/HtmlNodeBuildTest.php

namespace IGK\Tests\System\Html {

    use IGK\System\Html\HtmlNodeBuilder;
    use IGK\Tests\BaseTestCase;

    class HtmlNodeBuildTest extends BaseTestCase
    {
        public function test_subchain(){
            $n = igk_create_notagnode();
            $d = new HtmlNodeBuilder($n);
            $d(['a > b' => [               
                "c > d" => 'info', //['div'=>'presentation'],
                "e" => []
            ]], $n->addNode('template'));
            $this->assertEquals('<template><a href="#"><b><c><d>info</d></c><e></e></b></a></template>', $n->render());
        }
        public function test_subblock_ul(){
            $n = igk_create_notagnode();
            $d = new HtmlNodeBuilder($n);
            $n = igk_create_notagnode();
            $d = new HtmlNodeBuilder($n); 
            $d([ 
                'ul'=>[
                    'li.f'=>'Home', 
                    'li.a'=>'About',
                ]  
            ], $n->addNode('template')); 
            $this->assertEquals('<template><ul><li class="f">Home</li><li class="a">About</li></ul></template>', $n->render());
        }
        public function test_subblock_chain_property(){            
            $n = igk_create_notagnode();
            $d = new HtmlNodeBuilder($n); 
            $d([ 
                'ul'=>[
                    'li > a[to:/]'=>'Home', 
                    'li > b[to:/about]'=>'About',
                ]  
            ], $n->addNode('template')); 
            $this->assertEquals('<template><ul><li><a href="#" to="/">Home</a></li><li><b to="/about">About</b></li></ul></template>', $n->render());
        }
        public function test_subchain_2(){
            $n = igk_create_notagnode();
            $d = new HtmlNodeBuilder($n);
            $d(['a > b' => [ 
                "c > d" => ['div'=>'presentation'],
                "e" => []
            ]], $n->addNode('template'));
            $this->assertEquals('<template><a href="#"><b><c><d><div>presentation</div></d></c><e></e></b></a></template>', $n->render());
        }


        function test_args_value()
        {
            // + | Testing igk:param data : not rendered to client
            $n = igk_create_node("div");
            $n->load("<igk:input igk:args=\"'_id', 'password', 'sample'\" />");
            $this->assertEquals(
                '<div><input class="clpassword" id="_id" name="_id" type="password" value="sample"/></div>',
                $n->render(),
                "form rendering failed"
            );
        }

        function test_bind_expression()
        {
            $s = '<div *for="range(1,2)">info :{{ $raw }}</div>';
            $d = igk_create_node("div");
            $d->load($s, (object)[
                "raw" => (object)[],
                "ctrl" => null
            ]);
            $this->assertEquals(
                '<div><div>info :1</div><div>info :2</div></div>',
                $d->render()
            );
        }

        function test_bind_expression_sub_tag()
        {
            $s = '<div *for="range(1,2)">info :<div>{{ $raw }}</div></div>';
            $d = igk_create_node("div");
            $d->load($s, (object)[
                "raw" => (object)[],
                "ctrl" => null
            ]);
            $this->assertEquals(
                '<div><div>info :<div>1</div></div><div>info :<div>2</div></div></div>',
                $d->render()
            );
        }

        function test_passing_data_as_arg()
        {

            $s = '<igk:expression-node igk:args="[[:@raw]]" expression="{{ $raw->x + 88}}"></igk:expression-node>';
            $d = igk_create_node("div");
            $d->load($s, (object)[
                "raw" => (object)["x" => 8],
                "ctrl" => null
            ]);
            $this->assertEquals(
                '<div>96</div>',
                $d->render(),
                "passing data and operate failed"
            );
        }

        function test_subitem_pass()
        {
            $s = '<div *for="range(1,3)"><li>{{$raw}}</li></div>'; //<div *visible="$raw==2" id="mark"><igk:contact-block igk:args="[[:@raw]]"></igk:contact-block></div></div>';
            $d = igk_create_node("jump");
            $d->load($s, (object)[
                "raw" => (object)["x" => 8],
                "ctrl" => null
            ]);
            $this->assertEquals(
                '<jump><div><li>1</li></div><div><li>2</li></div><div><li>3</li></div></jump>',
                $d->render(),
                "sub item passing failed"
            );
        }

        function test_render_string_at_last()
        {
            $n = igk_create_node("div");
            $s = HtmlNodeBuilder::Init($n, [
                "div" => "Le titre du jour",
                ["@_t:div" => "Mon titre1"],
                ["@_t:div" => "Mon titre2"],
                "Nous voila dans le code"
            ]);
            $this->assertEquals(
                '<div><div>Le titre du jour</div><div>Mon titre1</div><div>Mon titre2</div>Nous voila dans le code</div>',
                $n->render()
            );
        }
        function test_render_method_as_entry_child_array()
        {
            // test array with index function attached 
            $n = igk_create_node("div");
            igk_is_debug(true);
            $s = HtmlNodeBuilder::Init($n, [
                "div" => [function ($i) {
                    $i->text("ok");
                }]
            ]);
            $this->assertEquals(
                '<div><div>ok</div></div>',
                $n->render()
            );
        }
        function test_render_method_as_entry_child_2()
        {
            // test direct function attached - to target node 
            $n = igk_create_node("div");
            HtmlNodeBuilder::Init($n, [
                "div" => function ($i) {
                    $i->text("ok");
                }
            ]);
            $this->assertEquals(
                '<div><div>ok</div></div>',
                $n->render()
            );
        }

        // function test_render_method_as_entry_child_2_callback()
        // {
        //     $n = igk_create_node("div");
        //     HtmlNodeBuilder::Init($n, [
        //         "div" => [
        //             function ($i) {
        //                 $i->text("ok1");
        //             },
        //             function ($i) {
        //                 $i->text("ok2");
        //             }
        //         ]
        //     ]);
        //     $this->assertEquals(
        //         '<div><div>ok1ok2</div></div>',
        //         $n->render()
        //     );
        // }

        function test_render_method_after_node_def()
        {
            $n = igk_create_node("div");
            HtmlNodeBuilder::Init($n, [
                "div" => [
                    "h2" => "presentation",
                    function ($i) {
                        $i->text("ok1");
                    }
                ]
            ]);
            $this->assertEquals(
                '<div><div><h2>presentation</h2>ok1</div></div>',
                $n->render()
            );
        }
        function test_render_method_as_entry()
        {
            $n = igk_create_node("div");

            HtmlNodeBuilder::Init($n, [
                function ($i) {
                    $i->text("ok");
                }
            ]);
            $this->assertEquals(
                '<div>ok</div>',
                $n->render()
            );
        }
        function test_render_chain()
        {
            $n = igk_create_node("div");

            HtmlNodeBuilder::Init($n, [
                "header" => [],
                "main" => [],
                "footer" => []
            ]);
            $this->assertEquals(
                '<div><header></header><main></main><footer></footer></div>',
                $n->render()
            );
        }
        function test_render_menus()
        {
            $n = igk_create_node("div");
            HtmlNodeBuilder::Init($n, [
                "igk:menus" => [
                    "@" => [
                        [
                            "home"
                        ]
                    ]
                ],
            ]);
            $this->assertEquals(
                '<div><ul class="igk-menu menu"><li><a href="home">Accueil</a></li></ul></div>',
                $n->render()
            );
        }
        function test_render_render_with_string()
        {
            $n = igk_create_node("div");
            HtmlNodeBuilder::Init($n, [
                [
                    "_" => ["class" => "igk-btn btn"],
                    "Login or Connect"
                ],
            ]);
            $this->assertEquals(
                '<div><div class="igk-btn btn">Login or Connect</div></div>',
                $n->render()
            );
        }
        // function test_contact_pass(){

        //     '<div *for="range(1,3)"><igk:contact-block igk:args="[[:@raw]]"></igk:contact-block></div>';//<div *visible="$raw==2" id="mark"><igk:contact-block igk:args="[[:@raw]]"></igk:contact-block></div></div>';
        //     $d = igk_create_node("jump");       
        //     $d->load($s, (object)[
        //         "raw"=>(object)["x"=>8],
        //         "ctrl"=>null
        //     ]);
        //     $this->assertEquals('<jump><div><li>1</li></div><div><li>2</li></div><div><li>3</li></div></jump>',
        //     $d->render(),
        //     "sub item passing failed"
        //     );
        // }
    
    
    }

    
}

namespace {
    function igk_html_node_same($p)
    {
        igk_wln_e("try create ..... ", $p);
    }
    if (!function_exists('igk_html_node_contact_block')) {
        function igk_html_node_contact_block($p)
        {
            igk_wln_e("try create ..... " . __FUNCTION__, $p);
        }
    }
}
