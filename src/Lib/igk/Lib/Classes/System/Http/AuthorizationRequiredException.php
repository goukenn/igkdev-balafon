<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AuthorizationRequiredException.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;
use IGKException;
use Throwable;
class AuthorizationRequiredException extends NotAllowedRequestException{
    /**
     * Constructor.
     *
     * @param string         $msg       The authorization error message.
     * @param Throwable|null $throwable Optional previous throwable.
     */
    public function __construct($msg, ?Throwable $throwable=null)
    {
        // igk_wln_e("the message ". $msg);
        parent::__construct(null, $msg, $throwable);
    }
}