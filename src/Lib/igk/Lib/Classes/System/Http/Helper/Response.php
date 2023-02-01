<?php
// @author: C.A.D. BONDJE DOUE
// @file: Response.php
// @date: 20230128 13:35:48
namespace IGK\System\Http\Helper;

use IGK\System\Http\Request;
use IGK\System\Http\WebResponse;
use IGKException;

///<summary></summary>
/**
* Request response helper
* @package IGK\System\Http\Helper
*/
class Response{
    /**
     * do system option respone
     * @return void 
     * @throws IGKException 
     */
    public static function OptionResponse($data=null, $options=null){
        $_req = Request::getInstance(); 
        $data = $data ?? "/Options:data,request_uri:".igk_io_request_uri();
        // error_log("login data : ".$data."\n ::: ". $_req->getHeader()->origin); //  json_encode(getallheaders()));
        $rep = new WebResponse($data, 200, self::GetHeaderOptions($options));
        $rep->cache =false;
        igk_do_response($rep);  
    }
    /**
     * get default access control header options
     * @param mixed $options 
     * @return string[] 
     * @throws IGKException 
     */
    public static function GetHeaderOptions($options = null){

        $_req = Request::getInstance(); 
        if (igk_server()->getAccessControl()){
            return [
                "Content-Type: text/html",            
                "Access-Control-Allow-Origin: ".igk_configs()->get("access-control-allow-origin", $_req->getHeader()->origin), //, "*"),
                "Access-Control-Allow-Methods: ".igk_configs()->get("access-control-allow-methods", "DELETE, PUT, GET, POST, STORE"),            
                // allow credential 
                "Access-Control-Allow-Headers: ".igk_configs()->get("access-control-allow-headers", "Content-Type, authorization"),
                "Access-Control-Allow-Credentials: ".igk_configs()->get("access-control-allow-credentials", "true")
            ];
        }
        return [];
    }
}