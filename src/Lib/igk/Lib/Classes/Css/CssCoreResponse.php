<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssCoreResponse.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Css;

use IGK\System\Http\WebResponse;

class CssCoreResponse extends WebResponse{
    public function __construct($content)
    {
        parent::__construct($content, 200, [
            "Content-Type: text/css"
        ]);
    }
}