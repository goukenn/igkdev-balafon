<?php

namespace IGK\Tests\Helper;

use IGK\Helper\MenuUtils;
use IGK\Helper\StringUtility;
use IGK\System\WinUI\Menus\MenuItem;
use IGK\Tests\BaseTestCase;

class StringUtilityTest extends BaseTestCase
{
    function test_uri_start(){
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
    function test_identifier(){

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

        $l = '<a href="'.$c->getUri("showConfig").'"></a>';
        $n = igk_create_notagnode();
        //$n->load($l);
        $p = [];
        $menu = new MenuItem("data", "", $c->getUri("showConfig"));

        // igk_wln_e( __FILE__.":".__LINE__,  $menu->getUri());
        // igk_debug(1);
        MenuUtils::InitMenu($n, 
            $menu
        , $p);
        $this->assertEquals(
            '<li><a href="./?c=igk%5Csystem%5Cconfiguration%5Ccontrollers%5Cauthorisationcontroller&f=showConfig">menu.data</a></li>',
            $n->render()
        ); 
    }
}