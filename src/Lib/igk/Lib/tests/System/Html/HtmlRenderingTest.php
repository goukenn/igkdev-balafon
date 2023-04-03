<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlRenderingTest.php
// @date: 20221114 12:33:49
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Html/HtmlRenderingTest.php 
namespace IGK\Tests\System\Html;

use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html
*/
class HtmlRenderingTest extends BaseTestCase{
    public function test_copy_node(){
        $g = igk_create_node('div');
        $parent = igk_create_node('div');
        $parent->li()->Content = '1';
        $parent->li()->Content = '2'; 
        $c = HtmlUtils::CopyNode($g, $parent->getChilds()->to_array());

        $this->assertEquals(
            '<div><li>1</li><li>2</li></div>',
            $g->render()
        );

    }
    public function test_copy_node_2(){
        $g = igk_create_node('div');
        $parent = igk_create_node('div');
        $parent->li()->b()->Content = '1';
        $parent->li()->Content = '2'; 
        $c = HtmlUtils::CopyNode($g, $parent->getChilds()->to_array());
        $this->assertEquals(
            '<div><li><b>1</b></li><li>2</li></div>',
            $g->render()
        ); 
    }
    public function test_copy_config(){
        $g = igk_create_node('configs');
        $parent = igk_create_node('data');
        $parent->domain()->Content = 'test.local';
        $parent->add('dev.ops')->domain()->Content = 'ops.local.domain';  
        $c = HtmlUtils::CopyNode($g, $parent->getChilds()->to_array());
        $this->assertEquals(
            '<configs><domain>test.local</domain><dev.ops><domain>ops.local.domain</domain></dev.ops></configs>',
            $g->render()
        ); 
    }
    public function test_render_litteral(){
        $g = igk_create_node("div");
        $g->setAttribute('prop','"info"."data"'); 
        $this->assertEquals(
            '<div prop="&quot;info&quot;.&quot;data&quot;"></div>',
            $g->render()
        );
    }
    public function test_load_text_area(){
        $g = igk_create_node("div");
        $g->Content = "<div><textarea>if (i<data){console.log('info');}</textarea></div>";
 
        $this->assertEquals(
            "<div><div><textarea>if (i<data){console.log('info');}</textarea></div></div>",
            $g->render()
        );
    }
}