<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestException.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Http;
use Exception;
class RequestException extends \IGKException{
    protected $status;
 
    public function __construct($code, $message="", ?\Throwable $previous=null)
    {
        if (empty($message)){
            $message = igk_get_header_status($code);
        }
        parent::__construct($message, $code, $previous);
    }
    function handle(){
        if (igk_server()->accept("json")){
            igk_set_header($this->code);
            igk_do_response(new JsonResponse((object)[
                "code"=>$this->code,
                "status"=>RequestResponse::GetStatus($this->code)
            ], $this->code));
            return true;
        }
    }
}