<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotAllowedRequestException.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Http;

use Throwable;

/**
 * 
 * @package IGK\System\Http
 */
class NotAllowedRequestException extends RequestException{
    public function __construct($uri=null, string $status=null, ?Throwable $previous=null){
        $this->code = RequestResponseCode::Forbiden;
        $this->status = $status ?? "Not allowed";
        $uri = $uri ?? igk_io_request_uri();
        parent::__construct($this->code, $this->status . " : ".$uri, $previous);
    }
}