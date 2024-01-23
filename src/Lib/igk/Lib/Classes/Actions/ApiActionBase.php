<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ApiActionBase.php
// @date: 20230220 11:47:52
// @desc: comment 

namespace IGK\Actions;

use IGK\System\Http\ErrorRequestResponse;
use IGK\System\Http\RequestResponse;
use Throwable;

// + | --------------------------------------------------------------------
// + | 
// + |

/**
 * global api action 
 * @package IGK\Actions
 */
abstract class ApiActionBase extends MiddlewireActionBase{
    protected function die($message, $code=400){
        igk_ilog("[api - die] : ".json_encode($message));
        igk_do_response(new ErrorRequestResponse($code, $message));
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
        return  !is_null($response) || ($response instanceof RequestResponse);
    }
    
    protected function _handleMethodNotFound($name)
    {
        igk_ilog(sprintf('method %s not found in ', $name, get_class($this)));
        $this->die("method not found:".$name, 500);
    }

    protected function _handleThrowable(Throwable $ex)
    { 
        $this->die(
            igk_environment()->isDev()? ['type'=>get_class($ex), 
            'ex_message'=>($p = $ex->getPrevious()) ? $p->getMessage() : null, 
            'message'=>"misconfiguration. Action handle throwable"] : null, $ex->getCode());  
    }
}