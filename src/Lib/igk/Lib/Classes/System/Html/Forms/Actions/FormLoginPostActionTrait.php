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

/**
 * form login action trait
 */
trait FormLoginPostActionTrait
{
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
        $query = [];
        if (!empty($redirect)){
            $query["continue"] =urlencode($redirect);
        }
        $q = count($query)>0 ? "?".http_build_query($query) : "";
        return $this->getController()->getAppUri("signin".$q);
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
        $g = $this->getController()->checkUser(false);
        $redirect =  igk_getr("continue", $this->get_login_redirect_uri());
        $pwd = igk_getr("password");
        $u = igk_getr("login");
        $is_cref = igk_valid_cref(1);
        if ($is_cref && !($c = $this->getController()->login($u, $pwd, false))) {
            $this->getController()->setParam("failed_log", 1);
            $redirect = $this->get_login_failed_redirect_uri($redirect);       
        }
        if (!empty($redirect)) {
            igk_navto($redirect);
        } 
    }
}
