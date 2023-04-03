<?php
// @author: C.A.D. BONDJE DOUE
// @filename: StringUtilityTest.php
// @date: 20220803 13:48:54
// @desc: 
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/Helper/StringUtilityTest.php


namespace IGK\Tests\Helper;

use IGK\Helper\MenuUtils;
use IGK\Helper\StringUtility;
use IGK\System\WinUI\Menus\MenuItem;
use IGK\Tests\BaseTestCase;

class StringUtilityTest extends BaseTestCase
{
    public function test_uri_start(){
        $this->assertTrue(
            StringUtility::UriStart("https://local.com/Configs", "https://local.com/Configs"),
            "not matching equal"
        );
        $this->assertFalse(
            StringUtility::UriStart("https://local.com/Configs!Settings", "https://local.com/Configs"),
            "not matching base"
        );
        $this->assertTrue(
            StringUtility::UriStart("https://local.com/Configs", "https://local.com/Configs/"),
            "not matching equal"
        );

        $this->assertTrue(
            StringUtility::UriStart("https://local.com/Configs/Sample/DAta", "https://local.com/Configs"),
            "not matching equal"
        ); 
        $this->assertTrue(
            StringUtility::UriStart("https://l81.local.com:7300/Configs/?c=c_cf&f=setpage", 
            "https://l81.local.com:7300/Configs")
        );
    }
    public function test_identifier(){

        $this->assertEquals(null, 
        StringUtility::Identifier("45698"),
        "identifier must return null value"
        );

        $this->assertEquals('__45698', 
        StringUtility::Identifier("__45698"),
        "identifier must return null value"
        );

        $this->assertEquals('__4569_m8', 
        StringUtility::Identifier("__4569_m8"),
        "identifier : test 3"
        );
        $this->assertEquals('__4569_M_8', 
        StringUtility::Identifier("__4569_m/8"),
        "identifier : test 4"
        );
    }

    public function test_get_uri_value(){
        $c = \IGK\System\Configuration\Controllers\AuthorisationController::ctrl(); 
        $n = igk_create_notagnode(); 
        $p = [];
        $menu = new MenuItem("data", "", $c->getUri("showConfig"));
        MenuUtils::InitMenu($n, 
            $menu
        , $p);
        $this->assertEquals(
            '<li><a href="./?c='. \urlencode($c->getName()).'&f=showConfig">menu.data</a></li>',
            $n->render()
        ); 
    }

    public function test_get_constant_name(){
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('userID'),
            "CASE 1 failed"
        ); 
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('user_ID')
            ,"CASE 2 failed"
        );
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('user_Id')
            ,"CASE 3 failed"
        );
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('UserId')
            ,"CASE 4 failed"
        );
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('__UserId')
            ,"CASE 5 failed"
        );
        // test with space content
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('__User Id')
            ,"CASE 6 failed"
        );
        // test with all __
        $this->assertEquals(
            "FD_USER_ID",
            "FD_".StringUtility::GetConstantName('__User Id__')
            ,"CASE 6 failed"
        );
    }
}