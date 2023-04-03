<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApiResponse.php
// @date: 20230215 11:47:35
namespace IGK\System\Http;


///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
class ApiResponse extends Response{

    public function output() { 
    }
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

}