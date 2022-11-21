<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FormLoginPostTrait.php
// @date: 20220603 06:53:07
// @desc: form login trait

namespace IGK\System\Html\Forms\Actions;

use IGK\System\Exceptions\CrefNotValidException;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
use function igk_resources_gets as __;

/**
 * form login action trait - manage user login and logout 
 */
trait FormLoginPostActionTrait
{
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
    public function login_get(){
        $this->redirect = $this->getController()::uri('ServiceLogin');
    }
    /**
     * post login to application
     * @return void 
     * @throws CrefNotValidException 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function login_post()
    {
        $ctrl = $this->getController();
        $redirect = $ctrl::uri($this->serviceLoginSigninView);
        // $this->notify(__('you failed to sign in - please try again later or contact the webmaster'),
        //     'igk-danger');
        // return;
        igk_ilog('login_post : call ');
        
        $this->getController()->checkUser(false);
        $redirect =  igk_getr("continue", $this->get_login_redirect_uri());
        $pwd = igk_getr("password");
        $u = igk_getr("login");
        $is_cref = 1 || igk_valid_cref(1);         
        if ($is_cref ){
            if (!($c = $ctrl::login($u, $pwd, false))) {            
                igk_ilog('controller login success');
                $ctrl->setParam("failed_log", 1);
                $redirect = $this->get_login_failed_redirect_uri($redirect);       
            }else{
                igk_ilog('login success: '.$u, 'FORMLOGIN');
                
            }
        } else {
            igk_wln('no cref');
            igk_ilog('cref not valid:', 'FORMLOGIN');
            $redirect = $ctrl::uri($this->serviceLoginSigninView);
            $this->notify(__('you failed to sign in - please try again later or contact the webmaster'),
            'igk-danger');
        } 
       
    }
    public function logout(){
        $this->getController()->logout(1);        
        igk_navto($this->getController()->uri(''));
    }
}
