<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ISignInProvider.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Services;

/**
 * interface to implement for signin 
 * @package IGK\System\Services
 */
interface ISignInProvider{
    /**
     * get provider name
     * @return string
     */
    function getProviderName() : string;
    /**
     * login provider
     * @param callable $callable 
     * @return bool 
     */
    function login(callable $callable) : bool;

    function redirectTo($uri);
}
