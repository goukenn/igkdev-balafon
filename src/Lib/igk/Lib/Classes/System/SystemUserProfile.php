<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SystemUserProfile.php
// @date: 20220601 08:28:05
// @desc: user profile
namespace IGK\System;

use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\Models\Users;
use IGK\System\Database\IUserProfile;

/**
 * represent user profile
 * @package IGK\System
 */
abstract class SystemUserProfile implements IUserProfile
{ 
    /**
     * resolved user info 
     * @var mixed
     */
    protected $m_profile;
    /**
     * system model
     * @var mixed
     */
    protected $m_model;

    /**
     * get the controller 
     * @var mixed
     */
    protected $m_controller;
    /**
     * 
     * @return void 
     */
    protected function __construct()
    {
    }

    public function getController(): ?BaseController {
        return $this->m_controller;
    }

    /**
     * check auth 
     * @param mixed $type 
     * @return bool 
     */
    public function auth($type): bool {
        return $this->m_profile->auth($type);
    }
    /**
     * get the model class 
     * @return Users 
     */
    public function model(): \IGK\Models\Users{
        if (!($this->m_model)|| ($this->m_model->is_mock())){
            return null;
        }
        return $this->m_model;
    }
    /**
     * create user profile from info
     * @param mixed $userInfo 
     * @return static 
     */
    public static function Create($userInfo, BaseController $controller)
    {   
        if (is_null($userInfo)){
            return null;
        }
        if (static::class == __CLASS__)
            igk_die('not allowed to create user profile');


        $c = Activator::CreateNewInstance(function () {
            return new static;
        }, $userInfo->to_array());
        $c->m_profile = $userInfo;
        $c->m_model = $userInfo->model();
        $c->m_controller = $controller;
        $c->registerProfile();
        return $c;
    }
   

    /**
     * get current user profile
     * @param mixed $ctrl 
     * @return static 
     */
    public static function GetUserProfile($ctrl){
        return $ctrl->getUser();
    }
    /**
     * to string name profile
     * @return mixed 
     */
    public function __toString()
    {
        return $this->clLogin;
    }
    /** display full name */
    public function display(){
        return implode(' ', array_filter([$this->clFirstName, $this->clLastName]));
    }
    public function __get($name){
        if ($this->m_profile){
            return igk_getv($this->m_profile, $name);
        }
    }
    /**
     * register a user profile with initial profile setting
     * @return mixed 
     */
    protected abstract function registerProfile();
}
