<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FormLoginPostTrait.php
// @date: 20220603 06:53:07
// @desc: form login trait

namespace IGK\System\Html\Forms\Actions\Traits;

use IGK\Actions\Traits\NotifyActionTrait;
use IGK\Helper\UriPath;
use IGK\System\Exceptions\CrefNotValidException;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use ReflectionException;
use function igk_resources_gets as __;

/**
 * form login action trait - manage user login and logout 
 */
trait FormLoginPostActionTrait
{
    use NotifyActionTrait;
    /**
     * form default signin view
     * @var string
     */
    protected $formLoginPostSigninView='signin';
    /**
     * login success uri
     * @return mixed 
     */
    protected function get_login_redirect_uri()
    {
        return $this->getController()->getAppUri("dashboard");
    }
    /**
     * failed login success
     * @param mixed $redirect 
     * @return mixed 
     */
    protected function get_login_failed_redirect_uri($redirect)
    {
        $v_view = $this->formLoginSigninView ?? 'signin';   
        $query = [];
        if (!empty($redirect)){
            $query["continue"] =urlencode($redirect);
        }
        $q = count($query)>0 ? "?".http_build_query($query) : "";
        return $this->getController()->getAppUri($v_view.$q);
    }
    /**
     * redirect to login service 
     * @param Request $request 
     * @return null 
     */
    public function login_get(Request $request){
        $this->redirect = $this->getController()::uri('ServiceLogin');
        return null;
    }
    /**
     * post login to application
     * @return void 
     * @throws CrefNotValidException 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function login_post(Request $request)
    {
        // igk_ilog('login_post : call ');   
        $this->notifyActionName = 'form_login';
        $ctrl = $this->getController();
        $redirect = $ctrl::uri($this->serviceLoginSigninView);
 
      
        $this->getController()->checkUser(false);

        $pwd = igk_getr("password");
        $u = igk_getr("login");
        $is_cref = igk_valid_cref(1);         
        if ($is_cref ){
            igk_ilog('try login >');
            $this->notify('try login', 'igk-danger');
            if (!($ctrl::login($u, $pwd, false))) {            
                igk_ilog('login failed : '.$u);
                $ctrl->setParam("failed_log", 1);
                $redirect = $this->get_login_failed_redirect_uri($redirect);       
                
            }else{
                igk_ilog('login success: '.$u, 'FORMLOGIN');
                $redirect = $this->get_login_redirect_uri();
                 
            }
        } else {            
            igk_ilog('cref not valid:', 'FORMLOGIN');
            $redirect = $ctrl::uri($this->serviceLoginSigninView);
            $this->notify(__('you failed to sign in - please try again later or contact the webmaster'),
            'igk-danger');
        }     
        // + | --------------------------------------------------------------------
        // + | check action extends 
        // + |
        
        if ($redirect && !UriPath::CheckActionExtend($redirect, 'login')){
            $redirect = $redirect;
        } else {
            $redirect = $this->getController()->uri('');
        }   
        $ctrl->redirect = $redirect;
        
        if (igk_is_ajx_demand()){
            return [
                'redirect'=>$this->redirect,
            ];
        }     
    }
  
}
