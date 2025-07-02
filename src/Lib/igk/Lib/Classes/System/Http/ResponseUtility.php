<?php
// @author: C.A.D. BONDJE DOUE
// @file: ResponseUtility.php
// @date: 20250325 21:09:06
namespace IGK\System\Http;

use Exception;
use IGKException;

/**
* shared reponse utility functions
* @package IGK\System\Http
* @author C.A.D. BONDJE DOUE
*/
abstract class ResponseUtility{
    /**
     * 
     * @param bool $allow_auto_origin 
     * @param null|array $allowed_header 
     * @return array 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function CreateCredentialHeader(bool $allow_auto_origin=true, ?array $allowed_header=null):array{
        return array_filter([
            'Access-Control-Allow-Origin: ' . ($allow_auto_origin ? igk_server()->get('HTTP_ORIGIN', '*') : igk_io_baseuri()),
            'Access-Control-Allow-Credentials: true',
            $allowed_header ? sprintf('Access-Control-Allow-Headers:%s', implode(',', $allowed_header)) : null
        ]);
    }
}