<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHeaderResponse.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Http;

interface IHeaderResponse{
    function getResponseHeaders() : ?array;
}