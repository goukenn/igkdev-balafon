<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotFoundRequestResponse.php
// @date: 20220311 08:19:04
// @desc: 
namespace IGK\System\Http;
use IGK\Controllers\BaseController;

/**
* auto generate doc.
* @package IGK\System\Http
*/
class ControllerRequestNotFoundRequestResponse extends ControllerRequestResponse{
    var $message;
    /**
     * Constructor.
     *
     * @param string $uri        The requested URI that was not found.
     * @param mixed  $controller The controller handling the request.
     */
    public function __construct($uri, $controller)
    {
        $this->code = RequestResponseCode::NotFound;
        parent::__construct($uri, $controller);
    }     
}