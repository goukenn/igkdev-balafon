<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorization.php
// @date: 20221118 10:57:56
namespace IGK\Helper;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helpers\ArticleHelper;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\Models\Authorizations;
use IGK\System\Database\QueryBuilder;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Net\Mail;
use IGK\System\Net\SendMailUtility;
use IGK\System\Process\CronJobProcess;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException;
use function igk_resources_gets as __;

///<summary></summary>
/**
* autorisation helper class 
* @package IGK\Helper
*/
class Authorization{

    /**
     * 
     * @param Users $user 
     * @param BaseController $controller 
     * @param mixed $auth_name 
     * @return bool 
     */
    public static function Can(\IGK\Models\Users $user, BaseController $controller, $auth_name):bool{
        if (!is_array($auth_name)){
            $auth_name = [$auth_name];
        }
        $auth_name = array_filter(array_map(function($a)use($controller){
                return $controller->authName($a);
            }, $auth_name)); 
        return $user->auth($auth_name);
    }
    /**
     * get controller authorizations 
     * @param BaseController $controller 
     * @return mixed 
     */
    public static function GetAuthorizations(BaseController $controller){
        $keyname = StringUtility::GetControllerKeyName($controller);
        return Authorizations::select_all([
            Authorizations::FD_CL_CONTROLLER => $keyname 
        ], [
            "Columns"=>[
                Authorizations::FD_CL_NAME => "name"
            ]
        ]);
    }
    /**
     * get controller groups
     * @param BaseController $controller 
     * @return mixed 
     */
    public static function GetGroups(BaseController $controller){
        $keyname = StringUtility::GetControllerKeyName($controller);
        return array_map(function($a){ 
            return $a->name; 
        }, Groups::select_all([
            Groups::FD_CL_CONTROLLER => $keyname 
        ], [
            "Columns"=>[
                Groups::FD_CL_NAME => "name"
            ]
        ]));
    }
    /**
     * get user groups
     * @param BaseController $controller 
     * @param string $group group name
     * @param callable $builder gbuilder listener
     * @return QueryBuilder 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetGroupUsers(BaseController $controller, string $group, callable $builder=null){
        $keyname = StringUtility::GetControllerKeyName($controller);
        $cl_uid = Users::column(Users::FD_CL_ID);
        $g = Groups::prepare()
        ->join_left_on(Usergroups::table(), Groups::column(Groups::FD_CL_ID), Usergroups::column(Usergroups::FD_CL_GROUP_ID))
        ->join_left_on(Users::table(), Users::column(Users::FD_CL_ID), Usergroups::column(Usergroups::FD_CL_USER_ID))
        ->where([Groups::column("clName")=>$group, Groups::column("clController")=>$keyname])
        ->columns([
            $cl_uid => "id",
            "clFirstName" => "firstName",
            "clLastName" => "lastName",
            "clLogin" => "login",
            "clGuid" => "guid",
        ]);
        if ($builder){
            $builder($g);
        }
        return $g->execute();
    }
    /**
     * bind user to group
     * @param BaseController $controller 
     * @param Users $user system user 
     * @param string $group controller's group without key identification. 
     * @return object|null|false 
     */
    public static function BindUserToGroup(BaseController $controller, Users $user, string $group){
        $keyname = StringUtility::GetControllerKeyName($controller);
        $gid = Groups::createIfNotExists(["clName"=>$group, "clController"=>$keyname]);
        if (!$gid){
            return false;
        }
        $gid = $gid->clId;
        $gp =  Usergroups::insertIfNotExists([
            'clUser_Id' => $user->clId,
            'clGroup_Id' => $gid,
        ]);
        return $gp;
    }
    /**
     * remove user attached to controller groups
     * @param BaseController $controller 
     * @param Users $user 
     * @param string $group 
     * @return bool 
     */
    public static function UnbindUserFromGroup(BaseController $controller, Users $user, string $group){
        $keyname = StringUtility::GetControllerKeyName($controller);
        $gid = Groups::createIfNotExists(["clName"=>$group, "clController"=>$keyname]);
        if (!$gid){
            return false;
        }
        $gid = $gid->clId;
        $gp =  Usergroups::delete([
            'clUser_Id' => $user->clId,
            'clGroup_Id' => $gid,
        ]);
        return $gp;
    }
    /**
     * grant autorization to group
     * @param BaseController $controller 
     * @param string $autorizationName 
     * @return void 
     */
    public static function BindControllerAuth(?BaseController $controller, string $autorizationName, string $groupName, $grant=1){
        $name = is_null($controller) ? null : igk_uri(get_class($controller));
        $auth_name = StringUtility::AuthorizationPath($autorizationName, $name);
        $auth = Authorizations::createIfNotExists(['clController'=>$name, 'clName'=>$auth_name]);
        $group = Groups::createIfNotExists(['clController'=>$name, 'clName'=>$groupName]);

        return Groupauthorizations::createIfNotExists([
            "clGroup_Id"=>$group->clId,
            "clAuth_Id"=>$auth->clId,
            "clGrant"=>$grant
        ]);
    }

    /**
     * helper: send mail recovery instruction 
     * @param null|BaseController $baseController      
     * @param array $data 
     * @param string $article
     * @return int|bool 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     */
    public static function SendRecoveryPasswordMailInstructrion(?BaseController $baseController, array $data, string $article='/mails/auth/recoveryPasswordInstruction'){
        return SendMailUtility::SendMailInstruction($baseController, $article, $data);         
    }
}