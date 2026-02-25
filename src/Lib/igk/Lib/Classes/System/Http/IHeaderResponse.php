<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHeaderResponse.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;
interface IHeaderResponse{
    /**
     * Returns the response headers array, or null if none are defined.
     *
     * @return array|null
     */
    function getResponseHeaders() : ?array;
}