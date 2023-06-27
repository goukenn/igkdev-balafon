<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationUserProfile.php
// @date: 20230129 13:34:47
namespace IGK\System\Applications;

use IGK\Controllers\BaseController;
use IGK\Models\ModelBase as ModelsModelBase;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Database\ICustomUserProfile;
use IGK\System\SystemUserProfile;
 

///<summary></summary>
/**
* 
* @package IGK\System\Application
*/
class ApplicationUserProfile extends SystemUserProfile implements ICustomUserProfile{

    private $m_user;
    private $m_app_user;
    protected $m_controller;

    protected function registerProfile() { }

    public function user(): ModelsModelBase {
        return $this->m_app_user;
     } 

    public function setUserInfo($userInfo) {
        $this->m_profile = $userInfo;
     }

    public function getUserInfo(){ 
        return $this->m_profile;
    }
    public function getController(): ?BaseController
    {
        return $this->m_controller;
    }
    /**
     * get project user
     * @return mixed 
     */
    public function getApplicationUser():?ModelBase{
        return $this->m_app_user;
    }
    public function model(): Users { 
        return $this->m_user;
    }

    public function __construct(Users $user)
    {
        Users::IsMockInstance($user) && igk_die('mock instance not allowed');
        $this->m_user = $user; 
        parent::__construct();
    }
    /**
     * bind info or null if semthing bad append 
     * @param mixed $userProfile 
     * @param mixed $appUser 
     * @return null|ICustomUserProfile 
     */
    public function bindInfo($userProfile, $appUser): ?ICustomUserProfile{ 
        $this->setUserInfo($userProfile);
        $this->m_app_user = $appUser;
        $this->m_controller = $appUser->getController();
        return $this;
    }
}