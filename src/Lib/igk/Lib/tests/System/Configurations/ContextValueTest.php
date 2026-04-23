<?php
// @file: AttributeTemplateTest.php
// @author: C.A.D. BONDJE DOUE
// @description: Html attribute template register
// @copyright: igkdev © 2022
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Tests\System\Html;
use IGK\Controllers\BaseController;
use IGK\System\Configuration\SysAppConfigExpression;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;

/**
* Context value test.
* @package IGK\Tests\System\Html
*/
class ContextValueTest extends BaseTestCase
{
    /**
    * Tests loading configuration.
    */
    function test_loading_configuration()
    {
        $g = igk_conf_load_content(<<<MSG_EOF
<balafon><div>sample</div></balafon>
MSG_EOF, "balafon");
        $this->assertTrue($g == (object)[
            "div" => "sample"
        ], "failed to load configuration");
    }
    /**
    * Tests get custom expression.
    */
    function test_get_custom_expression()
    {
        $s = "";
        \IGK\System\Configuration\SysConfigExpressionFactory::Register("baba", DummyExpression::class);
        $c = \IGK\System\Configuration\SysConfigExpressionFactory::Create("baba", "baba.operator");
        $this->assertEquals(
            "operator:1",
            "" . $c
        );
    }
}
/**
* Dummy expression.
* @package IGK\Tests\System\Html
*/
class DummyExpression extends SysAppConfigExpression
{
    /**
    * Property: tag.
    * @var mixed
    */
    protected $tag = "baba";
    /**
    * Returns Operator.
    */
    public function getOperator()
    {
        return "operator:1";
    }
}