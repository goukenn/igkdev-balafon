<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PageNotFoundException.php
// @date: 20220803 13:48:55
// @desc: 

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