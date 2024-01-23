<?php

namespace IGK\Database\Macros;

use GrahamCampbell\ResultType\Success;
use IGK\Controllers\BaseController;
use IGK\Database\Mapping\SysDbMapping;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
use IGK\Models\PhoneBookEntries;
use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\PhoneBookUserAssociations;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\PhoneBook\PhoneBookEntry;
use IGK\System\Constants\PhonebookTypeNames;
use IGK\System\Database\MySQL\BooleanQueryResult;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;


/**
 * used for macros injection 
 * @package IGK\Database\Macros
 */
abstract class UsersMacros
{

    /**
     * register and init project user by login
     * @param string $login 
     * @param BaseController $ctrl 
     * @return mixed 
     */
    static function RegisterAndInitProjectUserByLogin(string $login,BaseController $ctrl ){
        if ($u = Users::Register(['clLogin'=>$login])){ 
            $ctrl->initUserFromSysUser($u); 
            return $u;
        }
    }
    /**
     * get list of user's authorization
     * @param Users $user 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function auths(Users $user){
        if ($user->is_mock()){
            igk_die('auths method not allowed');
        }
        $joint = [
            Groupauthorizations::table()=>[
                sprintf('%s=%s', Groupauthorizations::column("clGroup_Id"),Usergroups::column("clGroup_Id"))
            ],
            Authorizations::table()=>[sprintf('%s=%s', Authorizations::column("clId"),Groupauthorizations::column("clAuth_Id"))],
        ]; 
        $g = Usergroups::prepare()
        ->join($joint)
        ->distinct(true)
        ->where([
            'clGrant'=>1,
            Usergroups::column('clUser_Id')=>$user->clId
        ])
        ->columns([
            Authorizations::column("clName")=>"name",
            Authorizations::column("clController")=>"controller",
            //Authorizations::column("*"),
        ])
        ->orderBy(["name"])
        ->execute();
        if ($g){
            return $g->to_array();
        } 
    }
    /**
     * activate user
     * @param Users $user 
     * @param string $newPassword 
     * @return bool 
     */
    public static function activate(Users $user)
    {
        $user->clStatus = 1;
        unset($user->clPwd);
        return $user->save();
    }

    public static function isActive(Users $user){
        return $user->clStatus == 1;
    }
    /**
     * set user password
     * @param Users $user 
     * @param string $newPassword 
     * @return null|IGK\Models\IIGKQueryResult 
     */
    public static function changePassword(Users $user, string $newPassword)
    {
        if ($user->is_mock()){ igk_die(__METHOD__.": mock is not allowed");}
        return  \IGK\Models\Users::update([
            'clPwd' => $newPassword,
        ], ['clGuid' => $user->clGuid]);
    }
    // + | -----------------------------------------------------------    
    // + | phone book macros
    // + |
    public static function addPhoneBookEntry(Users $model, $type, $value)
    {
        $r = static::getPhoneBookEntry($model);

        $guid = ($r ? $r->usrphb_PhoneBookEntryGuid : null) ?? PhoneBookEntries::create()->rcphbe_Guid;

        if (($r && !$r->usrphb_PhoneBookEntryGuid) && ($guid)) {
            $r->usrphb_PhoneBookEntryGuid = $guid;
            $r->save();
        }

        $t = PhoneBookTypes::GetCache(PhoneBookTypes::FD_RCPHBT_NAME, $type);
        if (!$t) {
            return false;
        }
        $success = false;
        if (empty($value)) {
            PhoneBooks::delete([
                PhoneBooks::FD_RCPHB_ENTRY_GUID => $guid,
                PhoneBooks::FD_RCPHB_TYPE => $t->rcphbt_Id,
            ]);
        } else {
            PhoneBooks::beginTransaction();
            if ($g = PhoneBooks::createIfNotExists([
                PhoneBooks::FD_RCPHB_ENTRY_GUID => $guid,
                PhoneBooks::FD_RCPHB_TYPE => $t->rcphbt_Id,
                PhoneBooks::FD_RCPHB_VALUE => $value,
            ])) {
                if (!$r) {
                    $success  = $g && PhoneBookUserAssociations::create([
                        PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid,
                        PhoneBookUserAssociations::FD_USRPHB_PHONE_BOOK_ENTRY_GUID => $guid,
                    ]);
                } else {
                    if ($g->isNew()) {
                        $success = true;
                    }
                }
            }
            if (!$success) {
                PhoneBooks::rollback();
            } else {
                PhoneBooks::commit();
            }
        }
    }
    /**
     * get phone book entries
     * @param Users $model 
     * @return mixed 
     */
    public static function getPhoneBookEntries(Users $model)
    {
        return PhoneBookUserAssociations::select_all([
            PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid
        ]);
    }
    /**
     * get user phonebook entry
     * @param Users $model 
     * @return null|PhoneBookUserAssociations 
     */
    public static function getPhoneBookEntry(Users $model)
    {
        return PhoneBookUserAssociations::select_row([
            PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid
        ]);
    }
    /**
     * get phone entry by type
     * @param Users $model 
     * @param null|string $type 
     * @return ?array<PhoneBookEntry>
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function getPhoneBookEntryByType(Users $model, ?string $type = PhonebookTypeNames::PHT_PHONE)
    {
        if ($g = PhoneBookUserAssociations::select_row([
            PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid
        ])) {
            $response = [];
            PhoneBooks::prepare()
                ->join_left(
                    PhoneBookTypes::table(),
                    PhoneBooks::FD_RCPHB_TYPE . '=' . PhoneBookTypes::FD_RCPHBT_ID
                )
                ->where([
                    PhoneBooks::FD_RCPHB_ENTRY_GUID => $g->usrphb_PhoneBookEntryGuid,
                    PhoneBookTypes::FD_RCPHBT_NAME => $type
                ])
                ->execute(true, [
                    "@callback" => function ($a) use (&$response) {
                        $phone = new PhoneBookEntry();
                        $phone->value = $a->rcphb_Value;
                        $phone->type = $a->rcphbt_Name;
                        $phone->id = $a->rcphb_Id;
                        $response[] = $phone;
                        return true;
                    }
                ]);
            return $response;
        }
        return null;
    } 
    /**
     * get user full name
     * @param Users $user 
     * @return string 
     */
    public static function fullName(Users $user){
        $s = trim(implode(' ', array_filter([$user->clFirstName, strtoupper($user->clLastName ?? '')])));
        return empty($s)? $user->clLogin : $s;
    }

    /**
     * bind user to group 
     * @param Users $user 
     * @param BaseController $ctrl 
     * @param string $groupname 
     * @return object|null|false 
     */
    public static function bindToGroup(Users $user, BaseController $ctrl, string $groupname){
        return \IGK\Helper\Authorization::BindUserToGroup($ctrl, $user, $groupname);
    }

     /**
     * remove current user from that group 
     * @param Users $user 
     * @param string $groupName 
     * @return bool
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function removeFromGroup(Users $user, string $groupName):bool{
        $user->is_mock() ?? igk_die('mock not allowed');
        $uid = $user->clId;
        $r = false;
        $v_ctrl_name = null;
        $condition = [];
        $tgroup = array_filter(explode('@', $groupName));
        if (count($tgroup)>1){
            $v_ctrl_name = array_shift($tgroup);
            $groupName = implode('@', $tgroup);
            $condition['clController'] = $v_ctrl_name;
        }
        $condition['clName'] = $groupName; 

        if ($gid = (($m = Groups::select_row($condition)) ? $m->clId : null)){

            $condition = [];
            $condition = array_merge($condition, ["clGroup_Id"=>$gid, "clUser_Id"=>$uid]);
            $r = Usergroups::delete($condition);
            if ($r instanceof BooleanQueryResult){
                $r = $r->success();
            }
        }
        return $r;
        
    }

    /**
     * create user reponse data
     * @param Users $user 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function CreateUserApiResponseData(Users $user):array{
        $user->is_mock() ?? igk_die('not allowed');
        $v_user = SysDbMapping::CreateMapping($user)->map($user);
        $data = [
            'user' => $v_user,  
            'groups' => array_map(function ($a) {
                return implode('@', array_filter([$a['clController'], $a['clName']]));
            }, $user->groups()),
            'auths' => array_map(function ($a) {
                return implode('@', array_filter([$a['name']]));
            }, $user->auths()), 
        ];
        return $data;
    }
}
