<?php
// @file: IGKPageControllerBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\Controllers\ILibaryController;
use IGK\Helper\SysUtils;
use IGK\System\Applications\ApplicationUserProfile;
use IGK\System\Database\IUserProfile;
use IGK\System\EntryClassResolution;
use IGK\System\SystemUserProfile;
use IGKSession;
use IIGKUriActionRegistrableController;
use ReflectionClass;
/**
 * 
 * @package IGK\Controllers
 */
abstract class PageControllerBase extends ControllerTypeBase 
    implements IIGKUriActionRegistrableController, ILibaryController
{
    const PAGE_CONSTANT= IGK_USER_SETTING + 0xB;
    const PAGE_USER=self::PAGE_CONSTANT + 1;
    const PAGE_TEMPLATE=self::PAGE_CONSTANT + 2;
    /**
     * init view
     * */
    protected function _initView(){ 
        $this->register_autoload();
        parent::_initView();
    }
    /**
     * get buffer output
     * @return mixed 
     */
    public function get_output(){
        $s=$this->getEnvParam("_output");
        return $s;
    }
    public function getTableConst($n){
        $cl=get_class($this)."DbConstants";
        if(class_exists($cl, false)){
            $consts=(igk_sys_reflect_class($cl))->getConstants();
            return igk_getv($consts, $n, $n);
        }
        return $n;
    }
    protected function getUserDir(){
        if($u=$this->User)
            return $this->getDataDir()."/users/".$u->clLogin;
        return null;
    }
    protected function getUserSettingFile(){
        if($u=$this->User){
            return $this->getUserDir()."/.settings.xml";
        }
        return null;
    }
    protected function getUserSettings(){
        $settings=$this->getEnvParam(self::ENV_PARAM_USER_SETTINGS);
        if($settings)
            return $settings;
        $udir=$this->getUserDir();
        if(igk_io_file_exists($file=$this->getUserSettingFile()) && ($g=igk_conf_load_file($file, IGK_CNF_TAG))){
            $settings=igk_createObjStorage((array)$g);
        }
        else
            $settings=igk_createObjStorage();
        $this->setEnvParam(self::ENV_PARAM_USER_SETTINGS, $settings);
        return $settings;
    }
    public function handle_redirection_uri($u, $forcehandle=1){
        return false;
    }
    public static function HandlePage($ctrl, $view){
        return $ctrl->handleView($view);
    }
    protected function handleView($view){
        $f=$this->getViewFile($view);
        if(igk_io_file_exists($f) && method_exists($this, "renderDefaultDoc")){
            $this->renderDefaultDoc($view, null, true);
            igk_exit();
            return 1;
        }
        return 0;
    }
    /**
     * 
     * @param object $u 
     * @return IUserProfile 
     */
    protected function initUserFromSysUser(object $u): \IGK\System\Database\IUserProfile{
        if (!is_null($u)){ 
            $cl = $this->resolveClass(EntryClassResolution::UserProfile);
            if ($cl && class_exists($cl) && (is_subclass_of($cl, SystemUserProfile::class))){
                return $cl::Create($u, $this);
            }
            $profile = $this->getUser();
            $u = new ApplicationUserProfile($u->model(), $this, $profile);
        }
        return $u;
    }
    public function navtohome(){
        $this->resetCurrentView();
        $c=$this->getAppUri();
        igk_navto($c);
    } 
    protected function storeUserSettings(){
        $settings=$this->getUserSettings();
        if($settings && ($file=$this->getUserSettingFile())){
            igk_io_store_conf($file, $settings, IGK_CNF_TAG);
            return 1;
        }
        return 0;
    }
    // public final function updateDb(){
    //     $s=igk_is_conf_connected() || igk_user()->auth($this->Name.":".__FUNCTION__);
    //     if(!$s){
    //         igk_ilog("// not authorize to updateDb of " + $this->getName());
    //         igk_navto($this->getAppUri());
    //     }
    //     igk_db_update_ctrl_db($this);
    //     $uri=$this->getAppUri();
    //     igk_navto($uri);
    //     igk_exit();
    // }
    ///<summary> get a application document. getDoc return the global document </summary>
    /**
    *  get a application document. getDoc return the global document
    */
    protected function getAppDocument($newdoc=false){
        return igk_get_document($this::name("app_document"), $newdoc);
    }
}