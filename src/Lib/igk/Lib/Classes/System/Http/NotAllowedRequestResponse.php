<?php
namespace IGK\System\Http;

use IGK\System\Http\ErrorRequestResponse;

class NotAllowedRequestResponse extends ErrorRequestResponse{
    public function __construct(){
        parent::__construct(RequestResponseCode::Forbiden, "Not Allowed");
    }
}