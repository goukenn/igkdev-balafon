<?php

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
