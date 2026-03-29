<?php
// @author: C.A.D. BONDJE DOUE
// @file: DieRequestTrait.php
// @date: 20240104 17:26:40
namespace IGK\System\Http\Traits;
use IGK\System\Http\ErrorRequestResponse;
use IGKException;
/**
* auto generate doc.
* @package IGK\System\Http\Traits
* @author C.A.D. BONDJE DOUE
*/
trait DieRequestTrait{
    /**
     * die 
     * @param int $code error status code 
     * @param null|string $message 
     * @return mixed 
     * @throws IGKException 
     */
    protected function die(int $code, ?string $message=null){
        return igk_do_response(new ErrorRequestResponse($code, $message));
    }
}