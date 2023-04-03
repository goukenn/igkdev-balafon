<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SignProvider.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Services;

use IGK\Models\Users;
use IGK\System\Services\Auth\AuthSignInfo;
use IGKEvents;
use IGKException;
use IGKObjStorage;
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
     * check if provider contains registered signin information
     * @return bool 
     * @throws BindingResolutionException 
     */
    public static function IsRegistered(){
        $tab = & igk_environment()->createArray(self::ENV_KEY); 
        return count($tab)>0;  
    }
    /**
     * unregister login provider
     * @param string $name 
     * @return void 
     * @throws BindingResolutionException 
     */
    public static function Unregister(string $name){

        $tab = & igk_environment()->createArray(self::ENV_KEY);
        unset($tab[$name]);
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
     * return the provider by name
     * @param string $provider 
     * @return null|ISignInProvider
     * @throws BindingResolutionException 
     * @throws IGKException 
     */
    public static function GetProvider(string $provider){
        $tab = & igk_environment()->createArray(self::ENV_KEY);
        return igk_getv($tab, $provider);
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
    public static function RegisterUserInfoCallback( AuthSignInfo $userinfo){
        $c = false;
        if ($user = Users::select_row(["clLogin"=>$userinfo->email])){            
            igk_hook(IGKEvents::HOOK_USER_EXISTS, [$user]);
            $c = true;
        }else {            
            $u = Users::createEmptyRow();
            $u->clLogin = $userinfo->email;
            $u->clFirstName = $userinfo->firstname;
            $u->clLastName = $userinfo->lastname;
            $u->clPicture = $userinfo->picture;
            $u->clStatus = $userinfo->verified ? "1" : 0;
            if ($user = Users::Register($u)){ 
                $c = true;
            } 
        }        
        return $c; 
    } 
}