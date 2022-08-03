<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotAllowedRequestResponse.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Http;

use IGK\System\Http\ErrorRequestResponse;

class NotAllowedRequestResponse extends ErrorRequestResponse{
    public function __construct(){
        parent::__construct(RequestResponseCode::Forbiden, "Not Allowed");
    }
}