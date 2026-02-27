<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestUtility.php
// @date: 20220531 07:13:31
// @desc: 
namespace IGK\System\Http;
/**
 * 
 * @package IGK\System\Http
 */

/**
* auto generate doc.
* @package IGK\System\Http
*/
class RequestUtility{

    /**
    * auto generate doc.
    * @param bool $update
    * @return mixed
    */
    public static function RequestGet($paramHandler, $requestName, $paramName, $update=true){   
        $c = Request::getInstance()->have($requestName, $paramHandler->getParam($paramName));
        if ($update){
            if (!empty($c)){
                $paramHandler->setParam($paramName, $c);
            } else {
                $paramHandler->setParam($paramName, null);
            }
        }
        return $c;
    }
}