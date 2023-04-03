<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Response.php
// @date: 20220803 13:48:55
// @desc: 



namespace IGK\System\Http;

use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\IInjectable;
use IGKException;

///<summary> default response </summary>
abstract class Response implements IResponse, IInjectable{
    /**
     * response body
     * @var mixed
     */
    private $body;

    public function getBody(){}
    public function setBody($body){
    }

    /**
     * handle response 
     * @param mixed $r 
     * @return mixed 
     * @throws IGKException 
     */
    public static function HandleResponse($r){
        $app = igk_app();
        $handler = ($app ? $app->getService(\ResponseHandler::class) : null) ?? new ResponseHandler();
        return $handler->HandleReponse($r); 
    }
}