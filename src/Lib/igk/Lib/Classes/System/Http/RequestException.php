<?php

namespace IGK\System\Http;
use Exception;
///<summary>request exception</summary>
class RequestException extends \IGKException{
 
    ///<summary>.ctr request constructor</summary>
    public function __construct($code, $message="", ?\Throwable $previous=null)
    {
        if (empty($message)){
            $message = igk_get_header_status($code);
        }
        parent::__construct($message, $code, $previous);
    }
    ///<summary> handle this code on condition</summary>
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