<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerRequestExtensionTrait.php
// @date: 20230803 15:09:44
namespace IGK\Controllers\Traits;
use IGK\Actions\ActionResolutionInfo;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerEnvParams;
use IGK\Helper\ActionHelper;
use IGK\Resources\R;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use IGK\System\ViewEnvironmentArgs;
use IGKEnvironment;
use IGKEvents;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException;

/**
* auto generate doc.
* @package IGK\Controllers\Traits
*/
trait ControllerRequestExtensionTrait
{
    /**
    * auto generate doc.
    * @return mixed
    */
    private static function _BackupServerInfo(){
        $v_backup = [
            $_SERVER,
            $_REQUEST,
            $_GET,
            $_POST
        ];
        return $v_backup;
    }
    /**
    * auto generate doc.
    * @param mixed $data
    * @return mixed
    */
    private static function _RestoreBackupServerInfo($data){
        $v_backup = [
            $_SERVER,
            $_REQUEST,
            $_GET,
            $_POST
        ];
        return $v_backup;
    }
    /**
    * call new request view
    * @param BaseController $ctrl
    * @param string $path
    * @param mixed $options
    * @throws IGKException
    * @throws ArgumentTypeNotValidException
    * @throws ReflectionException
    * @return never
    */
    public static function request(BaseController $ctrl, string $path, $options = null)
    { 
        $v_backup = self::_BackupServerInfo();
        igk_server()->prepareServerInfo();
        $gp = $ctrl->setCurrentView($path);
        $ac_response = $ctrl->{ControllerEnvParams::ActionViewResponse};
        self::_RestoreBackupServerInfo($v_backup);
        return $ac_response;
    }
    /**
     * invoke action response
     * @param BaseController $controller 
     * @param string $path 
     * @param mixed $options 
     * @return mixed action response 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function requestHandleAction(BaseController $controller, string $path, $options = null)
    {
        $result = null;
        $params = [];
        $v_backup = self::_BackupServerInfo();
        $v_path = explode ('?', $path,2);
        list($path, $qoptions) = igk_extract(explode(';', array_shift($v_path), 2), '0|1');
        if ($qoptions){
            $controller->setEnvParam(IGK_VIEW_OPTIONS, igk_get_query_options($qoptions));
        }
        $q = [];
        count($v_path)>0 && parse_str($v_path[0], $q);
        if ($q){            
            $_REQUEST = $q;
            if (igk_server()->REQUEST_METHOD=='POST'){
                $_POST = array_merge($_POST, $q);
            }
        }
        $file = $controller->getViewFile($path, false, $params);
        $v_viewargs = (array)ViewEnvironmentArgs::CreateContextViewArgument($controller, $file, __METHOD__);
        $fname = $v_viewargs['fname'];
        $_env_arg_key = IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS;
        $_arg_bck = igk_get_env($_env_arg_key);
        igk_set_env($_env_arg_key, $v_viewargs);
        // + | stop heere before action - 
        $rep = new ActionResolutionInfo;
        $lang = R::GetCurrentLang();
        if ($lg = igk_getv($tab = explode('/', ltrim($path, '/'), 2), 0)){
            $cc = R::SupportLang($lg);
            if ($cc && ($lg!= $lang)){
                R::ChangeLang($lg);
                $path = '/'.$tab[1];
            }
        }
        if ($action = $controller->getActionHandler($path, $rep, null)) { 
            $params = $rep->params; 
            igk_hook(IGKEvents::HOOK_ACTION_WILL_DO_ACTION, compact('action', 'params', 'controller'));
            $result = ActionHelper::DoHandle($controller, $action, $fname, $params, $rep, $options);            
        } else {
            if (igk_ctrl_is_default_controller($controller)){
                igk_json([
                    'error'=>true,
                    'message'=>'missing action handler'
                ], 404);
            }
        }
        // + | restore 
        igk_set_env($_env_arg_key, $_arg_bck);
        self::_RestoreBackupServerInfo($v_backup); 
        return $result;
    }
}