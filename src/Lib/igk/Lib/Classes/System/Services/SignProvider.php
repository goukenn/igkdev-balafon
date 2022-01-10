<?php
namespace IGK\System\Services;

use IGKException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * sign provider helper with oauth
 */
class SignProvider{
    const ENV_KEY = "signin_provider";
    /**
     * shared redirect uri
     * @var mixed
     */
    private static $sm_redirect_uri;
    /**
     * register sign environment provider
     * @param array $providerList 
     * @return void 
     * @throws BindingResolutionException 
     */
    public static function Register(array $providerList){
        $tab = & igk_environment()->createArray(self::ENV_KEY);
        foreach($providerList as $k){
            $tab[$k->getProviderName()] = $k;
        }
    }

    /**
     * handle sign provider request
     * @param callable $callback 
     * @return mixed 
     * @throws IGKException 
     */
    public static function Handle($callback){
        if ($provider = igk_environment()->getArray(self::ENV_KEY, igk_getr("provider"))){
            return $provider->login($callback);
        }
    }

    /**
     * bind redirect uri
     * @param mixed $redirect_uri 
     * @return void 
     */
    public static function SetRedirectUri($redirect_uri){
        self::$sm_redirect_uri = $redirect_uri;
    }
    /**
     * get binded redirect uri
     * @return mixed 
     */
    public static function GetRedirectUri(){
        return self::$sm_redirect_uri; 
    }

}