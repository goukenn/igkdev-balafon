<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UsersMacros.php
// @date: 20220803 13:48:57
// @desc: 



namespace IGK\Models\Macros;

use IGK\Controllers\BaseController;
use IGK\Models\Groups;
use IGK\Models\ModelBase;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGKEvents;
use IGKException;
use IGKSysUtil;
use IGKValidator;

/**
 * use macros
 * @package IGK\Models\Macros
 */
abstract class UsersMacros {
    /**
     * get user form id or guid
     * @param mixed $user_id 
     * @return null|Users 
     */
    public static function GetUserFromIdOrGuid(Users $model, $user_id){
        return (IGKValidator::IsGUID($user_id)) ?
             Users::get(Users::FD_CL_GUID, $user_id):
             Users::get(USers::FD_CL_ID, $user_id);
    }
    /**
     * get all active users
     * @return mixed 
     */
    public static function ActiveUsersArray(Users $model, ?array $options){
        return Users::select_all(['clStatus'=>1], $options);
    }
    /**
     * register user helper
     * @param Users $model 
     * @param object|array|IUserRegisterInfo $o 
     * @return ModelBase 
     * @throws IGKException 
     */
    public static function Register(Users $model, $o, ?BaseController $ctrl=null, callable $beforeHook=null){
  
        if (!is_array($o) && !is_object($o)){
            igk_die(__METHOD__." object not valid");
        }
        if (is_array($o)){
            $o = (object)$o;
        }
        if (empty($guid = igk_getv($o, Users::FD_CL_GUID))){
            $guid = igk_create_guid();
            igk_setv($o, Users::FD_CL_GUID, $guid);
        }
        if (empty($pwd = igk_getv($o, Users::FD_CL_PWD))){
            $pwd = sha1( IGK_PWD_PREFIX. date("Ymd").microtime(true));
            igk_setv($o, Users::FD_CL_PWD, $pwd);
        }
 
        if (($login = igk_getv($o, Users::FD_CL_LOGIN))){
            if ( $model::select_row([Users::FD_CL_LOGIN=>$login])){    
                return false;
            }
        }
        if (empty(igk_getv($o, Users::FD_CL_CLASS_NAME) ) && $ctrl )
            igk_setv($o, Users::FD_CL_CLASS_NAME, get_class($ctrl));

  
        if ($r = $model::create($o)){  
            if ($beforeHook){
                $beforeHook($r);
            }  
            igk_hook(IGKEvents::HOOK_USER_ADDED, ["user"=>$r, "ctrl"=>$ctrl]);
        }  else {
            igk_dev_wln_e('failed to create = ', $o);
        }
        return $r;
    }

    /**
     * get group that this user is member of
     * @param Users $model 
     * @return mixed|array|null
     */
    public static function memberOf(Users $model){
        $mod = $model;
        if($mod->is_mock()){
            return null;
        }
        $gtable = Groups::table(); 
        // $ugtable =Usergroups::table(); 
        $c = Usergroups::prepare()
            ->join_left($mod->table(), Usergroups::column('clUser_Id').' = '.$mod->column('clId'))
            ->join_left($gtable, Groups::column('clId').' = '.Usergroups::column('clGroup_Id'))
            ->where(['clGuid'=>$mod->clGuid])
            ->distinct()
            ->columns([
                Groups::column('*'),
                // Groups::column('clId') => 'groupId',
                // Groups::column('clName') => 'groupName',
                // 'clLogin',
                // 'clGuid',
                // $mod->column('clId')
            ]
            )->orderBy([Groups::column('clId')."|ASC"]) 
            ->execute(false);
        if ($c){
            return $c->to_array();
        }
        return null; 
    }
    /**
     * get user groups name 
     * @param Users $model 
     * @return array<array-key, mixed> 
     */
    public static function getGroupNames(Users $model){
        return array_map(new \IGK\Mapping\PropertyMapper(Groups::FD_CL_NAME), $model->groups());
    }
    public static function getAuthorizationNames(Users $model){
        return array_map(new \IGK\Mapping\PropertyMapper(Groups::FD_CL_NAME), $model->auths());
    }
    /**
     * get user form guid :
     */
    public static function fromGuid(Users $model, string $guid){
        return $model->GetCache(Users::FD_CL_GUID, $guid);
    }

    public static function InitSystemUsers(){
        $d = igk_configs()->website_domain;
        $now = date(IGK_MYSQL_DATETIME_FORMAT);        
        $def_pwd = igk_configs()->get('default_adm_pwd', IGKSysUtil::GeneratePWD());
        Users::create(array(
            "clLogin" => "admin@" . $d,
            "clPwd" => $def_pwd,
            "clFirstName" => "admin",
            "clLastName" => "Administrator",
            "clDisplay" => "Admin",
            "clLocale" => "fr",
            "clLevel" => "-1",
            "clStatus" => 0,
            "clDate" => $now,
            "clGuid" => igk_create_guid()
        ));
        Users::create(array(
            "clLogin" => "test@" . $d,
            "clPwd" => $def_pwd,
            "clFirstName" => "test",
            "clLastName" => "test",
            "clLevel" => "1",
            "clStatus" => 0,
            "clDate" => $now,
            "clLocale" => "fr",
            "clGuid" => igk_create_guid()
        ));
        Users::create(array(
            "clLogin" => "info@" . $d,
            "clPwd" => $def_pwd,
            "clFirstName" => "info",
            "clLastName" => "info",
            "clLevel" => "1",
            "clStatus" => 0,
            "clDate" => $now,
            "clLocale" => "fr",
            "clGuid" => igk_create_guid()
        ));
        Users::create(array(
            "clLogin" => IGK_USER_LOGIN,
            "clPwd" => $def_pwd,
            "clFirstName" => "Charles",
            "clLastName" => "BONDJE DOUE",
            "clLevel" => "0",
            "clStatus" => 0,
            "clDate" => $now,
            "clLocale" => "en",
            "clGuid" => igk_create_guid(),
        ));
        Users::create(array(
            "clLogin" => "igk.system@igkdev.com",
            "clPwd" => $def_pwd, 
            "clFirstName" => "",
            "clLastName" => "IGKSystem",
            "clLevel" => "0",
            "clStatus" => 0,
            "clDate" => $now,
            "clLocale" => "fr",
            "clGuid" => igk_create_guid(),
        ));
    }
}