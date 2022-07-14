<?php
namespace IGK\System\Http;
 
/**
 * 
 * @package IGK\System\Http
 */
class ViewNotFoundException extends RequestException{
    public function __construct($uri=null){
        $uri = $uri ?? igk_io_request_uri();
        $this->status = "View not found";
        parent::__construct(RequestResponseCode::NotFound, $this->status . " : ".$uri);  
    }
}