<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CoreLoadingImgTest.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests;

use IGK\System\Html\Dom\HtmlNode;
use IGK\Tests\BaseTestCase;

class CoreLoadingImgTest extends BaseTestCase{
    public function test_load_img(){

        //image tag is a special tag 
        $s = "<div><img src=\"sample\"/><span>data</span></div>";
        $f = new HtmlNode("div");
        $f->load(<<<EOF
<img src="sample"><span>data</span>
EOF
    );
        $this->assertEquals($s, 
            $f->render(),
            "load image"
        );
    }

    public function test_load_img_closed(){

        //image tag is a special tag 
        $s = "<div><img src=\"sample\"/><span>data</span></div>";
        $f = new HtmlNode("div");
        $f->load(<<<EOF
<img src="sample"></img><span>data</span>
EOF
    );
        $this->assertEquals($s, $f->render());
    }
}