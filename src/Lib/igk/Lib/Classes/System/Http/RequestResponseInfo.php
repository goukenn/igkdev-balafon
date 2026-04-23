<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestResponseInfo.php
// @date: 20230124 11:45:24
namespace IGK\System\Http;

/**
* 
* @package IGK\System\Http
*/
/**
* auto generate doc.
* @package IGK\System\Http
*/
class RequestResponseInfo{
    /**
    * auto generate doc.
    * @var mixed
    */
    var $status = 'OK';
    /**
    * auto generate doc.
    * @var int
    */
    var $code = 200;
    /**
     * message to send
     * @var ?string
     */
    var $message;
    /**
    * auto generate doc.
    * @var ?data extra data to send
    */
    var $data;
}