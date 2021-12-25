<?php
// @file: IGKPageControllerBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGK\Controllers\ILibaryController; 
use IGKSession;
use IIGKUriActionRegistrableController;
use ReflectionClass;


/**
 * 
 * @package IGK\Controllers
 */
abstract class PageControllerBase extends ControllerTypeBase
 
implements IIGKUriActionRegistrableController, ILibaryController{

    const PAGE_CONSTANT=IGKSession::BASE_SESS_PARAM + 0xA0;
    const PAGE_TEMPLATE=self::PAGE_CONSTANT + 2;
    const PAGE_USER=self::PAGE_CONSTANT + 1;

    ///<summary>init view</summary>
    /**
     * init view
     * */
    protected function _initView(){ 
        $this->register_autoload();        
        parent::_initView();
    }
    ///<summary>get buffer output</summary>
    /**
     * get buffer output
     * @return mixed 
     */
    public function get_output(){
        $s=$this->getEnvParam("_output");
        return $s;
    }
    ///<summary>Represente getTableConst function</summary>
    ///<param name="n"></param>
    public function getTableConst($n){
        $cl=get_class($this)."DbConstants";
        if(class_exists($cl, false)){
            $consts=(igk_sys_reflect_class($cl))->getConstants();
            return igk_getv($consts, $n, $n);
        }
        return $n;
    }
    ///<summary></summary>
    public function getUser(){
        return $this->getEnvParam(self::PAGE_USER);
    }
    ///<summary></summary>
    protected function getUserDir(){
        if($u=$this->User)
            return $this->getDataDir()."/users/".$u->clLogin;
        return null;
    }
    ///<summary></summary>
    protected function getUserSettingFile(){
        if($u=$this->User){
            return $this->getUserDir()."/.settings.xml";
        }
        return null;
    }
    ///<summary></summary>
    protected function getUserSettings(){
        $settings=$this->getEnvParam(self::ENV_PARAM_USER_SETTINGS);
        if($settings)
            return $settings;
        $udir=$this->getUserDir();
        if(file_exists($file=$this->getUserSettingFile()) && ($g=igk_conf_load_file($file, IGK_CNF_TAG))){
            $settings=igk_createObjStorage((array)$g);
        }
        else
            $settings=igk_createObjStorage();
        $this->setEnvParam(self::ENV_PARAM_USER_SETTINGS, $settings);
        return $settings;
    }
    ///<summary></summary>
    ///<param name="u"></param>
    ///<param name="forcehandle" default="1"></param>
    public function handle_redirection_uri($u, $forcehandle=1){
        return false;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="view"></param>
    public static function HandlePage($ctrl, $view){
        return $ctrl->handleView($view);
    }
    ///<summary>override this to handle page</summary>
    protected function handleView($view){
        $f=$this->getViewFile($view);
        if(file_exists($f) && method_exists($this, "renderDefaultDoc")){
            $this->renderDefaultDoc($view, null, true);
            igk_exit();
            return 1;
        }
        return 0;
    }
    ///<summary>init app's. override this method to initialize user app's environment</summary>
    ///<remark>in general you must load app environment setting and store it in $user->EnvParam["app:://Name/setting"]</remark>
    protected function initUserFromSysUser($u){
        return $u;
    }
    ///<summary></summary>
    public function navtohome(){
        $this->resetCurrentView();
        $c=$this->getAppUri();
        igk_navto($c);
    }
    
    ///<summary></summary>
    ///<param name="v"></param>
    public function set_output($v){
        $s=$this->getEnvParam("_output");
        if(!$s){
            $s=$v;
        }
        else{
            $s=$v;
        }
        $this->setEnvParam("_output", $s);
    }
    ///<summary></summary>
    ///<param name="user"></param>
    protected function setUser($user){
        $this->setEnvParam(self::PAGE_USER, $user);
    }
    ///<summary></summary>
    protected function storeUserSettings(){
        $settings=$this->getUserSettings();
        if($settings && ($file=$this->getUserSettingFile())){
            igk_io_store_conf($file, $settings, IGK_CNF_TAG);
            return 1;
        }
        return 0;
    }
    ///<summary>update the current data base</summary>
    public final function updateDb(){
        $s=igk_is_conf_connected() || igk_user()->auth($this->Name.":".__FUNCTION__);
        if(!$s){
            igk_ilog("// not authorize to updateDb of " + $this->getName());
            igk_navto($this->getAppUri());
        }
        igk_db_update_ctrl_db($this);
        $uri=$this->getAppUri();
        igk_navto($uri);
        igk_exit();
    }

    ///<summary> get a application document. getDoc return the global document </summary>
    /**
    *  get a application document. getDoc return the global document
    */
    protected function getAppDocument($newdoc=false){
        return igk_get_document($this::name("app_document"), $newdoc);
    }
}
