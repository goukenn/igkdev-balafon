<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlRenderingLoopTest.php
// @date: 20230307 21:09:52
namespace IGK\Tests;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests
*/
class HtmlRenderingLoopTest extends BaseTestCase{
    /**
    * Tests htmlrender activate.
    */
    public function test_htmlrender_activate(){
        $src = implode("\n", [ 
            '<Column',
            'clIsUnique ',
            'clAutoIncrement ',
            'clIsPrimary',
            '></Column>'
        ]);
        $d = igk_create_node('p');
        $d->load($src); 
        $this->assertEquals(
            '<p><Column clAutoIncrement="true" clIsPrimary="true" clIsUnique="true"></Column></p>',
            $d->render()
        );
    }
    /**
    * Tests loop on range.
    */
    public function test_loop_on_range(){
        $n = igk_create_node('div');
        $n->ul()->loop(range(1,3))->host(function($a, $i){
            $a->li()->Content = $i;
        });
        $this->assertEquals(
            "<div><ul><li>1</li><li>2</li><li>3</li></ul></div>",
            $n->render(),
            "loop on range failed");
    }
    /**
    * Tests loop on assoc array.
    */
    public function test_loop_on_assoc_array(){
        $n = igk_create_node('div');
        $n->ul()->loop([
            "item1"=>"One",
            "item2"=>"Two",
            "item3"=>"Three"
        ])->host(function($a, $i){
            $a->li()->Content = $i;
        });
        $this->assertEquals(
            "<div><ul><li>One</li><li>Two</li><li>Three</li></ul></div>",
            $n->render(),
            "loop on range failed");
    }
}