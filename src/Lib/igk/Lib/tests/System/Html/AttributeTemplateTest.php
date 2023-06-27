<?php

// @file: AttributeTemplateTest.php
// @author: C.A.D. BONDJE DOUE
// @description: Html attribute template register
// @copyright: igkdev © 2022
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Tests\System\Html;

use IGK\Controllers\BaseController;
use IGK\Controllers\NotRegistrableControllerBase;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;

class AttributeTemplateTest extends BaseTestCase{
    public function test_html_utils_get_value1(){
        
        
        $this->assertEquals(
            "'welcome'",
            HtmlUtils::GetAttributeValue("'welcome'", null, true)
        );
    }
    public function test_pipe_expression(){
      
        $s = '<a *title="\'data\'">value</a>';
        $n = igk_create_notagnode();
        $n->load($s);

        $this->assertEquals(
            "<a title=\"data\">value</a>",
            $n->render(),
            "pipe expression not bind properly"
        );
    }
    public function test_simple_loop(){
        $n = igk_create_notagnode();
        $n->div()->loop(3)->div()->Content = 'index : {{ $raw }}';
        $this->assertEquals(
            "<div><div>index : 0</div><div>index : 1</div><div>index : 2</div></div>",
            $n->render(),
            "attribute bind"
        );
    }   
    public function test_simple_loop_with_attribute(){
        $n = igk_create_notagnode();
        $n->div()->setAttribute('*for', '$raw')->Content = 'index : {{ $raw }}';
        $s = igk_create_notagnode();
        $s->load($n->render(), [
            'raw'=>range(0,2)
        ]);
        $this->assertEquals(
            "<div>index : 0</div><div>index : 1</div><div>index : 2</div>",
            $s->render(),
            "attribute bind"
        );
    }    
    public function test_binding_attribute_expression_in_loop(){
        $s = '<a *title="\'data\'" *for="$raw">data : {{ $raw }} - {{ $ctrl->getName() }} </a>';
        $n = igk_create_notagnode();
        $n->load($s, (object)[
            "Context"=>HtmlContext::Html,            
            "raw"=>range(1,2),
            "ctrl"=>DummyController::ctrl() 
        ]);

        $this->assertEquals(
            "<a title=\"data\">data : 1 - ::test-dummy </a><a title=\"data\">data : 2 - ::test-dummy </a>",
            $n->render(),
            "attribute bind"
        );
    }

    public function test_binding_attribute_expression(){
        // passing custom controller 
        // $s = '<a *title="\'data\'"><igk:attr-expression *igk:uri="$ctrl->getAppUri(\'dashboard/edit_picture.form/\'.$raw->clId)" /></a>';
        // passing in loop context 
        // $s = '<a *title="\'data\'" *for="$raw"><igk:attr-expression *igk:uri="$ctrl->getName()" /></a>';

        // not passing in loop check 
        $s = '<a *title="\'data\'" >value<igk:attr-expression *igk:uri="$ctrl->getAppUri(\'dashboard/edit_picture.form/\'.$raw->clId)" /></a>';
        $n = igk_create_notagnode();
        $n->load($s, (object)[
            "Context"=>HtmlContext::Html,            
            "raw"=>(object)[
                "clId"=>-1
            ],
            "ctrl"=>DummyController::ctrl() 
        ]);
        $ts = $n->render();
       
        $this->assertEquals(
            "<a igk:uri=\"test://dashboard/edit_picture.form/-1\" title=\"data\">value</a>",
            $ts,
            "attribute controller not binding",
        );
    }


}


/**
 * dummy controller 
 */
class DummyController extends NotRegistrableControllerBase{
    public function getName(){
        return "::test-dummy";
    }
    public function getAppUri(?string $s=null):?string{            
        return "test://".$s;
    }
}