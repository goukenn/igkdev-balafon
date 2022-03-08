<?php
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