<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UsersMacros.php
// @date: 20220803 13:48:57
// @desc: 



namespace IGK\Models\Macros;

use IGK\Models\Groups;
use IGK\Models\ModelBase;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGKException;

/**
 * use macros
 * @package IGK\Models\Macros
 */
class UsersMacros {
    /**
     * register user helpers
     * @param Users $model 
     * @param object|array|IUserRegisterInfo $o 
     * @return ModelBase 
     * @throws IGKException 
     */
    public static function register(Users $model, $o){
        if (!is_array($o) && !is_object($o)){
            igk_die(__METHOD__." object not valid");
        }
        if (empty($guid = igk_getv($o, "clGuid"))){
            $guid = igk_create_guid();
            igk_setv($o, "clGuid", $guid);
        }
        if (empty($pwd = igk_getv($o, "clPwd"))){
            $pwd = sha1( IGK_PWD_PREFIX. date("Ymd").microtime(true));
            igk_setv($o, "clPwd", $pwd);
        }
        if ($login = igk_getv($o, "clLogin")){
            if ($model::select_row(["clLogin"=>$login])){
                return false;
            }
        }
        return $model::create($o);
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
}