<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationUserProfileTrait.php
// @date: 20221208 17:31:33
namespace IGK\Controllers\Traits;

 
use IGK\System\Database\ICustomUserProfile;
use IGK\System\Database\IUserProfile;
use IGK\Models\ModelBase as coreModelBase;
use IGK\System\EntryClassResolution;

///<summary></summary>
/**
* 
* @package IGK\Controllers\Traits
*/
trait ApplicationUserProfileTrait{
    /**
     * get class used to serve application used
     * @return ?string
     */
    protected function getApplicationUserModel(): ?string{
        return $this->resolveClass(EntryClassResolution::Models."/Users");
    }
    /**
     * 
     * @return array 
     */
    protected function createApplicationUserInfo(){
        return [];
    }
     /**
     * 
     * @param null|object $u use info
     * @return null|IUserProfile 
     * @throws BindingResolutionException 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function initUserFromSysUser(object $u): IUserProfile
    { 
        if (!$u || !$u->clGuid) {
            return null;
        }  
        // $this::register_autoload();
        $model = $this->getApplicationUserModel();       
        if (!$model){
            return $u;
        }
        $model = $this->model($model);

        $profile_class = $this->resolveClass(EntryClassResolution::UserProfile) ?? \IGK\System\Applications\ApplicationUserProfile::class;
        $key = $model->getPrimaryKey();
        if (method_exists($this, 'getInitFormSysUserCondition')){
            $condition = $this->getInitFormSysUserCondition($u);
        }else{
            $condition = [$key=>$u->clGuid];
        }

        return $this->createCustomUserProfile(
            $u,
            $profile_class,
            $model, 
            $condition,
            function()use($u){
               return $this->createApplicationUserInfo($u);              
            }            
        );
    }
    /**
     * 
     * @param mixed $userInfo 
     * @param string $profileClassName 
     * @param coreModelBase $customModel 
     * @param mixed $condition 
     * @param mixed $newDefinition 
     * @return null|IGK\System\Database\ICustomUserProfile 
     * @throws BindingResolutionException  
     * @throws ContainerExceptionInterface  
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function createCustomUserProfile(
        $userInfo,
        string $profileClassName,
        coreModelBase $customModel,
        $condition,
        $newDefinition
    ): ?ICustomUserProfile {
        if (!$profileClassName && !class_exists($profileClassName)) {
            return null;
        }
        $coreuser = $userInfo->model();
        $c = new $profileClassName($coreuser);
        $roles = $this->resolveClass(EntryClassResolution::Roles); 
        // check that the user exists
        $row = $customModel::select_row($condition);
       
        $m = null;
        if ($row){
            if (!$coreuser->memberOf()){
                $roles::InitRole($this, $coreuser);              
            } 
            $m = $c->bindInfo($userInfo, $row); 
        } else {
            // register user to member list; 
            if (is_callable($newDefinition)){
                $newDefinition = $newDefinition();
            } 
            $row = $customModel::createIfNotExists($condition, $newDefinition); 
            if (!$row) {
                igk_die(__("failed to register current user"));
            }
            if ($row->isNew()){
                $roles::InitRole($this, $coreuser );  
            }
            $m = $c->bindInfo($userInfo, $row );
        }
        if ($m === null) {
            igk_notifyctrl()->addError(__("not a member"));
            return null;
        } 
        return $c;
    } 
}