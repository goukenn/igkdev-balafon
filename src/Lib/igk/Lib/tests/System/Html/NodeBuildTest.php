<?php
// @author: C.A.D. BONDJE DOUE
// @file: NodeBuildTest.php
// @date: 20240109 19:55:57
namespace IGK\Tests\System\Html;

use IGK\System\Html\HtmlNodeBuilder;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html
* @author C.A.D. BONDJE DOUE
*/
class NodeBuildTest extends BaseTestCase{
    
    public function test_list_build(){
        $n = igk_create_node();
        $builder = new HtmlNodeBuilder($n);
        $builder(['ul.menu'=>[
            [
                "li.item > a" =>[ 
                    "Dashboard",
                ]
            ],
            [
                "li.item > a" =>[
                    "Users",
                ]
            ],
        ]]);

        $this->assertEquals('<div><ul class="menu"><li class="item"><a href="#">Dashboard</a></li>'
        .'<li class="item"><a href="#">Users</a></li>'
        .'</ul></div>',$n->render());
    }
}