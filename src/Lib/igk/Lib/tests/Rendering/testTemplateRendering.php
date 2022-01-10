<?php

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

        $this->expectOutputString("<div></div>");
    }
}