<?php
 

namespace IGK\System\Http;

class XmlResponse extends WebResponse{
    public function __construct($data, $status=200)
    {
        parent::__construct($data, $status, ["Content-Type:application/xml"]);
    }
}