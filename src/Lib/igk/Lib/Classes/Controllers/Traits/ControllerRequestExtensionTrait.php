<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerRequestExtensionTrait.php
// @date: 20230803 15:09:44
namespace IGK\Controllers\Traits;

use IGK\Actions\ActionResolutionInfo;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerEnvParams;
use IGK\Helper\ActionHelper;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use IGK\System\ViewEnvironmentArgs;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\Controllers\Traits
 */
trait ControllerRequestExtensionTrait
{
    private static function _BackupServerInfo(){
        $v_backup = [
            $_SERVER,
            $_REQUEST,
            $_GET,
            $_POST
        ];
        return $v_backup;
    }
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
     * @param string $view  
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function request(BaseController $ctrl, string $path, $options = null)
    { 
        $v_backup = self::_BackupServerInfo();
        // configure server - 
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
        $path = array_shift($v_path);
        $q = [];
        count($v_path)>0 && parse_str($v_path[0], $q);
        $_REQUEST = $q;
        $_POST = $q;
        $file = $controller->getViewFile($path, false, $params);
        $v_viewargs = (array)ViewEnvironmentArgs::CreateContextViewArgument($controller, $file, __METHOD__);
        $fname = $v_viewargs['fname'];
        $rep = new ActionResolutionInfo;
        if ($action = $controller->getActionHandler($path, $rep, null)) { 
            $params = $rep->params; // 
            $result = ActionHelper::DoHandle($controller, $action, $fname, $params, $rep, $options);            
        }
        // restore 
        self::_RestoreBackupServerInfo($v_backup); 
        return $result;
    }
}
