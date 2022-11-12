<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServiceLoginActionTrait.php
// @date: 20221110 19:53:03
namespace IGK\System\Actions\Traits;

use IGK\System\Services\SignProvider;

///<summary>trait to declared service login with social provider connection</summary>
/**
* trait to declared service login with social provider connection
* @package IGK\System\Actions\Traits
*/
trait ServiceLoginActionTrait{
    protected $serviceLoginSigninView = 'ServiceLogin';
    public function connect(){ 
        // + | --------------------------------------------------------------------
        // + | connection with service providers - autoloaded 
        // + |        
        $ctrl = $this->getController(); 
        if ($provider = igk_getr("provider")){ 
            $provider = SignProvider::GetProvider($provider);
        }
        if ($provider){
            $provider->redirect_uri = $ctrl->getAppUri($this->serviceLoginSigninView);
            $provider->setSuccessURL(igk_getr("success", $ctrl->getAppUri("")));
            if (!$provider->login([SignProvider::class, "RegisterUserInfoCallback"]))
            {
                $provider->redirectTo($this->getController()->getAppUri($this->serviceLoginSigninView. "&error=provider_failed"));
            } else {
                return igk_do_response((array)$provider->getResponse());
            }
        }  else {
            igk_ilog('missing signin provider', 'BLF-SRV-ACTION-TRAIT');            
        }
    }
}