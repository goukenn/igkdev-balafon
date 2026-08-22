<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApiActionBase.php
// @date: 20230220 11:47:52
// @desc: comment 
namespace IGK\Actions;
use IGK\System\Http\ErrorRequestResponse;
use IGK\System\Http\JsonResponse;
use IGK\System\Http\Request;
use IGK\System\Http\RequestResponse;
use IGK\System\Http\RequestResponseCode;
use Throwable;
// + | --------------------------------------------------------------------
// + | 
// + |

/**
 * global api action 
 * @package IGK\Actions
 */
abstract class ApiActionBase extends MiddlewireActionBase{
    /**
    * Property: response.
    * @var mixed
    */
    protected $response;
    /**
    * Property: status.
    * @var mixed
    */
    protected $status;
    /**
    * .ctr
    */
    public function __construct()
    {
        parent::__construct();
        $this->status = RequestResponseCode::Ok;
    }
    /**
    * Die.
    * @param mixed $message
    * @param mixed $code
    */
    protected function die($message, $code=400){
        igk_ilog("[api - die] : ".json_encode($message));
        igk_do_response(new ErrorRequestResponse($code, $message));
    }
    /**
     * handle error
     * @param mixed $code 
     * @param mixed ...$params 
     * @return mixed|void 
     */
    protected function handleError($code, ...$params)
    {
        igk_do_response(new JsonResponse(json_encode($params), $code));
    }
    /**
    * Json.
    * @param mixed $data
    * @param mixed $code
    */
    protected function _json($data, $code=RequestResponseCode::Ok){
        return igk_json(json_encode($data), $code);
    }
    /**
     * enabled handling response.
     * @param mixed $response 
     * @return bool 
     */
    protected function _handleResponse($response): bool
    {
        // + | --------------------------------------------------------------------
        // + | by default in ajx context and not null 
        // + | 
        if (Request::getInstance()->method('GET')){ 
            if($response instanceof RequestResponse)
                return true; 
        }
        return parent::_handleResponse($response) || is_array($response); 
    }
    /**
    * Handle method not found.
    * @param mixed $name
    */
    protected function _handleMethodNotFound($name)
    {
        igk_ilog(sprintf('method %s not found in ', $name, get_class($this)));
        $this->die("method not found:".$name, 500);
    }
    /**
    * Handle throwable.
    * @param Throwable $ex
    */
    protected function _handleThrowable(Throwable $ex)
    { 
        $this->die(
            igk_environment()->isDev()? 
            ['type'=>get_class($ex), 
            'ex_message'=>($p = $ex->getPrevious()) ? $p->getMessage() : null, 
            'message'=>"misconfiguration. Action handle throwable",
            'real_message'=>$ex->getMessage(),
            'at'=>$ex->getFile().":".$ex->getLine(),
            ] : null, $ex->getCode());  
    } 
}