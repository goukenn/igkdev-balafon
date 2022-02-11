<?php
namespace IGK\System\Http;
 
/**
 * 
 * @package IGK\System\Http
 */
class PageNotFoundException extends RequestException{
    public function __construct($uri=null){
        $uri = $uri ?? igk_io_request_uri();
        $this->status = "Page not found";
        parent::__construct(RequestResponseCode::NotFound, $this->status . " : ".$uri);
  
    }
}