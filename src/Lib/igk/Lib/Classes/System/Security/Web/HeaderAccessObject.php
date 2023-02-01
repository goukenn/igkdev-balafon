<?php
// @author: C.A.D. BONDJE DOUE
// @file: HeaderAccessObject.php
// @date: 20230130 08:19:18
namespace IGK\System\Security\Web;

use IGK\Helper\Activator; 
use IGK\System\Traits\ActivableTrait;
use IGKException;

///<summary></summary>
/**
* handle Header Access Controller 
* @package IGK\System\Security\Web
*/
class HeaderAccessObject{ 
    use ActivableTrait;
    const AUTH_BEARER = 'Bearer';

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
     * list of auth user 
     * @var ?string
     */
    var $headers;

    /**
     * request origin
     * @var ?string
     */
    var $origin;

    public function getAuthType(){
        $g = explode(' ', $this->authorization);
        return $g[0];
    }
    /**
     * retreive bearead token 
     * @return mixed 
     * @throws IGKException 
     */
    public function getBearerToken(){
        $g = explode(' ', $this->authorization);
        if ($g[0] == self::AUTH_BEARER){
            return trim(igk_getv($g, 1, ''));
        }
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