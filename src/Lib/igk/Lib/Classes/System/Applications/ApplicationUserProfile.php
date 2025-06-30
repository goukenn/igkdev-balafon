<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationUserProfile.php
// @date: 20230129 13:34:47
namespace IGK\System\Applications;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\ViewHelper;
use IGK\Models\ModelBase as ModelsModelBase;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Database\ICustomUserProfile;
use IGK\System\SystemUserProfile;
use IGKUserInfo;

///<summary></summary>
/**
* 
* @package IGK\System\Application
*/
class ApplicationUserProfile extends SystemUserProfile implements ICustomUserProfile{

    private $m_user;
    private $m_app_user; 

    protected function registerProfile() { }

    public function user(): ModelsModelBase {
        return $this->m_app_user;
     } 
    /**
     * use use info profile 
     * @param mixed $userInfo 
     * @return void 
     */
    public function setUserInfo($userInfo) {
        $this->m_profile = $userInfo;
     }

    public function getUserInfo(){ 
        return $this->m_profile;
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

    /**
     * construct use model 
     * @param Users $user 
     * @return void 
     * @throws Exception 
     */
    public function __construct(Users $user, ?BaseController $ctrl=null, ?IGKUserInfo $profile = null) {
        Users::IsMockInstance($user) && igk_die('mock instance not allowed');
        $ctrl = $ctrl ?? igk_current_ctrl();
        $this->m_user = $user; 
        $this->m_controller = $ctrl;
        $this->m_profile = $profile ?? $ctrl->getUser();
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
    /**
     * get static class instance 
     * @param mixed $user 
     * @return static 
     */
    protected static function _CreateClassInstance($user){
        return new static($user, ...array_slice(func_get_args(),1));
    }
}