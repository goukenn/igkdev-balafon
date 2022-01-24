<?php
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