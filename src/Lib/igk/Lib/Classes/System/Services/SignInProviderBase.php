<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SignInProviderBase.php
// @date: 20220607 19:41:35
// @desc: autor base


namespace IGK\System\Services;
 
use IGKEvents;

/**
 * provider base class
 * @package IGK\System\Services
 */
abstract class SignInProviderBase{
    use SignInProviderTrait; 
    /**
     * navigate on login
     * @var true
     */
    protected $navigate_onlogin = true;

    protected $response;

    /**
     * url used for success
     * @var mixed
     */
    protected $successURL;

    public function setSuccessURL(?string $uri=null){
        $this->successURL = $uri;
    }
    public function getSuccessURL(){
        return $this->successURL;
    }

    public function getResponse(){
        return $this->response;
    }
    /**
     * set the provider reponse data
     * @param mixed $value 
     * @return void 
     */
    public function setResponse($value){
        $this->response = $value;

    }
}