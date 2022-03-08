<?php
namespace IGK\Tests\System\Html{

use IGK\Tests\BaseTestCase;

class HtmlNodeBuildTest extends BaseTestCase{

    function test_args_value(){
        // + | Testing igk:param data : not rendered to client
        $n = igk_create_node("div"); 
        $n->load("<igk:input igk:args=\"'_id', 'password', 'sample'\" />");
        $this->assertEquals(
            '<div><input type="password" value="sample" name="_id" id="_id" class="clpassword"/></div>',
            $n->render(),
            "form rendering failed"
        );
    }

    function test_bind_expression(){
        $s = '<div *for="range(1,2)">info :{{ $raw }}</div>';
        $d = igk_create_node("div");       
        $d->load($s, (object)[
            "raw"=>(object)[],
            "ctrl"=>null
        ]);
        $this->assertEquals('<div><div>info :1</div><div>info :2</div></div>',
        $d->render());
    }

    function test_bind_expression_sub_tag(){
        $s = '<div *for="range(1,2)">info :<div>{{ $raw }}</div></div>';
        $d = igk_create_node("div");       
        $d->load($s, (object)[
            "raw"=>(object)[],
            "ctrl"=>null
        ]);
        $this->assertEquals('<div><div>info :<div>1</div></div><div>info :<div>2</div></div></div>',
        $d->render());
    }

    function test_passing_data_as_arg(){
        
        $s = '<igk:expression-node igk:args="[[:@raw]]" expression="{{ $raw->x + 88}}"></igk:expression-node>';
        $d = igk_create_node("div");       
        $d->load($s, (object)[
            "raw"=>(object)["x"=>8],
            "ctrl"=>null
        ]);
        $this->assertEquals('<div>96</div>',
        $d->render(),
        "passing data and operate failed"
        );
    }

    // function test_passing_same_call(){
 
    //     $s = '<igk:same igk:args="[[:@raw]]" expression="{{ $raw->x + 88}}"></igk:same>';
    //     $d = igk_create_node("div");       
    //     $d->load($s, (object)[
    //         "raw"=>(object)["x"=>8],
    //         "ctrl"=>null
    //     ]);
    //     $this->assertEquals('<div>96</div>',
    //     $d->render(),
    //     "passing data and operate failed"
    //     );
    // }
    function test_subitem_pass(){ 
        $s = '<div *for="range(1,3)"><li>{{$raw}}</li></div>';//<div *visible="$raw==2" id="mark"><igk:contact-block igk:args="[[:@raw]]"></igk:contact-block></div></div>';
        $d = igk_create_node("jump");       
        $d->load($s, (object)[
            "raw"=>(object)["x"=>8],
            "ctrl"=>null
        ]);
        $this->assertEquals('<jump><div><li>1</li></div><div><li>2</li></div><div><li>3</li></div></jump>',
        $d->render(),
        "sub item passing failed"
        );
    }
    // function test_contact_pass(){
 
    //     $s = '<div *for="range(1,3)"><igk:contact-block igk:args="[[:@raw]]"></igk:contact-block></div>';//<div *visible="$raw==2" id="mark"><igk:contact-block igk:args="[[:@raw]]"></igk:contact-block></div></div>';
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
    function igk_html_node_same($p){
        igk_wln_e("try create ..... ", $p);
    }
    if (!function_exists('igk_html_node_contact_block')){
        function igk_html_node_contact_block($p){
            igk_wln_e("try create ..... ".__FUNCTION__, $p);
        }
    }
}