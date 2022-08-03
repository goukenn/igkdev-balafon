<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IResponse.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Http;

/**
 * sent the output response
 * @package IGK
 */
interface IResponse{
    /**
     * send output
     * @return mixed 
     */
    function output();
}