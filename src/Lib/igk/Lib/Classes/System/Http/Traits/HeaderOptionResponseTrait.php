<?php
// @author: C.A.D. BONDJE DOUE
// @file: HeaderOptionResponseTrait.php
// @date: 20230128 13:31:49
namespace IGK\System\Http\Traits;

use IGK\System\Http\WebResponse;
use IGKException;
use IGK\System\Http\Helper\Response as http;

///<summary></summary>
/**
* 
* @package IGK\System\Http\Traits
*/
trait HeaderOptionResponseTrait{
    /**
     * do option response
     * @return void 
     * @throws IGKException 
     */
    function optionResponse(){
        http::OptionResponse();     
    }
}