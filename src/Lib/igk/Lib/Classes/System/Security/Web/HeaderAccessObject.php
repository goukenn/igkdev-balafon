<?php
// @author: C.A.D. BONDJE DOUE
// @file: HeaderAccessObject.php
// @date: 20230130 08:19:18
namespace IGK\System\Security\Web;
use Exception;
use IGK\Helper\Activator; 
use IGK\System\Traits\ActivableTrait;
use IGKException;
/**
* handle Header Access Controller 
* @package IGK\System\Security\Web
*/
class HeaderAccessObject{ 
    use ActivableTrait;
    /**
    * Constant: auth bearer.
    * @var mixed
    */
    const AUTH_BEARER = 'Bearer';
    /**
    * Constant: auth basic.
    * @var mixed
    */
    const AUTH_BASIC = 'Basic';
    /**
     * auth ?demand
     * @var mixed
     */
    var $authorization;
    /**
     * list of auth methods
     * @var ?string
     */
    var $method;
    /**
     * header access 
     * @var ?string * 
     */
    var $headers;
    /**
     * request origin
     * @var ?string
     */
    var $origin;
    /**
    * auto generate doc.
    * @return string
    */
    public function getAuthType(){
        $g = explode(' ', $this->authorization);
        return $g[0];
    }
    /**
     * retreive bearer token 
     * @return mixed 
     * @throws IGKException 
     * @remark bearer token list
     */
    public function getBearerToken(){
        $g = explode(' ', $this->authorization);
        if ($g[0] == self::AUTH_BEARER){
            return trim(igk_getv($g, 1, ''));
        }
    }
    /**
    * auto generate doc.
    * @return string|void
    */
    public function getBasicToken(){
        $g = explode(' ', $this->authorization);
        if ($g[0] == self::AUTH_BASIC){
            return trim(igk_getv($g, 1, ''));
        }
    }
    /**
     * maybe passed in basic authentication service
     * @return array 
     */
    public static function HandleBasicAuth(){
        $user = igk_server()->PHP_AUTH_USER;
        $pwd =  igk_server()->PHP_AUTH_PW;
        return compact('user', 'pwd');
    }
    /**
     * get data 
     * @param mixed $data 
     * @return static 
     */
    public static function CreateFromData($data){
        return Activator::CreateNewInstance(self::class, $data);
    }
}