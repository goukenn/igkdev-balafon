<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RouteHandlerTest.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\Tests\System\Http;
use IGK\System\Http\RouteHandler;
use IGK\Tests\BaseTestCase;

/**
* Route handler test.
* @package IGK\Tests\System\Http
*/
class RouteHandlerTest extends BaseTestCase{
    /**
    * Tests route handler.
    */
    public function test_route_handler(){
        $route = "/demo/";
        $this->assertEquals(
            "#^/demo/$#",
            RouteHandler::GetRouteRegex("/demo/", ["id"=>".*"])
        );
    }
    /**
    * Tests route handler options.
    */
    public function test_route_handler_options(){         
        $this->assertEquals(
            "#^/demo(/(?P<id>[^/]+))$#",
            RouteHandler::GetRouteRegex("/demo/{id}", ["id"=>".*"])
        );
    }
    /**
    * Tests route handler options optional.
    */
    public function test_route_handler_options_optional(){
        $this->assertEquals(
            '#^/demo(/(?P<id>[^/]+)?)?$#',
            RouteHandler::GetRouteRegex("/demo/{id*}", ["id"=>".*"])
        );
    }
    /**
    * Tests route handler post.
    */
    public function test_route_handler_post(){ 
        $regex = RouteHandler::GetRouteRegex("/demo/post-{id*}/", ["id"=>".*"], false); 
        $this->assertEquals(
            '#^/demo/post-(?P<id>[^/]+)?(/)?$#',
            $regex            
        );
    }
    /**
    * Tests get route uri.
    */
    public function test_get_route_uri(){ 
        $regex = RouteHandler::GetResolveURI("/demo/post-{id*}/", ["id"=>"25"]); 
        $this->assertEquals(
            '/demo/post-25',
            $regex            
        );
        $regex = RouteHandler::GetResolveURI("/demo/{id*}/", ["id"=>"25"]); 
        $this->assertEquals(
            '/demo/25',
            $regex            
        );
    }
    /**
    * Tests dash uri.
    */
    public function test_dash_uri(){
        $s = "/l81/dashboard/get-calendars";
        $regex = RouteHandler::GetRouteRegex("l81/dashboard/get-calendars", ["id"=>".*"], false); 
        $this->assertEquals(
            '#^/l81/dashboard/get-calendars$#',
            $regex            
        );
    }
}