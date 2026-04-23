<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XmlResponse.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;

/**
* auto generate doc.
*/
class XmlResponse extends WebResponse{
    /**
    * .ctr
    * @param mixed $data
    * @param mixed $status
    */
    public function __construct($data, $status=200)
    {
        parent::__construct($data, $status, ["Content-Type: application/xml"]);
    }
}