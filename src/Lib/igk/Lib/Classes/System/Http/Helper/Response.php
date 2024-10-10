<?php
// @author: C.A.D. BONDJE DOUE
// @file: Response.php
// @date: 20230128 13:35:48
namespace IGK\System\Http\Helper;

use IGK\System\Http\Request;
use IGK\System\Http\RequestResponseCode;
use IGK\System\Http\WebResponse;
use IGKException;

///<summary></summary>
/**
* Request response helper
* @package IGK\System\Http\Helper
*/
class Response{
    const DEFAULT_ALLOWED_HEADERS = "Access-Control-Allow-Headers|igk-x-requested-with|igk-ajx|igk-from|Content-Type|Authorization|X-Authorization";
    /**
     * do system option response
     * @return void 
     * @throws IGKException 
     */
    public static function OptionResponse($data=null, $options=null){
        $_req = Request::getInstance(); 
        $data = $data ?? (igk_environment()->isDev() ?  "/Options:data,request_uri:".igk_io_request_uri():null);
        $rep = new WebResponse($data, 200, self::GetHeaderOptions(null, $options));
        $rep->cache =false; 
        return $_req->response($rep);

    }
    /**
     * get default access control header options \
     * by default check for configuration and return the expected header response
     * @param mixed $verb 
     * @return string[] 
     * @throws IGKException 
     */
    public static function GetHeaderOptions(?string $verb='options'){
        $verb = $verb ?? igk_server()->REQUEST_METHOD ?? 'options';
        $_req = Request::getInstance();  
        $_cnf = igk_configs(); 
        return [
            "Content-Type: text/html",            
            "Access-Control-Allow-Origin: ".$_cnf->get("access-control-allow-origin", $_req->getHeader()->origin), //, "*"),
            "Access-Control-Allow-Methods: ".$_cnf->get("access-control-allow-methods", "DELETE, PUT, GET, POST, STORE"),            
            // allow credential 
            "Access-Control-Allow-Headers: ".$_cnf->get("access-control-allow-headers", 
                    // + | for dev with vite response
                    implode(', ', 
                        explode('|', self::DEFAULT_ALLOWED_HEADERS)
                    )
            ),
            "Access-Control-Allow-Credentials: ".$_cnf->get("access-control-allow-credentials", "true")
        ]; 
    }

    /**
     * get bad request response
     * @return mixed 
     */
    public static function BadRequest(){
        static $sm_bad_request;
        if ($sm_bad_request===null){
            $sm_bad_request = new WebResponse('bad request', RequestResponseCode::BadRequest);
        }
        return $sm_bad_request;
    }
}