<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RouteHandlerTest.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\System\Http;

use IGK\System\Http\RouteHandler;
use IGK\Tests\BaseTestCase;

class RouteHandlerTest extends BaseTestCase{
    public function test_route_handler(){
        $route = "/demo/";
        $this->assertEquals(
            "#^/demo/$#",
            RouteHandler::GetRouteRegex("/demo/", ["id"=>".*"])
        );
    }
    public function test_route_handler_options(){         
        $this->assertEquals(
            "#^/demo(/(?P<id>[^/]+))$#",
            RouteHandler::GetRouteRegex("/demo/{id}", ["id"=>".*"])
        );
    }
    public function test_route_handler_options_optional(){
      
        $this->assertEquals(
            '#^/demo(/(?P<id>[^/]+)?)?$#',
            RouteHandler::GetRouteRegex("/demo/{id*}", ["id"=>".*"])
        );
    }
    public function test_route_handler_post(){ 
        $regex = RouteHandler::GetRouteRegex("/demo/post-{id*}/", ["id"=>".*"], false); 
        // var_dump($tab);
        $this->assertEquals(
            '#^/demo/post-(?P<id>[^/]+)?(/)?$#',
            $regex            
        );
    }
    public function test_get_route_uri(){ 
        $regex = RouteHandler::GetResolveURI("/demo/post-{id*}/", ["id"=>"25"]); 
        // var_dump($tab);
        $this->assertEquals(
            '/demo/post-25',
            $regex            
        );


        $regex = RouteHandler::GetResolveURI("/demo/{id*}/", ["id"=>"25"]); 
        // var_dump($tab);
        $this->assertEquals(
            '/demo/25',
            $regex            
        );
    }

    public function test_dash_uri(){
        $s = "/l81/dashboard/get-calendars";
        $regex = RouteHandler::GetRouteRegex("l81/dashboard/get-calendars", ["id"=>".*"], false); 
        // $tab = [];
        // preg_match_all("#^/l81/dashboard/get-calendars$#", $s, $tab);
 
        $this->assertEquals(
            '#^/l81/dashboard/get-calendars$#',
            $regex            
        );

    }
}