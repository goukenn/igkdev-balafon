<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApiResponse.php
// @date: 20230215 11:47:35
namespace IGK\System\Http;
use Exception;
use IGKException;
/**
* 
* @package IGK\System\Http
*/
class ApiResponse extends Response{

    /**
    * Property: header.
    * @var mixed
    */
    protected $m_header;
    /**
     * set the current header 
     * @param mixed $header 
     * @return void 
     */

    public function setHeader($header){
        $this->m_header = $header;
    }
    /**
     * get header
     * @return mixed 
     */

    public function getHeader(){
        return $this->m_header ; 
    }
    /**
     * base output 
     * @return mixed 
     */

    public function output(){
    }

    /**
    * Die.
    * @param string $message
    * @param mixed $code
    */
    public function die(string $message, $code=500){
        igk_do_response(new ErrorRequestResponse($code, $message));
    }
    /**
     * reply with response field
     * @param mixed $data 
     * @return array 
     */

    public function response($data, $code=200){
        return [
            "code"=>$code,
            "response"=>$data
        ];
    }
    /**
     * return an empty api response 
     */

    public static function EmptyJsonResponse(){
        return new JsonResponse([], 204);
    }
    /**
     * do response
     * @param mixed $data 
     * @param int $code 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     */

    public function doResponse($data, int $code=200){
        igk_do_response(
            new JsonResponse($this->response($data, $code), $code, $this->m_header)
        );
    }
}