<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SystemUserProfile.php
// @date: 20220601 08:28:05
// @desc: user profile
namespace IGK\System;

use IGK\Helper\Activator;
use IGK\System\Database\IUserProfile;

/**
 * represent user profile
 * @package IGK\System
 */
class SystemUserProfile implements IUserProfile
{
    var $clFirstName;
    var $clGuid;
    var $clLogin;
    var $clLastName;
    var $clPicture;
    var $clDate;
    var $clLastLogin;
    var $clParent_Id;
    var $clDisplay;
    var $clLocale;
    var $clLevel;
    var $clId;
    /**
     * user info 
     * @var mixed
     */
    protected $m_profile;
    protected function __construct()
    {
    }

    public function auth($type): bool {
        return $this->m_profile->auth();
    }
    /**
     * create user profile from info
     * @param mixed $userInfo 
     * @return static 
     */
    public static function Create($userInfo)
    { 
        $c = Activator::CreateNewInstance(function () {
            return new static;
        }, $userInfo->to_array());
        $c->m_profile = $userInfo;
        $c->registerProfile();
        return $c;
    }
    /**
     * override this to register profile
     * @return void 
     */
    protected function registerProfile(){

    }

    /**
     * get current user profile
     * @param mixed $ctrl 
     * @return static 
     */
    public static function GetUserProfile($ctrl){
        return $ctrl->getUser();
    }
}
