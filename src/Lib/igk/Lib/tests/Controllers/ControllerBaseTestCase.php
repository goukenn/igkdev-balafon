<?php 
namespace IGK\Tests\Controllers;

use IGK\Controllers\BaseController;
use IGK\System\Http\RequestResponse;
use IGK\Tests\BaseTestCase;

abstract class ControllerBaseTestCase extends BaseTestCase{
    protected $controller;

    public function __construct(?BaseController $ctrl = null){
        parent::__construct();
        if ($ctrl === null){
            if ($c = igk_getv($_ENV, "IGK_TEST_CONTROLER")){
                $this->controller = igk_getctrl($c); 
            }
        }else{
            $this->controller = $ctrl;
        }
       
    }
    /**
     * handle request and return a request response
     * @param string $uri 
     * @return RequestResponse 
     */
    protected function handleRequest(string $uri):RequestResponse{ 
        $this->controller->getConfigs()->no_auto_cache_view = true;
		return $this->controller::handle($uri);
	}
}