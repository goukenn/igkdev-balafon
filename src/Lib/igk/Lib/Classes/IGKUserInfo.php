<?php
// @file: IGKUserInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\IToArray;
use IGK\System\Traits\StoredPropertiesTrait;

class IGKUserInfo extends IGKObject implements IToArray{
    const DB_INFO_KEY="sys://db/info";
    var $clId;
    var $clLogin;
    var $clGuid;
    // var $clPwd;
    var $csrf;
    use StoredPropertiesTrait;

    ///<summary></summary>
    public function __construct(){    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="value"></param>
    public function __set($name, $value){
        if(!$this->_setIn($name, $value))
            $this->setProperty($name, $value);
    }
    public function __get($key){
        if(method_exists($this, $fc = "get".ucfirst($key))){ 
            return call_user_func(array($this, $fc), array_slice(func_get_args(), 1));
        }
        return $this->getProperty($key);
    }
    ///<summary>Represente auth function</summary>
    ///<param name="name"></param>
    ///<param name="strict" default="false"></param>
    /**
     * 
     * @param string|array $name 
     * @param bool $strict 
     * @return mixed 
     */
    public function auth($name, $strict=false){      
        
        return $this->model()->auth($name, $strict);
    }
    ///<summary>Represente fullname function</summary>
    public function fullname(){
        return igk_user_fullname($this);
    }
    ///get all available authorisation for this user
    public function getAuths(){
        // if (igk_environment()->isDev()){
        //     /// TASK: handle authorisation for all
        //     return true;
        // }
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
    ///<summary></summary>
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
    ///<summary></summary>
    ///<param name="uinfo"></param>
    ///<param name="authname"></param>
    ///<param name="authCtrl" default="null"></param>
    ///<param name="adapter" default="IGK_MYSQL_DATAADAPTER"></param>
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
    ///<summary></summary>
    ///<param name="authname"></param>
    ///<param name="authCtrl" default="null"></param>
    ///<param name="adapter" default="IGK_MYSQL_DATAADAPTER"></param>
    public final function IsAuthorize($authname, $authCtrl=null, $adapter=IGK_MYSQL_DATAADAPTER){
        $s=$this;
        return self::GetIsAuthorize($s, $authname, $authCtrl, $adapter);
    }
    ///<summary></summary>
    ///<param name="userTableData"></param>
    public function loadData($userTableData){
        if($userTableData){
            foreach($userTableData as $k=>$v){
                $this->$k=$v;
            }
        }
    }
    ///<summary>Represente to_json function</summary>
    public function to_json(){
        return json_encode($this);
    }
    ///<summary></summary>
    public function toString(){
        return get_class($this);
    }
    public function to_array():?array{
        return (array)$this;
    }
    /**
     * retrieve the model
     * @return object|null 
     */
    public function model(){
        return IGK\Models\Users::createFromCache($this, (object)['clGuid'=>$this->clGuid], []);
    }
}
