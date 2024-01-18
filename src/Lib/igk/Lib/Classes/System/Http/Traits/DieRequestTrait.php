<?php
// @author: C.A.D. BONDJE DOUE
// @file: DieRequestTrait.php
// @date: 20240104 17:26:40
namespace IGK\System\Http\Traits;

use IGK\System\Http\ErrorRequestResponse;

///<summary></summary>
/**
* 
* @package IGK\System\Http\Traits
* @author C.A.D. BONDJE DOUE
*/
trait DieRequestTrait{
    protected function die($code, $message=null){
        return igk_do_response(new ErrorRequestResponse($code, $message));
    }
}