<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ApiActionBase.php
// @date: 20230220 11:47:52
// @desc: comment 

namespace IGK\Actions;

use IGK\System\Http\ErrorRequestResponse;
use Throwable;

// + | --------------------------------------------------------------------
// + | 
// + |

/**
 * global api action 
 * @package IGK\Actions
 */
abstract class ApiActionBase extends ActionBase{
    protected function die($message, $code=400){
        igk_ilog("response error: ".json_encode($message));
        igk_do_response(new ErrorRequestResponse($code, $message));
    }
    
    protected function _handleMethodNotFound($name)
    {
        igk_ilog(sprintf('method %s not found in ', $name, get_class($this)));
        $this->die("method not found:".$name, 500);
    }

    protected function _handleThrowable(Throwable $ex)
    { 
        $this->die(['type'=>get_class($ex), 'ex_message'=>$ex->getMessage(), 
        'message'=>"misconfiguration. Action handle throwable"], 500);  
    }
}