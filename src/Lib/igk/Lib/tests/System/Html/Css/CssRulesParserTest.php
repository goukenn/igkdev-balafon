<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssRulesParserTest.php
// @date: 20250628 22:16:02
namespace IGK\Tests\System\Html\Css;
use IGK\System\Html\Css\CssRulesParser;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssRulesParserTest extends BaseTestCase
{
    /**
    * Tests cssruleparser test.
    */
    public function test_cssruleparser_test()
    {
        $tab = CssRulesParser::Parse("background-color:red;");
        $this->assertEquals([], $tab);
    }
    /**
    * Tests cssruleparser body.
    */
    public function test_cssruleparser_body()
    {
        $tab = CssRulesParser::Parse("body{background-color:red;}");
        $this->assertEquals(['body{background-color:red;}'], $tab);
    }
    /**
    * Tests cssruleparser body line.
    */
    public function test_cssruleparser_body_line()
    {
        $tab = CssRulesParser::Parse("body{   \n   background-color:red; \n  }");
        $this->assertEquals(['body{background-color:red;}'], $tab);
    }
    /**
    * Tests cssruleparser selector.
    */
    public function test_cssruleparser_selector()
    {
        $tab = CssRulesParser::Parse("div.span +      .card:hover{background-color:red;}");
        $this->assertEquals(['div.span+.card:hover{background-color:red;}'], $tab);
    }
    /**
    * Tests cssruleparser media.
    */
    public function test_cssruleparser_media()
    {
        $tab = CssRulesParser::Parse("@media(max-width:77) {.card{color:   red; border-color: indianred;}}");
        $this->assertEquals(['@media(max-width:77){.card{color:red;border-color:indianred;}}'], $tab);
    }
    /**
    * Tests cssruleparser multi media.
    */
    public function test_cssruleparser_multi_media()
    {
        $tab = CssRulesParser::Parse("@media(max-width:77px) {.card{color:   red; second{border-color:      indianred;} }}");
        $this->assertEquals(['@media(max-width:77px){.card{color:red;second{border-color:indianred;}}}'], $tab);
    }
    /**
    * Tests cssruleparser multi selector.
    */
    public function test_cssruleparser_multi_selector()
    {
        $tab = CssRulesParser::Parse("body a, img a, a:focus{color:red;}");
        $this->assertEquals(['body a, img a, a:focus{color:red;}'], $tab);
    }
}