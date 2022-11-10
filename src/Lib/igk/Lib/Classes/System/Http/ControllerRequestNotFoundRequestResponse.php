<?php

// @author: C.A.D. BONDJE DOUE
// @filename: NotFoundRequestResponse.php
// @date: 20220311 08:19:04
// @desc: 
namespace IGK\System\Http;

use IGK\Controllers\BaseController;

class ControllerRequestNotFoundRequestResponse extends ControllerRequestResponse{
    public function __construct($uri, $controller)
    {
        $this->code = 404;
        parent::__construct($uri, $controller);
        
    }     
 
}
