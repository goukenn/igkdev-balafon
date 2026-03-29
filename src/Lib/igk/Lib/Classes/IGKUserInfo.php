<?php
// @file: IGKUserInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Controllers\BaseController;
use IGK\Database\Helpers\AuthorizationHelper;
use IGK\Models\Caches\CacheModels;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Database\IUserProfile;
use IGK\System\IToArray;
use IGK\System\Traits\StoredPropertiesTrait;
/**
* Igkuser info.
*/
class IGKUserInfo extends IGKObject implements IToArray{
    /**
    * Constant: db info key.
    * @var mixed
    */
    const DB_INFO_KEY="sys://db/info";
    /**
    * Identifier: cl id.
    * @var mixed
    */
    var $clId;
    /**
    * Property: cl login.
    * @var mixed
    */
    var $clLogin;
    /**
    * Identifier: cl guid.
    * @var mixed
    */
    var $clGuid;
    // var $clPwd;
    /**
    * Property: csrf.
    * @var mixed
    */
    var $csrf;
    use StoredPropertiesTrait;
    /**
    * .ctr
    */
    public function __construct(){    }
    /**
    * Magic setter for dynamic properties.
    * @param mixed $name
    * @param mixed $value
    */
    public function __set($name, $value){
        if(!$this->_setIn($name, $value))
            $this->setProperty($name, $value);
    }
    /**
    * Magic getter for dynamic properties.
    * @param mixed $key
    */
    public function __get($key){
        if(method_exists($this, $fc = "get".ucfirst($key))){ 
            return call_user_func(array($this, $fc), array_slice(func_get_args(), 1));
        }
        return $this->getProperty($key);
    }
    /**
    * auto generate doc.
    * @param ?BaseController $ctrl current - load controller
    * @return mixed
    */
    public function auth($name, $strict=false, $ctrl=null){   
        $name = AuthorizationHelper::Map($name, $ctrl);
        return $this->model()->auth($name, $strict);
    }
    /**
    * Fullname.
    */
    public function fullname(){
        return igk_user_fullname($this);
    }
    /**
     * display user info
     * @return mixed 
     * @throws Exception 
     */
    public function display():string{
        if (strlen(trim( $s = $this->fullname())) == 0){
            $s = $this->clLogin;
        }
        return $s;
    }
    ///get all available authorisation for this user
    /**
    * Returns Auths.
    */
    public function getAuths(){ 
        if($this->clId){
            $tab=array();
            $db=igk_db_table_select_where($this->usergrouptable, array(IGK_FD_USER_ID=>$this->clId));
            foreach($db->Rows as $v){
                $rdb=igk_db_table_select_where($this->groupauthtable, array(IGK_FD_GROUP_ID=>$v->clGroup_Id));
                if($rdb){
                    foreach($rdb->Rows as $b){
                        if(!isset($tab[$b->clAuthId])){
                            $authinfo=igk_db_table_select_row($this->authtable, array(IGK_FD_ID=>$b->clAuthId));
                            $authinfo->clGrant=$b->clGrant;
                            $tab[$b->clAuthId]=$authinfo;
                        }
                        else
                            $tab[$b->clAuthId]->clGrant=$tab[$b->clAuthId]->clGrant && $b->clGrant;
                    }
                }
            }
            return $tab;
        }
        return null;
    }
    /**
    * Returns Groups.
    */
    public function getGroups(){
        if($this->clId){
            $tab=array();
            $db=igk_db_table_select_where($this->usergrouptable, array(IGK_FD_USER_ID=>$this->clId));
            foreach($db->Rows as $v){
                $rdb=igk_db_table_select_where($this->grouptable, array(IGK_FD_ID=>$v->clGroup_Id))->getRowAtIndex(0);
                if($rdb){
                    $tab[$rdb->clName]=$rdb;
                }
            }
            return $tab;
        }
        return null;
    }
    /**
    * Returns Is Authorize.
    * @param mixed $uinfo
    * @param mixed $authname
    * @param mixed $strict
    * @param null|mixed $authCtrl
    * @param mixed $adapter
    */
    public static function GetIsAuthorize($uinfo, $authname, $strict=false, $authCtrl=null, $adapter=IGK_MYSQL_DATAADAPTER){
        $s=$uinfo;
        $k=self::DB_INFO_KEY;
        $v_authtable=$s->$k->authtable;
        $v_grouptable=$s->$k->grouptable;
        $v_usergrouptable=$s->$k->usergrouptable;
        $v_groupauthtable=$s->$k->groupauthtable;
        if($authCtrl !== null){
            $v_authtable=$authCtrl->AuthTable;
            $v_grouptable=$authCtrl->GroupTable;
            $v_usergrouptable=$authCtrl->UserGroupTable;
            $v_groupauthtable=$authCtrl->GroupAthTable;
        }
        return igk_db_is_user_authorized($uinfo, $authname, $strict, $v_authtable, $v_usergrouptable, $v_groupauthtable);
    }
    public final
    /**
    * Returns true if Authorize.
    * @param mixed $authname
    * @param null|mixed $authCtrl
    * @param mixed $adapter
    */
    function IsAuthorize($authname, $authCtrl=null, $adapter=IGK_MYSQL_DATAADAPTER){
        $s=$this;
        return self::GetIsAuthorize($s, $authname, $authCtrl, $adapter);
    }
    /**
    * Loads Data.
    * @param mixed $userTableData
    */
    public function loadData($userTableData){
        if($userTableData){
            foreach($userTableData as $k=>$v){
                $this->$k=$v;
            }
        }
    }
    /**
    * To json.
    */
    public function to_json(){
        return json_encode($this);
    }
    /**
    * To string.
    */
    public function toString(){
        return get_class($this);
    }
    /**
    * To array.
    * @return ?array
    */
    public function to_array():?array{
        return (array)$this;
    }
    /**
     * retrieve the model
     * @return object|null 
     */
    public function model(){
        $model = IGK\Models\Users::model();
        $key = CacheModels::GetCacheKey($model, Users::FD_CL_GUID, $this->clGuid);
        if ($o = CacheModels::Get($key)) {
            return $o;
        } 
        return IGK\Models\Users::createFromCache($this, ['clGuid'=>$this->clGuid], []);
    }
}