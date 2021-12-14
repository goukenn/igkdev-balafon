<?php
namespace IGK\System\Http;
 
/**
 * 
 * @package IGK\System\Http
 */
class NotAllowedRequestException extends RequestException{
    public function __construct($uri=null){
        $this->code = RequestResponseCode::Forbiden;
        $this->status = "Not allowed";
    }
    function __toString(){

    }
    
}