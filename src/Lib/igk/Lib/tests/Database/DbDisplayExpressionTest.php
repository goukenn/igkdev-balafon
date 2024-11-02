<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbDisplayExpressionTest.php
// @date: 20240921 09:33:01
namespace IGK\Tests\Database;

use IGK\Database\DbDisplayExpression;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\Database
* @author C.A.D. BONDJE DOUE
*/
class DbDisplayExpressionTest extends BaseTestCase{
    public function test_dbmodel_displayvalue(){
        $row = ["id"=>10, "name"=>"to_render", "title"=>"ok"];
        $exp = "{id} - {title}";
        $is_display = DbDisplayExpression::IsDisplayExpression($exp);
        $this->assertTrue($is_display);
        $r = DbDisplayExpression::RenderDisplayExpression($exp, $row);
        $this->assertEquals("10 - ok", $r);
    }
}