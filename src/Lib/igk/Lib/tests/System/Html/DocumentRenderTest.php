<?php
namespace IGK\Test\System\Html;

use IGK\Tests\BaseTestCase;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGKHtmlRelativeUriValueAttribute;
use IGKResourceUriResolver;

class DocumentRenderTest extends BaseTestCase{

    function test_element_script_inner(){
        $s = new HtmlNode("data");

        $s->load(<<<EOF
<template lang="">
    <div>
        information introduction 
    </div>
</template>
<script>
export default {
}
</script>
<style lang="">
    alert("ok")
</style>
EOF
);
        $v = $s->getElementsByTagName("script")[0]; 
        $g = trim($v->getinnerHtml());
        $this->assertEquals("export default {\n}", $g);
    }
    function test_must_close_non_html_tag(){
        $c = new HtmlNode("router-view");
        $this->assertEquals("<router-view></router-view>",
            $c->render()
        );
    }
    // function test_render_indent_document(){
    //     $doc = new HtmlDocumentNode();
    //     $options = (object)[
    //         "Indent"=>true, 
    //     ];
    //     igk_setting()->no_page_cache = true;
    //     // $doc->getBody()->setIsVisible(false);
    //     $s = $doc->render($options);
    //     $this->assertEquals(
    //         "",
    //         $s,
    //         "Render document : failed"
    //     );
    // }


    function test_current_relative_uri(){
        igk_server()->REQUEST_URI = "/testapi-test/";
        $this->assertEquals(
            "../data",
            igk_io_currentrelativeuri("data"),
            "relative relative uri not matching 1"
        );

        igk_server()->REQUEST_URI = "/testapi-test/";
        $this->assertEquals(
            "../".ltrim(IGK_LIB_DIR."/test-loading.txt", "/"),
            igk_io_currentrelativeuri(IGK_LIB_DIR."/test-loading.txt"),
            "relative relative uri not matching 1"
        );
    }
    function test_php_remove_comment(){
        $src = "// data \n\$data = 0;";        
        $this->assertEquals(
            "\$data = 0;",
            ltrim(substr(PHPScriptBuilderUtility::RemoveComment("<?php\n".$src), 6)),
            "remove comment");
    }

    function test_resolv_path(){
        $g = IGKResourceUriResolver::getInstance(); 
        igk_server()->REQUEST_URI = "/testapi/";
        $this->assertEquals(
            "../data-info",
            (new IGKHtmlRelativeUriValueAttribute("/data-info"))->getValue(),
            "resolv path 1"); 

        // resolv with file exists
        $this->assertEquals(
            "../assets/_lib_/Scripts/igk.js",
            (new IGKHtmlRelativeUriValueAttribute(IGK_LIB_DIR."/Scripts/igk.js"))->getValue(),
            "resolv path 2");
        $f = tempnam(sys_get_temp_dir(), "test-");
        
        // resolv with non exists file
        $this->assertEquals(
            "../assets/_lib_/Scripts/".basename($f),
            (new IGKHtmlRelativeUriValueAttribute(IGK_LIB_DIR."/Scripts/".basename($f)))->getValue(),
            "resolv path 3"); 
        unlink($f);
    }

    function test_igk_io_currentrelativeuri(){
        $this->assertEquals(
            "./",
            igk_io_currentrelativeuri(),
            "data: relative path not match 1");
        // igk_server()->REQUEST_URI = "/Sample/";
        $this->assertEquals(
            "Configs",
            igk_io_currentrelativeuri("/Configs"),
            "data: relative path not match 2");
        // $this->assertEquals(
        //         "Configs",
        //         igk_io_currentrelativeuri("../Configs/Data"),
        //         "data: relative path not match 3");
    }
    function test_igk_html_get_system_uri(){

        $this->assertEquals(
            "./Configs",
            igk_html_get_system_uri("/Configs"),
            "data:");

        igk_server()->REQUEST_URI = "/test/test/";

        
        $this->assertEquals(
            igk_io_baseuri("/Configs"),
            igk_html_get_system_uri("/Configs", (object)["StandAlone"=>true, "Context"=>"XML"]),
            "data: fulle paht not matching");

    }

    function test_render_no_tagnode(){
        $doc = new HtmlNode("div");
        $doc->notagnode()->div()->Content = "Sample";
        $options = (object)[
            "Indent"=>true, 
        ];
        igk_setting()->no_page_cache = true; 
        $s = $doc->render($options);
        $this->assertEquals(
            <<<EOF
<div>
\t<div>Sample</div>
</div>
EOF,
            $s,
            "Render document : failed"
        );
    }


}