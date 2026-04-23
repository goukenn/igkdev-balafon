<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotFoundRequestResponse.php
// @date: 20220311 08:19:04
// @desc: 
namespace IGK\System\Http;
use IGK\Controllers\BaseController;

/**
 * reprensent a controller request response
 * @package IGK\System\Http
 */
class ControllerRequestResponse extends RequestResponse{
    /**
    * Property: uri.
    * @var mixed
    */
    var $uri;
    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;
    /**
    * .ctr
    * @param string $uri
    * @param null|BaseController $controller
    */
    public function __construct(string $uri, ?BaseController $controller=null){
        $this->uri = $uri;
        $this->$controller = $controller; 
        parent::__construct();
    }
    /**
    * Renders.
    */
    public function render() { 
    }
}