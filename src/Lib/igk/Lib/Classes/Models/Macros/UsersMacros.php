<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UsersMacros.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\Models\Macros;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DataAdapterBase;
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
 * @method bool auth($auths, bool $strict=false) macros instance 
 */
abstract class UsersMacros
{
    /**
     * get user form id or guid
     * @param Users $model
     * @param mixed $user_id
     * @return null|Users
     */
    public static function GetUserFromIdOrGuid(Users $model, $user_id)
    {
        return (IGKValidator::IsGUID($user_id)) ?
            Users::get(Users::FD_CL_GUID, $user_id) :
            Users::get(USers::FD_CL_ID, $user_id);
    }
    /**
     * get all active users
     * @param Users $model
     * @param ?array $options
     * @return mixed
     */
    public static function ActiveUsersArray(Users $model, ?array $options)
    {
        return Users::select_all(['clStatus' => 1], $options);
    }
    /**
     * register user helper
     * @param Users $model
     * @param object|array|IUserRegisterInfo $o
     * @param ?BaseController $ctrl
     * @param ?callable $beforeHook
     * @throws IGKException
     * @return ModelBase
     */
    public static function Register(Users $model, $o, ?BaseController $ctrl = null, ?callable $beforeHook = null)
    {
        if (!is_array($o) && !is_object($o)) {
            igk_die(__METHOD__ . " object not valid");
        }
        if (is_array($o)) {
            $o = (object)$o;
        }
        if (empty($guid = igk_getv($o, Users::FD_CL_GUID))) {
            $guid = igk_create_guid();
            igk_setv($o, Users::FD_CL_GUID, $guid);
        }
        if (empty($pwd = igk_getv($o, Users::FD_CL_PWD))) {
            $pwd = sha1(IGK_PWD_PREFIX . date("Ymd") . microtime(true));
            igk_setv($o, Users::FD_CL_PWD, $pwd);
        }
        if (($login = igk_getv($o, Users::FD_CL_LOGIN))) {
            if (!IGKValidator::IsEmail($login) && ($domain = igk_configs()->website_domain)) {
                $o->{Users::FD_CL_LOGIN} = $login = sprintf('%s@%s', $login, $domain);
            }
            if ($model::select_row([Users::FD_CL_LOGIN => $login])) {
                return false;
            }
        }
        if (empty(igk_getv($o, Users::FD_CL_CLASS_NAME)) && $ctrl)
            igk_setv($o, Users::FD_CL_CLASS_NAME, get_class($ctrl));
        if ($r = $model::create($o)) {
            if ($beforeHook) {
                $beforeHook($r);
            }
            igk_hook(IGKEvents::HOOK_USER_ADDED, ["user" => $r, "ctrl" => $ctrl]);
        } else {
            igk_dev_wln_e('failed to create = ', $o);
        }
        return $r;
    }
    /**
     * user::drop used for dev to drop user 
     * @param Users $model 
     * @return bool
     */
    public static function dropUser(Users $model)
    {
        $cl = $model->clClassName;
        $ctrl = SysDbController::ctrl();
        if ($cl) {
            $ctrl = $cl::ctrl(true);
        }
        $_commit = true;
        $r = ['user' => $model] + compact('ctrl');
        $ad = $model->getDataAdapter();
        $r['$ref'] = (object)['commit' => &$_commit];
        $ad->beginTransaction();
        try {
            igk_hook(IGKEvents::HOOK_USER_DROP, $r);
            $model->delete();
            if ($_commit) {
                $ad->commit();
            } else {
                $ad->rollback();
            }
        } catch (\Exception $ex) {
            $ad->rollback();
        }
        return $_commit;
    }
    /**
     * get group that this user is member of
     * @param Users $model 
     * @return mixed|array|null
     */
    public static function memberOf(Users $model, ?string $auth = null)
    {
        $mod = $model;
        if ($mod->is_mock()) {
            return null;
        }
        $cond = ['clGuid' => $mod->clGuid];
        $gtable = Groups::table();
        if ($auth) {
            list($controller, $name) = explode('@', $auth);
            $cond[Groups::FD_CL_NAME] = $name;
            $cond[Groups::FD_CL_CONTROLLER] = $controller;
        }
        $c = Usergroups::prepare()
            ->join_left($mod->table(), Usergroups::column('clUser_Id') . ' = ' . $mod->column('clId'))
            ->join_left($gtable, Groups::column('clId') . ' = ' . Usergroups::column('clGroup_Id'))
            ->where($cond)
            ->distinct()
            ->columns(
                [
                    Groups::column('*')
                ]
            )->orderBy([Groups::column('clId') . "|ASC"])
            ->execute(false);
        if ($c) {
            return $c->to_array();
        }
        return null;
    }
    /**
     * get user groups name 
     * @param Users $model 
     * @return array<array-key, mixed> 
     */
    public static function getGroupNames(Users $model)
    {
        return array_map(new \IGK\Mapping\PropertyMapper(Groups::FN_CL_NAME()), $model->groups());
    }
    /**
     * Returns Authorization Names.
     * @param Users $model
     */
    public static function getAuthorizationNames(Users $model)
    {
        return array_map(new \IGK\Mapping\PropertyMapper(Groups::FN_CL_NAME()), $model->auths());
    }
    /**
     * get user form guid :
     * @param Users $model
     * @param string $guid
     */
    public static function fromGuid(Users $model, string $guid)
    {
        return $model->GetCache(Users::FD_CL_GUID, $guid);
    }
    /**
     * initialize system user 
     * @return void 
     */
    public static function InitSystemUsers()
    {
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
        /**
         * from configuration default users 
         */
        $defuser = [];
        foreach ($defuser as $u) {
            Users::create($u);
        }
    }
    /**
     * just register an user
     * @param Users $model 
     * @param string $login 
     * @param null|string $pwd 
     * @param null|array $extra 
     * @return null|bool|DataAdapterBase|Users 
     */
    public static function registerUserByLoginPassAndExtra(Users $model, string $login, ?string $pwd = null, ?array $extra = null)
    {
        return Users::insertIfNotExists(
            [
                Users::FD_CL_LOGIN => $login,
                Users::FD_CL_PWD => $pwd
            ],
            [
                'extra' => $extra
            ],
            true
        );
    }

    /**
     * check system user authentication 
     * @param Users $user 
     * @param ?string|array<string> $auths separated | string
     * @param bool $strict 
     * @return bool 
     */
    public static function checkAuth(\IGK\Models\Users $user, $auths, bool $strict = false): bool
    {
        /**
         * auto generate doc.
         * @var ModelBase $q
         */
        if ($user->is_mock()) {
            return false;
        }
        $q = $user;
        $key = \IGK\Models\ModelBase::AuthKey;
        $is_auth = false;
        if (!is_array($auths)) {
            if (!is_string($auths)) {
                return false;
            }
            $auths = explode('|', $auths);
        }
        if (($g = $q->{$key}) === null) {
            $g = [];
            if ($q->clId !== null) {
                if ($b = $user->auths()) {
                    foreach ($b as $t) {
                        $g[] = $t->name;
                    }
                }
                $q->set($key, $g);
            } else
                return false;
        }
        if (($is_auth = count($g) > 0)) {
            if ($strict) {
                while ($is_auth && ($auth = array_shift($auths))) {
                    if (!($is_auth = in_array($auth, $g))) {
                        break;
                    }
                }
            } else {
                $is_auth = false;
                while ($auth = array_shift($auths)) {
                    if (in_array($auth, $g)) {
                        $is_auth = true;
                        break;
                    }
                }
            }
        }
        return $is_auth;
    }
    /**
     * auth calculation 
     * @param Users $model 
     * @param mixed $auths 
     * @param bool $strict must strictly be granted 
     * @return bool 
     */
    public static function auth(Users $model, $auths, bool $strict = false)
    {
        return self::checkAuth($model, $auths, $strict);
    }
    public static function groups(Users $model)
    {
        return Usergroups::getUserGroups($model->clId);
    }

    /**
     * counting registered member on that profiles 
     * @param Users $model 
     * @param ModelBase $profile_model 
     * @param string $group_name 
     * @param null|string $controllername 
     * @return int 
     */
    public static function projectProfileCountMemberOf(Users $model, ModelBase $profile_model, string $group_name, ?string $controllername = null, bool $all = false)
    {
        $n = $controllername;
        $tables = [
            'target' => $profile_model::table(),
            'users' => Users::table(),
            'usergroups' => Usergroups::table(),
            'groups' => Groups::Table(),
        ];
        extract($tables);
        $cond = [
            Groups::FN_CL_NAME() => $group_name,
            Groups::FN_CL_CONTROLLER() => $n
        ];
        if (!$all) {
            $cond[Users::FN_CL_STATUS()] = 1;
        }
        $g = null;
        if (get_class($profile_model) == get_class($model)) {
            $g = $profile_model::prepare()
                ->columns(['count(*) as T'])
                ->join_left_on($usergroups, Usergroups::FN_CL_USER_ID(), Users::FN_CL_ID())
                ->join_left_on($groups, Groups::FN_CL_ID(), Usergroups::FN_CL_GROUP_ID())
                ->where($cond)
                ->execute();
        } else {
            $g = $profile_model::prepare()
                ->columns(['count(*) as T'])
                ->join_left_on($users, Users::FN_CL_GUID(), $profile_model::FN_GUID())
                ->join_left_on($usergroups, Usergroups::FN_CL_USER_ID(), Users::FN_CL_ID())
                ->join_left_on($groups, Groups::FN_CL_ID(), Usergroups::FN_CL_GROUP_ID())
                ->where($cond)
                ->execute();
        }
        $T = 0;
        if ($g && ($c = $g->to_array())) {
            $T = intval(igk_getv($c[0], 'T', $T));
        }
        return $T;
    }
}
