<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserProfileBase.php
// @date: 20221113 10:22:28
namespace IGK\System\Database;

use IGK\Controllers\BaseController;
use IGK\Helper\StringUtility;  
use IGK\Models\ModelBase; 
use IGK\Models\Users;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
abstract class UserProfileBase implements ICustomUserProfile{
    protected $m_info;
    protected $m_model;
    protected $m_controller;

    public function setUserInfo($userInfo) {
        $this->m_info = $userInfo;
    }   
    public function getUserInfo()
    {
        return $this->m_info;
    }
    /**
     * return the model attached to this UserProfile
     * @return mixed 
     */
    public function model(): \IGK\Models\Users{        
        return $this->m_model;
    }
    /**
     * return the system model 
     * @return null|Users 
     */
    public function systemModel() : ?\IGK\Models\Users{
        return $this->m_info->model();
    }
    /**
     * bind user info
     * @param Users|IGKUserInfo $userInfo 
     * @param ModelBase $app_user 
     * @return void 
     */
    public function bindInfo($userInfo, ModelBase $app_user): ?ICustomUserProfile{
        if (is_null($userInfo)){
            igk_die("can't bind user to null");
        }        
        $this->m_info = $userInfo;
        $this->m_model = $userInfo->model(); 
        $this->m_controller = $this->m_model->getController();
        return $this;
    }
    public function getController(): ?BaseController
    {
        return $this->m_controller;
    }
    /**
     * get auth list
     * @return mixed 
     */
    public function auths(){
        return $this->systemModel()->auths();
    }
    /**
     * check user auth 
     * @param mixed $auth 
     * @return bool 
     */
    public function auth($auth, bool $strict=false, ?BaseController $ctrl=null):bool{
        return $this->systemModel()->auth($auth, $strict, $ctrl);
    }
    public function __toString()
    {
        return $this->m_model->to_json();
    }
    /**
     * get the profile display
     * @return string 
     */
    public function display(): ?string{
        return StringUtility::DisplayName(
            $this->m_info->clFirstName, 
            $this->m_info->clLastName
        );
    }
    public function __call($n, $args){
        if (method_exists($this ,  $fc = 'get'.ucfirst($n))){
            return $this->$fc(...$args);
        }
        return $this->m_info->{'cl'.ucfirst($n)};
    }
    public function __get($n){
        return $this->m_info->$n;
    }
    public function save(){
        return $this->m_model->save();
    }

    /**
     * get list of groups 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function memberOf(){
        $mod = $this->systemModel(); 
        return $mod->memberOf(); 
    }
    
}