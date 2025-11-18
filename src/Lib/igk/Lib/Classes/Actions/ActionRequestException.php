<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionRequestException.php
// @date: 20240923 11:40:00
namespace IGK\Actions;

use IGK\System\Http\RequestException;
use Throwable;

/**
 * 
 * @package IGK\Actions
 * @author C.A.D. BONDJE DOUE
 */
class ActionRequestException extends RequestException
{
    public function __construct( $message, ?int $code=null, ?Throwable $previous = null)
    {
        return parent::__construct($code, $message, $previous);
    }
    public function handle()
        {
            return parent::handle();
        }
}
