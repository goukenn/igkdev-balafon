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
    
    public function test_binding_attribute_expression_in_loop(){
        $s = '<a *title="\'data\'" *for="$raw">data : {{ $raw }} - {{ $ctrl->getName() }} </a>';
        $n = igk_create_notagnode();
        $n->load($s, (object)[
            "Context"=>HtmlContext::Html,            
            "raw"=>range(1,2),
            "ctrl"=>DummyController::ctrl() 
        ]);

        $this->assertEquals(
            "<a title=\"data\">data :1 -::test-dummy</a><a title=\"data\">data :2 -::test-dummy</a>",
            $n->render(),
            "attribute bind"
        );
    }

    public function test_binding_attribute_expression(){
        $s = '<a *title="\'data\'"><igk:attr-expression *igk:uri="$ctrl->getAppUri(\'dashboard/edit_picture.form/\'.$raw->clId)" /></a>';
        // passing in loop context 
        $s = '<a *title="\'data\'" *for="$raw"><igk:attr-expression *igk:uri="$ctrl->getName()" /></a>';

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

        $this->assertEquals(
            "<a title=\"data\" igk:uri=\"test://dashboard/edit_picture.form/-1\">value</a>",
            $n->render(),
            "attribute bind"
        );
    }


}



class DummyController extends BaseController{
    public function getName(){
        return "::test-dummy";
    }
    public function getAppUri($s=null){        
        return "test://".$s;
    }
}