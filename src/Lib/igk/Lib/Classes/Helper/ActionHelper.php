<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionHelper.php
// @date: 20220803 13:48:57
// @desc: 

// @file: ActionHelper.php
// @author: C.A.D. BONDJE DOUE
// date: 2022-10-02
// description: contains function that IGKActionBase can use

namespace IGK\Helper;

use DateInterval;
use Exception;
use IGK\Actions\ActionBase;
use IGKActionBase;
use IGK\Controllers\BaseController;
use IGK\Models\RegistrationLinks;
use IGK\Models\Users;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use IGK\System\IO\Path;
use IGK\System\Net\Mail;
use IGK\System\Process\CronJobProcess;
use IGKEvents;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use IGKException;
use IGKValidator;
use ReflectionException;
use ReflectionMethod;
use function igk_resources_gets as __;

/**
 * action helper
 * @package IGK\Helper
 */
abstract class ActionHelper
{
    const ENTRY_NAME = 'Actions\\';
    /**
     * expected action call 
     * @var mixed
     */
    static $ResolvedClass;
    /**
     * dispatch to action 
     * @param string $method 
     * @param string $action_class 
     * @param array $arguments 
     * @param callable|null $callable 
     * @return mixed 
     */
    public static function DispatchToAction(string $method, string $action_class, array $arguments, callable $callable = null)
    {
        $verb = ["", '_' . strtolower(igk_server()->REQUEST_METHOD)];
        while (count($verb) > 0) {
            $q = array_shift($verb);
            if (method_exists($action_class, $m = $method . $q)) {
                $c = new $action_class;
                if ($callable) {
                    $callable($c);
                }
                $tab = $arguments ?? [];
                array_unshift($tab, $method);
                return ActionBase::HandleObjAction('__dispatch__', $c, $tab);
            }
        }
    }
    /**
     * 
     * @param Users $u user ask to change password
     * @param string $password password
     * @param string $repassword confirm password
     * @param mixed $not notification handler
     * @return IGK\Models\IIGKQueryResult|false 
     */
    public static function ChangePassword(Users $u, string $password, string $repassword, $not = null)
    {
        $not = $not ?? igk_notifyctrl();
        if ($password) {
            if ($password == $repassword) {
                if (IGKValidator::IsValidPwd($password)) {
                    if ($u = \IGK\Models\Users::update(
                        ['clPwd' => $password],
                        ['clId' => $u->clId]
                    )) {
                        $not->success(__('password changed'));
                        return $u;
                    } else {
                        $not->danger('password not changed');
                    }
                } else {
                    $not->danger('not a valid passord');
                }
            } else {
                $not->danger('password mismatch');
            }
        } else {
            $not->danger('password is empty');
        }
        return false;
    }
    /**
     * retrieve alive token 
     * @param mixed $token 
     * @return null|RegistrationLinks 
     */
    public static function GetAliveToken(string $token)
    {
        $row = RegistrationLinks::select_row([
            'regLinkToken' => $token,
            'regLinkAlive' => 1
        ]);
        return $row;
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param mixed $token 
     * @param null|RegistrationLinks $regLink 
     * @return RegistrationLinks|bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function ActivateUser(BaseController $ctrl, $token, ?RegistrationLinks $regLink = null)
    {

        if ($row = $regLink ?? self::GetAliveToken($token)) {
            $format = IGK_MYSQL_DATETIME_FORMAT;
            $now = date_create_from_format($format, date($format));
            $pass = date_create_from_format($format, date($row->regLinkUpdate_At));
            $diff = $now->diff($pass);
            $interval =  new DateInterval('P3D');
            $d = str_pad($diff->format('%d%h%i'), 4, '0', STR_PAD_LEFT);
            $m = str_pad($interval->format('%d%h%i'), 4, '0', STR_PAD_LEFT);



            /// TODO: ACTIVATE ACCOUNT
            // if ( $d < $m){
            if ($r = \IGK\Models\Users::update(
                ["clStatus" => 1],
                ['clGuid' => $row->regLinkUserGuid]
            )) {
                $row->regLinkAlive = 0;
                $row->regLinkActivate = date($format);
                $row->save();
                igk_hook(IGKEvents::HOOK_USER_ACTIVATED, [
                    "ctrl" => $ctrl,
                    "row" => $r,
                    "status" => 1
                ]);
                return $row;
            }
        }
        return false;
    }

    public static function UnregisterUser($ctrl, $token)
    {
    }
    //do nothing
    /**
     * used to pass empty anonymous
     * @return callable 
     */
    public static function Nothing(): callable
    {
        return function () {
            // nothing call back method
        };
    }
    /**
     * helper: Handle action call
     * @param string $actionClassName 
     * @param BaseController $controller 
     * @param string $fname 
     * @param array $params 
     * @param bool $auto_exit 
     * @return mixed 
     */
    public static function HandleAction(
        string $actionClassName,
        BaseController $controller,
        string $fname,
        array $params,
        $auto_exit = true
    ) {
        if (class_exists($actionClassName) && (is_subclass_of($actionClassName, IGKActionBase::class))) {
            return $actionClassName::Handle(
                $controller,
                $fname,
                $params,
                $auto_exit
            );
        }
    }
    /**
     * sanitize method name
     * @param string $name 
     * @return string 
     */
    public static function SanitizeMethodName(?string $name)
    {
        if ($name === null) {
            return $name;
        }
        $name = trim(preg_replace("/[^0-9\w]/", "_", $name));
        return $name;
    }
    /**
     * bind request args
     * @param mixed $object 
     * @param mixed $action 
     * @param mixed $args 
     * @return void 
     */
    public static function BindRequestArgs($object, $action, &$args)
    {
        $g = new ReflectionMethod($object, $action);
        if (($g->getNumberOfRequiredParameters() == 1) && ($cl = $g->getParameters()[0]->getType()) && igk_is_request_type($cl)) {
            $req = \IGK\System\Http\Request::getInstance();
            $req->setParam($args);
            $args = [$req];
        }
    }
    /**
     * handle args helper 
     * @param string $fname 
     * @param array $handleArgs 
     * @return bool 
     */
    public static function HandleArgs(string $fname, array &$handlerArgs, string $entryName = IGK_DEFAULT): bool
    {
        if ((strpos($fname, "/") !== false) && !igk_str_endwith($fname, $entryName)) {
            if (!empty($tb = array_slice(explode("/", $fname), -1)[0])) {
                array_unshift($handlerArgs, $tb);
            }
            return true;
        }
        return false;
    }

    /**
     * helper: get action current model 
     * @param ActionBase $action 
     * @return null|Users 
     */
    public static function CurrentActionUserModel(ActionBase $action): ?Users
    {
        $ret = null;
        $ctrl = $action->getController();
        if ($profile = $ctrl->getUser()) {
            $ret = $profile->systemModel();
        }
        return $ret;
    }

    /**
     * send mail helper 
     * @param BaseController $controller 
     * @param string $to 
     * @param null|string $from 
     * @param string $title 
     * @param string $msg 
     * @param array|null $options 
     * @return false|void 
     * @throws NotFoundExceptionInterface  
     * @throws ContainerExceptionInterface  
     * @throws IGKException 
     */
    public static function SendMail(
        BaseController $controller,
        string $to,
        ?string $from,
        string $title,
        string $msg,
        array $options = null,
        ?string $mail_title = null
    ) {

        if (empty($v_sysmail_account = igk_configs()->mail_user)) {
            return false;
        }

        $info = (object)array_merge([
            "to" => $to,
            "title" => $title,
            "msg" => $msg,
        ], $options ??  []);

        $v_reg_info = CronJobProcess::Register(
            "mail",
            "mail.register.php",
            $info,
            $controller
        );

        if (!$v_reg_info) {
            igk_ilog('failed to register cron job mail process');
            return false;
        }

        $mail = new Mail();
        $mail->addTo($to);
        $mail_title = StringUtility::GetApplicationMailTitle($controller, $mail_title);
        if ($mail_title)
            $from = sprintf('"%s" <%s>', $mail_title, $v_sysmail_account);
        else
            $from = sprintf('%s', $v_sysmail_account);
        $mail->From = $from;
        $mail->setHtmlMsg($info->msg);
        $mail->setTitle($info->title);

        if ($cc = igk_getv($info, 'gcc')) {
            $mail->addToGCC($cc);
        }
        if ($cc = igk_getv($info, 'cc')) {
            $mail->addToCC($cc);
        }

        $rep = $v_reg_info && $mail->sendMail();
        if ($rep) {
            $v_reg_info->crons_status = 1;
            $v_reg_info->save();
        }
        return $v_reg_info;
    }

    /**
     * generate a geristration link token
     * @param Users $user 
     * @param null|string $prefix 
     * @return mixed 
     */
    public static function GenerateUserRegistrationLinkToken(Users $user, ?string $prefix = null)
    {
        return self::GenerateRegistrationLinkToken($user->clLogin, $user->clGuid, $prefix);
    }
    /**
     * generate a registration link the login et registration link token
     * @param string $login login to get registration link
     * @param string $guid the guid generated
     * @param null|string $prefix extra prefix
     * @return string|false 
     */
    public static function GenerateRegistrationLinkToken(string $login, string $guid, ?string $prefix = null)
    {

        $token = igk_encrypt($login .
            ($prefix ?? $login . date('Ymd') . time()));
        try {


            if (!($row = RegistrationLinks::select_row([
                RegistrationLinks::FD_REG_LINK_USER_GUID => $guid,
            ]))) {
                RegistrationLinks::createIfNotExists([
                    RegistrationLinks::FD_REG_LINK_TOKEN => $token,
                    RegistrationLinks::FD_REG_LINK_USER_GUID  => $guid
                ], [
                    RegistrationLinks::FD_REG_LINK_USER_GUID  => $guid,
                    RegistrationLinks::FD_REG_LINK_ACTIVATE => null,
                    RegistrationLinks::FD_REG_LINK_ALIVE => 1
                ]);
            } else {
                $row->regLinkToken = $token;
                $row->regLinkAlive = 1;
                $row->regLinkActivate = null;
                $row->save();
            }
        } catch (Exception $ex) {
            igk_dev_wln($ex->getMessage());
        }
        return $token;
    }

    /**
     * retrieve all action controller action 
     * @param BaseController $controller 
     * @return null|array  
     */
    public static function GetActionClasses(BaseController $controller)
    {
        $dir = $controller->getClassesDir() . "/Actions";
        if (!is_dir($dir)) {
            return null;
        }
        $tab = [];
        foreach (igk_io_getfiles($dir, "/Action\.php$/") as $f) {
            $path = ltrim(igk_str_rm_start($f, $dir), '/');
            $actions = Path::CombineAndFlattenPath("/Actions/", dirname($path), igk_io_basenamewithoutext($path));
            $tab[] = $controller->resolveClass($actions) ?? igk_die("missing class : " . $actions);
        }
        return $tab;
    }
    /**
     * get actions exposed method
     * @param object|string $object_or_class 
     * @return null|array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetExposedMethods($object_or_class)
    {
        if (!is_subclass_of($object_or_class, ActionBase::class)) {
            return null;
        }
        $cl = igk_sys_reflect_class($object_or_class);
        if ($cl->isAbstract()) {
            return null;
        }
        return array_filter(array_map(function ($m) {
            $n = $m->getName();
            if ($m->isStatic() || $m->isConstructor() || in_array($n, [
                '__call',
                '__set',
                '__get'
            ]) || preg_match("/^(_|get|set)/i", $n)) {
                return null;
            }
            return $n;
        }, $cl->getMethods(ReflectionMethod::IS_PUBLIC)));
    }

    /**
     * 
     * @param BaseController $controller 
     * @param string $action_class_name 
     * @return mixed 
     */
    public static function GetActionName(BaseController $controller, string $action_class_name): ?string
    {
        $fs = ltrim($controller->getEntryNamespace() . "\\Actions", "\\") . "\\";
        if (strpos($action_class_name, $fs) === 0) {
            $a = igk_str_rm_start($action_class_name, $fs);
            if ((strrpos($a, "Action") !== false)) {
                $a = igk_str_rm_last($a, "Action");
            }
            $a = igk_uri($a);
            if (basename($a) == basename(dirname($a))) {
                // + consider as default action 
                $a = igk_str_rm_last($a, '/' . basename($a));
            }

            return $a;
        }
        return null;
    }
    /**
     * get expected action class
     * @param BaseController $controller 
     * @param string $view_action_name 
     * @return null|string 
     */
    public  static function ExpectedAction(BaseController $controller, string $view_action_name): ?string
    {
        $name = $view_action_name;
        if (($name != IGK_DEFAULT_VIEW) && preg_match("/" . IGK_DEFAULT_VIEW . "$/", $name)) {
            $name = rtrim(substr($name, 0, -strlen(IGK_DEFAULT_VIEW)), "/");
        }
        $name = implode("/", array_map("ucfirst", explode("/", $name)));
        $action = $controller::ns(sprintf(
            "%s\\%s",
            \Actions::class,
            $name . "Action"
        ));
        return $action;
    }

    /**
     * get if resolved class is expected 
     * @param BaseController $baseController 
     * @param mixed $action_name action path name
     * @param mixed $resolved_class 
     * @return bool 
     */
    public static function IsExpectedAction(BaseController $baseController, string $action_name, string $resolved_class): bool
    {
        if (self::$ResolvedClass && ($resolved_class == self::$ResolvedClass->class)) {
            //match resolved class.
            return true;
        }
        if ($g = self::ExpectedAction($baseController, $action_name)) {
            return $g == $resolved_class;
        }
        return false;
    }
    /**
     * get action uri
     * @param BaseController $baseController 
     * @param string $action_name 
     * @return string 
     */
    public static function GetActionUri(BaseController $baseController, string $action_name): string
    {
        return  '/' . igk_uri(self::GetActionName($baseController, $baseController->resolveClass($action_name))) . "/";
    }


    /**
     * do handle 
     * @param BaseController $controller 
     * @param string $handler_class_name 
     * @param mixed $fname 
     * @param mixed $params 
     * @return mixed 
     * @throws IGKException 
     */
    public static function DoHandle(BaseController $controller, 
    string $handler_class_name, string $fname, array $params, $rep, $options = null)
    {
        $is_expected = ActionHelper::IsExpectedAction($controller, $fname, $handler_class_name);
        $is_ajx = ($options ? igk_getv($options, 'is_ajx') : null) ?? 
            (igk_server()->CONTENT_TYPE == "application/json") || igk_is_ajx_demand();
        $verb = ($options ? igk_getv($options, 'method') : null) ?? 
            strtolower(igk_server()->REQUEST_METHOD ?? 'get');
        $v_user = ($options ? igk_getv($options, 'user') : null);
        $old_data = null;
        if ($v_requestData = ($options? igk_getv($options, 'requestData') : null)){
            $old_data = Request::getInstance()->setJsonData(json_encode($v_requestData));
        }

        // check for source user
        $controller->checkUser(false);
        // traitement before passing args to handlers
        $handlerArgs = $params;
        $_t = null;
        $_index = 'index';
        if (strpos($fname, '/') !== false) {
            $_t = explode('/', ltrim($fname, '/'));
            $view = igk_array_peek_last($_t);
            if ($view == IGK_DEFAULT) {
                array_pop($_t);
            }
            // if default view passing 
            while (count($_t) > 1) {
                $np = array_pop($_t);
                if (
                    strtolower($np . 'action') !=
                    strtolower(basename(igk_uri($handler_class_name)))
                ) {
                    array_unshift($handlerArgs, $np);
                    break;
                }
            }
        }
        // igk_dev_wln_e(__FILE__.":".__LINE__,  $params, $handlerArgs, $_index);
        if (count($handlerArgs) == 0) {
            // no parameter pass to index method of the action handler
            if ($is_expected) {
                $handlerArgs = [$_index];
            } else {
                if (!ActionHelper::HandleArgs($fname, $handlerArgs)) {
                    if (!empty($fname)) {
                        $tp = igk_array_last(explode('/', $fname));
                        if ($tp != IGK_DEFAULT) {
                            $_index = $tp;
                        }
                    }
                    $handlerArgs = [$_index];
                }
            }
        } else if (is_numeric(array_keys($handlerArgs)[0]) && is_numeric($handlerArgs[0])) {
            // + | passing numeric data to index
            array_unshift($handlerArgs, $_index);
        } else {
            if ($handler_class_name == $controller->resolveClass(\Actions\DefaultAction::class)) {
                self::HandleArgs($fname, $handlerArgs);
                if ($_t) {
                    array_unshift($handlerArgs, ...$_t);
                }
            } else {
                if (!$is_expected) {
                    $p = "Actions";
                    if ($_t) {
                        $p .= "\\" . implode("\\", array_map('ucfirst', array_filter($_t)));
                    }
                    while (count($handlerArgs) > 0) {
                        $g = array_shift($handlerArgs);
                        $p .= "\\" . ucfirst(StringUtility::CamelClassName($g));
                        $r = basename(igk_uri($p));
                        if (!($cl = $controller->resolveClass($p . "Action"))) {
                            $cl = $controller->resolveClass($p . "\\" . $r . "Action");
                            if ($cl && !empty($handlerArgs) && ($handler_class_name == $cl) && (strtolower($r) == strtolower($handlerArgs[0]))) {
                                // + | shift handle args api/api -default resolution
                                array_shift($handlerArgs);
                            }
                        }
                        if ($handler_class_name == $cl) {
                            if (empty($handlerArgs)) {
                                array_unshift($handlerArgs, $_index);
                            }
                            break;
                        }
                    }
                } else {
                    if (!is_null($rep->params)) {
                        $handlerArgs = $rep->params;
                    } else {
                        if ($rep->level > 0) {
                            $handlerArgs = array_splice($handlerArgs, $rep->level);
                        }
                    }
                    if (empty($handlerArgs)) {
                        array_unshift($handlerArgs, $_index);
                    }
                }
            }
        }
        $r = $handler_class_name::Handle(
            $controller,
            $fname,
            $handlerArgs,
            $is_ajx,
            true,
            $verb,
            $v_user
        );
        Request::getInstance()->setJsonData($old_data);
        return $r;
    }
}
