<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLoopTest.php
// @date: 20221109 11:10:47
// @desc: 
// @phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Html/Dom/HtmlLoopTest.php
namespace IGK\Tests\System\Html\Dom;

use IGK\System\Html\Dom\HtmlNode;
use IGK\Tests\BaseTestCase;

class HtmlLoopTest extends BaseTestCase{

    public function test_loop_class(){
        $t = new HtmlNode("div");
        $t->div()->loop(3)->div()->host(function($a){
            $a["*class"] = json_encode([
                "presentation"=>true,
                "info"=>false
            ]);
            $a->Content = " welcome {{ \$raw }} ";
        });
        $this->assertEquals('<div><div><div class="presentation"> welcome 0 </div><div class="presentation"> welcome 1 </div><div class="presentation"> welcome 2 </div></div></div>', 
        $t->render(), 
    );
    }

    public function test_loop_class2(){
        $t = new HtmlNode("div");
            $t->div()->loop(3)->div()->host(function($a){
                $a["*class"] = json_encode(['$raw==1 ?"item-2":null', '$raw==2 ?"item-3": null']);
                $a->Content = " welcome {{ \$raw }} ";
            });

            $this->assertEquals(
                '<div><div><div class=""> welcome 0 </div><div class="item-2"> welcome 1 </div><div class="item-3"> welcome 2 </div></div></div>', 
                $t->render(), 
            );
    }

    public function test_loop_class_href(){
        $t = new HtmlNode("div");
            $t->div()->loop(3)->div()->host(function($a){
                $a["*class"] = json_encode(['$raw==1 ?"item-2":null', '$raw==2 ?"item-3": null']);
                $a->a('#')->setAttribute("*href", '$raw')->Content = "data";
                $a->Content = " welcome {{ \$raw }} ";
            });

            $this->assertEquals(
                '<div><div><div class=""> welcome 0 <a href="0">data</a></div><div class="item-2"> welcome 1 <a href="1">data</a></div><div class="item-3"> welcome 2 <a href="2">data</a></div></div></div>', 
                $t->render(), 
            );
    }

    public function test_loop_class_key(){
        $t = new HtmlNode("div");
            $t->div()->loop(3)->div()->host(function($a){
                //$a["*class"] = json_encode(['$raw==1 ?"item-2":null', '$raw==2 ?"item-3": null']);
                $a->a('#')->setAttribute("*href", '$raw')->Content = "data";
                $a->Content = " welcome {{ \$raw }} - {{ \$index }}";
            });

            $this->assertEquals(
                '<div><div><div> welcome 0 - 0<a href="0">data</a></div><div> welcome 1 - 1<a href="1">data</a></div><div> welcome 2 - 2<a href="2">data</a></div></div></div>', 
                $t->render(), 
            );
    }
}
