<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestException.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;
use Exception;

/**
* auto generate doc.
* @package IGK\System\Http
*/
class RequestException extends \IGKException{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $status;
    /**
     * Constructor.
     *
     * @param int            $code     The HTTP status code for the exception.
     * @param string         $message  Optional exception message; defaults to the status label.
     * @param Throwable|null $previous Optional previous throwable.
     */

    public function __construct($code, $message="", ?\Throwable $previous=null)
    {
        if (empty($message)){
            $message = igk_get_header_status($code);
        }
        parent::__construct($message, $code, $previous);
    }

    /**
    * auto generate doc.
    */

    function handle(){
        if (igk_server()->accept('json') || Request::getInstance()->isRestRequest()){
            igk_set_header($this->code);
            $d =  [
                'code'=>$this->code,
                'status'=>RequestResponse::GetStatus($this->code)
            ];
            if (igk_environment()->isDev()){
                $d['message'] = $this->getMessage();
            }
            igk_do_response(new JsonResponse((object)$d, $this->code));
            return true;
        }
    }
}