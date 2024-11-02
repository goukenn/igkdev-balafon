<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewNotFoundException.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Http;
 
/**
 * 
 * @package IGK\System\Http
 */
class ViewNotFoundException extends RequestException{
    /**
     * get the status message
     * @var string
     */
    protected $status;
    public function __construct($uri=null, $code=RequestResponseCode::NotFound){
        $uri = $uri ?? igk_io_request_uri();
        $this->status = "View not found";
        parent::__construct($code, $this->status . " : ".$uri);  
    }
}