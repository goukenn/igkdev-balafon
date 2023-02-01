<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestResponseInfo.php
// @date: 20230124 11:45:24
namespace IGK\System\Http;


///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
class RequestResponseInfo{
    /**
     * 
     * @var mixed
     */
    var $status = 'OK';
    /**
     * 
     * @var int
     */
    var $code = 200;
    /**
     * message to send
     * @var ?string
     */
    var $message;    
    /**
     * 
     * @var ?data extra data to send
     */
    var $data;
}