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
    const profileModelClass=null;
    const initProjectDbUserMethod = 'initProjectDbUser';
    /**
     * 
     * @var mixed
     */
    protected $m_projectUser;
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
    /**
     * retrieve the controller 
     * @return null|BaseController 
     */
    public function getController(): ?BaseController {
        return $this->m_controller;
    }
    /**
     * check auth 
     * @param array|string $type 
     * @param bool $strict if array must match all requirement
     * @return bool 
     */
    public function auth($type, bool $strict=true, ?BaseController $ctrl=null): bool {        
        return $this->m_profile->auth($type, $strict, $ctrl);
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
        $c = Activator::CreateNewInstance(function () use ($userInfo, $controller) {
            return static::_CreateClassInstance($userInfo->model(), $controller);
            //return new static;
        }, $userInfo->to_array());
        $c->m_profile = $userInfo;
        $c->m_model = $userInfo->model();
        $c->m_controller = $controller;
        $c->registerProfile(); 
        return $c;
    }
    /**
     * 
     */
    protected static function _CreateClassInstance(Users $u) { 
        $l = new static;
        $v_user = null;
        if ($model_class = static::profileModelClass){
            if (method_exists($l, $fc = self::initProjectDbUserMethod)){
                call_user_func_array([$l, $fc], [$u]);  
            }else{
                list($column, $prop) = $l->getdbCacheColumnList($model_class);
                if (is_null($v_user = $model_class::GetCache($column, $u->{$prop}))){
                    $v_user = $l->createNewProjectUser($u, $model_class);
                }
                ($l->m_projectUser = $v_user) || igk_die('failed to register project user');
            }
        } 
        return $l;
    }
    /**
     * 
     * @param Users $user 
     * @param string $model_class 
     * @return mixed 
     */
    protected function createNewProjectUser(Users $user, string $model_class){
        return $model_class::insertIfNotExists($user->to_array());
    }
    /**
     * 
     * @param mixed $smodel_class 
     * @return (mixed|string)[] 
     */
    protected function getdbCacheColumnList($smodel_class){
        $column = $smodel_class::FD_USER_ID;
        $prop = IGK_FD_GUID;
        return [
            $column,
            $prop
        ];
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
     * register a user profile with initial profile setting. bind to group or user 
     * @return mixed 
     */
    protected abstract function registerProfile();
}