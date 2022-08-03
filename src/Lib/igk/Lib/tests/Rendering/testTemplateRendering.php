<?php
// @author: C.A.D. BONDJE DOUE
// @filename: testTemplateRendering.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\System\Html\HtmlReader;
use IGK\Tests\BaseTestCase;

class testTemplateRendering extends BaseTestCase{
    public function test_loadingfor(){
        $src = <<<MSG_EOF
<div *for="\$raw->data->info">
    info
</div>
MSG_EOF;

$g = HtmlReader::Load($src);
echo $g->render();

        $this->expectOutputString("<div>ddd</div>");
    }
}